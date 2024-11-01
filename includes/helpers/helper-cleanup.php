<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
if ( ! class_exists( 'Ultimate_DB_Manager_Cleanup_Helper' ) ) :

    class Ultimate_DB_Manager_Cleanup_Helper {

        /**
        * Empty table data
        *
        * @since 1.0.0
        */
        public function empty_table_data($table = ''){
            global $wpdb;
				$where = array();
				switch ( $table ) {
                    case 'revision':
						$where['post_type'] = 'revision';
						$this->delete( $wpdb->posts, $where );
                        break;

					case 'auto-draft':
						$where['post_status'] = 'auto-draft';
						$this->delete( $wpdb->posts, $where );
                        break;
                    
                    case 'trash-posts':
                        $where['post_status'] = 'trash-posts';
                        $this->delete( $wpdb->posts, $where );
                        break;    

                    case 'moderated-comments':
                        $where['comment_approved'] = '0';
                        $this->delete( $wpdb->comments, $where );
                        break;

                    case 'spam-comments':
                        $where['comment_approved'] = 'spam';
                        $this->delete( $wpdb->comments, $where );
                        break;    

                    case 'trash-comments':
                        $where['comment_approved'] = 'trash';
                        $this->delete( $wpdb->comments, $where );
                        break;     
           
                    case 'pingbacks':
                        $where['comment_type'] = 'pingback';
                        $this->delete( $wpdb->comments, $where );
                        break;     
                        
                    case 'trackbacks':
                        $where['comment_type'] = 'trackbacks';
                        $this->delete( $wpdb->comments, $where );
                        break;  

                    case 'orphan-postmeta':
                        $wpdb->query("DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");
                        break; 
                    
                    case 'orphan-commentmeta':
                        $wpdb->query("DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)");
                        break;

                    case 'orphan-relationships':
                        $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)");
                        break;

                    case 'orphan-usermeta':
                        $wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id NOT IN (SELECT ID FROM $wpdb->users)");
                        break;
                        
                    case 'orphan-termmeta':
                        $wpdb->query("DELETE FROM $wpdb->termmeta WHERE term_id NOT IN (SELECT term_id FROM $wpdb->terms)");
                        break;

                    case "expired-transients":
                        $type_arg = " AND b.option_value < UNIX_TIMESTAMP()";
                        $this->clean_all_transients($type_arg);
                        break;    
				}
        }

        /**
         * Insert data in database
         *
         * @param string $table_name
         * @param string $data
         */
        public function insert( $table_name, $data ) {
	        global $wpdb;
	        $wpdb->insert( $table_name, $data );// WPCS: db call ok.
	        return $wpdb->insert_id;
        }

        /**
         * Update data in database
         *
         * @param string $table_name
         * @param string $data 
         * @param string $where 
         */
        public function update( $table_name, $data, $where ) {
	        global $wpdb;
	        $wpdb->update( $table_name, $data, $where );// WPCS: db call ok, cache ok.
        }

        /**
         * Delete data from database
         *
         * @param string $table_name
         * @param string $where
         */
        public function delete( $table_name, $where ) {
	        global $wpdb;
	        $wpdb->delete( $table_name, $where );// WPCS: db call ok, cache ok.
        }

        /*
         * Cleans transients based on the type specified in parameter
         */
        public function clean_all_transients($type_arg){
	        global $wpdb;
	        $all_transients = $wpdb->get_results("SELECT a.option_name, b.option_value FROM $wpdb->options a LEFT JOIN $wpdb->options b ON b.option_name = 
	            CONCAT(
		            CASE WHEN a.option_name LIKE '_site_transient_%'
			        THEN '_site_transient_timeout_'
			        ELSE '_transient_timeout_'
		        END
		            ,
		        SUBSTRING(a.option_name, CHAR_LENGTH(
			        CASE WHEN a.option_name LIKE '_site_transient_%'
			        THEN '_site_transient_'
			        ELSE '_transient_'
			    END
		        ) + 1)
	        )
	        WHERE (a.option_name LIKE '_transient_%' OR a.option_name LIKE '_site_transient_%') AND a.option_name NOT LIKE '%_transient_timeout_%'" . $type_arg);

	        foreach($all_transients as $transient){
		        $site_wide = (strpos($transient->option_name, '_site_transient') !== false);
		        $name = str_replace($site_wide ? '_site_transient_' : '_transient_', '', $transient->option_name);
		        if(false !== $site_wide){
			        delete_site_transient($name);
		        }else{
			        delete_transient($name);
		        }
	        }
        }       

        /**
         * Get tables to clean (in the current site or MU)
         *
         * @since 1.0.0
         * @return array
         */
        public function getTables(){
            global $wpdb;
            $tables = $this->prepareTables();

            // Initialize counts to 0
            foreach($tables as $key => $value){
                $tables[$key]['count'] = 0;
            }

            //(for the table usermeta, only one table exists for MU, do not witch over blogs for it)
            if(function_exists('is_multisite') && is_multisite()){
                $blogs_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach($blogs_ids as $blog_id){
                    switch_to_blog($blog_id);
                    $this->countEachTable($tables);
                    restore_current_blog();
                }
            }else{
                $this->countEachTable($tables);
            }
            return $tables;
        }

        /**
         * Counts elements to clean in the current site
         *
         * @since 1.0.0
         * @return array
         */
        public function countEachTable(&$tables){
            global $wpdb;

            // Execute count queries
            $tables["revision"]['count'] += $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'revision'");
            $tables["auto-draft"]['count'] += $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            $tables["trash-posts"]['count'] += $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'trash'");

            $tables["moderated-comments"]['count'] += $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");
            $tables["spam-comments"]['count'] += $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = 'spam'");
            $tables["trash-comments"]['count'] += $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = 'trash'");

            $tables["pingbacks"]['count'] += $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'");
            $tables["trackbacks"]['count'] += $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'trackback'");

            $tables["orphan-postmeta"]['count'] += $wpdb->get_var("SELECT COUNT(meta_id) FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");
            $tables["orphan-commentmeta"]['count'] += $wpdb->get_var("SELECT COUNT(meta_id) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)");
            // for the table usermeta, only one table exists for MU, do not switch over blogs for it. Get count only in main site
            if(is_main_site()){
                $tables["orphan-usermeta"]['count'] += $wpdb->get_var("SELECT COUNT(umeta_id) FROM $wpdb->usermeta WHERE user_id NOT IN (SELECT ID FROM $wpdb->users)");
            }
            $tables["orphan-termmeta"]['count'] += $wpdb->get_var("SELECT COUNT(meta_id) FROM $wpdb->termmeta WHERE term_id NOT IN (SELECT term_id FROM $wpdb->terms)");

            $tables["orphan-relationships"]['count'] += $wpdb->get_var("SELECT COUNT(object_id) FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT ID FROM $wpdb->posts)");

            $expired_transient_names = $wpdb->get_col("SELECT REPLACE(option_name, '_timeout', '') FROM $wpdb->options where (option_name LIKE '_transient_timeout_%' OR option_name LIKE '_site_transient_timeout_%') AND option_value < UNIX_TIMESTAMP()");

            $tables["expired-transients"]['count'] += count($expired_transient_names);
        }

        /**
         * Prepare an array of tables need to cleanup
         *
         * @since 1.0.0
         * @return array
         */
        public function prepareTables(){
            
            $tables["revision"]['name'] 	                = esc_html__('Revisions', 'ultimate-db-manager');
            $tables["auto-draft"]['name'] 	                = esc_html__('Auto drafts', 'ultimate-db-manager');
            $tables["trash-posts"]['name']                  = esc_html__('Trashed posts', 'ultimate-db-manager');

            $tables["moderated-comments"]['name']      = esc_html__('Pending comments', 'ultimate-db-manager');
            $tables["spam-comments"]['name'] 		    = esc_html__('Spam comments', 'ultimate-db-manager');
            $tables["trash-comments"]['name'] 		    = esc_html__('Trashed comments', 'ultimate-db-manager');

            $tables["pingbacks"]['name'] 			    = esc_html__('Pingbacks', 'ultimate-db-manager');
            $tables["trackbacks"]['name'] 			    = esc_html__('Trackbacks', 'ultimate-db-manager');

            $tables["orphan-postmeta"]['name'] 	    = esc_html__('Orphaned post meta', 'ultimate-db-manager');
            $tables["orphan-commentmeta"]['name'] 	    = esc_html__('Orphaned comment meta', 'ultimate-db-manager');
            $tables["orphan-usermeta"]['name'] 	    = esc_html__('Orphaned user meta', 'ultimate-db-manager');
            $tables["orphan-termmeta"]['name'] 	    = esc_html__('Orphaned term meta', 'ultimate-db-manager');

            $tables["orphan-relationships"]['name'] 	= esc_html__('Orphaned relationships', 'ultimate-db-manager');

            $tables["expired-transients"]['name'] 		= esc_html__("Expired transients", 'ultimate-db-manager');

            return $tables;
        }

    }
endif;
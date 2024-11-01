<?php
/**
 * Ultimate_DB_Manager_Cleanup_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Cleanup_Page' ) ) :

    class Ultimate_DB_Manager_Cleanup_Page extends Ultimate_DB_Manager_Admin_Page {

        /**
        * Page number
        *
        * @var int
        */
        protected $page_number = 1;

        /**
        * Initialize
        *
        * @since 1.0.0
        */
        public function init() {
            $pagenum           = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0; // WPCS: CSRF OK
            $this->page_number = max( 1, $pagenum );
            $this->processRequest();
        }


        /**
         * Count cleanup
         *
         * @since 1.0.0
         * @return int
         */
        public function countCleanup() {
            
        }

        /**
         * Pagination
         *
         * @since 1.0.0
         */
        public function pagination() {
            $count = $this->countCleanup();
            ultimate_db_manager_list_pagination( $count );
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

        /**
         * Add page screen hooks
         *
         * @since 1.0.0
         *
         * @param $hook
         */
        public function enqueue_scripts( $hook ) {
            // Load admin styles
            ultimate_db_manager_admin_enqueue_styles( ULTIMATE_DB_MANAGER_VERSION );

            $ultimate_db_manager_data = new Ultimate_DB_Manager_Admin_Data();

            // Load admin cleanup scripts
            ultimate_db_manager_admin_enqueue_scripts_cleanup(
                ULTIMATE_DB_MANAGER_VERSION,
                $ultimate_db_manager_data->get_options_data()
            );
        }

        /**
        * Process request
        *
        * @since 1.0.0
        */
        public function processRequest() {

            if ( ! isset( $_POST['ultimate_cleanup_nonce'] ) ) {
                return;
            }

            $nonce = $_POST['ultimate_cleanup_nonce'];
            if ( ! wp_verify_nonce( $nonce, 'ultimate-cleanup-request' ) ) {
                return;
            }

            $is_redirect = true;
            $action = "";
            if(isset($_POST['ultimate_bulk_action'])){
                $action = sanitize_text_field($_POST['ultimate_bulk_action']);
                $cleanup_names = isset( $_POST['cleanup-names'] ) ? sanitize_text_field( $_POST['cleanup-names'] ) : '';
            }

            switch ( $action ) {
                case 'empty-cleanup' :
                    if ( isset( $cleanup_names ) && !empty( $cleanup_names ) ) {
                        $tables = explode( ',', $cleanup_names );
                        if ( is_array( $tables ) && count( $tables ) > 0 ) {
                            foreach ( $tables as $table ) {
                                $cleanup_helper = new Ultimate_DB_Manager_Cleanup_Helper();
                                $cleanup_helper->empty_table_data($table);
                            }    
                        }
                    }						
                    break;

                default:
                    break;
            }

            if ( $is_redirect ) {
                $fallback_redirect = admin_url( 'admin.php' );
                $fallback_redirect = add_query_arg(
                    array(
                        'page' => $this->get_admin_page(),
                    ),
                    $fallback_redirect
                );

                $this->maybe_redirect_to_referer( $fallback_redirect );
            }

            exit;
        }

        /**
        * Bulk actions
        *
        * @since 1.0.0
        * @return array
        */
        public function bulk_actions() {
            return apply_filters(
                'ultimate_cleanup_bulk_actions',
                array(
                    'empty-cleanup'     => esc_html__( "Empty", 'ultimate-db-manager' ),
                ) );
        }

    }

endif;

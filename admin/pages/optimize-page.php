<?php
/**
 * Ultimate_DB_Manager_Optimize_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Optimize_Page' ) ) :

    class Ultimate_DB_Manager_Optimize_Page extends Ultimate_DB_Manager_Admin_Page {

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
                $optimize_names = isset( $_POST['optimize-names'] ) ? sanitize_text_field( $_POST['optimize-names'] ) : '';
            }

            global $wpdb;
            switch ( $action ) {
                case 'optimize' :
                    if ( isset( $optimize_names ) && !empty( $optimize_names ) ) {
                        $tables = explode( ',', $optimize_names );
                        if ( is_array( $tables ) && count( $tables ) > 0 ) {
                            foreach ( $tables as $table ) {
                                $wpdb->query(
                                    "OPTIMIZE TABLE $table"
                                );
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

            // Load admin optimize scripts
            ultimate_db_manager_admin_enqueue_scripts_optimize(
                ULTIMATE_DB_MANAGER_VERSION,
                $ultimate_db_manager_data->get_options_data()
            );
        }

        /**
         * Get tables to clean (in the current site or MU)
         *
         * @since 1.0.0
         * @return array
         */
        public function getTables(){
            global $wpdb;
            $tables = $wpdb->get_results( 'SHOW FULL TABLES', ARRAY_N );

            foreach ( $tables as $key=>$table ) {
                $optimize_tables[$key]['name'] = $table[0];
                $count_rows     = $wpdb->get_var(
                    "SELECT COUNT(*) FROM $table[0]"
                ); 
                $optimize_tables[$key]['rows'] = $count_rows;
            }

            return $optimize_tables;
        }

        /**
        * Bulk actions
        *
        * @since 1.0.0
        * @return array
        */
        public function bulk_actions() {
            return apply_filters(
                'ultimate_optimize_bulk_actions',
                array(
                    'optimize'     => esc_html__( "Optimize", 'ultimate-db-manager' ),
                ) );
        }

    }
endif;    
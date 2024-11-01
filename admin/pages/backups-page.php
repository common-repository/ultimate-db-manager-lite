<?php
/**
 * Ultimate_DB_Manager_Backups_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Backups_Page' ) ) :

    class Ultimate_DB_Manager_Backups_Page extends Ultimate_DB_Manager_Admin_Page {

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
            $this->filesystem = new Ultimate_DB_Manager_Filesystem_Model();
            $this->processRequest();
            $this->processDownload();
        }

        /**
        * Process download request
        *
        * @since 1.0.0
        */
        public function processDownload() {
            if(isset($_GET['action']) && $_GET['action'] === 'ultimate_download_backup'){
                $filename = sanitize_text_field($_GET['filename']);
                $upload_path = $this->filesystem->get_upload_info();
                $download_file = $upload_path . DIRECTORY_SEPARATOR . $filename;
                if ( is_file( $download_file ) ) {
                    header('Content-Description: File Transfer');
		            header('Content-Type: application/octet-stream');
		            header('Content-Disposition: attachment; filename="'.basename($download_file).'";');
		            header('Expires: 0');
		            header('Cache-Control: must-revalidate');
		            header('Pragma: public');
		            header('Content-Length: ' . filesize($download_file));
			        @readfile( $download_file );
                }
                exit();
            }
        }

        /**
        * Process request
        *
        * @since 1.0.0
        */
        public function processRequest() {

            if ( ! isset( $_POST['ultimate_backups_nonce'] ) ) {
                return;
            }

            $nonce = $_POST['ultimate_backups_nonce'];
            if ( ! wp_verify_nonce( $nonce, 'ultimate-backups-request' ) ) {
                return;
            }

            $is_redirect = true;
            $action = "";
            if(isset($_POST['ultimate_bulk_action'])){
                $action = sanitize_text_field($_POST['ultimate_bulk_action']);
                $backup_names = isset( $_POST['backup-names'] ) ? sanitize_text_field( $_POST['backup-names'] ) : '';
            }

            switch ( $action ) {
                case 'delete-backups' :
                    if ( isset( $backup_names ) && !empty( $backup_names ) ) {
                        $files = explode( ',', $backup_names );
                        $upload_path = $this->filesystem->get_upload_info();
                        if ( is_array( $files ) && count( $files ) > 0 ) {
                            foreach ( $files as $filename ) {
                                $file = $upload_path . DIRECTORY_SEPARATOR . $filename;
                                if ( is_file( $file ) ) {
                                    $this->filesystem->delete($file);
                                }
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
        * @since 1.0
        * @return array
        */
        public function bulk_actions() {
            return apply_filters(
                'ultimate_backups_bulk_actions',
                array(
                    'delete-backups'     => esc_html__( "Delete", 'ultimate-db-manager' ),
                ) );
        }

        /**
         * Count backups
         *
         * @since 1.0.0
         * @return int
         */
        public function countBackups() {
            $upload_path = $this->filesystem->get_upload_info();
            $file_count = 0;
            $files = glob( $upload_path . "/*.sql" );
            if ($files){
                $file_count = count($files);
            }
            return $file_count;
        }

        /**
         * Pagination
         *
         * @since 1.0.0
         */
        public function pagination() {
            $count = $this->countBackups();
            ultimate_db_manager_list_pagination( $count );
        }

        /**
         * Get backups
         *
         * @since 1.0.0
         * @return int
         */
        public function getBackups() {
            $files_info = array();
            $upload_path = $this->filesystem->get_upload_info();
            $files = glob( $upload_path . "/*.sql" );
            foreach ( $files as $key => $value ) {
                $files_info[$key]['name'] = str_replace($upload_path . "/", "", $value);
                $files_info[$key]['date'] = date( "M j G:i:s Y",  filemtime($value));
                $files_info[$key]['size'] = ultimate_human_file_size( filesize($value) );
            }
            return $files_info;
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

            // Load admin backups scripts
            ultimate_db_manager_admin_enqueue_scripts_backups(
                ULTIMATE_DB_MANAGER_VERSION,
                $ultimate_db_manager_data->get_options_data()
            );
        }

    }

endif;

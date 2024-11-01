<?php
/**
 * Ultimate_DB_Manager_Admin_AJAX Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Admin_AJAX' ) ) :

    class Ultimate_DB_Manager_Admin_AJAX {

        /**
         * @var Ultimate_DB_Manager_Table_Model
         */
        public $table;

        /**
         * @var Ultimate_DB_Manager_Filesystem_Model
         */
        public $filesystem;

        /**
         * Ultimate_DB_Manager_Admin_AJAX constructor.
         *
         * @since 1.0
         */
        public function __construct() {

            $this->filesystem = new Ultimate_DB_Manager_Filesystem_Model();
            $this->table = new Ultimate_DB_Manager_Table_Model($this->filesystem);

            // WP Ajax Actions.
            add_action( 'wp_ajax_ultimate_db_trigger_backup', array( $this, 'trigger_backup' ) );
            add_action( 'wp_ajax_ultimate_db_table_backup', array( $this->table, 'ajax_table_backup' ) );
            add_action( 'wp_ajax_ultimate_db_delete_backup', array( $this, 'delete_backup' ) );
            add_action( 'wp_ajax_ultimate_db_empty_cleanup', array( $this, 'empty_cleanup' ) );
            add_action( 'wp_ajax_ultimate_db_save_schedule', array( $this, 'save_schedule' ) );
            add_action( 'wp_ajax_ultimate_db_single_optimize', array( $this, 'single_optimize' ) );
            add_action( 'wp_ajax_ultimate_db_skip_premium', array( $this, 'skip_premium' ) );

        }

        /**
         * Delete Backup
         *
         * @since 1.0.0
         */
        public function delete_backup() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }

            if ( isset( $_POST['fields_data'] ) ) {

                $fields  = $_POST['fields_data'];
                $upload_path = $this->filesystem->get_upload_info( 'path' );

                if ( false === $this->filesystem->is_writable( $upload_path ) ) {
                    $error  = sprintf(
                        esc_html__( '<p><strong>Backup Delete Failed</strong> — We can\'t save database backup to the following folder:<br><strong>%s</strong></p><p>Please adjust the permissions on this folder. <a href="%s" target="_blank">See our documentation for more information »</a></p>', 'ultimate-db-manager' ),
                        $upload_path,
                        'https://wphobby.com'
                    );
                    wp_send_json_error( $error );
                }

                $file = $upload_path . DIRECTORY_SEPARATOR . sanitize_text_field($fields['filename']);
                if ( !is_file( $file ) ) {
                    wp_send_json_error( esc_html__( 'File not exist!', 'ultimate-db-manager' ) );
                }else{
                    $return = $this->filesystem->delete($file);

                    if($return){
                        wp_send_json_success( $return );
                    }else{
                        wp_send_json_error( esc_html__( 'File delete failed!', 'ultimate-db-manager' ) );
                    }
                }
            

            } else {

                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
         * Trigger Backup
         *
         * @since 1.0.0
         */
        public function trigger_backup() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }

            if ( isset( $_POST['fields_data'] ) ) {
                $fields = $_POST['fields_data'];

                $return['dump_path'] = $this->table->get_sql_dump_info( 'backup', 'path' );
                $return['dump_filename']    = wp_basename( $return['dump_path'] );
                if(empty($fields['table_manual_option'] )){
                    $table_manual_option = 'migrate_only_with_prefix';
                }else{
                    $table_manual_option = $fields['table_manual_option'];
                }
                if($table_manual_option === 'migrate_only_with_prefix'){
                    $return['tables']  = $this->table->get_tables();
                }else if($table_manual_option === 'migrate_select'){
                    $return['tables']  = $fields['manual_select_tables'];
                }

                $upload_path = $this->filesystem->get_upload_info( 'path' );

                if ( false === $this->filesystem->is_writable( $upload_path ) ) {
                    $error  = sprintf(
                        esc_html__( '<p><strong>Backup Failed</strong> â€” We can\'t save database backup to the following folder:<br><strong>%s</strong></p><p>Please adjust the permissions on this folder. <a href="%s" target="_blank">See our documentation for more information Â»</a></p>', 'ultimate-db-manager' ),
                        $upload_path,
                        'https://wphobby.com'
                    );
                    wp_send_json_error( $error );
                }

                $fp = $this->filesystem->open( $upload_path . DIRECTORY_SEPARATOR . $return['dump_filename'] );
                $this->table->db_backup_header( $fp );
                $this->filesystem->close( $fp );

                wp_send_json_success( $return );

            } else {

                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
         * Empty Cleanup
         *
         * @since 1.0.0
         */
        public function empty_cleanup() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }

            if ( isset( $_POST['fields_data'] ) ) {
                $fields  = $_POST['fields_data'];
                $cleanup_helper = new Ultimate_DB_Manager_Cleanup_Helper();
                $cleanup_helper->empty_table_data(sanitize_text_field($fields['name']));
                wp_send_json_success( esc_html__( 'Cleanup empty table successfully!', 'ultimate-db-manager' ) );
            } else {
                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
         * Save Schedule
         *
         * @since 1.0.0
         */
        public function save_schedule() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }

            if ( isset( $_POST['fields_data'] ) ) {
                $fields  = $_POST['fields_data'];
                $id      = isset( $fields['schedule_id'] ) ? sanitize_text_field( $fields['schedule_id'] ) : null;
                $id      = intval( $id );
                $title   = sanitize_text_field( $fields['ultimate_schedule_name'] );
                $status  = isset( $fields['schedule_status'] ) ? sanitize_text_field( $fields['schedule_status'] ) : '';
                
                if ( is_null( $id ) || $id <= 0 ) {
                    $form_model = new Ultimate_DB_Manager_Schedule_Model();
                    $action     = 'create';
    
                    if ( empty( $status ) ) {
                        $status = Ultimate_DB_Manager_Schedule_Model::STATUS_DRAFT;
                    }
                }else{
                    $form_model = Ultimate_DB_Manager_Schedule_Model::model()->load( $id );
                    $action     = 'update';
                }

                // Schedule next run time
                $time_length = ultimate_db_shcedule_next_time($fields['update_frequency'], $fields['update_frequency_unit']);
                $fields['next_run_time'] = time() + $time_length;

                // Set Settings to model
                $form_model->settings = $fields;

                // status
                $form_model->status = $status;

                // Save data
                $id = $form_model->save();

                if (!$id) {
                    wp_send_json_error( $id );
                }else{
                    wp_send_json_success( $id );
                }

                wp_send_json_success( esc_html__( 'Schedule saved successfully!', 'ultimate-db-manager' ) );
            } else {
                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
         * Single table optimize
         *
         * @since 1.0.0
         */
        public function single_optimize() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }


            global $wpdb;
            if ( isset( $_POST['fields_data'] ) ) {
                $fields  = $_POST['fields_data'];
                $table   = sanitize_text_field($fields['name']);
                $wpdb->query(
                    "OPTIMIZE TABLE $table"
                );
                wp_send_json_success( esc_html__( 'Optimize single table successfully!', 'ultimate-db-manager' ) );
            } else {
                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
     * Skip premium
     *
     * @since 1.0.0
     */
    public function skip_premium() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
        }

        update_option( 'ultimate-db-skip-premium', 'skip' );
        $message = __( 'skip premium.' );
        wp_send_json_success( $message );        

    }

    }

endif;

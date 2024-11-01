<?php
/**
 * Ultimate_DB_Manager_Core Class
 *
 * Plugin Core Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Core' ) ) :

    class Ultimate_DB_Manager_Core {

        /**
         * @var Ultimate_DB_Manager_Admin
         */
        public $admin;

        /**
         * Store modules objects
         *
         * @var array
         */
        public $modules = array();

        /**
         * Store forms objects
         *
         * @var array
         */
        public $forms = array();

        /**
         * Plugin instance
         *
         * @var null
         */
        private static $instance = null;

        /**
         * Return the plugin instance
         *
         * @since 1.0.0
         * @return Ultimate_DB_Manager_Core
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Ultimate_DB_Manager_Core constructor.
         *
         * @since 1.0
         */
        public function __construct() {
            // Include all necessary files
            $this->includes();

            if ( is_admin() ) {
                // Initialize admin core
                $this->admin = new Ultimate_DB_Manager_Admin();
                // Add sub pages
                $this->admin->add_backups_page();
                $this->admin->add_cleanup_page();
                $this->admin->add_optimize_page();
                // Enabled modules
                $modules = new Ultimate_DB_Manager_Modules();
                $this->admin->add_help_page();
            
            }
        }

        /**
         * Includes
         *
         * @since 1.0.0
         */
        private function includes() {
            // Helpers
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/helpers/helper-core.php';
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/helpers/helper-backup.php';
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/helpers/helper-cleanup.php';

            // Model
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/model/class-table-model.php';
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/model/class-filesystem-model.php';
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/model/class-schedule-model.php';

            // Jobs
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/jobs/class-backup-job.php';

            // Cron Jobs
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/jobs/class-cron-job.php';

            // Modules
            require_once ULTIMATE_DB_MANAGER_DIR . 'includes/class-modules.php';

            if ( is_admin() ) {
                require_once ULTIMATE_DB_MANAGER_DIR . 'admin/abstracts/class-admin-page.php';
                require_once ULTIMATE_DB_MANAGER_DIR . 'admin/abstracts/class-admin-module.php';
                require_once ULTIMATE_DB_MANAGER_DIR . 'admin/classes/class-admin.php';
            }

        }

    }

endif;

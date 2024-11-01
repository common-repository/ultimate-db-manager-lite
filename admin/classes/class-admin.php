<?php
/**
 * Ultimate_DB_Manager_Admin Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Admin' ) ) :

    class Ultimate_DB_Manager_Admin {

        /**
         * @var array
         */
        public $pages = array();

        /**
         * Ultimate_DB_Manager_Admin constructor.
         */
        public function __construct() {
            $this->includes();

            // Init admin pages
            add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );

            // Init Admin AJAX class
            new Ultimate_DB_Manager_Admin_AJAX();

            /**
             * Triggered when Admin is loaded
             */
            do_action( 'ultimate_db_manager_admin_loaded' );
        }

        /**
         * Include required files
         *
         * @since 1.0.0
         */
        private function includes() {
            // Admin pages
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/pages/dashboard-page.php';
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/pages/backups-page.php';
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/pages/cleanup-page.php';
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/pages/optimize-page.php';
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/pages/help-page.php';

            // Admin AJAX
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/classes/class-admin-ajax.php';

            // Admin Data
            require_once ULTIMATE_DB_MANAGER_DIR . '/admin/classes/class-admin-data.php';
        }

        /**
         * Initialize Dashboard page
         *
         * @since 1.0.0
         */
        public function add_dashboard_page() {
            $title = esc_html__( 'Ultimate DB', 'ultimate-db-manager' );
            $this->pages['ultimate_db_manager']           = new Ultimate_DB_Manager_Dashboard_Page( 'ultimate-db-manager', 'dashboard', $title, $title, false, false );
            $this->pages['ultimate_db_manager-dashboard'] = new Ultimate_DB_Manager_Dashboard_Page( 'ultimate-db-manager', 'dashboard', esc_html__( 'Ultimate WP DB Manager Dashboard', 'ultimate-db-manager' ), esc_html__( 'Dashboard', 'ultimate-db-manager' ), 'ultimate-db-manager' );
        }

        /**
         * Add backups page
         *
         * @since 1.0.0
         */
        public function add_backups_page() {
            add_action( 'admin_menu', array( $this, 'init_backups_page' ) );
        }

        /**
         * Initialize Logs page
         *
         * @since 1.0.0
         */
        public function init_backups_page() {
            $this->pages['ultimate_db_backups'] = new Ultimate_DB_Manager_Backups_Page(
                'ultimate-db-manager-backups',
                'backups',
                esc_html__( 'Backups', 'ultimate-db-manager' ),
                esc_html__( 'Backups', 'ultimate-db-manager' ),
                'ultimate-db-manager'
            );
        }

        /**
         * Add cleanup page
         *
         * @since 1.0.0
         */
        public function add_cleanup_page() {
            add_action( 'admin_menu', array( $this, 'init_cleanup_page' ) );
        }

        /**
         * Initialize Logs page
         *
         * @since 1.0.0
         */
        public function init_cleanup_page() {
            $this->pages['ultimate_db_cleanup'] = new Ultimate_DB_Manager_Cleanup_Page(
                'ultimate-db-manager-cleanup',
                'cleanup',
                esc_html__( 'Cleanup', 'ultimate-db-manager' ),
                esc_html__( 'Cleanup', 'ultimate-db-manager' ),
                'ultimate-db-manager'
            );
        }

        /**
         * Add optimize page
         *
         * @since 1.0.0
         */
        public function add_optimize_page() {
            add_action( 'admin_menu', array( $this, 'init_optimize_page' ) );
        }

        /**
         * Initialize Logs page
         *
         * @since 1.0.0
         */
        public function init_optimize_page() {
            $this->pages['ultimate_db_optimize'] = new Ultimate_DB_Manager_Optimize_Page(
                'ultimate-db-manager-optimize',
                'optimize',
                esc_html__( 'Optimize', 'ultimate-db-manager' ),
                esc_html__( 'Optimize', 'ultimate-db-manager' ),
                'ultimate-db-manager'
            );
        }

        /**
         * Add help page
         *
         * @since 1.0.0
         */
        public function add_help_page() {
            add_action( 'admin_menu', array( $this, 'init_help_page' ) );
        }

        /**
         * Initialize Logs page
         *
         * @since 1.0.0
         */
        public function init_help_page() {
            $this->pages['ultimate_db_help'] = new Ultimate_DB_Manager_Help_Page(
                'ultimate-db-manager-help',
                'help',
                esc_html__( 'Help', 'ultimate-db-manager' ),
                esc_html__( 'Help', 'ultimate-db-manager' ),
                'ultimate-db-manager'
            );
        }


    }

endif;

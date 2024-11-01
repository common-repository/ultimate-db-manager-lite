<?php
/**
 * Plugin Name: Ultimate WP DB Manager Lite
 * Plugin URI: http://wphobby.com
 * Description: WordPress Database Backup, Optimize & Cleanup
 * Version: 1.3.5
 * Author: wphobby
 * Author URI: https://wphobby.com/
 *
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Set constants
 */
if ( ! defined( 'ULTIMATE_DB_MANAGER_DIR' ) ) {
    define( 'ULTIMATE_DB_MANAGER_DIR', plugin_dir_path(__FILE__) );
}

if ( ! defined( 'ULTIMATE_DB_MANAGER_URL' ) ) {
    define( 'ULTIMATE_DB_MANAGER_URL', plugin_dir_url(__FILE__) );
}

if ( ! defined( 'ULTIMATE_DB_MANAGER_VERSION' ) ) {
    define( 'ULTIMATE_DB_MANAGER_VERSION', '1.3.5' );
}

/**
 * Class Ultimate_DB_Manager
 *
 * Main class. Initialize plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Ultimate_DB_Manager' ) ) {
    /**
     * Ultimate_DB_Manager
     */
    class Ultimate_DB_Manager {

        const DOMAIN = 'ultimate-db-manager';

        /**
         * Instance of Ultimate_DB_Manager
         *
         * @since  1.0.0
         * @var (Object) Ultimate_DB_Manager
         */
        private static $_instance = null;

        /**
         * Get instance of Ultimate_DB_Manager
         *
         * @since  1.0.0
         *
         * @return object Class object
         */
        public static function get_instance() {
            if ( ! isset( self::$_instance ) ) {
                self::$_instance = new self;
            }
            return self::$_instance;
        }

        /**
         * Constructor
         *
         * @since  1.0.0
         */
        private function __construct() {
            $this->includes();
            $this->init();
        }

        /**
         * Load plugin files
         *
         * @since 1.0
         */
        private function includes() {
            // Core files.
            require_once ULTIMATE_DB_MANAGER_DIR . '/includes/class-core.php';
        }


        /**
         * Init the plugin
         *
         * @since 1.0.0
         */
        private function init() {
            // Initialize plugin core
            $this->ultimate_db_manager = Ultimate_DB_Manager_Core::get_instance();

            // Initial Class for WP Cron Jobs
            Ultimate_DB_Manager_Cron_Job::get_instance();

            /**
             * Triggered when plugin is loaded
             */
            do_action( 'ultimate_db_manager_loaded' );

            $skip_premium = get_option( 'ultimate-db-skip-premium', false );
            if($skip_premium !== 'skip'){
                add_action('current_screen', array( $this, 'current_screen_action') );
            }

        }

        /**
        * Current screen action
        *
        * @since 1.0.1
        * @return void
        */
        public function current_screen_action() {
            $screen = get_current_screen();
            $where = array(
                'toplevel_page_ultimate-db-manager',
                'ultimate-db_page_ultimate-db-manager-backups',
                'ultimate-db_page_ultimate-db-manager-cleanup',
                'ultimate-db_page_ultimate-db-manager-optimize',
                'ultimate-db_page_ultimate-db-manager-schedule',
                'ultimate-db_page_ultimate-db-manager-cleanup',
                'ultimate-db_page_ultimate-db-manager-help'
            );

            $enable_notice = true;
            if ( in_array($screen->base, $where) ) {
                $enable_notice = false;
            };

            if($enable_notice){
                add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ));
            }

        }

        /**
        * Display an admin notice for premium version link
        *
        * @since 1.0.1
        * @return void
        * @use admin_notices hooks
        */
        public function display_admin_notice() {
        ?>
            <div class='ultimate-notice-container notice success'>
                <div class='ultimate-notice-inner-wrapper'>
                    <div class="ultimate-notice-message-container">
                        <h4 class="ultimate-notice-header"><?php esc_html_e( 'Try the Ultimate WP DB Manager premium version!', 'ultimate-db-manager' ); ?></h4>
                        <span class="ultimate-notice-message"><?php esc_html_e( 'Clean database, optimize database, and make these jobs schedule running hourly, daily and weekly, more backup database advanced options, dedicated tech support and more.', 'ultimate-db-manager' ); ?></span>
                    </div>
                    <div class="ultimate-notice-actions">
                        <a href="<?php echo esc_url('https://1.envato.market/oQDB9') ?>" class="ultimate-notice-button button button-primary"><?php esc_html_e( 'Activate Premium', 'ultimate-db-manager' ); ?></a>
                        <a href="#" class="ultimate-notice-button ultimate-notice-skip button"><?php esc_html_e( 'No, thanks, not now', 'ultimate-db-manager' ); ?></a>
                    </div>
                </div>
            </div>
        <?php
        }

        public function enqueue_scripts() {
            wp_enqueue_style( 'ultimate-db-notice-style', ULTIMATE_DB_MANAGER_URL . 'assets/css/notice.css', array(), ULTIMATE_DB_MANAGER_VERSION, false );
            wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), ULTIMATE_DB_MANAGER_VERSION, false );
            wp_register_script(
                'ultimate-db-notice',
                ULTIMATE_DB_MANAGER_URL . '/assets/js/notice.js',
                array(
                    'jquery'
                ),
                ULTIMATE_DB_MANAGER_VERSION,
                true
            );

            wp_enqueue_script( 'ultimate-db-notice' );

            $ultimate_db_data = new Ultimate_DB_Manager_Admin_Data();
            $data = $ultimate_db_data->get_options_data();
            wp_localize_script( 'ultimate-db-notice', 'Ultimate_DB_Manager_Data', $data );
        }

    }
}

if ( ! function_exists( 'ultimate_db_manager' ) ) {
    function ultimate_db_manager() {
        return Ultimate_DB_Manager::get_instance();
    }

    /**
     * Init the plugin and load the plugin instance
     *
     * @since 1.0.0
     */
    add_action( 'plugins_loaded', 'ultimate_db_manager' );
}

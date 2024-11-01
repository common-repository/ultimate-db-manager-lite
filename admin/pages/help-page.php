<?php
/**
 * Ultimate_DB_Manager_Help_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Help_Page' ) ) :

    class Ultimate_DB_Manager_Help_Page extends Ultimate_DB_Manager_Admin_Page {

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

            // Load admin help scripts
            ultimate_db_manager_admin_enqueue_scripts_help(
                ULTIMATE_DB_MANAGER_VERSION
            );
        }

    }

endif;

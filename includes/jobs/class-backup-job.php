<?php
/**
 * Ultimate_DB_Manager_Backup_Job Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Backup_Job' ) ) :

    class Ultimate_DB_Manager_Backup_Job {

        /**
         * @var Ultimate_DB_Manager_Table_Model
         */
        public $table;

        /**
         * Ultimate_DB_Manager_Backup_Job constructor.
         *
         */
        public function __construct() {

        }

    }

endif;
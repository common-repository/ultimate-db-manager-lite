<?php
/**
 * Ultimate_DB_Manager_Cron_Job Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Cron_Job' ) ) :

   class Ultimate_DB_Manager_Cron_Job {

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
        * @return Ultimate_DB_Manager_Cron_Job
        */
       public static function get_instance() {
           if ( is_null( self::$instance ) ) {
               self::$instance = new self();
           }

           return self::$instance;
       }

       /**
       * Ultimate_DB_Manager_Cron_Job constructor.
       *
       * @since 1.0.0
       */
       public function __construct() {
           // Add a custom interval
           add_filter( 'cron_schedules', array( $this, 'ultimate_db_add_cron_interval' ) );
           // Setup Cron Job
           $this->cron_setup();
       }

       /**
        * Add a custom interval
        *
        * @since 1.0.0
        */
       public function ultimate_db_add_cron_interval( $schedules ) {
           $schedules['once_ultimate_db_a_minute'] = array(
               'interval' => 60,
               'display'  => esc_html__( 'Once Ultimate DB manager Job a Minute', 'ultimate-db-manager' )
           );
           return $schedules;
       }

       /**
        * Setup Cron Job
        *
        * @since 1.0.0
        */
       public function cron_setup(){

         if ( ! wp_next_scheduled( 'ultimate_db_cron_hook' ) ) {
            wp_schedule_event( time(), 'once_ultimate_db_a_minute', 'ultimate_db_cron_hook' );
         }

         // Add Cron Job Hook Function
         add_action( 'ultimate_db_cron_hook', array( $this, 'ultimate_db_cron_exec' )  );

       }

       /**
        * Cron Job Execute
        *
        * @since 1.0.0
        */
       public function ultimate_db_cron_exec(){
           // Run schedules job here
           $models = Ultimate_DB_Manager_Schedule_Model::model()->get_all_models();

           $campaigns = $models['models'];

           foreach($campaigns as $key=>$model){
               $settings = $model->settings;
               $current_time = time();

               // Run this campaign when time on schedule time
               if($current_time >= $settings['next_run_time']){
                   $model->run();
                   // Update next run time
                   $time_length = ultimate_db_shcedule_next_time($settings['update_frequency'], $settings['update_frequency_unit']);
                   $settings['next_run_time'] = time() + $time_length;
                   // Save Settings to model
                   $model->settings = $settings;
                   // Save data
                   $model->save();
               }
           }


           
       }

       
   }

endif;

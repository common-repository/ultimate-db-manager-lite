<?php
/**
 * Ultimate_DB_Manager_Schedule_New_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Schedule_New_Page' ) ) :

class Ultimate_DB_Manager_Schedule_New_Page extends Ultimate_DB_Manager_Admin_Page {
    
  
    /**
     * Get wizard title
     *
     * @since 1.0
     * @return mixed
     */
    public function getWizardTitle() {
        if ( isset( $_REQUEST['id'] ) ) { // WPCS: CSRF OK
            return esc_html__( "Edit Schedule", 'ultimate-db-manager' );
        } else {
            return esc_html__( "New Schedule", 'ultimate-db-manager' );
        }
    }

    /**
     * Add page screen hooks
     *
     * @since 1.0.0
     * @param $hook
     */
    public function enqueue_scripts( $hook ) {
        // Load admin styles
        ultimate_db_manager_admin_enqueue_styles( ULTIMATE_DB_MANAGER_VERSION );

        $ultimate_db_manager_data = new Ultimate_DB_Manager_Admin_Data();

        // Load admin schedule edit scripts
        ultimate_db_manager_admin_enqueue_scripts_schedule_edit(
            ULTIMATE_DB_MANAGER_VERSION,
            $ultimate_db_manager_data->get_options_data()
        );    
        
    }

    /**
     * Render page header
     *
     * @since 1.0
     */
    protected function render_header() { ?>
        <?php
        if ( $this->template_exists( $this->folder . '/header' ) ) {
            $this->template( $this->folder . '/header' );
        } else {
            ?>
            <h1 class="ultimate-header-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <?php } ?>
        <?php
    }

    /**
     * Return single model
     *
     * @since 1.0.0
     *
     * @param int $id
     *
     * @return array
     */
    public function get_single_model( $id ) {
        $data = Ultimate_DB_Manager_Schedule_Model::model()->get_single_model( $id );

        return $data;
    }



}

endif;

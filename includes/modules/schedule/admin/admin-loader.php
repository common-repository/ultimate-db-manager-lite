<?php
/**
 * Ultimate_DB_Manager_Schedule_Admin Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Schedule_Admin' ) ) :
	
class Ultimate_DB_Manager_Schedule_Admin extends Ultimate_DB_Manager_Admin_Module {

	/**
	 * Init module admin
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->module       = Ultimate_DB_Manager_Schedule::get_instance();
		$this->page         = 'ultimate-db-manager-schedule';
		$this->page_edit    = 'ultimate-db-manager-schedule-wizard';
	}

	/**
	 * Include required files
	 *
	 * @since 1.0
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/admin-page-new.php';
		include_once dirname( __FILE__ ) . '/admin-page-view.php';
	}

	/**
	 * Add module pages to Admin
	 *
	 * @since 1.0
	 */
	public function add_menu_pages() {
		new Ultimate_DB_Manager_Schedule_Page( $this->page, 'schedule/list', esc_html__( 'Schedules', 'ultimate-db-manager' ), esc_html__( 'Schedules', 'ultimate-db-manager' ), 'ultimate-db-manager' );
		new Ultimate_DB_Manager_Schedule_New_Page( $this->page_edit, 'schedule/wizard', esc_html__( 'Edit Schedule', 'ultimate-db-manager' ), esc_html__( 'New Schedule', 'ultimate-db-manager' ), 'ultimate-db-manager' );
	}

	/**
	 * Remove necessary pages from menu
	 *
	 * @since 1.0
	 */
	public function hide_menu_pages() {
		remove_submenu_page( 'ultimate-db-manager', $this->page_edit );
	}

	/**
	 * Return template
	 *
	 * @since 1.0
	 * @return Ultimate_DB_Manager_Template|false
	 */
	private function get_template() {
		if( isset( $_GET['template'] ) )  {
			$id = trim( sanitize_text_field( $_GET['template'] ) );
		} else {
			$id = 'blank';
		}

		foreach ( $this->module->templates as $key => $template ) {
			if ( $template->options['id'] === $id ) {
				return $template;
			}
		}

		return false;
	}
}

endif;

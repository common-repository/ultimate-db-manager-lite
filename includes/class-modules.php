<?php
/**
 * Ultimate_DB_Manager_Modules Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Modules' ) ) :

class Ultimate_DB_Manager_Modules {

	/**
	 * Store modules objects
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 * Ultimate_DB_Manager_Modules constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->includes();
		$this->load_modules();
	}

	/**
	 * Includes
	 *
	 * @since 1.0
	 */
	private function includes() {

		require_once ULTIMATE_DB_MANAGER_DIR . '/includes/abstracts/abstract-class-module.php';
	}

	/**
	 * Load modules
	 *
	 * @since 1.0
	 */
	private function load_modules() {
		/**
		 * Filters modules list
		 */
		$modules = apply_filters( 'ultimate_db_manager_modules', array(
			'schedule' => array(
				'class'	  => 'Schedule',
				'slug'  => 'schedule',
				'label'	  => esc_html__( 'schedule', 'ultimate-db-manager' )
			),
		) );

		array_walk( $modules, array( $this, 'load_module' ) );
	}

	/**
	 * Load module
	 *
	 * @since 1.0
	 * @param $data
	 * @param $id
	 */
	public function load_module( $data, $id ) {
		$module_class = 'Ultimate_DB_Manager_' . $data[ 'class' ];
		$module_slug = $data[ 'slug' ];
		$module_label = $data[ 'label' ];
		
		// Include module
		$path = ULTIMATE_DB_MANAGER_DIR . '/includes/modules/' . $module_slug . '/loader.php';
		if ( file_exists( $path ) ) {
			include_once $path;
		}

		if ( class_exists( $module_class ) ) {
			$module_object = new $module_class( $id, $module_label );

			$this->modules[ $id ] = $module_object;
		}
	}

	/**
	 * Retrieve modules objects
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_modules() {
		return $this->modules;
	}
}

endif;

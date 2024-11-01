<?php
/**
 * Ultimate_DB_Manager_Admin_Page Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Admin_Page' ) ) :
    
    abstract class Ultimate_DB_Manager_Admin_Page {

        /**
        * Current page ID
        *
        * @var number
        */
        public $page_id = null;

        /**
        * Current page slug
        *
        * @var string
        */
        protected $page_slug = '';

        /**
        * Path to view folder
        *
        * @var string
        */
        protected $folder = '';

        /**
        * All registered content boxes
        *
        * @var array
        */
        protected $content_boxes = array();

        /**
         * @var Ultimate_DB_Manager_Table_Model
         */
        public $table;

        /**
         * @var Ultimate_DB_Manager_Filesystem_Model
         */
        public $filesystem;

        /**
         * @var Ultimate_DB_Manager_Cleanup_Helper
         */
        public $cleanup_helper;
        
        /**
        * @since 1.0.0
        *
        * @param string $page_slug  Page slug.
        * @param string $folder
        * @param string $page_title Page title.
        * @param string $menu_title Menu title.
        * @param bool   $parent     Parent or not.
        * @param bool   $render     Render the page.
        */
        public function __construct(
            $page_slug,
            $folder = '',
            $page_title,
            $menu_title,
            $parent = false,
            $render = true
        ) {
            $this->page_slug = $page_slug;
            $this->folder    = $folder;

            if ( ! $parent ) {
                $this->page_id = add_menu_page(
                    $page_title,
                    $menu_title,
                    ultimate_db_manager_get_admin_cap(),
                    $page_slug,
                    $render ? array( $this, 'render' ) : null,
                    'dashicons-buddicons-replies'
                );
            } else {
                $this->page_id = add_submenu_page(
                    $parent,
                    $page_title,
                    $menu_title,
                    ultimate_db_manager_get_admin_cap(),
                    $page_slug,
                    $render ? array( $this, 'render' ) : null
                );
            }

            if ( $render ) {
                $this->render_page_hooks();
            }
            $this->init();


        }

        /**
         * Use that method instead of __construct
         *
         * @since 1.0.0
         */
        public function init() {
            $this->filesystem = new Ultimate_DB_Manager_Filesystem_Model();
            $this->table = new Ultimate_DB_Manager_Table_Model($this->filesystem);
            $this->cleanup_helper = new Ultimate_DB_Manager_Cleanup_Helper();
        }

     /**
	 * Hooks before content render
	 *
	 * @since 1.0
	 */
	public function render_page_hooks() {
        add_filter( 'load-' . $this->page_id, array( $this, 'add_page_scripts' ) );
	}

        /**
         * Render page container
         *
         * @since 1.0.0
         */
        public function render() {
        ?>
            <main class="ultimate-wrap <?php echo esc_attr( 'ultimate-db-manager-' . $this->page_slug ); ?>">

                <?php
                $this->render_menu();
                ?>

                <div class="ultimate-content-wrap">

                <?php
                $this->render_header();

                $this->render_page_content();

                $this->render_footer();
                ?>

                </div>

            </main>

            <?php
        }

        /**
         * Render page menu
         *
         * @since 1.0.0
         */
        protected function render_menu() {
            if ( $this->template_exists( 'menu/header' ) ) {
               $this->template( 'menu/header' );
            }
        }

        /**
         * Render page header
         *
         * @since 1.0.0
         */
        protected function render_header() {
            ?>

            <header class="ultimate-header">
                <?php
                if ( $this->template_exists( $this->folder . '/header' ) ) {
                    $this->template( $this->folder . '/header' );
                } else {
                    ?>
                    <h1 class="ultimate-header-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <?php } ?>

            </header>

            <?php
        }

        /**
         * Render page footer
         *
         * @since 1.0
         */
        protected function render_footer() {

            $footer_text = sprintf(/* translators: ... */
                esc_html__( 'Made with %s by WPHobby', 'ultimate-db-manager' ),
                ' <ion-icon class="ultimate-icon-heart" name="heart"></ion-icon>'
            );

            if ( $this->template_exists( $this->folder . '/footer' ) ) {
                $this->template( $this->folder . '/footer' );
            }
            ?>
            <div class="ultimate-footer"><?php echo $footer_text; ?></div>

            <?php
        }

        /**
         * Render actual page template
         *
         * @since 1.0.0
         */
        protected function render_page_content() {
            $this->template( $this->folder . '/content' );
        }

        /**
         * Load an admin template
         *
         * @since 1.0
         *
         * @param       $path
         * @param array $args
         * @param bool  $echo
         *
         * @return string
         */
        public function template( $path, $args = array(), $echo = true ) {
            $file    = ULTIMATE_DB_MANAGER_DIR . "/admin/views/$path.php";
            $content = '';

            if ( is_file( $file ) ) {
                ob_start();

                $settings = $args;

                include $file;

                $content = ob_get_clean();
            }

            if ( $echo ) {
                echo $content;// phpcs:ignore
            }

            return $content;
        }

        /**
         * Check if template exist
         *
         * @since 1.0.0
         *
         * @param $path
         *
         * @return bool
         */
        protected function template_exists( $path ) {
            $file = ULTIMATE_DB_MANAGER_DIR . "admin/views/$path.php";

            return is_file( $file );
        }

        /**
         * Add page screen hooks
         *
         * @since 1.0.0
         */
        public function add_page_scripts() {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'init', array( $this, 'init_scripts' ) );
        }

        /**
         * Add page screen hooks
         *
         * @since 1.0.0
         *
         * @param $hook
         */
        public function enqueue_scripts( $hook ) {

        }

        /**
         * Init Admin scripts
         *
         * @since 1.0.0
         *
         * @param $hook
         */
        public function init_scripts( $hook ) {
            // Init jquery ui
            ultimate_db_manager_admin_jquery_ui_init();
        }


        /**
         * Redirect to referer if available
         *
         * @since 1.0.0
         *
         * @param string $fallback_redirect url if referer not found
         */
        protected function maybe_redirect_to_referer( $fallback_redirect = '' ) {
            $referer = wp_get_referer();
            $referer = ! empty( $referer ) ? $referer : wp_get_raw_referer();
            if ( $referer ) {
                wp_safe_redirect( $referer );
            } elseif ( $fallback_redirect ) {
                wp_safe_redirect( $fallback_redirect );
            } else {
                $admin_url = admin_url( 'admin.php' );
                $admin_url = add_query_arg(
                    array(
                        'page' => $this->get_admin_page(),
                    ),
                    $admin_url
                );
                wp_safe_redirect( $admin_url );
            }

            exit();
        }

        /**
         * Get admin page param
         *
         * @since 1.0.0
         * @return string
         */
        protected function get_admin_page() {
            return ( isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '' );
        }


    }

endif;

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Return needed cap for admin pages
 *
 * @since 1.0.0
 * @return string
 */
function ultimate_db_manager_get_admin_cap() {
    $cap = 'manage_options';

    if ( is_multisite() && is_network_admin() ) {
        $cap = 'manage_network';
    }

    return apply_filters( 'ultimate_db_manager_admin_cap', $cap );
}

/**
 * Enqueue admin styles
 *
 * @since 1.0.0
 *
 * @param $version
 */
function ultimate_db_manager_admin_enqueue_styles( $version ) {
    wp_enqueue_style( 'magnific-popup', ULTIMATE_DB_MANAGER_URL . 'assets/css/magnific-popup.css', array(), $version, false );
    wp_enqueue_style( 'ultimate-db-manager-main-style', ULTIMATE_DB_MANAGER_URL . 'assets/css/main.css', array(), $version, false );
}

/**
 * Enqueue admin scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts( $version, $data = array(), $l10n = array() ) {

    if ( function_exists( 'wp_enqueue_editor' ) ) {
        wp_enqueue_editor();
    }
    if ( function_exists( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_enqueue_script( 'jquery-magnific-popup', ULTIMATE_DB_MANAGER_URL . 'assets/js/library/jquery.magnific-popup.min.js', array( 'jquery' ), $version, false );
}

/**
 * Enqueue admin backups scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_backups( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_register_script(
        'ultimate-db-manager-backups',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/backups.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-backups' );

    wp_localize_script( 'ultimate-db-manager-backups', 'Ultimate_DB_Manager_Data', $data );
}

/**
 * Enqueue admin backups scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_schedule_edit( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_register_script(
        'ultimate-db-manager-schedule-edit',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/schedule-edit.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-schedule-edit' );

    wp_localize_script( 'ultimate-db-manager-schedule-edit', 'Ultimate_DB_Manager_Data', $data );
}

/**
 * Enqueue admin backups scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_schedule_list( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_register_script(
        'ultimate-db-manager-schedule-list',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/schedule-list.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-schedule-list' );

    wp_localize_script( 'ultimate-db-manager-schedule-list', 'Ultimate_DB_Manager_Data', $data );
}

/**
 * Enqueue admin cleanup scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_cleanup( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_register_script(
        'ultimate-db-manager-cleanup',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/cleanup.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-cleanup' );

    wp_localize_script( 'ultimate-db-manager-cleanup', 'Ultimate_DB_Manager_Data', $data );
}

/**
 * Enqueue admin optimize scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_optimize( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_register_script(
        'ultimate-db-manager-optimize',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/optimize.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-optimize' );

    wp_localize_script( 'ultimate-db-manager-optimize', 'Ultimate_DB_Manager_Data', $data );
}


/**
 * Enqueue admin dashboard scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_dashboard( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );

    wp_enqueue_script( 'jquery-magnific-popup', ULTIMATE_DB_MANAGER_URL . 'assets/js/library/jquery.magnific-popup.min.js', array( 'jquery' ), $version, false );

    wp_register_script(
        'ultimate-db-manager-dashboard',
        ULTIMATE_DB_MANAGER_URL . 'assets/js/dashboard.js',
        array(
            'jquery'
        ),
        $version,
        true
    );

    wp_enqueue_script( 'ultimate-db-manager-dashboard' );

    wp_localize_script( 'ultimate-db-manager-dashboard', 'Ultimate_DB_Manager_Data', $data );
}
/**
 * Enqueue admin help scripts
 *
 * @since 1.0.0
 *
 * @param       $version
 * @param array $data
 * @param array $l10n
 */
function ultimate_db_manager_admin_enqueue_scripts_help( $version, $data = array(), $l10n = array() ) {
    wp_enqueue_script( 'ionicons', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array(), $version, false );
   
}
/**
 * Load admin scripts
 *
 * @since 1.0.0
 */
function ultimate_db_manager_admin_jquery_ui_init() {
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-mouse' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-resize' );
    wp_enqueue_style( 'wp-color-picker' );
}

/**
 * Return AJAX url
 *
 * @since 1.0.0
 * @return mixed
 */
function ultimate_db_manager_ajax_url() {
    return admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
}

/**
 * Handle all pagination
 *
 * @since 1.0
 *
 * @param int $total - the total records
 * @param string $type - The type of page (listings or entries)
 *
 * @return string
 */
function ultimate_db_manager_list_pagination( $total, $type = 'listings' ) {
    $pagenum     = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0; // phpcs:ignore
    $page_number = max( 1, $pagenum );
    $global_settings = get_option('ultimate_db_manager_global_settings');
    $per_page = isset($global_settings['ultimate-campaign-per-page']) ? $global_settings['ultimate-campaign-per-page'] : 10;
    if ( $total > $per_page ) {
        $removable_query_args = wp_removable_query_args();

        $current_url   = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        $current_url   = remove_query_arg( $removable_query_args, $current_url );
        $current       = $page_number + 1;
        $total_pages   = ceil( $total / $per_page );
        $total_pages   = absint( $total_pages );
        $disable_first = false;
        $disable_last  = false;
        $disable_prev  = false;
        $disable_next  = false;
        $mid_size      = 2;
        $end_size      = 1;
        $show_skip     = false;

        if ( $total_pages > 10 ) {
            $show_skip = true;
        }

        if ( $total_pages >= 4 ) {
            $disable_prev = true;
            $disable_next = true;
        }

        if ( 1 === $page_number ) {
            $disable_first = true;
        }

        if ( $page_number === $total_pages ) {
            $disable_last = true;

        }

        ?>
        <ul class="ultimate-pagination">

            <?php if ( ! $disable_first ) : ?>
                <?php
                $prev_url  = esc_url( add_query_arg( 'paged', min( $total_pages, $page_number - 1 ), $current_url ) );
                $first_url = esc_url( add_query_arg( 'paged', min( 1, $total_pages ), $current_url ) );
                ?>
                <?php if ( $show_skip ) : ?>
                    <li class="ultimate-pagination--prev">
                        <a href="<?php echo esc_attr( $first_url ); ?>"><i class="ultimate-icon-arrow-skip-start" aria-hidden="true"></i></a>
                    </li>
                <?php endif; ?>
                <?php if ( $disable_prev ) : ?>
                    <li class="ultimate-pagination--prev">
                        <a href="<?php echo esc_attr( $prev_url ); ?>"><i class="ultimate-icon-chevron-left" aria-hidden="true"></i></a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php
            $dots = false;
            for ( $i = 1; $i <= $total_pages; $i ++ ) :
                $class = ( $page_number === $i ) ? 'ultimate-active' : '';
                $url   = esc_url( add_query_arg( 'paged', ( $i ), $current_url ) );
                if ( ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total_pages - $end_size ) ) {
                    ?>
                    <li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_attr( $url ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $i ); ?></a></li>
                    <?php
                    $dots = true;
                } elseif ( $dots ) {
                    ?>
                    <li class="ultimate-pagination-dots"><span><?php esc_html_e( '&hellip;' ); ?></span></li>
                    <?php
                    $dots = false;
                }

                ?>

            <?php endfor; ?>

            <?php if ( ! $disable_last ) : ?>
                <?php
                $next_url = esc_url( add_query_arg( 'paged', min( $total_pages, $page_number + 1 ), $current_url ) );
                $last_url = esc_url( add_query_arg( 'paged', max( $total_pages, $page_number - 1 ), $current_url ) );
                ?>
                <?php if ( $disable_next ) : ?>
                    <li class="ultimate-pagination--next">
                        <a href="<?php echo esc_attr( $next_url ); ?>"><i class="ultimate-icon-chevron-right" aria-hidden="true"></i></a>
                    </li>
                <?php endif; ?>
                <?php if ( $show_skip ) : ?>
                    <li class="ultimate-pagination--next">
                        <a href="<?php echo esc_attr( $last_url ); ?>"><i class="ultimate-icon-arrow-skip-end" aria-hidden="true"></i></a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <?php
    }
}

/**
 * Converts bytes into human readable file size.
 *
 * @param string $bytes
 * @return string human readable file size (2,87 Мб)
 */
function ultimate_human_file_size($bytes)
{
    $bytes = floatval($bytes);
    $arBytes = array(
        0 => array(
            "UNIT" => "TB",
            "VALUE" => pow(1024, 4)
        ),
        1 => array(
            "UNIT" => "GB",
            "VALUE" => pow(1024, 3)
        ),
        2 => array(
            "UNIT" => "MB",
            "VALUE" => pow(1024, 2)
        ),
        3 => array(
            "UNIT" => "KB",
            "VALUE" => 1024
        ),
        4 => array(
            "UNIT" => "B",
            "VALUE" => 1
        ),
    );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

/**
 * Get schedule name by id
 *
 * @since 1.0.0
 * @return string
 */
function ultimate_db_manager_get_schedule_name($id) {
    $model = Ultimate_DB_Manager_Schedule_Model::model()->load( $id );

	$settings = $model->settings;

    // Return Schedule Name
	if ( ! empty( $settings['ultimate_schedule_name'] ) ) {
		return $settings['ultimate_schedule_name'];
	}
}

/**
 * Get schedule next run time by id
 *
 * @since 1.0.0
 * @return string
 */
function ultimate_db_manager_get_next_run_time($id) {
    $model = Ultimate_DB_Manager_Schedule_Model::model()->load( $id );

	$settings = $model->settings;

    // Return schedule next run time
	if ( ! empty( $settings['next_run_time'] ) ) {
        return wp_date( "M j G:i:s Y", $settings['next_run_time'], wp_timezone() );
	}
}

/**
 * Central per page for form view
 *
 * @since 1.0.0
 * @return int
 */
function ultimate_db_manager_view_per_page( $type = 'listings' ) {

    $global_settings = get_option('ultimate_db_global_settings');
    $per_page = isset($global_settings['ultimate-schedule-per-page']) ? $global_settings['ultimate-schedule-per-page'] : 10;

	// force at least 1 data per page
	if ( $per_page < 1 ) {
		$per_page = 1;
	}
	return apply_filters( 'ultimate_db_per_page', $per_page, $type );
}

/**
 * Schedule next run time
 *
 * @since 1.0.0
 * @return int
 */
function ultimate_db_shcedule_next_time($update_frequency, $update_frequency_unit){
    $time_length = 0;

    switch ( $update_frequency_unit ) {
        case 'Minutes':
            $time_length = $update_frequency*60;
            break;
        case 'Hours':
            $time_length = $update_frequency*60*60;
            break;
        case 'Days':
            $time_length = $update_frequency*60*60*24;
            break;
        default:
            break;
    }

    return $time_length;
}
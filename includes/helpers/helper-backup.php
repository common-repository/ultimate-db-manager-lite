<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Returns the absolute path to the root of the website.
 *
 * @return string
 */
function get_absolute_root_file_path() {

    $absolute_path = rtrim( ABSPATH, '\\/' );
    $site_url      = rtrim( site_url( '', 'http' ), '\\/' );
    $home_url      = rtrim( home_url( '', 'http' ), '\\/' );

    if ( $site_url != $home_url ) {
        $difference = str_replace( $home_url, '', $site_url );
        if ( strpos( $absolute_path, $difference ) !== false ) {
            $absolute_path = rtrim( substr( $absolute_path, 0, - strlen( $difference ) ), '\\/' );
        }
    }

    return $absolute_path;
}

/**
 * Add backquotes to tables and db-names in
 * SQL queries. Taken from phpMyAdmin.
 *
 * @param $a_name
 *
 * @return array|string
 */
function backquote( $a_name ) {
    if ( ! empty( $a_name ) && $a_name != '*' ) {
        if ( is_array( $a_name ) ) {
            $result = array();
            reset( $a_name );
            foreach ( $a_name as $key => $val ) {
                $result[ $key ] = '`' . $val . '`';
            }

            return $result;
        } else {
            return '`' . $a_name . '`';
        }
    } else {
        return $a_name;
    }
}

function format_dump_name( $dump_name ) {
    $extension  = '.sql';
    $dump_name = sanitize_file_name( $dump_name );
    return $dump_name . $extension;
}

/**
 * Better addslashes for SQL queries.
 * Taken from phpMyAdmin.
 *
 * @param string $a_string
 * @param bool   $is_like
 *
 * @return mixed
 */
function sql_addslashes( $a_string = '', $is_like = false ) {
    if ( $is_like ) {
        $a_string = str_replace( '\\', '\\\\\\\\', $a_string );
    } else {
        $a_string = str_replace( '\\', '\\\\', $a_string );
    }

    return str_replace( '\'', '\\\'', $a_string );
}
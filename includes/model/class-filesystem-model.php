<?php
/**
 * Ultimate_DB_Manager_Filesystem_Model Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Filesystem_Model' ) ) :

    class Ultimate_DB_Manager_Filesystem_Model {

        /**
         * Ultimate_DB_Manager_Filesystem_Model constructor.
         *
         */
        public function __construct() {}

        /**
         * Returns folder information for backup files.
         *
         * @param string $type Either `path` or `url`.
         *
         * @return string The Path or the URL to the folder being used.
         */
        public function get_upload_info( $type = 'path' ) {
            $upload_info = apply_filters( 'ultimate_db_upload_info', array() );

            // No need to create the directory again if it already exist.
            if ( ! empty( $upload_info ) ) {
                return $upload_info[ $type ];
            }

            $upload_dir = wp_upload_dir();

            $upload_info['path'] = $upload_dir['basedir'];
            $upload_info['url']  = $upload_dir['baseurl'];

            $upload_dir_name = apply_filters( 'ultimate_db_upload_dir_name', 'ultimate-db-backup' );

            if ( ! file_exists( $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $upload_dir_name ) ) {
                // Create the directory.
                if ( false === @mkdir( $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $upload_dir_name, 0755 ) ) {
                    return $upload_info[ $type ];
                }

                // Protect from directory listings by making sure an index file exists.
                $filename = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $upload_dir_name . DIRECTORY_SEPARATOR . 'index.php';
                if ( false === @file_put_contents( $filename, "<?php\r\n// Silence is golden\r\n?>" ) ) {
                    return $upload_info[ $type ];
                }
            }

            // Protect from directory listings by ensuring this folder does not allow Indexes if using Apache.
            $htaccess = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $upload_dir_name . DIRECTORY_SEPARATOR . '.htaccess';
            if ( ! file_exists( $htaccess ) ) {
                if ( false === @file_put_contents( $htaccess, "Options -Indexes\r\nDeny from all" ) ) {
                    return $upload_info[ $type ];
                }
            }

            $upload_info['path'] .= DIRECTORY_SEPARATOR . $upload_dir_name;
            $upload_info['url']  .= '/' . $upload_dir_name;

            return $upload_info[ $type ];
        }

        /**
         * Converts file paths that include mixed slashes to use the correct type of slash for the current operating system.
         *
         * @param $path string
         *
         * @return string
         */
        public function slash_one_direction( $path ) {
            return str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
        }

        /**
         * Check if the specified path writable
         *
         * @param string $abs_path
         *
         * @return bool
         */
        public function is_writable( $abs_path ) {
            $return = is_writable( $abs_path );
            return $return;
        }

        /**
         * Open file
         */
        public function open( $filename = '', $mode = 'a', $gzip = false ) {
            if ( '' == $filename ) {
                return false;
            }
            $fp = fopen( $filename, $mode );
            return $fp;
        }

        /**
         * Close file
         */
        function close( $fp ) {
            fclose( $fp );
        }


        /**
         * Delete file
         */
        public function delete($file = ''){
            return unlink($file);
        }

    }

endif;
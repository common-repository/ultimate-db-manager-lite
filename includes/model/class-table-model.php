<?php
/**
 * Ultimate_DB_Manager_Table_Model Class
 *
 * @since  1.0.0
 * @package Ultimate WP DB Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Ultimate_DB_Manager_Table_Model' ) ) :

    class Ultimate_DB_Manager_Table_Model {

        /**
         * @var Ultimate_DB_Manager_Filesystem_Model
         */
        public $filesystem;

        /**
         * @var
         */
        public $current_chunk;

        /**
         * @var
         */
        public $primary_keys;

        /**
         * @var mixed|void
         */
        public $rows_per_segment;

        /**
         * @var
         */
        public $query_buffer;

        /**
         * @var
         */
        private $query_size;

        /**
         * @var
         */
        public $row_tracker;

        /**
         * @var Ultimate_DB_Manager_Log
         */
        private $error_log;

        /**
         * Ultimate_DB_Manager_Table_Model constructor.
         *
         */
        public function __construct($filesystem) {
            $this->filesystem = $filesystem;
            $this->rows_per_segment = apply_filters( 'ultimate_db_rows_per_segment', 100 );
        }

        /**
         * Get only the tables beginning with our DB prefix or temporary prefix.
         * @return array
         */
        public function get_tables() {
            global $wpdb;
            $tables = $wpdb->get_results( 'SHOW FULL TABLES', ARRAY_N );

            foreach ( $tables as $table ) {
                $clean_tables[] = $table[0];
            }

            return apply_filters( 'ultimate_db_tables', $clean_tables );
        }

        /**
         * Returns an array of table names with associated size in kilobytes.
         *
         * @return mixed
         *
         */
        public function get_table_sizes() {
            global $wpdb;

            $return = array();

            $sql = $wpdb->prepare(
                "SELECT TABLE_NAME AS 'table',
			ROUND( ( data_length + index_length ) / 1024, 0 ) AS 'size'
			FROM INFORMATION_SCHEMA.TABLES
			WHERE table_schema = %s
			AND table_type = %s
			ORDER BY TABLE_NAME",
                DB_NAME,
                'BASE TABLE'
            );

            $results = $wpdb->get_results( $sql, ARRAY_A );

            if ( ! empty( $results ) ) {
                foreach ( $results as $result ) {
                    $return[ $result['table'] ] = $result['size'];
                }
            }

            return apply_filters( 'ultimate_db_sizes', $return);
        }

        public function get_sql_dump_info( $migration_type, $info_type ) {
            $session_salt = strtolower( wp_generate_password( 5, false, false ) );

            $datetime  = date( 'YmdHis' );
            $ds        = ( $info_type == 'path' ? DIRECTORY_SEPARATOR : '/' );
            $dump_info = sprintf( '%s%s%s-%s-%s-%s.sql', $this->filesystem->get_upload_info( $info_type ), $ds, sanitize_title_with_dashes( DB_NAME ), $migration_type, $datetime, $session_salt );

            //return $dump_info;

            return ( $info_type == 'path' ? $this->filesystem->slash_one_direction( $dump_info ) : $dump_info );
        }

        /**
         * Database Backup Header
         *
         * @since 1.0.0
         */
        public function db_backup_header( $fp ) {
            global $wpdb;

            $charset = ( defined( 'DB_CHARSET' ) ? DB_CHARSET : 'utf8' );
            $this->output( '# ' . esc_html__( 'WordPress MySQL database migration', 'ultimate-db-manager' ) . "\n", $fp );
            $this->output( "#\n", $fp );
            $this->output( '# ' . sprintf( esc_html__( 'Generated: %s', 'ultimate-db-manager' ), date( 'l j. F Y H:i T' ) ) . "\n", $fp );
            $this->output( '# ' . sprintf( esc_html__( 'Hostname: %s', 'ultimate-db-manager' ), DB_HOST ) . "\n", $fp );
            $this->output( '# ' . sprintf( esc_html__( 'Database: %s', 'ultimate-db-manager' ), backquote( DB_NAME ) ) . "\n", $fp );

            $home_url = apply_filters( 'ultimate_db_backup_header_url', home_url() );
            $url      = preg_replace( '(^https?:)', '', $home_url, 1 );
            $this->output( '# URL: ' . esc_html( addslashes( $url ) ) . "\n", $fp );

            $path = get_absolute_root_file_path();
            $this->output( '# Path: ' . esc_html( addslashes( $path ) ) . "\n", $fp );

            $included_tables = $this->get_tables();
            $included_tables = apply_filters( 'ultimate_db_backup_header_included_tables', $included_tables );

            $this->output( '# Tables: ' . implode( ', ', $included_tables ) . "\n", $fp );
            $this->output( '# Table Prefix: ' . $wpdb->base_prefix . "\n", $fp );
            $this->output( '# Post Types: ' . implode( ', ', $this->get_post_types() ) . "\n", $fp );

            $protocol = 'http';
            if ( 'https' === substr( $home_url, 0, 5 ) ) {
                $protocol = 'https';
            }

            $this->output( '# Protocol: ' . $protocol . "\n", $fp );

            $is_multisite = is_multisite() ? 'true' : 'false';
            $this->output( '# Multisite: ' . $is_multisite . "\n", $fp );

            $this->output( "# --------------------------------------------------------\n\n", $fp );
            $this->output( "/*!40101 SET NAMES $charset */;\n\n", $fp );
            $this->output( "SET sql_mode='NO_AUTO_VALUE_ON_ZERO';\n\n", $fp );
            

        }

        /**
         * Creates the header for a table in a SQL file.
         *
         * @param string $table
         * @param string $target_table_name
         * @param string $temp_table_name
         *
         * @return null|bool
         */
        public function build_table_header( $table, $fp ) {
            global $wpdb;

            // Don't output data until after `wpmdb_create_table_query` filter is applied as mysql_compat_filter() can return an error
            $output          = '';

            // Add SQL statement to drop existing table
            $output .= ( "\n\n" );
            $output .= ( "#\n" );
            $output .= ( '# ' . sprintf( esc_html__( 'Delete any existing table %s', 'ultimate-db-manager' ), backquote( $table ) ) . "\n" );
            $output .= ( "#\n" );
            $output .= ( "\n" );
            $output .= ( 'DROP TABLE IF EXISTS ' . backquote( $table ) . ";\n" );

            // Table structure
            // Comment in SQL-file
            $output .= ( "\n\n" );
            $output .= ( "#\n" );
            $output .= ( '# ' . sprintf( esc_html__( 'Table structure of table %s', 'ultimate-db-manager' ), backquote( $table ) ) . "\n" );
            $output .= ( "#\n" );
            $output .= ( "\n" );


            $create_table = $wpdb->get_results( 'SHOW CREATE TABLE ' . backquote( $table ), ARRAY_N );

            if ( false === $create_table ) {
                return false;
            }
            $create_table[0][1] = str_replace( 'TYPE=', 'ENGINE=', $create_table[0][1] );
            $output               .= ( $create_table[0][1] . ";\n" );

            $this->output( $output, $fp );

            // Comment in SQL-file
            $this->output( "\n\n", $fp );
            $this->output( "#\n", $fp );
            $this->output( '# ' . sprintf( esc_html__( 'Data contents of table %s', 'ultimate-db-manager' ), backquote( $table ) ) . "\n", $fp );
            $this->output( "#\n", $fp );

        }

        /**
         * Write query line to chunk, file pointer, or buffer depending on action type.
         *
         * @param string $query_line
         *
         * @return bool
         */
        public function output( $query_line, $fp = null ) {

            $this->current_chunk .= $query_line;

            if ( 0 === strlen( $query_line ) ) {
                return true;
            }

            if ( false === @fwrite( $fp, $query_line ) ) {
                //$this->error_log->setError( esc_html__( 'Failed to write the SQL data to the file. (#128)', 'ultimate-db-manager' ) );
                return false;
            }

        }

        /**
         * Return array of post type slugs stored in Database
         *
         * @return array List of post types
         */
        function get_post_types() {
            global $wpdb;

            if ( is_multisite() ) {
                $tables         = $this->get_tables();
                $sql            = "SELECT DISTINCT `post_type` FROM `{$wpdb->base_prefix}posts` ;";
                $post_types     = $wpdb->get_results( $sql, ARRAY_A );
                $prefix_escaped = preg_quote( $wpdb->base_prefix, '/' );

                foreach ( $tables as $table ) {
                    if ( 0 == preg_match( '/' . $prefix_escaped . '[0-9]+_posts/', $table ) ) {
                        continue;
                    }
                    $blog_id         = str_replace( array( $wpdb->base_prefix, '_posts' ), array( '', '' ), $table );
                    $sql             = "SELECT DISTINCT `post_type` FROM `{$wpdb->base_prefix}" . $blog_id . '_posts` ;';
                    $site_post_types = $wpdb->get_results( $sql, ARRAY_A );
                    if ( is_array( $site_post_types ) ) {
                        $post_types = array_merge( $post_types, $site_post_types );
                    }
                }
            } else {
                $post_types = $wpdb->get_results(
                    "SELECT DISTINCT `post_type`
				FROM `{$wpdb->base_prefix}posts`
				WHERE 1;",
                    ARRAY_A
                );
            }

            $return = array( 'revision' );

            foreach ( $post_types as $post_type ) {
                $return[] = $post_type['post_type'];
            }

            return apply_filters( 'ultimate_db_post_types', array_unique( $return ) );
        }

        /**
         * Start schedule table backup now
         *
         */
        public function schedule_table_backup($table, $filename) {

            $sql_dump_file_name = $this->filesystem->get_upload_info( 'path' ) . DIRECTORY_SEPARATOR . $filename;
            $fp                 = $this->filesystem->open( $sql_dump_file_name );

            $result = $this->process_table( $table, $fp );     

        }

        /**
         * Start ajax table backup now
         *
         */
        public function ajax_table_backup() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_ajax_nonce'], 'ultimate-db-manager') ) {
                wp_send_json_error( esc_html__( 'You are not allowed to perform this action', 'ultimate-db-manager' ) );
            }

            if ( isset( $_POST['fields_data'] ) ) {

                $fields  = $_POST['fields_data'];

                $filename = sanitize_text_field($fields['filename']);
                $table = sanitize_text_field($fields['table']);

                $sql_dump_file_name = $this->filesystem->get_upload_info( 'path' ) . DIRECTORY_SEPARATOR . $filename;
                $fp                 = $this->filesystem->open( $sql_dump_file_name );

                $result = $this->process_table( $table, $fp );

                $return = '<p class="per-table-item-info">' . $table . ' processed </p>';

                wp_send_json_success( $return );

            } else {

                wp_send_json_error( esc_html__( 'User submit data are empty!', 'ultimate-db-manager' ) );
            }

        }

        /**
         * Loops data in the provided table to perform a backup
         *
         * @param string $table
         *
         * @return mixed
         */
        public function process_table( $table, $fp = null ) {
            global $wpdb;

            $this->build_table_header($table, $fp);

            $structure_info    = $this->get_structure_info( $table );
            $row_start         = 0;

            // Build and run the query
            $select_sql = $this->build_select_query( $table, $row_start, $structure_info );
            $table_data = $wpdb->get_results( $select_sql );

            // Loop over the results
            foreach ( $table_data as $row ) {
                $result = $this->process_row( $table, $row, $structure_info);
                $this->output( $result, $fp );
            }
        }

        /**
         * Initializes the query buffer and template.
         *
         * @param $target_table_name
         * @param $temp_table_name
         * @param $structure_info
         *
         * @return null
         */
        function start_query_buffer( $table, $structure_info ) {
           $fields          = implode( ', ', $structure_info['field_set'] );
           $query_buffer = 'INSERT INTO ' . backquote( $table ) . ' ( ' . $fields . ") VALUES\n";
           return $query_buffer;
        }

        /**
         * Processes the data in a given row.
         *
         * @param array  $row
         * @param array  $structure_info
         *
         * @return array|void
         */
        public function process_row( $table, $row, $structure_info ) {

            $fields = implode( ', ', $structure_info['field_set'] );
            $query = 'INSERT INTO ' . backquote( $table ) . ' ( ' . $fields . ") VALUES ";

            foreach ( $row as $key => $value ) {
                    if ( isset( $structure_info['ints'][ strtolower( $key ) ] ) && $structure_info['ints'][ strtolower( $key ) ] ) {
                        // make sure there are no blank spots in the insert syntax,
                        // yet try to avoid quotation marks around integers
                        $value    = ( null === $value || '' === $value ) ? $structure_info['defs'][ strtolower( $key ) ] : $value;
                        $values[] = ( '' === $value ) ? "''" : $value;
                        continue;
                    }

                   $test_bit_key = strtolower( $key ) . '__bit';
                   // Correct null values IF we're not working with a BIT type field, they're handled separately below
                   if ( null === $value && ! property_exists( $row, $test_bit_key  ) ) {
                       $values[] = 'NULL';
                       continue;
                   }

                   // If we have binary data, substitute in hex encoded version and remove hex encoded version from row.
                   $hex_key = strtolower( $key ) . '__hex';
                   if ( isset( $structure_info['bins'][ strtolower( $key ) ] ) && $structure_info['bins'][ strtolower( $key ) ] && isset( $row->$hex_key ) ) {
                        $value    = "UNHEX('" . $row->$hex_key . "')";
                        $values[] = $value;
                        unset( $row->$hex_key );
                        continue;
                   }

                   // If we have bit data, substitute in properly bit encoded version.
                   $bit_key = strtolower( $key ) . '__bit';
                   if ( isset( $structure_info['bits'][ strtolower( $key ) ] ) && $structure_info['bits'][ strtolower( $key ) ] && ( isset( $row->$bit_key ) || null === $row->$bit_key ) ) {
                        $value    = null === $row->$bit_key ? 'NULL' : "b'" . $row->$bit_key . "'";
                        $values[] = $value;
                        unset( $row->$bit_key );
                        continue;
                   }

                   // \x08\\x09, not required
                   $multibyte_search  = array( "\x00", "\x0a", "\x0d", "\x1a" );
                   $multibyte_replace = array( '\0', '\n', '\r', '\Z' );

                   $value = sql_addslashes( $value );
                   $value = str_replace( $multibyte_search, $multibyte_replace, $value );

                   $values[] = "'" . $value . "'";
            }
            $query .= '(' . implode( ', ', $values ) . ');' . "\n";

            return $query;
        }

        /**
         * Builds the SELECT query to get data to backup.
         *
         * @param string $table
         * @param int    $row_start
         * @param array  $structure_info
         *
         * @return string
         */
        function build_select_query( $table, $row_start, $structure_info ) {

            global $wpdb;

            $join     = array();
            $where    = 'WHERE 1=1';
            $order_by = '';

            $limit = "LIMIT {$row_start}, {$this->rows_per_segment}";

            $sel = backquote( $table ) . '.*';
            if ( ! empty( $structure_info['bins'] ) ) {
                foreach ( $structure_info['bins'] as $key => $bin ) {
                    $hex_key = strtolower( $key ) . '__hex';
                    $sel     .= ', HEX(' . backquote( $key ) . ') as ' . backquote( $hex_key );
                }
            }
            if ( ! empty( $structure_info['bits'] ) ) {
                foreach ( $structure_info['bits'] as $key => $bit ) {
                    $bit_key = strtolower( $key ) . '__bit';
                    $sel     .= ', ' . backquote( $key ) . '+0 as ' . backquote( $bit_key );
                }
            }
            $join     = implode( ' ', array_unique( $join ) );
            $join     = apply_filters( 'ultimate_db_rows_join', $join, $table );
            $where    = apply_filters( 'ultimate_db_rows_where', $where, $table );
            $order_by = apply_filters( 'ultimate_db_rows_order_by', $order_by, $table );
            $limit    = apply_filters( 'ultimate_db_rows_limit', $limit, $table );

            $sql = 'SELECT ' . $sel . ' FROM ' . backquote( $table ) . " $join $where $order_by $limit";
            $sql = apply_filters( 'ultimate_db_rows_sql', $sql, $table );

            return $sql;
        }

        /**
         * Parses the provided table structure.
         *
         * @param array $table_structure
         *
         * @return array
         */
        public function get_structure_info( $table, $table_structure = array() ) {
            if ( empty( $table_structure ) ) {
                $table_structure = $this->get_table_structure( $table );
            }

            if ( ! is_array( $table_structure ) ) {
                return false;
            }

            // $defs = mysql defaults, looks up the default for that particular column, used later on to prevent empty inserts values for that column
            // $ints = holds a list of the possible integer types so as to not wrap them in quotation marks later in the insert statements
            $defs               = array();
            $ints               = array();
            $bins               = array();
            $bits               = array();
            $field_set          = array();
            $this->primary_keys = array();
            $use_primary_keys   = true;

            foreach ( $table_structure as $struct ) {
                if ( ( 0 === strpos( $struct->Type, 'tinyint' ) ) ||
                    ( 0 === strpos( strtolower( $struct->Type ), 'smallint' ) ) ||
                    ( 0 === strpos( strtolower( $struct->Type ), 'mediumint' ) ) ||
                    ( 0 === strpos( strtolower( $struct->Type ), 'int' ) ) ||
                    ( 0 === strpos( strtolower( $struct->Type ), 'bigint' ) )
                ) {
                    $defs[ strtolower( $struct->Field ) ] = ( null === $struct->Default ) ? 'NULL' : $struct->Default;
                    $ints[ strtolower( $struct->Field ) ] = '1';
                } elseif ( 0 === strpos( $struct->Type, 'binary' ) || apply_filters( 'ultimate_db_process_column_as_binary', false, $struct ) ) {
                    $bins[ strtolower( $struct->Field ) ] = '1';
                } elseif ( 0 === strpos( $struct->Type, 'bit' ) || apply_filters( 'ultimate_db_process_column_as_bit', false, $struct ) ) {
                    $bits[ strtolower( $struct->Field ) ] = '1';
                }

                $field_set[] = backquote( $struct->Field );

                if ( 'PRI' === $struct->Key && true === $use_primary_keys ) {
                    if ( false === strpos( $struct->Type, 'int' ) ) {
                        $use_primary_keys   = false;
                        $this->primary_keys = array();
                        continue;
                    }
                    $this->primary_keys[ $struct->Field ] = 0;
                }
            }

            $return = array(
                'defs'      => $defs,
                'ints'      => $ints,
                'bins'      => $bins,
                'bits'      => $bits,
                'field_set' => $field_set,
            );

            return $return;
        }

        /**
         * Returns the table structure for the provided table.
         *
         * @param string $table
         *
         * @return array|bool
         */
        public function get_table_structure( $table ) {
            global $wpdb;

            $table_structure = false;

            if ( $this->table_exists( $table ) ) {
                $table_structure = $wpdb->get_results( 'DESCRIBE ' . backquote( $table ) );
            }

            if ( ! $table_structure ) {
                return false;
            }

            return $table_structure;
        }

        /**
         * Checks if a given table exists.
         *
         * @param $table
         *
         * @return bool
         */
        public function table_exists( $table ) {
            global $wpdb;

            $table = esc_sql( $table );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
                return true;
            }

            return false;
        }



    }

endif;
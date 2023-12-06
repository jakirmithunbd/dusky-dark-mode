<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Dusky_Install' ) ) {
    class Dusky_Install {

        public static function activate() {
            self::create_default_data();
            self::create_tables();
        }

        private static function create_tables() {
            global $wpdb;

            $wpdb->hide_errors();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $tables = [

                // Analytics table
                "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dusky_analytics(
                    id bigint(20) NOT NULL AUTO_INCREMENT,
                    unique_id VARCHAR(32) NOT NULL UNIQUE,
                    activation bigint(20) NOT NULL DEFAULT 0,
                    deactivation bigint(20) NOT NULL DEFAULT 0,
                    view bigint(20) NOT NULL DEFAULT 0,
                    dark_view bigint(20) NOT NULL DEFAULT 0,
                    visitor bigint(20) NOT NULL DEFAULT 0,
                    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    UNIQUE KEY (unique_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            ];

            foreach ( $tables as $table ) {
                dbDelta( $table );
            }
        }

        private static function create_default_data() {

            $version = get_option( 'dusky_version', '0' );
            $dusky_installed = get_option( 'dusky_installed' );

            if ( ! $dusky_installed ) {
                update_option( 'dusky_installed', time() );
            }

            if ( version_compare( $version, DUSKY_VERSION, '<' ) ) {
                update_option( 'dusky_version', DUSKY_VERSION );
            }

        }

    }

}

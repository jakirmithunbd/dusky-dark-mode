<?php

if ( ! class_exists( 'Dusky_Ajax' ) ) {
    class Dusky_Ajax {

        public function __construct() {
            add_filter( 'upload_mimes', [$this, 'allow_json_upload'] );

            add_action( 'wp_ajax_save_dusky_settings', [$this, 'save_dusky_settings'] );

            add_action( 'wp_ajax_get_dusky_data', [$this, 'get_dusky_data'] );

            add_action( 'wp_ajax_import_dusky_settings', [$this, 'import_dusky_settings'] );
        }

        public function allow_json_upload( $mimes ) {
            $mimes['json'] = 'application/json';
            return $mimes;
        }

        public function import_dusky_settings() {
            $nonce = isset( $_POST['nonce'] ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : '';

            if ( ! wp_verify_nonce( $nonce, 'dusky_nonce' ) || ! current_user_can( 'manage_options' ) ) {
                wp_die( "Oops! It seems like you don't have the necessary permissions to perform this action. If you believe this is an error, please contact the site administrator." );
                return false;
            }

            function dusky_sanitize_array( $json_data ) {
                $validated_json_data = [];

                if ( is_array( $json_data ) ) {
                    foreach ( $json_data as $key => $value ) {
                        $sanitized_key = sanitize_text_field( $key );

                        if ( is_string( $value ) ) {
                            $sanitized_value = sanitize_text_field( $value );
                        } else if ( is_array( $value ) ) {
                            // Recursively sanitize nested arrays
                            $sanitized_value = dusky_sanitize_array( $value );
                        } else if ( is_bool( $value ) ) {
                            // Use filter_var for boolean validation
                            $sanitized_value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
                        } else if ( is_numeric( $value ) ) {
                            // Use filter_var for integer validation
                            $sanitized_value = filter_var( $value, FILTER_VALIDATE_INT );
                        } else {
                            // For other types, set the sanitized value to null
                            $sanitized_value = null;
                        }

                        // Add the sanitized key-value pair to the result array
                        $validated_json_data[$sanitized_key] = $sanitized_value;
                    }
                }

                return $validated_json_data;
            }

            $json_file_url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';

            if ( filter_var( $json_file_url, FILTER_VALIDATE_URL ) !== false ) {
                $json_data = file_get_contents( $json_file_url );
                if ( $json_data !== false ) {
                    $imported_data = json_decode( $json_data, true );

                    $imported_data = dusky_sanitize_array( $imported_data );

                    if ( $imported_data !== null ) {
                        // Update the 'dusky_settings' option with the imported data
                        update_option( 'dusky_settings', $imported_data );

                        $status = ['data' => $imported_data, 'message' => 'Data has been successfully imported'];

                    } else {
                        $status = ['data' => '', 'message' => 'Error: The selected file is not valid JSON data.'];
                    }
                }

                wp_send_json_success( $status );
            } else {
                // Handle invalid URL
                wp_send_json_error( "Invalid URL provided." );
                wp_die( "Invalid URL provided." );
            }
        }

        public function get_dusky_data() {
            $dusky_nonce = isset( $_POST['nonce'] ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : '';

            if ( ! wp_verify_nonce( $dusky_nonce, 'dusky_nonce' ) || ! current_user_can( 'manage_options' ) ) {
                $message = ['message' => __( 'Unauthorize Request', 'dusky-dark-mode' )];
                return wp_send_json_error( $message, 401 );
            }

            $data = isset( $_POST['data'] ) ? sanitize_key( wp_unslash( $_POST['data'] ) ) : '';

            if ( $data === 'roles' ) {
                $data = dusky_get_user_roles();
            } else {
                $data = ['message' =>
                    "No Data Found"];
            }

            wp_send_json_success( $data );
        }

        private function validate_and_sanitize( $data, $sanitization_callback ) {

            $sanitized_data = [];
            if ( $sanitization_callback === 'sanitize_text_field' ) {

                $sanitized_data = $this->sanitizeTextAndArray( $data, $sanitized_data );

            } else if ( $sanitization_callback === 'checkbox_sanitization_callback' ) {

                $sanitized_data = $this->sanitize_boolean( $data, $sanitized_data );

            }
            return $sanitized_data;
        }

        private function sanitizeTextAndArray( $data, $key ) {
            if ( is_array( $data ) ) {
                foreach ( $data as $_key => $value ) {
                    if ( is_array( $value ) ) {
                        $key[$_key] = $this->sanitizeTextAndArray( $value, isset( $key[$_key] ) && is_array( $key[$_key] ) ? $key[$_key] : [] );
                    } else {
                        if ( 'fromReportingEmail' === $_key ) {
                            $key[$_key] = $value;
                        } else {
                            $key[$_key] = call_user_func( 'sanitize_text_field', $value );
                        }
                    }
                }
            }

            return $key;
        }

        public function sanitize_boolean( $data, $key ) {
            foreach ( $data as $_key => $value ) {
                $key[$_key] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
            }
            return $key;
        }

        public function save_dusky_settings() {
            $dusky_nonce = isset( $_POST['dusky_nonce'] ) ? sanitize_key( wp_unslash( $_POST['dusky_nonce'] ) ) : '';

            if ( ! wp_verify_nonce( $dusky_nonce, 'dusky_nonce' ) || ! current_user_can( 'manage_options' ) ) {
                $message = ['message' => __( 'Unauthorize Request', 'dusky-dark-mode' )];
                return wp_send_json_error( $message, 401 );
            }

            $dusky_settings = $_POST['data'];
            $input = isset( $dusky_settings['input'] ) ? $dusky_settings['input'] : null;
            $checkbox = isset( $dusky_settings['checkbox'] ) ? $dusky_settings['checkbox'] : null;
            $adminDarkMode = isset( $dusky_settings['adminDarkMode'] ) ? $dusky_settings['adminDarkMode'] : null;
            $_dusky_settings = null;
            $_dusky_admin_settings = null;

            // Validation and sanitization functions
            $input_sanitization_callback = 'sanitize_text_field';
            $checkbox_sanitization_callback = 'checkbox_sanitization_callback';

            function validateArrayIndex( $array, $key, $default = '' ) {
                return isset( $array[$key] ) ? $array[$key] : $default;
            }

            if ( is_array( $adminDarkMode ) ) {

                $admin_input = [
                    "adminMode"            => validateArrayIndex( $adminDarkMode, "adminMode" ),
                    "customToggleSize"     => validateArrayIndex( $adminDarkMode, "customToggleSize" ),
                    "toggleBottomOffset"   => validateArrayIndex( $adminDarkMode, "toggleBottomOffset" ),
                    "toggleButton"         => validateArrayIndex( $adminDarkMode, "toggleButton" ),
                    "toggleCustomPosition" => validateArrayIndex( $adminDarkMode, "toggleCustomPosition" ),
                    "togglePosition"       => validateArrayIndex( $adminDarkMode, "togglePosition" ),
                    "toggleSideOffset"     => validateArrayIndex( $adminDarkMode, "toggleSideOffset" ),
                    "toggleStyle"          => validateArrayIndex( $adminDarkMode, "toggleStyle" ),
                    "toggleSize"           => validateArrayIndex( $adminDarkMode, "toggleSize" ),
                ];
                $admin_checkbox = [
                    "adminDark"             => validateArrayIndex( $adminDarkMode, "adminDark", true ),
                    "blockEditorDarkMode"   => validateArrayIndex( $adminDarkMode, "blockEditorDarkMode", false ),
                    "classicEditorDarkMode" => validateArrayIndex( $adminDarkMode, "classicEditorDarkMode", false ),
                ];

                $validate_input = (array) $this->validate_and_sanitize( $admin_input, $input_sanitization_callback );
                $validate_checkbox = (array) $this->validate_and_sanitize( $admin_checkbox, $checkbox_sanitization_callback );

                $_dusky_admin_settings = [
                    'adminDarkMode' => array_merge( $validate_input, $validate_checkbox ),

                ];

                update_user_meta( get_current_user_id(), 'dusky_admin_settings', $_dusky_admin_settings );
            }

            if ( isset( $dusky_settings['input'] ) && isset( $dusky_settings['checkbox'] ) ) {

                // Perform validation and sanitization
                $_dusky_settings = [
                    'input'    => $this->validate_and_sanitize( $input, $input_sanitization_callback ),
                    'checkbox' => $this->validate_and_sanitize( $checkbox, $checkbox_sanitization_callback ),
                ];
                update_option( 'dusky_settings', $_dusky_settings );
            }

            wp_send_json_success( [$_dusky_settings, $_dusky_admin_settings] );
        }

        private static $instance = null;

        public static function instance() {

            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

Dusky_Ajax::instance();
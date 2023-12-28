<?php

/**
 * WP Dusk Dark Mode
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

if ( ! function_exists( 'dusky_get_settings' ) ) {

    /**
     * Get setting database option
     *
     * @param string $key Option key name.
     * @param mixed  $default Default value to return.
     * @return mixed
     */
    function dusky_get_settings( $key = '', $default = '' ) {
        $settings_name = 'dusky_settings';

        $settings = get_option( $settings_name, [] );

        if ( $key === 'state' ) {
            return $settings;
        }

        if ( ! isset( $settings["input"] ) && ! isset( $settings["checkbox"] ) ) {
            return $default;
        }

        if ( ! is_array( $settings["checkbox"] ) && ! is_array( $settings["input"] ) ) {
            return $default;
        }

        $merge_settings = array_merge( $settings["input"], $settings["checkbox"] );

        switch ( $merge_settings['toggleStyle'] ) {
        case '1':
            $merge_settings['toggleStyle'] = '1';
            break;
        case '2':
            $merge_settings['toggleStyle'] = '2';
            break;
        case '3':
            $merge_settings['toggleStyle'] = '3';
            break;
        default:
            $merge_settings['toggleStyle'] = '1';

            $merge_settings['toggleSize'] = 'medium';
            $merge_settings['togglePosition'] = 'right';
            $merge_settings['ColorSepia'] = '10';
            $merge_settings['ColorGrayscale'] = '0';
            $merge_settings['ColorContrast'] = '90';
            $merge_settings['ColorBrightness'] = '100';
            $merge_settings['darkModeUserRoles'] = ['administrator'];
            $merge_settings['imgLowBrightnessInput'] = '90';
            $merge_settings['customBGColor'] = '#1B2430';
            $merge_settings['customTextColor'] = '#162447';
            $merge_settings['isDarkModeUserRoles'] = false;
            $merge_settings['urlParameter'] = false;
            $merge_settings['imgLowBrightness'] = false;
            $merge_settings['imgDarkenBG'] = false;
        }

        if ( ! $key ) {
            return $merge_settings;
        }

        return isset( $merge_settings[$key] ) ? $merge_settings[$key] : $default;
    }
}

if ( ! function_exists( 'dusky_get_admin_settings' ) ) {
    function dusky_get_admin_settings( $user_id = 0, $key = '', $default = '' ) {
        $settings_name = 'dusky_admin_settings';
        $settings = get_user_meta( $user_id, $settings_name, true );

        if ( isset( $settings['adminDarkMode'] ) ) {
            $settings = $settings['adminDarkMode'];
        } else {
            return;
        }

        if ( ! $user_id ) {
            return;
        }

        switch ( $settings['toggleStyle'] ) {
        case '1':
            $settings['toggleStyle'] = '1';
            break;
        case '2':
            $settings['toggleStyle'] = '2';
            break;
        case '3':
            $settings['toggleStyle'] = '3';
            break;
        default:
            $settings['toggleStyle'] = '1';
        }

        if ( empty( $key ) ) {
            return $settings;
        }

        if ( isset( $key ) && isset( $settings[$key] ) ) {
            return $settings[$key];
        }

        return $default;
    }
}

if ( ! function_exists( 'dusky_get_mode' ) ) {
    function dusky_get_mode() {

        if ( isset( $_GET['darkmode'] ) && ! empty( sanitize_text_field( $_GET['darkmode'] ) ) ) {
            return 'dark';
        } elseif ( isset( $_GET['lightmode'] ) && ! empty( sanitize_text_field( $_GET['lightmode'] ) ) ) {
            return 'light';
        } elseif ( isset( $_GET['automode'] ) && ! empty( sanitize_text_field( $_GET['automode'] ) ) ) {
            return 'auto';
        }

        $mode = 'light';

        if ( is_admin() ) {
            // TODO

        } else {
            $is_default_mode = dusky_get_settings( 'defaultDarkMode', false );
            if ( $is_default_mode ) {
                $mode = 'dark';
            }
            $is_auto = dusky_get_settings( 'autoOsMatch', true );
            if ( $is_auto ) {
                $mode = 'auto';
            }
            if ( ! empty( $_SESSION['dusky_mode'] ) ) {
                $mode = sanitize_key( $_SESSION['dusky_mode'] );
            }
        }

        if ( isset( $_COOKIE['duskyf-mode'] ) ) {
            $mode = sanitize_text_field( $_COOKIE['duskyf-mode'] );
        }

        return $mode;
    }
}

if ( ! function_exists( 'dusky_get_user_roles' ) ) {
    function dusky_get_user_roles() {
        $roles = wp_roles()->roles;
        $user_roles = [];

        if ( isset( $roles ) && is_array( $roles ) ) {
            foreach ( $roles as $key => $value ) {
                array_push( $user_roles, [
                    'label' => $value['name'],
                    'value' => $key,
                ] );
            }
        }

        return $user_roles;
    }
}

if ( ! function_exists( 'dusky_get_active_role' ) ) {
    function dusky_get_active_role() {
        $active_roles = (array) dusky_get_settings( 'darkModeUserRoles' );

        $cuttent_role = array_filter( $active_roles, function ( $item ) {
            return current_user_can( $item );
        } );

        if ( ! empty( $cuttent_role ) && is_array( $cuttent_role ) && array_values( $cuttent_role )[0] ) {
            return array_values( $cuttent_role )[0];
        }

        return false;
    }
}

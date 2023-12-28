<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'Dusky_Enqueue' ) ) {
    /**
     * The enqueue class;
     */
    class Dusky_Enqueue {

        /**
         * The class instance variable
         * @var null
         * @since 1.0.0
         * @static
         */
        protected static $instance = null;

        /**
         * The construct function
         * @return void
         * @since 1.0.0
         */
        public function __construct() {
            add_action( 'wp_enqueue_scripts', [$this, 'frontend_scripts'] );
            add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts'] );
        }

        /**
         * Enqueue frontend scripts
         * @return void
         * @since 1.0.0
         */
        public function frontend_scripts(): void {

            wp_register_style(
                'dusky-frontend',
                DUSKY_ASSETS . '/css/frontend-custom.css',
                [],
                DUSKY_VERSION
            );
            $custom_css = $this->get_custom_css();
            wp_add_inline_style( 'dusky-frontend', $custom_css );

            wp_register_script(
                'dusky-darkmode',
                DUSKY_ASSETS . '/js/dark-mode.js',
                ['jquery'],
                DUSKY_VERSION,
                true
            );

            wp_register_script(
                'dusky-frontend',
                DUSKY_ASSETS . '/js/frontend-custom.js',
                ['jquery', 'dusky-darkmode'],
                DUSKY_VERSION,
                true
            );
            $excludes = [
                'isDarkModeUserRoles',
                'flotingToggleTab',
                'darkModeUserRoles',
                'reportingEmail',
                'reportingEmailSub',
                'reportingFrequency',
                'timeZone',
                'apiUrl',
                'autoSave',
                'chartStyle',
                'currentUserRole',
                'emailReporting',
                'emailSendTime',
                'enableAnalytics',
                'flotingToggleTab',
                'fromReportingEmail',
                'is_pro',
                'nonce',
            ];
            $includes = [
                'dusky_mode' => dusky_get_mode(),
            ];

            wp_localize_script( 'dusky-frontend', 'dusky_localize', $this->get_localize_data( $includes, $excludes ) );

            unset( $includes );
            unset( $excludes );

            wp_register_script(
                'dusky-frontend-config',
                DUSKY_URL . 'frontend/dusky-frontend-config.js',
                [
                    'dusky-darkmode',
                    'wp-components',
                    'wp-element',
                    'wp-editor',
                    'wp-util',
                ],
                DUSKY_VERSION,
                true
            );

            $isFrontDark = dusky_get_settings( 'frontDark', true );

            if ( current_user_can( 'manage_options' ) && $isFrontDark ) {
                wp_enqueue_style( 'dusky-frontend' );
                wp_enqueue_script( 'dusky-frontend' );
            } else if ( $isFrontDark ) {
                wp_enqueue_style( 'dusky-frontend' );
                wp_enqueue_script( 'dusky-frontend' );
            }
        }

        /**
         * Enqueue admin scripts
         * @return void
         * @since 1.0.0
         */
        public function admin_scripts( $hook ): void {

            wp_register_style(
                'admin-toggle-css',
                DUSKY_ASSETS . '/css/frontend-custom.css',
                [],
                DUSKY_VERSION,
                false
            );

            wp_register_script(
                'admin-dusky-darkmode',
                DUSKY_ASSETS . '/js/dark-mode.js',
                ['jquery'],
                DUSKY_VERSION,
                true
            );

            wp_register_style(
                'dusky-style',
                DUSKY_ASSETS . '/admin/css/style.css',
                [],
                DUSKY_VERSION,
                false
            );

            wp_register_style(
                'toggles-css',
                DUSKY_ASSETS . '/admin/css/toggles.css',
                [],
                DUSKY_VERSION,
                false
            );

            wp_register_script(
                'dusky-dark-mode-app',
                DUSKY_URL . '/assets/js/dusky-backend.js',
                [
                    'jquery',
                    'admin-dusky-darkmode',
                ],
                DUSKY_VERSION,
                true
            );

            wp_register_script(
                'dusky-admin-scripts',
                DUSKY_ASSETS . '/admin/js/dusky-admin-scripts.js',
                [
                    'jquery',
                    'dusky-chart',
                ],
                DUSKY_VERSION,
                true
            );

            wp_register_script(
                'dusky-chart',
                DUSKY_ASSETS . '/admin/js/chart.js',
                [],
                DUSKY_VERSION,
                true
            );

            $user_id = get_current_user_id();
            $admin_dark_settings = dusky_get_admin_settings( $user_id );
            $admin_pages = Dusky_Admin::instance()->get_admin_pages();
            $adminDark = dusky_get_admin_settings( $user_id, 'adminDark', true );
            $data = $this->get_localize_data();
            $data['admin_settings'] = $admin_dark_settings;
            wp_enqueue_script( 'dusky-admin-scripts' );

            if ( $adminDark || $hook === $admin_pages['dusky'] ) {
                wp_enqueue_style( 'admin-dusky-darkmode' );
                wp_enqueue_style( 'admin-toggle-css' );
                wp_enqueue_script( 'admin-dusky-darkmode' );
                wp_enqueue_script( 'dusky-dark-mode-app' );
                wp_localize_script( 'dusky-dark-mode-app', 'dusky_localize', $data );
                wp_enqueue_media();
            }

            if ( $hook === $admin_pages['dusky'] ) {
                wp_enqueue_style( 'dusky-style' );
                wp_enqueue_style( 'toggles-css' );
            }
        }

        /**
         * The custom css
         * @return string
         * @since 1.0.0
         */
        private function get_custom_css(): string {
            $custom_css = '';
            //Image Settings
            $invert_images = dusky_get_settings( 'imgInvert', false );
            $low_brightness = dusky_get_settings( 'imgLowBrightness', false );
            $gray_scale = dusky_get_settings( 'imgGrayscale', false );

            if ( $invert_images || $low_brightness || $gray_scale ) {
                $custom_css .= 'html[data-dusky-dark-mode="dark"] img:not(.dusky-toggle *, .dusky-ignore, .dusky-ignore * , .elementor-background-overlay, .elementor-element-overlay, .elementor-button-link, .elementor-button-link *, .elementor-widget-spacer, .elementor-widget-spacer *, .wp-block-button__link, .wp-block-button__link *){';
                $filter_css = '';

                if ( $invert_images ) {
                    $invert_images_level = dusky_get_settings( 'imgInvertInput', 80 ) / 100;
                    $filter_css .= sprintf( 'invert(%s) ', $invert_images_level );
                }

                if ( $low_brightness ) {
                    $low_brightness_level = dusky_get_settings( 'imgLowBrightnessInput', 80 ) / 100;
                    $filter_css .= sprintf( 'brightness(%s) ', $low_brightness_level );
                }

                if ( $gray_scale ) {
                    $gray_scale_level = dusky_get_settings( 'imgGrayscaleInput', 80 ) / 100;
                    $filter_css .= sprintf( 'grayscale(%s) ', $gray_scale_level );
                }

                $custom_css .= sprintf( 'filter: %s; }', $filter_css );
                $custom_css .= '}';
            }

            //Video Settings
            $video_low_brightness = dusky_get_settings( 'videoLowBrightness', false );
            $video_gray_scale = dusky_get_settings( 'videoGrayscale', false );

            if ( $video_low_brightness || $video_gray_scale ) {
                $custom_css .= 'html[data-dusky-dark-mode="dark"] video:not(.dusky-toggle *, .dusky-ignore, .dusky-ignore * ),';
                $custom_css .= 'html[data-dusky-dark-mode="dark"] iframe[src*="youtube.com"],';
                $custom_css .= 'html[data-dusky-dark-mode="dark"] iframe[src*="vimeo.com"],';
                $custom_css .= 'html[data-dusky-dark-mode="dark"] iframe[src*="dailymotion.com"]{';
                $filter_css = '';

                if ( $video_low_brightness ) {
                    $video_low_brightness_level = dusky_get_settings( 'videoLowBrightnessInput', 80 ) / 100;
                    $filter_css .= sprintf( 'brightness(%s) ', $video_low_brightness_level );
                }

                if ( $video_gray_scale ) {
                    $video_gray_scale_level = dusky_get_settings( 'videoGrayscaleInput', 80 ) / 100;
                    $filter_css .= sprintf( 'grayscale(%s) ', $video_gray_scale_level );
                }

                $custom_css .= sprintf( 'filter: %s; }', $filter_css );
                $custom_css .= '}';
            }

            return $custom_css;
        }

        /**
         * Set localize script
         * @return array
         * @since 1.0.0
         */
        private function get_localize_data( $includes = [], $excludes = [] ): array {

            if ( ! is_admin() ) {
                $settings = (array) dusky_get_settings();

                foreach ( $excludes as $value ) {
                    unset( $settings[$value] );
                }

                foreach ( $includes as $key => $value ) {
                    $settings[$key] = $value;
                }

                return $settings;
            } else {
                $data = [
                    'siteUrl'         => site_url(),
                    'siteTitle'       => get_bloginfo( 'name' ),
                    'adminUrl'        => admin_url(),
                    'email'           => get_option( 'admin_email' ),
                    'pluginUrl'       => DUSKY_URL,
                    'apiUrl'          => home_url( '/wp-json' ),
                    'nonce'           => wp_create_nonce( 'dusky_nonce' ),
                    'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
                    'currentUserRole' => wp_get_current_user()->roles[0],
                    'settings'        => dusky_get_settings( 'state' ),
                ];

                return $data;
            }

            return [];
        }

        /**
         *  The class singleton instance.
         * @return Enqueue|null
         * @since 1.0.0
         * @static
         */
        public static function instance(): ?Dusky_Enqueue {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }

}

Dusky_Enqueue::instance();

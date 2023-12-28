<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

if ( ! class_exists( 'Dusky_Hooks' ) ) {
    class Dusky_Hooks {

        private static $instance = null;

        public function __construct() {

            if ( ! is_admin() ) {
                if ( dusky_get_settings( 'frontDark', true ) ) {
                    add_action( 'init', function () {
                        if ( ! session_id() ) {
                            @session_start();
                        }
                        $mode = dusky_get_mode();

                        if ( dusky_get_settings( 'performanceMode', false ) || 'light' === $mode ) {
                            add_filter(
                                'script_loader_tag',
                                [$this, 'add_defer_attribute'],
                                10,
                                2
                            );
                        }

                    } );
                    if ( dusky_get_settings( 'flotingToggle', true ) ) {
                        add_action( 'wp_footer', [$this, 'render_floating_toggle'] );
                    }
                }

            } else {
                add_action( 'admin_footer', [$this, 'render_admin_floating_toggle'] );
            }
        }

        public function render_admin_floating_toggle() {
            $user_id = get_current_user_id();

            $toggleButton = dusky_get_admin_settings( $user_id, 'toggleButton', 'floatingToggle' );
            if ( $toggleButton === 'floatingToggle' ) {
                echo '<div id="dusky-floating-toggle"></div>';
            }
        }

        public function render_floating_toggle() {
            $mode = dusky_get_mode();
            $toggleStyle = dusky_get_settings( 'toggleStyle', '1' );
            $toggleSize = dusky_get_settings( 'toggleSize', 'medium' );
            $togglePosition = dusky_get_settings( 'togglePosition', 'right' );

            printf( '<div data-dusky_mode="%s" class="dusky-toggle-wrap dusky-floting dusky-%s dusky-active-%s dusky-toggle-style-%s dusky-toggle-%s dusky-ignore"><div class="dusky-toggle-icon"><div class="dusky-toggle-main-icon"></div></div><div class="dusky-toggle-text"><span class="dusky-dark">Dark</span><span class="dusky-light">Light</span></div></div>', esc_attr( $mode ), esc_attr( $togglePosition ), esc_attr( $mode ), esc_attr( $toggleStyle ), esc_attr( $toggleSize ) );
        }

        public function add_defer_attribute( $tag, $handle ) {
            if ( in_array( $handle, ['dusky-darkmode', 'dusky-frontend', 'dusky-frontend-config'] ) ) {
                $tag = str_replace( ' src', ' defer src', $tag );
            }
            return $tag;
        }

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                return self::$instance = new self();
            }
        }
    }

}

Dusky_Hooks::instance();
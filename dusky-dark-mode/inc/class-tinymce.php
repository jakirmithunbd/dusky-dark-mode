<?php

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Dusky_TinyMCE' ) ) {
    class Dusky_TinyMCE {
        /**
         * @var null
         */
        private static $instance = null;

        public function __construct() {
            add_filter( 'mce_css', [$this, 'enqueue_css'] );
            add_filter( 'mce_buttons', [$this, 'add_buttons'] );
            add_filter( 'mce_external_plugins', [$this, 'add_plugins'] );
        }

        public function add_buttons( $buttons ) {
            $buttons[] = 'dusky_toggle';

            return $buttons;
        }

        public function add_plugins( $plugins ) {
            $plugins['dusky_tinymce_js'] = DUSKY_ASSETS . '/js/tinymce.js';

            return $plugins;
        }

        public function enqueue_css( $mce_css ) {
            if ( ! empty( $mce_css ) ) {
                $mce_css .= ',';
            }

            $mce_css .= DUSKY_ASSETS . '/css/tinymce.css';

            return $mce_css;
        }

        /**
         * @return Dusky_TinyMCE|null
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

    }

}

Dusky_TinyMCE::instance();
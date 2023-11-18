<?php

defined( 'ABSPATH' ) or wp_die( 'Hey, what are you doing here? You silly human!' );

if ( ! class_exists( 'Dusky_Admin' ) ) {
    /**
     * The Plugin Admin Class
     * @since 1.0.0
     */
    class Dusky_Admin {

        /**
         * The single instance of the class.
         * @since 1.0.0
         * @static
         * @var
         */
        private static $instance = null;

        /**
         * The Admin Pages
         * @since 1.0.0
         * @static
         * @var
         */
        public static $admin_pages = [];

        /**
         * The class construct function
         * @return void
         * @since 1.0.0
         */
        public function __construct() {
            add_action( 'admin_menu', [$this, 'add_admin_menu'] );
            add_action( 'admin_bar_menu', [$this, 'add_admin_bar_menu'] );
        }

        public function add_dusky_submenu( $page_name, $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
            return self::$admin_pages[$page_name] = add_submenu_page(
                $parent_slug,
                $page_title,
                $menu_title,
                $capability,
                $menu_slug,
                $callback,
                $position
            );
        }

        /**
         * Add admin meun
         * @return void
         * @since 1.0.0
         */
        public function add_admin_menu(): void {
            $isDarkModeUserRoles = dusky_get_settings( 'isDarkModeUserRoles', false );

            $user_role = $isDarkModeUserRoles ? dusky_get_active_role() : false;

            add_menu_page(
                'Dusky Dark Mode',
                'Dark Mode',
                $user_role ? $user_role : 'manage_options',
                'dusky-dark-mode',
                [
                    $this,
                    'admin_page',
                ],
                DUSKY_ASSETS . '/admin/icons/DUSKY-white.svg',
                30
            );

            $this->add_dusky_submenu(
                'dusky',
                'dusky-dark-mode',
                __( 'Settings - Dusky', 'dusky-dark-mode' ),
                __( 'Settings', 'dusky-dark-mode' ),
                $user_role ? $user_role : 'manage_options',
                'dusky-dark-mode',
                [
                    $this,
                    'admin_page',
                ]
            );

            do_action( 'dusky_add_submenu_page', $this );
        }

        /**
         * Add admin bar menu
         * @return void
         * @since 1.0.0
         */
        public function add_admin_bar_menu( $wp_admin_bar ) {
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return;
            }
            if ( is_admin() ) {
                $args = [
                    'parent' => 'top-secondary',
                    'id'     => 'dusky',
                ];
                $wp_admin_bar->add_node( $args );
            }
        }

        /**
         * The Dusky admin menu page.
         * @return void
         * @since 1.0.0
         */
        public function admin_page(): void {
            echo '<div id="dusky-admin-app"></div>';
        }

        /**
         * Get admin menu pages.
         * @return array
         * @since 1.0.0
         * @static
         */
        public static function get_admin_pages(): array {
            return self::$admin_pages;
        }

        /**
         * The instantiate singleton class.
         * @return Dusky_Admin|null
         * @since 1.0.0
         * @static
         */
        public static function instance(): ?Dusky_Admin {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }

}

Dusky_Admin::instance();

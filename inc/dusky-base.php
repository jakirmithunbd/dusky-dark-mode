<?php

/**
 * WP Dusk Dark Mode
 */
final class Dusky_Dark_Mode {

    /**
     * The single instance of the class.
     * @var
     * @since 1.0.0
     * @static
     */
    private static $instance = null;

    /**
     * Ensure Singletone instance in this class.
     * @return Dusky_Dark_Mode|null
     * @since 1.0.0
     * @static
     */
    public static function instance(): ?Dusky_Dark_Mode {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->init_includes();
        $this->init_hooks();

        register_activation_hook( DUSKY_FILE, [$this, 'activate'] );
        register_deactivation_hook( DUSKY_FILE, [$this, 'deactivate'] );

        do_action( 'dusky_loaded' );
    }

    public function activate() {
        $this->include_class( 'class-install' );

        Dusky_Install::activate();
    }

    public function deactivate() {
        $this->include_class( 'class-uninstall' );

        Dusky_Uninstall::deactivate();
    }

    /**
     * Include Classes
     * @return void
     * @since 1.0.0
     */
    private function include_class( $name ): void {
        $path = DUSKY_INC . "/{$name}.php";
        if ( realpath( $path ) ) {
            require_once $path;
        }
    }

    /**
     * Includes requried files
     * @return void
     * @since 1.0.0
     */
    private function init_includes(): void {
        $this->include_class( 'functions' );
        $this->include_class( 'class-enqueue' );
        $this->include_class( 'class-admin' );
        $this->include_class( 'class-ajax' );
        $this->include_class( 'class-hooks' );
        $this->include_class( 'class-tinymce' );
        // $this->include_class('dusky-setting-router');
    }

    /**
     * Add required action hooks
     * @return void
     * @since 1.0.0
     */
    private function init_hooks(): void {
        add_action( 'plugins_loaded', [$this, 'dusky_load_text_domain'] );
    }

    /**
     * Load plugin textdomain
     * @return void
     * @since 1.0.0
     */
    public function dusky_load_text_domain(): void {
        load_plugin_textdomain( 'dusky', false, dirname( plugin_basename( DUSKY_FILE ) ) . '/languages' );
    }
}

if ( ! function_exists( 'Dusky_Dark_Mode' ) ) {
    function Dusky_Dark_Mode() {
        return Dusky_Dark_Mode::instance();
    }
}

Dusky_Dark_Mode();

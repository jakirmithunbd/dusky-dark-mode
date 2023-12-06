<?php

/**
 * Dusky Dark Mode
 */

/*
 * Plugin Name:       Dusky Dark Mode
 * Plugin URI:
 * Description:       Dusky Dark Mode instantly activates an elegant dark mode for your website, adapting to the user's operating system. Compatible with macOS, Windows, Android, and iOS platforms.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            wpwebdevs
 * Author URI:        https://wpwebdevs.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:
 * Text Domain:       dusky-dark-mode
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

//  Define constand
define( 'DUSKY_VERSION', '1.0.0' );
define( 'DUSKY_FILE', __FILE__ );
define( 'DUSKY_PATH', dirname( DUSKY_FILE ) );
define( 'DUSKY_INC', DUSKY_PATH . '/inc' );
define( 'DUSKY_URL', plugin_dir_url( __FILE__ ) );
define( 'DUSKY_ASSETS', DUSKY_URL . 'assets' );

if ( realpath( DUSKY_INC . '/dusky-base.php' ) ) {
    require_once realpath( DUSKY_INC . '/dusky-base.php' );
}
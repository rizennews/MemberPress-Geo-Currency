<?php
/**
 * Plugin Name: MemberPress Geo Currency
 * Plugin URI:  https://github.com/rizennews/memberpress-geo-currency
 * Description: Automatically switch the displayed currency in MemberPress based on the user's geolocation.
 * Version:     1.0.0
 * Author:      Padmore Aning
 * Author URI:  https://designolabs.com
 * Text Domain: memberpress-geo-currency
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Update Checker (using YahnisElsts/plugin-update-checker)
require 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/rizennews/memberpress-geo-currency/',
    __FILE__,
    'memberpress-geo-currency'
);

// Define plugin constants
define( 'MPGC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPGC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MPGC_PLUGIN_VERSION', '1.0.0' );

// Include necessary files
require_once MPGC_PLUGIN_DIR . 'admin/admin-settings.php';
require_once MPGC_PLUGIN_DIR . 'includes/class-currency-converter.php';
require_once MPGC_PLUGIN_DIR . 'includes/class-geolocation.php';

/**
 * Load plugin textdomain.
 */
function mpgc_load_textdomain() {
    load_plugin_textdomain( 'memberpress-geo-currency', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'mpgc_load_textdomain' );

/**
 * Initialize the plugin.
 */
function mpgc_init() {
    // Check if MemberPress is active
    if ( is_plugin_active( 'memberpress/memberpress.php' ) ) {
        // Initialize Geolocation
        $geolocation = new MPGC_Geolocation();

        // Initialize Currency Converter
        $currency_converter = new MPGC_Currency_Converter( $geolocation );
    
        // Hook into MemberPress price display
        add_filter( 'mepr-display-price-string', array( $currency_converter, 'convert_price_display' ), 10, 2 );

    } else {
        // Display an admin notice if MemberPress is not active
        add_action( 'admin_notices', 'mpgc_memberpress_not_active_notice' );
    }
}
add_action( 'plugins_loaded', 'mpgc_init' );

/**
 * Admin notice when MemberPress is not active.
 */
function mpgc_memberpress_not_active_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'MemberPress Geo Currency requires MemberPress to be activated.', 'memberpress-geo-currency' ); ?></p>
    </div>
    <?php
}

/**
 * Enqueue admin scripts and styles.
 */
function mpgc_enqueue_admin_scripts( $hook ) {
    if ( 'toplevel_page_mpgc-settings' !== $hook ) { // Ensure scripts are only loaded on our settings page
        return;
    }
    wp_enqueue_style( 'mpgc-admin-css', MPGC_PLUGIN_URL . 'assets/css/admin.css', array(), MPGC_PLUGIN_VERSION );
    wp_enqueue_script( 'mpgc-admin-js', MPGC_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), MPGC_PLUGIN_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'mpgc_enqueue_admin_scripts' );





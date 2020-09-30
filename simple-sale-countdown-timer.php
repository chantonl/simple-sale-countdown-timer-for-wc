<?php
/**
 * Plugin Name: Simple Sale Countdown Timer
 * Plugin URI: https://woocommerce.com/
 * Description: Add live countdown timer to your store and create limited time deal using this countdown plugin.
 * Version: 1.0.0
 * Author: Avamaksa
 * Author URI: https://woocommerce.com
 * Developer: Akash Dhawade
 * Developer URI: https://woocommerce.com
 * Text Domain: simple-sale-countdown-timer
 * Domain Path: /languages
 *
 * Woo: 6437673:2750b972bce8be0a1b719bc82263b439
 * WC requires at least: 3.5.0
 * WC tested up to: 4.3.3
 *
 * @package Simple Sale Countdown Timer
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Include the main Simple Sale Countdown Timer Class class.
if ( ! class_exists( 'Simple_Sale_Countdown_Timer', false ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-simple-sale-countdown-timer.php';
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || version_compare( get_option( 'woocommerce_db_version' ), Simple_Sale_Countdown_Timer::$wc_min_supported_version, '<' ) ) {
	add_action( 'admin_notices', 'Simple_Sale_Countdown_Timer::wc_inactive_notice' );
	return;
}

if ( ! defined( 'SSCT_PLUGIN_FILE' ) ) {
	define( 'SSCT_PLUGIN_FILE', __FILE__ );
}

/**
 * Returns the main instance of Simple_Sale_Countdown_Timer.
 *
 * @since  1.0.0
 * @return Simple_Sale_Countdown_Timer
 */
function s_s_c_t() {
	return new Simple_Sale_Countdown_Timer();
}

s_s_c_t();

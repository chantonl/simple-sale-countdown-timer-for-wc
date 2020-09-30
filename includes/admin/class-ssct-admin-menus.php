<?php
/**
 * Setup menus in WP admin.
 *
 * @package Simple Sale Countdown Timer\Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * SSCT_Admin_Menus Class.
 */
class SSCT_Admin_Menus {

	/**
	 * Hook in tabs.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );
		add_action( 'admin_init', array( $this, 'save_sale_data' ), 10 );
		add_action( 'load-woocommerce_page_ssct', array( $this, 'show_screen_options' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
		add_filter( 'set_screen_option_ssct_products_per_page', array( $this, 'set_screen_option' ), 10, 3 );

	}

	/**
	 * Add menu items.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		$hook = add_submenu_page( 'woocommerce', __( 'Sale Countdown', 'simple-sale-countdown-timer' ), __( 'Sale Countdown', 'simple-sale-countdown-timer' ), 'manage_options', 'ssct', array( $this, 'ssct_page_content' ), 5 );
	}

	/**
	 * Validate screen options on update.
	 *
	 * @since 1.0.0
	 * @param bool|int $status Screen option value. Default false to skip.
	 * @param string   $option The option name.
	 * @param int      $value  The number of rows to use.
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( in_array( $option, array( 'ssct_products_per_page' ), true ) ) {
			return ( $value <= 15 ) ? $value : 15;
		}

		return $status;
	}

	/**
	 * Show page content for the menu.
	 *
	 * @since 1.0.0
	 */
	public function ssct_page_content() {
		include_once SSCT_ABSPATH . 'includes/admin/class-ssct-admin-manage-sale.php';

		SSCT_Admin_Manage_Sale::output();
	}

	/**
	 * Add screen options.
	 *
	 * @since 1.0.0
	 */
	public function show_screen_options() {
		add_screen_option(
			'per_page',
			array(
				'default' => 10,
				'option'  => 'ssct_products_per_page',
			)
		);
	}

	/**
	 * Handle saving of settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save_sale_data() {
		include_once SSCT_ABSPATH . 'includes/admin/class-ssct-admin-manage-sale.php';

		// We should only save on the ssct page.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || 'ssct' !== $_GET['page'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// Save sale data if it has been posted.
		SSCT_Admin_Manage_Sale::add();

		// Save settings if it has been posted.
		SSCT_Admin_Manage_Sale::save_settings();
	}
}

return new SSCT_Admin_Menus();

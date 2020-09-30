<?php
/**
 * Simple Sale Countdown Timer Admin
 *
 * @class    SSCT_Admin
 * @package  Simple Sale Countdown Timer/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SSCT_Admin class.
 */
class SSCT_Admin {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_and_styles' ), 99 );
		add_filter( 'plugin_action_links_' . SSCT_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/class-ssct-admin-menus.php';
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @since 1.0.0
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=ssct&tab=settings' ) . '" aria-label="' . esc_attr__( 'View Simple Sale Countdown Settings', 'simple-sale-countdown-timer' ) . '">' . esc_html__( 'Settings', 'simple-sale-countdown-timer' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Enqueue admin scripts & styles.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts_and_styles() {
		wp_register_style( 'ssct-select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), WC_VERSION );
		wp_register_style( 'ssct-admin-css', plugins_url( 'assets/css/admin.css', SSCT_PLUGIN_FILE ), array(), SSCT_VERSION );

		$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

		wp_register_script( 'ssct-admin-js', SSCT_PLUGIN_URL . '/assets/js/admin/admin.js', array( 'jquery' ), SSCT_VERSION, true );
		wp_localize_script(
			'ssct-admin-js',
			'ssct_admin_params',
			array(
				'cm_settings' => $cm_settings,
			)
		);
	}
}

return new SSCT_Admin();

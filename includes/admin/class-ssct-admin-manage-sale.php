<?php
/**
 * Admin Manage Sale
 *
 * Functions used for displaying manage sale functionality and view all products on sale.
 *
 * @package Simple Sale Countdown Timer/Admin
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'SSCT_Admin_Manage_Sale', false ) ) {
	return;
}

/**
 * SSCT_Admin_Manage_Sale Class.
 */
class SSCT_Admin_Manage_Sale {

	/**
	 * Supported product types.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private static $supports = array( 'simple', 'variation', 'external', 'subscription', 'subscription_variation' );

	/**
	 * Error messages.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Update messages.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $messages = array();

	/**
	 * Handles the displays of admin pages.
	 *
	 * @since 1.0.0
	 */
	public static function output() {
		if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}

		wp_enqueue_style( 'jquery-ui-style' );
		wp_enqueue_style( 'wp-codemirror' );
		wp_enqueue_style( 'ssct-select2' );
		wp_enqueue_style( 'ssct-admin-css' );

		wp_enqueue_script( 'selectWoo' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_script( 'wp-codemirror' );
		wp_enqueue_script( 'ssct-admin-js' );

		$tabs = array(
			'on-sale'  => __( 'Manage', 'simple-sale-countdown-timer' ),
			'settings' => __( 'Settings', 'simple-sale-countdown-timer' ),

		);

		$current_tab = ( isset( $_GET['tab'] ) ) ? sanitize_title( wp_unslash( $_GET['tab'] ) ) : 'on-sale'; // phpcs:ignore csrf ok, sanitization ok. ?>

		<div id="ssct-add-new-wrapper" class="wrap nosubsub">
			<nav class="nav-tab-wrapper ssct-nav-tab-wrapper">
				<?php
				foreach ( $tabs as $slug => $label ) {
					echo '<a href="' . esc_html( admin_url( 'admin.php?page=ssct&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
				}
				?>
			</nav>
			<?php self::show_messages(); ?>
			<?php
			switch ( $current_tab ) {
				case 'on-sale':
					include_once 'views/html-manage-sale.php';
					break;

				case 'settings':
					include_once 'views/html-sale-settings.php';
					break;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Add sale data
	 *
	 * @since 1.0.0
	 * @param array $data Optional. Data to use for saving. Defaults to $_POST.
	 * @return bool
	 */
	public static function add( $data = null ) {
		if ( is_null( $data ) ) {
			$data = isset( $_POST['ssct_add_sale'] ) && isset( $_POST['ssct_set_sale_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ssct_set_sale_nonce'] ) ), 'ssct_set_sale' ) ? $_POST : array();
		}

		if ( empty( $data ) ) {
			return false;
		}

		$product_ids = array();
		$start_date  = '';
		$end_date    = '';

		$sale_on        = isset( $data['ssct_is_on'] ) ? wp_unslash( $data['ssct_is_on'] ) : '';
		$offer_at       = isset( $data['ssct_offer_at'] ) ? wp_unslash( $data['ssct_offer_at'] ) : '';
		$offer_type     = isset( $data['ssct_offer_type'] ) ? wp_unslash( $data['ssct_offer_type'] ) : '';
		$start_date_str = isset( $data['ssct_start_date'] ) ? wc_clean( wp_unslash( $data['ssct_start_date'] ) ) : '';
		$end_date_str   = isset( $data['ssct_end_date'] ) ? wc_clean( wp_unslash( $data['ssct_end_date'] ) ) : '';

		if ( 'products' === $sale_on ) {
			$product_ids = isset( $data['ssct_product_ids'] ) ? $data['ssct_product_ids'] : array();
		}

		if ( 'categories' === $sale_on ) {
			$category_slugs = isset( $data['ssct_category_ids'] ) ? $data['ssct_category_ids'] : array();

			if ( $category_slugs ) {
				$product_ids = wc_get_products(
					array(
						'category' => $category_slugs,
						'return'   => 'ids',
					)
				);
			}
		}

		if ( $start_date_str ) {
			$start_date = gmdate( 'Y-m-d 00:00:00', strtotime( $start_date_str ) );
		}

		if ( $end_date_str ) {
			$end_date = gmdate( 'Y-m-d 23:59:59', strtotime( $end_date_str ) );
		}

		if ( $product_ids ) {
			$sale_added = 0;

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product instanceof WC_Product && in_array( $product->get_type(), self::$supports, true ) ) {
					$regular_price = $product->get_regular_price();
					$sale_price    = ssct_calculate_sale_price( $regular_price, $offer_at, $offer_type );

					if ( $sale_price < $regular_price ) {
						$product->set_sale_price( $sale_price );
						$product->set_date_on_sale_from( $start_date );
						$product->set_date_on_sale_to( $end_date );
						$product->save();

						$sale_added++;
					}
				}
			}

			if ( 0 < $sale_added ) {
				self::add_message( __( 'Sale has been added.', 'simple-sale-countdown-timer' ) );
			} else {
				self::add_error( __( 'No sale has been added. Please make sure the calculated offered price is less than the regular price of the product.', 'simple-sale-countdown-timer' ) );
			}
		}
	}

	/**
	 * Save updated settings.
	 *
	 * @since 1.0.0
	 * @param array $data Optional. Data to use for saving. Defaults to $_POST.
	 * @return bool
	 */
	public static function save_settings( $data = null ) {
		if ( is_null( $data ) ) {
			$data = isset( $_POST['ssct_save_settings'] ) && isset( $_POST['ssct_save_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ssct_save_settings_nonce'] ) ), 'ssct_save_settings' ) ? $_POST : array();
		}

		if ( empty( $data ) ) {
			return false;
		}

		$default = Simple_Sale_Countdown_Timer::get_default_settings();

		$ssct_settings = array(
			'countdown_timer' => array(
				'shop'    => ! empty( $_POST['countdown_timer']['shop'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_timer']['shop'] ) ) : 'no',
				'product' => ! empty( $_POST['countdown_timer']['product'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_timer']['product'] ) ) : 'no',
				'deals'   => ! empty( $_POST['countdown_timer']['deals'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_timer']['deals'] ) ) : 'no',
				'when'    => ! empty( $_POST['countdown_timer']['when'] ) && 48 >= $_POST['countdown_timer']['when'] ? absint( $_POST['countdown_timer']['when'] ) : $default['countdown_timer']['when'],
				'format'  => isset( $_POST['countdown_timer']['format'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_timer']['format'] ) ) : $default['countdown_timer']['format'],

			),
			'texts'           => array(
				'prefix'   => isset( $_POST['texts']['prefix'] ) ? sanitize_text_field( wp_unslash( $_POST['texts']['prefix'] ) ) : '',
				'finish'   => isset( $_POST['texts']['finish'] ) ? sanitize_text_field( wp_unslash( $_POST['texts']['finish'] ) ) : '',
				'sale'     => isset( $_POST['texts']['sale'] ) ? sanitize_text_field( wp_unslash( $_POST['texts']['sale'] ) ) : '',
				'deals'    => isset( $_POST['texts']['deals'] ) ? sanitize_text_field( wp_unslash( $_POST['texts']['deals'] ) ) : '',
				'no_deals' => isset( $_POST['texts']['no_deals'] ) ? sanitize_text_field( wp_unslash( $_POST['texts']['no_deals'] ) ) : '',
			),
			'additional'      => array(
				'savings'    => ! empty( $_POST['additional']['savings'] ) ? sanitize_text_field( wp_unslash( $_POST['additional']['savings'] ) ) : 'no',
				'deals_page' => ! empty( $_POST['additional']['deals_page'] ) ? absint( $_POST['additional']['deals_page'] ) : '',
				'custom_css' => ! empty( $_POST['additional']['custom_css'] ) ? sanitize_text_field( $_POST['additional']['custom_css'] ) : '',
			),
		);

		do_action( 'ssct_before_save_settings', $ssct_settings );

		update_option( 'ssct_settings', $ssct_settings );

		self::add_message( __( 'Your settings have been saved.', 'simple-sale-countdown-timer' ) );

		do_action( 'ssct_after_save_settings', $ssct_settings );
	}

	/**
	 * Add a message.
	 *
	 * @since 1.0.0
	 * @param string $text Message.
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}

	/**
	 * Add an error.
	 *
	 * @since 1.0.0
	 * @param string $text Message.
	 */
	public static function add_error( $text ) {
		self::$errors[] = $text;
	}

	/**
	 * Output messages + errors.
	 *
	 * @since 1.0.0
	 */
	public static function show_messages() {
		if ( count( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error ) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		} elseif ( count( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message ) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}
}

<?php
/**
 * Simple Sale Countdown Timer Init
 *
 * @package Simple Sale Countdown Timer
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Simple Sale Countdown Timer Class.
 *
 * @class Simple_Sale_Countdown_Timer
 */
final class Simple_Sale_Countdown_Timer {

	/**
	 * Simple_Sale_Countdown_Timer version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * WC minimum supported version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $wc_min_supported_version = '3.5.0';

	/**
	 * Setting values.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Default setting values.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $default_settings = array();

	/**
	 * Simple_Sale_Countdown_Timer Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define Simple_Sale_Countdown_Timer Constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define( 'SSCT_PLUGIN_URL', untrailingslashit( plugins_url( '/', SSCT_PLUGIN_FILE ) ) );
		$this->define( 'SSCT_ABSPATH', dirname( SSCT_PLUGIN_FILE ) . '/' );
		$this->define( 'SSCT_PLUGIN_BASENAME', plugin_basename( SSCT_PLUGIN_FILE ) );
		$this->define( 'SSCT_VERSION', $this->version );
	}

	/**
	 * Initialise settings.
	 *
	 * @since 1.0.0
	 */
	public function init_settings() {
		self::$settings = get_option( 'ssct_settings', array() );

		if ( empty( self::$settings ) ) {
			self::$settings = self::get_default_settings();
		}
	}

	/**
	 * Initialise default settings.
	 *
	 * @since 1.0.0
	 */
	public static function get_default_settings() {
		self::$default_settings = array(
			'countdown_timer' => array(
				'shop'    => 'yes',
				'product' => 'yes',
				'deals'   => 'yes',
				'when'    => 24,
				'format'  => 'simple',

			),
			'texts'           => array(
				'prefix'   => __( 'Ends in', 'simple-sale-countdown-timer' ),
				'finish'   => __( 'Sorry! You missed this deal', 'simple-sale-countdown-timer' ),
				'sale'     => __( 'Limited time deal', 'simple-sale-countdown-timer' ),
				'no_deals' => __( 'No product(s) found.', 'simple-sale-countdown-timer' ),
			),
			'additional'      => array(
				'savings'    => 'yes',
				'deals_page' => '',
				'custom_css' => '',
			),
		);

		update_option( 'ssct_settings', self::$default_settings );

		return self::$default_settings;
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'wp_head', array( $this, 'remove_actions' ) );
		add_action( 'init', array( $this, 'init_settings' ) );
		add_filter( 'shortcode_atts_sale_products', array( $this, 'set_deals_attr' ), 10, 4 );
		add_filter( 'woocommerce_shortcode_products_query_results', array( $this, 'filter_query_results' ), 10, 2 );
		add_action( 'woocommerce_shortcode_sale_products_loop_no_results', array( $this, 'no_deals_found_html' ) );
		add_filter( 'woocommerce_sale_flash', array( $this, 'sale_flash_html' ), 20, 3 );
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'init_countdown_timer' ), 8, 1 );
		add_action( 'woocommerce_before_single_product', array( $this, 'init_countdown_timer' ), 10, 1 );
		add_filter( 'woocommerce_get_price_html', array( $this, 'show_countdown_timer_after_price' ), 10, 3 );
		add_filter( 'woocommerce_get_price_html', array( $this, 'show_countdown_timer_after_price_variation' ), 10, 3 );
		add_filter( 'woocommerce_subscriptions_product_price_string', array( $this, 'show_countdown_timer_after_price_subscription' ), 10, 3 );
		add_filter( 'woocommerce_subscriptions_product_price_string', array( $this, 'show_countdown_timer_after_price_variation_subscription' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_filter( 'woocommerce_available_variation', array( $this, 'init_countdown_timer_variation' ), 10, 3 );
		add_action( 'wp_head', array( $this, 'custom_css' ) );
		add_filter( 'ssct_countdown_timer_html', array( $this, 'show_savings' ), 10, 4 );
	}



	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		/**
		 * Core classes.
		 */
		include_once SSCT_ABSPATH . 'includes/ssct-core-functions.php';

		if ( is_admin() ) {
			include_once SSCT_ABSPATH . 'includes/admin/class-ssct-admin.php';
		}
	}

	/**
	 * Called when WooCommerce is inactive or running and out-of-date version to display an inactive notice.
	 *
	 * @since 1.0.0
	 */
	public static function wc_inactive_notice() {
		if ( current_user_can( 'activate_plugins' ) ) {

			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				$install_url = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => 'woocommerce',
						),
						admin_url( 'update.php' )
					),
					'install-plugin_woocommerce'
				);

				// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
				printf( esc_html__( '%1$sSimple Sale Countdown Timer is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Simple Sale Countdown Timer to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'simple-sale-countdown-timer' ), '<div class="notice notice-error"><p><strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a></p></div>' );
			} elseif ( version_compare( get_option( 'woocommerce_db_version' ), self::$wc_min_supported_version, '<' ) ) {
				// translators: 1$-2$: opening and closing <strong> tags, 3$: minimum supported WooCommerce version, 4$-5$: opening and closing link tags, leads to plugin admin.
				printf( esc_html__( '%1$sSimple Sale Countdown Timer is inactive.%2$s This version of the plugin requires WooCommerce %3$s or newer. Please %4$supdate WooCommerce to version %3$s or newer &raquo;%5$s', 'simple-sale-countdown-timer' ), '<div class="notice notice-error"><p><strong>', '</strong>', esc_html( self::$wc_min_supported_version ), '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '</a></p></div>' );
			}
		}
	}

	/**
	 * Remove actions
	 *
	 * @since 1.0.0
	 */
	public function remove_actions() {
		if ( ( is_shop() && 'yes' !== self::$settings['countdown_timer']['shop'] ) ||
			( is_product() && 'yes' !== self::$settings['countdown_timer']['product'] ) ||
			( get_the_ID() === self::$settings['additional']['deals_page'] && 'yes' !== self::$settings['countdown_timer']['deals'] )
		) {
			remove_action( 'woocommerce_before_shop_loop_item', array( $this, 'init_countdown_timer' ), 8, 1 );
		}
	}

	/**
	 * Called when no products are found to show on todays deal page.
	 *
	 * @since 1.0.0
	 * @param array $attributes The user defined shortcode attributes.
	 */
	public function no_deals_found_html( $attributes ) {
		if ( isset( $attributes['ssct_deals'] ) && $attributes['ssct_deals'] ) {
			echo wp_kses_post( apply_filters( 'ssct_no_deals_found_html', '<p class="woocommerce-info">' . esc_html( self::$settings['texts']['no_deals'] ) . '</p>' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Filter shortcode products query results to show products that are on sale for the day.
	 *
	 * @since 1.0.0
	 * @param  stdClass              $results            Query results.
	 * @param  WC_Shortcode_Products $products_shortcode WC_Shortcode_Products instance.
	 * @return stdClass
	 */
	public function filter_query_results( $results, $products_shortcode ) {
		$shortcode_attr = $products_shortcode->get_attributes();
		$current_date   = gmdate( 'Y-m-d', current_time( 'timestamp' ) );

		if ( isset( $shortcode_attr['ssct_deals'] ) && $shortcode_attr['ssct_deals'] && $results->ids ) {
			foreach ( $results->ids as $index => $product_id ) {
				$product      = wc_get_product( $product_id );
				$product_type = $product->get_type();
				$unset        = false;

				if ( in_array( $product_type, array( 'simple', 'external' ), true ) ) {
					$end_date = gmdate( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() );

					if ( $current_date !== $end_date ) {
						$unset = true;
					}
				}

				if ( in_array( $product_type, array( 'variable', 'variable-subscription' ), true ) ) {
					$children      = $product->get_children();
					$on_sale_count = 0;

					if ( $children ) {
						foreach ( $children as $child_id ) {
							$child           = wc_get_product( $child_id );
							$date_on_sale_to = $child->get_date_on_sale_to();

							if ( $date_on_sale_to ) {
								$end_date = gmdate( 'Y-m-d', $date_on_sale_to->getTimestamp() );

								if ( $current_date === $end_date ) {
									$on_sale_count ++;
									break;
								};
							}
						}
					}

					if ( 0 === $on_sale_count ) {
						$unset = true;
					}
				}

				if ( $unset ) {
					unset( $results->ids[ $index ] );
				}
			}
		}

		return $results;
	}

	/**
	 * If the shortcode has 'ssct_deals' attribute then the shortocde result will be filtered only to show products
	 * that are on sale for the day.
	 *
	 * @since 1.0.0
	 * @param  array $out       The output array of shortcode attributes.
	 * @param  array $pairs     The supported attributes and their defaults.
	 * @param  array $atts      The user defined shortcode attributes.
	 * @param  array $shortcode The shortcode being used.
	 * @return string
	 */
	public function set_deals_attr( $out, $pairs, $atts, $shortcode ) {
		if ( 'sale_products' === $shortcode && isset( $atts['ssct_deals'] ) && wc_string_to_bool( $atts['ssct_deals'] ) ) {
			$out['ssct_deals'] = true;
		}

		return $out;
	}

	/**
	 * Show user defined sale flash text while the countdown is still active.
	 *
	 * @since 1.0.0
	 * @param string     $sale_html The flash sale html.
	 * @param WP_Post    $post      The current post.
	 * @param WC_Product $product   The current product.
	 * @return string
	 */
	public function sale_flash_html( $sale_html, $post, $product ) {
		global $product;

		if ( isset( $product->ssct_data[ $product->get_id() ]['seconds_left'] ) ) {
			$sale_html = '<span class="onsale">' . self::$settings['texts']['sale'] . '</span>';
		}

		return apply_filters( 'ssct_sale_flash_html', $sale_html, self::$settings, $post, $product );
	}

	/**
	 * Register/queue frontend scripts.
	 *
	 * @since 1.0.0
	 */
	public function load_scripts() {
		if ( ( is_shop() && 'yes' === self::$settings['countdown_timer']['shop'] ) ||
			( is_product() && 'yes' === self::$settings['countdown_timer']['product'] ) ||
			( get_the_ID() === self::$settings['additional']['deals_page'] && 'yes' === self::$settings['countdown_timer']['deals'] )
		) {
			wp_enqueue_script( 'ssct-count-down-timer', SSCT_PLUGIN_URL . '/assets/js/frontend/count-down-timer.js', array( 'jquery' ), SSCT_VERSION, true );
			wp_localize_script( 'ssct-count-down-timer', 'ssct_count_down_timer_params', array( 'settings' => self::$settings ) );
		}

	}

	/**
	 * Fetch hours left for the sale to end.
	 *
	 * @since 1.0.0
	 * @param WC_Product $product Product object.
	 * @return string|bool
	 */
	private function get_hours_left_for_sale_to_end( $product ) {
		return ( $product->get_date_on_sale_to() ) ? max( ( $product->get_date_on_sale_to()->getTimestamp() - time() ) / 3600, 1, 0 ) : false;
	}

	/**
	 * Initialise countdown timer for a variation on sale.
	 *
	 * @since 1.0.0
	 * @param array                $variation_data   Avaialble variation data.
	 * @param WC_Product_Variable  $variable_product Variable product object.
	 * @param WC_Product Variatiob $variation        Variation object.
	 * @return array
	 */
	public function init_countdown_timer_variation( $variation_data, $variable_product, $variation ) {
		global $product;

		if ( isset( $product->ssct_data[ $variation->get_id() ]['seconds_left'] ) ) {
			$variation_id                   = $variation->get_id();
			$variation_data['ssct_seconds'] = $product->ssct_data[ $variation_id ]['seconds_left'];
			$variation_data['ssct_target']  = 'ssct-' . $variation_id;
		}

		return $variation_data;
	}

	/**
	 * Initialise countdown timer for simple, external, variable & variable-subscription type products.
	 *
	 * @since 1.0.0
	 */
	public function init_countdown_timer() {
		global $product;

		if ( $product->is_on_sale() ) {
			$product->ssct_data = array();
			$product_type       = $product->get_type();

			if ( in_array( $product_type, array( 'simple', 'external', 'subscription' ), true ) ) {
				$hours_left = $this->get_hours_left_for_sale_to_end( $product );

				if ( $hours_left && $hours_left <= self::$settings['countdown_timer']['when'] ) {
					$product->ssct_data[ $product->get_id() ]['seconds_left'] = $product->get_date_on_sale_to()->getTimestamp() - time();
				}
			}

			if ( in_array( $product_type, array( 'variable', 'variable-subscription' ), true ) ) {
				$children  = $product->get_children();
				$max_hours = 24;

				if ( $children ) {
					foreach ( $children as $child_id ) {
						$child      = wc_get_product( $child_id );
						$hours_left = $this->get_hours_left_for_sale_to_end( $child );

						if ( $hours_left && $hours_left <= self::$settings['countdown_timer']['when'] ) {
							$seconds_left = $child->get_date_on_sale_to()->getTimestamp() - time();

							if ( $hours_left < $max_hours ) {
								$max_hours = $hours_left;
								$product->ssct_data[ $product->get_id() ]['seconds_left'] = $seconds_left;
							}

							$product->ssct_data[ $child_id ]['seconds_left'] = $seconds_left;
						}
					}
				}
			}
		}
	}

	/**
	 * Called to show countdown timer after price on shop, deals & single product page.
	 *
	 * @since 1.0.0
	 * @param string    $price Price html.
	 * @param WC_Produt $product Product object.
	 * @return string
	 */
	public function show_countdown_timer_after_price( $price, $product ) {
		if ( isset( $product->ssct_data[ $product->get_id() ]['seconds_left'] ) ) {
			$supported_types = array();

			if ( is_shop() || ( get_queried_object_id() === self::$settings['additional']['deals_page'] ) ) {
				$supported_types = array( 'simple', 'variable', 'external' );
			}

			if ( is_single() ) {
				$supported_types = array( 'simple', 'external' );
			}

			if ( in_array( $product->get_type(), $supported_types, true ) ) {
				$price .= self::countdown_timer_html( $product, null, $product->ssct_data[ $product->get_id() ]['seconds_left'] );
			}
		}

		return $price;
	}

	/**
	 * Called to show countdown timer after price on shop, deals & single product page for a subscription product.
	 *
	 * @since 1.0.0
	 * @param string     $price   Price html.
	 * @param WC_Product $product Product object.
	 * @param array      $include Subscription inclusions.
	 * @return string
	 */
	public function show_countdown_timer_after_price_subscription( $price, $product, $include ) {
		if ( isset( $product->ssct_data[ $product->get_id() ]['seconds_left'] ) ) {
			$supported_types = array();

			if ( is_shop() || ( get_queried_object_id() === self::$settings['additional']['deals_page'] ) ) {
				$supported_types = array( 'subscription', 'variable-subscription' );
			}

			if ( is_single() ) {
				$supported_types = array( 'subscription' );
			}

			if ( in_array( $product->get_type(), $supported_types, true ) ) {
				$price .= self::countdown_timer_html( $product, null, $product->ssct_data[ $product->get_id() ]['seconds_left'] );
			}
		}

		return $price;
	}

	/**
	 * Called to show countdown timer after price on single product page for a variation.
	 *
	 * @since 1.0.0
	 * @param string               $price     Price html.
	 * @param WC_Product_Variation $variation Variation object.
	 * @return string
	 */
	public function show_countdown_timer_after_price_variation( $price, $variation ) {
		global $product;

		$supported_types = array( 'variation' );

		if ( isset( $product->ssct_data[ $product->get_id() ]['seconds_left'] ) && in_array( $variation->get_type(), $supported_types, true ) ) {
			$price .= self::countdown_timer_html( $product, $variation, $product->ssct_data[ $product->get_id() ]['seconds_left'] );
		}

		return $price;

	}

	/**
	 * Called to show countdown timer after price on single product page for a subscription variation.
	 *
	 * @since 1.0.0
	 * @param string               $price     Price html.
	 * @param WC_Product_Variation $variation Variation object.
	 * @return string
	 */
	public function show_countdown_timer_after_price_variation_subscription( $price, $variation ) {
		global $product;

		$supported_types = array( 'subscription_variation' );

		if ( isset( $product->ssct_data[ $product->get_id() ]['seconds_left'] ) && in_array( $variation->get_type(), $supported_types, true ) ) {
			$price .= self::countdown_timer_html( $product, $variation, $product->ssct_data[ $product->get_id() ]['seconds_left'] );
		}

		return $price;
	}

	/**
	 * The html required to show countdown timer
	 *
	 * @since 1.0.0
	 * @param WC_Product                $product      Product object.
	 * @param WC_Product_Variation|null $variation    Variation object.
	 * @param string                    $seconds_left Seconds left for the sale to end.
	 * @return string
	 */
	public static function countdown_timer_html( $product, $variation = null, $seconds_left = '' ) {
		$html = apply_filters( 'ssct_before_countdown_timer_html', '', $product, $variation, $seconds_left );

		if ( $product && $seconds_left ) {
			$id = ( $variation instanceof WC_Product_Variation ) ? $variation->get_id() : $product->get_id();

			$html .= '<span id="ssct-' . $id . '" class="ssct" data-product_id="' . $id . '" data-seconds="' . $seconds_left . '" style="display:block;font-size:14px"><span class="ssct-text"></span><span class="ssct-timer"></span></span>';
		}

		return apply_filters( 'ssct_countdown_timer_html', $html, $product, $variation, $seconds_left );
	}

	/**
	 * Called to show savings on single product page for products on sale.
	 *
	 * @since 1.0.0
	 * @param string                    $html         Price html.
	 * @param WC_Product                $product      Product object.
	 * @param WC_Product_Variation|null $variation    Variation object.
	 * @param string                    $seconds_left Seconds left for the sale to end.
	 * @return string
	 */
	public function show_savings( $html, $product, $variation, $seconds_left ) {
		if ( ssct_show_savings() ) {
			$html .= ssct_get_savings_html( $product, $variation );
		}

		return $html;
	}

	/**
	 * Load user defined custom css.
	 *
	 * @since 1.0.0
	 */
	public function custom_css() {
		if ( self::$settings['additional']['custom_css'] ) {
			echo '<style type="text/css">' . esc_html( self::$settings['additional']['custom_css'] ) . '</style>';
		}
	}
}

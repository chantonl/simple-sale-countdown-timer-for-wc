<?php
/**
 * Simple Sale Countdown Timer Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package Simple Sale Countdown Timer\Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to calculate sale price.
 *
 * @since 1.0.0
 * @param string $regular_price Product regular price.
 * @param string $offer_at      Offered price.
 * @param string $offer_type    Product Offer type.
 * @return string
 */
function ssct_calculate_sale_price( $regular_price = 0, $offer_at = 0, $offer_type = 'fixed-price' ) {
	switch ( $offer_type ) {
		case 'fixed-price':
			$price = ( $regular_price > $offer_at ) ? $offer_at : $regular_price;
			break;

		case 'fixed-discount':
			$price = ( $regular_price >= $offer_at ) ? $regular_price - $offer_at : $regular_price;
			break;

		case 'percentage-discount':
			$percent_discount = ( min( $offer_at, 100 ) / 100 ) * $regular_price;
			$price            = ( $regular_price >= $percent_discount ) ? $regular_price - $percent_discount : $regular_price;
			break;
	}

	return number_format( $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
}

/**
 * Function to get savings html.
 *
 * @since 1.0.0
 * @param WC_product           $product   Product object.
 * @param WC_product_Variation $variation Variation object.
 * @return string
 */
function ssct_get_savings_html( $product, $variation = null ) {
	$savings_html   = '';
	$product        = ( $variation ) ? $variation : $product;
	$supported_type = array( 'simple', 'external', 'subscription', 'variation', 'subscription_variation' );

	if ( $product->is_on_sale() && in_array( $product->get_type(), $supported_type, true ) ) {
		$price         = $product->get_price();
		$regular_price = $product->get_regular_price();

		$saved_amount   = $regular_price - $price;
		$percent_saving = round( ( $saved_amount * 100 ) / $regular_price );

		$savings_html = sprintf( '<span class="ssct-savings" style="display:block;margin:1em 0;font-size:16px">' . __( 'You save:', 'simple-sale-countdown-timer' ) . '<b>&nbsp;%s (%d&#37;)</span></b>', wc_price( $saved_amount ), $percent_saving );
	}

	return apply_filters( 'ssct_savings_html', $savings_html, $product, $variation );
}

/**
 * Whether to show savings on a particular page or not.
 *
 * @since 1.0.0
 * @return bool
 */
function ssct_show_savings() {
	return ( 'yes' === Simple_Sale_Countdown_Timer::$settings['additional']['savings'] && is_product() ) ? true : false;
}

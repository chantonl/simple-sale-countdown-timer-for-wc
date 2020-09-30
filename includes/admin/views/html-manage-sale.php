<?php
/**
 * Admin View: Manage Sale
 *
 * @package Simple Sale Countdown Timer/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SSCT_ABSPATH . 'includes/admin/class-ssct-products-list.php';

$onsale_products = new SSCT_Products_List();
$onsale_products->prepare_items();

?>
<h1 class="wp-heading-inline"><?php echo esc_html__( 'Manage products on sale', 'simple-sale-countdown-timer' ); ?></h1>
<hr class="wp-header-end">
<p><?php $onsale_products->display_admin_notices(); ?></p>
<form method="post" class="search-form wp-clearfix">
	<?php $onsale_products->search_box( esc_html__( 'Search Product', 'simple-sale-countdown-timer' ), 'ssct-product' ); ?>
</form>	
<div id="col-container" class="wp-clearfix">
	<div id="col-left">
		<div class="col-wrap">
			<p><?php echo esc_html__( 'Products on sale for your store can be managed here. Select products/categories for sale, set an offer price and schedule your sale in one go.', 'simple-sale-countdown-timer' ); ?></p>	
			<div class="form-wrap">
				<h2><?php echo esc_html__( 'Set products on sale', 'simple-sale-countdown-timer' ); ?></h2>
				<form method="post" action="admin.php?page=ssct">
					<div class="form-field">
						<nav id="nav-tab-type" class="nav-tab-wrapper ssct-nav-tab-wrapper">
							<a href="#" data-sale_on="products" class="nav-tab nav-tab-active"><?php echo esc_html__( 'Products', 'simple-sale-countdown-timer' ); ?></a>
							<a href="#" data-sale_on="categories" class="nav-tab"><?php echo esc_html__( 'Categories', 'simple-sale-countdown-timer' ); ?></a>
							<input type="hidden" id="ssct-is-on" name="ssct_is_on" value="products">
						</nav>	
						<div class="group-fields">
							<div id="ssct-products-container"> 
								<select class="wc-product-search" multiple="multiple" style="width: 80%" id="ssct-products" name="ssct_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'simple-sale-countdown-timer' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude_type="variable,variable-subscription,grouped" required>
								</select>
							</div>	
							<div id="ssct-categories-container" class="ssct-hidden"> 
								<select class="wc-category-search" multiple="multiple" style="width: 80%" id="ssct-categories" name="ssct_category_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product category&hellip;', 'simple-sale-countdown-timer' ); ?>" data-action="woocommerce_json_search_categories">
								</select>
							</div>	
						</div>
						<p><?php echo esc_html__( 'You can choose multiple products/categories.', 'simple-sale-countdown-timer' ); ?></p>
					</div>	
					<div class="form-field">
						<label for="offer-at"><?php echo esc_html__( 'Offer at *', 'simple-sale-countdown-timer' ); ?></label>
						<div class="group-fields">
							<input type="text" id="ssct-offer-at" class="ssct-offer-at decimal" name="ssct_offer_at" size="5" required>
							<select id="ssct-offer-type" class="ssct-offer-type" name="ssct_offer_type">
								<option value="percentage-discount"><?php echo esc_html__( 'Percentage discount', 'simple-sale-countdown-timer' ); ?></option>
								<option value="fixed-price"><?php echo esc_html__( 'Fixed price', 'simple-sale-countdown-timer' ); ?></option>
								<option value="fixed-discount"><?php echo esc_html__( 'Fixed discount', 'simple-sale-countdown-timer' ); ?></option>
							</select>
						</div>	
						<p><?php echo esc_html__( 'The price/discount you want to offer.', 'simple-sale-countdown-timer' ); ?></p>
					</div>
					<div class="form-field">						
						<div class="col-5">
							<label for="start-date"><?php echo esc_html__( 'Start date', 'simple-sale-countdown-timer' ); ?></label>
							<input type="text" id="ssct-start-date" class="ssct-datepicker ssct-start-date" name="ssct_start_date" autocomplete="off" placeholder="<?php echo esc_html__( 'From&hellip;YYYY-MM-DD', 'simple-sale-countdown-timer' ); ?>">
							<p><?php echo esc_html__( 'Sale start date', 'simple-sale-countdown-timer' ); ?></p>
						</div>
						<div class="col-5">
							<label for="start-date"><?php echo esc_html__( 'End date', 'simple-sale-countdown-timer' ); ?></label>
							<input type="text" id="ssct-end-date" class="ssct-datepicker ssct-end-date" name="ssct_end_date" autocomplete="off" placeholder="<?php echo esc_html__( 'To&hellip;YYYY-MM-DD', 'simple-sale-countdown-timer' ); ?>">
							<p><?php echo esc_html__( 'Sale end date', 'simple-sale-countdown-timer' ); ?></p>
						</div>	
					</div>
					<p class="submit">
						<button name="ssct_add_sale" type="submit" value="Set" class="button button-primary"><?php echo esc_html__( 'Set', 'simple-sale-countdown-timer' ); ?></button>
					</p>
					<?php wp_nonce_field( 'ssct_set_sale', 'ssct_set_sale_nonce' ); ?>	
				</form>	
			</div>				
		</div>
	</div>
	<div id="col-right">
		<div class="col-wrap">
			<form id="posts-filter" method="post">
				<?php $onsale_products->display_page(); ?>
			</form>	
		</div>
	</div>
</div>

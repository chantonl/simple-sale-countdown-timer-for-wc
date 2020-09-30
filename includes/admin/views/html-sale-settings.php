<?php
/**
 * Admin View: Sale Settings
 *
 * @package Simple Sale Countdown Timer/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ssct_settings = get_option( 'ssct_settings', Simple_Sale_Countdown_Timer::$default_settings );

?>
<ul class="subsubsub">
	<li>
		<a href="<?php echo esc_html( admin_url( 'admin.php?page=ssct&tab=settings' ) ); ?>" class="<?php echo ! isset( $_GET['section'] ) || '' === sanitize_text_field( wp_unslash( $_GET['section'] ) ) ? 'current' : ''; // WPCS: input var okay, CSRF ok. ?>">
			<?php echo esc_html__( 'Countdown timer', 'simple-sale-countdown-timer' ); ?>
		</a> | 
		<a href="<?php echo esc_html( admin_url( 'admin.php?page=ssct&tab=settings&section=customize-texts' ) ); ?>" class="<?php echo isset( $_GET['section'] ) && 'customize-texts' === $_GET['section'] ? 'current' : ''; // WPCS: input var okay, CSRF ok. ?>">
			<?php echo esc_html__( 'Customize texts', 'simple-sale-countdown-timer' ); ?>
		</a> | 
		<a href="<?php echo esc_html( admin_url( 'admin.php?page=ssct&tab=settings&section=additional' ) ); ?>" class="<?php echo isset( $_GET['section'] ) && 'additional' === $_GET['section'] ? 'current' : ''; // WPCS: input var okay, CSRF ok. ?>">
			<?php echo esc_html__( 'Additional', 'simple-sale-countdown-timer' ); ?>
		</a>
	</li>	
</ul>		
<br class="clear">	
<form id="ssct-settings" action="" method="post"> 
	<div class="<?php echo ! isset( $_GET['section'] ) ? '' : 'hide'; // WPCS: input var okay, CSRF ok. ?>">
		<h2><?php echo esc_html__( 'Countdown timer', 'simple-sale-countdown-timer' ); ?></h2>
		<p><?php echo esc_html__( 'This is where you can customize the countdown timer for your store.', 'simple-sale-countdown-timer' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Enable on?', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Enable on?', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="countdown_timer_shop">
								<input name="countdown_timer[shop]" id="countdown_timer_shop" type="checkbox" class="" value="yes" <?php echo 'yes' === $ssct_settings['countdown_timer']['shop'] ? 'checked' : ''; ?>><?php echo esc_html__( 'Shop page', 'simple-sale-countdown-timer' ); ?>
							</label>
							<br>
							<label for="countdown_timer_product">
								<input name="countdown_timer[product]" id="countdown_timer_product" type="checkbox" class="" value="yes" <?php echo 'yes' === $ssct_settings['countdown_timer']['product'] ? 'checked' : ''; ?>><?php echo esc_html__( 'Product page', 'simple-sale-countdown-timer' ); ?>
							</label>
							<br>
							<label for="countdown_timer_deals">
								<input name="countdown_timer[deals]" id="countdown_timer_deals" type="checkbox" class="" value="yes" <?php echo 'yes' === $ssct_settings['countdown_timer']['deals'] ? 'checked' : ''; ?>><?php echo esc_html__( 'Today\'s deal page', 'simple-sale-countdown-timer' ); ?>
							</label>
							<p class="description"><?php echo esc_html__( 'The countdown timer will only be visible for scheduled on sale product(s)', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'When to show?', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'When to show?', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="countdown_timer_when">
								<?php echo esc_html__( 'Show countdown timer when less than', 'simple-sale-countdown-timer' ); ?>&nbsp;<input type="number" name="countdown_timer[when]" id="countdown_timer_when" min="1" max="48" value="<?php echo esc_attr( $ssct_settings['countdown_timer']['when'] ); ?>"><?php echo esc_html__( 'hour(s) left for the sale to end.', 'simple-sale-countdown-timer' ); ?>
							</label>
							<p class="description"><?php echo esc_html__( 'Default value is 24 hours. Maximum value is 48 hours.', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Format', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Countdown timer format', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="countdown_timer_format">
								<input value="simple" type="radio" name="countdown_timer[format]" <?php echo 'simple' === $ssct_settings['countdown_timer']['format'] ? 'checked' : ''; ?>><?php echo esc_html__( '02h 30m 20s', 'simple-sale-countdown-timer' ); ?>&nbsp;&nbsp;
								<input value="colon" type="radio" name="countdown_timer[format]" <?php echo 'colon' === $ssct_settings['countdown_timer']['format'] ? 'checked' : ''; ?>><?php echo esc_html__( '02:30:20', 'simple-sale-countdown-timer' ); ?>&nbsp;&nbsp;
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="<?php echo isset( $_GET['section'] ) && 'customize-texts' === $_GET['section'] ? '' : 'hide'; // WPCS: input var okay, CSRF ok. ?>">	
		<h2><?php echo esc_html__( 'Customize texts', 'simple-sale-countdown-timer' ); ?></h2>
		<p><?php echo esc_html__( 'This is where you can customize all texts related to the countdown timer and sale.', 'simple-sale-countdown-timer' ); ?></p>
		<table class="form-table">
			<tbody>		
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Countdown text', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Countdown text', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="texts_prefix">
								<input name="texts[prefix]" id="texts_prefix" type="text" class="regular-text" value="<?php echo esc_attr( $ssct_settings['texts']['prefix'] ); ?>">
							</label>
							<p class="description"><?php echo esc_html__( 'The text to append before the countdown timer for e.g Ends in 2h 30m 20s.', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Countdown finish text', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Countdown finish text', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="texts_finish">
								<input name="texts[finish]" id="texts_finish" type="text" class="regular-text" value="<?php echo esc_attr( $ssct_settings['texts']['finish'] ); ?>">
							</label>
							<p class="description"><?php echo esc_html__( 'The text to show after countdown has ended.', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Flash sale text', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Flash sale text', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="texts_sale">
								<input name="texts[sale]" id="texts_sale" type="text" class="regular-text" value="<?php echo esc_attr( $ssct_settings['texts']['sale'] ); ?>">
							</label>
							<p class="description"><?php echo esc_html__( 'The urgency text to show while the countdown timer is still active.', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>			
				<tr valign="top" class="">
					<th scope="row" class="titledesc"><?php echo esc_html__( 'No products found text', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'No products found text', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="texts_no_deals">
								<input name="texts[no_deals]" id="texts_no_deals" type="text" class="regular-text" value="<?php echo esc_attr( $ssct_settings['texts']['no_deals'] ); ?>" >
							</label>
							<p class="description"><?php echo esc_html__( 'The text to show when there are no products found in Today\'s deal', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="<?php echo isset( $_GET['section'] ) && 'additional' === $_GET['section'] ? '' : 'hide'; // WPCS: input var okay, CSRF ok. ?>">
		<h2><?php echo esc_html__( 'Additional', 'simple-sale-countdown-timer' ); ?></h2>
		<p><?php echo esc_html__( 'This is where you can configure the additional settings,', 'simple-sale-countdown-timer' ); ?></p>	
		<table class="form-table">
			<tbody>			
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Show savings?', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Show savings?', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="additional_savings">
								<input name="additional[savings]" id="additional_savings" type="checkbox" class="" value="yes" <?php echo 'yes' === $ssct_settings['additional']['savings'] ? 'checked' : ''; ?>><?php echo esc_html__( 'Amount saved will be displayed on single product page.', 'simple-sale-countdown-timer' ); ?>
							</label>
							<p class="description">
								<?php
									/* translators: 1: currency symbol */
									printf( esc_html__( 'For example, You save: %1$s16 (33&#37;)', 'simple-sale-countdown-timer' ), esc_html( get_woocommerce_currency_symbol() ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
								?>
							</p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Today\'s deals page', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Today\'s deals page', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<?php
							wp_dropdown_pages(
								array(
									'name'              => 'additional[deals_page]',
									'id'                => 'additional_deals_page',
									'sort_column'       => 'menu_order',
									'sort_order'        => 'ASC',
									'show_option_none'  => esc_attr__( 'Select a page&hellip;', 'simple-sale-countdown-timer' ),
									'class'             => 'ssct-page-dropdown',
									'echo'              => true,
									'selected'          => absint( $ssct_settings['additional']['deals_page'] ),
									'post_status'       => 'publish,private,draft',
									'option_none_value' => '',
								)
							);
							?>
							<p class="description"><?php echo esc_html__( 'Select today\'s deals page to show all products on your store that are on sale for the day.', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc"><?php echo esc_html__( 'Custom CSS', 'simple-sale-countdown-timer' ); ?></th>
					<td class="forminp forminp-text">
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php echo esc_html__( 'Custom CSS', 'simple-sale-countdown-timer' ); ?></span>
							</legend>
							<label for="additional_custom_css">
								<textarea name="additional[custom_css]" id="custom_css" cols="50" rows="8"><?php echo esc_html( $ssct_settings['additional']['custom_css'] ); ?></textarea>
							</label>
							<p class="description"><?php echo esc_html__( 'Add your custom css for the countdown timer over here', 'simple-sale-countdown-timer' ); ?></p>
						</fieldset>
					</td>
				</tr>	
			</tbody>
		</table>
	</div>	
	<p class="submit">
		<button name="ssct_save_settings" id="ssct_save_settings" class="button-primary ssct-save-button" type="submit" value="Save changes"><?php echo esc_html__( 'Save changes', 'simple-sale-countdown-timer' ); ?></button>
		<?php wp_nonce_field( 'ssct_save_settings', 'ssct_save_settings_nonce' ); ?>
	</p>	
</form>	

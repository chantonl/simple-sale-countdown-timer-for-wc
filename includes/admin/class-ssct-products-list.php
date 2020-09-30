<?php
/**
 * Simple Sale Countdown Timer List
 *
 * Show all products that are on sale and scheduled to be on sale.
 *
 * @package  Simple Sale Countdown Timer/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 *  List all products on sale.
 *
 * @class SSCT_Products_List
 */
class SSCT_Products_List extends WP_List_Table {

	/**
	 * Notices to display when loading the table.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $admin_notices = array();


	/**
	 * Initialize the ssct list.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Products On Sale', 'simple-sale-countdown-timer' ), // Singular name of the listed records.
				'plural'   => __( 'Product On Sale', 'simple-sale-countdown-timer' ), // Plural name of the listed records.
				'ajax'     => false, // should this table support ajax?

			)
		);

	}

	/**
	 * Display the table heading and search query, if any
	 *
	 * @since 1.0.0
	 */
	public function display_admin_notices() {
		foreach ( $this->admin_notices as $notice ) {
			echo '<div id="message" class="' . esc_attr( $notice['class'] ) . '">';
			echo '	<p>' . wp_kses_post( $notice['message'] ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Retrieve products on sale from the database
	 *
	 * @since 1.0.0
	 * @param int $per_page Items to show per page.
	 * @param int $page_number Current page number.
	 *
	 * @return mixed
	 */
	public static function get_product_ids_on_sale( $per_page = 10, $page_number = 1 ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'post_title'; // WPCS: input var okay, CSRF ok.
		$order   = ( ! empty( $_REQUEST['order'] ) && 'desc' === strtolower( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) ) ) ? 'DESC' : 'ASC'; // WPCS: input var okay, CSRF ok.

		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation'      => 'AND',
				'regular_price' => array(
					'key'     => '_regular_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'numeric',
				),
				'sale_price'    => array(
					'key'     => '_sale_price',
					'value'   => '',
					'compare' => '!=',
					'type'    => 'string',
				),
			),
			'orderby'        => array( esc_sql( $orderby ) => $order ),
			'offset'         => ( $page_number - 1 ) * $per_page,
			'fields'         => 'ids',
		);

		if ( ! empty( $_REQUEST['s'] ) ) { // WPCS: input var okay, CSRF ok.
			$args['s'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ); // WPCS: input var okay, CSRF ok.
		}

		$product_ids = new WP_Query( $args );

		return $product_ids->posts;
	}

	/**
	 * Returns the count of on sale products in the database.
	 *
	 * @since 1.0.0
	 * @return null|string
	 */
	public static function on_sale_product_count() {
		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation'      => 'AND',
				'regular_price' => array(
					'key'     => '_regular_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'numeric',
				),
				'sale_price'    => array(
					'key'     => '_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'numeric',
				),
			),
			'fields'         => 'ids',
		);

		if ( ! empty( $_REQUEST['s'] ) ) { // WPCS: input var okay, CSRF ok.
			$args['s'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ); // WPCS: input var okay, CSRF ok.
		}

		$product_ids = new WP_Query( $args );

		return $product_ids->post_count;
	}

	/**
	 * Text displayed when no products are on sale
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		printf( esc_html__( 'Your store as no products on sale. ', 'simple-sale-countdown-timer' ) . '<a href="%s" target="_blank">' . esc_html__( 'View products', 'simple-sale-countdown-timer' ) . '</a>', esc_url( admin_url( 'edit.php?post_type=product' ) ) );
	}

	/**
	 *  Associative array of columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {

		$currency_symbol = '&nbsp;(' . get_woocommerce_currency_symbol() . ')';

		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'thumb'         => '<span class="dashicons dashicons-format-image"></span>',
			'post_title'    => __( 'Title', 'simple-sale-countdown-timer' ),
			'regular_price' => __( 'Regular Price', 'simple-sale-countdown-timer' ) . $currency_symbol,
			'sale_price'    => __( 'Sale Price', 'simple-sale-countdown-timer' ) . $currency_symbol,
			'start_date'    => __( 'Start Date', 'simple-sale-countdown-timer' ),
			'end_date'      => __( 'End Date', 'simple-sale-countdown-timer' ),
			'status'        => __( 'Status', 'simple-sale-countdown-timer' ),
		);

		return $columns;
	}

	/**
	 * Get bulk actions.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'remove_dates' => __( 'Remove Schedule Dates', 'simple-sale-countdown-timer' ),
			'end_sale'     => __( 'End Sale', 'simple-sale-countdown-timer' ),
		);
	}


	/**
	 * Get a list of sortable columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'post_title'    => array( 'post_title', true ),
			'regular_price' => array( 'regular_price', true ),
			'sale_price'    => array( 'sale_price', true ),
			'start_date'    => array( 'start_date', true ),
			'end_date'      => array( 'end_date', true ),
		);
	}

	/**
	 * Column cb.
	 *
	 * @since 1.0.0
	 * @param  int $product_id Product id in loop.
	 * @return string
	 */
	public function column_cb( $product_id ) {
		return sprintf( '<input type="checkbox" name="ssct_bulk_delete[]" value="%1$s" />', esc_attr( $product_id ) );
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @since 1.0.0
	 * @param int    $product_id Product id in loop.
	 * @param string $column_name Column name in loop.
	 *
	 * @return mixed
	 */
	public function column_default( $product_id, $column_name ) {
		$start_date           = '';
		$end_date             = '';
		$start_date_timestamp = '';
		$end_date_timestamp   = '';

		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product_Variation ) {
			$product_id = $product->get_parent_id();
			$title      = $product->get_title() . '&nbsp;-&nbsp;' . implode( ' / ', $product->get_variation_attributes() );
		} else {
			$product_id = $product->get_id();
			$title      = $product->get_title();
		}

		$edit_link = get_edit_post_link( $product_id );
		$view_link = get_permalink( $product_id );

		$sale_price_dates_from_timestamp = $product->get_date_on_sale_from( 'edit' ) ? $product->get_date_on_sale_from( 'edit' )->getOffsetTimestamp() : false;
		$sale_price_dates_to_timestamp   = $product->get_date_on_sale_to( 'edit' ) ? $product->get_date_on_sale_to( 'edit' )->getOffsetTimestamp() : false;

		$sale_price_dates_from = $sale_price_dates_from_timestamp ? date_i18n( 'Y/m/d H:i', $sale_price_dates_from_timestamp ) : '-';
		$sale_price_dates_to   = $sale_price_dates_to_timestamp ? date_i18n( 'Y/m/d H:i', $sale_price_dates_to_timestamp ) : '-';

		$thumbnail_url = $product->get_image_id( 'edit' ) ? esc_url( wp_get_attachment_thumb_url( $product->get_image_id( 'edit' ) ) ) : esc_url( wc_placeholder_img_src() );

		switch ( $column_name ) {

			case 'thumb':
				return '<img src="' . $thumbnail_url . '">';

			case 'post_title':
				$title = '<a href="' . $edit_link . '" target="_blank">' . $title . '</a>';

				$row_actions = array(
					'edit' => '<a href="' . $edit_link . '" target="_blank">' . __( 'Edit', 'simple-sale-countdown-timer' ) . '</a>',
					'view' => '<a href="' . $view_link . '" target="_blank">' . __( 'View', 'simple-sale-countdown-timer' ) . '</a>',
				);

				return $title .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

			case 'regular_price':
				return $product->get_regular_price();

			case 'sale_price':
				return $product->get_sale_price();

			case 'start_date':
				return $sale_price_dates_from;

			case 'end_date':
				return $sale_price_dates_to;

			case 'status':
				if ( $product->is_on_sale() ) {
					$status = '<span title="' . __( 'Active', 'simple-sale-countdown-timer' ) . '" class="dot dot-green"></span>';
				} elseif ( $sale_price_dates_to_timestamp && ( ( time() + wc_timezone_offset() ) > $sale_price_dates_to_timestamp ) ) {
					$status = '<span title="' . __( 'Ended', 'simple-sale-countdown-timer' ) . '"class="dot dot-red"></span>';
				} else {
					$status = '<span title="' . __( 'Upcoming', 'simple-sale-countdown-timer' ) . '"class="dot dot-blue"></span>';
				}

				return $status;
		}
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {

		$this->prepare_column_headers();

		// Process bulk action.
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'ssct_products_per_page' );
		$current_page = $this->get_pagenum();
		$total_items  = self::on_sale_product_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // We have to calculate the total number of items.
				'per_page'    => $per_page, // We have to determine how many items to show on a page.
			)
		);

		$this->items = self::get_product_ids_on_sale( $per_page, $current_page );
	}


	/**
	 * Set _column_headers property for table list
	 *
	 * @since 1.0.0
	 */
	protected function prepare_column_headers() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);
	}

	/**
	 * Handle bulk actions
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_action() {
		$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
		$action = 'bulk-' . $this->_args['plural'];

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return;
		}

		$ids = ! empty( $_POST['ssct_bulk_delete'] ) ? array_map( 'absint', (array) $_POST['ssct_bulk_delete'] ) : array(); // WPCS: input var okay, CSRF ok.

		if ( 'end_sale' === $this->current_action() && $ids ) {
			foreach ( $ids as $product_id ) {
				$product = wc_get_product( $product_id );

				$product->set_sale_price( '' );
				$product->set_date_on_sale_from( '' );
				$product->set_date_on_sale_to( '' );
				$product->save();
			}
		}

		if ( 'remove_dates' === $this->current_action() && $ids ) {
			foreach ( $ids as $product_id ) {
				$product = wc_get_product( $product_id );

				$product->set_date_on_sale_from( '' );
				$product->set_date_on_sale_to( '' );
				$product->save();
			}
		}
	}

	/**
	 * Render the list table page, including header, notices, status filters and table.
	 *
	 * @since 1.0.0
	 */
	public function display_page() {
		$this->display();
	}
}

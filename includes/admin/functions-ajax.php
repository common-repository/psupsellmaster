<?php
/**
 * Admin - Functions - AJAX.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Search and return pages.
 */
function psupsellmaster_ajax_get_pages() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-pages', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'posts_per_page'      => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_page_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_pages', 'psupsellmaster_ajax_get_pages' );

/**
 * Search and return products.
 */
function psupsellmaster_ajax_get_products() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-products', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'posts_per_page'      => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_product_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_products', 'psupsellmaster_ajax_get_products' );

/**
 * Search and return product authors.
 */
function psupsellmaster_ajax_get_product_authors() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-product-authors', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_product_author_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_product_authors', 'psupsellmaster_ajax_get_product_authors' );

/**
 * Search and return product statuses.
 */
function psupsellmaster_ajax_get_product_statuses() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-product-statuses', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_product_status_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_product_statuses', 'psupsellmaster_ajax_get_product_statuses' );

/**
 * Search and return product category terms.
 */
function psupsellmaster_ajax_get_product_category_terms() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-product-category-terms', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
		'items'   => array(),
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_product_category_term_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_product_category_terms', 'psupsellmaster_ajax_get_product_category_terms' );

/**
 * Search and return product tag terms.
 */
function psupsellmaster_ajax_get_product_tag_terms() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-product-tag-terms', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
		'items'   => array(),
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_product_tag_term_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_product_tag_terms', 'psupsellmaster_ajax_get_product_tag_terms' );

/**
 * Search and return taxonomy terms.
 */
function psupsellmaster_ajax_get_taxonomy_terms() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-taxonomy-terms', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
		'items'   => array(),
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Get the taxonomy.
	$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Check if the taxonomy is not empty.
	if ( ! empty( $taxonomy ) ) {
		// Set the args.
		$args['taxonomy'] = $taxonomy;
	}

	// Get the data.
	$data = psupsellmaster_get_taxonomy_term_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_taxonomy_terms', 'psupsellmaster_ajax_get_taxonomy_terms' );

/**
 * Search and return campaigns.
 */
function psupsellmaster_ajax_get_campaigns() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-campaigns', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'posts_per_page'      => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_campaign_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_campaigns', 'psupsellmaster_ajax_get_campaigns' );

/**
 * Search and return campaign statuses.
 */
function psupsellmaster_ajax_get_campaign_statuses() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-campaign-statuses', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_campaign_status_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_campaign_statuses', 'psupsellmaster_ajax_get_campaign_statuses' );

/**
 * Search and return campaign locations.
 */
function psupsellmaster_ajax_get_campaign_locations() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-campaign-locations', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'number'              => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Get the data.
	$data = psupsellmaster_get_campaign_location_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_campaign_locations', 'psupsellmaster_ajax_get_campaign_locations' );

/**
 * Search and return coupons.
 */
function psupsellmaster_ajax_get_coupons() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-get-coupons', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	$page = filter_var( $page, FILTER_VALIDATE_INT );

	// Get the search.
	$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

	// Get the group.
	$group = isset( $_GET['group'] ) ? sanitize_text_field( wp_unslash( $_GET['group'] ) ) : '';

	// Set the args.
	$args = array(
		'psupsellmaster_page' => 1,
		'posts_per_page'      => 20,
	);

	// Check if the page is not empty.
	if ( ! empty( $page ) ) {
		// Set the args.
		$args['psupsellmaster_page'] = $page;
	}

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Set the args.
		$args['psupsellmaster_search_text'] = $search;
	}

	// Check if the group is not empty.
	if ( ! empty( $group ) ) {
		// Set the args.
		$args['psupsellmaster_group'] = $group;
	}

	// Get the data.
	$data = psupsellmaster_get_coupon_label_value_pairs( $args );

	// Set the items.
	$output['items'] = $data['items'];

	// Set the meta.
	$output['meta'] = $data['meta'];

	// Set the success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_get_coupons', 'psupsellmaster_ajax_get_coupons' );

/**
 * Assign multiple terms to multiple products.
 */
function psupsellmaster_ajax_assign_multiple_product_terms() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Check if the current user can perform this action.
	if ( ! current_user_can( 'edit_posts' ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the selectors.
	$selectors = isset( $_POST['selectors'] ) ? map_deep( wp_unslash( $_POST['selectors'] ), 'sanitize_text_field' ) : array();

	// Check if the selectors is empty.
	if ( empty( $selectors ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the taxonomies.
	$taxonomies = isset( $_POST['taxonomies'] ) ? map_deep( wp_unslash( $_POST['taxonomies'] ), 'sanitize_text_field' ) : array();

	// Check if the taxonomies is empty.
	if ( empty( $taxonomies ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the args.
	$args = array(
		'options' => $selectors,
	);

	// Get the products.
	$products = psupsellmaster_get_products_by_selectors( $args );

	// Check if the products is empty.
	if ( empty( $products ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the sanitized taxonomies.
	$taxonomies = psupsellmaster_insert_mixed_taxonomy_terms( array( 'taxonomies' => $taxonomies ) );

	// Check if the taxonomies is empty.
	if ( empty( $taxonomies ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the args.
	$args = array(
		'objects'    => $products,
		'taxonomies' => $taxonomies,
	);

	// Assign the terms to the products.
	psupsellmaster_assign_object_taxonomy_terms( $args );

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_assign_multiple_product_terms', 'psupsellmaster_ajax_assign_multiple_product_terms' );

/**
 * Unassign terms from a post through ajax.
 */
function psupsellmaster_ajax_unassign_post_terms() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Check if the current user can perform this action.
	if ( ! current_user_can( 'edit_posts' ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the post id.
	$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
	$post_id = filter_var( $post_id, FILTER_VALIDATE_INT );

	// Check if the post id is empty.
	if ( empty( $post_id ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the taxonomy.
	$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : '';

	// Check if the taxonomy is empty.
	if ( empty( $taxonomy ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the term ids.
	$term_ids = array();

	// Check if the term ids is set.
	if ( isset( $_POST['term_ids'] ) ) {
		// Check if the term ids is an array.
		if ( is_array( $_POST['term_ids'] ) ) {
			// Set the term ids.
			$term_ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['term_ids'] ) );
		} else {
			// Set the term ids.
			$term_ids = array( sanitize_text_field( wp_unslash( $_POST['term_ids'] ) ) );
		}

		// Set the term ids.
		$term_ids = filter_var_array( $term_ids, FILTER_VALIDATE_INT );

		// Remove duplicate entries.
		$term_ids = array_unique( $term_ids );

		// Remove empty entries.
		$term_ids = array_filter( $term_ids );
	}

	// Check if the term ids is empty.
	if ( empty( $term_ids ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Delete the terms from the post.
	wp_remove_object_terms( $post_id, $term_ids, $taxonomy );

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_unassign_post_terms', 'psupsellmaster_ajax_unassign_post_terms' );

/**
 * Get the campaigns through ajax.
 */
function psupsellmaster_ajax_get_campaigns_list_table() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Get the input draw.
	$input_draw = isset( $_POST['draw'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['draw'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_draw = false !== $input_draw ? $input_draw : 0;

	// Get the input start.
	$input_start = isset( $_POST['start'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['start'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_start = false !== $input_start ? $input_start : 0;

	// Get the input length.
	$input_length = isset( $_POST['length'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['length'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_length = false !== $input_length ? $input_length : 0;

	// Get the input filters.
	$input_filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();

	// Set the input order.
	$input_order = null;

	// Set the input orderby.
	$input_orderby = null;

	// Set the default orderby.
	$default_orderby = 3;

	// Set the default order.
	$default_order = 'DESC';

	// Check if the input order inputs does exist.
	if ( isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) && isset( $_POST['order'][0]['dir'] ) ) {
		// Set the input order.
		$input_orderby = filter_var( sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) ), FILTER_VALIDATE_INT );
		$input_orderby = false !== $input_orderby ? $input_orderby : $default_orderby;

		// Set the input order.
		$input_order = strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) );
		$input_order = in_array( $input_order, array( 'ASC', 'DESC' ), true ) ? $input_order : $default_order;
	}

	// Set the charts.
	$charts = array(
		'main' => array(
			'items'   => array(),
			'legends' => array(),
		),
	);

	// Set the datatable.
	$datatable = array(
		'data'     => array(),
		'draw'     => ( $input_draw + 1 ),
		'filtered' => 0,
		'total'    => 0,
	);

	// Set the datatables.
	$datatables = array(
		'main' => $datatable,
	);

	// Set the kpis.
	$kpis = array();

	// Set the output.
	$output = array(
		'charts'     => $charts,
		'datatables' => $datatables,
		'kpis'       => $kpis,
		'success'    => false,
	);

	// Set the sql select.
	$sql_select = ( 'SELECT COUNT( * ) AS `rows`' );

	// Set the sql from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `campaigns`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the sql query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Get the count all rows.
	$count_all_rows = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql where.
	$sql_where = ( 'WHERE 1 = 1' );

	// Check if the filter is set.
	if ( isset( $input_filters['status'] ) ) {
		// Set the filters.
		$filters = $input_filters['status'];

		// Check if the filter is not an array.
		if ( ! is_array( $input_filters['status'] ) ) {
			// Set the filters.
			$filters = array( $input_filters['status'] );
		}

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filters ), '%s' ) );

		// Set the sql where.
		$sql_where .= PsUpsellMaster_Database::prepare(
			" AND `campaigns`.`status` IN ( {$placeholders} )",
			$filters
		);
	}

	// Check if the filter is set.
	if ( isset( $input_filters['locations'] ) ) {
		// Set the filters.
		$filters = $input_filters['locations'];

		// Check if the filter is not an array.
		if ( ! is_array( $input_filters['locations'] ) ) {
			// Set the filters.
			$filters = array( $input_filters['locations'] );
		}

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filters ), '%s' ) );

		// Set the sql locations in.
		$sql_locations_in = PsUpsellMaster_Database::prepare( "`campaign_locations`.`location` IN ( {$placeholders} )", $filters );

		// Set the sql locations.
		$sql_locations = PsUpsellMaster_Database::prepare(
			"
			SELECT
				1
			FROM
				%i AS `campaign_locations`
			WHERE
				`campaign_locations`.`campaign_id` = `campaigns`.`id`
			AND
				{$sql_locations_in}
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' )
		);

		// Set the meta key.
		$meta_key = 'locations_flag';

		// Set the meta value.
		$meta_value = 'all';

		// Set the sql where.
		$sql_where .= PsUpsellMaster_Database::prepare(
			"
			AND EXISTS (
				{$sql_locations}
				UNION ALL
				SELECT
					1
				FROM
					%i AS `campaignmeta`
				WHERE
					`campaignmeta`.`psupsellmaster_campaign_id` = `campaigns`.`id`
				AND
					`campaignmeta`.`meta_key` = %s
				AND
					`campaignmeta`.`meta_value` = %s
			)
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
			$meta_key,
			$meta_value
		);
	}

	// Check if the filter is set.
	if ( isset( $input_filters['weekdays'] ) ) {
		// Set the filters.
		$filters = $input_filters['weekdays'];

		// Check if the filter is not an array.
		if ( ! is_array( $input_filters['weekdays'] ) ) {
			// Set the filters.
			$filters = array( $input_filters['weekdays'] );
		}

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filters ), '%s' ) );

		// Set the sql weekdays in.
		$sql_weekdays_in = PsUpsellMaster_Database::prepare( "`campaign_weekdays`.`weekday` IN ( {$placeholders} )", $filters );

		// Set the sql weekdays.
		$sql_weekdays = PsUpsellMaster_Database::prepare(
			"
			SELECT
				1
			FROM
				%i AS `campaign_weekdays`
			WHERE
				`campaign_weekdays`.`campaign_id` = `campaigns`.`id`
			AND
				{$sql_weekdays_in}
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' )
		);

		// Set the meta key.
		$meta_key = 'weekdays_flag';

		// Set the meta value.
		$meta_value = 'all';

		// Set the sql where.
		$sql_where .= PsUpsellMaster_Database::prepare(
			"
			AND EXISTS (
				{$sql_weekdays}
				UNION ALL
				SELECT
					1
				FROM
					%i AS `campaignmeta`
				WHERE
					`campaignmeta`.`psupsellmaster_campaign_id` = `campaigns`.`id`
				AND
					`campaignmeta`.`meta_key` = %s
				AND
					`campaignmeta`.`meta_value` = %s
			)
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
			$meta_key,
			$meta_value
		);
	}

	// Check if the filter is set.
	if ( isset( $input_filters['campaign_date'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['campaign_date']['start'] ) ) {
			// Set the filter.
			$filter = DateTime::createFromFormat( 'Y/m/d', $input_filters['campaign_date']['start'] );
			$filter = false !== $filter ? $filter->format( 'Y-m-d' ) : null;

			// Check if the filter is not empty.
			if ( ! empty( $filter ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `campaigns`.`start_date` >= %s',
					$filter
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['campaign_date']['end'] ) ) {
			// Set the filter.
			$filter = DateTime::createFromFormat( 'Y/m/d', $input_filters['campaign_date']['end'] );
			$filter = false !== $filter ? $filter->format( 'Y-m-d' ) : null;

			// Check if the filter is not empty.
			if ( ! empty( $filter ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `campaigns`.`end_date` <= %s',
					$filter
				);
			}
		}
	}

	// Check if the filter is set.
	if ( isset( $input_filters['events'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['events']['impression'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['events']['impression']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `impression_events`.`quantity` >= %d',
					$input_filters['events']['impression']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['events']['impression']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `impression_events`.`quantity` <= %d',
					$input_filters['events']['impression']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['events']['click'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['events']['click']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `click_events`.`quantity` >= %d',
					$input_filters['events']['click']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['events']['click']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `click_events`.`quantity` <= %d',
					$input_filters['events']['click']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['events']['add_to_cart'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['events']['add_to_cart']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `cart_events`.`quantity` >= %d',
					$input_filters['events']['add_to_cart']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['events']['add_to_cart']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `cart_events`.`quantity` <= %d',
					$input_filters['events']['add_to_cart']['max']
				);
			}
		}
	}

	// Check if the filter is set.
	if ( isset( $input_filters['carts'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['carts']['gross_earnings'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['gross_earnings']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `gross_earnings`.`amount` >= %f',
					$input_filters['carts']['gross_earnings']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['gross_earnings']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `gross_earnings`.`amount` <= %f',
					$input_filters['carts']['gross_earnings']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['tax'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['tax']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `tax`.`amount` >= %f',
					$input_filters['carts']['tax']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['tax']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `tax`.`amount` <= %f',
					$input_filters['carts']['tax']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['discount'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['discount']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `discount`.`amount` >= %f',
					$input_filters['carts']['discount']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['discount']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `discount`.`amount` <= %f',
					$input_filters['carts']['discount']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['net_earnings'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['net_earnings']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `net_earnings`.`amount` >= %f',
					$input_filters['carts']['net_earnings']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['net_earnings']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `net_earnings`.`amount` <= %f',
					$input_filters['carts']['net_earnings']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['orders_count'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['orders_count']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `orders_count`.`total` >= %d',
					$input_filters['carts']['orders_count']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['orders_count']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `orders_count`.`total` <= %d',
					$input_filters['carts']['orders_count']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['aov'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['aov']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( `net_earnings`.`amount` / COALESCE( NULLIF( `orders_count`.`total`, 0 ), 1 ) ) >= %f',
					$input_filters['carts']['aov']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['aov']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( `net_earnings`.`amount` / COALESCE( NULLIF( `orders_count`.`total`, 0 ), 1 ) ) <= %f',
					$input_filters['carts']['aov']['max']
				);
			}
		}
	}

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Set the sql products count.
	$sql_products_count = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaignmeta`.`psupsellmaster_campaign_id` AS `campaign_id`,
			COUNT( * ) AS `total`
		FROM
			%i AS `campaignmeta`
		INNER JOIN
			%i AS `eligible_products`
		ON
			`eligible_products`.`campaign_id` = `campaignmeta`.`psupsellmaster_campaign_id`
		WHERE
			`campaignmeta`.`meta_key` = %s
		AND
			`campaignmeta`.`meta_value` = %s
		GROUP BY
			`campaign_id`
		UNION
		SELECT
			`campaignmeta`.`psupsellmaster_campaign_id` AS `campaign_id`,
			(
				SELECT
					COUNT( * )
				FROM
					%i AS `posts`
				WHERE
					`posts`.`post_type` = %s
				AND
					`posts`.`post_status` = %s
			) AS `total`
		FROM
			%i AS `campaignmeta`
		WHERE
			`campaignmeta`.`meta_key` = %s
		AND
			`campaignmeta`.`meta_value` = %s
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
		'products_flag',
		'selected',
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$product_post_type,
		'publish',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
		'products_flag',
		'all',
	);

	// Set the sql impression events.
	$sql_impression_events = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			SUM( `quantity` ) AS `quantity`
		FROM
			%i
		WHERE
			`event_name` = %s
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' ),
		'impression'
	);

	// Set the sql click events.
	$sql_click_events = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			SUM( `quantity` ) AS `quantity`
		FROM
			%i
		WHERE
			`event_name` = %s
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' ),
		'click'
	);

	// Set the sql cart events.
	$sql_cart_events = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			SUM( `quantity` ) AS `quantity`
		FROM
			%i
		WHERE
			`event_name` = %s
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' ),
		'add_to_cart'
	);

	// Set the sql discount.
	$sql_discount = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			SUM( `discount` ) AS `amount`
		FROM
			%i
		WHERE
			`order_id` <> 0
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Set the sql net earnings.
	$sql_net_earnings = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			SUM( `subtotal` - `discount` ) AS `amount`
		FROM
			%i
		WHERE
			`order_id` <> 0
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Set the sql orders count.
	$sql_orders_count = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			COUNT( * ) AS `total`
		FROM
			%i
		WHERE
			`order_id` <> 0
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Set the sql paid orders count.
	$sql_paid_orders_count = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaign_id`,
			COUNT( * ) AS `total`
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		GROUP BY
			`campaign_id`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Set the sql join.
	$sql_join = PsUpsellMaster_Database::prepare(
		"
		LEFT JOIN
			%i AS `coupons`
		ON
			`campaigns`.`id` = `coupons`.`campaign_id`
		LEFT JOIN
			( {$sql_products_count} ) AS `products_count`
		ON
			`campaigns`.`id` = `products_count`.`campaign_id`
		LEFT JOIN
			( {$sql_impression_events} ) AS `impression_events`
		ON
			`campaigns`.`id` = `impression_events`.`campaign_id`
		LEFT JOIN
			( {$sql_click_events} ) AS `click_events`
		ON
			`campaigns`.`id` = `click_events`.`campaign_id`
		LEFT JOIN
			( {$sql_cart_events} ) AS `cart_events`
		ON
			`campaigns`.`id` = `cart_events`.`campaign_id`
		LEFT JOIN
			( {$sql_discount} ) AS `discount`
		ON
			`campaigns`.`id` = `discount`.`campaign_id`
		LEFT JOIN
			( {$sql_net_earnings} ) AS `net_earnings`
		ON
			`campaigns`.`id` = `net_earnings`.`campaign_id`
		LEFT JOIN
			( {$sql_orders_count} ) AS `orders_count`
		ON
			`campaigns`.`id` = `orders_count`.`campaign_id`
		LEFT JOIN
			( {$sql_paid_orders_count} ) AS `paid_orders_count`
		ON
			`campaigns`.`id` = `paid_orders_count`.`campaign_id`
		",
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' )
	);

	// Set the sql query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where}";

	// Get the count filtered rows.
	$count_filtered_rows = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql orderby.
	$sql_orderby = array();

	// Check if the inputs order and orderby are not empty.
	if ( ! is_null( $input_order ) && ! is_null( $input_orderby ) ) {
		// Check the input orderby value.
		if ( 0 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`campaigns`.`id` {$input_order}";

			// Check the input orderby value.
		} elseif ( 1 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`campaigns`.`title` {$input_order}";

			// Check the input orderby value.
		} elseif ( 2 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`coupons`.`code` {$input_order}";

			// Check the input orderby value.
		} elseif ( 3 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "FIELD( `campaigns`.`status`, 'active', 'scheduled', 'inactive', 'expired' ) {$input_order}";

			// Check the input orderby value.
		} elseif ( 4 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`campaigns`.`priority` {$input_order}";

			// Check the input orderby value.
		} elseif ( 5 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`products_count`.`total` {$input_order}";

			// Check the input orderby value.
		} elseif ( 7 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`impression_events`.`quantity` {$input_order}";

			// Check the input orderby value.
		} elseif ( 8 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`click_events`.`quantity` {$input_order}";

			// Check the input orderby value.
		} elseif ( 9 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`cart_events`.`quantity` {$input_order}";

			// Check the input orderby value.
		} elseif ( 10 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`orders_count`.`total` {$input_order}";

			// Check the input orderby value.
		} elseif ( 11 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`discount`.`amount` {$input_order}";

			// Check the input orderby value.
		} elseif ( 12 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`net_earnings`.`amount` {$input_order}";

			// Check the input orderby value.
		} elseif ( 13 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`aov` {$input_order}";
		}
	}

	// Set the sql orderby.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Set the sql limit.
	$sql_limit = -1 !== $input_length ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $input_start, $input_length ) : '';

	// Set the sql query.
	$sql_query = ( "SELECT `campaigns`.`id` {$sql_from} {$sql_join} {$sql_where}" );

	// Get the campaign ids.
	$campaign_ids = PsUpsellMaster_Database::get_col( $sql_query );

	// Add -1 to the list so that it contains at least one id.
	array_push( $campaign_ids, -1 );

	// Set the sql query.
	$sql_query = (
		"
		SELECT
			`campaigns`.`id`,
			`campaigns`.`title`,
			`campaigns`.`status`,
			`campaigns`.`priority`,
			`campaigns`.`start_date`,
			`campaigns`.`end_date`,
			`coupons`.`code` AS `coupon_code`,
			`coupons`.`amount` AS `coupon_amount`,
			`coupons`.`type` AS `coupon_type`,
			`products_count`.`total` AS `products_count`,
			`impression_events`.`quantity` AS `impression_events`,
			`click_events`.`quantity` AS `click_events`,
			`cart_events`.`quantity` AS `cart_events`,
			`discount`.`amount` AS `discount`,
			`net_earnings`.`amount` AS `net_earnings`,
			`orders_count`.`total` AS `orders_count`,
			( `net_earnings`.`amount` / COALESCE( NULLIF( `paid_orders_count`.`total`, 0 ), 1 ) ) AS `aov`
		{$sql_from}
		{$sql_join}
		{$sql_where}
		{$sql_orderby}
		{$sql_limit}
		"
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query );

	// Get the locations.
	$locations = psupsellmaster_get_product_locations();

	// Get the statuses.
	$statuses = psupsellmaster_campaigns_get_statuses();

	// Loop through the rows.
	foreach ( $rows as $row ) :
		// Get the campaign id.
		$campaign_id = isset( $row->id ) ? filter_var( $row->id, FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Get the campaign status.
		$campaign_status = isset( $row->status ) ? $row->status : '';

		// Get the WordPress current date.
		$wp_current_date = new DateTime( 'now', psupsellmaster_get_timezone() );

		// Set the html row.
		$html_row = array();

		//
		// Column: # ID.
		//

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<div class="psupsellmaster-ignore-on-export" style="display: none;">
				<input class="psupsellmaster-hidden-campaign-id" type="hidden" value="<?php echo esc_attr( $campaign_id ); ?>" />
			</div>
			<input class="psupsellmaster-check-row" type="checkbox" />
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Campaign.
		//

		// Get the campaign title.
		$campaign_title = isset( $row->title ) ? stripslashes( $row->title ) : '';

		// Get the start date.
		$start_date = isset( $row->start_date ) ? $row->start_date : '';

		// Get the end date.
		$end_date = isset( $row->end_date ) ? $row->end_date : '';

		// Set the datetime start date.
		$datetime_start_date = null;

		// Check the start date.
		if ( ! empty( $start_date ) && '0000-00-00 00:00:00' !== $start_date ) {
			// Set the datetime start date.
			$datetime_start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $start_date );
		}

		// Set the formatted start date.
		$formatted_start_date = null;

		// Check the start date.
		if ( $datetime_start_date instanceof DateTime ) {
			// Set the timezone.
			$datetime_start_date->setTimezone( psupsellmaster_get_timezone() );

			// Set the formatted start date.
			$formatted_start_date = $datetime_start_date->format( 'Y/m/d' );
		}

		// Set the datetime end date.
		$datetime_end_date = null;

		// Check the end date.
		if ( ! empty( $end_date ) && '0000-00-00 00:00:00' !== $end_date ) {
			// Set the datetime end date.
			$datetime_end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $end_date );
		}

		// Set the formatted end date.
		$formatted_end_date = null;

		// Check the end date.
		if ( $datetime_end_date instanceof DateTime ) {
			// Set the timezone.
			$datetime_end_date->setTimezone( psupsellmaster_get_timezone() );

			// Set the formatted end date.
			$formatted_end_date = $datetime_end_date->format( 'Y/m/d' );
		}

		// Get the formatted datetime left.
		$formatted_datetime_left = psupsellmaster_get_formatted_campaign_datetime_left( $campaign_id );

		// Set the edit url.
		$edit_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign_id );

		// Set the view url.
		$view_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=view&campaign=' . $campaign_id );

		// Set the tag terms.
		$tag_terms = array();

		// Set the taxonomy.
		$taxonomy = 'psupsellmaster_product_tag';

		// Get the taxonomy terms.
		$taxonomy_terms = psupsellmaster_get_campaign_included_taxonomy_terms(
			$campaign_id,
			$taxonomy
		);

		// Loop through the taxonomy terms.
		foreach ( $taxonomy_terms as $taxonomy_term ) {
			// Get the term id.
			$term_id = filter_var( $taxonomy_term['term_id'], FILTER_VALIDATE_INT );

			// Check if the term id is empty.
			if ( empty( $term_id ) ) {
				continue;
			}

			// Get the term name.
			$term_name = get_term_field( 'name', $term_id, $taxonomy );

			// Check if the term name is empty.
			if ( empty( $term_name ) ) {
				continue;
			}

			// Add the term to the list.
			$tag_terms[ $term_id ] = $term_name;
		}

		// Set the tags url.
		$tags_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<div>
				<a class="psupsellmaster-link" href="<?php echo esc_url( $edit_url ); ?>" target="_blank"><strong><?php echo esc_html( $campaign_title ); ?></strong></a>
			</div>
			<?php if ( ! empty( $formatted_start_date ) || ! empty( $formatted_end_date ) ) : ?>
				<div class="psupsellmaster-ignore-on-export">
					<?php if ( $formatted_start_date === $formatted_end_date ) : ?>
						<span><?php echo esc_html( $formatted_start_date ); ?></span>
					<?php else : ?>
						<?php if ( empty( $formatted_start_date ) ) : ?>
							<span><?php esc_html_e( 'N/A', 'psupsellmaster' ); ?></span>
						<?php else : ?>
							<span><?php echo esc_html( $formatted_start_date ); ?></span>
						<?php endif; ?>
						<span>&ndash;</span>
						<?php if ( empty( $formatted_end_date ) ) : ?>
							<span><?php esc_html_e( 'N/A', 'psupsellmaster' ); ?></span>
						<?php else : ?>
							<span><?php echo esc_html( $formatted_end_date ); ?></span>
							<?php if ( ! empty( $formatted_datetime_left ) ) : ?>
								<br />
								<span class="psupsellmaster-datetime-left">
									<i><?php echo esc_html( $formatted_datetime_left ); ?></i>
									<i class="dashicons dashicons-update psupsellmaster-btn-refresh-datetime-left" title="<?php esc_attr_e( 'Refresh', 'psupsellmaster' ); ?>"></i>
								</span>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="psupsellmaster-row-actions psupsellmaster-ignore-on-export">
				<div class="psupsellmaster-row-actions-section">
					<span><?php echo esc_html( "#{$campaign_id}" ); ?></span>
					<span>&#124;</span>
					<a class="psupsellmaster-row-action" href="<?php echo esc_url( $view_url ); ?>" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
					<span>&#124;</span>
					<a class="psupsellmaster-row-action" href="<?php echo esc_url( $edit_url ); ?>" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
					<span>&#124;</span>
					<a class="psupsellmaster-row-action psupsellmaster-duplicate" href="#" title="<?php esc_attr_e( 'Duplicate', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Duplicate', 'psupsellmaster' ); ?></a>
				</div>
				<?php if ( in_array( $campaign_status, array( 'active', 'inactive' ), true ) ) : ?>
					<div class="psupsellmaster-row-actions-section">
						<?php if ( 'inactive' === $campaign_status ) : ?>
							<a class="psupsellmaster-row-action psupsellmaster-set-status psupsellmaster-activate" data-status="active" href="#" title="<?php esc_attr_e( 'Activate', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Activate', 'psupsellmaster' ); ?></a>
						<?php elseif ( 'active' === $campaign_status ) : ?>
							<a class="psupsellmaster-row-action psupsellmaster-set-status psupsellmaster-deactivate" data-status="inactive" href="#" title="<?php esc_attr_e( 'Deactivate', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Deactivate', 'psupsellmaster' ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( psupsellmaster_is_pro() ) : ?>
				<?php if ( ! empty( $tag_terms ) ) : ?>
					<div class="psupsellmaster-on-hover psupsellmaster-ignore-on-export">
						<br />
						<ul class="psupsellmaster-list">
							<?php foreach ( $tag_terms as $term_id => $term_name ) : ?>
								<?php $term_edit_url = get_edit_term_link( $term_id, $taxonomy ); ?>
								<?php $term_manage_url = add_query_arg( 'filters[taxonomies][' . $taxonomy . ']', $term_id, $tags_url ); ?>
								<li class="psupsellmaster-item">
									<a class="psupsellmaster-link" href="<?php echo esc_url( $term_manage_url ); ?>" target="_blank"><?php echo esc_html( $term_name ); ?></a>
									<div class="psupsellmaster-item-actions psupsellmaster-ignore-on-export">
										<?php if ( ! empty( $term_edit_url ) ) : ?>
											<a class="psupsellmaster-link" href="<?php echo esc_url( $term_edit_url ); ?>" target="_blank"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
											<span>&#124;</span>
										<?php endif; ?>
										<a class="psupsellmaster-link" href="<?php echo esc_url( $term_manage_url ); ?>" target="_blank"><?php esc_html_e( 'Manage', 'psupsellmaster' ); ?></a>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="psupsellmaster-row-details psupsellmaster-ignore-on-export">
				<a class="psupsellmaster-toggle-details" href="javascript:;" title="<?php esc_attr_e( 'Show/Hide details.', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Show/Hide details.', 'psupsellmaster' ); ?></a>
			</div>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Coupon.
		//

		// Get the coupon code.
		$coupon_code = isset( $row->coupon_code ) ? $row->coupon_code : '';

		// Get the coupon amount.
		$coupon_amount = isset( $row->coupon_amount ) ? filter_var( $row->coupon_amount, FILTER_VALIDATE_FLOAT ) : false;

		// Get the coupon type.
		$coupon_type = isset( $row->coupon_type ) ? $row->coupon_type : '';

		// Set the formatted amount.
		$formatted_amount = psupsellmaster_format_decimal_amount( $coupon_amount );

		// Check the coupon type.
		if ( 'discount_percent' === $coupon_type ) {
			// Set the formatted amount.
			$formatted_amount = psupsellmaster_format_percentage_amount( $formatted_amount );

			// Check the coupon type.
		} elseif ( 'discount_fixed' === $coupon_type ) {
			// Set the formatted amount.
			$formatted_amount = psupsellmaster_format_currency_amount( $formatted_amount );
		}

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<div>
				<?php if ( empty( $coupon_code ) ) : ?>
					<span>&#8212;</span>
				<?php else : ?>
					<span><?php echo esc_html( $coupon_code ); ?></span>
				<?php endif; ?>
			</div>
			<div class="psupsellmaster-ignore-on-export">
				<span><i><?php echo esc_html( $formatted_amount ); ?></i></span>
			</div>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Status.
		//

		// Set the status key.
		$status_key = $campaign_status;

		// Set the status label.
		$status_label = __( 'Unknown', 'psupsellmaster' );

		// Check if the campaign status does exist.
		if ( $statuses[ $campaign_status ] ) {
			// Set the status label.
			$status_label = $statuses[ $campaign_status ];
		}

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<span class="psupsellmaster-status psupsellmaster-status-<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_label ); ?></span>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Priority.
		//

		// Get the campaign priority.
		$campaign_priority = isset( $row->priority ) ? filter_var( $row->priority, FILTER_VALIDATE_INT ) : false;
		$campaign_priority = false !== $campaign_priority ? $campaign_priority : 0;

		// Set the html column.
		$html_column = psupsellmaster_format_integer_amount( $campaign_priority );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Check if this is the pro version.
		if ( psupsellmaster_is_pro() ) {
			//
			// Column: # Products.
			//

			// Set the products count.
			$products_count = isset( $row->products_count ) ? filter_var( $row->products_count, FILTER_VALIDATE_INT ) : false;
			$products_count = false !== $products_count ? $products_count : 0;

			// Set the formatted products count.
			$formatted_products_count = psupsellmaster_format_integer_amount( $products_count );

			// Start the buffer.
			ob_start();

			?>
			<div>
				<span><?php echo esc_html( $formatted_products_count ); ?></span>
			</div>
			<?php

			// Set the html column.
			$html_column = ob_get_clean();

			// Add the column to the row.
			array_push( $html_row, $html_column );
		}

		//
		// Column: Locations.
		//

		// Set the location labels.
		$location_labels = array();

		// Get the locations.
		$locations = psupsellmaster_get_campaign_locations( $campaign_id );
		$locations = is_array( $locations ) ? $locations : array();

		// Check if the locations is empty.
		if ( empty( $locations ) ) {
			// Set the location labels.
			$location_labels = array( __( 'All', 'psupsellmaster' ) );

			// Otherwise...
		} else {
			// Loop through the locations.
			foreach ( $locations as $location ) {
				// Get the location label.
				$location_label = psupsellmaster_get_product_location_label( $location );

				// Requested to remove Page and Popup suffix.
				$location_label = str_replace( ' Page', '', $location_label );
				$location_label = str_replace( ' Popup', '', $location_label );

				// Check if the location label is empty.
				if ( empty( $location_label ) ) {
					continue;
				}

				// Add the location label to the location labels.
				array_push( $location_labels, $location_label );
			}
		}

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<?php if ( empty( $location_labels ) ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<ul class="psupsellmaster-list">
					<?php foreach ( $location_labels as $location_label ) : ?>
						<li><?php echo esc_html( $location_label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Impressions.
		//

		// Set the impression events.
		$impression_events = isset( $row->impression_events ) ? filter_var( $row->impression_events, FILTER_VALIDATE_INT ) : false;
		$impression_events = false !== $impression_events ? $impression_events : 0;

		// Set the html column.
		$html_column = psupsellmaster_format_integer_amount( $impression_events );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Clicks.
		//

		// Set the click events.
		$click_events = isset( $row->click_events ) ? filter_var( $row->click_events, FILTER_VALIDATE_INT ) : false;
		$click_events = false !== $click_events ? $click_events : 0;

		// Set the formatted.
		$formatted = psupsellmaster_format_integer_amount( $click_events );

		// Set the percentage.
		$percentage = psupsellmaster_safe_divide(
			$click_events,
			$impression_events
		);

		// Set the percentage.
		$percentage *= 100;

		// Set the percentage.
		$formatted_percentage = psupsellmaster_format_decimal_amount( $percentage );

		// Set the formatted percentage.
		$formatted_percentage = psupsellmaster_format_percentage_amount( $formatted_percentage );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<span><?php echo esc_html( $formatted ); ?></span>
			<br />
			<span><i><?php echo esc_html( $formatted_percentage ); ?></i></span>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Add to Cart.
		//

		// Set the cart events.
		$cart_events = isset( $row->cart_events ) ? filter_var( $row->cart_events, FILTER_VALIDATE_INT ) : false;
		$cart_events = false !== $cart_events ? $cart_events : 0;

		// Set the formatted.
		$formatted = psupsellmaster_format_integer_amount( $cart_events );

		// Set the percentage.
		$percentage = psupsellmaster_safe_divide(
			$cart_events,
			$click_events
		);

		// Set the percentage.
		$percentage *= 100;

		// Set the percentage.
		$formatted_percentage = psupsellmaster_format_decimal_amount( $percentage );

		// Set the formatted percentage.
		$formatted_percentage = psupsellmaster_format_percentage_amount( $formatted_percentage );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<span><?php echo esc_html( $formatted ); ?></span>
			<br />
			<span><i><?php echo esc_html( $formatted_percentage ); ?></i></span>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Orders.
		//

		// Set the orders count.
		$orders_count = isset( $row->orders_count ) ? filter_var( $row->orders_count, FILTER_VALIDATE_INT ) : false;
		$orders_count = false !== $orders_count ? $orders_count : 0;

		// Set the formatted.
		$formatted = psupsellmaster_format_integer_amount( $orders_count );

		// Set the percentage.
		$percentage = psupsellmaster_safe_divide(
			$orders_count,
			$cart_events
		);

		// Set the percentage.
		$percentage *= 100;

		// Set the percentage.
		$formatted_percentage = psupsellmaster_format_decimal_amount( $percentage );

		// Set the formatted percentage.
		$formatted_percentage = psupsellmaster_format_percentage_amount( $formatted_percentage );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<span><?php echo esc_html( $formatted ); ?></span>
			<br />
			<span><i><?php echo esc_html( $formatted_percentage ); ?></i></span>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Discounts.
		//

		// Set the html column.
		$html_column = isset( $row->discount ) ? filter_var( $row->discount, FILTER_VALIDATE_FLOAT ) : false;
		$html_column = false !== $html_column ? $html_column : 0;
		$html_column = psupsellmaster_format_currency_amount( $html_column );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Net Earnings.
		//

		// Set the net earnings.
		$net_earnings = isset( $row->net_earnings ) ? filter_var( $row->net_earnings, FILTER_VALIDATE_FLOAT ) : false;
		$net_earnings = false !== $net_earnings ? $net_earnings : 0;

		// Set the formatted net earnings.
		$formatted_net_earnings = psupsellmaster_format_currency_amount( $net_earnings );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<a class="psupsellmaster-link" href="<?php echo esc_url( $view_url ); ?>" target="_blank"><?php echo esc_html( $formatted_net_earnings ); ?></a>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Check if this is the pro version.
		if ( psupsellmaster_is_pro() ) {
			//
			// Column: $ AOV.
			//

			// Set the html column.
			$html_column = isset( $row->aov ) ? filter_var( $row->aov, FILTER_VALIDATE_FLOAT ) : false;
			$html_column = false !== $html_column ? $html_column : 0;
			$html_column = psupsellmaster_format_currency_amount( $html_column );

			// Add the column to the row.
			array_push( $html_row, $html_column );
		}

		// Add the row to the rows output.
		array_push( $output['datatables']['main']['data'], $html_row );
	endforeach;

	// Set the output: datatable total.
	$output['datatables']['main']['total'] = $count_all_rows;

	// Set the output: datatable filtered.
	$output['datatables']['main']['filtered'] = $count_filtered_rows;

	// Set the start date.
	$start_date = false;

	// Set the end date.
	$end_date = false;

	// Check if the filter is set.
	if ( isset( $input_filters['campaign_date'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['campaign_date']['start'] ) ) {
			// Set the start date.
			$start_date = DateTime::createFromFormat( 'Y/m/d', $input_filters['campaign_date']['start'] );
			$start_date = false !== $start_date ? $start_date->format( 'Y-m-d' ) : null;
		}

		// Check if the filter is set.
		if ( isset( $input_filters['campaign_date']['end'] ) ) {
			// Set the end date.
			$end_date = DateTime::createFromFormat( 'Y/m/d', $input_filters['campaign_date']['end'] );
			$end_date = false !== $end_date ? $end_date->format( 'Y-m-d' ) : null;
		}
	}

	// Check if the start date is empty and the end date is not empty.
	if ( empty( $start_date ) && ! empty( $end_date ) ) {
		// Set the start date.
		$start_date = gmdate( 'Y-m-d', strtotime( 'first day of this month', strtotime( $end_date ) ) );

		// Otherwise, check if the start date is empty.
	} elseif ( empty( $start_date ) ) {
		// Set the start date.
		$start_date = gmdate( 'Y-m-d', strtotime( 'first day of this month' ) );
	}

	// Check if the end date is empty and the start date is not empty.
	if ( empty( $end_date ) && ! empty( $start_date ) ) {
		// Set the end date.
		$end_date = gmdate( 'Y-m-d', strtotime( 'last day of this month', strtotime( $start_date ) ) );

		// Otherwise, check if the end date is empty.
	} elseif ( empty( $end_date ) ) {
		// Set the end date.
		$end_date = gmdate( 'Y-m-d' );
	}

	// Set the start timestamp.
	$start_timestamp = strtotime( $start_date );

	// Set the end timestamp.
	$end_timestamp = strtotime( $end_date );

	// Loop through the timestamp range.
	for ( $i = $start_timestamp; $i <= $end_timestamp; $i += 86400 ) {
		// Set the display timestamp.
		$display_timestamp = gmdate( 'j-M-Y', $i );

		// Set the mysql date.
		$mysql_date = gmdate( 'Y-m-d H:i:s', $i );

		// Set the start date.
		$start_date = $mysql_date;

		// Set the end date.
		$end_date = str_replace( '00:00:00', '23:59:59', $mysql_date );

		// Set the args.
		$args = array(
			'campaign_id' => $campaign_ids,
			'start_date'  => $start_date,
			'end_date'    => $end_date,
		);

		// Get the earnings.
		$earnings = psupsellmaster_get_campaign_net_earnings( $args );

		// Get the carts count.
		$carts_count = psupsellmaster_get_campaign_carts_count( $args );

		// Get the orders count.
		$orders_count = psupsellmaster_get_campaign_orders_count( $args );

		// Set the chart item.
		$chart_item = array(
			'earnings' => $earnings,
			'carts'    => $carts_count,
			'orders'   => $orders_count,
			'label'    => $display_timestamp,
		);

		// Add a chart item to the chart.
		array_push( $charts['main']['items'], $chart_item );

		// Set the chart legends.
		$chart_legends = array(
			'earnings' => __( 'Net Earnings', 'psupsellmaster' ),
			'carts'    => __( 'Carts', 'psupsellmaster' ),
			'orders'   => __( 'Orders', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['main']['legends'], $chart_legends );
	}

	// Set the kpis args.
	$kpis_args = array( 'campaign_id' => $campaign_ids );

	// Get the kpis.
	$kpis = psupsellmaster_get_campaigns_kpis( $kpis_args );

	// Start the output buffer.
	ob_start();

	?>
	<?php foreach ( $kpis as $kpi ) : ?>
		<div class="psupsellmaster-kpi">
			<div class="psupsellmaster-kpi-value"><?php echo esc_html( $kpi['formatted'] ); ?></div>
			<div class="psupsellmaster-kpi-label"><?php echo esc_html( $kpi['label'] ); ?></div>
		</div>
	<?php endforeach; ?>
	<?php

	// Set the kpis.
	$kpis = ob_get_clean();

	// Set the output: charts.
	$output['charts'] = $charts;

	// Set the output: kpis.
	$output['kpis'] = $kpis;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_campaigns_list_table', 'psupsellmaster_ajax_get_campaigns_list_table' );

/**
 * Get the campaign carts through ajax.
 */
function psupsellmaster_ajax_get_campaign_carts() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Get the input campaign id.
	$input_campaign_id = isset( $_POST['campaign_id'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['campaign_id'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_campaign_id = false !== $input_campaign_id ? $input_campaign_id : 0;

	// Get the input draw.
	$input_draw = isset( $_POST['draw'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['draw'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_draw = false !== $input_draw ? $input_draw : 0;

	// Get the input start.
	$input_start = isset( $_POST['start'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['start'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_start = false !== $input_start ? $input_start : 0;

	// Get the input length.
	$input_length = isset( $_POST['length'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['length'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_length = false !== $input_length ? $input_length : 0;

	// Get the input filters.
	$input_filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();

	// Set the input order.
	$input_order = null;

	// Set the input orderby.
	$input_orderby = null;

	// Set the default orderby.
	$default_orderby = 13;

	// Set the default order.
	$default_order = 'DESC';

	// Check if the input order inputs does exist.
	if ( isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) && isset( $_POST['order'][0]['dir'] ) ) {
		// Set the input order.
		$input_orderby = filter_var( sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) ), FILTER_VALIDATE_INT );
		$input_orderby = false !== $input_orderby ? $input_orderby : $default_orderby;

		// Set the input order.
		$input_order = strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) );
		$input_order = in_array( $input_order, array( 'ASC', 'DESC' ), true ) ? $input_order : $default_order;
	}

	// Set the charts.
	$charts = array(
		'main' => array(
			'items'   => array(),
			'legends' => array(),
		),
	);

	// Set the datatable.
	$datatable = array(
		'data'     => array(),
		'draw'     => ( $input_draw + 1 ),
		'filtered' => 0,
		'total'    => 0,
	);

	// Set the datatables.
	$datatables = array(
		'main' => $datatable,
	);

	// Set the kpis.
	$kpis = array();

	// Set the output.
	$output = array(
		'charts'     => $charts,
		'datatables' => $datatables,
		'kpis'       => $kpis,
		'success'    => false,
	);

	// Set the campaign id.
	$campaign_id = ! empty( $input_campaign_id ) ? $input_campaign_id : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the sql select.
	$sql_select = ( 'SELECT COUNT( * ) AS `rows`' );

	// Set the sql from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `carts`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' ) );

	// Set the sql where.
	$sql_where = PsUpsellMaster_Database::prepare( 'WHERE `carts`.`campaign_id` = %d', $campaign_id );

	// Set the sql query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where}";

	// Get the count all rows.
	$count_all_rows = PsUpsellMaster_Database::get_var( $sql_query );

	// Check if the filter is set.
	if ( isset( $input_filters['carts'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['carts']['date'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['date']['start'] ) ) {
				// Set the filter.
				$filter = DateTime::createFromFormat( 'Y/m/d', $input_filters['carts']['date']['start'] );
				$filter = false !== $filter ? $filter->format( 'Y-m-d' ) : null;

				// Check if the filter is not empty.
				if ( ! empty( $filter ) ) {
					// Set the sql where.
					$sql_where .= PsUpsellMaster_Database::prepare(
						' AND `carts`.`last_modified` >= %s',
						$filter
					);
				}
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['date']['end'] ) ) {
				// Set the filter.
				$filter = DateTime::createFromFormat( 'Y/m/d', $input_filters['carts']['date']['end'] );
				$filter = false !== $filter ? $filter->format( 'Y-m-d' ) : null;

				// Check if the filter is not empty.
				if ( ! empty( $filter ) ) {
					// Set the sql where.
					$sql_where .= PsUpsellMaster_Database::prepare(
						' AND `carts`.`last_modified` <= %s',
						$filter
					);
				}
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['gross_earnings'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['gross_earnings']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( `carts`.`subtotal` + `carts`.`tax` ) >= %f',
					$input_filters['carts']['gross_earnings']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['gross_earnings']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( `carts`.`subtotal` + `carts`.`tax` ) <= %f',
					$input_filters['carts']['gross_earnings']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['tax'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['tax']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`tax` >= %f',
					$input_filters['carts']['tax']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['tax']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`tax` <= %f',
					$input_filters['carts']['tax']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['discount'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['discount']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`discount` >= %f',
					$input_filters['carts']['discount']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['discount']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`discount` <= %f',
					$input_filters['carts']['discount']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['net_earnings'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['net_earnings']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND (  `carts`.`subtotal` - `carts`.`discount` ) >= %f',
					$input_filters['carts']['net_earnings']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['net_earnings']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND (  `carts`.`subtotal` - `carts`.`discount` ) <= %f',
					$input_filters['carts']['net_earnings']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['quantity'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['quantity']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`quantity` >= %d',
					$input_filters['carts']['quantity']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['quantity']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND `carts`.`quantity` <= %d',
					$input_filters['carts']['quantity']['max']
				);
			}
		}

		// Check if the filter is set.
		if ( isset( $input_filters['carts']['aov'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['aov']['min'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( ( `carts`.`subtotal` - `carts`.`discount` ) / COALESCE( NULLIF( `carts`.`quantity`, 0 ), 1 ) ) >= %f',
					$input_filters['carts']['aov']['min']
				);
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['aov']['max'] ) ) {
				// Set the sql where.
				$sql_where .= PsUpsellMaster_Database::prepare(
					' AND ( ( `carts`.`subtotal` - `carts`.`discount` ) / COALESCE( NULLIF( `carts`.`quantity`, 0 ), 1 ) ) <= %f',
					$input_filters['carts']['aov']['max']
				);
			}
		}
	}

	// Set the sql query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where}";

	// Get the count filtered rows.
	$count_filtered_rows = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql orderby.
	$sql_orderby = array();

	// Check if the inputs order and orderby are not empty.
	if ( ! is_null( $input_order ) && ! is_null( $input_orderby ) ) {
		// Check the input orderby value.
		if ( 0 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`carts`.`last_modified` {$input_order}";

			// Check the input orderby value.
		} elseif ( 1 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = '';

			// Check the input orderby value.
		} elseif ( 2 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`carts`.`order_id` {$input_order}";

			// Check the input orderby value.
		} elseif ( 3 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`carts`.`order_id` {$input_order}";

			// Check the input orderby value.
		} elseif ( 4 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`carts`.`subtotal` {$input_order}";

			// Check the input orderby value.
		} elseif ( 5 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`gross_earnings` {$input_order}";

			// Check the input orderby value.
		} elseif ( 6 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`tax` {$input_order}";

			// Check the input orderby value.
		} elseif ( 7 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`discount` {$input_order}";

			// Check the input orderby value.
		} elseif ( 8 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`net_earnings` {$input_order}";

			// Check the input orderby value.
		} elseif ( 9 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`carts`.`quantity` {$input_order}";

			// Check the input orderby value.
		} elseif ( 10 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`aov` {$input_order}";
		}
	}

	// Set the sql orderby.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Set the sql limit.
	$sql_limit = -1 !== $input_length ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $input_start, $input_length ) : '';

	// Set the sql query.
	$sql_query = ( "SELECT `carts`.`id` {$sql_from} {$sql_where}" );

	// Get the row ids.
	$row_ids = PsUpsellMaster_Database::get_col( $sql_query );

	// Add -1 to the list so that it contains at least one id.
	array_push( $row_ids, -1 );

	// Set the sql query.
	$sql_query = (
		"
		SELECT
			`carts`.`cart_key`,
			`carts`.`last_modified`,
			`carts`.`order_id`,
			`carts`.`quantity`,
			`carts`.`subtotal`,
			(
				CASE
					WHEN order_id = 0 THEN 0
					ELSE `carts`.`discount`
				END
			) AS `discount`,
			(
				CASE
					WHEN order_id = 0 THEN 0
					ELSE `carts`.`tax`
				END
			) AS `tax`,
			(
				CASE
					WHEN order_id = 0 THEN 0
					ELSE ( `carts`.`subtotal` + `carts`.`tax` )
				END
			) AS `gross_earnings`,
			(
				CASE
					WHEN order_id = 0 THEN 0
					ELSE ( `carts`.`subtotal` - `carts`.`discount` )
				END
			) AS `net_earnings`,
			(
				CASE
					WHEN order_id = 0 THEN 0
					ELSE ( ( `carts`.`subtotal` - `carts`.`discount` ) / COALESCE( NULLIF( `carts`.`quantity`, 0 ), 1 ) )
				END
			) AS `aov`
		{$sql_from}
		{$sql_where}
		{$sql_orderby}
		{$sql_limit}
		"
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Get the order id.
		$order_id = isset( $row->order_id ) ? filter_var( $row->order_id, FILTER_VALIDATE_INT ) : false;
		$order_id = false !== $order_id ? $order_id : 0;

		// Get the quantity.
		$quantity = isset( $row->quantity ) ? filter_var( $row->quantity, FILTER_VALIDATE_INT ) : false;
		$quantity = false !== $quantity ? $quantity : 0;

		// Get the subtotal.
		$subtotal = isset( $row->subtotal ) ? filter_var( $row->subtotal, FILTER_VALIDATE_FLOAT ) : false;
		$subtotal = false !== $subtotal ? $subtotal : 0;

		// Get the tax.
		$tax = isset( $row->tax ) ? filter_var( $row->tax, FILTER_VALIDATE_FLOAT ) : false;
		$tax = false !== $tax ? $tax : 0;

		// Get the discount.
		$discount = isset( $row->discount ) ? filter_var( $row->discount, FILTER_VALIDATE_FLOAT ) : false;
		$discount = false !== $discount ? $discount : 0;

		// Get the cart value.
		$cart_value = isset( $row->cart_value ) ? filter_var( $row->cart_value, FILTER_VALIDATE_FLOAT ) : false;
		$cart_value = false !== $cart_value ? $cart_value : 0;

		// Get the gross earnings.
		$gross_earnings = isset( $row->gross_earnings ) ? filter_var( $row->gross_earnings, FILTER_VALIDATE_FLOAT ) : false;
		$gross_earnings = false !== $gross_earnings ? $gross_earnings : 0;

		// Get the net earnings.
		$net_earnings = isset( $row->net_earnings ) ? filter_var( $row->net_earnings, FILTER_VALIDATE_FLOAT ) : false;
		$net_earnings = false !== $net_earnings ? $net_earnings : 0;

		// Get the aov.
		$aov = isset( $row->aov ) ? filter_var( $row->aov, FILTER_VALIDATE_FLOAT ) : false;
		$aov = false !== $aov ? $aov : 0;

		// Set the html row.
		$html_row = array();

		//
		// Column: Date.
		//

		// Get the last modified.
		$last_modified = isset( $row->last_modified ) ? $row->last_modified : false;

		// Set the html column.
		$html_column = gmdate( 'Y/m/d', strtotime( $last_modified ) );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Customer.
		//

		// Set the user id.
		$user_id = false;

		// Check if the order id is not empty.
		if ( ! empty( $order_id ) ) {
			// Get the user id.
			$user_id = psupsellmaster_get_user_id_by_order_id( $order_id );
		}

		// Set the customer url.
		$customer_url = '#';

		// Check if the user id is not empty.
		if ( ! empty( $user_id ) ) {
			// Set the customer url.
			$customer_url = psupsellmaster_get_admin_customer_url_by_user_id( $user_id );
		}

		// Get the customer name.
		$customer_name = psupsellmaster_get_customer_name_by_order_id( $order_id );

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<?php if ( empty( $customer_name ) ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<a class="psupsellmaster-link" href="<?php echo esc_url( $customer_url ); ?>" target="_blank"><?php echo esc_html( $customer_name ); ?></a>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Cart Type.
		//

		// Start the output buffer.
		ob_start();

		?>
		<div>
			<?php if ( empty( $order_id ) ) : ?>
				<span><?php esc_html_e( 'Abandoned', 'psupsellmaster' ); ?></span>
			<?php else : ?>
				<span><?php esc_html_e( 'Order', 'psupsellmaster' ); ?></span>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Order ID.
		//

		// Start the output buffer.
		ob_start();

		// Get the order url.
		$order_url = psupsellmaster_get_admin_order_url_by_order_id( $order_id );
		$order_url = ! empty( $order_url ) ? $order_url : '#';

		?>
		<div>
			<?php if ( empty( $order_id ) ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<a class="psupsellmaster-link" href="<?php echo esc_url( $order_url ); ?>" target="_blank"><?php echo esc_html( psupsellmaster_format_integer_amount( $order_id ) ); ?></a>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Cart Value.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $subtotal );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Gross Earnings.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $gross_earnings );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Taxes.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $tax );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Discounts.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $discount );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ Net Earnings.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $net_earnings );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: # Products.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_integer_amount( $quantity );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: $ AOV.
		//

		// Set the html column.
		$html_column = psupsellmaster_format_currency_amount( $aov );

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Add the row to the rows output.
		array_push( $output['datatables']['main']['data'], $html_row );
	}

	// Set the output: datatable total.
	$output['datatables']['main']['total'] = $count_all_rows;

	// Set the output: datatable filtered.
	$output['datatables']['main']['filtered'] = $count_filtered_rows;

	// Get the campaign data.
	$campaign_data = psupsellmaster_get_campaign( $campaign_id );

	// Get the campaign start date.
	$campaign_start_date = isset( $campaign_data['start_date'] ) ? stripslashes( $campaign_data['start_date'] ) : '';

	// Get the campaign end date.
	$campaign_end_date = isset( $campaign_data['end_date'] ) ? stripslashes( $campaign_data['end_date'] ) : '';

	// Set the sanitized start date.
	$sanitized_start_date = false;

	// Set the sanitized end date.
	$sanitized_end_date = false;

	// Check if the filter is set.
	if ( isset( $input_filters['carts'] ) ) {
		// Check if the filter is set.
		if ( isset( $input_filters['carts']['date'] ) ) {
			// Check if the filter is set.
			if ( isset( $input_filters['carts']['date']['start'] ) ) {
				// Set the start date.
				$sanitized_start_date = DateTime::createFromFormat( 'Y/m/d', $input_filters['carts']['date']['start'] );
				$sanitized_start_date = false !== $sanitized_start_date ? $sanitized_start_date->format( 'Y-m-d' ) : null;
			}

			// Check if the filter is set.
			if ( isset( $input_filters['carts']['date']['end'] ) ) {
				// Set the end date.
				$sanitized_end_date = DateTime::createFromFormat( 'Y/m/d', $input_filters['carts']['date']['end'] );
				$sanitized_end_date = false !== $sanitized_end_date ? $sanitized_end_date->format( 'Y-m-d' ) : null;
			}
		}
	}

	// Check if the start date is empty and the campaign start date is not empty.
	if ( empty( $sanitized_start_date ) && ! empty( $campaign_start_date ) ) {
		// Set the start date.
		$sanitized_start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $campaign_start_date, ( new DateTimeZone( 'UTC' ) ) );
		$sanitized_start_date = false !== $sanitized_start_date ? $sanitized_start_date->format( 'Y-m-d' ) : null;
	}

	// Check if the end date is empty and the campaign end date is not empty.
	if ( empty( $sanitized_end_date ) && ! empty( $campaign_end_date ) ) {
		// Set the end date.
		$sanitized_end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $campaign_end_date, ( new DateTimeZone( 'UTC' ) ) );
		$sanitized_end_date = false !== $sanitized_end_date ? $sanitized_end_date->format( 'Y-m-d' ) : null;
	}

	// Check if the start date is empty and the end date is not empty.
	if ( empty( $sanitized_start_date ) && ! empty( $sanitized_end_date ) ) {
		// Set the start date.
		$sanitized_start_date = gmdate( 'Y-m-d', strtotime( 'first day of this month', strtotime( $sanitized_end_date ) ) );

		// Otherwise, check if the start date is empty.
	} elseif ( empty( $sanitized_start_date ) ) {
		// Set the start date.
		$sanitized_start_date = gmdate( 'Y-m-d', strtotime( 'first day of this month' ) );
	}

	// Check if the end date is empty and the start date is not empty.
	if ( empty( $sanitized_end_date ) && ! empty( $sanitized_start_date ) ) {
		// Set the end date.
		$sanitized_end_date = gmdate( 'Y-m-d', strtotime( 'last day of this month', strtotime( $sanitized_start_date ) ) );

		// Otherwise, check if the end date is empty.
	} elseif ( empty( $sanitized_end_date ) ) {
		// Set the end date.
		$sanitized_end_date = gmdate( 'Y-m-d' );
	}

	// Set the start timestamp.
	$start_timestamp = strtotime( $sanitized_start_date );

	// Set the end timestamp.
	$end_timestamp = strtotime( $sanitized_end_date );

	// Loop through the timestamp range.
	for ( $i = $start_timestamp; $i <= $end_timestamp; $i += 86400 ) {
		// Set the display timestamp.
		$display_timestamp = gmdate( 'j-M-Y', $i );

		// Set the mysql date.
		$mysql_date = gmdate( 'Y-m-d H:i:s', $i );

		// Set the start date.
		$start_date = $mysql_date;

		// Set the end date.
		$end_date = str_replace( '00:00:00', '23:59:59', $mysql_date );

		// Set the args.
		$args = array(
			'campaign_id' => $campaign_id,
			'cart_id'     => $row_ids,
			'start_date'  => $start_date,
			'end_date'    => $end_date,
		);

		// Get the earnings.
		$earnings = psupsellmaster_get_campaign_net_earnings( $args );

		// Get the carts count.
		$carts_count = psupsellmaster_get_campaign_carts_count( $args );

		// Get the orders count.
		$orders_count = psupsellmaster_get_campaign_orders_count( $args );

		// Set the chart item.
		$chart_item = array(
			'earnings' => $earnings,
			'carts'    => $carts_count,
			'orders'   => $orders_count,
			'label'    => $display_timestamp,
		);

		// Add a chart item to the chart.
		array_push( $charts['main']['items'], $chart_item );

		// Set the chart legends.
		$chart_legends = array(
			'earnings' => __( 'Net Earnings', 'psupsellmaster' ),
			'carts'    => __( 'Carts', 'psupsellmaster' ),
			'orders'   => __( 'Orders', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['main']['legends'], $chart_legends );
	}

	// Set the kpis args.
	$kpis_args = array(
		'campaign_id' => $campaign_id,
		'cart_id'     => $row_ids,
		'start_date'  => $sanitized_start_date,
		'end_date'    => $sanitized_end_date,
	);

	// Get the kpis.
	$kpis = psupsellmaster_get_campaign_kpis( $kpis_args );

	// Start the output buffer.
	ob_start();

	?>
	<?php foreach ( $kpis as $kpi ) : ?>
		<div class="psupsellmaster-kpi">
			<div class="psupsellmaster-kpi-value"><?php echo esc_html( $kpi['formatted'] ); ?></div>
			<div class="psupsellmaster-kpi-label"><?php echo esc_html( $kpi['label'] ); ?></div>
		</div>
	<?php endforeach; ?>
	<?php

	// Set the kpis.
	$kpis = ob_get_clean();

	// Set the output: charts.
	$output['charts'] = $charts;

	// Set the output: kpis.
	$output['kpis'] = $kpis;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_campaign_carts', 'psupsellmaster_ajax_get_campaign_carts' );

/**
 * Get the campaign eligible products through ajax.
 */
function psupsellmaster_ajax_get_campaign_eligible_products() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Get the input campaign id.
	$input_campaign_id = isset( $_POST['campaign_id'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['campaign_id'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_campaign_id = false !== $input_campaign_id ? $input_campaign_id : 0;

	// Set the input search.
	$input_search = isset( $_POST['search'] ) && isset( $_POST['search']['value'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['search']['value'] ) ) ) : '';

	// Get the input draw.
	$input_draw = isset( $_POST['draw'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['draw'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_draw = false !== $input_draw ? $input_draw : 0;

	// Get the input start.
	$input_start = isset( $_POST['start'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['start'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_start = false !== $input_start ? $input_start : 0;

	// Get the input length.
	$input_length = isset( $_POST['length'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['length'] ) ), FILTER_VALIDATE_INT ) : false;
	$input_length = false !== $input_length ? $input_length : 0;

	// Get the input source.
	$input_source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : false;

	// Get the input options.
	$input_options = isset( $_POST['options'] ) && is_array( $_POST['options'] ) ? map_deep( wp_unslash( $_POST['options'] ), 'sanitize_text_field' ) : array();

	// Set the input order.
	$input_order = null;

	// Set the input orderby.
	$input_orderby = null;

	// Set the default orderby.
	$default_orderby = 0;

	// Set the default order.
	$default_order = 'DESC';

	// Check if the input order inputs does exist.
	if ( isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) && isset( $_POST['order'][0]['dir'] ) ) {
		// Set the input order.
		$input_orderby = filter_var( sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) ), FILTER_VALIDATE_INT );
		$input_orderby = false !== $input_orderby ? $input_orderby : $default_orderby;

		// Set the input order.
		$input_order = strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) );
		$input_order = in_array( $input_order, array( 'ASC', 'DESC' ), true ) ? $input_order : $default_order;
	}

	// Set the datatable.
	$datatable = array(
		'data'     => array(),
		'draw'     => ( $input_draw + 1 ),
		'filtered' => 0,
		'total'    => 0,
	);

	// Set the datatables.
	$datatables = array(
		'main' => $datatable,
	);

	// Set the output.
	$output = array(
		'datatables' => $datatables,
		'success'    => false,
	);

	// Check if the input source is not valid.
	if ( ! in_array( $input_source, array( 'preview', 'stored' ), true ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the campaign id.
	$campaign_id = ! empty( $input_campaign_id ) ? $input_campaign_id : false;

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Set the sql select.
	$sql_select = ( 'SELECT COUNT( * ) AS `rows`' );

	// Set the sql from.
	$sql_from = PsUpsellMaster_Database::prepare(
		'FROM %i AS `products`',
		PsUpsellMaster_Database::get_table_name( 'posts' )
	);

	// Set the sql join.
	$sql_join = PsUpsellMaster_Database::prepare(
		'
		LEFT JOIN
			%i AS `authors`
		ON
			`authors`.`id` = `products`.`post_author`
		',
		PsUpsellMaster_Database::get_table_name( 'users' )
	);

	// Set the sql where.
	$sql_where = array();

	// Add a condition to the sql where.
	array_push(
		$sql_where,
		PsUpsellMaster_Database::prepare( 'AND `products`.`post_type` = %s', $product_post_type )
	);

	// Add a condition to the sql where.
	array_push(
		$sql_where,
		PsUpsellMaster_Database::prepare( 'AND `products`.`post_status` = %s', 'publish' )
	);

	// Check the input search.
	if ( ! empty( $input_search ) ) {
		// Set the sql search.
		$sql_search = PsUpsellMaster_Database::prepare(
			'
			AND (
				`products`.`post_title` LIKE %s
				OR
				`authors`.`display_name` LIKE %s
			)
			',
			"%{$input_search}%",
			"%{$input_search}%"
		);

		// Add a condition to the sql where.
		array_push( $sql_where, $sql_search );
	}

	// Check the input source.
	if ( 'preview' === $input_source ) {
		// Get the products flag.
		$products_flag = isset( $input_options['products_flag'] ) ? $input_options['products_flag'] : false;
		$products_flag = in_array( $products_flag, array( 'all', 'selected' ), true ) ? $products_flag : 'all';

		// Check the products flag.
		if ( 'all' !== $input_options['products_flag'] ) {
			// Set the args.
			$args = array(
				'options' => $input_options,
			);

			// Get the eligible products.
			$eligible_products = psupsellmaster_get_products_by_selectors( $args );

			// Check if the eligible products is empty.
			if ( empty( $eligible_products ) ) {
				// Set the eligible products.
				$eligible_products = array( -1 );
			}

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $eligible_products ), '%d' ) );

			// Set the sql products.
			$sql_products = PsUpsellMaster_Database::prepare( "AND `products`.`ID` IN ( {$placeholders} )", $eligible_products );

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_products );
		}

		// Otherwise...
	} else {
		// Set the sql meta.
		$sql_meta = PsUpsellMaster_Database::prepare(
			'
			SELECT
				1
			FROM
				%i AS `campaignmeta`
			WHERE
				`campaignmeta`.`psupsellmaster_campaign_id` = %d
			AND
				`campaignmeta`.`meta_key` = %s
			AND
				`campaignmeta`.`meta_value` = %s
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
			$campaign_id,
			'products_flag',
			'all'
		);

		// Set the sql products.
		$sql_products = PsUpsellMaster_Database::prepare(
			'
			SELECT
				1
			FROM
				%i AS `eligible_products`
			WHERE
				`eligible_products`.`campaign_id` = %d
			AND
				`eligible_products`.`product_id` = `products`.`ID`
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
			$campaign_id
		);

		// Add a condition to the sql where.
		array_push(
			$sql_where,
			"AND ( EXISTS ( {$sql_meta} ) OR EXISTS ( {$sql_products} ) )"
		);
	}

	// Set the sql where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = ! empty( $sql_where ) ? "WHERE 1 = 1 {$sql_where}" : '';

	// Set the sql query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where}";

	// Get the count all rows.
	$count_all_rows = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql orderby.
	$sql_orderby = array();

	// Check if the inputs order and orderby are not empty.
	if ( ! is_null( $input_order ) && ! is_null( $input_orderby ) ) {
		// Check the input orderby value.
		if ( 0 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`products`.`post_title` {$input_order}";

			// Check the input orderby value.
		} elseif ( 1 === $input_orderby ) {
			// Set the sql orderby.
			$sql_orderby[] = "`authors`.`display_name` {$input_order}";
		}
	}

	// Set the sql orderby.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Set the sql limit.
	$sql_limit = -1 !== $input_length ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $input_start, $input_length ) : '';

	// Set the sql query.
	$sql_query = (
		"
		SELECT
			`products`.`ID` AS `product_id`,
			`products`.`post_title` AS `product_title`,
			`products`.`post_author` AS `author_id`,
			`authors`.`display_name` AS `author_name`
		{$sql_from}
		{$sql_join}
		{$sql_where}
		{$sql_orderby}
		{$sql_limit}
		"
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Get the product id.
		$product_id = isset( $row->product_id ) ? filter_var( $row->product_id, FILTER_VALIDATE_INT ) : false;
		$product_id = false !== $product_id ? $product_id : 0;

		// Set the html row.
		$html_row = array();

		//
		// Column: Product.
		//

		// Get the product title.
		$product_title = ! empty( $row->product_title ) ? $row->product_title : '';

		// Get the product view url.
		$product_view_url = get_permalink( $product_id );

		// Get the product edit url.
		$product_edit_url = get_edit_post_link( $product_id );

		// Start the buffer.
		ob_start();

		?>
		<div class="psupsellmaster-col">
			<?php if ( empty( $product_title ) ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<a class="psupsellmaster-link" href="<?php echo esc_url( $product_edit_url ); ?>" target="_blank"><?php echo esc_html( $product_title ); ?></a>
				<div class="psupsellmaster-row-actions psupsellmaster-ignore-on-export">
					<?php if ( ! empty( $product_edit_url ) ) : ?>
						<a class="psupsellmaster-row-action" href="<?php echo esc_url( $product_edit_url ); ?>" target="_blank" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
						<span>&#124;</span>
					<?php endif; ?>
					<a class="psupsellmaster-row-action" href="<?php echo esc_url( $product_view_url ); ?>" target="_blank" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
					<span>&#124;</span>
					<a class="psupsellmaster-row-action psupsellmaster-exclude" href="#" target="_blank" title="<?php esc_attr_e( 'Remove', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></a>
				</div>
			<?php endif; ?>
			<div class="psupsellmaster-ignore-on-export">
				<input class="psupsellmaster-field-product-id" type="hidden" value="<?php echo esc_attr( $product_id ); ?>" />
				<input class="psupsellmaster-field-product-title" type="hidden" value="<?php echo esc_attr( $product_title ); ?>" />
			</div>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: Author.
		//

		// Get the author id.
		$author_id = isset( $row->author_id ) ? filter_var( $row->author_id, FILTER_VALIDATE_INT ) : false;
		$author_id = false !== $author_id ? $author_id : 0;

		// Get the author name.
		$author_name = ! empty( $row->author_name ) ? $row->author_name : '';

		// Get the author edit url.
		$author_edit_url = get_edit_user_link( $author_id );

		// Start the buffer.
		ob_start();

		?>
		<div class="psupsellmaster-col">
			<?php if ( empty( $author_name ) ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<a class="psupsellmaster-link" href="<?php echo esc_url( $author_edit_url ); ?>" target="_blank"><?php echo esc_html( $author_name ); ?></a>
				<?php if ( psupsellmaster_is_plugin_active( 'edd' ) && psupsellmaster_is_plugin_active( 'edd-fes' ) ) : ?>
					<?php if ( EDD_FES()->vendors->user_is_vendor( $author_id ) ) : ?>
						<?php
						// Set the db vendors.
						$db_vendors = new FES_DB_Vendors();

						// Get the vendor.
						$vendor = $db_vendors->get_vendor_by( 'user_id', $author_id );

						// Get the vendor id.
						$vendor_id = ! empty( $vendor ) && isset( $vendor->id ) ? $vendor->id : 0;

						// Set the url args.
						$url_args = array(
							'action' => 'edit',
							'id'     => rawurlencode( $vendor_id ),
							'view'   => 'overview',
						);

						// Get the vendor edit url.
						$vendor_edit_url = fes_get_admin_vendors_base_url( $url_args );

						// Get the vendor store url.
						$vendor_store_url = EDD_FES()->vendors->get_vendor_store_url( $author_id );
						?>
						<?php if ( ! empty( $vendor_id ) && ( ! empty( $vendor_edit_url ) || ! empty( $vendor_store_url ) ) ) : ?>
							<div class="psupsellmaster-row-actions psupsellmaster-ignore-on-export">
								<a class="psupsellmaster-link" href="<?php echo esc_url( $author_edit_url ); ?>" target="_blank"><?php esc_html_e( 'User', 'psupsellmaster' ); ?></a>
								<?php if ( ! empty( $vendor_edit_url ) ) : ?>
									<span>&#124;</span>
									<a class="psupsellmaster-link" href="<?php echo esc_url( $vendor_edit_url ); ?>" target="_blank"><?php esc_html_e( 'Vendor', 'psupsellmaster' ); ?></a>
								<?php endif; ?>
								<?php if ( ! empty( $vendor_store_url ) ) : ?>
									<span>&#124;</span>
									<a class="psupsellmaster-link" href="<?php echo esc_url( $vendor_store_url ); ?>" target="_blank"><?php esc_html_e( 'Shop', 'psupsellmaster' ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		//
		// Column: UpsellMaster Tags.
		//

		// Set the taxonomy.
		$taxonomy = 'psupsellmaster_product_tag';

		// Set the tag args.
		$tag_args = array(
			'fields'     => 'id=>name',
			'hide_empty' => false,
			'number'     => 0,
			'order'      => 'ASC',
			'orderby'    => 'name',
		);

		// Get the taxonomy terms.
		$tag_terms = wp_get_post_terms( $product_id, $taxonomy, $tag_args );

		// Set the tags url.
		$tags_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' );

		// Start the buffer.
		ob_start();

		?>
		<div class="psupsellmaster-col">
			<?php if ( psupsellmaster_is_lite() ) : ?>
				<span>&#8212;</span>
			<?php else : ?>
				<?php if ( empty( $tag_terms ) ) : ?>
					<span>&#8212;</span>
				<?php else : ?>
					<ul class="psupsellmaster-list">
						<?php foreach ( $tag_terms as $term_id => $term_name ) : ?>
							<?php $term_edit_url = get_edit_term_link( $term_id, $taxonomy ); ?>
							<?php $term_manage_url = add_query_arg( 'filters[taxonomies][' . $taxonomy . ']', $term_id, $tags_url ); ?>
							<li class="psupsellmaster-item">
								<a class="psupsellmaster-link" href="<?php echo esc_url( $term_manage_url ); ?>" target="_blank"><?php echo esc_html( $term_name ); ?></a>
								<div class="psupsellmaster-item-actions psupsellmaster-ignore-on-export">
									<?php if ( ! empty( $term_edit_url ) ) : ?>
										<a class="psupsellmaster-link" href="<?php echo esc_url( $term_edit_url ); ?>" target="_blank"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
										<span>&#124;</span>
									<?php endif; ?>
									<a class="psupsellmaster-link" href="<?php echo esc_url( $term_manage_url ); ?>" target="_blank"><?php esc_html_e( 'Manage', 'psupsellmaster' ); ?></a>
									<span>&#124;</span>
									<a class="psupsellmaster-item-action psupsellmaster-unassign-post-term" data-post-id="<?php echo esc_attr( $product_id ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" data-term-id="<?php echo esc_attr( $term_id ); ?>" href="#" target="_blank"><?php esc_html_e( 'Unassign', 'psupsellmaster' ); ?></a>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php

		// Set the html column.
		$html_column = ob_get_clean();

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Add the row to the rows output.
		array_push( $output['datatables']['main']['data'], $html_row );
	}

	// Set the output: datatable total.
	$output['datatables']['main']['total'] = $count_all_rows;

	// Set the output: datatable filtered.
	$output['datatables']['main']['filtered'] = $count_all_rows;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_campaign_eligible_products', 'psupsellmaster_ajax_get_campaign_eligible_products' );

/**
 * Set the campaigns status through ajax.
 */
function psupsellmaster_ajax_set_campaigns_status() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the status.
	$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

	// Check if the status is not valid.
	if ( ! in_array( $status, array( 'active', 'inactive' ), true ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the ids.
	$ids = array();

	// Check if the ids is set.
	if ( isset( $_POST['ids'] ) ) {
		// Check if the ids is an array.
		if ( is_array( $_POST['ids'] ) ) {
			// Set the ids.
			$ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['ids'] ) );
		} else {
			// Set the ids.
			$ids = array( sanitize_text_field( wp_unslash( $_POST['ids'] ) ) );
		}

		// Set the ids.
		$ids = filter_var_array( $ids, FILTER_VALIDATE_INT );

		// Remove duplicate entries.
		$ids = array_unique( $ids );

		// Remove empty entries.
		$ids = array_filter( $ids );
	}

	// Check if the ids is empty.
	if ( empty( $ids ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the campaigns status.
	psupsellmaster_set_campaigns_status( $ids, $status );

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_set_campaigns_status', 'psupsellmaster_ajax_set_campaigns_status' );

/**
 * Duplicate campaigns through ajax.
 */
function psupsellmaster_ajax_duplicate_campaigns() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'duplicates' => array(),
		'redirect'   => false,
		'success'    => false,
	);

	// Set the ids.
	$ids = array();

	// Check if the ids is set.
	if ( isset( $_POST['ids'] ) ) {
		// Check if the ids is an array.
		if ( is_array( $_POST['ids'] ) ) {
			// Set the ids.
			$ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['ids'] ) );
		} else {
			// Set the ids.
			$ids = array( sanitize_text_field( wp_unslash( $_POST['ids'] ) ) );
		}

		// Set the ids.
		$ids = filter_var_array( $ids, FILTER_VALIDATE_INT );

		// Remove duplicate entries.
		$ids = array_unique( $ids );

		// Remove empty entries.
		$ids = array_filter( $ids );
	}

	// Check if the ids is empty.
	if ( empty( $ids ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Loop through the ids.
	foreach ( $ids as $id ) {
		// Duplicate the campaign and get the duplicate id.
		$duplicate_id = psupsellmaster_duplicate_campaign( $id );

		// Check if the duplicate id is not empty.
		if ( ! empty( $duplicate_id ) ) {
			// Add the duplicate id to the list.
			array_push( $output['duplicates'], $duplicate_id );
		}
	}

	// Set the output: success.
	$output['success'] = true;

	// Check if there is a single duplicate.
	if ( 1 === count( $output['duplicates'] ) ) {
		// Set the output: redirect.
		$output['redirect'] = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $duplicate_id );
	}

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_duplicate_campaigns', 'psupsellmaster_ajax_duplicate_campaigns' );

/**
 * Delete the campaigns through ajax.
 */
function psupsellmaster_ajax_delete_campaigns() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'deletions' => array(),
		'redirect'  => false,
		'success'   => false,
	);

	// Set the ids.
	$ids = array();

	// Check if the ids is set.
	if ( isset( $_POST['ids'] ) ) {
		// Check if the ids is an array.
		if ( is_array( $_POST['ids'] ) ) {
			// Set the ids.
			$ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['ids'] ) );
		} else {
			// Set the ids.
			$ids = array( sanitize_text_field( wp_unslash( $_POST['ids'] ) ) );
		}

		// Set the ids.
		$ids = filter_var_array( $ids, FILTER_VALIDATE_INT );

		// Remove duplicate entries.
		$ids = array_unique( $ids );

		// Remove empty entries.
		$ids = array_filter( $ids );
	}

	// Check if the ids is empty.
	if ( empty( $ids ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Loop through the ids.
	foreach ( $ids as $id ) {
		// Delete the campaign.
		$deleted = psupsellmaster_delete_campaign( $id );

		// Check if the campaign was deleted.
		if ( $deleted ) {
			// Add the duplicate id to the list.
			array_push( $output['deletions'], $id );
		}
	}

	// Set the output: success.
	$output['success'] = true;

	// Check if there is a single deletion.
	if ( 1 === count( $output['deletions'] ) ) {
		// Set the output: redirect.
		$output['redirect'] = admin_url( 'admin.php?page=psupsellmaster-campaigns' );
	}

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_delete_campaigns', 'psupsellmaster_ajax_delete_campaigns' );

/**
 * Create a new campaign from a template through ajax.
 */
function psupsellmaster_ajax_create_campaign_from_template() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'html'     => false,
		'redirect' => false,
		'success'  => false,
	);

	// Get the template.
	$template = isset( $_POST['template'] ) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : '';

	// Check if the template is empty.
	if ( empty( $template ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		// Check the limit.
		$limit = psupsellmaster_get_feature_limit_notices( 'campaigns_count' );

		// Check if the limit is not empty.
		if ( ! empty( $limit ) ) {
			// Set the output: html.
			$output['html'] = $limit;

			// Send the output.
			wp_send_json( $output );
		}
	}

	// Save the template attachments.
	psupsellmaster_campaigns_save_template_attachments( $template );

	// Create a new campaign.
	$campaign_id = psupsellmaster_create_campaign_from_template( $template );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Set the output: success.
	$output['success'] = true;

	// Set the output: redirect.
	$output['redirect'] = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign_id );

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_create_campaign_from_template', 'psupsellmaster_ajax_create_campaign_from_template' );

/**
 * Get the campaign templates through ajax.
 */
function psupsellmaster_ajax_get_campaign_templates() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the templates.
	$templates = psupsellmaster_campaigns_get_templates();

	// Start the output buffer.
	ob_start();

	// Include the templates file.
	require_once PSUPSELLMASTER_DIR . 'includes/admin/templates/campaigns/templates.php';

	// Set the templates.
	$templates = ob_get_clean();

	// Set the output: templates.
	$output['templates'] = $templates;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_campaign_templates', 'psupsellmaster_ajax_get_campaign_templates' );

/**
 * Save a campaign as a template through ajax.
 */
function psupsellmaster_ajax_save_campaign_as_template() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Start the output buffer.
	ob_start();

	?>
	<p style="color:#ff0000;"><?php esc_html_e( 'Something went wrong.', 'psupsellmaster' ); ?></p>
	<?php

	// Set the html.
	$html = ob_get_clean();

	// Set the output: html.
	$output['html'] = $html;

	// Get the campaign id.
	$campaign_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
	$campaign_id = filter_var( $campaign_id, FILTER_VALIDATE_INT );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the template title.
	$template_title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';

	// Set the args.
	$args = array(
		'campaign_id' => $campaign_id,
	);

	// Check if the template title is not empty.
	if ( ! empty( $template_title ) ) {
		// Set the args.
		$args['template_title'] = $template_title;
	}

	// Save the campaign data as a template.
	psupsellmaster_save_campaign_as_template( $args );

	// Start the output buffer.
	ob_start();

	?>
	<p style="color:#0e8c0e;"><?php esc_html_e( 'The template has been saved.', 'psupsellmaster' ); ?></p>
	<?php

	// Set the html.
	$html = ob_get_clean();

	// Set the output: success.
	$output['success'] = true;

	// Set the output: html.
	$output['html'] = $html;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_save_campaign_as_template', 'psupsellmaster_ajax_save_campaign_as_template' );

/**
 * Delete the campaign templates through ajax.
 */
function psupsellmaster_ajax_delete_campaign_templates() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'deletions' => array(),
		'success'   => false,
	);

	// Set the ids.
	$ids = array();

	// Check if the ids is set.
	if ( isset( $_POST['ids'] ) ) {
		// Check if the ids is an array.
		if ( is_array( $_POST['ids'] ) ) {
			// Set the ids.
			$ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['ids'] ) );
		} else {
			// Set the ids.
			$ids = array( sanitize_text_field( wp_unslash( $_POST['ids'] ) ) );
		}

		// Set the ids.
		$ids = filter_var_array( $ids, FILTER_VALIDATE_INT );

		// Remove duplicate entries.
		$ids = array_unique( $ids );

		// Remove empty entries.
		$ids = array_filter( $ids );
	}

	// Check if the ids is empty.
	if ( empty( $ids ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Loop through the ids.
	foreach ( $ids as $id ) {
		// Delete the campaign template.
		$deleted = psupsellmaster_delete_campaign_template( $id );

		// Check if the campaign template was deleted.
		if ( $deleted ) {
			// Add the duplicate id to the list.
			array_push( $output['deletions'], $id );
		}
	}

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_delete_campaign_templates', 'psupsellmaster_ajax_delete_campaign_templates' );

/**
 * Get the datetime left for a campaign through AJAX.
 */
function psupsellmaster_ajax_get_campaign_datetime_left() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'html'    => '',
		'success' => false,
	);

	// Get the id.
	$id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

	// Get the datetime left.
	$left = psupsellmaster_get_formatted_campaign_datetime_left( $id );

	// Check if the left is not empty.
	if ( ! empty( $left ) ) {
		// Start the output buffer.
		ob_start();

		?>
		<span class="psupsellmaster-datetime-left">
			<i><?php echo esc_html( $left ); ?></i>
			<i class="dashicons dashicons-update psupsellmaster-btn-refresh-datetime-left" title="<?php esc_attr_e( 'Refresh', 'psupsellmaster' ); ?>"></i>
		</span>
		<?php

		// Set the output: html.
		$output['html'] = ob_get_clean();
	}

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_campaign_datetime_left', 'psupsellmaster_ajax_get_campaign_datetime_left' );

/**
 * Subscribe to the newsletter.
 */
function psupsellmaster_lite_ajax_newsletter_subscribe() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the headers.
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Set the output.
	$output = array(
		'processed' => 0,
		'success'   => false,
	);

	// Get the email.
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$email = filter_var( $email, FILTER_VALIDATE_EMAIL );

	// Check if the email is not valid.
	if ( false === $email ) {
		// Set the email.
		$email = get_option( 'admin_email' );
	}

	// Set the args.
	$args = array(
		'body' => array(
			'email'       => $email,
			'plugin_name' => PSUPSELLMASTER_NAME,
			'domain'      => home_url( '/' ),
		),
	);

	// Send the request.
	wp_remote_post( PSUPSELLMASTER_NEWSLETTER_URL, $args );

	// Update the option.
	update_option( 'psupsellmaster_newsletter_subscribed', true, false );

	// Set the output: processed.
	$output['processed'] = 1;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_newsletter_subscribe', 'psupsellmaster_lite_ajax_newsletter_subscribe' );

/**
 * Handle the feedback through AJAX.
 */
function psupsellmaster_ajax_handle_feedback() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the headers.
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Check the capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Get the form.
	$form = isset( $_POST['form'] ) ? wp_kses_post( wp_unslash( $_POST['form'] ) ) : '';

	// Decode HTML entities before parsing.
	$data = html_entity_decode( $form );

	// Unserialize the data.
	wp_parse_str( $data, $data );

	// Check if the data is empty.
	if ( empty( $data ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Send the feedback email.
	$success = psupsellmaster_feedback_send_email( $data );

	// Set the output: success.
	$output['success'] = $success;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_handle_feedback', 'psupsellmaster_ajax_handle_feedback' );

/**
 * Save the setup wizard through ajax.
 */
function psupsellmaster_ajax_save_wizard() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'redirect' => false,
		'success'  => false,
	);

	// Get the data.
	$data = isset( $_POST['data'] ) ? wp_kses_post( wp_unslash( $_POST['data'] ) ) : '';

	// Decode HTML entities before parsing.
	$data = html_entity_decode( $data );

	// Unserialize the data.
	wp_parse_str( $data, $data );

	// Sanitize the data.
	$data = isset( $data ) ? map_deep( $data, 'sanitize_text_field' ) : array();

	// Get the step.
	$step = isset( $data['step'] ) ? $data['step'] : false;

	// Check if the step is empty.
	if ( empty( $step ) ) {
		// Send the output.
		wp_send_json( $output );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_setup_wizard_save', $step, $data );

	// Allow developers to use this.
	do_action( "psupsellmaster_admin_setup_wizard_save_{$step}", $data );

	// Get the redirect.
	$redirect = isset( $data['redirect'] ) ? $data['redirect'] : false;

	// Check if the redirect is not empty.
	if ( ! empty( $redirect ) ) {
		// Set the output: redirect.
		$output['redirect'] = esc_url_raw( $redirect );
	}

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_save_wizard', 'psupsellmaster_ajax_save_wizard' );

/**
 * Get the setup wizard statuses through ajax.
 */
function psupsellmaster_ajax_get_setup_wizard_statuses() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the output.
	$output = array(
		'redirect' => false,
		'success'  => false,
	);

	// Set the statuses.
	$statuses = array();

	// Get the started.
	$bp_queue_started = get_transient( 'psupsellmaster_setup_wizard_bp_queue' );
	$bp_queue_started = filter_var( $bp_queue_started, FILTER_VALIDATE_BOOLEAN );

	// Get the bp queue.
	$bp_queue = get_option( 'psupsellmaster_bp_queue' );

	// Set the statuses.
	$statuses['scores']['plain'] = $bp_queue_started && empty( $bp_queue );
	$statuses['scores']['plain'] = $statuses['scores']['plain'] ? 'success' : ( $bp_queue_started ? 'pending' : 'error' );

	// Start the output buffer.
	ob_start();

	// Render the summary item.
	psupsellmaster_admin_render_wizard_summary_item_scores( $statuses['scores']['plain'] );

	// Set the statuses.
	$statuses['scores']['html'] = ob_get_clean();

	// Set the output: statuses.
	$output['statuses'] = $statuses;

	// Set the output: success.
	$output['success'] = true;

	// Send the output.
	wp_send_json( $output );
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_setup_wizard_statuses', 'psupsellmaster_ajax_get_setup_wizard_statuses' );

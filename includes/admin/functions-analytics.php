<?php
/**
 * Admin - Functions - Analytics.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the analytics upsell results data.
 */
function psupsellmaster_get_analytics_upsell_results() {
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Get the category taxonomy.
	$category_taxonomy = psupsellmaster_get_product_category_taxonomy();

	// Get the tag taxonomy.
	$tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

	// Define the output.
	$output = array();

	// Define the charts.
	$charts = array(
		'base-products'   => array(
			'items'   => array(),
			'legends' => array(),
		),
		'upsell-products' => array(
			'items'   => array(),
			'legends' => array(),
		),
	);

	// Define the data.
	$data = array();

	// Define the input order.
	$input_order = null;

	// Define the input orderby.
	$input_orderby = null;

	// Check if the input order inputs does exist.
	if ( isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) && isset( $_POST['order'][0]['dir'] ) ) {
		// Set the input order.
		$input_orderby = sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) );
		$input_orderby = filter_var( $input_orderby, FILTER_VALIDATE_INT );
		$input_orderby = false !== $input_orderby ? $input_orderby : 4;

		// Set the input order.
		$input_order = strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) );
		$input_order = in_array( $input_order, array( 'ASC', 'DESC' ), true ) ? $input_order : 'DESC';
	}

	// Get the input draw.
	$input_draw = isset( $_POST['draw'] ) ? sanitize_text_field( wp_unslash( $_POST['draw'] ) ) : null;
	$input_draw = filter_var( $input_draw, FILTER_VALIDATE_INT );
	$input_draw = false !== $input_draw ? $input_draw : 0;

	// Get the input start.
	$input_start = isset( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : null;
	$input_start = filter_var( $input_start, FILTER_VALIDATE_INT );
	$input_start = false !== $input_start ? $input_start : 0;

	// Get the input length.
	$input_length = isset( $_POST['length'] ) ? sanitize_text_field( wp_unslash( $_POST['length'] ) ) : null;
	$input_length = filter_var( $input_length, FILTER_VALIDATE_INT );
	$input_length = false !== $input_length ? $input_length : 100;

	// Get the input filters.
	$input_filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();

	// Define the table names.
	$products_table_name           = PsUpsellMaster_Database::get_table_name( 'posts' );
	$term_relationships_table_name = PsUpsellMaster_Database::get_table_name( 'term_relationships' );
	$term_taxonomy_table_name      = PsUpsellMaster_Database::get_table_name( 'term_taxonomy' );
	$analytics_table_name          = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' );

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = 'COUNT( * )';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL from.
	$sql_from = "FROM `{$analytics_table_name}` AS `a`";

	// Build the SQL JOIN.
	$sql_join   = array();
	$sql_join[] = "LEFT JOIN `{$products_table_name}` AS `basep`";
	$sql_join[] = 'ON `basep`.`ID` = `a`.`base_product_id`';
	$sql_join[] = "LEFT JOIN `{$products_table_name}` AS `upsellp`";
	$sql_join[] = 'ON `upsellp`.`ID` = `a`.`upsell_product_id`';
	$sql_join   = implode( ' ', $sql_join );

	// Build the SQL where.
	$sql_where = array();

	// Get the filter base products.
	$filter_base_products = isset( $input_filters['products'] ) ? $input_filters['products'] : array();
	$filter_base_products = isset( $filter_base_products['base'] ) ? $filter_base_products['base'] : null;

	// Get the filter upsells products.
	$filter_upsell_products = isset( $input_filters['products'] ) ? $input_filters['products'] : array();
	$filter_upsell_products = isset( $filter_upsell_products['upsells'] ) ? $filter_upsell_products['upsells'] : null;

	// Get the filter amount start.
	$filter_amount_start = isset( $input_filters['amount'] ) ? $input_filters['amount'] : array();
	$filter_amount_start = isset( $filter_amount_start['start'] ) ? $filter_amount_start['start'] : null;
	$filter_amount_start = filter_var( $filter_amount_start, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter amount end.
	$filter_amount_end = isset( $input_filters['amount'] ) ? $input_filters['amount'] : array();
	$filter_amount_end = isset( $filter_amount_end['end'] ) ? $filter_amount_end['end'] : null;
	$filter_amount_end = filter_var( $filter_amount_end, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter sales start.
	$filter_sales_start = isset( $input_filters['sales'] ) ? $input_filters['sales'] : array();
	$filter_sales_start = isset( $filter_sales_start['start'] ) ? $filter_sales_start['start'] : null;
	$filter_sales_start = filter_var( $filter_sales_start, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

	// Get the filter sales end.
	$filter_sales_end = isset( $input_filters['sales'] ) ? $input_filters['sales'] : array();
	$filter_sales_end = isset( $filter_sales_end['end'] ) ? $filter_sales_end['end'] : null;
	$filter_sales_end = filter_var( $filter_sales_end, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

	// Get the filter average amount start.
	$filter_average_amount_start = isset( $input_filters['average_amount'] ) ? $input_filters['average_amount'] : array();
	$filter_average_amount_start = isset( $filter_average_amount_start['start'] ) ? $filter_average_amount_start['start'] : null;
	$filter_average_amount_start = filter_var( $filter_average_amount_start, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter average_amount end.
	$filter_average_amount_end = isset( $input_filters['average_amount'] ) ? $input_filters['average_amount'] : array();
	$filter_average_amount_end = isset( $filter_average_amount_end['end'] ) ? $filter_average_amount_end['end'] : null;
	$filter_average_amount_end = filter_var( $filter_average_amount_end, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter base categories.
	$filter_base_categories = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_base_categories = isset( $filter_base_categories['categories'] ) ? $filter_base_categories['categories'] : array();
	$filter_base_categories = isset( $filter_base_categories['base'] ) ? $filter_base_categories['base'] : null;

	// Get the filter base tags.
	$filter_base_tags = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_base_tags = isset( $filter_base_tags['tags'] ) ? $filter_base_tags['tags'] : array();
	$filter_base_tags = isset( $filter_base_tags['base'] ) ? $filter_base_tags['base'] : null;

	// Get the filter base custom.
	$filter_base_custom = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_base_custom = isset( $filter_base_custom['custom'] ) ? $filter_base_custom['custom'] : array();
	$filter_base_custom = isset( $filter_base_custom['base'] ) ? $filter_base_custom['base'] : null;

	// Get the filter upsells categories.
	$filter_upsell_categories = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_upsell_categories = isset( $filter_upsell_categories['categories'] ) ? $filter_upsell_categories['categories'] : array();
	$filter_upsell_categories = isset( $filter_upsell_categories['upsells'] ) ? $filter_upsell_categories['upsells'] : null;

	// Get the filter upsells tags.
	$filter_upsell_tags = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_upsell_tags = isset( $filter_upsell_tags['tags'] ) ? $filter_upsell_tags['tags'] : array();
	$filter_upsell_tags = isset( $filter_upsell_tags['upsells'] ) ? $filter_upsell_tags['upsells'] : null;

	// Get the filter upsells custom.
	$filter_upsell_custom = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_upsell_custom = isset( $filter_upsell_custom['custom'] ) ? $filter_upsell_custom['custom'] : array();
	$filter_upsell_custom = isset( $filter_upsell_custom['upsells'] ) ? $filter_upsell_custom['upsells'] : null;

	// Check if the filter base products is not null.
	if ( ! is_null( $filter_base_products ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filter_base_products ), '%d' ) );

		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND `basep`.ID IN ( {$placeholders} )", $filter_base_products );
	}

	// Check if the filter upsell products is not null.
	if ( ! is_null( $filter_upsell_products ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filter_upsell_products ), '%d' ) );

		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND `upsellp`.ID IN ( {$placeholders} )", $filter_upsell_products );
	}

	// Check if the filter amount start is not null.
	if ( ! is_null( $filter_amount_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_amount` >= %f', $filter_amount_start );
	}

	// Check if the filter amount end is not null.
	if ( ! is_null( $filter_amount_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_amount` <= %f', $filter_amount_end );
	}

	// Check if the filter sales start is not null.
	if ( ! is_null( $filter_sales_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_sales` >= %d', $filter_sales_start );
	}

	// Check if the filter sales end is not null.
	if ( ! is_null( $filter_sales_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_sales` <= %d', $filter_sales_end );
	}

	// Check if the filter average amount start is not null.
	if ( ! is_null( $filter_average_amount_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`average_amount` >= %f', $filter_average_amount_start );
	}

	// Check if the filter average amount end is not null.
	if ( ! is_null( $filter_average_amount_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`average_amount` <= %f', $filter_average_amount_end );
	}

	// Define the filter base taxonomies.
	$filter_base_taxonomies = array();

	// Define the filter upsell taxonomies.
	$filter_upsell_taxonomies = array();

	// Check if the filter base categories is not null.
	if ( ! is_null( $filter_base_categories ) ) {
		// Set the filter base categories.
		$filter_base_categories = array( $category_taxonomy => $filter_base_categories );

		// Merge the taxonomies.
		$filter_base_taxonomies = array_merge( $filter_base_taxonomies, $filter_base_categories );
	}

	// Check if the filter base tags is not null.
	if ( ! is_null( $filter_base_tags ) ) {
		// Set the filter base tags.
		$filter_base_tags = array( $tag_taxonomy => $filter_base_tags );

		// Merge the taxonomies.
		$filter_base_taxonomies = array_merge( $filter_base_taxonomies, $filter_base_tags );
	}

	// Check if the filter base custom taxonomy is not null.
	if ( ! is_null( $filter_base_custom ) ) {
		// Merge the taxonomies.
		$filter_base_taxonomies = array_merge( $filter_base_taxonomies, $filter_base_custom );
	}

	// Check if the filter upsell categories is not null.
	if ( ! is_null( $filter_upsell_categories ) ) {
		// Set the filter upsell categories.
		$filter_upsell_categories = array( $category_taxonomy => $filter_upsell_categories );

		// Merge the taxonomies.
		$filter_upsell_taxonomies = array_merge( $filter_upsell_taxonomies, $filter_upsell_categories );
	}

	// Check if the filter upsell tags is not null.
	if ( ! is_null( $filter_upsell_tags ) ) {
		// Set the filter upsell tags.
		$filter_upsell_tags = array( $tag_taxonomy => $filter_upsell_tags );

		// Merge the taxonomies.
		$filter_upsell_taxonomies = array_merge( $filter_upsell_taxonomies, $filter_upsell_tags );
	}

	// Check if the filter upsell custom taxonomy is not null.
	if ( ! is_null( $filter_upsell_custom ) ) {
		// Merge the taxonomies.
		$filter_upsell_taxonomies = array_merge( $filter_upsell_taxonomies, $filter_upsell_custom );
	}

	// Define the filter taxonomy types.
	$filter_taxonomy_types = array();

	// Check if the filter base taxonomies is not null.
	if ( ! is_null( $filter_base_taxonomies ) ) {
		// Add a type to the list.
		array_push( $filter_taxonomy_types, 'base' );
	}

	// Check if the filter upsell taxonomies is not null.
	if ( ! is_null( $filter_upsell_taxonomies ) ) {
		// Add a type to the list.
		array_push( $filter_taxonomy_types, 'upsells' );
	}

	// Loop through the filter taxonomy types.
	foreach ( $filter_taxonomy_types as $taxonomy_type ) {
		// Define the column name.
		$column_name = '';

		// Define the taxonomies.
		$taxonomies = array();

		// Check if the type is base.
		if ( 'base' === $taxonomy_type ) {
			// Set the column name.
			$column_name = 'base_product_id';

			// Set the taxonomies.
			$taxonomies = $filter_base_taxonomies;

			// Otherwise, check if the type is upsells.
		} elseif ( 'upsells' === $taxonomy_type ) {
			// Set the column name.
			$column_name = 'upsell_product_id';

			// Set the taxonomies.
			$taxonomies = $filter_upsell_taxonomies;
		}

		// Loop through the taxonomies.
		foreach ( $taxonomies as $taxonomy_name => $ids ) {
			// Build the sub SQL select.
			$sub_sql_select = 'SELECT 1';

			// Build the sub SQL from.
			$sub_sql_from = "FROM `{$term_relationships_table_name}` AS `tr`";

			// Build the sub SQL join.
			$sub_sql_join   = array();
			$sub_sql_join[] = "INNER JOIN `{$term_taxonomy_table_name}` AS `tt`";
			$sub_sql_join[] = 'ON `tt`.`term_taxonomy_id` = `tr`.`term_taxonomy_id`';
			$sub_sql_join   = implode( ' ', $sub_sql_join );

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

			// Build the sub SQL where.
			$sub_sql_where   = array();
			$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `tt`.`taxonomy` = %s', $taxonomy_name );
			$sub_sql_where[] = "AND `a`.`{$column_name}` = `tr`.`object_id`";
			$sub_sql_where[] = PsUpsellMaster_Database::prepare( "AND `tt`.`term_id` IN ( {$placeholders} )", $ids );
			$sub_sql_where   = implode( ' ', $sub_sql_where );
			$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

			// Build the sub SQL.
			$sub_sql_query = "{$sub_sql_select} {$sub_sql_from} {$sub_sql_join} {$sub_sql_where}";

			// Build the SQL where.
			$sql_where[] = "AND EXISTS ( $sub_sql_query )";
		}
	}

	// Build the SQL where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = "WHERE 1 = 1 {$sql_where}";

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Get the sql count all.
	$sql_count_all = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where}";

	// Get the sql count filtered.
	$sql_count_filtered = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.`base_product_id`';
	$sql_select[] = 'SUM( `a`.`total_amount` ) AS `sum_total_amount`';
	$sql_select[] = 'SUM( `a`.`total_sales` ) AS `sum_total_sales`';
	$sql_select[] = 'AVG( `a`.`average_amount` ) AS `avg_average_amount`';
	$sql_select[] = '`basep`.`post_title` AS `base_product_title`';
	$sql_select[] = '`upsellp`.`post_title` AS `upsell_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL groupby.
	$sql_groupby   = array();
	$sql_groupby[] = '`a`.`base_product_id`';
	$sql_groupby   = implode( ', ', $sql_groupby );
	$sql_groupby   = ! empty( $sql_groupby ) ? "GROUP BY {$sql_groupby}" : '';

	// Build the SQL orderby.
	$sql_orderby   = array();
	$sql_orderby[] = '`sum_total_amount` DESC';
	$sql_orderby[] = '`sum_total_sales` DESC';
	$sql_orderby[] = '`avg_average_amount` DESC';
	$sql_orderby[] = '`base_product_title` DESC';
	$sql_orderby   = implode( ', ', $sql_orderby );
	$sql_orderby   = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL limit.
	$sql_limit = PsUpsellMaster_Database::prepare( 'LIMIT %d', 10 );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_groupby} {$sql_orderby} {$sql_limit}";

	// Get the top base products.
	$top_base_products = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the top base products.
	foreach ( $top_base_products as $top_product ) {
		// Get the base product id.
		$base_product_id = isset( $top_product->base_product_id ) ? filter_var( $top_product->base_product_id, FILTER_VALIDATE_INT ) : false;
		$base_product_id = ! empty( $base_product_id ) ? $base_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $top_product->sum_total_amount ) ? filter_var( $top_product->sum_total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? round( $total_amount ) : 0;

		// Get the total sales.
		$total_sales = isset( $top_product->sum_total_sales ) ? filter_var( $top_product->sum_total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the base product title.
		$base_product_title = get_the_title( $base_product_id );

		// Define the chart item.
		$chart_item = array(
			'amount' => $total_amount,
			'sales'  => $total_sales,
			'label'  => $base_product_title,
		);

		// Add a chart item to the chart.
		array_push( $charts['base-products']['items'], $chart_item );

		// Define the chart legends.
		$chart_legends = array(
			'amount' => __( 'Total Upsells', 'psupsellmaster' ),
			'sales'  => __( 'Number of Upsells', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['base-products']['legends'], $chart_legends );
	}

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.`upsell_product_id`';
	$sql_select[] = 'SUM( `a`.`total_amount` ) AS `sum_total_amount`';
	$sql_select[] = 'SUM( `a`.`total_sales` ) AS `sum_total_sales`';
	$sql_select[] = 'SUM( `a`.`average_amount` ) AS `avg_average_amount`';
	$sql_select[] = '`basep`.`post_title` AS `base_product_title`';
	$sql_select[] = '`upsellp`.`post_title` AS `upsell_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL groupby.
	$sql_groupby   = array();
	$sql_groupby[] = '`a`.`upsell_product_id`';
	$sql_groupby   = implode( ', ', $sql_groupby );
	$sql_groupby   = ! empty( $sql_groupby ) ? "GROUP BY {$sql_groupby}" : '';

	// Build the SQL orderby.
	$sql_orderby   = array();
	$sql_orderby[] = '`sum_total_amount` DESC';
	$sql_orderby[] = '`sum_total_sales` DESC';
	$sql_orderby[] = '`avg_average_amount` DESC';
	$sql_orderby[] = '`upsell_product_title` DESC';
	$sql_orderby   = implode( ', ', $sql_orderby );
	$sql_orderby   = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_groupby} {$sql_orderby} {$sql_limit}";

	// Get the top upsell products.
	$top_upsell_products = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the top upsell products.
	foreach ( $top_upsell_products as $top_product ) {
		// Get the upsell product id.
		$upsell_product_id = isset( $top_product->upsell_product_id ) ? filter_var( $top_product->upsell_product_id, FILTER_VALIDATE_INT ) : false;
		$upsell_product_id = ! empty( $upsell_product_id ) ? $upsell_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $top_product->sum_total_amount ) ? filter_var( $top_product->sum_total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? round( $total_amount ) : 0;

		// Get the total sales.
		$total_sales = isset( $top_product->sum_total_sales ) ? filter_var( $top_product->sum_total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the upsell product title.
		$upsell_product_title = get_the_title( $upsell_product_id );

		// Define the chart item.
		$chart_item = array(
			'amount' => $total_amount,
			'sales'  => $total_sales,
			'label'  => $upsell_product_title,
		);

		// Add a chart item to the chart.
		array_push( $charts['upsell-products']['items'], $chart_item );

		// Define the chart legends.
		$chart_legends = array(
			'amount' => __( 'Total Upsells', 'psupsellmaster' ),
			'sales'  => __( 'Number of Upsells', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['upsell-products']['legends'], $chart_legends );
	}

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.*';
	$sql_select[] = '`basep`.`post_title` AS `base_product_title`';
	$sql_select[] = '`upsellp`.`post_title` AS `upsell_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL orderby.
	$sql_orderby = array();

	// Check if the inputs order and orderby are not empty.
	if ( ! is_null( $input_order ) && ! is_null( $input_orderby ) ) {

		// Check if the input orderby value is 0 (Base Product).
		if ( 0 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`basep`.`post_title` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";

			// Otherwise, check if the input orderby value is 1 (Upsell Product).
		} elseif ( 1 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`upsellp`.`post_title` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";

			// Otherwise, check if the input orderby value is 2 (Average Price).
		} elseif ( 2 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`basep`.`post_title` {$input_order}";
			$sql_orderby[] = "`upsellp`.`post_title` {$input_order}";

			// Otherwise, check if the input orderby value is 3 (Total Sales).
		} elseif ( 3 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";
			$sql_orderby[] = "`basep`.`post_title` {$input_order}";
			$sql_orderby[] = "`upsellp`.`post_title` {$input_order}";

			// Otherwise, check if the input orderby value is 4 (Total Amount).
		} elseif ( 4 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";
			$sql_orderby[] = "`basep`.`post_title` {$input_order}";
			$sql_orderby[] = "`upsellp`.`post_title` {$input_order}";
		}
	}

	// Build the SQL orderby.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL limit.
	$sql_limit = -1 !== $input_length ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $input_start, $input_length ) : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_orderby} {$sql_limit}";

	// Get the analytics.
	$analytics = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the analytics.
	foreach ( $analytics as $result ) {
		// Define the row.
		$row = array();

		// Get the base product id.
		$base_product_id = isset( $result->base_product_id ) ? filter_var( $result->base_product_id, FILTER_VALIDATE_INT ) : false;
		$base_product_id = ! empty( $base_product_id ) ? $base_product_id : 0;

		// Get the upsell product id.
		$upsell_product_id = isset( $result->upsell_product_id ) ? filter_var( $result->upsell_product_id, FILTER_VALIDATE_INT ) : false;
		$upsell_product_id = ! empty( $upsell_product_id ) ? $upsell_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $result->total_amount ) ? filter_var( $result->total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? $total_amount : 0;

		// Get the total sales.
		$total_sales = isset( $result->total_sales ) ? filter_var( $result->total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the average amount.
		$average_amount = isset( $result->average_amount ) ? filter_var( $result->average_amount, FILTER_VALIDATE_FLOAT ) : false;
		$average_amount = ! empty( $average_amount ) ? $average_amount : 0;

		// Get the base product title.
		$base_product_title = get_the_title( $base_product_id );

		// Get the base product view url.
		$base_product_view_url = get_permalink( $base_product_id );

		// Get the base product edit url.
		$base_product_edit_url = get_edit_post_link( $base_product_id );

		// Start the buffer.
		ob_start();

		?>

		<div class="psupsellmaster-analytics-table-col">
			<div class="psupsellmaster-analytics-table-col-data">
				<a class="psupsellmaster-link" href="<?php echo esc_url( $base_product_view_url ); ?>" title="<?php echo esc_attr( $base_product_title ); ?>"><?php echo esc_html( $base_product_title ); ?></a>
			</div>
			<div class="psupsellmaster-row-actions">

				<?php if ( ! empty( $base_product_edit_url ) ) : ?>
					<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $base_product_edit_url ); ?>" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
					&nbsp;|&nbsp;
				<?php endif; ?>

				<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $base_product_view_url ); ?>" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
			</div>
		</div>

		<?php

		// Set the column.
		$column = ob_get_clean();

		// Add the column to the row.
		array_push( $row, $column );

		// Get the upsell product title.
		$upsell_product_title = get_the_title( $upsell_product_id );

		// Get the upsell product view url.
		$upsell_product_view_url = get_permalink( $upsell_product_id );

		// Get the upsell product edit url.
		$upsell_product_edit_url = get_edit_post_link( $upsell_product_id );

		// Start the buffer.
		ob_start();

		?>

		<div class="psupsellmaster-analytics-table-col">
			<div class="psupsellmaster-analytics-table-col-data">
				<a class="psupsellmaster-link" href="<?php echo esc_url( $upsell_product_view_url ); ?>" title="<?php echo esc_attr( $upsell_product_title ); ?>"><?php echo esc_html( $upsell_product_title ); ?></a>
			</div>
			<div class="psupsellmaster-row-actions">

				<?php if ( ! empty( $upsell_product_edit_url ) ) : ?>
					<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $upsell_product_edit_url ); ?>" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
					&nbsp;|&nbsp;
				<?php endif; ?>

				<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $upsell_product_view_url ); ?>" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
			</div>
		</div>

		<?php

		// Set the column.
		$column = ob_get_clean();

		// Add the column to the row.
		array_push( $row, $column );

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the column.
			$column = wp_strip_all_tags( wc_price( $average_amount ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the column.
			$column = edd_currency_filter( edd_format_amount( $average_amount ) );
		}

		// Add the column to the row.
		array_push( $row, $column );

		// Set the column.
		$column = $total_sales;

		// Add the column to the row.
		array_push( $row, $column );

		// Set the column.
		$column = $total_amount;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the column.
			$column = wp_strip_all_tags( wc_price( $total_amount ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the column.
			$column = edd_currency_filter( edd_format_amount( $total_amount ) );
		}

		// Add the column to the row.
		array_push( $row, $column );

		// Add the row to the data.
		array_push( $data, $row );
	}

	// Get the background process last run date for upsells.
	$last_run_date = get_option( 'psupsellmaster_bp_analytics_upsells_last_run' );
	$last_run_date = ! empty( $last_run_date ) ? date_i18n( get_option( 'date_format' ), $last_run_date ) : false;
	$last_run_date = ! empty( $last_run_date ) ? sprintf( '%s: %s', __( 'Last Run Date', 'psupsellmaster' ), $last_run_date ) : __( 'Unknown', 'psupsellmaster' );

	// Set the output charts.
	$output['charts'] = $charts;

	// Set the output dates.
	$output['dates'] = array( 'last_run' => $last_run_date );

	// Set the output: datatable: datatable data.
	$output['datatable']['data'] = $data;

	// Set the output: datatable draw.
	$output['datatable']['draw'] = $input_draw + 1;

	// Set the output: datatable total.
	$output['datatable']['total'] = $sql_count_all;

	// Set the output: datatable filtered.
	$output['datatable']['filtered'] = $sql_count_filtered;

	echo wp_json_encode( $output );

	exit( 0 );
}
add_action( 'wp_ajax_psupsellmaster_get_analytics_upsell_results', 'psupsellmaster_get_analytics_upsell_results' );

/**
 * Gets the analytics order results data.
 */
function psupsellmaster_get_analytics_order_results() {
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Get the category taxonomy.
	$category_taxonomy = psupsellmaster_get_product_category_taxonomy();

	// Get the tag taxonomy.
	$tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

	// Define the output.
	$output = array();

	// Define the charts.
	$charts = array(
		'order-products'   => array(
			'items'   => array(),
			'legends' => array(),
		),
		'related-products' => array(
			'items'   => array(),
			'legends' => array(),
		),
	);

	// Define the data.
	$data = array();

	// Define the input order.
	$input_order = null;

	// Define the input orderby.
	$input_orderby = null;

	// Check if the input order inputs does exist.
	if ( isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) && isset( $_POST['order'][0]['dir'] ) ) {
		// Set the input order.
		$input_orderby = sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) );
		$input_orderby = filter_var( $input_orderby, FILTER_VALIDATE_INT );
		$input_orderby = false !== $input_orderby ? $input_orderby : 4;

		// Set the input order.
		$input_order = strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) );
		$input_order = in_array( $input_order, array( 'ASC', 'DESC' ), true ) ? $input_order : 'DESC';
	}

	// Get the input draw.
	$input_draw = isset( $_POST['draw'] ) ? sanitize_text_field( wp_unslash( $_POST['draw'] ) ) : null;
	$input_draw = filter_var( $input_draw, FILTER_VALIDATE_INT );
	$input_draw = false !== $input_draw ? $input_draw : 0;

	// Get the input start.
	$input_start = isset( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : null;
	$input_start = filter_var( $input_start, FILTER_VALIDATE_INT );
	$input_start = false !== $input_start ? $input_start : 0;

	// Get the input length.
	$input_length = isset( $_POST['length'] ) ? sanitize_text_field( wp_unslash( $_POST['length'] ) ) : null;
	$input_length = filter_var( $input_length, FILTER_VALIDATE_INT );
	$input_length = false !== $input_length ? $input_length : 100;

	// Get the input filters.
	$input_filters = isset( $_POST['filters'] ) ? map_deep( wp_unslash( $_POST['filters'] ), 'sanitize_text_field' ) : array();

	// Define the table names.
	$products_table_name           = PsUpsellMaster_Database::get_table_name( 'posts' );
	$term_relationships_table_name = PsUpsellMaster_Database::get_table_name( 'term_relationships' );
	$term_taxonomy_table_name      = PsUpsellMaster_Database::get_table_name( 'term_taxonomy' );
	$analytics_table_name          = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' );

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = 'COUNT( * )';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL from.
	$sql_from = "FROM `{$analytics_table_name}` AS `a`";

	// Build the SQL JOIN.
	$sql_join   = array();
	$sql_join[] = "LEFT JOIN `{$products_table_name}` AS `orderp`";
	$sql_join[] = 'ON `orderp`.`ID` = `a`.`order_product_id`';
	$sql_join[] = "LEFT JOIN `{$products_table_name}` AS `relatedp`";
	$sql_join[] = 'ON `relatedp`.`ID` = `a`.`related_product_id`';
	$sql_join   = implode( ' ', $sql_join );

	// Build the SQL where.
	$sql_where = array();

	// Get the filter order products.
	$filter_order_products = isset( $input_filters['products'] ) ? $input_filters['products'] : array();
	$filter_order_products = isset( $filter_order_products['orders'] ) ? $filter_order_products['orders'] : null;

	// Get the filter related products.
	$filter_related_products = isset( $input_filters['products'] ) ? $input_filters['products'] : array();
	$filter_related_products = isset( $filter_related_products['related'] ) ? $filter_related_products['related'] : null;

	// Get the filter amount start.
	$filter_amount_start = isset( $input_filters['amount'] ) ? $input_filters['amount'] : array();
	$filter_amount_start = isset( $filter_amount_start['start'] ) ? $filter_amount_start['start'] : null;
	$filter_amount_start = filter_var( $filter_amount_start, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter amount end.
	$filter_amount_end = isset( $input_filters['amount'] ) ? $input_filters['amount'] : array();
	$filter_amount_end = isset( $filter_amount_end['end'] ) ? $filter_amount_end['end'] : null;
	$filter_amount_end = filter_var( $filter_amount_end, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter sales start.
	$filter_sales_start = isset( $input_filters['sales'] ) ? $input_filters['sales'] : array();
	$filter_sales_start = isset( $filter_sales_start['start'] ) ? $filter_sales_start['start'] : null;
	$filter_sales_start = filter_var( $filter_sales_start, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

	// Get the filter sales end.
	$filter_sales_end = isset( $input_filters['sales'] ) ? $input_filters['sales'] : array();
	$filter_sales_end = isset( $filter_sales_end['end'] ) ? $filter_sales_end['end'] : null;
	$filter_sales_end = filter_var( $filter_sales_end, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

	// Get the filter average amount start.
	$filter_average_amount_start = isset( $input_filters['average_amount'] ) ? $input_filters['average_amount'] : array();
	$filter_average_amount_start = isset( $filter_average_amount_start['start'] ) ? $filter_average_amount_start['start'] : null;
	$filter_average_amount_start = filter_var( $filter_average_amount_start, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter average amount end.
	$filter_average_amount_end = isset( $input_filters['average_amount'] ) ? $input_filters['average_amount'] : array();
	$filter_average_amount_end = isset( $filter_average_amount_end['end'] ) ? $filter_average_amount_end['end'] : null;
	$filter_average_amount_end = filter_var( $filter_average_amount_end, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

	// Get the filter order categories.
	$filter_order_categories = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_order_categories = isset( $filter_order_categories['categories'] ) ? $filter_order_categories['categories'] : array();
	$filter_order_categories = isset( $filter_order_categories['orders'] ) ? $filter_order_categories['orders'] : null;

	// Get the filter order tags.
	$filter_order_tags = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_order_tags = isset( $filter_order_tags['tags'] ) ? $filter_order_tags['tags'] : array();
	$filter_order_tags = isset( $filter_order_tags['orders'] ) ? $filter_order_tags['orders'] : null;

	// Get the filter order custom.
	$filter_order_custom = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_order_custom = isset( $filter_order_custom['custom'] ) ? $filter_order_custom['custom'] : array();
	$filter_order_custom = isset( $filter_order_custom['orders'] ) ? $filter_order_custom['orders'] : null;

	// Get the filter related categories.
	$filter_related_categories = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_related_categories = isset( $filter_related_categories['categories'] ) ? $filter_related_categories['categories'] : array();
	$filter_related_categories = isset( $filter_related_categories['related'] ) ? $filter_related_categories['related'] : null;

	// Get the filter related tags.
	$filter_related_tags = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_related_tags = isset( $filter_related_tags['tags'] ) ? $filter_related_tags['tags'] : array();
	$filter_related_tags = isset( $filter_related_tags['related'] ) ? $filter_related_tags['related'] : null;

	// Get the filter related custom.
	$filter_related_custom = isset( $input_filters['taxonomies'] ) ? $input_filters['taxonomies'] : array();
	$filter_related_custom = isset( $filter_related_custom['custom'] ) ? $filter_related_custom['custom'] : array();
	$filter_related_custom = isset( $filter_related_custom['related'] ) ? $filter_related_custom['related'] : null;

	// Check if the filter order products is not null.
	if ( ! is_null( $filter_order_products ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filter_order_products ), '%d' ) );

		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND `orderp`.ID IN ( {$placeholders} )", $filter_order_products );
	}

	// Check if the filter related products is not null.
	if ( ! is_null( $filter_related_products ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $filter_related_products ), '%d' ) );

		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND `relatedp`.ID IN ( {$placeholders} )", $filter_related_products );
	}

	// Check if the filter amount start is not null.
	if ( ! is_null( $filter_amount_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_amount` >= %f', $filter_amount_start );
	}

	// Check if the filter amount end is not null.
	if ( ! is_null( $filter_amount_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_amount` <= %f', $filter_amount_end );
	}

	// Check if the filter sales start is not null.
	if ( ! is_null( $filter_sales_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_sales` >= %d', $filter_sales_start );
	}

	// Check if the filter sales end is not null.
	if ( ! is_null( $filter_sales_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`total_sales` <= %d', $filter_sales_end );
	}

	// Check if the filter average amount start is not null.
	if ( ! is_null( $filter_average_amount_start ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`average_amount` >= %f', $filter_average_amount_start );
	}

	// Check if the filter average amount end is not null.
	if ( ! is_null( $filter_average_amount_end ) ) {
		// Build the SQL where.
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `a`.`average_amount` <= %f', $filter_average_amount_end );
	}

	// Define the filter order taxonomies.
	$filter_order_taxonomies = array();

	// Define the filter related taxonomies.
	$filter_related_taxonomies = array();

	// Check if the filter order categories is not null.
	if ( ! is_null( $filter_order_categories ) ) {
		// Set the filter order categories.
		$filter_order_categories = array( $category_taxonomy => $filter_order_categories );

		// Merge the taxonomies.
		$filter_order_taxonomies = array_merge( $filter_order_taxonomies, $filter_order_categories );
	}

	// Check if the filter order tags is not null.
	if ( ! is_null( $filter_order_tags ) ) {
		// Set the filter order tags.
		$filter_order_tags = array( $tag_taxonomy => $filter_order_tags );

		// Merge the taxonomies.
		$filter_order_taxonomies = array_merge( $filter_order_taxonomies, $filter_order_tags );
	}

	// Check if the filter order custom taxonomy is not null.
	if ( ! is_null( $filter_order_custom ) ) {
		// Merge the taxonomies.
		$filter_order_taxonomies = array_merge( $filter_order_taxonomies, $filter_order_custom );
	}

	// Check if the filter related categories is not null.
	if ( ! is_null( $filter_related_categories ) ) {
		// Set the filter related categories.
		$filter_related_categories = array( $category_taxonomy => $filter_related_categories );

		// Merge the taxonomies.
		$filter_related_taxonomies = array_merge( $filter_related_taxonomies, $filter_related_categories );
	}

	// Check if the filter related tags is not null.
	if ( ! is_null( $filter_related_tags ) ) {
		// Set the filter related tags.
		$filter_related_tags = array( $tag_taxonomy => $filter_related_tags );

		// Merge the taxonomies.
		$filter_related_taxonomies = array_merge( $filter_related_taxonomies, $filter_related_tags );
	}

	// Check if the filter related custom taxonomy is not null.
	if ( ! is_null( $filter_related_custom ) ) {
		// Merge the taxonomies.
		$filter_related_taxonomies = array_merge( $filter_related_taxonomies, $filter_related_custom );
	}

	// Define the filter taxonomy types.
	$filter_taxonomy_types = array();

	// Check if the filter order taxonomies is not null.
	if ( ! is_null( $filter_order_taxonomies ) ) {
		// Add a type to the list.
		array_push( $filter_taxonomy_types, 'orders' );
	}

	// Check if the filter related taxonomies is not null.
	if ( ! is_null( $filter_related_taxonomies ) ) {
		// Add a type to the list.
		array_push( $filter_taxonomy_types, 'related' );
	}

	// Loop through the filter taxonomy types.
	foreach ( $filter_taxonomy_types as $taxonomy_type ) {
		// Define the column name.
		$column_name = '';

		// Define the taxonomies.
		$taxonomies = array();

		// Check if the type is orders.
		if ( 'orders' === $taxonomy_type ) {
			// Set the column name.
			$column_name = 'order_product_id';

			// Set the taxonomies.
			$taxonomies = $filter_order_taxonomies;

			// Otherwise, check if the type is related.
		} elseif ( 'related' === $taxonomy_type ) {
			// Set the column name.
			$column_name = 'related_product_id';

			// Set the taxonomies.
			$taxonomies = $filter_related_taxonomies;
		}

		// Loop through the taxonomies.
		foreach ( $taxonomies as $taxonomy_name => $ids ) {
			// Build the sub SQL select.
			$sub_sql_select = 'SELECT 1';

			// Build the sub SQL from.
			$sub_sql_from = "FROM `{$term_relationships_table_name}` AS `tr`";

			// Build the sub SQL join.
			$sub_sql_join   = array();
			$sub_sql_join[] = "INNER JOIN `{$term_taxonomy_table_name}` AS `tt`";
			$sub_sql_join[] = 'ON `tt`.`term_taxonomy_id` = `tr`.`term_taxonomy_id`';
			$sub_sql_join   = implode( ' ', $sub_sql_join );

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

			// Build the sub SQL where.
			$sub_sql_where   = array();
			$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `tt`.`taxonomy` = %s', $taxonomy_name );
			$sub_sql_where[] = "AND `a`.`{$column_name}` = `tr`.`object_id`";
			$sub_sql_where[] = PsUpsellMaster_Database::prepare( "AND `tt`.`term_id` IN ( {$placeholders} )", $ids );
			$sub_sql_where   = implode( ' ', $sub_sql_where );
			$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

			// Build the sub SQL.
			$sub_sql_query = "{$sub_sql_select} {$sub_sql_from} {$sub_sql_join} {$sub_sql_where}";

			// Build the SQL where.
			$sql_where[] = "AND EXISTS ( $sub_sql_query )";
		}
	}

	// Build the SQL where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = "WHERE 1 = 1 {$sql_where}";

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Get the sql count all.
	$sql_count_all = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where}";

	// Get the sql count filtered.
	$sql_count_filtered = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.`order_product_id`';
	$sql_select[] = 'SUM( `a`.`total_amount` ) AS `sum_total_amount`';
	$sql_select[] = 'SUM( `a`.`total_sales` ) AS `sum_total_sales`';
	$sql_select[] = 'SUM( `a`.`average_amount` ) AS `avg_average_amount`';
	$sql_select[] = '`orderp`.`post_title` AS `order_product_title`';
	$sql_select[] = '`relatedp`.`post_title` AS `related_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL groupby.
	$sql_groupby   = array();
	$sql_groupby[] = '`a`.`order_product_id`';
	$sql_groupby   = implode( ', ', $sql_groupby );
	$sql_groupby   = ! empty( $sql_groupby ) ? "GROUP BY {$sql_groupby}" : '';

	// Build the SQL orderby.
	$sql_orderby   = array();
	$sql_orderby[] = '`sum_total_amount` DESC';
	$sql_orderby[] = '`sum_total_sales` DESC';
	$sql_orderby[] = '`avg_average_amount` DESC';
	$sql_orderby[] = '`order_product_title` DESC';
	$sql_orderby   = implode( ', ', $sql_orderby );
	$sql_orderby   = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL limit.
	$sql_limit = PsUpsellMaster_Database::prepare( 'LIMIT %d', 10 );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_groupby} {$sql_orderby} {$sql_limit}";

	// Get the top order products.
	$top_order_products = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the top order products.
	foreach ( $top_order_products as $top_product ) {
		// Get the order product id.
		$order_product_id = isset( $top_product->order_product_id ) ? filter_var( $top_product->order_product_id, FILTER_VALIDATE_INT ) : false;
		$order_product_id = ! empty( $order_product_id ) ? $order_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $top_product->sum_total_amount ) ? filter_var( $top_product->sum_total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? round( $total_amount ) : 0;

		// Get the total sales.
		$total_sales = isset( $top_product->sum_total_sales ) ? filter_var( $top_product->sum_total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the order product title.
		$order_product_title = get_the_title( $order_product_id );

		// Define the chart item.
		$chart_item = array(
			'amount' => $total_amount,
			'sales'  => $total_sales,
			'label'  => $order_product_title,
		);

		// Add a chart item to the chart.
		array_push( $charts['order-products']['items'], $chart_item );

		// Define the chart legends.
		$chart_legends = array(
			'amount' => __( 'Total Amount', 'psupsellmaster' ),
			'sales'  => __( 'Number of Sales', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['order-products']['legends'], $chart_legends );
	}

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.`related_product_id`';
	$sql_select[] = 'SUM( `a`.`total_amount` ) AS `sum_total_amount`';
	$sql_select[] = 'SUM( `a`.`total_sales` ) AS `sum_total_sales`';
	$sql_select[] = 'SUM( `a`.`average_amount` ) AS `avg_average_amount`';
	$sql_select[] = '`orderp`.`post_title` AS `order_product_title`';
	$sql_select[] = '`relatedp`.`post_title` AS `related_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL groupby.
	$sql_groupby   = array();
	$sql_groupby[] = '`a`.`related_product_id`';
	$sql_groupby   = implode( ', ', $sql_groupby );
	$sql_groupby   = ! empty( $sql_groupby ) ? "GROUP BY {$sql_groupby}" : '';

	// Build the SQL orderby.
	$sql_orderby   = array();
	$sql_orderby[] = '`sum_total_amount` DESC';
	$sql_orderby[] = '`sum_total_sales` DESC';
	$sql_orderby[] = '`avg_average_amount` DESC';
	$sql_orderby[] = '`related_product_title` DESC';
	$sql_orderby   = implode( ', ', $sql_orderby );
	$sql_orderby   = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_groupby} {$sql_orderby} {$sql_limit}";

	// Get the top related products.
	$top_related_products = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the top related products.
	foreach ( $top_related_products as $top_product ) {
		// Get the related product id.
		$related_product_id = isset( $top_product->related_product_id ) ? filter_var( $top_product->related_product_id, FILTER_VALIDATE_INT ) : false;
		$related_product_id = ! empty( $related_product_id ) ? $related_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $top_product->sum_total_amount ) ? filter_var( $top_product->sum_total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? round( $total_amount ) : 0;

		// Get the total sales.
		$total_sales = isset( $top_product->sum_total_sales ) ? filter_var( $top_product->sum_total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the related product title.
		$related_product_title = get_the_title( $related_product_id );

		// Define the chart item.
		$chart_item = array(
			'amount' => $total_amount,
			'sales'  => $total_sales,
			'label'  => $related_product_title,
		);

		// Add a chart item to the chart.
		array_push( $charts['related-products']['items'], $chart_item );

		// Define the chart legends.
		$chart_legends = array(
			'amount' => __( 'Total Amount', 'psupsellmaster' ),
			'sales'  => __( 'Number of Sales', 'psupsellmaster' ),
		);

		// Add the legends to the chart.
		array_push( $charts['related-products']['legends'], $chart_legends );
	}

	// Build the SQL select.
	$sql_select   = array();
	$sql_select[] = '`a`.*';
	$sql_select[] = '`orderp`.`post_title` AS `order_product_title`';
	$sql_select[] = '`relatedp`.`post_title` AS `related_product_title`';
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";

	// Build the SQL orderby.
	$sql_orderby = array();

	// Check if the inputs order and orderby are not empty.
	if ( ! is_null( $input_order ) && ! is_null( $input_orderby ) ) {

		// Check if the input orderby value is 0 (Order Product).
		if ( 0 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`orderp`.`post_title` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";

			// Otherwise, check if the input orderby value is 1 (Related Product).
		} elseif ( 1 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`relatedp`.`post_title` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";

			// Otherwise, check if the input orderby value is 2 (Average Amount).
		} elseif ( 2 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";

			// Otherwise, check if the input orderby value is 3 (Total Sales).
		} elseif ( 3 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";

			// Otherwise, check if the input orderby value is 4 (Total Amount).
		} elseif ( 4 === $input_orderby ) {
			// Build the SQL orderby.
			$sql_orderby[] = "`a`.`total_amount` {$input_order}";
			$sql_orderby[] = "`a`.`total_sales` {$input_order}";
			$sql_orderby[] = "`a`.`average_amount` {$input_order}";
		}
	}

	// Build the SQL orderby.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL limit.
	$sql_limit = -1 !== $input_length ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $input_start, $input_length ) : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_orderby} {$sql_limit}";

	// Get the analytics.
	$analytics = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the analytics.
	foreach ( $analytics as $result ) {
		// Define the row.
		$row = array();

		// Get the order product id.
		$order_product_id = isset( $result->order_product_id ) ? filter_var( $result->order_product_id, FILTER_VALIDATE_INT ) : false;
		$order_product_id = ! empty( $order_product_id ) ? $order_product_id : 0;

		// Get the related product id.
		$related_product_id = isset( $result->related_product_id ) ? filter_var( $result->related_product_id, FILTER_VALIDATE_INT ) : false;
		$related_product_id = ! empty( $related_product_id ) ? $related_product_id : 0;

		// Get the total amount.
		$total_amount = isset( $result->total_amount ) ? filter_var( $result->total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? $total_amount : 0;

		// Get the total sales.
		$total_sales = isset( $result->total_sales ) ? filter_var( $result->total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Get the average amount.
		$average_amount = isset( $result->average_amount ) ? filter_var( $result->average_amount, FILTER_VALIDATE_FLOAT ) : false;
		$average_amount = ! empty( $average_amount ) ? $average_amount : 0;

		// Get the order product title.
		$order_product_title = get_the_title( $order_product_id );

		// Get the order product view url.
		$order_product_view_url = get_permalink( $order_product_id );

		// Get the order product edit url.
		$order_product_edit_url = get_edit_post_link( $order_product_id );

		// Start the buffer.
		ob_start();

		?>

		<div class="psupsellmaster-analytics-table-col">
			<div class="psupsellmaster-analytics-table-col-data">
				<a class="psupsellmaster-link" href="<?php echo esc_url( $order_product_view_url ); ?>" title="<?php echo esc_attr( $order_product_title ); ?>"><?php echo esc_html( $order_product_title ); ?></a>
			</div>
			<div class="psupsellmaster-row-actions">

				<?php if ( ! empty( $order_product_edit_url ) ) : ?>
					<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $order_product_edit_url ); ?>" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
					&nbsp;|&nbsp;
				<?php endif; ?>

				<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $order_product_view_url ); ?>" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
			</div>
		</div>

		<?php

		// Set the column.
		$column = ob_get_clean();

		// Add the column to the row.
		array_push( $row, $column );

		// Get the related product title.
		$related_product_title = get_the_title( $related_product_id );

		// Get the related product view url.
		$related_product_view_url = get_permalink( $related_product_id );

		// Get the related product edit url.
		$related_product_edit_url = get_edit_post_link( $related_product_id );

		// Start the buffer.
		ob_start();

		?>

		<div class="psupsellmaster-analytics-table-col">
			<div class="psupsellmaster-analytics-table-col-data">
				<a class="psupsellmaster-link" href="<?php echo esc_url( $related_product_view_url ); ?>" title="<?php echo esc_attr( $related_product_title ); ?>"><?php echo esc_html( $related_product_title ); ?></a>
			</div>
			<div class="psupsellmaster-row-actions">

				<?php if ( ! empty( $related_product_edit_url ) ) : ?>
					<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $related_product_edit_url ); ?>" title="<?php esc_attr_e( 'Edit', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Edit', 'psupsellmaster' ); ?></a>
					&nbsp;|&nbsp;
				<?php endif; ?>

				<a class="psupsellmaster-link psupsellmaster-row-action" href="<?php echo esc_url( $related_product_view_url ); ?>" title="<?php esc_attr_e( 'View', 'psupsellmaster' ); ?>"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
			</div>
		</div>

		<?php

		// Set the column.
		$column = ob_get_clean();

		// Add the column to the row.
		array_push( $row, $column );

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the column.
			$column = wp_strip_all_tags( wc_price( $average_amount ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the column.
			$column = edd_currency_filter( edd_format_amount( $average_amount ) );
		}

		// Add the column to the row.
		array_push( $row, $column );

		// Set the column.
		$column = $total_sales;

		// Add the column to the row.
		array_push( $row, $column );

		// Set the column.
		$column = $total_amount;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the column.
			$column = wp_strip_all_tags( wc_price( $total_amount ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the column.
			$column = edd_currency_filter( edd_format_amount( $total_amount ) );
		}

		// Add the column to the row.
		array_push( $row, $column );

		// Add the row to the data.
		array_push( $data, $row );
	}

	// Get the background process last run date for orders.
	$last_run_date = get_option( 'psupsellmaster_bp_analytics_orders_last_run' );
	$last_run_date = ! empty( $last_run_date ) ? date_i18n( get_option( 'date_format' ), $last_run_date ) : false;
	$last_run_date = ! empty( $last_run_date ) ? sprintf( '%s: %s', __( 'Last Run Date', 'psupsellmaster' ), $last_run_date ) : __( 'Unknown', 'psupsellmaster' );

	// Set the output charts.
	$output['charts'] = $charts;

	// Set the output dates.
	$output['dates'] = array( 'last_run' => $last_run_date );

	// Set the output: datatable data.
	$output['datatable']['data'] = $data;

	// Set the output: datatable draw.
	$output['datatable']['draw'] = $input_draw + 1;

	// Set the output: datatable total.
	$output['datatable']['total'] = $sql_count_all;

	// Set the output: datatable filtered.
	$output['datatable']['filtered'] = $sql_count_filtered;

	echo wp_json_encode( $output );

	exit( 0 );
}
add_action( 'wp_ajax_psupsellmaster_get_analytics_order_results', 'psupsellmaster_get_analytics_order_results' );

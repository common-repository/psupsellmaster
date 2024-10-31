<?php
/**
 * Functions - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if a campaign exists.
 *
 * @param int $id The campaign id.
 * @return bool Return true if the campaign exists, false otherwise.
 */
function psupsellmaster_campaign_exists( $id ) {
	// Set the exists.
	$exists = false;

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT 1 FROM %i WHERE `id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$id
	);

	// Get the campaign.
	$campaign = PsUpsellMaster_Database::get_var( $sql_query );

	// Check if the campaign exists.
	if ( ! empty( $campaign ) ) {
		// Set the exists.
		$exists = true;
	}

	// Return the exists.
	return $exists;
}

/**
 * Get the campaign ids and titles.
 *
 * @param array $args The arguments.
 * @return array The item pairs (ids and titles).
 */
function psupsellmaster_get_campaign_ids_titles( $args = array() ) {
	// Set the item pairs.
	$item_pairs = array();

	// Set the defaults.
	$defaults = array(
		'number' => -1,
		'search' => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the SQL select.
	$sql_select = array();

	// Add an item to the SQL select.
	array_push( $sql_select, '`campaigns`.`id`' );

	// Add an item to the SQL select.
	array_push( $sql_select, '`campaigns`.`title`' );

	// Build the SQL select.
	$sql_select = implode( ', ', $sql_select );
	$sql_select = "SELECT {$sql_select}";

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `campaigns`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Check the args.
	if ( ! empty( $parsed_args['search'] ) ) {
		// Add conditions to the SQL where.
		array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `campaigns`.`title` LIKE %s', '%' . $parsed_args['search'] . '%' ) );
	}

	// Build the SQL where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = "WHERE 1 = 1 {$sql_where}";

	// Set the SQL order by.
	$sql_order_by = array();

	// Add an item to the SQL select.
	array_push( $sql_order_by, '`campaigns`.`title`' );

	// Build the SQL order by.
	$sql_order_by = implode( ', ', $sql_order_by );
	$sql_order_by = ! empty( $sql_order_by ) ? "ORDER BY {$sql_order_by}" : '';

	// Set the SQL limit.
	$sql_limit = '';

	// Check the args.
	if ( -1 !== $parsed_args['number'] ) {
		// Set the SQL limit.
		$sql_limit = 'LIMIT ' . $parsed_args['number'];
	}

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where} {$sql_order_by} {$sql_limit}";

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign ) {
		// Get the campaign id.
		$campaign_id = isset( $campaign->id ) ? filter_var( $campaign->id, FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			// Skip this campaign.
			continue;
		}

		// Get the campaign title.
		$campaign_title = isset( $campaign->title ) ? stripslashes( $campaign->title ) : '';

		// Check if the campaign title is empty.
		if ( empty( $campaign_title ) ) {
			// Skip this campaign.
			continue;
		}

		// Set the item pairs.
		$item_pairs[ $campaign_id ] = $campaign_title;
	}

	// Return the item pairs.
	return $item_pairs;
}

/**
 * Get the coupons.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_coupons( $args = array() ) {
	// Set the data.
	$data = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the data.
		$data = psupsellmaster_woo_get_coupons( $args );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the data.
		$data = psupsellmaster_edd_get_coupons( $args );
	}

	// Return the data.
	return $data;
}

/**
 * Get the coupon label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_coupon_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'psupsellmaster_group'       => '',
		'include'                    => array(),
		'number'                     => 20,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_coupons( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'id', 'code' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the coupon code.
 *
 * @param int $coupon_id The coupon id.
 * @return string Return the coupon code.
 */
function psupsellmaster_get_coupon_code( $coupon_id ) {
	// Set the coupon code.
	$coupon_code = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the coupon code.
		$coupon_code = psupsellmaster_woo_get_coupon_code( $coupon_id );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the coupon code.
		$coupon_code = psupsellmaster_edd_get_coupon_code( $coupon_id );
	}

	// Return the coupon code.
	return $coupon_code;
}

/**
 * Get the campaign id by a coupon id.
 *
 * @param int $coupon_id The coupon id.
 * @return int|false Return the campaign id or false if no campaign is found.
 */
function psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id ) {
	// Set the where.
	$where = array( 'coupon_id' => $coupon_id );

	// Get the coupons.
	$coupons = psupsellmaster_db_campaign_coupons_select( $where );

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Set the campaign id.
	$campaign_id = isset( $coupon->campaign_id ) ? filter_var( $coupon->campaign_id, FILTER_VALIDATE_INT ) : false;

	// Return the campaign id.
	return $campaign_id;
}

/**
 * Generate an unique coupon code.
 *
 * @param string $code The desired code.
 * @return string Return the coupon code.
 */
function psupsellmaster_generate_unique_coupon_code( $code = '' ) {
	// Set the coupon code.
	$coupon_code = '';

	// Set the limit (number of attempts to generate an unique coupon code).
	$limit = 50;

	// Set the attempts.
	$attempts = 0;

	// Do while the generated coupon code exists.
	do {
		// Generate the coupon code.
		$coupon_code = 0 === $attempts ? $code : "{$code}-{$attempts}";

		// Check if the exists.
		$exists = psupsellmaster_coupon_code_exists( $coupon_code );

		// Increment the attempts.
		++$attempts;

		// Check if the coupon code exists and the attempts reached the limit.
		if ( $exists && $attempts > $limit ) {
			// Set the coupon code.
			$coupon_code = '';
		}

		// Check if the coupon code exists and the attempts didn't reach the limit.
	} while ( $exists && $attempts < $limit );

	// Return the coupon code.
	return $coupon_code;
}

/**
 * Check if a coupon code exists.
 *
 * @param string $coupon_code The coupon code.
 * @return bool Return true if the coupon code exists, false otherwise.
 */
function psupsellmaster_coupon_code_exists( $coupon_code ) {
	// Set the exists.
	$exists = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the exists.
		$exists = psupsellmaster_woo_coupon_code_exists( $coupon_code );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the exists.
		$exists = psupsellmaster_edd_coupon_code_exists( $coupon_code );
	}

	// Return the exists.
	return $exists;
}

/**
 * Get a coupon ID by code.
 *
 * @param string $coupon_code The coupon code.
 * @return int|false Return the coupon ID.
 */
function psupsellmaster_get_coupon_id_by_code( $coupon_code ) {
	// Set the coupon id.
	$coupon_id = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the coupon id.
		$coupon_id = psupsellmaster_woo_get_coupon_id_by_code( $coupon_code );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the coupon id.
		$coupon_id = psupsellmaster_edd_get_coupon_id_by_code( $coupon_code );
	}

	// Return the coupon id.
	return $coupon_id;
}

/**
 * Get the campaign products by type.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $type        The type.
 * @return array Return the campaign products.
 */
function psupsellmaster_get_campaign_products_by_type( $campaign_id, $type ) {
	// Set the products.
	$products = array();

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`product_id`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		AND
			`type` = %s
		ORDER BY
			`id` ASC
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' ),
		$campaign_id,
		$type
	);

	// Get the products.
	$products = PsUpsellMaster_Database::get_results( $query, ARRAY_A );
	$products = ! empty( $products ) ? $products : array();

	// Return the products.
	return $products;
}

/**
 * Get the campaign included products.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign included products.
 */
function psupsellmaster_get_campaign_included_products( $campaign_id ) {
	// Set the type.
	$type = 'include';

	// Get the products.
	$products = psupsellmaster_get_campaign_products_by_type( $campaign_id, $type );

	// Return the products.
	return $products;
}

/**
 * Get the campaign excluded products.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign excluded products.
 */
function psupsellmaster_get_campaign_excluded_products( $campaign_id ) {
	// Set the type.
	$type = 'exclude';

	// Get the products.
	$products = psupsellmaster_get_campaign_products_by_type( $campaign_id, $type );

	// Return the products.
	return $products;
}

/**
 * Get the campaign products.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return both the campaign included and excluded products.
 */
function psupsellmaster_get_campaign_products( $campaign_id ) {
	// Set the products.
	$products = array();

	// Set the type.
	$type = 'include';

	// Get the included products.
	$products[ $type ] = psupsellmaster_get_campaign_products_by_type( $campaign_id, $type );

	// Set the type.
	$type = 'exclude';

	// Get the excluded products.
	$products[ $type ] = psupsellmaster_get_campaign_products_by_type( $campaign_id, $type );

	// Return the products.
	return $products;
}

/**
 * Get the campaign conditions.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign conditions.
 */
function psupsellmaster_get_campaign_conditions( $campaign_id ) {
	// Set the conditions.
	$conditions = array();

	// Get the taxonomies.
	$taxonomies = psupsellmaster_get_product_taxonomies();

	// Get the stored.
	$stored = psupsellmaster_db_campaign_meta_select( $campaign_id, 'conditions', true );
	$stored = is_array( $stored ) ? $stored : array();

	// Set the keys.
	$keys = array(
		'authors',
		'products',
		'subtotal',
		'taxonomies',
	);

	// Set the types.
	$types = array(
		'include',
		'exclude',
	);

	// Loop through the keys.
	foreach ( $keys as $key ) {
		// Set the conditions.
		$conditions[ $key ] = isset( $stored[ $key ] ) ? $stored[ $key ] : array();

		// Check the key.
		if ( 'subtotal' === $key ) {
			// Set the conditions.
			$conditions[ $key ]['min'] = isset( $stored[ $key ]['min'] ) ? $stored[ $key ]['min'] : '';

			// Continue the loop.
			continue;

			// Check the key.
		} elseif ( 'products' === $key ) {
			// Set the conditions.
			$conditions[ $key ]['count']['min'] = isset( $stored[ $key ]['count'] ) && isset( $stored[ $key ]['count']['min'] ) ? $stored[ $key ]['count']['min'] : '';

			// Check the key.
		} elseif ( 'taxonomies' === $key ) {
			// Loop through the taxonomies.
			foreach ( $taxonomies as $taxonomy ) {
				// Set the taxonomy terms.
				$taxonomies_terms[ $taxonomy ] = isset( $stored[ $key ][ $taxonomy ] ) ? $stored[ $key ][ $taxonomy ] : array();

				// Loop through the types.
				foreach ( $types as $type ) {
					// Set the conditions.
					$conditions[ $key ][ $taxonomy ][ $type ] = isset( $stored[ $key ][ $taxonomy ][ $type ] ) ? $stored[ $key ][ $taxonomy ][ $type ] : array();
				}
			}

			// Continue the loop.
			continue;
		}

		// Loop through the types.
		foreach ( $types as $type ) {
			// Set the conditions.
			$conditions[ $key ][ $type ] = isset( $stored[ $key ][ $type ] ) ? $stored[ $key ][ $type ] : array();
		}
	}

	// Return the conditions.
	return $conditions;
}

/**
 * Get the campaign authors by type.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $type        The type.
 * @return array Return the campaign authors.
 */
function psupsellmaster_get_campaign_authors_by_type( $campaign_id, $type ) {
	// Set the authors.
	$authors = array();

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`author_id`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		AND
			`type` = %s
		ORDER BY
			`id` ASC
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' ),
		$campaign_id,
		$type
	);

	// Get the authors.
	$authors = PsUpsellMaster_Database::get_results( $query, ARRAY_A );
	$authors = ! empty( $authors ) ? $authors : array();

	// Return the authors.
	return $authors;
}

/**
 * Get the campaign included authors.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign included authors.
 */
function psupsellmaster_get_campaign_included_authors( $campaign_id ) {
	// Set the type.
	$type = 'include';

	// Get the authors.
	$authors = psupsellmaster_get_campaign_authors_by_type( $campaign_id, $type );

	// Return the authors.
	return $authors;
}

/**
 * Get the campaign excluded authors.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign excluded authors.
 */
function psupsellmaster_get_campaign_excluded_authors( $campaign_id ) {
	// Set the type.
	$type = 'exclude';

	// Get the authors.
	$authors = psupsellmaster_get_campaign_authors_by_type( $campaign_id, $type );

	// Return the authors.
	return $authors;
}

/**
 * Get the campaign authors.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return both the campaign included and excluded authors.
 */
function psupsellmaster_get_campaign_authors( $campaign_id ) {
	// Set the authors.
	$authors = array();

	// Set the type.
	$type = 'include';

	// Get the included authors.
	$authors[ $type ] = psupsellmaster_get_campaign_authors_by_type( $campaign_id, $type );

	// Set the type.
	$type = 'exclude';

	// Get the excluded authors.
	$authors[ $type ] = psupsellmaster_get_campaign_authors_by_type( $campaign_id, $type );

	// Return the authors.
	return $authors;
}

/**
 * Get the campaign taxonomy terms by type.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $taxonomy    The taxonomy.
 * @param string $type        The type.
 * @return array Return the campaign taxonomy terms.
 */
function psupsellmaster_get_campaign_taxonomy_terms_by_type( $campaign_id, $taxonomy, $type ) {
	// Set the taxonomy terms.
	$taxonomy_terms = array();

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`term_id`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		AND
			`taxonomy` = %s
		AND
			`type` = %s
		ORDER BY
			`id` ASC
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' ),
		$campaign_id,
		$taxonomy,
		$type
	);

	// Get the taxonomy terms.
	$taxonomy_terms = PsUpsellMaster_Database::get_results( $query, ARRAY_A );
	$taxonomy_terms = ! empty( $taxonomy_terms ) ? $taxonomy_terms : array();

	// Return the taxonomy terms.
	return $taxonomy_terms;
}

/**
 * Get the campaign included taxonomy terms.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $taxonomy The taxonomy.
 * @return array Return the campaign included taxonomy terms.
 */
function psupsellmaster_get_campaign_included_taxonomy_terms( $campaign_id, $taxonomy ) {
	// Set the type.
	$type = 'include';

	// Get the taxonomy terms.
	$taxonomy_terms = psupsellmaster_get_campaign_taxonomy_terms_by_type( $campaign_id, $taxonomy, $type );

	// Return the taxonomy terms.
	return $taxonomy_terms;
}

/**
 * Get the campaign excluded taxonomy terms.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $taxonomy The taxonomy.
 * @return array Return the campaign excluded taxonomy terms.
 */
function psupsellmaster_get_campaign_excluded_taxonomy_terms( $campaign_id, $taxonomy ) {
	// Set the type.
	$type = 'exclude';

	// Get the taxonomy terms.
	$taxonomy_terms = psupsellmaster_get_campaign_taxonomy_terms_by_type( $campaign_id, $taxonomy, $type );

	// Return the taxonomy terms.
	return $taxonomy_terms;
}

/**
 * Get the campaign taxonomy terms.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $taxonomy The taxonomy.
 * @return array Return both the campaign included and excluded taxonomy terms.
 */
function psupsellmaster_get_campaign_taxonomy_terms( $campaign_id, $taxonomy ) {
	// Set the taxonomy terms.
	$taxonomy_terms = array();

	// Set the type.
	$type = 'include';

	// Get the included taxonomy terms.
	$taxonomy_terms[ $type ] = psupsellmaster_get_campaign_taxonomy_terms_by_type( $campaign_id, $taxonomy, $type );

	// Set the type.
	$type = 'exclude';

	// Get the excluded taxonomy terms.
	$taxonomy_terms[ $type ] = psupsellmaster_get_campaign_taxonomy_terms_by_type( $campaign_id, $taxonomy, $type );

	// Return the taxonomy terms.
	return $taxonomy_terms;
}

/**
 * Get the campaign taxonomies terms.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return both the campaign included and excluded taxonomies terms.
 */
function psupsellmaster_get_campaign_taxonomies_terms( $campaign_id ) {
	// Set the taxonomies terms.
	$taxonomies_terms = array();

	// Get the taxonomies.
	$taxonomies = psupsellmaster_get_product_taxonomies();

	// Loop through the taxonomies.
	foreach ( $taxonomies as $taxonomy ) {
		// Set the taxonomy terms.
		$taxonomies_terms[ $taxonomy ] = psupsellmaster_get_campaign_taxonomy_terms( $campaign_id, $taxonomy );
	}

	// Return the taxonomies terms.
	return $taxonomies_terms;
}

/**
 * Get the campaign synced data.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign synced data.
 */
function psupsellmaster_get_campaign_synced_data( $campaign_id ) {
	// Get the synced data.
	$synced_data = psupsellmaster_db_campaign_meta_select( $campaign_id, 'synced', true );
	$synced_data = is_array( $synced_data ) ? $synced_data : array();

	// Return the synced data.
	return $synced_data;
}

/**
 * Get the campaign synced taxonomies.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign synced taxonomies.
 */
function psupsellmaster_get_campaign_synced_taxonomies( $campaign_id ) {
	// Get the synced taxonomies.
	$synced_taxonomies = psupsellmaster_get_campaign_synced_data( $campaign_id );
	$synced_taxonomies = isset( $synced_taxonomies['taxonomies'] ) ? $synced_taxonomies['taxonomies'] : array();

	// Return the synced taxonomies.
	return $synced_taxonomies;
}

/**
 * Get the campaign synced taxonomy terms.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $taxonomy The taxonomy.
 * @return array Return the campaign synced terms.
 */
function psupsellmaster_get_campaign_synced_taxonomy_terms( $campaign_id, $taxonomy ) {
	// Set the synced terms.
	$synced_terms = array();

	// Get the synced taxonomies.
	$synced_taxonomies = psupsellmaster_get_campaign_synced_taxonomies( $campaign_id );

	// Check if the taxonomy is set.
	if ( isset( $synced_taxonomies[ $taxonomy ] ) ) {
		// Set the synced terms.
		$synced_terms = is_array( $synced_taxonomies[ $taxonomy ] ) ? $synced_taxonomies[ $taxonomy ] : array();
	}

	// Return the synced terms.
	return $synced_terms;
}

/**
 * Get the campaign weekdays.
 *
 * @param int $id The campaign id.
 * @return array Return the campaign weekdays.
 */
function psupsellmaster_get_campaign_weekdays( $id ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`weekday`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' ),
		$id
	);

	// Get the weekdays.
	$weekdays = PsUpsellMaster_Database::get_col( $sql_query );
	$weekdays = is_array( $weekdays ) ? $weekdays : array();
	$weekdays = array_filter( array_unique( $weekdays ) );

	// Return the weekdays.
	return $weekdays;
}

/**
 * Get the campaign locations.
 *
 * @param int $id The campaign id.
 * @return array Return the campaign locations.
 */
function psupsellmaster_get_campaign_locations( $id ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`location`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' ),
		$id
	);

	// Get the locations.
	$locations = PsUpsellMaster_Database::get_col( $sql_query );
	$locations = is_array( $locations ) ? $locations : array();
	$locations = array_filter( array_unique( $locations ) );

	// Return the locations.
	return $locations;
}

/**
 * Check if a campaign has a location.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $location    The location.
 * @return bool Whether the campaign has the location.
 */
function psupsellmaster_campaign_has_location( $campaign_id, $location ) {
	// Set the has location.
	$has_location = false;

	// Get the locations flag.
	$locations_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'locations_flag', true );

	// Check the locations flag.
	if ( 'all' === $locations_flag ) {
		// Set the has location.
		$has_location = true;

		// Otherwise...
	} else {
		// Get the campaign locations.
		$campaign_locations = psupsellmaster_get_campaign_locations( $campaign_id );

		// Check if the location is in the campaign locations.
		if ( in_array( $location, $campaign_locations, true ) ) {
			// Set the has location.
			$has_location = true;
		}
	}

	// Return the has location.
	return $has_location;
}

/**
 * Get the campaigns in which the product is included.
 *
 * @param int $product_id The product id.
 * @return array Return the campaigns.
 */
function psupsellmaster_get_product_campaigns( $product_id ) {
	// Set the campaigns.
	$campaigns = array();

	// Set the meta key.
	$meta_key = 'products_flag';

	// Set the meta value.
	$meta_value = 'all';

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaignmeta`.`psupsellmaster_campaign_id`
		FROM
			%i AS `campaignmeta`
		WHERE
			`campaignmeta`.`meta_key` = %s
		AND
			`campaignmeta`.`meta_value` = %s
		UNION
		SELECT
			`eligible_products`.`campaign_id`
		FROM
			%i AS `eligible_products`
		WHERE
			`eligible_products`.`product_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
		$meta_key,
		$meta_value,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
		$product_id
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $query );
	$campaigns = ! empty( $campaigns ) ? $campaigns : array();

	// Return the campaigns.
	return $campaigns;
}

/**
 * Increase the quantity or insert campaign events into the database.
 *
 * @param array $args The arguments.
 * @return int|false The number of rows updated or insert, or false on failure.
 */
function psupsellmaster_increase_campaign_events_quantity( $args ) {
	// Set the affected rows.
	$affected_rows = 0;

	// Check if required data is provided.
	if (
		! isset( $args['campaign_id'] ) ||
		! isset( $args['event_date'] ) ||
		! isset( $args['event_name'] ) ||
		! isset( $args['location'] )
	) {
		// Return the affected rows.
		return $affected_rows;
	}

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;
	$campaign_id = false !== $campaign_id ? $campaign_id : 0;

	// Get the event date.
	$event_date = isset( $args['event_date'] ) ? $args['event_date'] : '';

	// Get the event name.
	$event_name = isset( $args['event_name'] ) ? $args['event_name'] : '';

	// Get the location.
	$location = isset( $args['location'] ) ? $args['location'] : '';

	// Get the quantity.
	$quantity = isset( $args['quantity'] ) ? filter_var( $args['quantity'], FILTER_VALIDATE_INT ) : false;
	$quantity = ! empty( $quantity ) ? $quantity : 1;

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`id`,
			`quantity`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		AND
			`event_name` = %s
		AND
			`location` = %s
		AND
			`event_date` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' ),
		$campaign_id,
		$event_name,
		$location,
		$event_date
	);

	// Get the row.
	$row = PsUpsellMaster_Database::get_row( $sql_query, ARRAY_A );

	// Get the row id.
	$row_id = isset( $row['id'] ) ? filter_var( $row['id'], FILTER_VALIDATE_INT ) : false;
	$row_id = false !== $row_id ? $row_id : 0;

	// Get the row quantity.
	$row_quantity = isset( $row['quantity'] ) ? filter_var( $row['quantity'], FILTER_VALIDATE_INT ) : false;
	$row_quantity = false !== $row_quantity ? $row_quantity : 0;

	// Check if the id is empty.
	if ( empty( $row_id ) ) {
		// Set the data.
		$data = array(
			'campaign_id' => $campaign_id,
			'event_date'  => $event_date,
			'event_name'  => $event_name,
			'location'    => $location,
			'quantity'    => $quantity,
		);

		// Insert the row.
		$affected_rows = psupsellmaster_db_campaign_events_insert( $data );

		// Otherwise...
	} else {
		// Increment the quantity.
		$quantity = $row_quantity + $quantity;

		// Set the data.
		$data = array( 'quantity' => $quantity );

		// Set the where.
		$where = array( 'id' => $row_id );

		// Update the row.
		$affected_rows = psupsellmaster_db_campaign_events_update( $data, $where );
	}

	// Return the affected rows.
	return $affected_rows;
}

/**
 * Get the campaign events.
 *
 * @param array $args The arguments.
 * @return array Return the campaign events.
 */
function psupsellmaster_get_campaign_events( $args = array() ) {
	// Set the events.
	$events = array();

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`id`,
			`event_date`,
			`event_name`,
			`location`,
			`quantity`
		FROM
			%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` = %d", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the start date.
		$start_date = $args['start_date'];

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_date` >= %s", $start_date );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the end date.
		$end_date = $args['end_date'];

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_date` <= %s", $end_date );
	}

	// Check if the event name is set.
	if ( isset( $args['event_name'] ) ) {
		// Set the event name.
		$event_name = $args['event_name'];

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_name` = %s", $event_name );
	}

	// Check if the location is set.
	if ( isset( $args['location'] ) ) {
		// Set the location.
		$location = $args['location'];

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `location` = %s", $location );
	}

	// Check if the quantity is set.
	if ( isset( $args['quantity'] ) ) {
		// Set the quantity.
		$quantity = $args['quantity'];

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `quantity` = %d", $quantity );
	}

	// Get the events.
	$events = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );
	$events = ! empty( $events ) ? $events : array();

	// Return the events.
	return $events;
}

/**
 * Get a campaign display option.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $location The location.
 * @param string $option_name The option name.
 * @return string Return the campaign display option.
 */
function psupsellmaster_get_campaign_display_option( $campaign_id, $location, $option_name ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`option_value`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		AND
			`location` = %s
		AND
			`option_name` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' ),
		$campaign_id,
		$location,
		$option_name
	);

	// Get the option.
	$option = PsUpsellMaster_Database::get_var( $sql_query );

	// Return the option.
	return $option;
}

/**
 * Get the campaigns count.
 *
 * @param array $args The arguments.
 * @return int Return the campaigns count.
 */
function psupsellmaster_get_campaigns_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `start_date` <= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `end_date` >= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaigns events quantity.
 *
 * @param array $args The arguments.
 * @return int Return the campaigns events quantity.
 */
function psupsellmaster_get_campaign_events_quantity( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `quantity` )
		FROM
			%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_date` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_date` <= %s", $args['end_date'] );
	}

	// Check if the event name is set.
	if ( isset( $args['event_name'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `event_name` = %s", $args['event_name'] );
	}

	// Check if the location is set.
	if ( isset( $args['location'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `location` = %s", $args['location'] );
	}

	// Check if the group by is set.
	if ( isset( $args['group_by'] ) ) {
		// Set the valid columns.
		$valid_columns = array(
			'campaign_id',
			'event_date',
			'event_name',
			'location',
		);

		// Check if the group by is not an array.
		if ( ! is_array( $args['group_by'] ) ) {
			// Set the group by columns.
			$group_by_columns = array( $args['group_by'] );
		}

		// Set the group by columns.
		$group_by_columns = array_intersect( $args['group_by'], $valid_columns );

		// Check if the group by columns is not empty.
		if ( ! empty( $group_by_columns ) ) {
			// Set the group by columns.
			$group_by_columns = implode( '`, `', $group_by_columns );

			// Set the sql query.
			$sql_query = "{$sql_query} GROUP BY `{$group_by_columns}`";
		}
	}

	// Get the quantity.
	$quantity = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$quantity = false !== $quantity ? $quantity : 0;

	// Return the quantity.
	return $quantity;
}

/**
 * Get the campaign orders count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign orders count.
 */
function psupsellmaster_get_campaign_orders_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`order_id` <> 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign free orders count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign orders count.
 */
function psupsellmaster_get_campaign_free_orders_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` = 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign paid orders count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign orders count.
 */
function psupsellmaster_get_campaign_paid_orders_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign taxes count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign taxes count.
 */
function psupsellmaster_get_campaign_taxes_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`tax` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign order taxes count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign order taxes count.
 */
function psupsellmaster_get_campaign_order_taxes_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		AND
			`tax` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign discounts count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign discounts count.
 */
function psupsellmaster_get_campaign_discounts_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`discount` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign order discounts count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign order discounts count.
 */
function psupsellmaster_get_campaign_order_discounts_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		AND
			`discount` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign subtotal amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign subtotal amount.
 */
function psupsellmaster_get_campaign_subtotal( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` )
		FROM
			%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign order subtotal amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign order subtotal amount.
 */
function psupsellmaster_get_campaign_order_subtotal( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign gross earnings amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign gross earnings amount.
 */
function psupsellmaster_get_campaign_gross_earnings( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` + `tax` )
		FROM
			%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign order gross earnings amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign order gross earnings amount.
 */
function psupsellmaster_get_campaign_order_gross_earnings( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` + `tax` )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign order taxes amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign order taxes amount.
 */
function psupsellmaster_get_campaign_order_taxes_amount( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `tax` )
		FROM
			%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign order discounts amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign order discounts amount.
 */
function psupsellmaster_get_campaign_order_discounts_amount( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `discount` )
		FROM
		%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign net earnings amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign net earnings amount.
 */
function psupsellmaster_get_campaign_net_earnings( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` - `discount` )
		FROM
		%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign order net earnings amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign order net earnings amount.
 */
function psupsellmaster_get_campaign_order_net_earnings( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `subtotal` - `discount` )
		FROM
		%i
		WHERE
			`order_id` <> 0
		AND
			`total` > 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign taxes amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign taxes amount.
 */
function psupsellmaster_get_campaign_taxes_amount( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `tax` )
		FROM
		%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign discounts amount.
 *
 * @param array $args The arguments.
 * @return float Return the campaign discounts amount.
 */
function psupsellmaster_get_campaign_discounts_amount( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `discount` )
		FROM
		%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the amount.
	$amount = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
	$amount = false !== $amount ? $amount : 0;

	// Return the amount.
	return $amount;
}

/**
 * Get the campaign cart abandoment count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign cart abandoment count.
 */
function psupsellmaster_get_campaign_carts_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
		%i
		WHERE
			1 = 1
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Get the campaign cart abandoment count.
 *
 * @param array $args The arguments.
 * @return int Return the campaign cart abandoment count.
 */
function psupsellmaster_get_campaign_cart_abandoment_count( $args = array() ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * )
		FROM
		%i
		WHERE
			`order_id` = 0
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' )
	);

	// Check if the campaign id is set.
	if ( isset( $args['cart_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['cart_id'] ) ) {
			$args['cart_id'] = array( $args['cart_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['cart_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `id` IN ( {$placeholder} )", $args['cart_id'] );
	}

	// Check if the campaign id is set.
	if ( isset( $args['campaign_id'] ) ) {
		// Check if the campaign id is not an array.
		if ( ! is_array( $args['campaign_id'] ) ) {
			$args['campaign_id'] = array( $args['campaign_id'] );
		}

		// Set the placeholder.
		$placeholder = implode( ', ', array_fill( 0, count( $args['campaign_id'] ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `campaign_id` IN ( {$placeholder} )", $args['campaign_id'] );
	}

	// Check if the start date is set.
	if ( isset( $args['start_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` >= %s", $args['start_date'] );
	}

	// Check if the end date is set.
	if ( isset( $args['end_date'] ) ) {
		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `last_modified` <= %s", $args['end_date'] );
	}

	// Get the count.
	$count = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Check if a coupon is from campaigns.
 *
 * @param string $coupon_code The coupon code.
 * @return bool Whether the coupon is from campaigns or not.
 */
function psupsellmaster_is_coupon_from_campaigns( $coupon_code ) {
	// Set the from campaigns.
	$from_campaigns = false;

	// Get the coupon id.
	$coupon_id = psupsellmaster_get_coupon_id_by_code( $coupon_code );

	// Get the campaign id.
	$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );

	// Check if the campaign id is not empty.
	if ( ! empty( $campaign_id ) ) {
		// Set the from campaigns.
		$from_campaigns = true;
	}

	// Return the from campaigns.
	return $from_campaigns;
}

/**
 * Get the campaign.
 *
 * @param int $id The campaign id.
 * @return array Return the campaign.
 */
function psupsellmaster_get_campaign( $id ) {
	// Set the campaign.
	$campaign = array();

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`title`,
			`status`,
			`priority`,
			`start_date`,
			`end_date`
		FROM
			%i
		WHERE
			`id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$id
	);

	// Get the campaign.
	$campaign = PsUpsellMaster_Database::get_row( $sql_query, ARRAY_A );

	// Return the campaign.
	return $campaign;
}

/**
 * Get the campaign coupon.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign coupon.
 */
function psupsellmaster_get_campaign_coupon( $campaign_id ) {
	// Set the campaign coupon.
	$campaign_coupon = array();

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`coupon_id`,
			`code`,
			`type`,
			`amount`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
		$campaign_id
	);

	// Get the campaign coupon.
	$campaign_coupon = PsUpsellMaster_Database::get_row( $sql_query, ARRAY_A );

	// Return the campaign coupon.
	return $campaign_coupon;
}

/**
 * Check if any of the coupons exists in any campaign.
 *
 * @param array $coupons The coupons.
 * @return bool Whether any of the coupons exists in any campaign.
 */
function psupsellmaster_campaign_coupon_exists( $coupons ) {
	// Set the exists.
	$exists = false;

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $coupons ), '%d' ) );

	// Set the sql coupons.
	$sql_coupons = PsUpsellMaster_Database::prepare( "`coupon_id` IN ( {$placeholders} )", $coupons );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			1 AS `exists`
		FROM
			%i
		WHERE
			{$sql_coupons}
		",
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' )
	);

	// Get the exists.
	$exists = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_BOOLEAN );

	// Return the exists.
	return $exists;
}

/**
 * Get the campaign coupon codes.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign coupon codes.
 */
function psupsellmaster_get_campaign_coupon_codes( $campaign_id ) {
	// Set the coupons.
	$coupons = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_woo_get_campaign_coupon_codes( $campaign_id );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_edd_get_campaign_coupon_codes( $campaign_id );
	}

	// Return the coupons.
	return $coupons;
}

/**
 * Get the campaign coupons.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign coupons.
 */
function psupsellmaster_get_campaign_coupons( $campaign_id ) {
	// Set the coupons.
	$coupons = array();

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`coupon_id`
		FROM
			%i
		WHERE
			`campaign_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
		$campaign_id
	);

	// Get the coupons.
	$coupons = PsUpsellMaster_Database::get_col( $sql_query );
	$coupons = is_array( $coupons ) ? $coupons : array();
	$coupons = array_filter( array_unique( $coupons ) );

	// Return the coupons.
	return $coupons;
}

/**
 * Get a campaigns cache value.
 *
 * @param string $cache_key The cache key.
 * @return false|array Return the cache value or false.
 */
function psupsellmaster_campaigns_get_cache( $cache_key ) {
	// Set the data.
	$data = false;

	// Get the transient.
	$transient = get_transient( $cache_key );
	$transient = is_array( $transient ) ? $transient : array();

	// Get the timestamp.
	$timestamp = isset( $transient['time'] ) ? filter_var( $transient['time'], FILTER_VALIDATE_INT ) : false;

	// Get the stored date.
	$stored_date = DateTime::createFromFormat( 'U', $timestamp, new DateTimeZone( 'UTC' ) );

	// Check if the stored date is not false.
	if ( false !== $stored_date ) {
		// Set the timezone.
		$stored_date->setTimezone( psupsellmaster_get_timezone() );

		// Get the stored date.
		$stored_date = $stored_date->format( 'Y-m-d' );
	}

	// Get the current date.
	$wp_current_date = new DateTime( 'now', psupsellmaster_get_timezone() );
	$wp_current_date = $wp_current_date->format( 'Y-m-d' );

	// Check if the dates match and the data is set.
	if ( $stored_date === $wp_current_date && isset( $transient['data'] ) ) {
		// Set the data.
		$data = $transient['data'];
	}

	// Return the data.
	return $data;
}

/**
 * Set a campaigns cache key and value.
 *
 * @param string $key The cache key.
 * @param array  $data The cache data.
 * @return bool Return true if the cache was set, false otherwise.
 */
function psupsellmaster_campaigns_set_cache( $key, $data ) {
	// Set the value.
	$value = array(
		'data' => $data,
		'time' => time(),
	);

	// Set the transient.
	$set = set_transient( $key, $value, DAY_IN_SECONDS );

	// Return the set.
	return $set;
}

/**
 * Delete a campaigns cache key and value.
 *
 * @param string $key The cache key.
 * @return bool Return true if the cache was deleted, false otherwise.
 */
function psupsellmaster_campaigns_delete_cache( $key ) {
	// Set the transient.
	$deleted = delete_transient( $key );

	// Return the deleted.
	return $deleted;
}

/**
 * Purge all campaigns caches.
 */
function psupsellmaster_campaigns_purge_caches() {
	// Allow developers to use this.
	do_action( 'psupsellmaster_campaigns_purge_caches_before' );

	// Set the keys.
	$keys = array(
		'psupsellmaster_planned_campaigns',
		'psupsellmaster_running_campaigns',
		'psupsellmaster_eligible_campaigns',
	);

	// Loop through the keys.
	foreach ( $keys as $key ) {
		// Delete the transient.
		delete_transient( $key );
	}

	// Check if the Kinsta Must-Use plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'kinsta-mu' ) ) {
		global $kinsta_muplugin;

		// Set the kinsta cache.
		$kinsta_cache = false;

		// Check if the property does exist.
		if ( property_exists( $kinsta_muplugin, 'kinsta_cache_purge' ) ) {
			// Set the kinsta cache.
			$kinsta_cache = $kinsta_muplugin->kinsta_cache_purge;
		}

		// Check if the method is callable.
		if ( is_callable( array( $kinsta_cache, 'purge_complete_caches' ) ) ) {
			// Purge the caches.
			$kinsta_cache->purge_complete_caches();
		}
	}

	// Check if the Autoptimize plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'autoptimize' ) ) {
		// Check if the method is callable.
		if ( is_callable( array( 'autoptimizeCache', 'clearall' ) ) ) {
			// Purge the caches.
			autoptimizeCache::clearall();
		}
	}

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd-advanced-shortcodes' ) ) {
		// Check if the function is callable.
		if ( is_callable( 'edd_eas_clear_all_cache' ) ) {
			// Purge the caches.
			edd_eas_clear_all_cache();
		}
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_campaigns_purge_caches_after' );
}

/**
 * Get the planned campaigns (active and scheduled statuses).
 *
 * @param bool $cache Whether or not to use cache when available. Default true.
 * @return array Return the planned campaigns.
 */
function psupsellmaster_get_planned_campaigns( $cache = true ) {
	// Check if cache is true.
	if ( true === $cache ) {
		// Get the stored cache.
		$stored_cache = psupsellmaster_campaigns_get_cache( 'psupsellmaster_planned_campaigns' );

		// Check if the stored cache is not empty.
		if ( ! empty( $stored_cache ) ) {
			// Return the stored cache.
			return $stored_cache;
		}
	}

	// Set the args.
	$args = array(
		'status' => array( 'active', 'scheduled' ),
	);

	// Get the campaigns.
	$campaigns = psupsellmaster_db_campaigns_select( $args );

	// Set the cache.
	psupsellmaster_campaigns_set_cache( 'psupsellmaster_planned_campaigns', $campaigns );

	// Return the campaigns.
	return $campaigns;
}

/**
 * Get the running campaigns.
 * It will check the status, dates, and weekdays.
 *
 * @param bool $cache Whether or not to use cache when available. Default true.
 * @return array Return the active campaigns.
 */
function psupsellmaster_get_running_campaigns( $cache = true ) {
	// Check if cache is true.
	if ( true === $cache ) {
		// Get the stored cache.
		$stored_cache = psupsellmaster_campaigns_get_cache( 'psupsellmaster_running_campaigns' );

		// Check if the stored cache is not empty.
		if ( ! empty( $stored_cache ) ) {
			// Return the stored cache.
			return $stored_cache;
		}
	}

	// Set the active campaigns.
	psupsellmaster_campaigns_set_status_active();

	// Set the scheduled campaigns.
	psupsellmaster_campaigns_set_status_scheduled();

	// Set the expired campaigns.
	psupsellmaster_campaigns_set_status_expired();

	// Set the status.
	$status = 'active';

	// Get the current date.
	$current_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$current_date = $current_date->format( 'Y-m-d H:i:s' );

	// Get the WordPress current date.
	$wp_current_date = new DateTime( 'now', psupsellmaster_get_timezone() );

	// Get the wp weekday.
	$wp_weekday = strtolower( $wp_current_date->format( 'l' ) );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaigns`.`id`
		FROM
			%i AS `campaigns`
		WHERE
			`campaigns`.`status` = %s
		AND (
			`campaigns`.`start_date` IS NULL
			OR
			`campaigns`.`start_date` <= %s
		)
		AND (
			`campaigns`.`end_date` IS NULL
			OR
			`campaigns`.`end_date` >= %s
		)
		AND (
			NOT EXISTS (
				SELECT
					1
				FROM
					%i AS `campaign_weekdays`
				WHERE
					`campaign_weekdays`.`campaign_id` = `campaigns`.`id`
			)
			OR
			EXISTS
			(
				SELECT
					1
				FROM
					%i AS `campaign_weekdays`
				WHERE
					`campaign_weekdays`.`campaign_id` = `campaigns`.`id`
				AND
					`campaign_weekdays`.`weekday` = %s
			)
		)
		ORDER BY
			`campaigns`.`priority` ASC
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status,
		$current_date,
		$current_date,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' ),
		$wp_weekday
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $sql_query );
	$campaigns = is_array( $campaigns ) ? array_map( 'absint', $campaigns ) : array();
	$campaigns = array_filter( array_unique( $campaigns ) );

	// Set the cache.
	psupsellmaster_campaigns_set_cache( 'psupsellmaster_running_campaigns', $campaigns );

	// Return the campaigns.
	return $campaigns;
}

/**
 * Check if there are overlapping dates for campaigns.
 * It will look for multiple active campaigns that would run at the same time.
 *
 * @return bool Whether campaigns have overlapping dates.
 */
function psupsellmaster_campaigns_has_overlapping_dates() {
	// Set the status.
	$status = 'active';

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			1
		FROM
			%i AS `campaigns1`
		INNER JOIN
			%i AS `campaigns2`
		ON
			`campaigns1`.`id` <> `campaigns2`.`id`
		WHERE
			`campaigns1`.`status` = %s
		AND
			`campaigns2`.`status` = %s
		AND (
			(
				`campaigns1`.`start_date` IS NULL
				AND
				`campaigns1`.`end_date` IS NULL
			)
			OR
			(
				`campaigns1`.`start_date` IS NULL
				AND
				`campaigns2`.`start_date` IS NULL
			)
			OR
			(
				`campaigns1`.`end_date` IS NULL
				AND
				`campaigns2`.`end_date` IS NULL
			)
			OR
			(
				(
					`campaigns1`.`end_date` IS NULL
					OR
					`campaigns2`.`start_date` IS NULL
				)
				AND
				`campaigns1`.`start_date` <= `campaigns2`.`end_date`
			)
			OR
			(
				`campaigns1`.`start_date` <= `campaigns2`.`end_date`
				AND
				`campaigns2`.`start_date` <= `campaigns1`.`end_date`
			)
		)
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status,
		$status
	);

	// Get the exists (check if multiple active campaigns would run at the same time).
	$exists = ! empty( PsUpsellMaster_Database::get_var( $sql_query ) );

	// Return the exists.
	return $exists;
}

/**
 * Get the coupon statuses.
 *
 * @return array The coupon statuses.
 */
function psupsellmaster_get_coupon_statuses() {
	// Set the statuses.
	$statuses = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the statuses.
		$statuses = psupsellmaster_woo_get_coupon_statuses();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the statuses.
		$statuses = psupsellmaster_edd_get_coupon_statuses();
	}

	// Return the statuses.
	return $statuses;
}

/**
 * Update the integrated coupon status.
 *
 * @param int    $coupon_id The coupon id.
 * @param string $status The coupon status.
 */
function psupsellmaster_update_integrated_coupon_status( $coupon_id, $status ) {
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Update the coupon status.
		psupsellmaster_woo_update_coupon_status( $coupon_id, $status );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Update the coupon status.
		psupsellmaster_edd_update_coupon_status( $coupon_id, $status );
	}
}

/**
 * Set the campaigns from active to expired according to the end date.
 */
function psupsellmaster_campaigns_set_status_expired() {
	// Set the status.
	$status = 'expired';

	// Set the status active.
	$status_active = 'active';

	// Get the current date.
	$current_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$current_date = $current_date->format( 'Y-m-d H:i:s' );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`id`
		FROM
			%i
		WHERE
			`status` = %s
		AND
			`end_date` < %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status_active,
		$current_date
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $sql_query );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		return false;
	}

	// Set the placeholder.
	$placeholder = implode( ', ', array_fill( 0, count( $campaigns ), '%d' ) );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( 'UPDATE %i SET `status` = %s', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ), $status );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} WHERE `id` IN ( {$placeholder} )", $campaigns );

	// Update the campaigns.
	PsUpsellMaster_Database::query( $sql_query );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_get_campaign_coupons( $campaign_id );

		// Loop through the campaign coupons.
		foreach ( $campaign_coupons as $coupon_id ) {
			// Update the coupon status.
			psupsellmaster_update_integrated_coupon_status( $coupon_id, $status );
		}
	}
}

/**
 * Set the campaigns from active to scheduled according to the start date.
 */
function psupsellmaster_campaigns_set_status_scheduled() {
	// Set the status.
	$status = 'scheduled';

	// Set the status active.
	$status_active = 'active';

	// Get the current date.
	$current_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$current_date = $current_date->format( 'Y-m-d H:i:s' );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`id`
		FROM
			%i
		WHERE
			`status` = %s
		AND
			`start_date` > %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status_active,
		$current_date
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $sql_query );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		return false;
	}

	// Set the placeholder.
	$placeholder = implode( ', ', array_fill( 0, count( $campaigns ), '%d' ) );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( 'UPDATE %i SET `status` = %s', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ), $status );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} WHERE `id` IN ( {$placeholder} )", $campaigns );

	// Update the campaigns.
	PsUpsellMaster_Database::query( $sql_query );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_get_campaign_coupons( $campaign_id );

		// Loop through the campaign coupons.
		foreach ( $campaign_coupons as $coupon_id ) {
			// Update the coupon status.
			psupsellmaster_update_integrated_coupon_status( $coupon_id, $status );
		}
	}
}

/**
 * Set the campaigns from scheduled to active according to the dates.
 * Lite: If active campaigns already exist, it will set from scheduled to inactive.
 */
function psupsellmaster_campaigns_set_status_active() {
	// Set the status.
	$status = 'active';

	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		// Check if any active campaign was found.
		if ( ! empty( psupsellmaster_db_campaigns_select( array( 'status' => $status ) ) ) ) {
			// Set the status.
			$status = 'inactive';
		}
	}

	// Set the status scheduled.
	$status_scheduled = 'scheduled';

	// Get the current date.
	$current_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$current_date = $current_date->format( 'Y-m-d H:i:s' );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`id`
		FROM
			%i
		WHERE
			`status` = %s
		AND
			`start_date` <= %s
		AND
			`end_date` >= %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status_scheduled,
		$current_date,
		$current_date
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $sql_query );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		return false;
	}

	// Set the placeholder.
	$placeholder = implode( ', ', array_fill( 0, count( $campaigns ), '%d' ) );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( 'UPDATE %i SET `status` = %s', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ), $status );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} WHERE `id` IN ( {$placeholder} )", $campaigns );

	// Update the campaigns.
	PsUpsellMaster_Database::query( $sql_query );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_get_campaign_coupons( $campaign_id );

		// Loop through the campaign coupons.
		foreach ( $campaign_coupons as $coupon_id ) {
			// Update the coupon status.
			psupsellmaster_update_integrated_coupon_status( $coupon_id, $status );
		}
	}
}

/**
 * Set the campaigns from active to inactive.
 */
function psupsellmaster_campaigns_set_status_inactive() {
	// Set the status.
	$status = 'inactive';

	// Set the status active.
	$status_active = 'active';

	// Set the data.
	$data = array( 'status' => $status );

	// Set the where.
	$where = array( 'status' => $status_active );

	// Get the campaigns.
	$campaigns = psupsellmaster_db_campaigns_select( $where );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign ) {
		// Get the campaign id.
		$campaign_id = filter_var( $campaign->id, FILTER_VALIDATE_INT );
		$campaign_id = ! empty( $campaign_id ) ? $campaign_id : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Set the where.
		$where = array( 'id' => $campaign_id );

		// Update the campaigns status.
		psupsellmaster_db_campaigns_update( $data, $where );

		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_get_campaign_coupons( $campaign_id );

		// Loop through the campaign coupons.
		foreach ( $campaign_coupons as $coupon_id ) {
			// Update the coupon status.
			psupsellmaster_update_integrated_coupon_status( $coupon_id, $status );
		}
	}
}

/**
 * Get the eligible campaigns.
 * It gets the running campaigns and discards campaigns without eligible products.
 * Also, it collects campaign-related data and returns extra data per campaign.
 *
 * @param bool $cache Whether or not to use cache when available. Default true.
 * @return array Return the eligible campaigns.
 */
function psupsellmaster_get_eligible_campaigns( $cache = true ) {
	// Check if cache is true.
	if ( true === $cache ) {
		// Get the stored cache.
		$stored_cache = psupsellmaster_campaigns_get_cache( 'psupsellmaster_eligible_campaigns' );

		// Check if the stored cache is not empty.
		if ( ! empty( $stored_cache ) ) {
			// Return the stored cache.
			return $stored_cache;
		}
	}

	// Set the campaigns.
	$campaigns = array();

	// Get the active campaigns.
	$active_campaigns = psupsellmaster_get_running_campaigns( $cache );

	// Check if the active campaigns is empty.
	if ( empty( $active_campaigns ) ) {
		// Return the campaigns.
		return $campaigns;
	}

	// Set the meta key.
	$meta_key = 'products_flag';

	// Set the meta value.
	$meta_value = 'all';

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $active_campaigns ), '%d' ) );

	// Set the sql active campaigns.
	$sql_active_campaigns = PsUpsellMaster_Database::prepare( " `campaigns`.`id` IN ( {$placeholders} ) ", $active_campaigns );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`campaigns`.`id`
		FROM
			%i AS `campaigns`
		WHERE
			{$sql_active_campaigns}
		AND EXISTS (
			SELECT
				1
			FROM
				%i AS `products`
			WHERE
				`products`.`campaign_id` = `campaigns`.`id`
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
		ORDER BY
			`campaigns`.`priority` ASC
		",
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
		$meta_key,
		$meta_value
	);

	// Get the campaigns.
	$campaigns = PsUpsellMaster_Database::get_col( $sql_query );
	$campaigns = is_array( $campaigns ) ? array_map( 'absint', $campaigns ) : array();
	$campaigns = array_filter( array_unique( $campaigns ) );

	// Set the data.
	$data = array();

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the locations flag.
		$locations_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'locations_flag', true );

		// Check if the locations flag is set to all.
		if ( 'all' === $locations_flag ) {
			// Set the locations.
			$locations = array_keys( psupsellmaster_get_product_locations() );

			// Otherwise...
		} else {
			// Get the locations.
			$locations = psupsellmaster_get_campaign_locations( $campaign_id );
		}

		// Get the products flag.
		$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

		// Set the campaign meta.
		$campaign_meta = array(
			'locations_flag' => $locations_flag,
			'products_flag'  => $products_flag,
		);

		// Get the products.
		$products = psupsellmaster_get_campaign_eligible_products( $campaign_id );

		// Get the coupon.
		$coupon = psupsellmaster_get_campaign_coupon( $campaign_id );

		// Set the coupons.
		$coupons = array( $coupon );

		// Get the conditions.
		$conditions = psupsellmaster_get_campaign_conditions( $campaign_id );

		// Get the campaign data.
		$campaign_data = psupsellmaster_get_campaign( $campaign_id );

		// Set the item.
		$item = array(
			'campaign_id'   => $campaign_id,
			'campaign_meta' => $campaign_meta,
			'title'         => $campaign_data['title'],
			'status'        => $campaign_data['status'],
			'priority'      => $campaign_data['priority'],
			'start_date'    => $campaign_data['start_date'],
			'end_date'      => $campaign_data['end_date'],
			'locations'     => $locations,
			'products'      => $products,
			'coupons'       => $coupons,
			'conditions'    => $conditions,
		);

		// Add the item to the data.
		array_push( $data, $item );
	}

	// Set the cache.
	psupsellmaster_campaigns_set_cache( 'psupsellmaster_eligible_campaigns', $data );

	// Return the data.
	return $data;
}

/**
 * Get the eligible campaigns.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the eligible campaign.
 */
function psupsellmaster_get_eligible_campaign_by_id( $campaign_id ) {
	// Set the campaign.
	$campaign = array();

	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();

	// Loop through the eligible campaigns.
	foreach ( $eligible_campaigns as $eligible_campaign ) {
		// Get the current id.
		$current_id = isset( $eligible_campaign['campaign_id'] ) ? filter_var( $eligible_campaign['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id does not match.
		if ( $campaign_id !== $current_id ) {
			continue;
		}

		// Set the campaign.
		$campaign = $eligible_campaign;
	}

	// Return the campaign.
	return $campaign;
}

/**
 * Get the eligible campaigns by location.
 *
 * @param array $args The arguments.
 * @return array Return the eligible campaigns.
 */
function psupsellmaster_get_eligible_campaigns_by_filters( $args ) {
	// Set the campaigns.
	$campaigns = array();

	// Set the filters.
	$filters = array();

	// Check the locations.
	if ( isset( $args['locations'] ) && is_array( $args['locations'] ) ) {
		// Set the filters.
		$filters['locations'] = $args['locations'];
	}

	// Check the products.
	if ( isset( $args['products'] ) && is_array( $args['products'] ) ) {
		// Set the filters.
		$filters['products'] = $args['products'];

		// Set the excluded.
		$excluded = apply_filters( 'psupsellmaster_campaigns_excluded_products', array() );

		// Check if there are excluded items within the filters.
		if ( ! empty( array_intersect( $excluded, $filters['products'] ) ) ) {
			// Return the campaigns.
			return $campaigns;
		}
	}

	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();

	// Loop through the eligible campaigns.
	foreach ( $eligible_campaigns as $eligible_campaign ) {
		// Check if the locations filter is set.
		if ( isset( $filters['locations'] ) ) {
			// Set the continue.
			$continue = false;

			// Get the locations.
			$locations = isset( $eligible_campaign['locations'] ) ? $eligible_campaign['locations'] : array();
			$locations = is_array( $locations ) ? $locations : array();

			// Loop through the filters.
			foreach ( $filters['locations'] as $location ) {
				// Check if the filter is not in the list.
				if ( ! in_array( $location, $locations, true ) ) {
					// Set the continue.
					$continue = true;

					// Break the loop.
					break;
				}
			}

			// Check if the continue is true.
			if ( $continue ) {
				// Continue the loop.
				continue;
			}
		}

		// Check if the products filter is set.
		if ( isset( $filters['products'] ) ) {
			// Get the products flag.
			$products_flag = psupsellmaster_db_campaign_meta_select( $eligible_campaign['campaign_id'], 'products_flag', true );

			// Check the products flag.
			if ( 'all' !== $products_flag ) {
				// Set the continue.
				$continue = false;

				// Get the products.
				$products = isset( $eligible_campaign['products'] ) ? $eligible_campaign['products'] : array();
				$products = is_array( $products ) ? $products : array();

				// Loop through the filters.
				foreach ( $filters['products'] as $product_id ) {
					// Check if the filter is not in the list.
					if ( ! in_array( $product_id, $products, true ) ) {
						// Set the continue.
						$continue = true;

						// Break the loop.
						break;
					}
				}

				// Check if the continue is true.
				if ( $continue ) {
					// Continue the loop.
					continue;
				}
			}
		}

		// Check if the coupons filter is set.
		if ( isset( $filters['coupons'] ) ) {
			// Set the continue.
			$continue = false;

			// Get the coupons.
			$coupons = isset( $eligible_campaign['coupons'] ) ? $eligible_campaign['coupons'] : array();
			$coupons = is_array( $coupons ) ? $coupons : array();

			// Get the coupon codes.
			$coupon_codes = array_column( $coupons, 'code' );

			// Loop through the filters.
			foreach ( $filters['coupons'] as $coupon_code ) {
				// Check if the filter is not in the list.
				if ( ! in_array( $coupon_code, $coupon_codes, true ) ) {
					// Set the continue.
					$continue = true;

					// Break the loop.
					break;
				}
			}

			// Check if the continue is true.
			if ( $continue ) {
				// Continue the loop.
				continue;
			}
		}

		// Add the eligible campaign to the campaigns.
		array_push( $campaigns, $eligible_campaign );
	}

	if ( ! isset( $filters['products'] ) ) {
		// Return the campaigns.
		return $campaigns;
	}

	// Check if no multiple campaigns were found.
	if ( in_array( count( $campaigns ), array( 0, 1 ), true ) ) {
		// Return the campaigns.
		return $campaigns;
	}

	// Set the product prices.
	$product_prices = array();

	// Loop through the products.
	foreach ( $filters['products'] as $product_id ) {
		// Set the product prices.
		$product_prices[ $product_id ] = psupsellmaster_get_product_prices( $product_id );
	}

	// Set the discounts.
	$discounts = array();

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign ) {
		// Get the campaign id.
		$campaign_id = isset( $campaign['campaign_id'] ) ? filter_var( $campaign['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Set the discounts.
		$discounts[ $campaign_id ] = 0;

		// Get the coupons.
		$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

		// Get a single coupon.
		$coupon = array_shift( $coupons );

		// Get the coupon type.
		$coupon_type = isset( $coupon['type'] ) ? $coupon['type'] : false;

		// Get the coupon amount.
		$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

		// Check if the coupon amount is empty.
		if ( empty( $coupon_amount ) ) {
			continue;
		}

		// Loop through the products.
		foreach ( $filters['products'] as $product_id ) {
			// Get the prices.
			$prices = isset( $product_prices[ $product_id ] ) ? $product_prices[ $product_id ] : array();

			// Loop through the prices.
			foreach ( $prices as $price ) {
				// Get the price amount.
				$price_amount = isset( $price['amount'] ) ? filter_var( $price['amount'], FILTER_VALIDATE_FLOAT ) : false;

				// Check if the price amount is empty.
				if ( empty( $price_amount ) ) {
					continue;
				}

				// Check the coupon type.
				if ( 'discount_percent' === $coupon_type ) {
					// Set the discount amount.
					$discount_amount = $price_amount * ( floatval( $coupon_amount ) / 100 );

					// Check the coupon type.
				} elseif ( 'discount_fixed' === $coupon_type ) {
					// Set the discount amount.
					$discount_amount = $coupon_amount;
				}

				// Check if the discount amount is empty.
				if ( empty( $discount_amount ) ) {
					continue;
				}

				// Check the discount amount.
				if ( $discount_amount < 0 ) {
					continue;
				}

				// Check the discount amount.
				if ( $discount_amount > $price ) {
					// Set the discount amount.
					$discount_amount = $price;
				}

				// Set the discounts.
				$discounts[ $campaign_id ] += $discount_amount;
			}
		}
	}

	// Sort the campaigns by discounts (higher amounts first).
	array_multisort( $discounts, SORT_DESC, $campaigns );

	// Return the campaigns.
	return $campaigns;
}

/**
 * Get the campaign eligible products.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign eligible products.
 */
function psupsellmaster_get_campaign_eligible_products( $campaign_id ) {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`products`.`product_id`
		FROM
			%i AS `products`
		WHERE
			`products`.`campaign_id` = %d
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
		$campaign_id
	);

	// Get the products.
	$products = PsUpsellMaster_Database::get_col( $sql_query );
	$products = is_array( $products ) ? array_map( 'absint', $products ) : array();
	$products = array_filter( array_unique( $products ) );

	// Return the products.
	return $products;
}

/**
 * Get the single eligible campaign by filters.
 *
 * @param array $args The arguments.
 * @return array $data The campaign data.
 */
function psupsellmaster_get_single_eligible_campaign_by_filters( $args = array() ) {
	// Set the campaign.
	$campaign = array();

	// Set the locations.
	$locations = array();

	// Set the filters.
	$filters = array();

	// Check the location.
	if ( isset( $args['locations'] ) && is_array( $args['locations'] ) ) {
		// Set the locations.
		$locations = $args['locations'];

		// Set the filters.
		$filters['locations'] = $args['locations'];
	}

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Set the sources.
	$sources = array();

	// Check the locations.
	if ( in_array( 'popup_add_to_cart', $locations, true ) ) {
		// Add a source to the list.
		array_push( $sources, 'store_cart' );

		// Check the page.
	} elseif ( psupsellmaster_is_page( 'product' ) ) {
		// Add a source to the list.
		array_push( $sources, 'page_product' );

		// Check the page.
	} elseif ( psupsellmaster_is_page( 'checkout' ) ) {
		// Add a source to the list.
		array_push( $sources, 'store_cart' );

		// Check the page.
	} elseif ( psupsellmaster_is_page( 'receipt' ) ) {
		// Add a source to the list.
		array_push( $sources, 'page_receipt' );
	}

	// Set the data.
	$data = array(
		'authors'    => array(),
		'products'   => array(),
		'taxonomies' => array(),
	);

	// Check the page.
	if ( in_array( 'store_cart', $sources, true ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_get_session_cart_author_ids();
		$author_ids = array_unique( array_filter( $author_ids ) );

		// Set the data.
		$data['authors'] = array_merge( $data['authors'], $author_ids );

		// Get the product ids.
		$product_ids = psupsellmaster_get_session_cart_product_ids();

		// Set the data.
		$data['products'] = array_merge( $data['products'], $product_ids );

		// Loop through the product taxonomies.
		foreach ( $product_taxonomies as $taxonomy ) {
			// Set the term ids.
			$term_ids = psupsellmaster_get_session_cart_term_ids( $taxonomy );

			// Set the data.
			$data['taxonomies'][ $taxonomy ] = isset( $data['taxonomies'][ $taxonomy ] ) ? $data['taxonomies'][ $taxonomy ] : array();
			$data['taxonomies'][ $taxonomy ] = array_merge( $data['taxonomies'][ $taxonomy ], $term_ids );
		}
	}

	// Check the page.
	if ( in_array( 'page_product', $sources, true ) ) {
		// Get the author ids.
		$author_ids = array( psupsellmaster_get_current_product_author_id() );
		$author_ids = array_unique( array_filter( $author_ids ) );

		// Set the data.
		$data['authors'] = array_merge( $data['authors'], $author_ids );

		// Get the product ids.
		$product_ids = array( psupsellmaster_get_current_product_id() );

		// Set the data.
		$data['products'] = array_merge( $data['products'], $product_ids );

		// Loop through the product taxonomies.
		foreach ( $product_taxonomies as $taxonomy ) {
			// Set the term ids.
			$term_ids = psupsellmaster_get_current_product_term_ids( $taxonomy );

			// Set the data.
			$data['taxonomies'][ $taxonomy ] = isset( $data['taxonomies'][ $taxonomy ] ) ? $data['taxonomies'][ $taxonomy ] : array();
			$data['taxonomies'][ $taxonomy ] = array_merge( $data['taxonomies'][ $taxonomy ], $term_ids );
		}
	}

	// Check the page.
	if ( in_array( 'page_receipt', $sources, true ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_get_receipt_author_ids();
		$author_ids = array_unique( array_filter( $author_ids ) );

		// Set the data.
		$data['authors'] = array_merge( $data['authors'], $author_ids );

		// Get the product ids.
		$product_ids = psupsellmaster_get_receipt_product_ids();

		// Set the data.
		$data['products'] = array_merge( $data['products'], $product_ids );

		// Loop through the product taxonomies.
		foreach ( $product_taxonomies as $taxonomy ) {
			// Set the term ids.
			$term_ids = psupsellmaster_get_receipt_term_ids( $taxonomy );

			// Set the data.
			$data['taxonomies'][ $taxonomy ] = isset( $data['taxonomies'][ $taxonomy ] ) ? $data['taxonomies'][ $taxonomy ] : array();
			$data['taxonomies'][ $taxonomy ] = array_merge( $data['taxonomies'][ $taxonomy ], $term_ids );
		}
	}

	// Get the campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( $filters );

	// Loop through the campaigns.
	foreach ( $campaigns as $item ) {
		// Get the campaign meta.
		$campaign_meta = isset( $item['campaign_meta'] ) ? $item['campaign_meta'] : array();

		// Check the products flag.
		if ( isset( $campaign_meta['products_flag'] ) && 'selected' === $campaign_meta['products_flag'] ) {
			// Get the products.
			$products = isset( $item['products'] ) ? $item['products'] : array();

			// Set the remove ids.
			$remove_ids = array();

			// Check the page.
			if ( psupsellmaster_is_page( 'product' ) ) {
				// Set the product ids.
				$product_ids = array( psupsellmaster_get_current_product_id() );
				$product_ids = ! empty( $product_ids ) ? $product_ids : array();

				// Merge the lists.
				$remove_ids = array_merge( $remove_ids, $product_ids );

				// Check the page.
			} elseif ( psupsellmaster_is_page( 'purchase_receipt' ) ) {
				// Set the product ids.
				$product_ids = psupsellmaster_get_receipt_product_ids();
				$product_ids = ! empty( $product_ids ) ? $product_ids : array();

				// Merge the lists.
				$remove_ids = array_merge( $remove_ids, $product_ids );
			}

			// Set the product ids.
			$product_ids = psupsellmaster_get_session_cart_product_ids();
			$product_ids = ! empty( $product_ids ) ? $product_ids : array();

			// Merge the lists.
			$remove_ids = array_merge( $remove_ids, $product_ids );

			// Set the ignore ids.
			$ignore_ids = apply_filters( 'psupsellmaster_ignore_products_id_list', array() );
			$ignore_ids = array_map( 'absint', $ignore_ids );

			// Merge the lists.
			$remove_ids = array_merge( $remove_ids, $ignore_ids );

			// Filter out empty and duplicate entries.
			$remove_ids = array_unique( array_filter( $remove_ids ) );

			// Filter out invalid products.
			$products = array_diff( $products, $remove_ids );

			// Check if the products is empty.
			if ( empty( $products ) ) {
				// Continue the loop.
				continue;
			}
		}

		// Get the conditions.
		$conditions = isset( $item['conditions'] ) ? $item['conditions'] : array();

		// Check if the conditions is empty.
		if ( empty( $conditions ) ) {
			// Set the campaign.
			$campaign = $item;

			// Stop the loop.
			break;
		}

		// Check if the included products is not empty.
		if ( ! empty( $conditions['products']['include'] ) ) {
			// Set the included.
			$included = array_column( $conditions['products']['include'], 'product_id' );

			// Set the items diff.
			$items_diff = array_diff(
				$included,
				$data['products']
			);

			// Check if data does not have all included items.
			if ( ! empty( $items_diff ) ) {
				// Continue the loop.
				continue;
			}
		}

		// Check if the excluded products is not empty.
		if ( ! empty( $conditions['products']['exclude'] ) ) {
			// Set the excluded.
			$excluded = array_column( $conditions['products']['exclude'], 'product_id' );

			// Set the items intersect.
			$items_intersect = array_intersect(
				$excluded,
				$data['products']
			);

			// Check if data has any excluded items.
			if ( ! empty( $items_intersect ) ) {
				// Continue the loop.
				continue;
			}
		}

		// Check if the included authors is not empty.
		if ( ! empty( $conditions['authors']['include'] ) ) {
			// Set the included.
			$included = array_column( $conditions['authors']['include'], 'author_id' );

			// Set the items diff.
			$items_diff = array_diff(
				$included,
				$data['authors']
			);

			// Check if data does not have all included items.
			if ( ! empty( $items_diff ) ) {
				// Continue the loop.
				continue;
			}
		}

		// Check if the excluded authors is not empty.
		if ( ! empty( $conditions['authors']['exclude'] ) ) {
			// Set the excluded.
			$excluded = array_column( $conditions['authors']['exclude'], 'author_id' );

			// Set the items intersect.
			$items_intersect = array_intersect(
				$excluded,
				$data['authors']
			);

			// Check if data has any excluded items.
			if ( ! empty( $items_intersect ) ) {
				// Continue the loop.
				continue;
			}
		}

		// Set the continue.
		$continue = false;

		// Loop through the product taxonomies.
		foreach ( $product_taxonomies as $taxonomy ) {
			// Get the taxonomy data.
			$taxonomy_data = isset( $data['taxonomies'][ $taxonomy ] ) ? $data['taxonomies'][ $taxonomy ] : array();

			// Get the taxonomy conditions.
			$taxonomy_conditions = isset( $conditions['taxonomies'][ $taxonomy ] ) ? $conditions['taxonomies'][ $taxonomy ] : array();

			// Check if the included terms is not empty.
			if ( ! empty( $taxonomy_conditions['include'] ) ) {
				// Set the included.
				$included = array_column( $taxonomy_conditions['include'], 'term_id' );

				// Set the items diff.
				$items_diff = array_diff(
					$included,
					$taxonomy_data
				);

				// Check if data does not have all included items.
				if ( ! empty( $items_diff ) ) {
					// Set the continue.
					$continue = true;

					// Stop the loop.
					break;
				}
			}

			// Check if the excluded terms is not empty.
			if ( ! empty( $taxonomy_conditions['exclude'] ) ) {
				// Set the excluded.
				$excluded = array_column( $taxonomy_conditions['exclude'], 'term_id' );

				// Set the items intersect.
				$items_intersect = array_intersect(
					$excluded,
					$taxonomy_data
				);

				// Check if data has any excluded items.
				if ( ! empty( $items_intersect ) ) {
					// Set the continue.
					$continue = true;

					// Stop the loop.
					break;
				}
			}
		}

		// Check the continue.
		if ( $continue ) {
			// Continue the loop.
			continue;
		}

		// Set the campaign.
		$campaign = $item;

		// Stop the loop.
		break;
	}

	// Return the campaign.
	return $campaign;
}

/**
 * Check if a coupon is valid.
 *
 * @param string $code The coupon code.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_is_coupon_valid( $code ) {
	// Set the is valid.
	$is_valid = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the is valid.
		$is_valid = psupsellmaster_woo_is_coupon_valid( $code );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the is valid.
		$is_valid = psupsellmaster_edd_is_coupon_valid( $code );
	}

	// Return the is valid.
	return $is_valid;
}

/**
 * Update the cart coupons based on campaigns.
 * It will remove and add coupons as per the eligible campaigns, coupons, and products.
 */
function psupsellmaster_campaigns_update_cart_coupons() {
	// Get the applied coupons (campaign coupons).
	$applied_coupons = psupsellmaster_get_applied_coupons_by_context( 'campaigns' );

	// Loop through the applied campaign coupons.
	foreach ( $applied_coupons as $coupon ) {
		// Remove the coupon.
		psupsellmaster_remove_coupon_from_cart( $coupon );
	}

	// Get the cart subtotal.
	$cart_subtotal = psupsellmaster_get_session_cart_subtotal();

	// Check if the cart subtotal is empty.
	if ( empty( $cart_subtotal ) ) {
		// Free cart, no need to apply coupons.
		return false;
	}

	// Set the valid coupons.
	$valid_coupons = array();

	// Get the cart products.
	$cart_products = psupsellmaster_get_session_cart_product_ids();

	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();

	// Loop through the eligible campaigns.
	foreach ( $eligible_campaigns as $eligible_campaign ) {
		// Get the campaign id.
		$campaign_id = isset( $eligible_campaign['campaign_id'] ) ? filter_var( $eligible_campaign['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Get the products flag.
		$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

		// Check the products flag.
		if ( 'selected' === $products_flag ) {
			// Get the eligible products.
			$eligible_products = isset( $eligible_campaign['products'] ) ? $eligible_campaign['products'] : array();

			// Check if there are no eligible products in the cart.
			if ( empty( array_intersect( $eligible_products, $cart_products ) ) ) {
				continue;
			}

			// Get the cart subtotal.
			$cart_subtotal = psupsellmaster_get_session_cart_subtotal_by_filters( array( 'products' => $eligible_products ) );

			// Check if the cart subtotal is empty.
			if ( empty( $cart_subtotal ) ) {
				continue;
			}
		}

		// Get the coupons.
		$coupons = psupsellmaster_get_campaign_coupon_codes( $campaign_id );

		// Loop through the coupons.
		foreach ( $coupons as $coupon ) {
			// Get the is valid.
			$is_valid = psupsellmaster_is_coupon_valid( $coupon );

			// Check if the coupon is not valid.
			if ( ! $is_valid ) {
				continue;
			}

			// Add the coupon to the list.
			array_push( $valid_coupons, $coupon );
		}
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the allow mix.
	$allow_mix = isset( $settings['coupons_allow_mix'] ) ? filter_var( $settings['coupons_allow_mix'], FILTER_VALIDATE_BOOLEAN ) : true;

	// Check if the valid coupons is not empty.
	if ( ! empty( $valid_coupons ) ) {
		// Check the allow mix.
		if ( false === $allow_mix ) {
			// Get the applied coupons (standard coupons).
			$applied_coupons = psupsellmaster_get_applied_coupons_by_context( 'standard' );

			// Loop through the applied campaign coupons.
			foreach ( $applied_coupons as $coupon ) {
				// Remove the coupon.
				psupsellmaster_remove_coupon_from_cart( $coupon );
			}
		}
	}

	// Remove the actions.
	remove_action( 'edd_cart_discount_set', 'psupsellmaster_edd_update_coupons_on_apply_coupon' );
	remove_action( 'woocommerce_applied_coupon', 'psupsellmaster_woo_update_coupons_on_apply_coupon' );

	// Loop through the valid coupons.
	foreach ( $valid_coupons as $coupon ) {
		// Maybe apply the coupon.
		psupsellmaster_maybe_apply_coupon_to_cart( $coupon );

		// Check if multiple coupons is not allowed.
		if ( ! psupsellmaster_is_multiple_coupons_allowed() ) {
			break;
		}
	}

	// Add the actions.
	add_action( 'edd_cart_discount_set', 'psupsellmaster_edd_update_coupons_on_apply_coupon' );
	add_action( 'woocommerce_applied_coupon', 'psupsellmaster_woo_update_coupons_on_apply_coupon' );
}
add_action( 'edd_post_add_to_cart', 'psupsellmaster_campaigns_update_cart_coupons' );
add_action( 'edd_post_remove_from_cart', 'psupsellmaster_campaigns_update_cart_coupons' );
add_action( 'edd_after_set_cart_item_quantity', 'psupsellmaster_campaigns_update_cart_coupons' );
add_action( 'woocommerce_add_to_cart', 'psupsellmaster_campaigns_update_cart_coupons', 30 );
add_action( 'woocommerce_cart_item_removed', 'psupsellmaster_campaigns_update_cart_coupons', 30 );
add_action( 'woocommerce_cart_item_set_quantity', 'psupsellmaster_campaigns_update_cart_coupons', 30 );

/**
 * Maybe remove incompatible coupons from the session cart when applying a coupon.
 *
 * @param string $coupon_code The coupon code.
 */
function psupsellmaster_maybe_remove_coupons_from_cart_on_apply_coupon( $coupon_code ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the allow mix.
	$allow_mix = isset( $settings['coupons_allow_mix'] ) ? filter_var( $settings['coupons_allow_mix'], FILTER_VALIDATE_BOOLEAN ) : true;

	// Check the allow mix.
	if ( false === $allow_mix ) {
		// Check if the coupon is from campaigns.
		if ( psupsellmaster_is_coupon_from_campaigns( $coupon_code ) ) {
			// Get the applied coupons (standard coupons).
			$applied_coupons = psupsellmaster_get_applied_coupons_by_context( 'standard' );

			// Otherwise...
		} else {
			// Get the applied coupons (campaign coupons).
			$applied_coupons = psupsellmaster_get_applied_coupons_by_context( 'campaigns' );
		}

		// Loop through the applied campaign coupons.
		foreach ( $applied_coupons as $coupon ) {
			// Remove the coupon.
			psupsellmaster_remove_coupon_from_cart( $coupon );
		}
	}

	// Get the multiple behavior.
	$multiple_behavior = isset( $settings['coupons_multiple_behavior'] ) ? $settings['coupons_multiple_behavior'] : false;

	// Check the multiple behavior.
	if ( 'campaigns' === $multiple_behavior ) {
		// Get the applied coupons (standard coupons).
		$applied_coupons = psupsellmaster_get_applied_coupons_by_context( 'standard' );

		// Get the coupon key.
		$coupon_key = array_search( $coupon_code, array_map( 'strtolower', $applied_coupons ), true );

		// Check the coupon key.
		if ( false !== $coupon_key ) {
			// Unset the item.
			unset( $applied_coupons[ $coupon_key ] );
		}

		// Loop through the applied campaign coupons.
		foreach ( $applied_coupons as $coupon ) {
			// Remove the coupon.
			psupsellmaster_remove_coupon_from_cart( $coupon );
		}
	}
}

/**
 * Check if using multiple coupons is allowed.
 *
 * @return bool Whether multiple coupons are allowed or not.
 */
function psupsellmaster_is_multiple_coupons_allowed() {
	// Set the allowed.
	$allowed = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the allowed.
		$allowed = psupsellmaster_woo_is_multiple_coupons_allowed();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the allowed.
		$allowed = psupsellmaster_edd_is_multiple_coupons_allowed();
	}

	// Allow developers to filter this.
	$allowed = apply_filters( 'psupsellmaster_is_multiple_coupons_allowed', $allowed );

	// Return the allowed.
	return $allowed;
}

/**
 * Maybe apply the coupon to the cart.
 *
 * @param string $coupon The coupon.
 * @return bool Whether the coupon was applied or not.
 */
function psupsellmaster_maybe_apply_coupon_to_cart( $coupon ) {
	// Set the applied.
	$applied = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Maybe apply the coupon.
		$applied = psupsellmaster_woo_maybe_apply_coupon_to_cart( $coupon );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Maybe apply the coupon.
		$applied = psupsellmaster_edd_maybe_apply_coupon_to_cart( $coupon );
	}

	// Return the applied.
	return $applied;
}

/**
 * Check if the cart has the coupon.
 *
 * @param string $coupon The coupon.
 * @return bool Whether the cart has the coupon or not.
 */
function psupsellmaster_cart_has_coupon( $coupon ) {
	// Set the has coupon.
	$has_coupon = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the has coupon.
		$has_coupon = psupsellmaster_woo_cart_has_coupon( $coupon );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the has coupon.
		$has_coupon = psupsellmaster_edd_cart_has_coupon( $coupon );
	}

	// Return the has coupon.
	return $has_coupon;
}

/**
 * Remove the coupon from the cart.
 *
 * @param string $coupon The coupon.
 */
function psupsellmaster_remove_coupon_from_cart( $coupon ) {
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Remove the coupon.
		psupsellmaster_woo_remove_coupon_from_cart( $coupon );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Remove the coupon.
		psupsellmaster_edd_remove_coupon_from_cart( $coupon );
	}
}

/**
 * Get the applied coupons.
 *
 * @return array The applied coupons.
 */
function psupsellmaster_get_applied_coupons() {
	// Set the coupons.
	$coupons = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_woo_get_applied_coupons();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_edd_get_applied_coupons();
	}

	// Return the coupons.
	return $coupons;
}

/**
 * Get the applied coupons.
 *
 * @param string $context The context.
 * @return array The applied coupons.
 */
function psupsellmaster_get_applied_coupons_by_context( $context = 'all' ) {
	// Set the coupons.
	$coupons = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_woo_get_applied_coupons_by_context( $context );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the coupons.
		$coupons = psupsellmaster_edd_get_applied_coupons_by_context( $context );
	}

	// Return the coupons.
	return $coupons;
}

/**
 * Register the taxonomies.
 */
function psupsellmaster_register_taxonomies() {
	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Set the post types.
	$post_types = array( $product_post_type );

	// Set the taxonomy.
	$taxonomy = 'psupsellmaster_product_tag';

	// Set the labels.
	$labels = array(
		'name'          => esc_html__( 'UpsellMaster Tags', 'psupsellmaster' ),
		'singular_name' => esc_html__( 'UpsellMaster Tag', 'psupsellmaster' ),
	);

	// Set the args.
	$args = array(
		'label'              => esc_html__( 'UpsellMaster Tags', 'psupsellmaster' ),
		'labels'             => $labels,
		'public'             => false,
		'hierarchical'       => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => false,
	);

	// Register the taxonomy.
	register_taxonomy( $taxonomy, $post_types, $args );

	// Register the taxonomy for the product post type.
	register_taxonomy_for_object_type( $taxonomy, $product_post_type );
}
add_action( 'init', 'psupsellmaster_register_taxonomies' );

/**
 * Get the banner data by id.
 *
 * @param int $banner_id The banner id.
 * @return array The banner data.
 */
function psupsellmaster_get_campaign_banner_data_by_id( $banner_id ) {
	// Set the data.
	$data = array(
		'alt'    => '',
		'height' => '',
		'name'   => '',
		'title'  => '',
		'width'  => '',
	);

	// Get the metadata.
	$metadata = wp_get_attachment_metadata( $banner_id );

	// Set the title.
	$data['title'] = get_the_title( $banner_id );

	// Set the url.
	$data['url'] = wp_get_attachment_url( $banner_id );

	// Set the name.
	$data['name'] = wp_basename( get_attached_file( $banner_id ) );

	// Set the alt.
	$data['alt'] = ! empty( $metadata['alt'] ) ? $metadata['alt'] : '';

	// Set the height.
	$data['height'] = ! empty( $metadata['height'] ) ? $metadata['height'] : '';

	// Set the width.
	$data['width'] = ! empty( $metadata['width'] ) ? $metadata['width'] : '';

	// Return the data.
	return $data;
}

/**
 * Render the campaign banner images.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_images( $args ) {
	// Get the banners.
	$banners = isset( $args['banners'] ) ? $args['banners'] : false;
	$banners = is_array( $banners ) ? $banners : array();

	// Check if the banners is emtpy.
	if ( empty( $banners ) ) {
		return;
	}

	// Set the attrs.
	$attrs = array( 'class' => 'attachment-full size-full psupsellmaster-image' );

	// Set the desktop image html.
	$banners['desktop']['image_html'] = wp_get_attachment_image( $banners['desktop']['id'], 'full', false, $attrs );

	// Set the mobile image html.
	$banners['mobile']['image_html'] = wp_get_attachment_image( $banners['mobile']['id'], 'full', false, $attrs );
	?>
	<?php if ( ! empty( $banners['desktop']['image_html'] ) || ! empty( $banners['mobile']['image_html'] ) ) : ?>
		<div class="psupsellmaster-campaign-banners">
			<?php if ( ! empty( $banners['desktop']['image_html'] ) ) : ?>
				<div class="psupsellmaster-banner-wrapper psupsellmaster-banner-desktop">
					<?php if ( ! empty( $banners['desktop']['link_url'] ) ) : ?>
						<a href="<?php echo esc_url( $banners['desktop']['link_url'] ); ?>" target="_blank">
							<?php echo wp_kses_post( $banners['desktop']['image_html'] ); ?>
						</a>
					<?php else : ?>
						<?php echo wp_kses_post( $banners['desktop']['image_html'] ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $banners['mobile']['image_html'] ) ) : ?>
				<div class="psupsellmaster-banner-wrapper psupsellmaster-banner-mobile">
					<?php if ( ! empty( $banners['mobile']['link_url'] ) ) : ?>
						<a href="<?php echo esc_url( $banners['mobile']['link_url'] ); ?>" target="_blank">
							<?php echo wp_kses_post( $banners['mobile']['image_html'] ); ?>
						</a>
					<?php else : ?>
						<?php echo wp_kses_post( $banners['mobile']['image_html'] ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php
}

/**
 * Render the campaign banner images from locations.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_images_from_locations( $args ) {
	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return;
	}

	// Get the location.
	$location = isset( $args['location'] ) ? $args['location'] : '';

	// Check the location.
	if ( empty( $location ) ) {
		return;
	}

	// Set the banners.
	$banners = array(
		'desktop' => array(
			'id'       => false,
			'link_url' => false,
		),
		'mobile'  => array(
			'id'       => false,
			'link_url' => false,
		),
	);

	// Loop through the banners.
	foreach ( $banners as $type => $banner ) {
		// Set the banner id.
		$banners[ $type ]['id'] = psupsellmaster_get_campaign_display_option( $campaign_id, $location, "{$type}_banner_id" );

		// Check if the banner id is empty.
		if ( empty( $banners[ $type ]['id'] ) ) {
			// Set the banner id.
			$banners[ $type ]['id'] = psupsellmaster_get_campaign_display_option( $campaign_id, 'all', "{$type}_banner_id" );
		}

		// Set the banner link url.
		$banners[ $type ]['link_url'] = psupsellmaster_get_campaign_display_option( $campaign_id, $location, "{$type}_banner_link_url" );

		// Check if the banner link url is empty.
		if ( empty( $banners[ $type ]['link_url'] ) ) {
			// Set the banner link url.
			$banners[ $type ]['link_url'] = psupsellmaster_get_campaign_display_option( $campaign_id, 'all', "{$type}_banner_link_url" );
		}
	}

	// Render the campaign banner images.
	psupsellmaster_render_campaign_banner_images( array( 'banners' => $banners ) );
}

/**
 * Render the campaign banner images from page.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_images_from_page( $args ) {
	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return;
	}

	// Get the meta value.
	$meta_value = psupsellmaster_db_campaign_meta_select( $campaign_id, 'product_page', true );
	$meta_value = is_array( $meta_value ) ? $meta_value : array();

	// Set the banners.
	$banners = array(
		'desktop' => array(
			'id'       => false,
			'link_url' => false,
		),
		'mobile'  => array(
			'id'       => false,
			'link_url' => false,
		),
	);

	// Loop through the banners.
	foreach ( $banners as $type => $banner ) {
		// Set the data key.
		$data_key = "{$type}_banner_id";

		// Set the banner id.
		$banners[ $type ]['id'] = isset( $meta_value[ $data_key ] ) ? $meta_value[ $data_key ] : '';

		// Set the data key.
		$data_key = "{$type}_banner_link_url";

		// Set the banner link url.
		$banners[ $type ]['link_url'] = isset( $meta_value[ $data_key ] ) ? $meta_value[ $data_key ] : '';
	}

	// Render the campaign banner images.
	psupsellmaster_render_campaign_banner_images( array( 'banners' => $banners ) );
}

/**
 * Render the campaign banner texts.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_texts( $args ) {
	// Get the description.
	$description = isset( $args['description'] ) ? $args['description'] : '';
	?>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="psupsellmaster-campaign-texts">
			<?php if ( ! empty( $description ) ) : ?>
				<div class="psupsellmaster-text psupsellmaster-description"><?php echo wp_kses_post( wpautop( stripslashes( $description ) ) ); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php
}

/**
 * Render the campaign banner texts from page.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_texts_from_page( $args ) {
	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return;
	}

	// Get the meta value.
	$meta_value = psupsellmaster_db_campaign_meta_select( $campaign_id, 'product_page', true );
	$meta_value = is_array( $meta_value ) ? $meta_value : array();

	// Get the description.
	$description = isset( $meta_value['description'] ) ? $meta_value['description'] : '';

	// Check the description.
	if ( empty( $description ) ) {
		return;
	}

	// Render the campaign banner texts.
	psupsellmaster_render_campaign_banner_texts( array( 'description' => $description ) );
}

/**
 * Render the campaign banner texts from locations.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_campaign_banner_texts_from_locations( $args ) {
	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return;
	}

	// Get the location.
	$location = isset( $args['location'] ) ? $args['location'] : '';

	// Check the location.
	if ( empty( $location ) ) {
		return;
	}

	// Get the description.
	$description = psupsellmaster_get_campaign_display_option( $campaign_id, $location, 'description' );

	// Check if the description is empty.
	if ( empty( $description ) ) {
		// Get the description.
		$description = psupsellmaster_get_campaign_display_option( $campaign_id, 'all', 'description' );
	}

	// Check the description.
	if ( empty( $description ) ) {
		return;
	}

	// Render the campaign banner texts.
	psupsellmaster_render_campaign_banner_texts( array( 'description' => $description ) );
}

/**
 * Generate a campaign cart key.
 *
 * @return string The cart key.
 */
function psupsellmaster_generate_campaign_cart_key() {
	// Set the cart key length.
	$cart_key_length = 32;

	// Set the characters allowed in the cart key.
	$allowed_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	// Generate a random cart key.
	$cart_key = substr( str_shuffle( $allowed_characters ), 0, $cart_key_length );

	// Return the cart key.
	return $cart_key;
}

/**
 * Check if a campaign cart key exists.
 *
 * @param string $cart_key The cart key.
 * @return bool Whether the cart key exists.
 */
function psupsellmaster_campaign_cart_key_exists( $cart_key ) {
	// Set the exists.
	$exists = false;

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			1
		FROM
			%i
		WHERE
			cart_key = %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' ),
		$cart_key
	);

	// Get the cart key.
	$cart_key = PsUpsellMaster_Database::get_var( $sql_query );

	// Check if the cart key exists.
	if ( ! empty( $cart_key ) ) {
		// Set the exists.
		$exists = true;
	}

	// Return the exists.
	return $exists;
}

/**
 * Generate a unique campaign cart key.
 *
 * @return string The campaign cart key.
 */
function psupsellmaster_generate_unique_campaign_cart_key() {
	// Set the cart key.
	$cart_key = '';

	// Set the limit (number of attempts to generate a unique cart key).
	$limit = 50;

	// Set the attempts.
	$attempts = 0;

	// Do while the generated cart key exists.
	do {
		// Generate the cart key.
		$cart_key = psupsellmaster_generate_campaign_cart_key();

		// Check if the cart key exists.
		$exists = psupsellmaster_campaign_cart_key_exists( $cart_key );

		// Increment the attempts.
		++$attempts;

		// Check if the cart key exists and the attempts didn't reach the limit.
	} while ( $exists && $attempts < $limit );

	// Return the cart key.
	return $cart_key;
}

/**
 * Adds a campaign click event according to the query parameters.
 */
function psupsellmaster_campaigns_wp_click() {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Check if the main query is not for a product.
	if ( ! is_singular( $post_type ) ) {
		return false;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return false;
	}

	// Set the prefix.
	$prefix = 'psupsellmaster_';

	// Set the campaign id.
	$campaign_id = false;

	// Set the location.
	$location = '';

	// Check if the campaign id is not empty.
	if ( ! empty( $_GET[ "{$prefix}campaign_id" ] ) ) {
		// Get the campaign id.
		$campaign_id = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}campaign_id" ] ) );
		$campaign_id = filter_var( $campaign_id, FILTER_VALIDATE_INT );
	}

	// Check if the location is not empty.
	if ( ! empty( $_GET[ "{$prefix}location" ] ) ) {
		// Set the location.
		$location = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}location" ] ) );
	}

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return false;
	}

	// Set the event date.
	$event_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$event_date = $event_date->format( 'Y-m-d' );

	// Set the event data.
	$event_data = array(
		'campaign_id' => $campaign_id,
		'event_date'  => $event_date,
		'event_name'  => 'click',
		'location'    => $location,
	);

	// Increment the campaign events quantity.
	psupsellmaster_increase_campaign_events_quantity( $event_data );
}
add_action( 'wp', 'psupsellmaster_campaigns_wp_click' );

/**
 * Update the campaign cart in the database based on the current session cart.
 */
function psupsellmaster_update_current_campaign_cart() {
	// Get the cart key.
	$cart_key = psupsellmaster_session_get( 'cart_key' );

	// Check if the cart key is empty.
	if ( empty( $cart_key ) ) {
		// Generate a cart key.
		$cart_key = psupsellmaster_generate_unique_campaign_cart_key();

		// Set the cart key.
		psupsellmaster_session_set( 'cart_key', $cart_key );
	}

	// Check if the cart key is empty.
	if ( empty( $cart_key ) ) {
		return false;
	}

	// Set the found.
	$found = false;

	// Set the campaign id.
	$campaign_id = false;

	// Get the cart products.
	$cart_products = psupsellmaster_get_session_cart_product_ids();

	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();

	// Loop through the eligible campaigns.
	foreach ( $eligible_campaigns as $campaign ) {
		// Get the campaign id.
		$campaign_id = isset( $campaign['campaign_id'] ) ? filter_var( $campaign['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Get the products flag.
		$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

		// Check the products flag.
		if ( 'all' === $products_flag ) {
			// Set the found.
			$found = true;

			// Stop the loop.
			break;
		}

		// Get the campaign products.
		$campaign_products = isset( $campaign['products'] ) ? $campaign['products'] : array();

		// Check if any of the cart products is in the campaign products.
		if ( ! empty( array_intersect( $cart_products, $campaign_products ) ) ) {
			// Set the found.
			$found = true;

			// Stop the loop.
			break;
		}
	}

	// Set the data.
	$where = array(
		'cart_key' => $cart_key,
		'order_id' => 0,
	);

	// Delete the cart.
	psupsellmaster_db_campaign_carts_delete( $where );

	// Check if the found is empty.
	if ( empty( $found ) ) {
		return false;
	}

	// Get the cart data.
	$cart_data = psupsellmaster_get_session_cart_data();

	// Get the quantity.
	$quantity = isset( $cart_data['quantity'] ) ? filter_var( $cart_data['quantity'], FILTER_VALIDATE_INT ) : false;
	$quantity = false !== $quantity ? $quantity : 0;

	// Check if the quantity is empty.
	if ( empty( $quantity ) ) {
		return false;
	}

	// Get the subtotal.
	$subtotal = isset( $cart_data['subtotal'] ) ? filter_var( $cart_data['subtotal'], FILTER_VALIDATE_FLOAT ) : false;
	$subtotal = false !== $subtotal ? $subtotal : 0;

	// Get the discount.
	$discount = isset( $cart_data['discount'] ) ? filter_var( $cart_data['discount'], FILTER_VALIDATE_FLOAT ) : false;
	$discount = false !== $discount ? $discount : 0;

	// Get the tax.
	$tax = isset( $cart_data['tax'] ) ? filter_var( $cart_data['tax'], FILTER_VALIDATE_FLOAT ) : false;
	$tax = false !== $tax ? $tax : 0;

	// Get the total.
	$total = isset( $cart_data['total'] ) ? filter_var( $cart_data['total'], FILTER_VALIDATE_FLOAT ) : false;
	$total = false !== $total ? $total : 0;

	// Set the current time.
	$current_time = current_time( 'mysql', true );

	// Set the data.
	$data = array(
		'cart_key'      => $cart_key,
		'campaign_id'   => $campaign_id,
		'last_modified' => $current_time,
		'quantity'      => $quantity,
		'subtotal'      => $subtotal,
		'discount'      => $discount,
		'tax'           => $tax,
		'total'         => $total,
	);

	// Insert the cart.
	psupsellmaster_db_campaign_carts_insert( $data );
}

/**
 * Clear the campaigns-related cart session.
 */
function psupsellmaster_campaigns_session_clear() {
	psupsellmaster_session_set( 'cart_key', null );
}

/**
 * Render the campaign banner data from locations.
 *
 * @param array $args The arguments.
 * @return string The output.
 */
function psupsellmaster_render_campaign_banner_data_from_locations( $args ) {
	// Set the output.
	$output = '';

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the output.
		return $output;
	}

	// Get the locations.
	$locations = psupsellmaster_get_product_locations();
	$locations = array_keys( $locations );

	// Get the location.
	$location = isset( $args['location'] ) && in_array( $args['location'], $locations, true ) ? $args['location'] : 'all';

	// Check if the location is empty.
	if ( empty( $location ) ) {
		// Return the output.
		return $output;
	}

	// Set the render options.
	$render_options = array( 'images', 'texts' );

	// Get the render.
	$render = isset( $args['render'] ) && in_array( $args['render'], $render_options, true ) ? $args['render'] : 'all';

	// Set the images.
	$images = '';

	// Check the render.
	if ( in_array( $render, array( 'all', 'images' ), true ) ) {
		// Start the buffer.
		ob_start();

		// Set the banner args.
		$args_banner = array(
			'campaign_id' => $campaign_id,
			'location'    => $location,
		);

		// Render the campaign images.
		psupsellmaster_render_campaign_banner_images_from_locations( $args_banner );

		// Get the images.
		$images = ob_get_clean();
	}

	// Set the texts.
	$texts = '';

	// Check the render.
	if ( in_array( $render, array( 'all', 'texts' ), true ) ) {
		// Start the buffer.
		ob_start();

		// Set the banner args.
		$args_banner = array(
			'campaign_id' => $campaign_id,
			'location'    => $location,
		);

		// Render the campaign texts.
		psupsellmaster_render_campaign_banner_texts_from_locations( $args_banner );

		// Get the texts.
		$texts = ob_get_clean();
	}

	// Set the data.
	$data = array( trim( $images, "\t\n\r\0\x0B" ), trim( $texts, "\t\n\r\0\x0B" ) );

	// Check if there is data to render.
	if ( empty( array_filter( $data ) ) ) {
		// Return the output.
		return $output;
	}
	?>
	<div class="psupsellmaster-campaign-banner-data">
		<?php echo wp_kses_post( $images ); ?>
		<?php echo wp_kses_post( $texts ); ?>
	</div>
	<?php
}

/**
 * Render the campaign banner data from page.
 *
 * @param array $args The arguments.
 * @return string The output.
 */
function psupsellmaster_render_campaign_banner_data_from_page( $args ) {
	// Set the output.
	$output = '';

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the output.
		return $output;
	}

	// Set the render options.
	$render_options = array( 'images', 'texts' );

	// Get the render.
	$render = isset( $args['render'] ) && in_array( $args['render'], $render_options, true ) ? $args['render'] : 'all';

	// Set the images.
	$images = '';

	// Check the render.
	if ( in_array( $render, array( 'all', 'images' ), true ) ) {
		// Start the buffer.
		ob_start();

		// Render the campaign images.
		psupsellmaster_render_campaign_banner_images_from_page( array( 'campaign_id' => $campaign_id ) );

		// Get the images.
		$images = ob_get_clean();
	}

	// Set the texts.
	$texts = '';

	// Check the render.
	if ( in_array( $render, array( 'all', 'texts' ), true ) ) {
		// Start the buffer.
		ob_start();

		// Render the campaign texts.
		psupsellmaster_render_campaign_banner_texts_from_page( array( 'campaign_id' => $campaign_id ) );

		// Get the texts.
		$texts = ob_get_clean();
	}

	// Set the data.
	$data = array( trim( $images, "\t\n\r\0\x0B" ), trim( $texts, "\t\n\r\0\x0B" ) );

	// Check if there is data to render.
	if ( empty( array_filter( $data ) ) ) {
		// Return the output.
		return $output;
	}
	?>
	<div class="psupsellmaster-campaign-banner-data">
		<?php echo wp_kses_post( $images ); ?>
		<?php echo wp_kses_post( $texts ); ?>
	</div>
	<?php
}

/**
 * Render the single eligible campaign banner data from page.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_render_single_eligible_campaign_banner_data_from_page( $args ) {
	// Get the campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( $args );
	$campaigns = array_column( $campaigns, 'campaign_id' );

	// Get the campaign id.
	$campaign_id = array_shift( $campaigns );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return;
	}

	// Render the campaign banner data.
	psupsellmaster_render_campaign_banner_data_from_page( array( 'campaign_id' => $campaign_id ) );
}

/**
 * Get the campaign products by selectors.
 *
 * @param int $campaign_id The campaign id.
 * @return array The products.
 */
function psupsellmaster_campaigns_get_products_by_selectors( $campaign_id ) {
	// Set the args.
	$args_selectors = array(
		'options' => array(),
	);

	// Get the campaign authors.
	$campaign_authors = psupsellmaster_get_campaign_authors( $campaign_id );

	// Set the args.
	$args_selectors['options']['authors'] = array(
		'include' => array_column( $campaign_authors['include'], 'author_id' ),
		'exclude' => array_column( $campaign_authors['exclude'], 'author_id' ),
	);

	// Get the campaign products.
	$campaign_products = psupsellmaster_get_campaign_products( $campaign_id );

	// Set the args.
	$args_selectors['options']['products'] = array(
		'include' => array_column( $campaign_products['include'], 'product_id' ),
		'exclude' => array_column( $campaign_products['exclude'], 'product_id' ),
	);

	// Get the campaign taxonomies terms.
	$campaign_taxonomies_terms = psupsellmaster_get_campaign_taxonomies_terms( $campaign_id );

	// Loop through the campaign taxonomies terms.
	foreach ( $campaign_taxonomies_terms as $taxonomy => $terms ) {
		// Set the args.
		$args_selectors['options']['taxonomies'][ $taxonomy ] = array(
			'include' => array_column( $terms['include'], 'term_id' ),
			'exclude' => array_column( $terms['exclude'], 'term_id' ),
		);
	}

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the products type.
		$products_type = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_type', true );

		// Check the products type.
		if ( 'bundle' === $products_type ) {
			// Set the args.
			$args_selectors['options']['products_type'] = $products_type;
		}
	}

	// Get the products.
	$products = psupsellmaster_get_products_by_selectors( $args_selectors );

	// Return the products.
	return $products;
}

/**
 * Update the campaigns eligible products.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_campaigns_update_eligible_products( $args = array() ) {
	// Get the campaigns.
	$campaigns = isset( $args['campaigns'] ) ? $args['campaigns'] : false;
	$campaigns = is_array( $campaigns ) ? array_map( 'absint', $campaigns ) : array();
	$campaigns = array_unique( array_filter( $campaigns ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Set the campaigns.
		$campaigns = psupsellmaster_get_running_campaigns( false );
	}

	// Get the products.
	$products = isset( $args['products'] ) ? $args['products'] : false;
	$products = is_array( $products ) ? array_map( 'absint', $products ) : array();
	$products = array_unique( array_filter( $products ) );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the products flag.
		$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

		// Check the products flag.
		if ( 'all' === $products_flag ) {
			// Continue the loop.
			continue;
		}

		// Get the eligible products.
		$eligible_products = psupsellmaster_campaigns_get_products_by_selectors( $campaign_id );

		// Set the to remove.
		$to_remove = array_diff( $products, $eligible_products );

		// Loop through the to remove.
		foreach ( $to_remove as $product_id ) {
			// Set the where.
			$where = array(
				'campaign_id' => $campaign_id,
				'product_id'  => $product_id,
			);

			// Delete the eligible products.
			psupsellmaster_db_campaign_eligible_products_delete( $where );
		}

		// Set the to insert.
		$to_insert = array_intersect( $products, $eligible_products );

		// Check if the to insert is empty.
		if ( empty( $to_insert ) ) {
			// Skip.
			continue;
		}

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			'
			SELECT
				`id`
			FROM
				%i
			WHERE
				`campaign_id` = %d
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
			$campaign_id
		);

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $to_insert ), '%d' ) );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare( "{$sql_query} AND `product_id` IN ( {$placeholders} )", $to_insert );

		// Get the already exists.
		$already_exists = PsUpsellMaster_Database::get_col( $sql_query );
		$already_exists = is_array( $already_exists ) ? array_map( 'absint', $already_exists ) : array();
		$already_exists = array_filter( array_unique( $already_exists ) );

		// Set the to insert.
		$to_insert = array_diff( $to_insert, $already_exists );

		// Check if the to insert is empty.
		if ( empty( $to_insert ) ) {
			// Skip.
			continue;
		}

		// Set the placeholders.
		// At the end, this will create a string format like '( %d, %d ), ( %d, %d ), ( %d, %d )'...
		$placeholders = implode( ' ), ( ', array_fill( 0, count( $to_insert ), '%d, #1#' ) );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $to_insert ), $campaign_id ) );

		// Set the placeholders.
		$placeholders = str_replace( '#1#', '%d', $sql_values );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $to_insert );

		// Set the sql insert.
		$sql_insert = PsUpsellMaster_Database::prepare(
			"
			INSERT INTO %i
				( `campaign_id`, `product_id` )
			VALUES
				( {$sql_values} )
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' )
		);

		// Insert the rows.
		PsUpsellMaster_Database::query( $sql_insert );
	}
}

/**
 * Update all the campaigns eligible products.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_campaigns_update_all_eligible_products( $args = array() ) {
	// Get the campaigns.
	$campaigns = isset( $args['campaigns'] ) ? $args['campaigns'] : false;
	$campaigns = is_array( $campaigns ) ? array_map( 'absint', $campaigns ) : array();
	$campaigns = array_unique( array_filter( $campaigns ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Set the campaigns.
		$campaigns = psupsellmaster_get_running_campaigns( false );
	}

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Delete the eligible products.
		psupsellmaster_db_campaign_eligible_products_delete( array( 'campaign_id' => $campaign_id ) );

		// Set the to insert.
		$to_insert = psupsellmaster_campaigns_get_products_by_selectors( $campaign_id );

		// Check if the to insert is empty.
		if ( empty( $to_insert ) ) {
			// Skip.
			continue;
		}

		// Set the placeholders.
		// At the end, this will create a string format like '( %d, %d ), ( %d, %d ), ( %d, %d )'...
		$placeholders = implode( ' ), ( ', array_fill( 0, count( $to_insert ), '%d, #1#' ) );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $to_insert ), $campaign_id ) );

		// Set the placeholders.
		$placeholders = str_replace( '#1#', '%d', $sql_values );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $to_insert );

		// Set the sql insert.
		$sql_insert = PsUpsellMaster_Database::prepare(
			"
			INSERT INTO %i
				( `campaign_id`, `product_id` )
			VALUES
				( {$sql_values} )
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' )
		);

		// Insert the rows.
		PsUpsellMaster_Database::query( $sql_insert );
	}
}

/**
 * Update the campaigns eligible products.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_campaigns_sync_eligible_products( $args = array() ) {
	// Get the campaigns.
	$campaigns = isset( $args['campaigns'] ) ? $args['campaigns'] : false;
	$campaigns = is_array( $campaigns ) ? array_map( 'absint', $campaigns ) : array();
	$campaigns = array_unique( array_filter( $campaigns ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Set the campaigns.
		$campaigns = psupsellmaster_get_running_campaigns( false );
	}

	// Get the products.
	$products = isset( $args['products'] ) ? $args['products'] : false;
	$products = is_array( $products ) ? array_map( 'absint', $products ) : array();
	$products = array_unique( array_filter( $products ) );

	// Loop through the campaigns.
	foreach ( $campaigns as $campaign_id ) {
		// Get the synced.
		$synced = psupsellmaster_db_campaign_meta_select( $campaign_id, 'synced', true );

		// Check if the synced is empty.
		if ( empty( $synced ) ) {
			// Continue the loop.
			continue;
		}

		// Get the taxonomies.
		$taxonomies = isset( $synced['taxonomies'] ) ? $synced['taxonomies'] : array();

		// Check if the taxonomies is empty.
		if ( empty( $taxonomies ) ) {
			// Continue the loop.
			continue;
		}

		// Set the taxonomy.
		$taxonomy = 'psupsellmaster_product_tag';

		// Get the terms.
		$terms = isset( $taxonomies[ $taxonomy ] ) ? $taxonomies[ $taxonomy ] : array();

		// Check if the terms is empty.
		if ( empty( $terms ) ) {
			// Continue the loop.
			continue;
		}

		// Check if the products is not empty.
		if ( ! empty( $products ) ) {
			// Set the eligible products.
			$eligible_products = $products;

			// Otherwise...
		} else {
			// Get the products flag.
			$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

			// Check the products flag.
			if ( 'all' === $products_flag ) {
				// Set the product status.
				$product_status = 'publish';

				// Get the product post type.
				$product_post_type = psupsellmaster_get_product_post_type();

				// Set the args.
				$get_posts_args = array(
					'fields'         => 'ids',
					'posts_per_page' => -1,
					'post_status'    => $product_status,
					'post_type'      => $product_post_type,
				);

				// Get the eligible products.
				$eligible_products = get_posts( $get_posts_args );

				// Otherwise, check the products flag.
			} elseif ( 'selected' === $products_flag ) {
				// Get the eligible products.
				$eligible_products = psupsellmaster_get_campaign_eligible_products( $campaign_id );
			}
		}

		// Loop through the eligible products.
		foreach ( $eligible_products as $product_id ) {
			// Add the terms to the product.
			wp_set_object_terms( $product_id, $terms, $taxonomy, true );
		}
	}
}

/**
 * Fires when a post is updated.
 * Maybe update the campaigns eligible products.
 * It will update it only if the product author has changed.
 *
 * @param int    $post_id Post ID.
 * @param object $post_after Post object following the update.
 * @param object $post_before Post object before the update.
 */
function psupsellmaster_campaigns_post_updated( $post_id, $post_after, $post_before ) {
	// Bail if doing WordPress autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && ( true === DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type does not match.
	if ( $product_post_type !== $post_type ) {
		return false;
	}

	// Get the old author.
	$old_author = isset( $post_before->post_author ) ? filter_var( $post_before->post_author, FILTER_VALIDATE_INT ) : false;

	// Get the new author.
	$new_author = isset( $post_after->post_author ) ? filter_var( $post_after->post_author, FILTER_VALIDATE_INT ) : false;

	// Check if the author does match.
	if ( $old_author === $new_author ) {
		return false;
	}

	// Set the args.
	$args = array( 'products' => array( $post_id ) );

	// Update the campaigns eligible products.
	psupsellmaster_campaigns_update_eligible_products( $args );

	// Sync some campaigns data with its eligible products.
	psupsellmaster_campaigns_sync_eligible_products( $args );

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();
}
add_action( 'post_updated', 'psupsellmaster_campaigns_post_updated', 10, 3 );

/**
 * Fires when a post status transition occurs.
 * It will update the campaigns eligible products.
 *
 * @param string $new_status The new post status.
 * @param string $old_status The old post status.
 * @param object $post The post object.
 */
function psupsellmaster_campaigns_transition_post_status( $new_status, $old_status, $post ) {
	// Bail if doing WordPress autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && ( true === DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get the post id.
	$post_id = isset( $post->ID ) ? filter_var( $post->ID, FILTER_VALIDATE_INT ) : false;

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type does not match.
	if ( $product_post_type !== $post_type ) {
		return false;
	}

	// Check if the post status is not valid.
	if ( 'auto-draft' === $new_status ) {
		return false;
	}

	// Check if the post status does match.
	if ( $old_status === $new_status ) {
		return false;
	}

	// Set the args.
	$args = array( 'products' => array( $post_id ) );

	// Update the campaigns eligible products.
	psupsellmaster_campaigns_update_eligible_products( $args );

	// Sync some campaigns data with its eligible products.
	psupsellmaster_campaigns_sync_eligible_products( $args );

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();
}
add_action( 'transition_post_status', 'psupsellmaster_campaigns_transition_post_status', 10, 3 );

/**
 * Fires when terms are set to an object.
 *
 * @param int    $object_id  Object ID.
 * @param array  $terms      An array of object terms.
 * @param array  $tt_ids     An array of term taxonomy IDs.
 * @param string $taxonomy   Taxonomy slug.
 * @param bool   $append     Whether to append new terms to the old terms.
 * @param array  $old_tt_ids Old array of term taxonomy IDs.
 */
function psupsellmaster_campaigns_set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	// Bail if doing WordPress autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && ( true === DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Check if the taxonomy does not match.
	if ( ! in_array( $taxonomy, $product_taxonomies, true ) ) {
		return false;
	}

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Get the post type.
	$post_type = get_post_type( $object_id );

	// Check if the post type does not match.
	if ( $product_post_type !== $post_type ) {
		return false;
	}

	// Get the difference.
	$difference = array_merge(
		array_diff( $old_tt_ids, $tt_ids ),
		array_diff( $tt_ids, $old_tt_ids ),
	);

	// Check if the terms haven't changed.
	if ( empty( $difference ) ) {
		return false;
	}

	// Set the args.
	$args = array( 'products' => array( $object_id ) );

	// Update the campaigns eligible products.
	psupsellmaster_campaigns_update_eligible_products( $args );

	// Remove the action to avoid infinite loops.
	remove_action( 'set_object_terms', 'psupsellmaster_campaigns_set_object_terms', 10, 6 );

	// Sync some campaigns data with its eligible products.
	psupsellmaster_campaigns_sync_eligible_products( $args );

	// Re-add the action.
	add_action( 'set_object_terms', 'psupsellmaster_campaigns_set_object_terms', 10, 6 );

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();
}
add_action( 'set_object_terms', 'psupsellmaster_campaigns_set_object_terms', 10, 6 );

/**
 * Validate a coupon.
 *
 * @param array $args The arguments.
 * @return array The validation.
 */
function psupsellmaster_validate_coupon( $args ) {
	// Set the validation.
	$validation = array(
		'is_valid' => false,
		'reason'   => __( 'This discount is invalid.', 'psupsellmaster' ),
	);

	// Get the coupon id.
	$coupon_id = isset( $args['coupon_id'] ) ? filter_var( $args['coupon_id'], FILTER_VALIDATE_INT ) : false;

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Get the campaign id.
		$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );
	}

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the validation.
		return $validation;
	}

	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();
	$eligible_campaigns = array_column( $eligible_campaigns, 'campaign_id' );

	// Check if the campaign id is not an eligible campaign.
	if ( ! in_array( $campaign_id, $eligible_campaigns, true ) ) {
		// Return the validation.
		return $validation;
	}

	// Set the is valid.
	$validation['is_valid'] = true;

	// Get the conditions.
	$conditions = psupsellmaster_db_campaign_meta_select( $campaign_id, 'conditions', true );
	$conditions = is_array( $conditions ) ? $conditions : array();

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Check if the products is set.
	if ( isset( $conditions['products'] ) ) {
		// Get the session cart items.
		$session_cart_items = psupsellmaster_get_session_cart_product_ids();
		$session_cart_items = array_unique( $session_cart_items );

		// Get the included items.
		$included_items = isset( $conditions['products']['include'] ) ? $conditions['products']['include'] : false;
		$included_items = is_array( $included_items ) ? array_column( $included_items, 'product_id' ) : array();

		// Check if there are included items.
		if ( ! empty( $included_items ) ) {
			// Check if all included items are in the session cart.
			if ( ! empty( array_diff( $included_items, $session_cart_items ) ) ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Return the validation.
				return $validation;
			}
		}

		// Get the excluded items.
		$excluded_items = isset( $conditions['products']['exclude'] ) ? $conditions['products']['exclude'] : false;
		$excluded_items = is_array( $excluded_items ) ? array_column( $excluded_items, 'product_id' ) : array();

		// Check if there are excluded items.
		if ( ! empty( $excluded_items ) ) {
			// Check if any excluded items are in the session cart items.
			if ( ! empty( array_intersect( $excluded_items, $session_cart_items ) ) ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Return the validation.
				return $validation;
			}
		}

		// Get the cart products min.
		$cart_products_min = isset( $conditions['products']['count'] ) && isset( $conditions['products']['count']['min'] ) ? filter_var( $conditions['products']['count']['min'], FILTER_VALIDATE_INT ) : false;

		// Check if the products min is set.
		if ( false !== $cart_products_min ) {
			// Set the session cart count.
			$session_cart_count = count( $session_cart_items );

			// Get the count type.
			$count_type = isset( $settings['conditions_products_count_type'] ) ? $settings['conditions_products_count_type'] : false;

			// Check the count type.
			if ( 'total_products' === $count_type ) {
				// Set the session cart count.
				$session_cart_count = psupsellmaster_get_session_cart_quantity();
			}

			// Check if the cart count is lower than the cart products min.
			if ( $session_cart_count < $cart_products_min ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Set the validation.
				$validation['reason'] = sprintf(
					/* translators: 1: the required minimum number of products in the cart. */
					_n(
						'This coupon requires at least %d product in the cart.',
						'This coupon requires at least %d products in the cart.',
						$cart_products_min,
						'psupsellmaster'
					),
					$cart_products_min
				);

				// Return the validation.
				return $validation;
			}
		}
	}

	// Check if the authors is set.
	if ( isset( $conditions['authors'] ) ) {
		// Get the session cart items.
		$session_cart_items = psupsellmaster_get_session_cart_author_ids();
		$session_cart_items = array_unique( $session_cart_items );

		// Get the included items.
		$included_items = isset( $conditions['authors']['include'] ) ? $conditions['authors']['include'] : false;
		$included_items = is_array( $included_items ) ? array_column( $included_items, 'author_id' ) : array();

		// Check if there are included items.
		if ( ! empty( $included_items ) ) {
			// Check if all included items are in the session cart.
			if ( ! empty( array_diff( $included_items, $session_cart_items ) ) ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Return the validation.
				return $validation;
			}
		}

		// Get the excluded items.
		$excluded_items = isset( $conditions['authors']['exclude'] ) ? $conditions['authors']['exclude'] : false;
		$excluded_items = is_array( $excluded_items ) ? array_column( $excluded_items, 'author_id' ) : array();

		// Check if there are excluded items.
		if ( ! empty( $excluded_items ) ) {
			// Check if any excluded items are in the session cart items.
			if ( ! empty( array_intersect( $excluded_items, $session_cart_items ) ) ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Return the validation.
				return $validation;
			}
		}
	}

	// Check if the taxonomies is set.
	if ( isset( $conditions['taxonomies'] ) ) {
		// Get the product taxonomies.
		$product_taxonomies = psupsellmaster_get_product_taxonomies();

		// Get the taxonomies.
		$taxonomies = is_array( $conditions['taxonomies'] ) ? $conditions['taxonomies'] : array();

		// Loop through the taxonomies.
		foreach ( $taxonomies as $taxonomy => $items ) {
			// Check if the taxonomy is not valid.
			if ( ! in_array( $taxonomy, $product_taxonomies, true ) ) {
				// Continue the loop.
				continue;
			}

			// Get the session cart items.
			$session_cart_items = psupsellmaster_get_session_cart_term_ids( $taxonomy );

			// Get the included items.
			$included_items = isset( $items['include'] ) ? $items['include'] : false;
			$included_items = is_array( $included_items ) ? array_column( $included_items, 'term_id' ) : array();

			// Check if there are included items.
			if ( ! empty( $included_items ) ) {
				// Check if all included items are in the session cart.
				if ( ! empty( array_diff( $included_items, $session_cart_items ) ) ) {
					// Set the validation.
					$validation['is_valid'] = false;

					// Return the validation.
					return $validation;
				}
			}

			// Get the excluded items.
			$excluded_items = isset( $items['exclude'] ) ? $items['exclude'] : false;
			$excluded_items = is_array( $excluded_items ) ? array_column( $excluded_items, 'term_id' ) : array();

			// Check if there are excluded items.
			if ( ! empty( $excluded_items ) ) {
				// Check if any excluded items are in the session cart items.
				if ( ! empty( array_intersect( $excluded_items, $session_cart_items ) ) ) {
					// Set the validation.
					$validation['is_valid'] = false;

					// Return the validation.
					return $validation;
				}
			}
		}
	}

	// Check if the subtotal is set.
	if ( isset( $conditions['subtotal'] ) ) {
		// Get the cart subtotal min.
		$cart_subtotal_min = isset( $conditions['subtotal']['min'] ) ? filter_var( $conditions['subtotal']['min'], FILTER_VALIDATE_FLOAT ) : false;

		// Check if the subtotal min is set.
		if ( false !== $cart_subtotal_min ) {
			// Get the session cart subtotal.
			$session_cart_subtotal = psupsellmaster_get_session_cart_subtotal();

			// Check if the cart subtotal is lower than the cart subtotal min.
			if ( $session_cart_subtotal < $cart_subtotal_min ) {
				// Set the validation.
				$validation['is_valid'] = false;

				// Set the validation.
				$validation['reason'] = sprintf(
					/* translators: 1: the required minimum cart subtotal. */
					__( 'The minimum spend for this coupon is %s.', 'psupsellmaster' ),
					psupsellmaster_format_currency_amount( $cart_subtotal_min )
				);

				// Return the validation.
				return $validation;
			}
		}
	}

	// Check if the is valid is false and there is no reason.
	if ( false === $validation['is_valid'] && empty( $validation['reason'] ) ) {
		// Set the reason.
		$validation['reason'] = __( 'This discount is invalid.', 'psupsellmaster' );

		// Check if the is valid is true.
	} elseif ( true === $validation['is_valid'] ) {
		// Set the reason.
		$validation['reason'] = '';
	}

	// Return the validation.
	return $validation;
}

/**
 * Add custom classes to the body tag.
 *
 * @param array|string $classes The classes.
 * @return array|string The classes.
 */
function psupsellmaster_campaigns_body_class( $classes ) {
	// Get the eligible campaigns.
	$eligible_campaigns = psupsellmaster_get_eligible_campaigns();

	// Check if the eligible campaigns is empty.
	if ( empty( $eligible_campaigns ) ) {
		// Return the classes.
		return $classes;
	}

	// Set the custom.
	$custom = $classes;

	// Check if the classes is a string.
	if ( is_string( $classes ) ) {
		// Set the custom.
		$custom = explode( ' ', $classes );
	}

	// Set the custom.
	$custom = is_array( $custom ) ? $custom : array();

	// Set the add.
	$add = array( 'psupsellmaster-campaigns' );

	// Set the custom.
	$custom = array_merge( $custom, $add );

	// Check if the classes is an array.
	if ( is_array( $classes ) ) {
		// Set the classes.
		$classes = $custom;
	} else {
		// Set the classes.
		$classes = implode( ' ', $custom );
	}

	// Return the classes.
	return $classes;
}
add_filter( 'body_class', 'psupsellmaster_campaigns_body_class' );

/**
 * Get the campaign id of the campaign created through the Setup Wizard.
 *
 * @return false|int The campaign id. False if not found.
 */
function psupsellmaster_campaigns_get_id_from_setup_wizard() {
	// Set the where.
	$where = array(
		'meta_key'   => 'origin',
		'meta_value' => 'wizard',
	);

	// Get the rows.
	$rows = psupsellmaster_db_campaignmeta_select( $where );

	// Get the row.
	$row = array_shift( $rows );

	// Get the campaign id.
	$campaign_id = isset( $row->psupsellmaster_campaign_id ) ? filter_var( $row->psupsellmaster_campaign_id, FILTER_VALIDATE_INT ) : false;

	// Return the campaign id.
	return $campaign_id;
}

/**
 * Get the campaign dates.
 *
 * @param int $campaign_id The campaign ID.
 * @return array The campaign dates.
 */
function psupsellmaster_campaigns_get_dates( $campaign_id ) {
	// Set the dates.
	$dates = array(
		'timezone_date'     => array(
			'start_date' => null,
			'end_date'   => null,
		),
		'timezone_datetime' => array(
			'start_date' => null,
			'end_date'   => null,
		),
		'utc_date'          => array(
			'start_date' => null,
			'end_date'   => null,
		),
		'utc_datetime'      => array(
			'start_date' => null,
			'end_date'   => null,
		),
	);

	// Get the campaign.
	$campaign = psupsellmaster_get_campaign( $campaign_id );

	// Check the start date.
	if ( isset( $campaign['start_date'] ) && '0000-00-00 00:00:00' !== $campaign['start_date'] ) {
		// Set the dates.
		$dates['utc_datetime']['start_date'] = $campaign['start_date'];

		// Set the datetime start date.
		$datetime_start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $dates['utc_datetime']['start_date'] );

		// Check the start date.
		if ( $datetime_start_date instanceof DateTime ) {
			// Set the dates.
			$dates['utc_date']['start_date'] = $datetime_start_date->format( 'Y/m/d' );

			// Set the timezone.
			$datetime_start_date->setTimezone( psupsellmaster_get_timezone() );

			// Set the dates.
			$dates['timezone_datetime']['start_date'] = $datetime_start_date->format( 'Y-m-d H:i:s' );

			// Set the dates.
			$dates['timezone_date']['start_date'] = $datetime_start_date->format( 'Y/m/d' );
		}
	}

	// Check the end date.
	if ( isset( $campaign['end_date'] ) && '0000-00-00 00:00:00' !== $campaign['end_date'] ) {
		// Set the dates.
		$dates['utc_datetime']['end_date'] = $campaign['end_date'];

		// Set the datetime end date.
		$datetime_end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $dates['utc_datetime']['end_date'] );

		// Check the end date.
		if ( $datetime_end_date instanceof DateTime ) {
			// Set the dates.
			$dates['utc_date']['end_date'] = $datetime_end_date->format( 'Y/m/d' );

			// Set the timezone.
			$datetime_end_date->setTimezone( psupsellmaster_get_timezone() );

			// Set the dates.
			$dates['timezone_datetime']['end_date'] = $datetime_end_date->format( 'Y-m-d H:i:s' );

			// Set the dates.
			$dates['timezone_date']['end_date'] = $datetime_end_date->format( 'Y/m/d' );
		}
	}

	// Return the dates.
	return $dates;
}

<?php
/**
 * Functions - Base.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivate the LITE version.
 */
function psupsellmaster_lite_deactivate() {
	deactivate_plugins( 'psupsellmaster/psupsellmaster.php' );
}

/**
 * Deactivate the PRO version.
 */
function psupsellmaster_pro_deactivate() {
	deactivate_plugins( 'psupsellmaster-pro/psupsellmaster.php' );
}

/**
 * Get the constant value.
 *
 * @param string $constant_name The constant name.
 * @return mixed|null The constant value.
 */
function psupsellmaster_get_constant( $constant_name ) {
	// Set the value.
	$value = null;

	// Check the constant name.
	if ( defined( $constant_name ) ) {
		// Set the value.
		$value = constant( $constant_name );
	}

	// Return the value.
	return $value;
}

/**
 * Wrapper for the get_plugins WordPress core function.
 *
 * @param string $plugin_folder Optional. Relative path to single plugin folder.
 * @return array[] Array of arrays of plugin data, keyed by plugin file name
 */
function psupsellmaster_get_plugins( $plugin_folder = '' ) {
	// Check if the get_plugins WordPress function does not exist.
	if ( ! function_exists( 'get_plugins' ) ) {
		// Includes the file that contains the function.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	// Get the plugins.
	$plugins = get_plugins( $plugin_folder );

	// Return the plugins.
	return $plugins;
}

/**
 * Get the plugin's files by a given key.
 *
 * @param string $value The value to search for.
 * @param string $key The key to search for.
 * @return array The plugin files.
 */
function psupsellmaster_get_plugin_files_by( $value, $key = 'TextDomain' ) {
	// Get the plugins.
	$plugins = psupsellmaster_get_plugins();

	// Get the values.
	$values = wp_list_pluck( $plugins, $key );

	// Get the files.
	$files = array_keys( $values, $value, true );

	// Return the files.
	return $files;
}

/**
 * Check if a plugin is active.
 *
 * @param string $plugin_key The plugin key.
 * @return bool Whether the plugin is active.
 */
function psupsellmaster_is_plugin_active( $plugin_key ) {
	// Set the is plugin active.
	$is_plugin_active = false;

	// Check the plugin key.
	if ( 'woo' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'WooCommerce' );

		// Check the plugin key.
	} elseif ( 'edd' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'Easy_Digital_Downloads' );

		// Check the plugin key.
	} elseif ( 'edd-advanced-shortcodes' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'EDD_Advanced_Shortcodes' );

		// Check the plugin key.
	} elseif ( 'edd-product-versions' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'EDD_Product_Versions' );

		// Check the plugin key.
	} elseif ( 'edd-fes' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'EDD_Front_End_Submissions' );

		// Check the plugin key.
	} elseif ( 'edd-wish-lists' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'EDD_Wish_Lists' );

		// Check the plugin key.
	} elseif ( 'kinsta-mu' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( '\Kinsta\KMP' );

		// Check the plugin key.
	} elseif ( 'autoptimize' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'autoptimizeMain' );

		// Check the plugin key.
	} elseif ( 'elementor' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( '\Elementor\Plugin' );

		// Check the plugin key.
	} elseif ( 'wpml' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( 'SitePress' );

		// Check the plugin key.
	} elseif ( 'vczapi-woocommerce-addon' === $plugin_key ) {
		// Set the is plugin active.
		$is_plugin_active = class_exists( '\Codemanas\ZoomWooCommerceAddon\Bootstrap' );
	}

	// Return the is plugin active.
	return $is_plugin_active;
}

/**
 * Get the product post type.
 *
 * @return string The product post type.
 */
function psupsellmaster_get_product_post_type() {
	// Set the post type.
	$post_type = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the post type.
		$post_type = 'product';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the post type.
		$post_type = 'download';
	}

	// Return the post type.
	return $post_type;
}

/**
 * Get the coupon post type.
 *
 * @return string The coupon post type.
 */
function psupsellmaster_get_coupon_post_type() {
	// Set the post type.
	$post_type = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the post type.
		$post_type = 'shop_coupon';
	}

	// Return the post type.
	return $post_type;
}

/**
 * Get the product taxonomies.
 *
 * @param string $output The output type (names or objects).
 * @param bool   $include_standard Whether to include the standard taxonomies.
 * @return string[]|WP_Taxonomy[] The product taxonomies.
 */
function psupsellmaster_get_product_taxonomies( $output = 'names', $include_standard = true ) {
	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Get the product taxonomies.
	$product_taxonomies = get_object_taxonomies( $product_post_type, $output );

	// Check if the include standard is false.
	if ( false === $include_standard ) {
		// Set the standard taxonomies.
		$standard_taxonomies = array();

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the standard taxonomies.
			$standard_taxonomies = array( 'product_cat', 'product_tag' );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the standard taxonomies.
			$standard_taxonomies = array( 'download_category', 'download_tag' );
		}

		// Loop through the standard taxonomies.
		foreach ( $standard_taxonomies as $standard_taxonomy ) {
			// Check if the output is objects.
			if ( 'objects' === $output ) {
				// Check if the standard taxonomy does exist.
				if ( isset( $product_taxonomies[ $standard_taxonomy ] ) ) {
					// Remove the standard taxonomy from the list.
					unset( $product_taxonomies[ $standard_taxonomy ] );
				}

				// Otherwise, check if the output is names.
			} elseif ( 'names' === $output ) {
				// Remove the standard taxonomy from the list.
				$product_taxonomies = array_diff( $product_taxonomies, $standard_taxonomies );
			}
		}
	}

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Check if the internal taxonomy is set.
		if ( isset( $product_taxonomies['product_shipping_class'] ) ) {
			// Remove the taxonomy from the list.
			unset( $product_taxonomies['product_shipping_class'] );
		}

		// Check if the internal taxonomy is set.
		if ( isset( $product_taxonomies['product_type'] ) ) {
			// Remove the taxonomy from the list.
			unset( $product_taxonomies['product_type'] );
		}

		// Check if the internal taxonomy is set.
		if ( isset( $product_taxonomies['product_visibility'] ) ) {
			// Remove the taxonomy from the list.
			unset( $product_taxonomies['product_visibility'] );
		}
	}

	// Return the product taxonomies.
	return $product_taxonomies;
}

/**
 * Get the campaigns available locations.
 *
 * @return array Return the locations.
 */
function psupsellmaster_campaigns_get_locations() {
	// Set the locations.
	$locations = array(
		'checkout'          => __( 'Checkout Page', 'psupsellmaster' ),
		'product'           => __( 'Product Page', 'psupsellmaster' ),
		'popup_add_to_cart' => __( 'Add to Cart Popup', 'psupsellmaster' ),
	);

	// Allow developers to filter this.
	$locations = apply_filters( 'psupsellmaster_campaigns_get_locations', $locations );

	// Return the locations.
	return $locations;
}

/**
 * Get the campaign available statuses.
 *
 * @return array Return the campaign statuses.
 */
function psupsellmaster_campaigns_get_statuses() {
	// Set the statuses.
	$statuses = array(
		'active'    => __( 'Active', 'psupsellmaster' ),
		'inactive'  => __( 'Inactive', 'psupsellmaster' ),
		'expired'   => __( 'Expired', 'psupsellmaster' ),
		'scheduled' => __( 'Scheduled', 'psupsellmaster' ),
	);

	// Return the statuses.
	return $statuses;
}

/**
 * Transform an array of items (arrays or objects) to an associative array based on specified keys.
 *
 * @param array $items The array of items.
 * @param string $value_attribute The attribute to use as the value.
 * @param string $label_attribute The attribute to use as the label.
 * @return array The associative array.
 */
function psupsellmaster_items_to_value_label_array( $items, $value_attribute, $label_attribute ) {
	// Return the array.
	$array = array();

	// Loop through the items.
	foreach ( $items as $item ) {
		// Set the value.
		$value = null;

		// Set the label.
		$label = null;

		// Check if the item is an object.
		if ( is_object( $item ) ) {
			// Get the value.
			$value = $item->$value_attribute ?? null;

			// Get the label.
			$label = $item->$label_attribute ?? null;

			// Check if the item is an array.
		} elseif ( is_array( $item ) ) {
			// Get the value.
			$value = $item[ $value_attribute ] ?? null;

			// Get the label.
			$label = $item[ $label_attribute ] ?? null;
		}

		// Check if the value or label is not set.
		if ( ! $value || ! $label ) {
			// Skip.
			continue;
		}

		// Set the item.
		$item = array(
			'label' => html_entity_decode( $label ),
			'value' => $value,
		);

		// Add the item to the list.
		array_push( $array, $item );
	}

	// Return the array.
	return $array;
}

/**
 * Set the query to customize the search on the users database table.
 *
 * @param WP_User_Query $user_query The user query.
 * @return WP_User_Query The user query.
 */
function psupsellmaster_pre_user_query_custom_search( $user_query ) {
	global $wpdb;

	// Get the custom search.
	$custom_search = $user_query->get( 'psupsellmaster_search_text' );

	// Check if the custom search is not empty.
	if ( ! empty( $custom_search ) ) {
		// Set the conditions.
		$conditions = array();

		// Set the escaped search.
		$escaped_search = $wpdb->esc_like( $custom_search );

		// Set the like search.
		$like_search = "%{$escaped_search}%";

		// Set the columns.
		$columns = array(
			'display_name',
			'user_email',
			'user_login',
			'user_nicename',
		);

		// Loop through the columns.
		foreach ( $columns as $column ) {
			// Set the item.
			$item = $wpdb->prepare(
				'%i.%i LIKE %s',
				$wpdb->users,
				$column,
				$like_search
			);

			// Add the item to the list.
			array_push( $conditions, $item );
		}

		// Set the query.
		$query = implode( ' OR ', $conditions );

		// Set the where.
		$user_query->query_where .= " AND ( {$query} ) ";
	}

	// Return the query.
	return $user_query;
}

/**
 * Get the users.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_users( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'has_published_posts'        => array(),
		'include'                    => array(),
		'number'                     => 20,
		'offset'                     => 0,
		'orderby'                    => 'display_name',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the paged.
	$parsed_args['paged'] = $parsed_args['psupsellmaster_page'];

	// Add the filter.
	add_filter( 'pre_user_query', 'psupsellmaster_pre_user_query_custom_search', 10 );

	// Set the query.
	$query = new WP_User_Query( $parsed_args );

	// Get the items.
	$items = $query->get_results();

	// Get the filtered count.
	$count_filtered = count( $items );

	// Get the total count.
	$count_total = absint( $query->get_total() );

	// Remove the filter.
	remove_filter( 'pre_user_query', 'psupsellmaster_pre_user_query_custom_search', 10 );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the authors.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_authors( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'has_published_posts'        => true,
		'include'                    => array(),
		'number'                     => 20,
		'offset'                     => 0,
		'orderby'                    => 'display_name',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_users( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Set the query to customize the search on the posts database table.
 *
 * @param string $where The where.
 * @param WP_Query $query The query.
 * @return string The where.
 */
function psupsellmaster_posts_where_custom_search( $where, $query ) {
	global $wpdb;

	// Get the custom search.
	$custom_search = $query->get( 'psupsellmaster_search_text' );

	// Check if the custom search is not empty.
	if ( ! empty( $custom_search ) ) {
		// Set the where.
		$where .= $wpdb->prepare(
			' AND ( %i.`ID` = %d OR %i.`post_title` LIKE %s ) ',
			$wpdb->posts,
			$custom_search,
			$wpdb->posts,
			'%' . $wpdb->esc_like( $custom_search ) . '%',
		);
	}

	// Return the where.
	return $where;
}

/**
 * Get the posts.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_posts( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'order'                      => 'ASC',
		'orderby'                    => 'title',
		'post__in'                   => array(),
		'post_status'                => 'publish',
		'posts_per_page'             => 20,
		'suppress_filters'           => false,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the paged.
	$parsed_args['paged'] = $parsed_args['psupsellmaster_page'];

	// Add the filter.
	add_filter( 'posts_where', 'psupsellmaster_posts_where_custom_search', 10, 2 );

	// Set the query.
	$query = new WP_Query( $parsed_args );

	// Get the items.
	$items = $query->posts;

	// Get the filtered count.
	$count_filtered = count( $items );

	// Get the total count.
	$count_total = absint( $query->found_posts );

	// Remove the filter.
	remove_filter( 'posts_where', 'psupsellmaster_posts_where_custom_search' );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['posts_per_page'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the post statuses.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_post_statuses( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the items.
	$items = array();

	// Get the statuses.
	$statuses = get_post_statuses();

	// Loop through the statuses.
	foreach ( $statuses as $key => $label ) {
		// Check the key.
		if ( empty( $key ) ) {
			continue;
		}

		// Check the label.
		if ( empty( $label ) ) {
			continue;
		}

		// Check if the search is not empty.
		if ( ! empty( $parsed_args['search'] ) ) {
			// Check the include (ignore search for included items).
			if ( ! in_array( $key, $parsed_args['include'], true ) ) {
				// Set the escaped.
				$escaped = preg_quote( $parsed_args['search'], '/' );

				// Set the pattern.
				$pattern = "/$escaped/i";

				// Check if the search does not match.
				if ( ! preg_match( $pattern, $label ) ) {
					continue;
				}
			}
		}

		// Set the item.
		$item = array(
			'key'   => $key,
			'label' => $label
		);

		// Add the item to the list.
		array_push( $items, $item );
	}

	// Get the total count.
	$count_total = count( $items );

	// Check the number.
	if ( -1 !== $parsed_args['number'] ) {
		// Set the items.
		$items = array_splice( $items, 0, $parsed_args['number'] );
	}

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the pages.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_pages( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'order'                      => 'ASC',
		'orderby'                    => 'title',
		'post__in'                   => array(),
		'post_type'                  => 'page',
		'post_status'                => 'publish',
		'posts_per_page'             => 20,
		'suppress_filters'           => false,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_posts( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the products.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_products( $args = array() ) {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'order'                      => 'ASC',
		'orderby'                    => 'title',
		'post__in'                   => array(),
		'post_type'                  => $post_type,
		'post_status'                => 'publish',
		'posts_per_page'             => 20,
		'suppress_filters'           => false,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_posts( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the product authors.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_authors( $args = array() ) {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'has_published_posts'        => array( $post_type ),
		'include'                    => array(),
		'number'                     => 20,
		'offset'                     => 0,
		'orderby'                    => 'display_name',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_users( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the product statuses.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_statuses( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the data.
	$data = psupsellmaster_get_post_statuses( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Set the query to customize the search on the terms database table.
 *
 * @param array $clauses    The query clauses.
 * @param array $taxonomies The taxonomy names.
 * @param array $args       The get_terms() arguments.
 * @return array The query clauses.
 */
function psupsellmaster_terms_clauses_custom_search( $clauses, $taxonomies, $args ) {
	global $wpdb;

	// Get the custom search.
	$custom_search = isset( $args['psupsellmaster_search_text'] ) ? $args['psupsellmaster_search_text'] : '';

	// Check if the custom search is not empty.
	if ( ! empty( $custom_search ) ) {
		// Set the conditions.
		$conditions = array();

		// Set the escaped search.
		$escaped_search = $wpdb->esc_like( $custom_search );

		// Set the like search.
		$like_search = "%{$escaped_search}%";

		// Set the columns.
		$columns = array(
			'name',
			'slug',
		);

		// Loop through the columns.
		foreach ( $columns as $column ) {
			// Set the item.
			$item = $wpdb->prepare(
				'%i.%i LIKE %s',
				't',
				$column,
				$like_search
			);

			// Add the item to the list.
			array_push( $conditions, $item );
		}

		// Set the query.
		$query = implode( ' OR ', $conditions );

		// Set the where.
		$clauses['where'] .= " AND ( {$query} ) ";
	}

	// Return the clauses.
	return $clauses;
}

/**
 * Get the taxonomy terms.
 *
 * @param string $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_taxonomy_terms( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'hide_empty'                 => false,
		'include'                    => array(),
		'number'                     => 20,
		'object_ids'                 => array(),
		'order'                      => 'ASC',
		'orderby'                    => 'name',
		'taxonomy'                   => '',
	);

	// Add the filter.
	add_filter( 'terms_clauses', 'psupsellmaster_terms_clauses_custom_search', 10, 3 );

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the offset.
	$parsed_args['offset'] = ( $parsed_args['psupsellmaster_page'] - 1 ) * $parsed_args['number'];

	// Set the query.
	$query = new WP_Term_Query( $parsed_args );

	// Get the items.
	$items = $query->get_terms();

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the count args.
	$count_args = $parsed_args;

	// Check the key.
	if ( array_key_exists('number', $count_args ) ) {
		// Unset the key.
		unset( $count_args['number'] );
	}

	// Get the total count.
	$count_total = wp_count_terms( $count_args );
	$count_total = ! ( $count_total instanceof WP_Error ) ? absint( $count_total ) : 0;

	// Remove the filter.
	remove_filter( 'terms_clauses', 'psupsellmaster_terms_clauses_custom_search', 10 );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the product taxonomy terms.
 *
 * @param string $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_taxonomy_terms( $args = array() ) {
	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'hide_empty'                 => false,
		'include'                    => array(),
		'number'                     => 20,
		'object_ids'                 => array(),
		'order'                      => 'ASC',
		'orderby'                    => 'name',
		'taxonomy'                   => $product_taxonomies,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_taxonomy_terms( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the product category terms.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_category_terms( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'hide_empty'                 => false,
		'include'                    => array(),
		'number'                     => 20,
		'object_ids'                 => array(),
		'order'                      => 'ASC',
		'orderby'                    => 'name',
	);

	// Set the taxonomy.
	$defaults['taxonomy'] = psupsellmaster_get_product_category_taxonomy();

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_taxonomy_terms( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the product tag terms.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_tag_terms( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'hide_empty'                 => false,
		'include'                    => array(),
		'number'                     => 20,
		'object_ids'                 => array(),
		'order'                      => 'ASC',
		'orderby'                    => 'name',
	);

	// Set the taxonomy.
	$defaults['taxonomy'] = psupsellmaster_get_product_tag_taxonomy();

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_taxonomy_terms( $parsed_args );

	// Return the data.
	return $data;
}

/**
 * Get the campaigns.
 * @todo.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_campaigns( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'number' => 20,
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
	$sql_from = PsUpsellMaster_Database::prepare(
		'FROM %i AS `campaigns`',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' )
	);

	// Set the SQL where.
	$sql_where = array();

	// Check the args.
	if ( ! empty( $parsed_args['search'] ) ) {
		// Add conditions to the SQL where.
		array_push(
			$sql_where,
			PsUpsellMaster_Database::prepare(
				'AND `campaigns`.`title` LIKE %s',
				'%' . $parsed_args['search'] . '%'
			)
		);
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
	$sql_query = "SELECT COUNT( * ) {$sql_from} {$sql_where}";

	// Get the total count.
	$count_total = absint( PsUpsellMaster_Database::get_var( $sql_query ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where} {$sql_order_by} {$sql_limit}";

	// Get the items.
	$items = PsUpsellMaster_Database::get_results( $sql_query );

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the campaign statuses.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_campaign_statuses( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the items.
	$items = array();

	// Get the statuses.
	$statuses = psupsellmaster_campaigns_get_statuses();

	// Loop through the statuses.
	foreach ( $statuses as $key => $label ) {
		// Check the key.
		if ( empty( $key ) ) {
			continue;
		}

		// Check the label.
		if ( empty( $label ) ) {
			continue;
		}

		// Check if the search is not empty.
		if ( ! empty( $parsed_args['search'] ) ) {
			// Check the include (ignore search for included items).
			if ( ! in_array( $key, $parsed_args['include'], true ) ) {
				// Set the escaped.
				$escaped = preg_quote( $parsed_args['search'], '/' );

				// Set the pattern.
				$pattern = "/$escaped/i";

				// Check if the search does not match.
				if ( ! preg_match( $pattern, $label ) ) {
					continue;
				}
			}
		}

		// Set the item.
		$item = array(
			'key'   => $key,
			'label' => $label
		);

		// Add the item to the list.
		array_push( $items, $item );
	}

	// Get the total count.
	$count_total = count( $items );

	// Check the number.
	if ( -1 !== $parsed_args['number'] ) {
		// Set the items.
		$items = array_splice( $items, 0, $parsed_args['number'] );
	}

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the campaign locations.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_search_campaign_locations( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Set the items.
	$items = array();

	// Get the locations.
	$locations = psupsellmaster_campaigns_get_locations();

	// Loop through the locations.
	foreach ( $locations as $key => $label ) {
		// Check the key.
		if ( empty( $key ) ) {
			continue;
		}

		// Check the label.
		if ( empty( $label ) ) {
			continue;
		}

		// Check if the search is not empty.
		if ( ! empty( $parsed_args['search'] ) ) {
			// Check the include (ignore search for included items).
			if ( ! in_array( $key, $parsed_args['include'], true ) ) {
				// Set the escaped.
				$escaped = preg_quote( $parsed_args['search'], '/' );

				// Set the pattern.
				$pattern = "/$escaped/i";

				// Check if the search does not match.
				if ( ! preg_match( $pattern, $label ) ) {
					continue;
				}
			}
		}

		// Set the item.
		$item = array(
			'key'   => $key,
			'label' => $label
		);

		// Add the item to the list.
		array_push( $items, $item );
	}

	// Get the total count.
	$count_total = count( $items );

	// Check the number.
	if ( -1 !== $parsed_args['number'] ) {
		// Set the items.
		$items = array_splice( $items, 0, $parsed_args['number'] );
	}

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Return the data.
	return $data;
}

/**
 * Get the page label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_page_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'fields'                     => array( 'ID', 'post_title' ),
		'include'                    => array(),
		'posts_per_page'             => 20,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_pages( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'ID', 'post_title' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the author label and value pairs.
 *
 * @param array $args The arguments.
 * @return array data.
 */
function psupsellmaster_get_author_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'fields'                     => array( 'ID', 'display_name' ),
		'include'                    => array(),
		'number'                     => 20,
		'offset'                     => 0,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_authors( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'ID', 'display_name' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the product author label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_author_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'fields'                     => array( 'ID', 'display_name' ),
		'include'                    => array(),
		'number'                     => 20,
		'offset'                     => 0,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_product_authors( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'ID', 'display_name' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the product label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'fields'                     => array( 'ID', 'post_title' ),
		'include'                    => array(),
		'posts_per_page'             => 20,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_products( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'ID', 'post_title' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the product status label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_status_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_product_statuses( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'key', 'label' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the taxonomy term label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_taxonomy_term_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page' => 1,
		'hide_empty'          => false,
		'include'             => array(),
		'number'              => 20,
		'object_ids'          => array(),
		'taxonomy'            => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_taxonomy_terms( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'term_id', 'name' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the product category term label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_category_term_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page' => 1,
		'hide_empty'          => false,
		'include'             => array(),
		'number'              => 20,
		'object_ids'          => array(),
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_product_category_terms( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'term_id', 'name' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the product tag term label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_product_tag_term_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page' => 1,
		'hide_empty'          => false,
		'include'             => array(),
		'number'              => 20,
		'object_ids'          => array(),
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_product_tag_terms( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'term_id', 'name' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the campaign label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_campaign_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'number' => 20,
		'search' => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_campaigns( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'id', 'title' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the campaign status label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_campaign_status_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_campaign_statuses( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'key', 'label' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

/**
 * Get the campaign location label and value pairs.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_get_campaign_location_label_value_pairs( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'include' => array(),
		'number'  => 20,
		'search'  => '',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_search_campaign_locations( $parsed_args );

	// Get the pairs.
	$pairs = psupsellmaster_items_to_value_label_array( $data['items'], 'key', 'label' );

	// Set the data.
	$data['items'] = $pairs;

	// Return the data.
	return $data;
}

<?php
/**
 * Admin - Functions - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Change the admin title for campaign pages.
 *
 * @param string $admin_title The admin title.
 * @return string Return the admin title.
 */
function psupsellmaster_campaigns_admin_title( $admin_title ) {
	// Check the admin page.
	if ( ! psupsellmaster_admin_is_page( 'campaigns' ) ) {
		// Return the admin title.
		return $admin_title;

		// Check the admin page.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'list' ) ) {
		// Return the admin title.
		return $admin_title;
	}

	// Set the campaigns label.
	$campaigns_label = __( 'Campaigns', 'psupsellmaster' );

	// Check the admin page.
	if ( psupsellmaster_admin_is_page( 'campaigns', 'new' ) ) {
		// Set the page label.
		$page_label = sprintf( __( 'Add New Campaign', 'psupsellmaster' ) );

		// Set the admin title.
		$admin_title = str_replace( $campaigns_label, $page_label, $admin_title );

		// Return the admin title.
		return $admin_title;
	}

	// Get the campaign id.
	$campaign_id = isset( $_GET['campaign'] ) ? filter_var( sanitize_text_field( wp_unslash( $_GET['campaign'] ) ), FILTER_VALIDATE_INT ) : false;
	$campaign_id = false !== $campaign_id ? $campaign_id : 0;

	// Get the stored campaign.
	$stored_campaign = psupsellmaster_get_campaign( $campaign_id );

	// Get the stored title.
	$stored_title = isset( $stored_campaign['title'] ) ? stripslashes( $stored_campaign['title'] ) : '';

	// Set the base label.
	/* translators: Admin screen title. 1: The campaign action label. 2: The campaign title. */
	$base_label = __( '%1$s &#8220;%2$s&#8221;', 'psupsellmaster' );

	// Check the view query argument.
	if ( psupsellmaster_admin_is_page( 'campaigns', 'edit' ) ) {
		// Set the page label.
		$page_label = sprintf( $base_label, __( 'Edit Campaign', 'psupsellmaster' ), $stored_title );

		// Check the view query argument.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'view' ) ) {
		// Set the page label.
		$page_label = sprintf( $base_label, __( 'View Campaign', 'psupsellmaster' ), $stored_title );

		// Check the view query argument.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'tags' ) ) {
		// Set the page label.
		$page_label = __( 'UpsellMaster Tags', 'psupsellmaster' );
	}

	// Set the admin title.
	$admin_title = str_replace( $campaigns_label, $page_label, $admin_title );

	// Return the admin title.
	return $admin_title;
}
add_filter( 'admin_title', 'psupsellmaster_campaigns_admin_title' );

/**
 * Save a campaign to the database.
 * It will insert or update the campaign.
 *
 * @param array $data The campaign data.
 * @return int|bool Return the campaign id if the campaign was saved, false otherwise.
 */
function psupsellmaster_save_campaign( $data ) {
	// Get the campaign id.
	$campaign_id = isset( $data['campaign_id'] ) ? filter_var( $data['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		// Check if the campaign id is empty (new campaign).
		if ( empty( $campaign_id ) ) {
			// Check if the campaigns limit has been reached.
			if ( psupsellmaster_has_reached_feature_limit( 'campaigns_count' ) ) {
				return false;
			}
		}
	}

	// Get the title.
	$title = ! empty( $data['title'] ) ? $data['title'] : __( 'New Campaign', 'psupsellmaster' );

	// Get the status.
	$status = isset( $data['status'] ) ? $data['status'] : '';
	$status = in_array( $status, array( 'active', 'inactive' ), true ) ? $status : 'inactive';

	// Get the priority.
	$priority = isset( $data['priority'] ) ? filter_var( $data['priority'], FILTER_VALIDATE_INT ) : false;

	// Check the priority.
	if ( false === $priority ) {
		// Set the priority.
		$priority = 10;

		// Check the priority.
	} elseif ( $priority < 1 ) {
		// Set the priority.
		$priority = 1;

		// Check the priority.
	} elseif ( $priority > 100 ) {
		// Set the priority.
		$priority = 100;
	}

	// Get the start date.
	$start_date = ! empty( $data['start_date'] ) ? $data['start_date'] : null;

	// Check if the start date is not empty.
	if ( ! empty( $start_date ) ) {
		// Set the start date.
		$start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', "$start_date 00:00:00", psupsellmaster_get_timezone() );

		// Check if the start date is valid.
		if ( $start_date instanceof DateTime ) {
			// Set the start date timezone.
			$start_date->setTimezone( new DateTimeZone( 'UTC' ) );

			// Set the start date.
			$start_date = $start_date->format( 'Y-m-d H:i:s' );
		}

		// Check if the start date is empty.
		if ( empty( $start_date ) ) {
			// Set the start date.
			$start_date = null;
		}
	}

	// Get the end date.
	$end_date = ! empty( $data['end_date'] ) ? $data['end_date'] : null;

	// Check if the end date is not empty.
	if ( ! empty( $end_date ) ) {
		// Set the end date.
		$end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', "$end_date 23:59:59", psupsellmaster_get_timezone() );

		// Check if the end date is valid.
		if ( $end_date instanceof DateTime ) {
			// Set the end date timezone.
			$end_date->setTimezone( new DateTimeZone( 'UTC' ) );

			// Set the end date.
			$end_date = $end_date->format( 'Y-m-d H:i:s' );
		}

		// Check if the end date is empty.
		if ( empty( $end_date ) ) {
			// Set the end date.
			$end_date = null;
		}
	}

	// Check if this is the lite version and the status is active.
	if ( psupsellmaster_is_lite() && 'active' === $status ) {
		// Set the inactive campaigns.
		psupsellmaster_campaigns_set_status_inactive();
	}

	// Set the save data.
	$save_data = array(
		'title'      => $title,
		'status'     => $status,
		'priority'   => $priority,
		'start_date' => $start_date,
		'end_date'   => $end_date,
	);

	// Check if the id is empty.
	if ( empty( $campaign_id ) ) {
		// Insert the campaign.
		psupsellmaster_db_campaigns_insert( $save_data );

		// Set the id.
		$campaign_id = psupsellmaster_db_get_inserted_id();
	} else {
		// Update the campaign.
		psupsellmaster_db_campaigns_update( $save_data, array( 'id' => $campaign_id ) );
	}

	// Set the valid weekdays.
	$valid_weekdays = array(
		'monday',
		'tuesday',
		'wednesday',
		'thursday',
		'friday',
		'saturday',
		'sunday',
	);

	// Get the weekdays.
	$weekdays = isset( $data['weekdays'] ) && is_array( $data['weekdays'] ) ? $data['weekdays'] : array();
	$weekdays = ! empty( $weekdays ) ? array_intersect( $weekdays, $valid_weekdays ) : array();
	$weekdays = count( $valid_weekdays ) !== count( $weekdays ) ? $weekdays : array();

	// Set the weekdays table.
	$weekdays_table = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' );

	// Set the where.
	$where = array( 'campaign_id' => $campaign_id );

	// Delete the campaign weekdays.
	psupsellmaster_db_campaign_weekdays_delete( $where );

	// Check if the weekdays is not empty.
	if ( ! empty( $weekdays ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $weekdays ), '( %d, #1# )' ) );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $weekdays ), $campaign_id ) );

		// Set the placeholders.
		$placeholders = str_replace( '#1#', '%s', $sql_values );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $weekdays );

		// Set the sql query.
		$sql_query = "INSERT INTO `{$weekdays_table}` ( `campaign_id`, `weekday` ) VALUES {$sql_values}";

		// Insert the weekdays.
		PsUpsellMaster_Database::query( $sql_query );
	}

	// Get the valid locations.
	$valid_locations = array_keys( psupsellmaster_get_product_locations() );

	// Get the locations.
	$locations = isset( $data['locations'] ) && is_array( $data['locations'] ) ? $data['locations'] : array();
	$locations = ! empty( $locations ) ? array_intersect( $locations, $valid_locations ) : array();
	$locations = count( $valid_locations ) !== count( $locations ) ? $locations : array();

	// Set the locations table.
	$locations_table = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' );

	// Set the where.
	$where = array( 'campaign_id' => $campaign_id );

	// Delete the campaign locations.
	psupsellmaster_db_campaign_locations_delete( $where );

	// Check if the locations is not empty.
	if ( ! empty( $locations ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $locations ), '( %d, #1# )' ) );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $locations ), $campaign_id ) );

		// Set the placeholders.
		$placeholders = str_replace( '#1#', '%s', $sql_values );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $locations );

		// Set the sql query.
		$sql_query = "INSERT INTO `{$locations_table}` ( `campaign_id`, `location` ) VALUES {$sql_values}";

		// Insert the locations.
		PsUpsellMaster_Database::query( $sql_query );
	}

	// Get the prices data.
	$prices_data = isset( $data['prices'] ) && is_array( $data['prices'] ) ? $data['prices'] : array();

	// Set the sanitized prices.
	$sanitized_prices = array();

	// Check if there are prices set.
	if ( isset( $prices_data['min'] ) || isset( $prices_data['max'] ) ) {
		// Set the sanitized prices.
		$sanitized_prices['min'] = filter_var( $prices_data['min'], FILTER_VALIDATE_INT );
		$sanitized_prices['max'] = filter_var( $prices_data['max'], FILTER_VALIDATE_INT );
	}

	// Check if the sanitized prices is empty.
	if ( empty( $sanitized_prices ) ) {
		// Delete the campaign meta.
		psupsellmaster_db_campaign_meta_delete( $campaign_id, 'prices' );

		// Otherwise...
	} else {
		// Update the campaign meta.
		psupsellmaster_db_campaign_meta_update( $campaign_id, 'prices', $sanitized_prices );
	}

	// Set the record types.
	$record_types = array(
		'include',
		'exclude',
	);

	// Set the products.
	$products = array();

	// Get the products data.
	$products_data = isset( $data['products'] ) && is_array( $data['products'] ) ? $data['products'] : array();

	// Set the products table.
	$products_table = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Loop through the record types.
	foreach ( $record_types as $type ) {
		// Set the products.
		$products[ $type ] = array();

		// Set the insert ids.
		$insert_ids = array();

		// Get the items data.
		$items_data = isset( $products_data[ $type ] ) && is_array( $products_data[ $type ] ) ? $products_data[ $type ] : array();

		// Loop through the items data.
		foreach ( $items_data as $item_data ) {
			// Check if the product id is not set.
			if ( ! isset( $item_data['product_id'] ) ) {
				// Skip this item.
				continue;
			}

			// Get the id.
			$data_id = filter_var( $item_data['product_id'], FILTER_VALIDATE_INT );

			// Check if the id is valid.
			if ( false === $data_id ) {
				// Skip this item.
				continue;
			}

			// Check if the id is already in the list.
			if ( in_array( $data_id, $insert_ids, true ) ) {
				// Skip this item.
				continue;
			}

			// Add the id to the list.
			array_push( $insert_ids, $data_id );
		}

		// Set the where.
		$where = array(
			'campaign_id' => $campaign_id,
			'type'        => $type,
		);

		// Delete the campaign products.
		psupsellmaster_db_campaign_products_delete( $where );

		// Check if the insert ids is not empty.
		if ( ! empty( $insert_ids ) ) {
			// Set the products.
			$products[ $type ] = $insert_ids;

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $insert_ids ), '( %d, #1#, #2# )' ) );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $campaign_id ) );

			// Set the placeholders.
			$placeholders = str_replace( '#1#', '%d', $sql_values );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $insert_ids );

			// Set the placeholders.
			$placeholders = str_replace( '#2#', '%s', $sql_values );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $type ) );

			// Set the sql query.
			$sql_query = "INSERT INTO `{$products_table}` ( `campaign_id`, `product_id`, `type` ) VALUES {$sql_values}";

			// Insert the products.
			PsUpsellMaster_Database::query( $sql_query );
		}
	}

	// Set the authors.
	$authors = array();

	// Get the authors data.
	$authors_data = isset( $data['authors'] ) && is_array( $data['authors'] ) ? $data['authors'] : array();

	// Set the authors table.
	$authors_table = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' );

	// Loop through the record types.
	foreach ( $record_types as $type ) {
		// Set the authors.
		$authors[ $type ] = array();

		// Set the insert ids.
		$insert_ids = array();

		// Get the items data.
		$items_data = isset( $authors_data[ $type ] ) && is_array( $authors_data[ $type ] ) ? $authors_data[ $type ] : array();

		// Loop through the items data.
		foreach ( $items_data as $item_data ) {
			// Check if the author id is not set.
			if ( ! isset( $item_data['author_id'] ) ) {
				// Skip this item.
				continue;
			}

			// Get the id.
			$data_id = filter_var( $item_data['author_id'], FILTER_VALIDATE_INT );

			// Check if the id is valid.
			if ( false === $data_id ) {
				// Skip this item.
				continue;
			}

			// Check if the id is already in the list.
			if ( in_array( $data_id, $insert_ids, true ) ) {
				// Skip this item.
				continue;
			}

			// Add the id to the list.
			array_push( $insert_ids, $data_id );
		}

		// Set the where.
		$where = array(
			'campaign_id' => $campaign_id,
			'type'        => $type,
		);

		// Delete the campaign authors.
		psupsellmaster_db_campaign_authors_delete( $where );

		// Check if the insert ids is not empty.
		if ( ! empty( $insert_ids ) ) {
			// Set the authors.
			$authors[ $type ] = $insert_ids;

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $insert_ids ), '( %d, #1#, #2# )' ) );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $campaign_id ) );

			// Set the placeholders.
			$placeholders = str_replace( '#1#', '%d', $sql_values );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $insert_ids );

			// Set the placeholders.
			$placeholders = str_replace( '#2#', '%s', $sql_values );

			// Set the sql values.
			$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $type ) );

			// Set the sql query.
			$sql_query = "INSERT INTO `{$authors_table}` ( `campaign_id`, `author_id`, `type` ) VALUES {$sql_values}";

			// Insert the authors.
			PsUpsellMaster_Database::query( $sql_query );
		}
	}

	// Set the taxonomies.
	$taxonomies = array();

	// Get the taxonomies data.
	$taxonomies_data = isset( $data['taxonomies'] ) && is_array( $data['taxonomies'] ) ? $data['taxonomies'] : array();

	// Set the taxonomies table.
	$taxonomies_table = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' );

	// Get the registered taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Loop through the product taxonomies.
	foreach ( $product_taxonomies as $taxonomy ) {
		// Loop through the record types.
		foreach ( $record_types as $type ) {
			// Set the taxonomies.
			$taxonomies[ $taxonomy ][ $type ] = array();

			// Set the insert ids.
			$insert_ids = array();

			// Set the items data.
			$items_data = array();

			// Check if the taxonomy and type are set.
			if ( isset( $taxonomies_data[ $taxonomy ] ) && isset( $taxonomies_data[ $taxonomy ][ $type ] ) ) {
				// Get the items data.
				$items_data = is_array( $taxonomies_data[ $taxonomy ][ $type ] ) ? $taxonomies_data[ $taxonomy ][ $type ] : array();
			}

			// Loop through the items data.
			foreach ( $items_data as $item_data ) {
				// Check if the term id is not set.
				if ( ! isset( $item_data['term_id'] ) ) {
					// Skip this item.
					continue;
				}

				// Get the id.
				$data_id = filter_var( $item_data['term_id'], FILTER_VALIDATE_INT );

				// Check if the id is valid.
				if ( false === $data_id ) {
					// Skip this item.
					continue;
				}

				// Check if the id is already in the list.
				if ( in_array( $data_id, $insert_ids, true ) ) {
					// Skip this item.
					continue;
				}

				// Add the id to the list.
				array_push( $insert_ids, $data_id );
			}

			// Set the where.
			$where = array(
				'campaign_id' => $campaign_id,
				'taxonomy'    => $taxonomy,
				'type'        => $type,
			);

			// Delete the campaign taxonomy terms.
			psupsellmaster_db_campaign_taxonomies_delete( $where );

			// Check if the insert ids is not empty.
			if ( ! empty( $insert_ids ) ) {
				// Set the taxonomies.
				$taxonomies[ $taxonomy ][ $type ] = $insert_ids;

				// Set the placeholders.
				$placeholders = implode( ', ', array_fill( 0, count( $insert_ids ), '( %d, #1#, #2#, #3# )' ) );

				// Set the sql values.
				$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $campaign_id ) );

				// Set the placeholders.
				$placeholders = str_replace( '#1#', '%d', $sql_values );

				// Set the sql values.
				$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $insert_ids );

				// Set the placeholders.
				$placeholders = str_replace( '#2#', '%s', $sql_values );

				// Set the sql values.
				$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $taxonomy ) );

				// Set the placeholders.
				$placeholders = str_replace( '#3#', '%s', $sql_values );

				// Set the sql values.
				$sql_values = PsUpsellMaster_Database::prepare( $placeholders, array_fill( 0, count( $insert_ids ), $type ) );

				// Set the sql query.
				$sql_query = "INSERT INTO `{$taxonomies_table}` ( `campaign_id`, `term_id`, `taxonomy`, `type` ) VALUES {$sql_values}";

				// Insert the taxonomies.
				PsUpsellMaster_Database::query( $sql_query );
			}
		}
	}

	// Set the valid origins.
	$valid_origins = array(
		'template',
		'user',
		'wizard',
	);

	// Set the origin.
	$origin = 'user';

	// Check if the origin is set.
	if ( isset( $data['origin'] ) ) {
		// Set the origin.
		$origin = in_array( $data['origin'], $valid_origins, true ) ? $data['origin'] : $origin;
	}

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'origin', $origin );

	// Get the template.
	$template = isset( $data['template'] ) ? $data['template'] : '';

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'template', $template );

	// Set the valid types.
	$valid_types = array(
		'all',
		'bundle',
	);

	// Set the products type.
	$products_type = 'all';

	// Check if the products type is set.
	if ( isset( $data['products_type'] ) ) {
		// Set the products type.
		$products_type = in_array( $data['products_type'], $valid_types, true ) ? $data['products_type'] : $products_type;
	}

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'products_type', $products_type );

	// Set the valid flags.
	$valid_flags = array(
		'all',
		'selected',
	);

	// Set the products flag.
	$products_flag = 'all';

	// Check if the products flag is set.
	if ( isset( $data['products_flag'] ) ) {
		// Set the products flag.
		$products_flag = in_array( $data['products_flag'], $valid_flags, true ) ? $data['products_flag'] : $products_flag;
	}

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'products_flag', $products_flag );

	// Set the locations flag.
	$locations_flag = 'all';

	// Check if the locations is not empty.
	if ( ! empty( $locations ) ) {
		// Check if the locations flag is set.
		if ( isset( $data['locations_flag'] ) ) {
			// Set the locations flag.
			$locations_flag = in_array( $data['locations_flag'], $valid_flags, true ) ? $data['locations_flag'] : $locations_flag;
		}
	}

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'locations_flag', $locations_flag );

	// Set the weekdays flag.
	$weekdays_flag = ! empty( $weekdays ) ? 'all' : 'selected';

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'weekdays_flag', $weekdays_flag );

	//
	// Conditions.
	//

	// Set the sanitized conditions.
	$sanitized_conditions = array();

	// Set the valid condition keys.
	$valid_condition_keys = array(
		'authors',
		'products',
		'subtotal',
		'taxonomies',
	);

	// Set the valid condition types.
	$valid_condition_types = array(
		'include',
		'exclude',
	);

	// Get the conditions.
	$conditions = isset( $data['conditions'] ) ? $data['conditions'] : array();
	$conditions = is_array( $conditions ) ? $conditions : array();

	// Loop through the conditions.
	foreach ( $conditions as $condition_key => $items ) {
		// Check if the condition key is not valid.
		if ( ! in_array( $condition_key, $valid_condition_keys, true ) ) {
			// Continue the loop.
			continue;
		}

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Continue the loop.
			continue;
		}

		// Check the condition key.
		switch ( $condition_key ) {
			case 'subtotal':
				if ( isset( $conditions[ $condition_key ]['min'] ) ) {
					// Set the value.
					$value = filter_var( $conditions[ $condition_key ]['min'], FILTER_VALIDATE_FLOAT );

					// Check if the value is valid.
					if ( false !== $value ) {
						// Set the sanitized conditions.
						$sanitized_conditions[ $condition_key ]['min'] = $value;
					}
				}

				break;
			case 'authors':
			case 'products':
				// Set the value key.
				$value_key = 'author_id';

				// Check the condition key.
				if ( 'products' === $condition_key ) {
					// Set the value key.
					$value_key = 'product_id';

					// Get the products count.
					$products_count = isset( $conditions[ $condition_key ]['count'] ) ? $conditions[ $condition_key ]['count'] : false;
					$products_count = is_array( $products_count ) ? $products_count : array();

					// Check if the min is set.
					if ( isset( $products_count['min'] ) ) {
						// Set the value.
						$value = filter_var( $products_count['min'], FILTER_VALIDATE_INT );

						// Check if the value is valid.
						if ( false !== $value ) {
							// Set the sanitized conditions.
							$sanitized_conditions[ $condition_key ]['count']['min'] = $value;
						}
					}
				}

				// Loop through the items.
				foreach ( $items as $item_key => $values ) {
					// Check if the type is not valid.
					if ( ! in_array( $item_key, $valid_condition_types, true ) ) {
						continue;
					}

					// Set the sanitized.
					$sanitized = array();

					// Loop through the values.
					foreach ( $values as $value ) {
						// Set the sanitized value.
						$sanitized_value = isset( $value[ $value_key ] ) ? filter_var( $value[ $value_key ], FILTER_VALIDATE_INT ) : false;

						// Check if the sanitized value is empty.
						if ( empty( $sanitized_value ) ) {
							continue;
						}

						// Add the sanitized value.
						array_push( $sanitized, array( $value_key => $sanitized_value ) );
					}

					// Check if the sanitized is empty.
					if ( empty( $sanitized ) ) {
						continue;
					}

					// Set the sanitized conditions.
					$sanitized_conditions[ $condition_key ][ $item_key ] = $sanitized;
				}

				break;
			case 'taxonomies':
				// Set the value key.
				$value_key = 'term_id';

				// Loop through the product taxonomies.
				foreach ( $product_taxonomies as $taxonomy ) {
					// Check if the taxonomy is not set.
					if ( ! isset( $conditions[ $condition_key ][ $taxonomy ] ) ) {
						continue;
					}

					// Set the items.
					$items = $conditions[ $condition_key ][ $taxonomy ];

					// Check if the items is not an array.
					if ( ! is_array( $items ) ) {
						continue;
					}

					// Loop through the items.
					foreach ( $items as $item_key => $values ) {
						// Check if the type is not valid.
						if ( ! in_array( $item_key, $valid_condition_types, true ) ) {
							continue;
						}

						// Set the sanitized.
						$sanitized = array();

						// Loop through the values.
						foreach ( $values as $value ) {
							// Set the sanitized value.
							$sanitized_value = isset( $value[ $value_key ] ) ? filter_var( $value[ $value_key ], FILTER_VALIDATE_INT ) : false;

							// Check if the sanitized value is empty.
							if ( empty( $sanitized_value ) ) {
								continue;
							}

							// Add the sanitized value.
							array_push( $sanitized, array( $value_key => $sanitized_value ) );
						}

						// Check if the sanitized is empty.
						if ( empty( $sanitized ) ) {
							continue;
						}

						// Set the sanitized conditions.
						$sanitized_conditions[ $condition_key ][ $taxonomy ][ $item_key ] = $sanitized;
					}
				}
		}
	}

	// Check if the sanitized conditions is empty.
	if ( empty( $sanitized_conditions ) ) {
		// Delete the campaign meta.
		psupsellmaster_db_campaign_meta_delete( $campaign_id, 'conditions' );
	} else {
		// Update the campaign meta.
		psupsellmaster_db_campaign_meta_update( $campaign_id, 'conditions', $sanitized_conditions );
	}

	// Set the sanitized synced.
	$sanitized_synced = array();

	// Set the valid synced keys.
	$valid_synced_keys = array(
		'taxonomies',
	);

	// Get the synced.
	$synced = isset( $data['synced'] ) ? $data['synced'] : array();
	$synced = is_array( $synced ) ? $synced : array();

	// Loop through the synced.
	foreach ( $synced as $synced_key => $items ) {
		// Check if the synced key is not valid.
		if ( ! in_array( $synced_key, $valid_synced_keys, true ) ) {
			// Continue the loop.
			continue;
		}

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Continue the loop.
			continue;
		}

		switch ( $synced_key ) {
			case 'taxonomies':
				// Set the sanitized synced.
				$sanitized_synced[ $synced_key ] = psupsellmaster_insert_mixed_taxonomy_terms( $synced );
		}
	}

	// Check if the sanitized synced is empty.
	if ( empty( $sanitized_synced ) ) {
		// Delete the campaign meta.
		psupsellmaster_db_campaign_meta_delete( $campaign_id, 'synced' );
	} else {
		// Update the campaign meta.
		psupsellmaster_db_campaign_meta_update( $campaign_id, 'synced', $sanitized_synced );
	}

	//
	// Display Options.
	//

	// Get the display options.
	$display_options = isset( $data['display_options'] ) ? $data['display_options'] : array();
	$display_options = is_array( $display_options ) ? $display_options : array();

	// Set the display keys.
	$display_keys = $valid_locations;

	// Add another key.
	array_unshift( $display_keys, 'all' );

	// Loop through the display keys.
	foreach ( $display_keys as $display_key ) {
		// Set the options data.
		$options_data = array();

		// Check if the location key is set.
		if ( isset( $display_options[ $display_key ] ) ) {
			// Get the options data.
			$options_data = is_array( $display_options[ $display_key ] ) ? $display_options[ $display_key ] : array();
		}

		// Set the update options.
		$update_options = array(
			'description'             => array(
				'value' => '',
				'type'  => '%s',
			),
			'desktop_banner_id'       => array(
				'value' => 0,
				'type'  => '%d',
			),
			'desktop_banner_url'      => array(
				'value' => '',
				'type'  => '%s',
			),
			'desktop_banner_link_url' => array(
				'value' => '',
				'type'  => '%s',
			),
			'mobile_banner_id'        => array(
				'value' => 0,
				'type'  => '%d',
			),
			'mobile_banner_url'       => array(
				'value' => '',
				'type'  => '%s',
			),
			'mobile_banner_link_url'  => array(
				'value' => '',
				'type'  => '%s',
			),
		);

		// Get the description.
		$description = isset( $options_data['description'] ) ? $options_data['description'] : '';

		// Get the desktop banner id.
		$desktop_banner_id = isset( $options_data['desktop_banner_id'] ) ? filter_var( $options_data['desktop_banner_id'], FILTER_VALIDATE_INT ) : false;

		// Get the desktop banner url.
		$desktop_banner_url = isset( $options_data['desktop_banner_url'] ) ? $options_data['desktop_banner_url'] : '';

		// Get the desktop banner link url.
		$desktop_banner_link_url = isset( $options_data['desktop_banner_link_url'] ) ? $options_data['desktop_banner_link_url'] : '';

		// Get the mobile banner id.
		$mobile_banner_id = isset( $options_data['mobile_banner_id'] ) ? filter_var( $options_data['mobile_banner_id'], FILTER_VALIDATE_INT ) : false;

		// Get the mobile banner url.
		$mobile_banner_url = isset( $options_data['mobile_banner_url'] ) ? $options_data['mobile_banner_url'] : '';

		// Get the mobile banner link url.
		$mobile_banner_link_url = isset( $options_data['mobile_banner_link_url'] ) ? $options_data['mobile_banner_link_url'] : '';

		// Check if the description is not empty.
		if ( ! empty( $description ) ) {
			// Set the data value.
			$update_options['description']['value'] = $description;
		}

		// Check if the desktop banner id is not empty.
		if ( ! empty( $desktop_banner_id ) ) {
			// Set the data value.
			$update_options['desktop_banner_id']['value'] = $desktop_banner_id;

			// Check if the desktop banner url is not empty.
			if ( ! empty( $desktop_banner_url ) ) {
				// Set the data value.
				$update_options['desktop_banner_url']['value'] = $desktop_banner_url;
			}
		}

		// Check if the desktop banner link url is not empty.
		if ( ! empty( $desktop_banner_link_url ) ) {
			// Set the data value.
			$update_options['desktop_banner_link_url']['value'] = $desktop_banner_link_url;
		}

		// Check if the mobile banner id is not empty.
		if ( ! empty( $mobile_banner_id ) ) {
			// Set the data value.
			$update_options['mobile_banner_id']['value'] = $mobile_banner_id;

			// Check if the mobile banner url is not empty.
			if ( ! empty( $mobile_banner_url ) ) {
				// Set the data value.
				$update_options['mobile_banner_url']['value'] = $mobile_banner_url;
			}
		}

		// Check if the mobile banner link url is not empty.
		if ( ! empty( $mobile_banner_link_url ) ) {
			// Set the data value.
			$update_options['mobile_banner_link_url']['value'] = $mobile_banner_link_url;
		}

		// Loop through the update options.
		foreach ( $update_options as $option_name => $update_data ) {
			// Get the data value.
			$option_value = isset( $update_data['value'] ) ? $update_data['value'] : '';

			// Get the data type.
			$option_type = isset( $update_data['type'] ) ? $update_data['type'] : '%s';

			// Set the sql query.
			$sql_query = PsUpsellMaster_Database::prepare(
				'
				SELECT
					`option_value`
				FROM
					%i
				WHERE
					1 = 1
				AND
					`campaign_id` = %d
				AND
					`location` = %s
				AND
					`option_name` = %s
				',
				PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' ),
				$campaign_id,
				$display_key,
				$option_name
			);

			// Get the stored data.
			$stored_data = PsUpsellMaster_Database::get_var( $sql_query );

			// Check the data type.
			if ( '%d' === $option_type ) {
				// Set the stored data.
				$stored_data = filter_var( $stored_data, FILTER_VALIDATE_INT );

				// Check the data type.
			} elseif ( '%f' === $option_type ) {
				// Set the stored data.
				$stored_data = filter_var( $stored_data, FILTER_VALIDATE_FLOAT );
			}

			// Check if the stored data does not match the new data.
			if ( $stored_data !== $option_value ) {
				// Set the where.
				$where = array(
					'campaign_id' => $campaign_id,
					'location'    => $display_key,
					'option_name' => $option_name,
				);

				// Delete the display option.
				psupsellmaster_db_campaign_display_options_delete( $where );

				// Check if the new data is not empty.
				if ( ! empty( $option_value ) ) {
					// Set the insert data.
					$insert_data = array(
						'campaign_id'  => $campaign_id,
						'location'     => $display_key,
						'option_name'  => $option_name,
						'option_value' => $option_value,
					);

					// Insert the display option.
					psupsellmaster_db_campaign_display_options_insert( $insert_data );
				}
			}
		}
	}

	//
	// Product Page.
	//

	// Get the product page.
	$product_page = isset( $data['product_page'] ) ? $data['product_page'] : array();
	$product_page = is_array( $product_page ) ? $product_page : array();

	// Set the meta value.
	$meta_value = array(
		'description'             => '',
		'desktop_banner_id'       => 0,
		'desktop_banner_url'      => '',
		'desktop_banner_link_url' => '',
		'mobile_banner_id'        => 0,
		'mobile_banner_url'       => '',
		'mobile_banner_link_url'  => '',
	);

	// Check the description.
	if ( isset( $product_page['description'] ) ) {
		// Set the meta value.
		$meta_value['description'] = $product_page['description'];
	}

	// Check the desktop banner id.
	if ( isset( $product_page['desktop_banner_id'] ) ) {
		// Set the meta value.
		$meta_value['desktop_banner_id'] = filter_var( $product_page['desktop_banner_id'], FILTER_VALIDATE_INT );
		$meta_value['desktop_banner_id'] = false !== $meta_value['desktop_banner_id'] ? $meta_value['desktop_banner_id'] : 0;

		// Check the desktop banner url.
		if ( isset( $product_page['desktop_banner_url'] ) ) {
			// Set the meta value.
			$meta_value['desktop_banner_url'] = $product_page['desktop_banner_url'];
		}
	}

	// Check the desktop banner link url.
	if ( isset( $product_page['desktop_banner_link_url'] ) ) {
		// Set the meta value.
		$meta_value['desktop_banner_link_url'] = $product_page['desktop_banner_link_url'];
	}

	// Check the mobile banner id.
	if ( isset( $product_page['mobile_banner_id'] ) ) {
		// Set the meta value.
		$meta_value['mobile_banner_id'] = filter_var( $product_page['mobile_banner_id'], FILTER_VALIDATE_INT );
		$meta_value['mobile_banner_id'] = false !== $meta_value['mobile_banner_id'] ? $meta_value['mobile_banner_id'] : 0;

		// Check the mobile banner url.
		if ( isset( $product_page['mobile_banner_url'] ) ) {
			// Set the meta value.
			$meta_value['mobile_banner_url'] = $product_page['mobile_banner_url'];
		}
	}

	// Check the mobile banner link url.
	if ( isset( $product_page['mobile_banner_link_url'] ) ) {
		// Set the meta value.
		$meta_value['mobile_banner_link_url'] = $product_page['mobile_banner_link_url'];
	}

	// Update the campaign meta.
	psupsellmaster_db_campaign_meta_update( $campaign_id, 'product_page', $meta_value );

	//
	// Coupons.
	//

	// Get the coupon id.
	$coupon_id = isset( $data['coupon_id'] ) ? $data['coupon_id'] : '';

	// Get the coupon code.
	$coupon_code = isset( $data['coupon_code'] ) ? $data['coupon_code'] : '';

	// Get the coupon type.
	$coupon_type = isset( $data['coupon_type'] ) ? $data['coupon_type'] : '';

	// Get the coupon amount.
	$coupon_amount = isset( $data['coupon_amount'] ) ? $data['coupon_amount'] : '';

	// Set the valid flags.
	$valid_flags = array(
		'campaign',
		'standard',
	);

	// Set the coupons flag.
	$coupons_flag = 'campaign';

	// Check if the coupons flag is set.
	if ( isset( $data['coupons_flag'] ) ) {
		// Set the coupons flag.
		$coupons_flag = in_array( $data['coupons_flag'], $valid_flags, true ) ? $data['coupons_flag'] : $coupons_flag;
	}

	// Check the coupons flag.
	if ( 'standard' === $coupons_flag ) {
		// Get the standard coupon id.
		$standard_coupon_id = ! empty( $data['standard_coupon_id'] ) ? $data['standard_coupon_id'] : false;

		// Get the standard coupon code.
		$standard_coupon_code = psupsellmaster_get_coupon_code( $standard_coupon_id );

		// Check the standard coupon code.
		if ( ! empty( $standard_coupon_code ) ) {
			// Update the coupon status (the previous coupon).
			psupsellmaster_update_integrated_coupon_status( $coupon_id, 'archived' );

			// Set the coupon id.
			$coupon_id = $standard_coupon_id;

			// Set the coupon code.
			$coupon_code = $standard_coupon_code;
		}
	}

	// Set the args.
	$args = array(
		'campaign_id' => $campaign_id,
		'coupon_id'   => $coupon_id,
		'status'      => $status,
		'code'        => $coupon_code,
		'type'        => $coupon_type,
		'amount'      => $coupon_amount,
		'start_date'  => $start_date,
		'end_date'    => $end_date,
		'products'    => $products,
		'taxonomies'  => $taxonomies,
	);

	// Save the campaign coupon.
	psupsellmaster_save_campaign_coupon( $args );

	// Set the active campaigns.
	psupsellmaster_campaigns_set_status_active();

	// Set the scheduled campaigns.
	psupsellmaster_campaigns_set_status_scheduled();

	// Set the expired campaigns.
	psupsellmaster_campaigns_set_status_expired();

	// Set the args.
	$args = array( 'campaigns' => array( $campaign_id ) );

	// Update the eligible products.
	psupsellmaster_campaigns_update_all_eligible_products( $args );

	// Sync some campaign data with its eligible products.
	psupsellmaster_campaigns_sync_eligible_products( $args );

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();

	// Return the id.
	return $campaign_id;
}

/**
 * Save the campaign coupon.
 *
 * @param array $args The coupon args.
 */
function psupsellmaster_save_campaign_coupon( $args ) {
	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return false;
	}

	// Set the valid types.
	$valid_types = array(
		'discount_fixed',
		'discount_percent',
	);

	// Get the coupon id.
	$coupon_id = isset( $args['coupon_id'] ) ? filter_var( $args['coupon_id'], FILTER_VALIDATE_INT ) : false;

	// Get the code.
	$code = isset( $args['code'] ) ? $args['code'] : '';

	// Get the type.
	$type = isset( $args['type'] ) ? $args['type'] : '';
	$type = in_array( $type, $valid_types, true ) ? $type : 'discount_percent';

	// Get the amount.
	$amount = isset( $args['amount'] ) ? filter_var( $args['amount'], FILTER_VALIDATE_FLOAT ) : false;
	$amount = false !== $amount ? $amount : 0;

	// Check the coupon id.
	if ( empty( $coupon_id ) ) {
		// Set the code.
		$code = ! empty( $code ) ? $code : strtoupper( 'psupsellmaster' );

		// Generate a new unique code.
		$code = psupsellmaster_generate_unique_coupon_code( $code );

		// Set the code.
		$args['code'] = $code;

		// Otherwise...
	} else {
		// Set the coupon id.
		$args['coupon_id'] = $coupon_id;

		// Get the stored code.
		$stored_code = psupsellmaster_get_coupon_code( $coupon_id );

		// Check the code.
		if ( empty( $stored_code ) && empty( $code ) ) {
			// Set the code.
			$code = strtoupper( 'psupsellmaster' );
		}

		// Check if the code does not match.
		if ( ! empty( $code ) && $stored_code !== $code ) {
			// Generate a new unique code.
			$code = psupsellmaster_generate_unique_coupon_code( $code );
		}

		// Check the code.
		if ( ! empty( $code ) ) {
			// Set the code.
			$args['code'] = $code;
		}
	}

	// Set the type.
	$args['type'] = $type;

	// Set the amount.
	$args['amount'] = $amount;

	// Save the integrated coupon.
	$coupon_id = psupsellmaster_save_integrated_coupon( $args );

	// Get the code.
	$code = psupsellmaster_get_coupon_code( $coupon_id );

	// Set the where.
	$where = array( 'campaign_id' => $campaign_id );

	// Delete the campaign coupons.
	psupsellmaster_db_campaign_coupons_delete( $where );

	// Set the data.
	$data = array(
		'campaign_id' => $campaign_id,
		'coupon_id'   => $coupon_id,
		'code'        => $code,
		'type'        => $type,
		'amount'      => $amount,
	);

	// Insert the coupon.
	psupsellmaster_db_campaign_coupons_insert( $data );
}

/**
 * Save the campaign coupon.
 *
 * @param array $args The coupon args.
 * @return int The coupon id.
 */
function psupsellmaster_save_integrated_coupon( $args ) {
	// Set the coupon id.
	$coupon_id = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the coupon id.
		$coupon_id = psupsellmaster_woo_save_coupon( $args );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the coupon id.
		$coupon_id = psupsellmaster_edd_save_coupon( $args );
	}

	// Return the coupon id.
	return $coupon_id;
}

/**
 * Delete an integrated coupon.
 *
 * @param int $coupon_id The coupon id.
 */
function psupsellmaster_delete_integrated_coupon( $coupon_id ) {
	// Update the coupon status.
	psupsellmaster_update_integrated_coupon_status( $coupon_id, 'archived' );

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Delete the coupon.
		psupsellmaster_woo_delete_coupon( $coupon_id );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Delete the coupon.
		psupsellmaster_edd_delete_coupon( $coupon_id );
	}
}

/**
 * Delete the integrated coupons.
 *
 * @param int $campaign_id The campaign id.
 */
function psupsellmaster_delete_integrated_coupons( $campaign_id ) {
	// Set the where.
	$where = array( 'campaign_id' => $campaign_id );

	// Get the coupons.
	$coupons = psupsellmaster_db_campaign_coupons_select( $where );

	// Loop through the coupons.
	foreach ( $coupons as $coupon ) {
		// Get the coupon id.
		$coupon_id = isset( $coupon->coupon_id ) ? filter_var( $coupon->coupon_id, FILTER_VALIDATE_INT ) : false;

		// Check if the coupon id is empty.
		if ( empty( $coupon_id ) ) {
			continue;
		}

		// Update the coupon status.
		psupsellmaster_update_integrated_coupon_status( $coupon_id, 'inactive' );

		// Delete the coupon.
		psupsellmaster_delete_integrated_coupon( $coupon_id );
	}
}

/**
 * Duplicate an integrated coupon.
 *
 * @param array $args The coupon args.
 * @return int The duplicate id.
 */
function psupsellmaster_duplicate_integrated_coupon( $args ) {
	// Set the duplicate id.
	$duplicate_id = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Duplicate the coupon and get the duplicate id.
		$duplicate_id = psupsellmaster_woo_duplicate_coupon( $args );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Duplicate the coupon and get the duplicate id.
		$duplicate_id = psupsellmaster_edd_duplicate_coupon( $args );
	}

	// Return the duplicate id.
	return $duplicate_id;
}

/**
 * Save the campaign settings.
 */
function psupsellmaster_save_campaign_settings() {
	// Get the page.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

	// Check if the page is not campaigns.
	if ( 'psupsellmaster-campaigns' !== $page ) {
		return false;
	}

	// Get the view.
	$view = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';

	// Check if the view is not valid.
	if ( ! in_array( $view, array( 'edit', 'new' ), true ) ) {
		return false;
	}

	// Get the action.
	$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

	// Check if the action is not save_campaign.
	if ( 'save_campaign' !== $action ) {
		return false;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	// Get the campaign id.
	$campaign_id = isset( $_POST['campaign_id'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign_id'] ) ) : '';

	// Get the title.
	$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';

	// Get the status.
	$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

	// Get the priority.
	$priority = isset( $_POST['priority'] ) ? sanitize_text_field( wp_unslash( $_POST['priority'] ) ) : 10;

	// Get the coupon code.
	$coupon_code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) : '';

	// Get the start date.
	$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) : '';
	$start_date = str_replace( '/', '-', $start_date );

	// Get the end date.
	$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) : '';
	$end_date = str_replace( '/', '-', $end_date );

	// Get the standard coupon id.
	$standard_coupon_id = isset( $_POST['standard_coupon_id'] ) ? sanitize_text_field( wp_unslash( $_POST['standard_coupon_id'] ) ) : '';

	// Get the coupon id.
	$coupon_id = isset( $_POST['coupon_id'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_id'] ) ) : '';

	// Get the coupon type.
	$coupon_type = isset( $_POST['coupon_type'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_type'] ) ) : '';

	// Get the coupon amount.
	$coupon_amount = isset( $_POST['coupon_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_amount'] ) ) : '';

	// Set the weekdays.
	$weekdays = array();

	// Check the weekdays flag.
	if ( isset( $_POST['weekdays'] ) ) {
		// Get the weekdays.
		$weekdays = is_array( $_POST['weekdays'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['weekdays'] ) ) : array();
	}

	// Get the locations flag.
	$locations_flag = isset( $_POST['locations_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['locations_flag'] ) ) : '';

	// Set the locations.
	$locations = array();

	// Check the locations flag.
	if ( 'all' !== $locations_flag && isset( $_POST['locations'] ) ) {
		// Get the locations.
		$locations = is_array( $_POST['locations'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['locations'] ) ) : array();
	}

	// Get the coupons flag.
	$coupons_flag = isset( $_POST['coupons_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['coupons_flag'] ) ) : '';

	// Get the products flag.
	$products_flag = isset( $_POST['products_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['products_flag'] ) ) : '';

	// Get the products type.
	$products_type = isset( $_POST['products_type'] ) ? sanitize_text_field( wp_unslash( $_POST['products_type'] ) ) : '';

	// Get the prices.
	$prices = isset( $_POST['prices'] ) ? map_deep( wp_unslash( $_POST['prices'] ), 'sanitize_text_field' ) : array();
	$prices = ! empty( $prices ) && is_array( $prices ) ? $prices : array();

	// Get the products.
	$products = isset( $_POST['products'] ) ? map_deep( wp_unslash( $_POST['products'] ), 'sanitize_text_field' ) : array();
	$products = ! empty( $products ) && is_array( $products ) ? $products : array();

	// Get the authors.
	$authors = isset( $_POST['authors'] ) ? map_deep( wp_unslash( $_POST['authors'] ), 'sanitize_text_field' ) : array();
	$authors = ! empty( $authors ) && is_array( $authors ) ? $authors : array();

	// Get the taxonomies.
	$taxonomies = isset( $_POST['taxonomies'] ) ? map_deep( wp_unslash( $_POST['taxonomies'] ), 'sanitize_text_field' ) : array();
	$taxonomies = ! empty( $taxonomies ) && is_array( $taxonomies ) ? $taxonomies : array();

	// Get the conditions.
	$conditions = isset( $_POST['conditions'] ) ? map_deep( wp_unslash( $_POST['conditions'] ), 'sanitize_text_field' ) : array();
	$conditions = ! empty( $conditions ) && is_array( $conditions ) ? $conditions : array();

	// Get the synced.
	$synced = isset( $_POST['synced'] ) ? map_deep( wp_unslash( $_POST['synced'] ), 'sanitize_text_field' ) : array();
	$synced = ! empty( $synced ) && is_array( $synced ) ? $synced : array();

	// Get the display options.
	$display_options = isset( $_POST['display_options'] ) ? map_deep( wp_unslash( $_POST['display_options'] ), 'wp_kses_post' ) : array();
	$display_options = ! empty( $display_options ) && is_array( $display_options ) ? $display_options : array();

	// Get the page product.
	$product_page = isset( $_POST['product_page'] ) ? map_deep( wp_unslash( $_POST['product_page'] ), 'wp_kses_post' ) : array();
	$product_page = ! empty( $product_page ) && is_array( $product_page ) ? $product_page : array();

	// Set the data.
	$data = array(
		'campaign_id'        => $campaign_id,
		'title'              => $title,
		'status'             => $status,
		'priority'           => $priority,
		'start_date'         => $start_date,
		'end_date'           => $end_date,
		'standard_coupon_id' => $standard_coupon_id,
		'coupon_id'          => $coupon_id,
		'coupon_code'        => $coupon_code,
		'coupon_type'        => $coupon_type,
		'coupon_amount'      => $coupon_amount,
		'coupons_flag'       => $coupons_flag,
		'products_flag'      => $products_flag,
		'products_type'      => $products_type,
		'locations_flag'     => $locations_flag,
		'weekdays'           => $weekdays,
		'locations'          => $locations,
		'prices'             => $prices,
		'products'           => $products,
		'authors'            => $authors,
		'taxonomies'         => $taxonomies,
		'conditions'         => $conditions,
		'synced'             => $synced,
		'display_options'    => $display_options,
		'product_page'       => $product_page,
	);

	// Save the campaign.
	$campaign_id = psupsellmaster_save_campaign( $data );

	// Check if the campaign id is not empty.
	if ( ! empty( $campaign_id ) ) {
		// Set the edit url.
		$edit_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign_id );

		// Redirect to the edit page.
		wp_safe_redirect( $edit_url );

		// Exit.
		exit;
	}
}
add_action( 'admin_init', 'psupsellmaster_save_campaign_settings' );

/**
 * Set the campaigns status.
 *
 * @param array $ids    The ids.
 * @param int   $status The status.
 */
function psupsellmaster_set_campaigns_status( $ids, $status ) {
	// Set the data.
	$data = array( 'status' => $status );

	// Check if this is the lite version and the status is active.
	if ( psupsellmaster_is_lite() && 'active' === $status ) {
		// Set the inactive campaigns.
		psupsellmaster_campaigns_set_status_inactive();

		// Set the ids.
		$ids = array( array_shift( $ids ) );
	}

	// Loop through the ids.
	foreach ( $ids as $id ) {
		// Set the where.
		$where = array( 'id' => $id );

		// Update the campaigns status.
		psupsellmaster_db_campaigns_update( $data, $where );

		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_get_campaign_coupons( $id );

		// Loop through the campaign coupons.
		foreach ( $campaign_coupons as $coupon_id ) {
			// Update the coupon status.
			psupsellmaster_update_integrated_coupon_status( $coupon_id, $status );
		}
	}

	// Set the active campaigns.
	psupsellmaster_campaigns_set_status_active();

	// Set the scheduled campaigns.
	psupsellmaster_campaigns_set_status_scheduled();

	// Set the expired campaigns.
	psupsellmaster_campaigns_set_status_expired();

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();
}

/**
 * Duplicate a campaign.
 *
 * @param int $id The id.
 * @return bool Whether the campaign was duplicated.
 */
function psupsellmaster_duplicate_campaign( $id ) {
	// Set the duplicate id.
	$duplicate_id = false;

	//
	// Duplicate the campaign.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$id
	);

	// Get the data.
	$data = PsUpsellMaster_Database::get_row( $sql_query, ARRAY_A );

	// Set the id.
	$data['id'] = 0;

	// Set the status.
	$data['status'] = 'inactive';

	// Set the title.
	$data['title'] = $data['title'] . ' (Duplicate)';

	// Get the dates.
	$dates = psupsellmaster_campaigns_get_next_dates( $data );

	// Set the start date.
	$data['start_date'] = $dates['start_date'];

	// Set the end date.
	$data['end_date'] = $dates['end_date'];

	// Set the priority.
	$data['priority'] = filter_var( $data['priority'], FILTER_VALIDATE_INT );

	// Check the priority.
	if ( false === $data['priority'] ) {
		// Set the priority.
		$data['priority'] = 10;

		// Check the priority.
	} elseif ( $data['priority'] < 1 ) {
		// Set the priority.
		$data['priority'] = 1;

		// Check the priority.
	} elseif ( $data['priority'] > 100 ) {
		// Set the priority.
		$data['priority'] = 100;
	}

	// Insert the new campaign.
	psupsellmaster_db_campaigns_insert( $data );

	// Set the duplicate id.
	$duplicate_id = psupsellmaster_db_get_inserted_id();

	//
	// Duplicate the campaignmeta.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `psupsellmaster_campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['meta_id'] = 0;

		// Set the campaign id.
		$row['psupsellmaster_campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaignmeta_insert( $row );
	}

	//
	// Duplicate the campaign coupons.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Set the code.
		$row['code'] .= '-DUPLICATE';
		$row['code']  = psupsellmaster_generate_unique_coupon_code( $row['code'] );

		// Set the args.
		$args = array(
			'coupon_id' => $row['coupon_id'],
			'code'      => $row['code'],
		);

		// Set the coupon id.
		$row['coupon_id'] = psupsellmaster_duplicate_integrated_coupon( $args );

		// Insert the new row.
		psupsellmaster_db_campaign_coupons_insert( $row );
	}

	//
	// Duplicate the campaign products.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_products_insert( $row );
	}

	//
	// Duplicate the campaign authors.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_authors_insert( $row );
	}

	//
	// Duplicate the campaign taxonomies.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_taxonomies_insert( $row );
	}

	//
	// Duplicate the campaign weekdays.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_weekdays_insert( $row );
	}

	//
	// Duplicate the campaign locations.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_locations_insert( $row );
	}

	//
	// Duplicate the campaign display options.
	//

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'SELECT * FROM %i WHERE `campaign_id` = %d',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' ),
		$id
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query, ARRAY_A );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Set the id.
		$row['id'] = 0;

		// Set the campaign id.
		$row['campaign_id'] = $duplicate_id;

		// Insert the new row.
		psupsellmaster_db_campaign_display_options_insert( $row );
	}

	// Return the duplicate id.
	return $duplicate_id;
}

/**
 * Delete a campaign.
 *
 * @param array $id The campaign id.
 */
function psupsellmaster_delete_campaign( $id ) {
	// Set the deleted.
	$deleted = false;

	// Set the tables.
	$tables = array(
		'psupsellmaster_campaign_carts',
		'psupsellmaster_campaign_events',
		'psupsellmaster_campaign_display_options',
		'psupsellmaster_campaign_eligible_products',
		'psupsellmaster_campaign_taxonomies',
		'psupsellmaster_campaign_authors',
		'psupsellmaster_campaign_products',
		'psupsellmaster_campaign_locations',
		'psupsellmaster_campaign_weekdays',
		'psupsellmaster_campaign_coupons',
		'psupsellmaster_campaignmeta',
		'psupsellmaster_campaigns',
	);

	// Delete the integrated coupons.
	psupsellmaster_delete_integrated_coupons( $id );

	// Loop through the tables.
	foreach ( $tables as $table ) {
		// Set the where.
		$where = array( 'campaign_id' => $id );

		// Check if the current table is the main table.
		if ( 'psupsellmaster_campaigns' === $table ) {
			// Set the where.
			$where = array( 'id' => $id );

			// Check if the current table is the meta table.
		} elseif ( 'psupsellmaster_campaignmeta' === $table ) {
			// Set the where.
			$where = array( 'psupsellmaster_campaign_id' => $id );
		}

		// Delete the table row.
		$deleted = ! empty( PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( $table ), $where ) );
	}

	// Delete the campaigns caches.
	psupsellmaster_campaigns_purge_caches();

	// Return the deleted.
	return $deleted;
}

/**
 * Get the campaigns kpis.
 *
 * @param array $args The campaigns kpis arguments.
 * @return array The campaigns kpis.
 */
function psupsellmaster_get_campaigns_kpis( $args = array() ) {
	// Set the kpis.
	$kpis = array(
		'#campaigns'                 => array(
			'label'     => __( '# Campaigns', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#impressions'               => array(
			'label'     => __( '# Impressions', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#clicks'                    => array(
			'label'     => __( '# Clicks', 'psupsellmaster' ),
			'formatted' => '',
		),
		'%ctr'                       => array(
			'label'     => __( 'CTR', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#add_to_cart'               => array(
			'label'     => __( '# Add to Cart', 'psupsellmaster' ),
			'formatted' => '',
		),
		'%add_to_cart'               => array(
			'label'     => __( 'Add to Cart', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#impressions_per_campaign'  => array(
			'label'     => __( '# Impressions per Campaign', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#clicks_per_campaign'       => array(
			'label'     => __( '# Clicks per Campaign', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#add_to_cart_per_campaign'  => array(
			'label'     => __( '# Add to Cart per Campaign', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#carts'                     => array(
			'label'     => __( '# Carts', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#orders'                    => array(
			'label'     => __( '# Orders', 'psupsellmaster' ),
			'formatted' => '',
		),
		'%orders'                    => array(
			'label'     => __( 'Orders', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#free_orders'               => array(
			'label'     => __( '# Free Orders', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#paid_orders'               => array(
			'label'     => __( '# Paid Orders', 'psupsellmaster' ),
			'formatted' => '',
		),
		'%paid_orders'               => array(
			'label'     => __( 'Paid Orders', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$gross_earnings'            => array(
			'label'     => __( 'Gross Earnings', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#taxes'                     => array(
			'label'     => __( '# Taxes', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$taxes'                     => array(
			'label'     => __( 'Taxes', 'psupsellmaster' ),
			'formatted' => '',
		),
		'#discounts'                 => array(
			'label'     => __( '# Discounts', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$discounts'                 => array(
			'label'     => __( 'Discounts', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$net_earnings'              => array(
			'label'     => __( 'Net Earnings', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$net_earnings_per_campaign' => array(
			'label'     => __( 'Net Earnings per Campaign', 'psupsellmaster' ),
			'formatted' => '',
		),
		'$aov'                       => array(
			'label'     => __( 'AOV', 'psupsellmaster' ),
			'formatted' => '',
		),
		'%cart_abandoment'           => array(
			'label'     => __( 'Cart Abandonment', 'psupsellmaster' ),
			'formatted' => '',
		),
	);

	//
	// KPI: # Campaigns.
	//

	// Set the value.
	$value = psupsellmaster_get_campaigns_count( $args );

	// Set the kpi plain.
	$kpis['#campaigns']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#campaigns']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Impressions.
	//

	// Set the event name.
	$args['event_name'] = 'impression';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#impressions']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#impressions']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Clicks.
	//

	// Set the event name.
	$args['event_name'] = 'click';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#clicks']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#clicks']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % CTR.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#clicks']['plain'],
		$kpis['#impressions']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%ctr']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%ctr']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Add to cart.
	//

	// Set the event name.
	$args['event_name'] = 'add_to_cart';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#add_to_cart']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#add_to_cart']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Add to cart.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#add_to_cart']['plain'],
		$kpis['#clicks']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%add_to_cart']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%add_to_cart']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Impressions per Campaign.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#impressions']['plain'],
		$kpis['#campaigns']['plain']
	);

	// Set the kpi plain.
	$kpis['#impressions_per_campaign']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#impressions_per_campaign']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Clicks per Campaign.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#clicks']['plain'],
		$kpis['#campaigns']['plain']
	);

	// Set the kpi plain.
	$kpis['#clicks_per_campaign']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#clicks_per_campaign']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Add to Cart per Campaign.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#add_to_cart']['plain'],
		$kpis['#campaigns']['plain']
	);

	// Set the kpi plain.
	$kpis['#add_to_cart_per_campaign']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#add_to_cart_per_campaign']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Carts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_carts_count( $args );

	// Set the kpi plain.
	$kpis['#carts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#carts']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_orders_count( $args );

	// Set the kpi plain.
	$kpis['#orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Orders.
	//

	// Get the carts count.
	$carts_count = psupsellmaster_get_campaign_carts_count( $args );

	// Set the value.
	$value = psupsellmaster_safe_divide( $kpis['#orders']['plain'], $carts_count );

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%orders']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%orders']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Free Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_free_orders_count( $args );

	// Set the kpi plain.
	$kpis['#free_orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#free_orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Paid Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_paid_orders_count( $args );

	// Set the kpi plain.
	$kpis['#paid_orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#paid_orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Paid Orders.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#paid_orders']['plain'],
		$kpis['#orders']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%paid_orders']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: $ Gross Earnings.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_gross_earnings( $args );

	// Set the kpi plain.
	$kpis['$gross_earnings']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$gross_earnings']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: # Taxes.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_taxes_count( $args );

	// Set the kpi plain.
	$kpis['#taxes']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#taxes']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: $ Taxes.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_taxes_amount( $args );

	// Set the kpi plain.
	$kpis['$taxes']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$taxes']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: # Discounts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_discounts_count( $args );

	// Set the kpi plain.
	$kpis['#discounts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#discounts']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: $ Discounts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_discounts_amount( $args );

	// Set the kpi plain.
	$kpis['$discounts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$discounts']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: $ Net Earnings.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_net_earnings( $args );

	// Set the kpi plain.
	$kpis['$net_earnings']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$net_earnings']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: $ Net Earnings per Campaign.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['$net_earnings']['plain'],
		$kpis['#campaigns']['plain']
	);

	// Set the kpi plain.
	$kpis['$net_earnings_per_campaign']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['$net_earnings_per_campaign']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: $ AOV.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['$net_earnings']['plain'],
		$kpis['#paid_orders']['plain']
	);

	// Set the kpi plain.
	$kpis['$aov']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['$aov']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: % Cart Abandonment.
	//

	// Set the value.
	$value = $kpis['#carts']['plain'] > 0 ? 100 - $kpis['%orders']['plain'] : 0;

	// Set the kpi plain.
	$kpis['%cart_abandoment']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['%cart_abandoment']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	// Return the kpis.
	return $kpis;
}

/**
 * Get the campaign kpis.
 *
 * @param array $args The campaign kpis arguments.
 * @return array The campaign kpis.
 */
function psupsellmaster_get_campaign_kpis( $args = array() ) {
	// Set the kpis.
	$kpis = array(
		'#impressions'     => array(
			'label'     => '# Impressions',
			'formatted' => '',
		),
		'#clicks'          => array(
			'label'     => '# Clicks',
			'formatted' => '',
		),
		'%ctr'             => array(
			'label'     => 'CTR',
			'formatted' => '',
		),
		'#add_to_cart'     => array(
			'label'     => '# Add to Cart',
			'formatted' => '',
		),
		'%add_to_cart'     => array(
			'label'     => 'Add to Cart',
			'formatted' => '',
		),
		'#carts'           => array(
			'label'     => '# Carts',
			'formatted' => '',
		),
		'#orders'          => array(
			'label'     => '# Orders',
			'formatted' => '',
		),
		'%orders'          => array(
			'label'     => 'Orders',
			'formatted' => '',
		),
		'#free_orders'     => array(
			'label'     => '# Free Orders',
			'formatted' => '',
		),
		'#paid_orders'     => array(
			'label'     => '# Paid Orders',
			'formatted' => '',
		),
		'%paid_orders'     => array(
			'label'     => '% Paid Orders',
			'formatted' => '',
		),
		'$gross_earnings'  => array(
			'label'     => 'Gross Earnings',
			'formatted' => '',
		),
		'#taxes'           => array(
			'label'     => '# Taxes',
			'formatted' => '',
		),
		'$taxes'           => array(
			'label'     => 'Taxes',
			'formatted' => '',
		),
		'#discounts'       => array(
			'label'     => '# Discounts',
			'formatted' => '',
		),
		'$discounts'       => array(
			'label'     => 'Discounts',
			'formatted' => '',
		),
		'$net_earnings'    => array(
			'label'     => 'Net Earnings',
			'formatted' => '',
		),
		'$aov'             => array(
			'label'     => 'AOV',
			'formatted' => '',
		),
		'%cart_abandoment' => array(
			'label'     => 'Cart Abandonment',
			'formatted' => '',
		),
	);

	//
	// KPI: # Impressions.
	//

	// Set the event name.
	$args['event_name'] = 'impression';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#impressions']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#impressions']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Clicks.
	//

	// Set the event name.
	$args['event_name'] = 'click';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#clicks']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#clicks']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % CTR.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#clicks']['plain'],
		$kpis['#impressions']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%ctr']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%ctr']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Add to cart.
	//

	// Set the event name.
	$args['event_name'] = 'add_to_cart';

	// Set the value.
	$value = psupsellmaster_get_campaign_events_quantity( $args );

	// Set the kpi plain.
	$kpis['#add_to_cart']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#add_to_cart']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Add to cart.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#add_to_cart']['plain'],
		$kpis['#clicks']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%add_to_cart']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%add_to_cart']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Carts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_carts_count( $args );

	// Set the kpi plain.
	$kpis['#carts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#carts']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_orders_count( $args );

	// Set the kpi plain.
	$kpis['#orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Orders.
	//

	// Get the carts count.
	$carts_count = psupsellmaster_get_campaign_carts_count( $args );

	// Set the value.
	$value = psupsellmaster_safe_divide( $kpis['#orders']['plain'], $carts_count );

	// Set the value.
	$value *= 100;

	// Set the kpi plain.
	$kpis['%orders']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%orders']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: # Free Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_free_orders_count( $args );

	// Set the kpi plain.
	$kpis['#free_orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#free_orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: # Paid Orders.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_paid_orders_count( $args );

	// Set the kpi plain.
	$kpis['#paid_orders']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#paid_orders']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: % Paid Orders.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['#paid_orders']['plain'],
		$kpis['#orders']['plain']
	);

	// Set the value.
	$value *= 100;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['%paid_orders']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	//
	// KPI: $ Gross Earnings.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_gross_earnings( $args );

	// Set the kpi plain.
	$kpis['$gross_earnings']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$gross_earnings']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: # Taxes.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_taxes_count( $args );

	// Set the kpi plain.
	$kpis['#taxes']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#taxes']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: $ Taxes.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_taxes_amount( $args );

	// Set the kpi plain.
	$kpis['$taxes']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$taxes']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: # Discounts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_discounts_count( $args );

	// Set the kpi plain.
	$kpis['#discounts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['#discounts']['formatted'] = psupsellmaster_format_integer_amount( $value );

	//
	// KPI: $ Discounts.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_discounts_amount( $args );

	// Set the kpi plain.
	$kpis['$discounts']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$discounts']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: $ Net Earnings.
	//

	// Set the value.
	$value = psupsellmaster_get_campaign_order_net_earnings( $args );

	// Set the kpi plain.
	$kpis['$net_earnings']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['$net_earnings']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: $ AOV.
	//

	// Set the value.
	$value = psupsellmaster_safe_divide(
		$kpis['$net_earnings']['plain'],
		$kpis['#paid_orders']['plain']
	);

	// Set the kpi plain.
	$kpis['$aov']['plain'] = $value;

	// Set the value.
	$value = psupsellmaster_format_decimal_amount( $value );

	// Set the kpi formatted.
	$kpis['$aov']['formatted'] = psupsellmaster_format_currency_amount( $value );

	//
	// KPI: % Cart Abandonment.
	//

	// Set the value.
	$value = $kpis['#carts']['plain'] > 0 ? 100 - $kpis['%orders']['plain'] : 0;

	// Set the kpi plain.
	$kpis['%cart_abandoment']['plain'] = $value;

	// Set the kpi formatted.
	$kpis['%cart_abandoment']['formatted'] = psupsellmaster_format_percentage_amount( $value );

	// Return the kpis.
	return $kpis;
}

/**
 * Get the next dates based on the arguments.
 *
 * @param array $args The arguments.
 * @return array The dates.
 */
function psupsellmaster_campaigns_get_next_dates( $args ) {
	// Set the dates.
	$dates = array(
		'start_date' => null,
		'end_date'   => null,
	);

	// Get the start date.
	$start_date = isset( $args['start_date'] ) ? $args['start_date'] : null;

	// Check the start date.
	if ( ! empty( $start_date ) ) {
		// Set the start date.
		$start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $start_date, new DateTimeZone( 'UTC' ) );

		// Check if the start date is valid.
		if ( $start_date instanceof DateTime ) {
			// Set the start date.
			$start_date = $start_date->format( 'Y-m-d H:i:s' );
		}
	}

	// Check if the start date is empty.
	if ( empty( $start_date ) ) {
		// Set the start date.
		$start_date = null;
	}

	// Get the end date.
	$end_date = isset( $args['end_date'] ) ? $args['end_date'] : null;

	// Check the end date.
	if ( ! empty( $end_date ) ) {
		// Set the end date.
		$end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $end_date, new DateTimeZone( 'UTC' ) );

		// Check if the end date is valid.
		if ( $end_date instanceof DateTime ) {
			// Set the end date.
			$end_date = $end_date->format( 'Y-m-d H:i:s' );
		}
	}

	// Check if the end date is empty.
	if ( empty( $end_date ) ) {
		// Set the end date.
		$end_date = null;
	}

	// Check if the start date and end date are empty.
	if ( empty( $start_date ) && empty( $end_date ) ) {
		// Return the dates.
		return $dates;
	}

	// Set the start timestamp.
	$start_timestamp = strtotime( $start_date );

	// Set the end timestamp.
	$end_timestamp = strtotime( $end_date );

	// Set the is year first day.
	$is_year_first_day = '01' === gmdate( 'm', $start_timestamp ) && '01' === gmdate( 'd', $start_timestamp );

	// Set the is year last day.
	$is_year_last_day = '12' === gmdate( 'm', $end_timestamp ) && '31' === gmdate( 'd', $end_timestamp );

	// Check if the dates refer to the start and end of a year.
	if ( $is_year_first_day && $is_year_last_day ) {
		// Set the start date.
		$dates['start_date'] = gmdate( 'Y-m-d', strtotime( 'first day of next year', $start_timestamp ) );

		// Set the end date.
		$dates['end_date'] = gmdate( 'Y-m-d', strtotime( 'last day of next year', $end_timestamp ) );

		// Otherwise...
	} else {
		// Check if the start date is not empty.
		if ( ! empty( $start_date ) ) {
			// Check if the start date is the first day of the month.
			if ( '01' === gmdate( 'd', $start_timestamp ) ) {
				// Set the start date.
				$dates['start_date'] = gmdate( 'Y-m-d', strtotime( 'first day of next month', $start_timestamp ) );
			} else {
				// Set the start date.
				$dates['start_date'] = gmdate( 'Y-m-d', strtotime( '+1 month', $start_timestamp ) );
			}
		}

		// Check if the end date is not empty.
		if ( ! empty( $end_date ) ) {
			// Check if the end date is the last day of the month.
			if ( gmdate( 't', $end_timestamp ) === gmdate( 'd', $end_timestamp ) ) {
				// Set the end date.
				$dates['end_date'] = gmdate( 'Y-m-d', strtotime( 'last day of next month', $end_timestamp ) );
			} else {
				// Set the end date.
				$dates['end_date'] = gmdate( 'Y-m-d', strtotime( '+1 month', $end_timestamp ) );
			}
		}
	}

	// Return the dates.
	return $dates;
}

/**
 * Maybe redirect the integrated coupon edit page to the campaign edit page.
 */
function psupsellmaster_maybe_redirect_integrated_coupon() {
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Maybe redirect the coupon edit page.
		psupsellmaster_woo_maybe_redirect_integrated_coupon();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Maybe redirect the coupon edit page.
		psupsellmaster_edd_maybe_redirect_integrated_coupon();
	}
}
add_action( 'admin_init', 'psupsellmaster_maybe_redirect_integrated_coupon', 6 );

/**
 * Get the core campaign templates.
 *
 * @return array The campaign templates.
 */
function psupsellmaster_campaigns_get_core_templates() {
	// Get the current datetime.
	$current_datetime = new DateTime( 'now', psupsellmaster_get_timezone() );

	// Get the current year.
	$current_year = $current_datetime->format( 'Y' );

	// Get the next year.
	$next_year = $current_datetime->modify( '+1 year' )->format( 'Y' );

	// Set the templates.
	$templates = array();

	// Set the template title.
	$template_title = __( 'Black Friday', 'psupsellmaster' );

	// Get the event date.
	$event_date = psupsellmaster_get_event_date( array( 'event' => 'black_friday' ) );

	// Set the coupon code.
	$coupon_code = "BF{$current_year}";

	// Set the banner data.
	$banner_data = array(
		'description'       => '<p style="text-align: center"><span style="font-weight: 400">🖤 ' . __( 'Black Friday Alert!', 'psupsellmaster' ) . ' 🎁</span></p>'
							. '<p style="text-align: center"><span style="font-weight: 400">' . __( 'Limited-time offers, huge discounts, and exclusive deals just a click away.', 'psupsellmaster' ) . '</span></p>'
							. '<p style="text-align: center"><span style="font-weight: 400">' . __( "Don't miss out - shop now!", 'psupsellmaster' ) . ' 🛍️💥</span></p>',
		'desktop_banner_id' => psupsellmaster_campaigns_get_template_attachment_id( 'black_friday', 'desktop' ),
		'mobile_banner_id'  => psupsellmaster_campaigns_get_template_attachment_id( 'black_friday', 'mobile' ),
	);

	// Set the template data.
	$templates['black_friday'] = array(
		'template' => array(
			'title'     => $template_title,
			'thumbnail' => psupsellmaster_campaigns_get_core_template_image_path( 'black_friday', 'thumbnail' ),
		),
		'campaign' => array(
			'title'           => sprintf( '%s: %s', __( 'Created from Template', 'psupsellmaster' ), $template_title ),
			'start_date'      => $event_date,
			'end_date'        => $event_date,
			'display_options' => array( 'all' => $banner_data ),
			'product_page'    => $banner_data,
		),
		'coupons'  => array(
			array(
				'code'   => $coupon_code,
				'amount' => 10,
			),
		),
	);

	// Set the template title.
	$template_title = __( 'Cyber Monday', 'psupsellmaster' );

	// Get the event date.
	$event_date = psupsellmaster_get_event_date( array( 'event' => 'cyber_monday' ) );

	// Set the coupon code.
	$coupon_code = "CYBERMON{$next_year}";

	// Set the banner data.
	$banner_data = array(
		'description'       => '<p style="text-align: center;"><span style="font-weight: 400;">🌟 ' . __( 'Cyber Monday Alert!', 'psupsellmaster' ) . ' 💻🛒</span></p>'
							. '<p style="text-align: center;"><span style="font-weight: 400;">' . __( 'Score incredible discounts on gadgets, electronics, and more. Embrace the digital revolution and shop smart!', 'psupsellmaster' ) . ' 🚀💡</span></p>',
		'desktop_banner_id' => psupsellmaster_campaigns_get_template_attachment_id( 'cyber_monday', 'desktop' ),
		'mobile_banner_id'  => psupsellmaster_campaigns_get_template_attachment_id( 'cyber_monday', 'mobile' ),
	);

	// Set the template data.
	$templates['cyber_monday'] = array(
		'template' => array(
			'title'     => $template_title,
			'thumbnail' => psupsellmaster_campaigns_get_core_template_image_path( 'cyber_monday', 'thumbnail' ),
		),
		'campaign' => array(
			'title'           => sprintf( '%s: %s', __( 'Created from Template', 'psupsellmaster' ), $template_title ),
			'start_date'      => $event_date,
			'end_date'        => $event_date,
			'display_options' => array( 'all' => $banner_data ),
			'product_page'    => $banner_data,
		),
		'coupons'  => array(
			array(
				'code'   => $coupon_code,
				'amount' => 10,
			),
		),
	);

	// Set the template title.
	$template_title = __( 'Cyber Week', 'psupsellmaster' );

	// Get the event date.
	$event_date = psupsellmaster_get_event_date( array( 'event' => 'cyber_week' ) );

	// Set the coupon code.
	$coupon_code = "CYBERWEEK{$next_year}";

	// Set the banner data.
	$banner_data = array(
		'description'       => '<p style="text-align: center;"><span style="font-weight: 400;">🌐 ' . __( 'Cyber Week Spectacular!', 'psupsellmaster' ) . ' 💻🚀</span></p>'
							. '<p style="text-align: center;"><span style="font-weight: 400;">' . __( 'Unlock the future of savings with electrifying deals on all things tech and more.', 'psupsellmaster' ) . '</span></p>'
							. '<p style="text-align: center;"><span style="font-weight: 400;">' . __( "Don't miss out on this digital shopping extravaganza!", 'psupsellmaster' ) . ' 🛒🔌</span></p>',
		'desktop_banner_id' => psupsellmaster_campaigns_get_template_attachment_id( 'cyber_week', 'desktop' ),
		'mobile_banner_id'  => psupsellmaster_campaigns_get_template_attachment_id( 'cyber_week', 'mobile' ),
	);

	// Set the template data.
	$templates['cyber_week'] = array(
		'template' => array(
			'title'     => $template_title,
			'thumbnail' => psupsellmaster_campaigns_get_core_template_image_path( 'cyber_week', 'thumbnail' ),
		),
		'campaign' => array(
			'title'           => sprintf( '%s: %s', __( 'Created from Template', 'psupsellmaster' ), $template_title ),
			'start_date'      => $event_date['start'],
			'end_date'        => $event_date['end'],
			'display_options' => array( 'all' => $banner_data ),
			'product_page'    => $banner_data,
		),
		'coupons'  => array(
			array(
				'code'   => $coupon_code,
				'amount' => 10,
			),
		),
	);

	// Set the template title.
	$template_title = __( 'Christmas Day', 'psupsellmaster' );

	// Get the event date.
	$event_date = psupsellmaster_get_event_date( array( 'event' => 'christmas_day' ) );

	// Set the coupon code.
	$coupon_code = "XMASDAY{$current_year}";

	// Set the banner data.
	$banner_data = array(
		'description'       => '<p style="text-align: center;"><span style="font-weight: 400;">🎄 ' . __( 'Merry Christmas! Unwrap the Joy!', 'psupsellmaster' ) . ' 🎁</span></p>'
							. '<p style="text-align: center;"><span style="font-weight: 400;">' . __( 'Celebrate this magical day with extraordinary discounts on the perfect gifts. Make your Christmas merry and bright with our festive offers. Shop now and spread the joy!', 'psupsellmaster' ) . ' 🛍️🎅</span></p>',
		'desktop_banner_id' => psupsellmaster_campaigns_get_template_attachment_id( 'christmas_day', 'desktop' ),
		'mobile_banner_id'  => psupsellmaster_campaigns_get_template_attachment_id( 'christmas_day', 'mobile' ),
	);

	// Set the template data.
	$templates['christmas_day'] = array(
		'template' => array(
			'title'     => $template_title,
			'thumbnail' => psupsellmaster_campaigns_get_core_template_image_path( 'christmas_day', 'thumbnail' ),
		),
		'campaign' => array(
			'title'           => sprintf( '%s: %s', __( 'Created from Template', 'psupsellmaster' ), $template_title ),
			'start_date'      => $event_date,
			'end_date'        => $event_date,
			'display_options' => array( 'all' => $banner_data ),
			'product_page'    => $banner_data,
		),
		'coupons'  => array(
			array(
				'code'   => $coupon_code,
				'amount' => 10,
			),
		),
	);

	// Set the template title.
	$template_title = __( "New Year's Day", 'psupsellmaster' );

	// Get the event date.
	$event_date = psupsellmaster_get_event_date( array( 'event' => 'new_years_day' ) );

	// Set the coupon code.
	$coupon_code = "NEWYEAR{$next_year}";

	// Set the banner data.
	$banner_data = array(
		'description'       => '<p style="text-align: center;"><span style="font-weight: 400;">🎉 ' . __( 'New Year, New Savings!', 'psupsellmaster' ) . ' 🌟</span></p>'
							. '<p style="text-align: center;"><span style="font-weight: 400;">' . __( 'Step into the new year in style with amazing discounts on our latest collection. Start the year off right with a shopping spree!', 'psupsellmaster' ) . ' 🛍️🎆</span></p>',
		'desktop_banner_id' => psupsellmaster_campaigns_get_template_attachment_id( 'new_years_day', 'desktop' ),
		'mobile_banner_id'  => psupsellmaster_campaigns_get_template_attachment_id( 'new_years_day', 'mobile' ),
	);

	// Set the template data.
	$templates['new_years_day'] = array(
		'template' => array(
			'title'     => $template_title,
			'thumbnail' => psupsellmaster_campaigns_get_core_template_image_path( 'new_years_day', 'thumbnail' ),
		),
		'campaign' => array(
			'title'           => sprintf( '%s: %s', __( 'Created from Template', 'psupsellmaster' ), $template_title ),
			'start_date'      => $event_date,
			'end_date'        => $event_date,
			'display_options' => array( 'all' => $banner_data ),
			'product_page'    => $banner_data,
		),
		'coupons'  => array(
			array(
				'code'   => $coupon_code,
				'amount' => 10,
			),
		),
	);

	// Set the templates.
	$templates = array( 'core-lite' => $templates );

	// Allow developers to filter this.
	$templates = apply_filters( 'psupsellmaster_campaigns_get_core_templates', $templates );

	// Return the templates.
	return $templates;
}

/**
 * Get the stored campaign templates.
 *
 * @return array The campaign templates.
 */
function psupsellmaster_campaigns_get_stored_templates() {
	// Set the templates.
	$templates = array( 'stored' => array() );

	// Get the campaign templates.
	$campaign_templates = psupsellmaster_db_campaign_templates_select();

	// Loop through the campaign templates.
	foreach ( $campaign_templates as $campaign_template ) {
		// Get the template id.
		$template_id = isset( $campaign_template->id ) ? filter_var( $campaign_template->id, FILTER_VALIDATE_INT ) : false;

		// Check if the template id is empty.
		if ( empty( $template_id ) ) {
			continue;
		}

		// Set the template.
		$template = array(
			'title'     => $campaign_template->title,
			'thumbnail' => '',
		);

		// Set thet data.
		$data = array(
			'template' => $template,
			'campaign' => array( 'title' => "Created from template: {$campaign_template->title}" ),
			'coupons'  => array( array() ),
		);

		// Get the campaign title.
		$campaign_title = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_title', true );

		// Check if the campaign title is not empty.
		if ( ! empty( $campaign_title ) ) {
			// Set thet data.
			$data['campaign']['title'] = $campaign_title;
		}

		// Get the campaign status.
		$campaign_status = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_status', true );

		// Check if the campaign status is not empty.
		if ( ! empty( $campaign_status ) ) {
			// Set thet data.
			$data['campaign']['status'] = $campaign_status;
		}

		// Get the campaign priority.
		$campaign_priority = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_priority', true );

		// Check if the campaign priority is not empty.
		if ( ! empty( $campaign_priority ) ) {
			// Set thet data.
			$data['campaign']['priority'] = $campaign_priority;
		}

		// Get the campaign start date.
		$campaign_start_date = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_start_date', true );

		// Check if the campaign start date is not empty.
		if ( ! empty( $campaign_start_date ) ) {
			// Set thet data.
			$data['campaign']['start_date'] = $campaign_start_date;
		}

		// Get the campaign end date.
		$campaign_end_date = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_end_date', true );

		// Check if the campaign end date is not empty.
		if ( ! empty( $campaign_end_date ) ) {
			// Set thet data.
			$data['campaign']['end_date'] = $campaign_end_date;
		}

		// Get the campaign locations flag.
		$campaign_locations_flag = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_locations_flag', true );

		// Check if the campaign locations flag is not empty.
		if ( ! empty( $campaign_locations_flag ) ) {
			// Set thet data.
			$data['campaign']['locations_flag'] = $campaign_locations_flag;
		}

		// Get the campaign products flag.
		$campaign_products_flag = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_products_flag', true );

		// Check if the campaign products flag is not empty.
		if ( ! empty( $campaign_products_flag ) ) {
			// Set thet data.
			$data['campaign']['products_flag'] = $campaign_products_flag;
		}

		// Get the campaign products type.
		$campaign_products_type = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_products_type', true );

		// Check if the campaign products type is not empty.
		if ( ! empty( $campaign_products_type ) ) {
			// Set thet data.
			$data['campaign']['products_type'] = $campaign_products_type;
		}

		// Get the campaign locations.
		$campaign_locations = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_locations', true );

		// Check if the campaign locations is not empty.
		if ( ! empty( $campaign_locations ) ) {
			// Set thet data.
			$data['campaign']['locations'] = $campaign_locations;
		}

		// Get the campaign weekdays.
		$campaign_weekdays = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_weekdays', true );

		// Check if the campaign weekdays is not empty.
		if ( ! empty( $campaign_weekdays ) ) {
			// Set thet data.
			$data['campaign']['weekdays'] = $campaign_weekdays;
		}

		// Get the campaign display options.
		$campaign_display_options = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_display_options', true );

		// Check if the campaign display options is not empty.
		if ( ! empty( $campaign_display_options ) ) {
			// Set thet data.
			$data['campaign']['display_options'] = $campaign_display_options;
		}

		// Get the campaign coupons.
		$campaign_coupons = psupsellmaster_db_campaign_template_meta_select( $template_id, 'campaign_coupons', true );

		// Check if the campaign coupons is not empty.
		if ( ! empty( $campaign_coupons ) ) {
			// Set thet data.
			$data['coupons'] = $campaign_coupons;
		}

		// Set the templates.
		$templates['stored'][ $campaign_template->id ] = $data;
	}

	// Allow developers to filter this.
	$templates = apply_filters( 'psupsellmaster_campaigns_get_stored_templates', $templates );

	// Return the templates.
	return $templates;
}

/**
 * Get the campaign templates.
 *
 * @return array The campaign templates.
 */
function psupsellmaster_campaigns_get_templates() {
	// Get the core templates.
	$core_templates = psupsellmaster_campaigns_get_core_templates();

	// Get the stored templates.
	$stored_templates = psupsellmaster_campaigns_get_stored_templates();

	// Set the templates.
	$templates = $stored_templates + $core_templates;

	// Allow developers to filter this.
	$templates = apply_filters( 'psupsellmaster_campaigns_get_templates', $templates );

	// Return the templates.
	return $templates;
}

/**
 * Get a campaign template.
 *
 * @param string $key The template key.
 * @return array The campaign template.
 */
function psupsellmaster_campaigns_get_template( $key ) {
	// Set the template.
	$template = array();

	// Get the templates.
	$templates = psupsellmaster_campaigns_get_templates();
	$templates = array_reduce( $templates, 'array_replace', array() );

	// Check if the template is set.
	if ( isset( $templates[ $key ] ) ) {
		// Set the template.
		$template = $templates[ $key ];
	}

	// Return the template.
	return $template;
}

/**
 * Create a new campaign from a template.
 *
 * @param string $template_key The template key.
 * @return int The campaign id.
 */
function psupsellmaster_create_campaign_from_template( $template_key ) {
	// Set the campaign id.
	$campaign_id = false;

	// Get the campaign template.
	$template = psupsellmaster_campaigns_get_template( $template_key );

	// Check if the template is empty.
	if ( empty( $template ) ) {
		// Return the campaign id.
		return $campaign_id;
	}

	// Get the campaign data.
	$campaign_data = isset( $template['campaign'] ) && is_array( $template['campaign'] ) ? $template['campaign'] : array();

	// Get the coupon data.
	$coupon_data = isset( $template['coupons'] ) && is_array( $template['coupons'] ) ? $template['coupons'] : array();
	$coupon_data = array_shift( $coupon_data );

	// Set the data.
	$data = array();

	// Set the template.
	$data['template'] = $template_key;

	// Set the origin.
	$data['origin'] = 'template';

	// Set the status.
	$data['status'] = 'inactive';

	// Check if the data is set.
	if ( isset( $campaign_data['title'] ) ) {
		// Set the data.
		$data['title'] = $campaign_data['title'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['priority'] ) ) {
		// Set the data.
		$data['priority'] = $campaign_data['priority'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['start_date'] ) ) {
		// Set the start date day.
		$start_date_day = isset( $campaign_data['start_date']['day'] ) ? filter_var( $campaign_data['start_date']['day'], FILTER_VALIDATE_INT ) : false;

		// Set the start date month.
		$start_date_month = isset( $campaign_data['start_date']['month'] ) ? filter_var( $campaign_data['start_date']['month'], FILTER_VALIDATE_INT ) : false;

		// Set the start date year.
		$start_date_year = isset( $campaign_data['start_date']['year'] ) ? filter_var( $campaign_data['start_date']['year'], FILTER_VALIDATE_INT ) : false;

		// Check if the day and month are not empty.
		if ( ! empty( $start_date_day ) && ! empty( $start_date_month ) ) {
			// Check if the start date year is empty.
			if ( empty( $start_date_year ) ) {
				// Set the start date year.
				$start_date_year = new DateTime( 'now', psupsellmaster_get_timezone() );
				$start_date_year = $start_date_year->format( 'Y' );
			}

			// Set the start date.
			$data['start_date'] = "{$start_date_year}-{$start_date_month}-{$start_date_day}";
		}
	}

	// Check if the data is set.
	if ( isset( $campaign_data['end_date'] ) ) {
		// Set the end date day.
		$end_date_day = isset( $campaign_data['end_date']['day'] ) ? filter_var( $campaign_data['end_date']['day'], FILTER_VALIDATE_INT ) : false;

		// Set the end date month.
		$end_date_month = isset( $campaign_data['end_date']['month'] ) ? filter_var( $campaign_data['end_date']['month'], FILTER_VALIDATE_INT ) : false;

		// Set the end date year.
		$end_date_year = isset( $campaign_data['end_date']['year'] ) ? filter_var( $campaign_data['end_date']['year'], FILTER_VALIDATE_INT ) : false;

		// Check if the day and month are not empty.
		if ( ! empty( $end_date_day ) && ! empty( $end_date_month ) ) {
			// Check if the end date year is empty.
			if ( empty( $end_date_year ) ) {
				// Set the end date year.
				$end_date_year = new DateTime( 'now', psupsellmaster_get_timezone() );
				$end_date_year = $end_date_year->format( 'Y' );
			}

			// Set the end date.
			$data['end_date'] = "{$end_date_year}-{$end_date_month}-{$end_date_day}";
		}
	}

	// Check if the data is set.
	if ( isset( $campaign_data['locations_flag'] ) ) {
		// Set the data.
		$data['locations_flag'] = $campaign_data['locations_flag'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['products_flag'] ) ) {
		// Set the data.
		$data['products_flag'] = $campaign_data['products_flag'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['products_type'] ) ) {
		// Set the data.
		$data['products_type'] = $campaign_data['products_type'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['prices'] ) ) {
		// Set the data.
		$data['prices'] = $campaign_data['prices'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['weekdays'] ) ) {
		// Set the data.
		$data['weekdays'] = $campaign_data['weekdays'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['locations'] ) ) {
		// Set the data.
		$data['locations'] = $campaign_data['locations'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['display_options'] ) ) {
		// Set the data.
		$data['display_options'] = $campaign_data['display_options'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['product_page'] ) ) {
		// Set the data.
		$data['product_page'] = $campaign_data['product_page'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['conditions'] ) ) {
		// Set the data.
		$data['conditions'] = $campaign_data['conditions'];
	}

	// Check if the data is set.
	if ( isset( $coupon_data['code'] ) ) {
		// Set the data.
		$data['coupon_code'] = $coupon_data['code'];
	}

	// Check if the data is set.
	if ( isset( $coupon_data['type'] ) ) {
		// Set the data.
		$data['coupon_type'] = $coupon_data['type'];
	}

	// Check if the data is set.
	if ( isset( $coupon_data['amount'] ) ) {
		// Set the data.
		$data['coupon_amount'] = $coupon_data['amount'];
	}

	// Save the campaign.
	$campaign_id = psupsellmaster_save_campaign( $data );

	// Return the campaign id.
	return $campaign_id;
}

/**
 * Render the campaigns popups.
 */
function psupsellmaster_campaigns_footer_render_popups() {
	?>
	<?php if ( psupsellmaster_admin_is_page( 'campaigns', 'list' ) ) : ?>
		<div class="psupsellmaster-modal psupsellmaster-fade" id="psupsellmaster-modal-new-campaign" style="display: none;">
			<div class="psupsellmaster-modal-dialog psupsellmaster-modal-lg">
				<div class="psupsellmaster-modal-content">
					<div class="psupsellmaster-modal-header">
						<strong class="psupsellmaster-modal-title"><?php esc_html_e( 'New Campaign', 'psupsellmaster' ); ?></strong>
						<div class="psupsellmaster-modal-btn-close-container">
							<button class="psupsellmaster-modal-btn-close psupsellmaster-trigger-close-modal" type="button">
								<svg fill="none" height="24" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
									<line x1="18" x2="6" y1="6" y2="18"></line>
									<line x1="6" x2="18" y1="6" y2="18"></line>
								</svg>
							</button>
						</div>
					</div>
					<div class="psupsellmaster-modal-body">
						<div class="psupsellmaster-loader-container psupsellmaster-modal-loader" style="display: none;">
							<div class="psupsellmaster-loader"></div>
						</div>
						<div class="psupsellmaster-modal-ajax-container"></div>
					</div>
					<div class="psupsellmaster-modal-footer">
						<button class="button psupsellmaster-trigger-close-modal" type="button"><?php esc_html_e( 'Close', 'psupsellmaster' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="psupsellmaster-modal-backdrop psupsellmaster-fade" style="display: none;"></div>
	<?php elseif ( psupsellmaster_admin_is_page( 'campaigns', 'edit' ) ) : ?>
		<div class="psupsellmaster-modal psupsellmaster-fade" id="psupsellmaster-modal-save-template" style="display: none;">
			<div class="psupsellmaster-modal-dialog">
				<form class="psupsellmaster-modal-content psupsellmaster-modal-form">
					<div class="psupsellmaster-modal-header">
						<strong class="psupsellmaster-modal-title"><?php esc_html_e( 'Save Template', 'psupsellmaster' ); ?></strong>
						<div class="psupsellmaster-modal-btn-close-container">
							<button class="psupsellmaster-modal-btn-close psupsellmaster-trigger-close-modal" type="button">
								<svg fill="none" height="24" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
									<line x1="18" x2="6" y1="6" y2="18"></line>
									<line x1="6" x2="18" y1="6" y2="18"></line>
								</svg>
							</button>
						</div>
					</div>
					<div class="psupsellmaster-modal-body">
						<div class="psupsellmaster-loader-container psupsellmaster-modal-loader" style="display: none;">
							<div class="psupsellmaster-loader"></div>
						</div>
						<div class="psupsellmaster-modal-fields">
							<input class="psupsellmaster-field-campaign-id" type="hidden" value="" />
							<div class="psupsellmaster-form-field psupsellmaster-form-field-title">
								<label><strong><?php esc_html_e( 'Title', 'psupsellmaster' ); ?></strong></label>
								<input class="psupsellmaster-field psupsellmaster-field-template-title" name="title" required="required" type="text" value="" />
							</div>
						</div>
						<div class="psupsellmaster-modal-ajax-container"></div>
					</div>
					<div class="psupsellmaster-modal-footer">
						<button class="button button-primary psupsellmaster-btn-save-as-template-confirm" type="submit"><?php esc_html_e( 'Save', 'psupsellmaster' ); ?></button>
						<button class="button psupsellmaster-trigger-close-modal" type="button"><?php esc_html_e( 'Cancel', 'psupsellmaster' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="psupsellmaster-modal-backdrop psupsellmaster-fade" style="display: none;"></div>
	<?php endif; ?>
	<?php
}
add_action( 'admin_footer', 'psupsellmaster_campaigns_footer_render_popups' );

/**
 * Save a campaign template.
 *
 * @param array $data The data.
 */
function psupsellmaster_save_campaign_template( $data ) {
	// Set the success.
	$success = false;

	// Set the save data.
	$save_data = array(
		'title'   => __( 'New Template', 'psupsellmaster' ),
		'user_id' => get_current_user_id(),
	);

	// Get the template data.
	$template_data = isset( $data['template'] ) ? $data['template'] : array();

	// Get the campaign data.
	$campaign_data = isset( $data['campaign'] ) ? $data['campaign'] : array();

	// Get the coupon data.
	$coupon_data = isset( $data['coupons'] ) ? $data['coupons'] : array();
	$coupon_data = array_shift( $coupon_data );

	// Check if the data is set.
	if ( isset( $template_data['user_id'] ) ) {
		// Set the data.
		$save_data['user_id'] = $template_data['user_id'];
	}

	// Check if the data is set.
	if ( isset( $template_data['title'] ) ) {
		// Set the data.
		$save_data['title'] = $template_data['title'];
	}

	// Insert the campaign template.
	psupsellmaster_db_campaign_templates_insert( $save_data );

	// Set the template id.
	$template_id = psupsellmaster_db_get_inserted_id();

	// Check if the template id is empty.
	if ( empty( $template_id ) ) {
		// Return the success.
		return $success;
	}

	// Set the save meta.
	$save_meta = array(
		'campaign_id'              => null,
		'campaign_title'           => null,
		'campaign_status'          => null,
		'campaign_priority'        => null,
		'campaign_start_date'      => null,
		'campaign_end_date'        => null,
		'campaign_locations_flag'  => null,
		'campaign_products_flag'   => null,
		'campaign_products_type'   => null,
		'campaign_weekdays'        => null,
		'campaign_locations'       => null,
		'campaign_display_options' => null,
		'campaign_coupons'         => null,
	);

	// Check if the data is set.
	if ( isset( $campaign_data['id'] ) ) {
		// Set the meta.
		$save_meta['campaign_id'] = $campaign_data['id'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['title'] ) ) {
		// Set the meta.
		$save_meta['campaign_title'] = $campaign_data['title'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['status'] ) ) {
		// Set the meta.
		$save_meta['campaign_status'] = $campaign_data['status'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['priority'] ) ) {
		// Set the meta.
		$save_meta['campaign_priority'] = $campaign_data['priority'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['start_date'] ) ) {
		// Set the meta.
		$save_meta['campaign_start_date'] = $campaign_data['start_date'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['end_date'] ) ) {
		// Set the meta.
		$save_meta['campaign_end_date'] = $campaign_data['end_date'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['locations_flag'] ) ) {
		// Set the meta.
		$save_meta['campaign_locations_flag'] = $campaign_data['locations_flag'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['products_flag'] ) ) {
		// Set the meta.
		$save_meta['campaign_products_flag'] = $campaign_data['products_flag'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['products_type'] ) ) {
		// Set the meta.
		$save_meta['campaign_products_type'] = $campaign_data['products_type'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['weekdays'] ) ) {
		// Set the meta.
		$save_meta['campaign_weekdays'] = $campaign_data['weekdays'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['locations'] ) ) {
		// Set the meta.
		$save_meta['campaign_locations'] = $campaign_data['locations'];
	}

	// Check if the data is set.
	if ( isset( $campaign_data['display_options'] ) ) {
		// Set the meta.
		$save_meta['campaign_display_options'] = $campaign_data['display_options'];
	}

	// Check if the data is set.
	if ( ! empty( $coupon_data ) ) {
		// Set the meta.
		$save_meta['campaign_coupons'] = array( array() );
	}

	// Check if the data is set.
	if ( isset( $coupon_data['code'] ) ) {
		// Set the meta.
		$save_meta['campaign_coupons'][0]['code'] = $coupon_data['code'];
	}

	// Check if the data is set.
	if ( isset( $coupon_data['type'] ) ) {
		// Set the meta.
		$save_meta['campaign_coupons'][0]['type'] = $coupon_data['type'];
	}

	// Check if the data is set.
	if ( isset( $coupon_data['amount'] ) ) {
		// Set the meta.
		$save_meta['campaign_coupons'][0]['amount'] = $coupon_data['amount'];
	}

	// Loop through the save meta.
	foreach ( $save_meta as $key => $value ) {
		// Check if the value is false.
		if ( is_null( $value ) ) {
			// Delete the meta.
			psupsellmaster_db_campaign_template_meta_delete( $template_id, $key );
		} else {
			// Update the meta.
			psupsellmaster_db_campaign_template_meta_update( $template_id, $key, $value );
		}
	}

	// Set the success.
	$success = true;

	// Return the success.
	return $success;
}

/**
 * Save campaign data as a template.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_save_campaign_as_template( $args ) {
	// Set the success.
	$success = false;

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the success.
		return $success;
	}

	// Get the campaign.
	$campaign = psupsellmaster_get_campaign( $campaign_id );

	// Check if the campaign is empty.
	if ( empty( $campaign ) ) {
		// Return the success.
		return $success;
	}

	// Set the data.
	$data = array(
		'template' => array(),
		'campaign' => array(),
		'coupons'  => array(),
	);

	// Check if the template title is not empty.
	if ( ! empty( $args['template_title'] ) ) {
		// Set the data.
		$data['template']['title'] = $args['template_title'];
	}

	// Set the data.
	$data['campaign']['id'] = $campaign_id;

	// Check if the campaign priority is not empty.
	if ( ! empty( $campaign['priority'] ) ) {
		// Set the data.
		$data['campaign']['priority'] = $campaign['priority'];
	}

	// Check if the campaign start date is not empty.
	if ( ! empty( $campaign['start_date'] ) ) {
		// Get the start date day.
		$start_date_day = gmdate( 'j', strtotime( $campaign['start_date'] ) );

		// Get the start date month.
		$start_date_month = gmdate( 'n', strtotime( $campaign['start_date'] ) );

		// Set the data.
		$data['campaign']['start_date'] = array(
			'day'   => $start_date_day,
			'month' => $start_date_month,
		);
	}

	// Check if the campaign end date is not empty.
	if ( ! empty( $campaign['end_date'] ) ) {
		// Get the end date day.
		$end_date_day = gmdate( 'j', strtotime( $campaign['end_date'] ) );

		// Get the end date month.
		$end_date_month = gmdate( 'n', strtotime( $campaign['end_date'] ) );

		// Set the data.
		$data['campaign']['end_date'] = array(
			'day'   => $end_date_day,
			'month' => $end_date_month,
		);
	}

	// Get the locations flag.
	$locations_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'locations_flag', true );

	// Check if the locations flag is not empty.
	if ( ! empty( $locations_flag ) ) {
		// Set the data.
		$data['campaign']['locations_flag'] = $locations_flag;
	}

	// Get the products flag.
	$products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );

	// Check if the products flag is not empty.
	if ( ! empty( $products_flag ) ) {
		// Set the data.
		$data['campaign']['products_flag'] = $products_flag;
	}

	// Get the products type.
	$products_type = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_type', true );

	// Check if the products type is not empty.
	if ( ! empty( $products_type ) ) {
		// Set the data.
		$data['campaign']['products_type'] = $products_type;
	}

	// Get the weekdays.
	$weekdays = psupsellmaster_get_campaign_weekdays( $campaign_id );

	// Check if the weekdays is not empty.
	if ( ! empty( $weekdays ) ) {
		// Set the data.
		$data['campaign']['weekdays'] = $weekdays;
	}

	// Get the locations.
	$locations = psupsellmaster_get_campaign_locations( $campaign_id );

	// Check if the locations is not empty.
	if ( ! empty( $locations ) ) {
		// Set the data.
		$data['campaign']['locations'] = $locations;
	}

	// Set the display options.
	$display_options = array();

	// Set the where.
	$where = array( 'campaign_id' => $campaign_id );

	// Get the stored display options.
	$stored_display_options = psupsellmaster_db_campaign_display_options_select( $where );

	// Loop through the stored display options.
	foreach ( $stored_display_options as $display_option ) {
		// Get the location.
		$location = isset( $display_option->location ) ? $display_option->location : '';

		// Check if the location is empty.
		if ( empty( $location ) ) {
			continue;
		}

		// Get the option name.
		$option_name = isset( $display_option->option_name ) ? $display_option->option_name : '';

		// Check if the option name is empty.
		if ( empty( $option_name ) ) {
			continue;
		}

		// Get the option value.
		$option_value = isset( $display_option->option_value ) ? $display_option->option_value : '';

		// Check if the option value is empty.
		if ( empty( $option_value ) ) {
			continue;
		}

		// Check if the location is not set.
		if ( ! isset( $display_options[ $location ] ) ) {
			// Set the location.
			$display_options[ $location ] = array();
		}

		// Set the display options.
		$display_options[ $location ][ $option_name ] = $option_value;
	}

	// Check if the display options is not empty.
	if ( ! empty( $display_options ) ) {
		// Set the data.
		$data['campaign']['display_options'] = $display_options;
	}

	// Get the campaign coupons.
	$campaign_coupons = psupsellmaster_db_campaign_coupons_select( $where );

	// Loop through the campaign coupons.
	foreach ( $campaign_coupons as $campaign_coupon ) {
		// Get the coupon code.
		$coupon_code = isset( $campaign_coupon->code ) ? $campaign_coupon->code : '';

		// Get the coupon type.
		$coupon_type = isset( $campaign_coupon->type ) ? $campaign_coupon->type : '';

		// Get the coupon amount.
		$coupon_amount = isset( $campaign_coupon->amount ) ? filter_var( $campaign_coupon->amount, FILTER_VALIDATE_FLOAT ) : false;
		$coupon_amount = false !== $coupon_amount ? $coupon_amount : 0;

		// Set the coupon data.
		$coupon_data = array(
			'code'   => $coupon_code,
			'type'   => $coupon_type,
			'amount' => $coupon_amount,
		);

		// Add the coupon data.
		array_push( $data['coupons'], $coupon_data );
	}

	// Save the campaign template.
	$success = psupsellmaster_save_campaign_template( $data );

	// Return the success.
	return $success;
}

/**
 * Delete a campaign template.
 *
 * @param array $id The campaign template id.
 * @return int|false The number of rows deleted, or false on error
 */
function psupsellmaster_delete_campaign_template( $id ) {
	// Set the deleted.
	$deleted = false;

	// Set the where.
	$where = array( 'psupsellmaster_campaign_template_id' => $id );

	// Delete the table row.
	$deleted = ! empty( PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templatemeta' ), $where ) );

	// Set the where.
	$where = array( 'id' => $id );

	// Delete the table row.
	$deleted = ! empty( PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' ), $where ) );

	// Return the deleted.
	return $deleted;
}

/**
 * Get the core campaign template image paths.
 *
 * @return array The paths.
 */
function psupsellmaster_campaigns_get_core_template_image_paths() {
	// Set the paths.
	$paths = array(
		'black_friday'  => array(
			'desktop'   => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/black-friday-desktop.png',
			'mobile'    => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/black-friday-mobile.png',
			'thumbnail' => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/black-friday-thumbnail.png',
		),
		'christmas_day' => array(
			'desktop'   => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/christmas-day-desktop.png',
			'mobile'    => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/christmas-day-mobile.png',
			'thumbnail' => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/christmas-day-thumbnail.png',
		),
		'cyber_monday'  => array(
			'desktop'   => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-monday-desktop.png',
			'mobile'    => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-monday-mobile.png',
			'thumbnail' => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-monday-thumbnail.png',
		),
		'cyber_week'    => array(
			'desktop'   => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-week-desktop.png',
			'mobile'    => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-week-mobile.png',
			'thumbnail' => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/cyber-week-thumbnail.png',
		),
		'new_years_day' => array(
			'desktop'   => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/new-years-day-desktop.png',
			'mobile'    => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/new-years-day-mobile.png',
			'thumbnail' => PSUPSELLMASTER_URL . 'assets/images/campaigns/templates/new-years-day-thumbnail.png',
		),
	);

	// Allow developers to filter this.
	$paths = apply_filters( 'psupsellmaster_campaigns_get_core_template_image_paths', $paths );

	// Return the paths.
	return $paths;
}

/**
 * Get a core campaign template image path.
 *
 * @param string $template_key The template key.
 * @param string $image_type The image type.
 * @return string The image path.
 */
function psupsellmaster_campaigns_get_core_template_image_path( $template_key, $image_type ) {
	// Set the path.
	$path = false;

	// Get the paths.
	$paths = psupsellmaster_campaigns_get_core_template_image_paths();

	// Check if the paths is set.
	if ( ! isset( $paths[ $template_key ] ) ) {
		// Return the path.
		return $path;
	}

	// Check if the image type is set.
	if ( ! isset( $paths[ $template_key ][ $image_type ] ) ) {
		// Return the path.
		return $path;
	}

	// Set the path.
	$path = $paths[ $template_key ][ $image_type ];

	// Return the path.
	return $path;
}

/**
 * Save the campaign template attachments.
 *
 * @param string $template_key The template key.
 */
function psupsellmaster_campaigns_save_template_attachments( $template_key ) {
	// Get the stored.
	$stored_attachments = psupsellmaster_get_uploaded_attachments();

	// Set the campaign attachments.
	$campaign_attachments = array();

	// Check if the campaign attachments is set.
	if ( ! empty( $stored_attachments['campaigns'] ) ) {
		// Set the campaign attachments.
		$campaign_attachments = $stored_attachments['campaigns'];
	}

	// Set the template attachments.
	$template_attachments = array();

	// Check if the template attachments is set.
	if ( ! empty( $campaign_attachments[ $template_key ] ) ) {
		// Set the template attachments.
		$template_attachments = $campaign_attachments[ $template_key ];
	}

	// Validate the desktop attachment.
	$template_attachments['desktop'] = isset( $template_attachments['desktop'] ) ? filter_var( $template_attachments['desktop'], FILTER_VALIDATE_INT ) : false;
	$template_attachments['desktop'] = ! empty( get_post_status( $template_attachments['desktop'] ) ) ? $template_attachments['desktop'] : false;

	// Validate the mobile attachment.
	$template_attachments['mobile'] = isset( $template_attachments['mobile'] ) ? filter_var( $template_attachments['mobile'], FILTER_VALIDATE_INT ) : false;
	$template_attachments['mobile'] = ! empty( get_post_status( $template_attachments['mobile'] ) ) ? $template_attachments['mobile'] : false;

	// Check if both attachments are valid.
	if ( ! empty( $template_attachments['desktop'] ) || ! empty( $template_attachments['mobile'] ) ) {
		return;
	}

	// Get the paths.
	$paths = psupsellmaster_campaigns_get_core_template_image_paths();
	$paths = ! empty( $paths[ $template_key ] ) ? $paths[ $template_key ] : false;

	// Get the desktop path.
	$desktop_path = ! empty( $paths['desktop'] ) ? $paths['desktop'] : false;

	// Check if the desktop path is not empty.
	if ( ! empty( $desktop_path ) ) {
		// Save the image as an attachment.
		$attachment_id = media_sideload_image( $desktop_path, 0, null, 'id' );
		$attachment_id = filter_var( $attachment_id, FILTER_VALIDATE_INT );

		// Check if the attachment id is empty.
		if ( ! empty( $attachment_id ) ) {
			// Set the template attachments.
			$template_attachments['desktop'] = $attachment_id;
		}
	}

	// Get the mobile path.
	$mobile_path = ! empty( $paths['mobile'] ) ? $paths['mobile'] : false;

	// Check if the mobile path is not empty.
	if ( ! empty( $mobile_path ) ) {
		// Save the image as an attachment.
		$attachment_id = media_sideload_image( $mobile_path, 0, null, 'id' );
		$attachment_id = filter_var( $attachment_id, FILTER_VALIDATE_INT );

		// Check if the attachment id is empty.
		if ( ! empty( $attachment_id ) ) {
			// Set the template attachments.
			$template_attachments['mobile'] = $attachment_id;
		}
	}

	// Set the campaign attachments.
	$campaign_attachments[ $template_key ] = array();

	// Check if the desktop attachment is not empty.
	if ( ! empty( $template_attachments['desktop'] ) ) {
		// Set the campaign attachments.
		$campaign_attachments[ $template_key ]['desktop'] = $template_attachments['desktop'];
	}

	// Check if the mobile attachment is not empty.
	if ( ! empty( $template_attachments['mobile'] ) ) {
		// Set the campaign attachments.
		$campaign_attachments[ $template_key ]['mobile'] = $template_attachments['mobile'];
	}

	// Check if the template attachments is empty.
	if ( empty( $campaign_attachments[ $template_key ] ) ) {
		// Unset the campaign attachments.
		unset( $campaign_attachments[ $template_key ] );
	}

	// Set the stored attachments.
	$stored_attachments['campaigns'] = $campaign_attachments;

	// Update the stored attachments.
	psupsellmaster_update_uploaded_attachments( $stored_attachments );
}

/**
 * Get the campaign template attachment id.
 *
 * @param string $template_key The template key.
 * @param string $size_key     The size key.
 * @return int|bool The attachment id or false if not found.
 */
function psupsellmaster_campaigns_get_template_attachment_id( $template_key, $size_key ) {
	// Set the attachment id.
	$attachment_id = false;

	// Get the stored.
	$stored_attachments = psupsellmaster_get_uploaded_attachments();

	// Get the campaign attachments.
	$campaign_attachments = ! empty( $stored_attachments['campaigns'] ) ? $stored_attachments['campaigns'] : array();

	// Get the template attachments.
	$template_attachments = ! empty( $campaign_attachments[ $template_key ] ) ? $campaign_attachments[ $template_key ] : array();

	// Set the attachment id.
	$attachment_id = ! empty( $template_attachments[ $size_key ] ) ? filter_var( $template_attachments[ $size_key ], FILTER_VALIDATE_INT ) : false;

	// Return the attachment id.
	return $attachment_id;
}

/**
 * Get the datetime left for a campaign based on its end date.
 *
 * @param int $campaign_id The campaign id.
 * @return array The datetime left.
 */
function psupsellmaster_get_campaign_datetime_left( $campaign_id ) {
	// Set the left.
	$left = array();

	// Get the data.
	$data = psupsellmaster_get_campaign( $campaign_id );

	// Get the status.
	$status = isset( $data['status'] ) ? $data['status'] : '';

	// Check if the status is not active.
	if ( 'active' !== $status ) {
		// Return the left.
		return $left;
	}

	// Check the end date.
	if ( empty( $data['end_date'] ) || '0000-00-00 00:00:00' === $data['end_date'] ) {
		// Return the left.
		return $left;
	}

	// Set the end date.
	$end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $data['end_date'] );

	// Set the timezone.
	$timezone = new DateTimeZone( 'UTC' );

	// Get the current date.
	$current_date = new DateTime( 'now', $timezone );

	// Check if the end date is not in the future.
	if ( $current_date >= $end_date ) {
		// Return the left.
		return $left;
	}

	// Get the left between the dates.
	$left = psupsellmaster_get_datetime_difference( $current_date, $end_date, $timezone );

	// Return the left.
	return $left;
}

/**
 * Get a formatted text of the datetime left for a campaign based on its end date.
 *
 * @param int $campaign_id The campaign id.
 * @return string The datetime left.
 */
function psupsellmaster_get_formatted_campaign_datetime_left( $campaign_id ) {
	// Set the formatted.
	$formatted = '';

	// Get the datetime left between the dates.
	$left = psupsellmaster_get_campaign_datetime_left( $campaign_id );

	// Check the left.
	if ( empty( $left ) ) {
		// Return the formatted.
		return $formatted;
	}

	// Set the seconds to zero.
	$left['seconds'] = 0;

	// Get the formatted left.
	$formatted = psupsellmaster_format_datetime_difference( $left );

	// Set the formatted.
	$formatted .= ' ' . __( 'left', 'psupsellmaster' );

	// Return the formatted.
	return $formatted;
}

/**
 * Render the planned campaigns list.
 */
function psupsellmaster_campaigns_render_planned_list() {
	// Set the campaigns.
	$campaigns = psupsellmaster_get_planned_campaigns();
	?>
	<?php if ( empty( $campaigns ) ) : ?>
		<p class="psupsellmaster-paragraph"><?php esc_html_e( 'No planned campaigns found.', 'psupsellmaster' ); ?></p>
	<?php else : ?>
		<ul class="psupsellmaster-list">
			<?php foreach ( $campaigns as $campaign ) : ?>
				<?php
				// Set the link url.
				$link_url = admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign->id );

				// Set the link text.
				$link_text = $campaign->title;

				// Get the campaign dates.
				$dates = psupsellmaster_campaigns_get_dates( $campaign->id );

				// Check the dates.
				if ( ! empty( $dates['timezone_date']['start_date'] ) || ! empty( $dates['timezone_date']['end_date'] ) ) {
					// Set the texts list.
					$texts_list = array();

					// Check if the dates match.
					if ( $dates['timezone_date']['start_date'] === $dates['timezone_date']['end_date'] ) {
						// Add the text to the list.
						array_push( $texts_list, $dates['timezone_date']['start_date'] );

						// Otherwise...
					} else {
						// Check the start date.
						if ( empty( $dates['timezone_date']['start_date'] ) ) {
							// Add the text to the list.
							array_push( $texts_list, __( 'N/A', 'psupsellmaster' ) );
						} else {
							// Add the text to the list.
							array_push( $texts_list, $dates['timezone_date']['start_date'] );
						}

						// Check the end date.
						if ( empty( $dates['timezone_date']['end_date'] ) ) {
							// Add the text to the list.
							array_push( $texts_list, __( 'N/A', 'psupsellmaster' ) );
						} else {
							// Add the text to the list.
							array_push( $texts_list, $dates['timezone_date']['end_date'] );
						}
					}

					// Set the dates text.
					$dates_text = implode( ' &ndash; ', $texts_list );

					// Set the link text.
					$link_text = sprintf( '%s (%s)', $link_text, $dates_text );
				}
				?>
				<li class="psupsellmaster-item">
					<a href="<?php echo esc_url( $link_url ); ?>" target="_blank"><?php echo esc_html( $link_text ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php
}

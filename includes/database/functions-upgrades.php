<?php
/**
 * Functions - Database - Upgrades.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run the database upgrades.
 */
function psupsellmaster_database_upgrades() {
	// Get the version.
	$psupsellmaster_version = get_option( 'psupsellmaster_version' );

	// Check if the plugin version is different than the database version.
	if ( false === version_compare( $psupsellmaster_version, PSUPSELLMASTER_VER, '==' ) ) {
		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.3.11', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_3_11();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.4.0', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_4_0();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.6.0', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_6_0();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.7.0', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_7_0();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.7.9', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_7_9();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.7.50', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_7_50();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.7.72', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_7_72();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '1.8.25', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_1_8_25();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '2.0.1', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_2_0_1();
		}

		// Check the version.
		if ( true === version_compare( $psupsellmaster_version, '2.0.20', '<' ) ) {
			// Upgrade the database.
			psupsellmaster_database_upgrade_version_2_0_20();
		}

		// Update the version.
		update_option( 'psupsellmaster_version', PSUPSELLMASTER_VER, false );

		// Get the installed at.
		$installed_at = get_option( 'psupsellmaster_installed_at', false );

		// Check if the installed at is empty.
		if ( empty( $installed_at ) ) {
			// Update the installed at.
			update_option( 'psupsellmaster_installed_at', time(), false );
		}
	}

	// Set the type.
	$type = psupsellmaster_is_pro() ? 'pro' : 'lite';

	// Get the stored type.
	$stored_type = get_option( 'psupsellmaster_type', false );

	// Check if the type has been changed.
	if ( $stored_type !== $type ) {
		// Check if the stored type is not empty.
		if ( ! empty( $stored_type ) ) {
			// Set the transient.
			set_transient( 'psupsellmaster_type_changed', "{$stored_type}_{$type}" );
		}

		// Update the type.
		update_option( 'psupsellmaster_type', $type, false );
	}
}
add_action( 'plugins_loaded', 'psupsellmaster_database_upgrades', 200 );

/**
 * Upgrade the database to version 1.3.11.
 */
function psupsellmaster_database_upgrade_version_1_3_11() {
	// Set the charset collate.
	$charset_collate = '';

	// Check if the database has the collation capability.
	if ( PsUpsellMaster_Database::has_cap( 'collation' ) ) {
		// Set the charset collate.
		$charset_collate = PsUpsellMaster_Database::get_charset_collate();
	}

	// Set the max index length.
	$max_index_length = 191;

	// Set the table name visitors.
	$table_name_visitors = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' );

	// Check if the table visitors does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_visitors ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_visitors} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL DEFAULT 0,
			`cookie` varchar(50) NULL DEFAULT '',
			`ip` varchar(100) NULL DEFAULT '',
			`visits` longtext NULL DEFAULT '',
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `user_id` (`user_id`),
			KEY `cookie` (`cookie`),
			KEY `ip` (`ip`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name visitormeta.
	$table_name_visitormeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitormeta' );

	// Check if the table productmeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_visitormeta ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_visitormeta} (
			`meta_id` bigint(20) unsigned NOT NULL auto_increment,
			`psupsellmaster_visitor_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext DEFAULT NULL,
			PRIMARY KEY (`meta_id`),
			KEY `psupsellmaster_visitor_id` (`psupsellmaster_visitor_id`),
			KEY `meta_key` (`meta_key`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name interests.
	$table_name_interests = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interests' );

	// Check if the table interests does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_interests ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_interests} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`visitor_id` bigint(20) NOT NULL DEFAULT 0,
			`order_id` bigint(20) NOT NULL DEFAULT 0,
			`product_id` bigint(20) NOT NULL DEFAULT 0,
			`variation_id` bigint(20) NOT NULL DEFAULT 0,
			`base_product_id` bigint(20) NOT NULL DEFAULT 0,
			`location` varchar(50) NULL DEFAULT '',
			`source` varchar(30) NULL DEFAULT '',
			`type` varchar(30) NULL DEFAULT '',
			`view` varchar(30) NULL DEFAULT '',
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `visitor_id` (`visitor_id`),
			KEY `order_id` (`order_id`),
			KEY `product_id` (`product_id`),
			KEY `variation_id` (`variation_id`),
			KEY `base_product_id` (`base_product_id`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name interestmeta.
	$table_name_interestmeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interestmeta' );

	// Check if the table productmeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_interestmeta ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_interestmeta} (
			`meta_id` bigint(20) unsigned NOT NULL auto_increment,
			`psupsellmaster_interest_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext DEFAULT NULL,
			PRIMARY KEY (`meta_id`),
			KEY `psupsellmaster_interest_id` (`psupsellmaster_interest_id`),
			KEY `meta_key` (`meta_key`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name results.
	$table_name_results = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );

	// Check if the table results does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_results ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_results} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`order_id` bigint(20) NOT NULL DEFAULT 0,
			`order_item_id` bigint(20) NOT NULL DEFAULT 0,
			`customer_id` bigint(20) NOT NULL DEFAULT 0,
			`product_id` bigint(20) NOT NULL DEFAULT 0,
			`variation_id` bigint(20) NOT NULL DEFAULT 0,
			`base_product_id` bigint(20) NOT NULL DEFAULT 0,
			`amount` float(10,2) NOT NULL DEFAULT 0.00,
			`location` varchar(50) NULL DEFAULT '',
			`source` varchar(30) NULL DEFAULT '',
			`type` varchar(30) NULL DEFAULT '',
			`view` varchar(30) NULL DEFAULT '',
			`store` varchar(10) NULL DEFAULT '',
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `order_id` (`order_id`),
			KEY `order_item_id` (`order_item_id`),
			KEY `customer_id` (`customer_id`),
			KEY `product_id` (`product_id`),
			KEY `variation_id` (`variation_id`),
			KEY `base_product_id` (`base_product_id`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name resultmeta.
	$table_name_resultmeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_resultmeta' );

	// Check if the table productmeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_resultmeta ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_resultmeta} (
			`meta_id` bigint(20) unsigned NOT NULL auto_increment,
			`psupsellmaster_result_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext DEFAULT NULL,
			PRIMARY KEY (`meta_id`),
			KEY `psupsellmaster_result_id` (`psupsellmaster_result_id`),
			KEY `meta_key` (`meta_key`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Migrate data from old to new database tablesf.
	psupsellmaster_database_upgrade_version_1_3_11_migrate();
}

/**
 * Migrate database data to version 1.3.11.
 */
function psupsellmaster_database_upgrade_version_1_3_11_migrate() {
	// Check if the old table results does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', PsUpsellMaster_Database::get_table_name( 'psupsell_results' ) ) ) ) ) {
		return false;
	}

	// Build the SQL query.
	$sql_select = 'SELECT *';
	$sql_from   = PsUpsellMaster_Database::prepare( 'FROM %i AS `r`', PsUpsellMaster_Database::get_table_name( 'psupsell_results' ) );
	$sql_query  = "{$sql_select} {$sql_from}";

	// Get the sql results.
	$sql_results = PsUpsellMaster_Database::get_results( $sql_query );

	// Check if the sql results is empty.
	if ( empty( $sql_results ) ) {
		return false;
	}

	// Loop through the results.
	foreach ( $sql_results as $result_item ) {
		// Get the result id.
		$result_id = isset( $result_item->id ) ? filter_var( $result_item->id, FILTER_VALIDATE_INT ) : false;
		$result_id = ! empty( $result_id ) ? $result_id : 0;

		// Check if the result id is empty.
		if ( empty( $result_id ) ) {
			continue;
		}

		// Build the SQL query.
		$sql_select  = "SELECT 'yes' AS `exists`";
		$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `rm`', PsUpsellMaster_Database::get_table_name( 'psupsell_resultmeta' ) );
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `rm`.`meta_key` = %s', 'old_result_id' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `rm`.`meta_value` = %d', $result_id );
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_select} {$sql_from} {$sql_where}";

		// Get the sql row.
		$sql_row = PsUpsellMaster_Database::get_row( $sql_query );

		// Check if the sql row exists.
		if ( isset( $sql_row->exists ) && 'yes' === $sql_row->exists ) {
			continue;
		}

		// Get the order id.
		$order_id = isset( $result_item->payment_id ) ? filter_var( $result_item->payment_id, FILTER_VALIDATE_INT ) : false;
		$order_id = ! empty( $order_id ) ? $order_id : 0;

		// Check if the order id is empty.
		if ( empty( $order_id ) ) {
			continue;
		}

		// Set the order.
		$order = false;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Get the order.
			$order = wc_get_order( $order_id );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Get the order.
			$order = edd_get_order( $order_id );
		}

		// Check if the order is empty.
		if ( empty( $order ) ) {
			continue;
		}

		// Get the customer id.
		$customer_id = isset( $result_item->customer_id ) ? filter_var( $result_item->customer_id, FILTER_VALIDATE_INT ) : false;
		$customer_id = ! empty( $customer_id ) ? $customer_id : 0;

		//
		// Try to get the customer id directly from the order.
		//

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the order customer id.
			$order_customer_id = 0;

			// Get the order customer id.
			$order_customer_id = filter_var( $order->get_customer_id(), FILTER_VALIDATE_INT );
			$order_customer_id = ! empty( $order_customer_id ) ? $order_customer_id : 0;

			// Check if the order customer id is not empty.
			if ( ! empty( $order_customer_id ) ) {
				// Set the customer id.
				$customer_id = $order_customer_id;
			}

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the order customer id.
			$order_customer_id = 0;

			// Get the order customer id.
			$order_customer_id = isset( $order->customer_id ) ? filter_var( $order->customer_id, FILTER_VALIDATE_INT ) : false;
			$order_customer_id = ! empty( $order_customer_id ) ? $order_customer_id : 0;

			// Check if the order customer id is not empty.
			if ( ! empty( $order_customer_id ) ) {
				// Set the customer id.
				$customer_id = $order_customer_id;
			}
		}

		// Get the product id.
		$product_id = isset( $result_item->upsell_id ) ? filter_var( $result_item->upsell_id, FILTER_VALIDATE_INT ) : false;
		$product_id = ! empty( $product_id ) ? $product_id : 0;

		// Get the variation id.
		$variation_id = isset( $result_item->variation_id ) ? filter_var( $result_item->variation_id, FILTER_VALIDATE_INT ) : false;
		$variation_id = ! empty( $variation_id ) ? $variation_id : 0;

		// Set the order item id.
		$order_item_id = 0;

		// Set the order products.
		$order_products = array();

		//
		// Try to get the order item id.
		//

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			// Set the order products variations.
			$order_products_variations = array();

			// Get the order items.
			$order_items = $order->get_items();

			// Loop through the order items.
			foreach ( $order_items as $item_id => $item ) {
				// Get the order item product id.
				$item_product_id = filter_var( $item->get_product_id(), FILTER_VALIDATE_INT );
				$item_product_id = ! empty( $item_product_id ) ? $item_product_id : 0;

				// Check if the product is empty.
				if ( empty( $item_product_id ) ) {
					continue;
				}

				// Get the order item variation id.
				$item_variation_id = filter_var( $item->get_variation_id(), FILTER_VALIDATE_INT );
				$item_variation_id = ! empty( $item_variation_id ) ? $item_variation_id : 0;

				// Add the item product id to the order products list.
				$order_products[ $item_id ] = $item_product_id;

				// Add the item product and variation ids to the order products variations list.
				$order_products_variations[ $item_id ] = "{$item_product_id}_{$item_variation_id}";
			}

			// Get the order items related to the product id.
			$order_items = array_keys( $order_products, $product_id, true );

			// Check if multiple order items were found.
			if ( count( $order_items ) > 1 ) {
				// Set the product variation.
				$product_variation = "{$product_id}_{$variation_id}";

				// Get the order items related to the product and variation ids.
				$order_items = array_keys( $order_products_variations, $product_variation, true );
			}

			// Check if a single order item was found.
			if ( 1 === count( $order_items ) ) {
				// Set the order item id.
				$order_item_id = filter_var( array_pop( $order_items ), FILTER_VALIDATE_INT );
				$order_item_id = ! empty( $order_item_id ) ? $order_item_id : 0;
			}

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Set the edd get order items args.
			$edd_get_order_items_args = array(
				'fields'   => 'product_id',
				'number'   => 0,
				'order_id' => $order_id,
			);

			// Get the order products.
			$order_products = edd_get_order_items( $edd_get_order_items_args );

			// Remove empty and duplicate order products.
			$order_products = array_unique( array_filter( $order_products ) );

			// Reindex the order products.
			$order_products = array_values( $order_products );

			// Set the edd get order items args.
			$edd_get_order_items_args = array(
				'fields'     => array( 'id' ),
				'number'     => 0,
				'order_id'   => $order_id,
				'product_id' => $product_id,
			);

			// Get the order items.
			$order_items = edd_get_order_items( $edd_get_order_items_args );

			// Check if multiple order items were found.
			if ( count( $order_items ) > 1 ) {
				// Set the arg price id.
				$edd_get_order_items_args['price_id'] = $variation_id;

				// Get the order items.
				$order_items = edd_get_order_items( $edd_get_order_items_args );
			}

			// Check if a single order item was found.
			if ( 1 === count( $order_items ) ) {
				// Get the order item.
				$order_item = array_pop( $order_items );

				// Set the order item id.
				$order_item_id = isset( $order_item->id ) ? filter_var( $order_item->id, FILTER_VALIDATE_INT ) : false;
				$order_item_id = ! empty( $order_item_id ) ? $order_item_id : 0;
			}
		}

		//
		// Try to get the base product id (a single base product instead of multiple base products we previously stored).
		//

		// Set the base products.
		$base_products = array();

		// Build the SQL query.
		$sql_select  = "SELECT GROUP_CONCAT( `bp`.`base_product_id` SEPARATOR ',' ) AS `base_products`";
		$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `bp`', PsUpsellMaster_Database::get_table_name( 'psupsell_base_products' ) );
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `bp`.`upsell_result_id` = %d', $result_id );
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_select} {$sql_from} {$sql_where}";

		// Get the sql row.
		$sql_row = PsUpsellMaster_Database::get_row( $sql_query );

		// Check if the sql row is empty.
		if ( ! empty( $sql_row ) ) {
			// Set the base products.
			$base_products = ! empty( $sql_row->base_products ) ? explode( ',', $sql_row->base_products ) : array();
		}

		// Set the base product id.
		$base_product_id = 0;

		// Check if the base products is not empty.
		if ( ! empty( $base_products ) ) {
			// Set the base products (discard the product id).
			$base_products = array_diff( $base_products, array( $product_id ) );

			// Check if the base products list still has multiple entries.
			if ( count( $base_products ) > 1 ) {
				// Set the validated base products.
				$validated_base_products = array();

				// Loop through the base products.
				foreach ( $base_products as $item_base_product_id ) {
					// Build the SQL query.
					$sql_select  = 'SELECT `p`.`upsell_ids`';
					$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `p`', PsUpsellMaster_Database::get_table_name( 'psupsell_products' ) );
					$sql_where   = array();
					$sql_where[] = 'WHERE 1 = 1';
					$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `p`.`product_id` = %d', $item_base_product_id );
					$sql_where   = implode( ' ', $sql_where );
					$sql_query   = "{$sql_select} {$sql_from} {$sql_where}";

					// Get the sql row.
					$sql_row = PsUpsellMaster_Database::get_row( $sql_query );

					// Get the upsells.
					$upsells = ! empty( $sql_row->upsell_ids ) ? maybe_unserialize( $sql_row->upsell_ids ) : false;
					$upsells = is_array( $upsells ) ? $upsells : array();

					// Check if the product id is within the current base product upsells.
					if ( in_array( $product_id, $upsells, true ) ) {
						// Add the item base product id to the validated base products list.
						array_push( $validated_base_products, $item_base_product_id );
					}
				}

				// Set the base products.
				$base_products = $validated_base_products;

				// Check if the base products list still has multiple entries.
				if ( count( $base_products ) > 1 ) {
					// Set the base products (discard all products from the same order).
					$base_products = array_diff( $base_products, $order_products );
				}
			}

			// Check if the base products list has a single entry.
			if ( 1 === count( $base_products ) ) {
				// Set the base product id.
				$base_product_id = filter_var( array_pop( $base_products ), FILTER_VALIDATE_INT );
				$base_product_id = ! empty( $base_product_id ) ? $base_product_id : 0;
			}
		}

		// Set the amount.
		$amount = isset( $result_item->sales_value ) ? filter_var( $result_item->sales_value, FILTER_VALIDATE_FLOAT ) : false;
		$amount = ! empty( $amount ) ? $amount : 0;

		// Set the location.
		$location = '';

		// Check if the upsell location is not empty.
		if ( ! empty( $result_item->upsell_location ) ) {
			// Set the valid locations.
			$valid_locations = array_keys( psupsellmaster_get_product_locations() );

			// Check if the upsell location is valid.
			if ( in_array( $result_item->upsell_location, $valid_locations, true ) ) {
				// Set the location.
				$location = $result_item->upsell_location;

				// Otherwise, check if the location is download.
			} elseif ( 'download' === $result_item->upsell_location ) {
				// Set the location.
				$location = 'product';
			}
		}

		// Set the source.
		$source = '';

		// Check if the upsell type is not empty.
		if ( ! empty( $result_item->upsell_type ) ) {

			// Check if the upsell type is upsells.
			if ( 'upsells' === $result_item->upsell_type ) {
				// Set the source.
				$source = 'upsells';

				// Otherwise, check if the upsell type is viewed.
			} elseif ( 'viewed' === $result_item->upsell_type ) {
				// Set the source.
				$source = 'visits';
			}
		}

		// Set the type.
		$type = '';

		// Set the view.
		$view = '';

		// Set the store.
		$store = '';

		// Check if the shop type is not empty.
		if ( ! empty( $result_item->shop_type ) ) {

			// Check if the shop type is edd.
			if ( 'edd' === $result_item->shop_type ) {
				// Set the store.
				$store = 'edd';

				// Otherwise, check if the shop type is woo.
			} elseif ( 'woocommerce' === $result_item->shop_type ) {
				// Set the store.
				$store = 'woo';
			}
		}

		// Set the created at.
		$created_at = '';

		// Check if the created is not empty.
		if ( ! empty( $result_item->created ) ) {
			// Set the created at.
			$created_at = $result_item->created;
		}

		// Set the insert data.
		$insert_data = array(
			'order_id'        => $order_id,
			'order_item_id'   => $order_item_id,
			'customer_id'     => $customer_id,
			'product_id'      => $product_id,
			'variation_id'    => $variation_id,
			'base_product_id' => $base_product_id,
			'amount'          => $amount,
			'location'        => $location,
			'source'          => $source,
			'type'            => $type,
			'view'            => $view,
			'store'           => $store,
			'created_at'      => $created_at,
		);

		// Insert a new result into the database.
		psupsellmaster_db_results_insert( $insert_data );

		// Check if the inserted id is not empty.
		if ( ! empty( PsUpsellMaster_Database::get_insert_id() ) ) {
			// Add a meta key related to the migration.
			psupsellmaster_db_result_meta_insert( PsUpsellMaster_Database::get_insert_id(), 'old_result_id', $result_id, true );
		}
	}
}

/**
 * Upgrade the database to version 1.4.0.
 */
function psupsellmaster_database_upgrade_version_1_4_0() {
	// Set the charset collate.
	$charset_collate = '';

	// Check if the database has the collation capability.
	if ( PsUpsellMaster_Database::has_cap( 'collation' ) ) {
		// Set the charset collate.
		$charset_collate = PsUpsellMaster_Database::get_charset_collate();
	}

	// Set the table name analytics upsells.
	$table_name_analytics_upsells = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' );

	// Check if the table analytics upsells does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_analytics_upsells ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_analytics_upsells} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`base_product_id` bigint(20) NOT NULL DEFAULT 0,
			`upsell_product_id` bigint(20) NOT NULL DEFAULT 0,
			`average_amount` float(10,2) NOT NULL DEFAULT 0.00,
			`total_amount` float(10,2) NOT NULL DEFAULT 0.00,
			`total_sales` int(11) NOT NULL DEFAULT 0,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `base_product_id` (`base_product_id`),
			KEY `upsell_product_id` (`upsell_product_id`),
			KEY `average_amount` (`average_amount`),
			KEY `total_amount` (`total_amount`),
			KEY `total_sales` (`total_sales`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name analytics orders.
	$table_name_analytics_orders = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' );

	// Check if the table analytics orders does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_analytics_orders ) ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_analytics_orders} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`order_product_id` bigint(20) NOT NULL DEFAULT 0,
			`related_product_id` bigint(20) NOT NULL DEFAULT 0,
			`average_amount` float(10,2) NOT NULL DEFAULT 0.00,
			`total_amount` float(10,2) NOT NULL DEFAULT 0.00,
			`total_sales` int(11) NOT NULL DEFAULT 0,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `order_product_id` (`order_product_id`),
			KEY `related_product_id` (`related_product_id`),
			KEY `average_amount` (`average_amount`),
			KEY `total_amount` (`total_amount`),
			KEY `total_sales` (`total_sales`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}
}

/**
 * Upgrade the database to version 1.6.0.
 */
function psupsellmaster_database_upgrade_version_1_6_0() {
	// Set the charset collate.
	$charset_collate = '';

	// Check if the database has the collation capability.
	if ( PsUpsellMaster_Database::has_cap( 'collation' ) ) {
		// Set the charset collate.
		$charset_collate = PsUpsellMaster_Database::get_charset_collate();
	}

	// Set the max index length.
	$max_index_length = 191;

	//
	// Remove database tables.
	//

	// Set the database tables.
	$database_tables = array(
		PsUpsellMaster_Database::get_table_name( 'psupsell_base_products' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_results' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_tracking' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_tracking_visits' ),
	);

	// Loop through the database tables.
	foreach ( $database_tables as $database_table ) {
		// Drop the database table.
		PsUpsellMaster_Database::query( PsUpsellMaster_Database::prepare( 'DROP TABLE IF EXISTS %i', $database_table ) );
	}

	//
	// Create new database tables.
	//

	// Set the table name scores.
	$table_name_scores = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' );

	// Check if the database table scores does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_scores ) ) ) ) {
		// Set the query.
		$query = (
			"
			CREATE TABLE IF NOT EXISTS `{$table_name_scores}` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`base_product_id` bigint(20) NOT NULL DEFAULT 0,
				`upsell_product_id` bigint(20) NOT NULL DEFAULT 0,
				`criteria` varchar(100) NOT NULL DEFAULT '',
				`score` decimal(50,25) NOT NULL DEFAULT 0,
				`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
				`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
				PRIMARY KEY  (`id`),
				KEY `base_product_id` (`base_product_id`),
				KEY `upsell_product_id` (`upsell_product_id`),
				KEY `criteria` (`criteria`),
				KEY `score` (`score`)
			) {$charset_collate}
			"
		);

		// Requires the upgrade.php file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Creates the database table.
		dbDelta( $query );
	}

	// Set the table name scoremeta.
	$table_name_scoremeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scoremeta' );

	// Check if the database table scoremeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_scoremeta ) ) ) ) {
		// Set the query.
		$query = (
			"
			CREATE TABLE IF NOT EXISTS `{$table_name_scoremeta}` (
				`meta_id` bigint(20) unsigned NOT NULL auto_increment,
				`psupsellmaster_score_id` bigint(20) unsigned NOT NULL default '0',
				`meta_key` varchar(255) DEFAULT NULL,
				`meta_value` longtext DEFAULT NULL,
				PRIMARY KEY  (`meta_id`),
				KEY `psupsellmaster_score_id` (`psupsellmaster_score_id`),
				KEY `meta_key` (`meta_key`({$max_index_length}))
			) {$charset_collate}
			"
		);

		// Requires the upgrade.php file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Creates the database table.
		dbDelta( $query );
	}

	//
	// Update the database.
	//

	// Set the table name products.
	$table_name_products = PsUpsellMaster_Database::get_table_name( 'psupsell_products' );

	// Check if the database table products does exist.
	if ( ! empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name_products ) ) ) ) {
		// Set the sql query.
		$sql_query = (
			"
			SELECT
				`psupsell_products`.`enable`,
				`psupsell_products`.`product_id`,
				`psupsell_products`.`prefproduct_ids`,
				`psupsell_products`.`exclproduct_ids`,
				`psupsell_products`.`exclcat_ids`,
				`psupsell_products`.`excltags_ids`
			FROM
				`{$table_name_products}` AS `psupsell_products`
			WHERE
				1 = 1
			"
		);

		// Get the product category taxonomy.
		$category_taxonomy = psupsellmaster_get_product_category_taxonomy();

		// Get the product tag taxonomy.
		$tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

		// Get the produts.
		$products = PsUpsellMaster_Database::get_results( $sql_query );

		// Loop through the products.
		foreach ( $products as $product ) {
			// Get the product id.
			$product_id = isset( $product->product_id ) ? filter_var( $product->product_id, FILTER_VALIDATE_INT ) : false;

			// Check if the product id is empty.
			if ( empty( $product_id ) ) {
				continue;
			}

			// Get the enable upsell.
			$enable_upsell = isset( $product->enable ) ? filter_var( $product->enable, FILTER_VALIDATE_INT ) : false;

			// Check if the enable upsell is zero.
			if ( 0 === $enable_upsell ) {
				// Set the meta key.
				$meta_key = '_psupsellmaster_scores_disabled';

				// Migrate the meta data.
				update_post_meta( $product_id, $meta_key, true );
			}

			// Get the preferred products.
			$preferred_products = isset( $product->prefproduct_ids ) ? maybe_unserialize( $product->prefproduct_ids ) : array();

			// Check if the preferred products is not empty and is an array.
			if ( ! empty( $preferred_products ) && is_array( $preferred_products ) ) {
				// Loop through the preferred products.
				foreach ( $preferred_products as $preferred_product ) {
					// Set the meta key.
					$meta_key = 'psupsellmaster_preferred_products';

					// Add the meta data.
					add_post_meta( $product_id, $meta_key, $preferred_product );
				}
			}

			// Get the excluded products.
			$excluded_products = isset( $product->exclproduct_ids ) ? maybe_unserialize( $product->exclproduct_ids ) : array();

			// Check if the excluded products is not empty and is an array.
			if ( ! empty( $excluded_products ) && is_array( $excluded_products ) ) {
				// Loop through the excluded products.
				foreach ( $excluded_products as $excluded_product ) {
					// Set the meta key.
					$meta_key = 'psupsellmaster_excluded_products';

					// Add the meta data.
					add_post_meta( $product_id, $meta_key, $excluded_product );
				}
			}

			// Get the excluded categories.
			$excluded_categories = isset( $product->exclcat_ids ) ? maybe_unserialize( $product->exclcat_ids ) : array();

			// Check if the excluded categories is not empty and is an array.
			if ( ! empty( $excluded_categories ) && is_array( $excluded_categories ) ) {
				// Loop through the excluded categories.
				foreach ( $excluded_categories as $excluded_category ) {
					// Set the meta key.
					$meta_key = "psupsellmaster_excluded_tax_{$category_taxonomy}";

					// Add the meta data.
					add_post_meta( $product_id, $meta_key, $excluded_category );
				}
			}

			// Get the excluded tags.
			$excluded_tags = isset( $product->excltags_ids ) ? maybe_unserialize( $product->excltags_ids ) : array();

			// Check if the excluded tags is not empty and is an array.
			if ( ! empty( $excluded_tags ) && is_array( $excluded_tags ) ) {
				// Loop through the excluded tags.
				foreach ( $excluded_tags as $excluded_tag ) {
					// Set the meta key.
					$meta_key = "psupsellmaster_excluded_tax_{$tag_taxonomy}";

					// Add the meta data.
					add_post_meta( $product_id, $meta_key, $excluded_tag );
				}
			}

			// Get the product taxonomies.
			$product_taxonomies = psupsellmaster_get_product_taxonomies( 'names', false );

			// Loop through the product taxonomies.
			foreach ( $product_taxonomies as $product_taxonomy ) {
				// Set the meta key.
				$meta_key = "excluded_{$product_taxonomy}_ids";

				// Set the sql query.
				$sql_query = PsUpsellMaster_Database::prepare(
					'
					SELECT
						`psupsell_productmeta`.`meta_value`
					FROM
						%i AS `psupsell_productmeta`
					WHERE
						1 = 1
					AND
						`psupsell_productmeta`.`psupsell_product_id` = %d
					AND
						`psupsell_productmeta`.`meta_key` = %s
					',
					PsUpsellMaster_Database::get_table_name( 'psupsell_productmeta' ),
					$product_id,
					$meta_key
				);

				// Get the excluded terms.
				$excluded_terms = PsUpsellMaster_Database::get_var( $sql_query );
				$excluded_terms = ! empty( $excluded_terms ) ? maybe_unserialize( $excluded_terms ) : array();

				// Check if the excluded terms is not empty and is an array.
				if ( ! empty( $excluded_terms ) && is_array( $excluded_terms ) ) {
					// Loop through the excluded terms.
					foreach ( $excluded_terms as $excluded_term ) {
						// Set the meta key.
						$meta_key = "psupsellmaster_excluded_tax_{$product_taxonomy}";

						// Add the meta data.
						add_post_meta( $product_id, $meta_key, $excluded_term );
					}
				}
			}
		}
	}

	//
	// Update options.
	//

	// Get the old settings.
	$old_settings = get_option( 'psupsell_settings', array() );

	// Check if the old settings is not empty.
	if ( ! empty( $old_settings ) ) {
		// Check if there is a deprecated key.
		if ( isset( $old_settings['calc_data'] ) ) {
			// Unset the deprecated key.
			unset( $old_settings['calc_data'] );
		}

		// Check if there is a deprecated key.
		if ( isset( $old_settings['calc_progress'] ) ) {
			// Unset the deprecated key.
			unset( $old_settings['calc_progress'] );
		}

		// Check if there is a deprecated key.
		if ( isset( $old_settings['enable_log'] ) ) {
			// Unset the deprecated key.
			unset( $old_settings['enable_log'] );
		}

		// Update the new settings.
		update_option( 'psupsellmaster_settings', $old_settings, true );
	}

	//
	// Remove options.
	//

	// Delete the old settings option.
	delete_option( 'psupsell_settings' );

	// Delete the old wp cron analytics option.
	delete_option( 'psupsellmaster_wp_cron_analytics' );

	//
	// Remove old options not in use (that don't need migration but might be still in the database).
	//

	delete_option( 'upsellmaster_review_time' );
	delete_option( 'upsellmaster_license_key' );
	delete_option( 'upsellmaster_license_expiry' );
	delete_option( 'upsellmaster_license_status' );
	delete_option( 'upsellmaster_dismiss_review_notice' );

	PsUpsellMaster_Database::query(
		PsUpsellMaster_Database::prepare(
			'
			DELETE FROM 
				%i
			WHERE
				`option_name` LIKE %s
			',
			PsUpsellMaster_Database::get_table_name( 'options' ),
			'%psupsellmaster%analytics_update%'
		)
	);

	//
	// Remove old post meta keys not in use (that don't need migration but might be still in the database).
	//

	// Delete the meta key.
	delete_metadata( 'post', null, 'enable_upsell', '', true );

	//
	// Remove database tables.
	//

	// Set the database tables.
	$database_tables = array(
		PsUpsellMaster_Database::get_table_name( 'psupsell_products_debug' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_product_prices' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_productmeta' ),
		PsUpsellMaster_Database::get_table_name( 'psupsell_products' ),
	);

	// Loop through the database tables.
	foreach ( $database_tables as $database_table ) {
		// Drop the database table.
		PsUpsellMaster_Database::query( PsUpsellMaster_Database::prepare( 'DROP TABLE IF EXISTS %i', $database_table ) );
	}

	//
	// Remove old transients.
	//

	delete_transient( 'psupsellmaster_process_analytics_upsells' );
	delete_transient( 'psupsellmaster_process_analytics_upsells_lock' );
	delete_transient( 'psupsellmaster_process_analytics_orders' );
	delete_transient( 'psupsellmaster_process_analytics_orders_lock' );

	//
	// Clear scheduled events.
	//

	wp_clear_scheduled_hook( 'psupsells_data_cleanup_process' );
}

/**
 * Upgrade the database to version 1.7.0.
 */
function psupsellmaster_database_upgrade_version_1_7_0() {
	// Set the charset collate.
	$charset_collate = '';

	// Check if the database has the collation capability.
	if ( PsUpsellMaster_Database::has_cap( 'collation' ) ) {
		// Set the charset collate.
		$charset_collate = PsUpsellMaster_Database::get_charset_collate();
	}

	// Set the max index length.
	$max_index_length = 191;

	//
	// Create new database tables.
	//

	// Set the table name campaigns.
	$table_name_campaigns = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' );

	// Check if the table campaigns does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaigns}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaigns} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`title` text NOT NULL DEFAULT '',
			`status` varchar(20) NOT NULL DEFAULT '',
			`priority` mediumint(6) NOT NULL DEFAULT 0,
			`start_date` date NULL DEFAULT NULL,
			`end_date` date NULL DEFAULT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `status` (`status`),
			KEY `priority` (`priority`),
			KEY `start_date` (`start_date`),
			KEY `end_date` (`end_date`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaignmeta.
	$table_name_campaignmeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' );

	// Check if the table campaignmeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaignmeta}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaignmeta} (
			`meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`psupsellmaster_campaign_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext DEFAULT NULL,
			PRIMARY KEY  (`meta_id`),
			KEY `psupsellmaster_campaign_id` (`psupsellmaster_campaign_id`),
			KEY `meta_key` (`meta_key`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign eligible products.
	$table_name_campaign_eligible_products = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' );

	// Check if the table campaign eligible products does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_eligible_products}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_eligible_products} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`product_id` bigint(20) NOT NULL DEFAULT 0,
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `product_id` (`product_id`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign coupons.
	$table_name_campaign_coupons = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' );

	// Check if the table campaign coupons does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_coupons}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_coupons} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`coupon_id` bigint(20) NOT NULL DEFAULT 0,
			`code` varchar(100) DEFAULT '',
			`type` varchar(50) DEFAULT '',
			`amount` decimal(18,9) DEFAULT 0.00,
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `coupon_id` (`coupon_id`),
			KEY `code` (`code`),
			KEY `type` (`type`),
			KEY `amount` (`amount`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign products.
	$table_name_campaign_products = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Check if the table campaign products does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_products}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_products} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`product_id` bigint(20) NOT NULL DEFAULT 0,
			`type` varchar(20) NOT NULL DEFAULT '',
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `product_id` (`product_id`),
			KEY `type` (`type`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign authors.
	$table_name_campaign_authors = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' );

	// Check if the table campaign authors does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_authors}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_authors} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`author_id` bigint(20) NOT NULL DEFAULT 0,
			`type` varchar(20) NOT NULL DEFAULT '',
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `author_id` (`author_id`),
			KEY `type` (`type`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign taxonomies.
	$table_name_campaign_taxonomies = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' );

	// Check if the table campaign taxonomies does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_taxonomies}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_taxonomies} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`term_id` bigint(20) NOT NULL DEFAULT 0,
			`taxonomy` varchar(32) NOT NULL DEFAULT '',
			`type` varchar(20) NOT NULL DEFAULT '',
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `taxonomy` (`taxonomy`),
			KEY `term_id` (`term_id`),
			KEY `type` (`type`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign locations.
	$table_name_campaign_locations = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' );

	// Check if the table campaign locations does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_locations}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_locations} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`location` varchar(50) NOT NULL DEFAULT 0,
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `location` (`location`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign weekdays.
	$table_name_campaign_weekdays = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' );

	// Check if the table campaign weekdays does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_weekdays}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_weekdays} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`weekday` varchar(10) NOT NULL DEFAULT '',
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `weekday` (`weekday`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign display options.
	$table_name_campaign_display_options = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' );

	// Check if the table campaign display options does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_display_options}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_display_options} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`location` varchar(50) NOT NULL DEFAULT 0,
			`option_name` varchar(191) NOT NULL DEFAULT '',
			`option_value` longtext NOT NULL DEFAULT '',
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `location` (`location`),
			KEY `option_name` (`option_name`),
			KEY `option_value` (`option_value`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign carts.
	$table_name_campaign_carts = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' );

	// Check if the table campaign carts does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_carts}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_carts} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`cart_key` varchar(100) NOT NULL DEFAULT '',
			`last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`order_id` bigint(20) NOT NULL DEFAULT 0,
			`status` varchar(20) NOT NULL DEFAULT '',
			`quantity` bigint(20) NOT NULL DEFAULT 0,
			`subtotal` decimal(18,9) NOT NULL DEFAULT 0.00,
			`discount` decimal(18,9) NOT NULL DEFAULT 0.00,
			`tax` decimal(18,9) NOT NULL DEFAULT 0.00,
			`total` decimal(18,9) NOT NULL DEFAULT 0.00,
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `cart_key` (`cart_key`),
			KEY `last_modified` (`last_modified`),
			KEY `order_id` (`order_id`),
			KEY `status` (`status`),
			KEY `quantity` (`quantity`),
			KEY `subtotal` (`subtotal`),
			KEY `discount` (`discount`),
			KEY `tax` (`tax`),
			KEY `total` (`total`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign events.
	$table_name_campaign_events = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' );

	// Check if the table campaign events does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_events}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_events} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`campaign_id` bigint(20) NOT NULL DEFAULT 0,
			`event_date` date NOT NULL DEFAULT CURRENT_DATE(),
			`event_name` varchar(100) NOT NULL DEFAULT '',
			`location` varchar(100) NOT NULL DEFAULT '',
			`quantity` bigint(20) NOT NULL DEFAULT 0,
			PRIMARY KEY  (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `event_date` (`event_date`),
			KEY `event_name` (`event_name`),
			KEY `location` (`location`),
			KEY `quantity` (`quantity`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}
}

/**
 * Upgrade the database to version 1.7.9.
 */
function psupsellmaster_database_upgrade_version_1_7_9() {
	// Set the charset collate.
	$charset_collate = '';

	// Check if the database has the collation capability.
	if ( PsUpsellMaster_Database::has_cap( 'collation' ) ) {
		// Set the charset collate.
		$charset_collate = PsUpsellMaster_Database::get_charset_collate();
	}

	// Set the max index length.
	$max_index_length = 191;

	//
	// Create new database tables.
	//

	// Set the table name campaign templates.
	$table_name_campaign_templates = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' );

	// Check if the table campaign templates does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_templates}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_templates} (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL DEFAULT 0,
			`title` text NOT NULL DEFAULT '',
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
			PRIMARY KEY  (`id`),
			KEY `user_id` (`user_id`),
			KEY `title` (`title`)
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}

	// Set the table name campaign templatemeta.
	$table_name_campaign_templatemeta = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templatemeta' );

	// Check if the table campaign templatemeta does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaign_templatemeta}'" ) ) ) {
		// Set the query.
		$query = "CREATE TABLE IF NOT EXISTS {$table_name_campaign_templatemeta} (
			`meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`psupsellmaster_campaign_template_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext DEFAULT NULL,
			PRIMARY KEY  (`meta_id`),
			KEY `psupsellmaster_campaign_template_id` (`psupsellmaster_campaign_template_id`),
			KEY `meta_key` (`meta_key`({$max_index_length}))
		) {$charset_collate}";

		// Create the database table.

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $query );
	}
}

/**
 * Upgrade the database to version 1.7.50.
 */
function psupsellmaster_database_upgrade_version_1_7_50() {
	//
	// Modify existing database tables.
	//

	// Set the table name campaigns.
	$table_name_campaigns = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' );

	// Check if the table campaigns exists.
	if ( ! empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name_campaigns}'" ) ) ) {
		// Set the SQL query to modify the columns.
		$query = "ALTER TABLE `{$table_name_campaigns}`
			MODIFY COLUMN `start_date` datetime NULL DEFAULT NULL,
			MODIFY COLUMN `end_date` datetime NULL DEFAULT NULL";

		// Execute the query.
		PsUpsellMaster_Database::query( $query );
	}
}

/**
 * Upgrade the database to version 1.7.72.
 */
function psupsellmaster_database_upgrade_version_1_7_72() {
	//
	// Modify existing database tables.
	//

	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interests' );

	// Check if the table exists.
	if ( ! empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
		// Set the SQL query to modify the columns.
		$query = "ALTER TABLE `{$table_name}`
			ADD COLUMN `campaign_id` bigint(20) NOT NULL DEFAULT 0 AFTER `base_product_id`,
			ADD INDEX `campaign_id` (`campaign_id`)";

		// Execute the query.
		PsUpsellMaster_Database::query( $query );
	}

	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );

	// Check if the table exists.
	if ( ! empty( PsUpsellMaster_Database::get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
		// Set the SQL query to modify the columns.
		$query = "ALTER TABLE `{$table_name}`
			ADD COLUMN `campaign_id` bigint(20) NOT NULL DEFAULT 0 AFTER `base_product_id`,
			ADD INDEX `campaign_id` (`campaign_id`)";

		// Execute the query.
		PsUpsellMaster_Database::query( $query );
	}
}

/**
 * Upgrade the database to version 1.8.25.
 */
function psupsellmaster_database_upgrade_version_1_8_25() {
	// Get the version.
	$psupsellmaster_version = get_option( 'psupsellmaster_version' );

	// Check if the version is not empty (meaning it's not a fresh install).
	if ( ! empty( $psupsellmaster_version ) ) {
		// Update the option.
		update_option( 'psupsellmaster_admin_setup_wizard_status', 'completed', false );
	}

	// Delete the option.
	delete_option( 'psupsellmaster_quick_tour' );
}

/**
 * Upgrade the database to version 2.0.1.
 */
function psupsellmaster_database_upgrade_version_2_0_1() {
	// Delete bp-related (background process) options and transients.
	PsUpsellMaster_Database::query(
		PsUpsellMaster_Database::prepare(
			'
			DELETE FROM 
				%i
			WHERE
				`option_name` LIKE %s
			',
			PsUpsellMaster_Database::get_table_name( 'options' ),
			'%psupsellmaster_bp_%'
		)
	);
}

/**
 * Upgrade the database to version 2.0.20.
 */
function psupsellmaster_database_upgrade_version_2_0_20() {
	// Delete bp-related (background process) options and transients.
	PsUpsellMaster_Database::query(
		PsUpsellMaster_Database::prepare(
			'
			DELETE FROM 
				%i
			WHERE
				`visits` = %s
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ),
			'' // Yes, empty is correct.
		)
	);
}

<?php
/**
 * Uninstall.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall the plugin.
 */
function psupsellmaster_uninstall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/functions-base.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/database/class-psupsellmaster-database.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-psupsellmaster-settings.php';

	// Init the settings.
	PsUpsellMaster_Settings::init();

	// Get the remove data.
	$remove_data = PsUpsellMaster_Settings::get( 'remove_data' );
	$remove_data = filter_var( $remove_data, FILTER_VALIDATE_BOOLEAN );

	// Check if the remove data is true.
	if ( true === $remove_data ) {
		// Key.
		$key = 'psupsellmaster';

		// Set the is woo.
		$is_woo = psupsellmaster_is_plugin_active( 'woo' );

		// Set the is edd.
		$is_edd = psupsellmaster_is_plugin_active( 'edd' );

		// Check if the Easy Digital Downloads plugin is enabled.
		if ( $is_edd ) {
			// Set the edd path.
			$edd_path = plugin_dir_path( 'easy-digital-downloads/easy-digital-downloads.php' );

			// Check if the edd path exists.
			if ( file_exists( $edd_path ) ) {
				// Load the edd file.
				require_once $edd_path;

				// Set the edd instance.
				EDD();

				// Register components.
				edd_setup_components();
			}
		}

		//
		// Remove integrated data.
		//

		// Get the post meta rows.
		$rows = PsUpsellMaster_Database::get_results(
			PsUpsellMaster_Database::prepare(
				'SELECT `coupon_id` FROM %i',
				PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' )
			)
		);

		// Loop through the rows.
		foreach ( $rows as $row ) {
			// Get the coupon id.
			$coupon_id = isset( $row->coupon_id ) ? filter_var( $row->coupon_id, FILTER_VALIDATE_INT ) : false;

			// Check if the coupon id is empty.
			if ( empty( $coupon_id ) ) {
				continue;
			}

			// Check if the WooCommerce plugin is enabled.
			if ( $is_woo ) {
				// Set the args.
				$args = array(
					'ID'          => $coupon_id,
					'post_status' => 'pending',
				);

				// Update the coupon status (in case deletion fails for any reason).
				wp_update_post( $args );

				// Delete the coupon.
				wp_delete_post( $coupon_id, true );

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( $is_edd ) {
				// Set the update name.
				$update_name = PsUpsellMaster_Database::get_table_name( 'edd_adjustments' );

				// Set the update data.
				$update_data = array( 'status' => 'archived' );

				// Set the update where.
				$update_where = array(
					'id'   => $coupon_id,
					'type' => 'discount',
				);

				// Set the update data format.
				$update_data_format = array( '%s' );

				// Set the update where format.
				$update_where_format = array( '%d', '%s' );

				// Update the coupon status (in case deletion fails for any reason).
				// In EDD, coupons are not allowed to be deleted, so we archive them instead.
				// We could force its deletion, but it looks like EDD does not recommend it.
				PsUpsellMaster_Database::update(
					$update_name,
					$update_data,
					$update_where,
					$update_data_format,
					$update_where_format
				);

				// Delete the coupon.
				edd_delete_discount( $coupon_id );
			}
		}

		//
		// Remove terms.
		//

		// Set the taxonomies.
		$taxonomies = array( 'psupsellmaster_product_tag' );

		// Loop through the taxonomies.
		foreach ( $taxonomies as $taxonomy ) {
			// Set the sql query.
			$sql_query = PsUpsellMaster_Database::prepare(
				'
				SELECT
					`term_taxonomy`.`term_id`,
					`term_taxonomy`.`term_taxonomy_id`
				FROM
					%i AS `terms`
				INNER JOIN
					%i AS `term_taxonomy`
				ON
					`terms`.`term_id` = `term_taxonomy`.`term_id`
				WHERE
					`term_taxonomy`.`taxonomy` = %s
				',
				PsUpsellMaster_Database::get_table_name( 'terms' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$taxonomy
			);

			// Get the terms.
			$terms = PsUpsellMaster_Database::get_results( $sql_query );

			// Loop through the terms.
			foreach ( $terms as $term ) {
				// Delete the database rows.
				PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'term_relationships' ), array( 'term_taxonomy_id' => $term->term_taxonomy_id ), array( '%d' ) );
				PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ), array( 'term_taxonomy_id' => $term->term_taxonomy_id ), array( '%d' ) );
				PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'terms' ), array( 'term_id' => $term->term_id ), array( '%d' ) );
			}

			// Delete taxonomies.
			PsUpsellMaster_Database::delete( PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ), array( 'taxonomy' => $taxonomy ), array( '%s' ) );
		}

		//
		// Remove postmeta.
		//

		// Get the post meta rows.
		$rows = PsUpsellMaster_Database::get_results(
			PsUpsellMaster_Database::prepare(
				'
				SELECT
					`post_id`,
					`meta_key`
				FROM
					%i
				WHERE
					`meta_key` LIKE %s
				',
				PsUpsellMaster_Database::get_table_name( 'postmeta' ),
				'%' . $key . '%',
			)
		);

		// Loop through the rows.
		foreach ( $rows as $row ) {
			// Get the post id.
			$post_id = isset( $row->post_id ) ? $row->post_id : filter_var( $row->post_id, FILTER_VALIDATE_INT );

			// Check if the post id is empty.
			if ( empty( $post_id ) ) {
				continue;
			}

			// Get the meta key.
			$meta_key = isset( $row->meta_key ) ? $row->meta_key : '';

			// Check if the meta key is empty.
			if ( empty( $meta_key ) ) {
				continue;
			}

			// Delete the post meta.
			delete_post_meta( $post_id, $meta_key );
		}

		//
		// Remove options.
		//

		// Get the options.
		$options = PsUpsellMaster_Database::get_col(
			PsUpsellMaster_Database::prepare(
				'SELECT
					`option_name`
				FROM
					%i
				WHERE
					`option_name` LIKE %s
				',
				PsUpsellMaster_Database::get_table_name( 'options' ),
				'%' . $key . '%'
			)
		);

		// Loop through the options.
		foreach ( $options as $option ) {
			// Check if the option is empty.
			if ( empty( $option ) ) {
				continue;
			}

			// Delete the option.
			delete_option( $option );
		}

		//
		// Remove database tables.
		//

		// Set the database tables.
		$database_tables = array(
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templatemeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_productmeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_products' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitormeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interestmeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interests' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_resultmeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scoremeta' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' ),
		);

		// Loop through the database tables.
		foreach ( $database_tables as $database_table ) {
			// Drop the database table.
			PsUpsellMaster_Database::query( PsUpsellMaster_Database::prepare( 'DROP TABLE IF EXISTS %i', $database_table ) );
		}

		//
		// Clear scheduled events.
		//

		wp_clear_scheduled_hook( 'psupsellmaster_wp_cron_daily' );
		wp_clear_scheduled_hook( 'psupsellmaster_bp_analytics_orders_cron' );
		wp_clear_scheduled_hook( 'psupsellmaster_bp_analytics_upsells_cron' );
		wp_clear_scheduled_hook( 'psupsellmaster_bp_edd_prices_cron' );
		wp_clear_scheduled_hook( 'psupsellmaster_bp_scores_cron' );
	}
}

// Uninstall.
psupsellmaster_uninstall();

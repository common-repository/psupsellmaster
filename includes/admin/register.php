<?php
/**
 * Admin - Functions - Register.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a flash notice to {prefix}options table until a full page refresh is done.
 *
 * @param string  $notice our notice message.
 * @param string  $type This can be "info", "warning", "error" or "success", "warning" as default.
 * @param boolean $dismissible set this to TRUE to add is-dismissible functionality to your notice.
 */
function psupsellmaster_add_flash_notice( $notice = '', $type = 'warning', $dismissible = true ) {

	$notices          = PsUpsellMaster_Settings::get( 'flash_notices' );
	$dismissible_text = ( $dismissible ) ? 'is-dismissible' : '';
	array_push(
		$notices,
		array(
			'notice'      => $notice,
			'type'        => $type,
			'dismissible' => $dismissible_text,
		)
	);
	PsUpsellMaster_Settings::set( 'flash_notices', $notices, true );
}

/**
 * Function executed when the 'admin_notices' action is called, here we check if there are notices on
 * our database and display them, after that, we remove the option to prevent notices being displayed forever.
 *
 * @return void
 */
function psupsellmaster_display_flash_notices() {
	$notices = PsUpsellMaster_Settings::get( 'flash_notices' );

	foreach ( $notices as $notice ) {
		printf(
			'<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
			esc_attr( $notice['type'] ),
			esc_attr( $notice['dismissible'] ),
			wp_kses_post( $notice['notice'] )
		);
	}

	if ( ! empty( $notices ) ) {
		PsUpsellMaster_Settings::set( 'flash_notices', array(), true );
	}

	// Check if the WooCommerce plugin or the Easy Digital Downloads plugin is active.
	if ( psupsellmaster_is_plugin_active( 'edd' ) || psupsellmaster_is_plugin_active( 'woo' ) ) {
		return;
	}

	// Get the is edd installed.
	$is_edd_installed = psupsellmaster_is_plugin_installed( 'edd' );

	// Get the is woo installed.
	$is_woo_installed = psupsellmaster_is_plugin_installed( 'woo' );

	// Check if the Easy Digital Downloads plugin is installed.
	if ( $is_edd_installed ) {
		// Get the Easy Digital Downloads plugin path.
		$edd_path = psupsellmaster_get_plugin_path( 'edd' );

		// Define the activate url.
		$activate_url = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $edd_path ), 'activate-plugin_' . $edd_path ) );

		// Validate the activate url.
		$activate_url = false !== $edd_path ? $activate_url : '#';

		// Define the missing message.
		$message = sprintf(
			'%s %s. %s <a href="%s">%s</a> %s!',
			PSUPSELLMASTER_NAME,
			__( 'requires Easy Digital Downloads', 'psupsellmaster' ),
			__( 'Please', 'psupsellmaster' ),
			$activate_url,
			__( 'activate the Easy Digital Downloads plugin', 'psupsellmaster' ),
			__( 'to continue', 'psupsellmaster' ),
		);

		// Check if the WooCommerce plugin is installed.
	} elseif ( $is_woo_installed ) {
		// Get the WooCommerce plugin path.
		$woo_path = psupsellmaster_get_plugin_path( 'woo' );

		// Define the activate url.
		$activate_url = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $woo_path ), 'activate-plugin_' . $woo_path ) );

		// Validate the activate url.
		$activate_url = false !== $woo_path ? $activate_url : '#';

		// Define the message.
		$message = sprintf(
			'%s %s. %s <a href="%s">%s</a> %s!',
			PSUPSELLMASTER_NAME,
			__( 'requires WooCommerce', 'psupsellmaster' ),
			__( 'Please', 'psupsellmaster' ),
			$activate_url,
			__( 'activate the WooCommerce plugin', 'psupsellmaster' ),
			__( 'to continue', 'psupsellmaster' ),
		);

		// Otherwise...
	} else {
		// Define the install woo url.
		$install_woo_url = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) );

		// Define the install edd url.
		$install_edd_url = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=easy-digital-downloads' ), 'install-plugin_easy-digital-downloads' ) );

		// Define the message.
		$message = sprintf(
			'%s %s. %s <a href="%s">%s</a> %s <a href="%s">%s</a> %s!',
			PSUPSELLMASTER_NAME,
			__( 'requires either WooCommerce or Easy Digital Downloads', 'psupsellmaster' ),
			__( 'Please', 'psupsellmaster' ),
			$install_woo_url,
			__( 'install the WooCommerce plugin', 'psupsellmaster' ),
			__( 'or', 'psupsellmaster' ),
			$install_edd_url,
			__( 'install the Easy Digital Downloads plugin', 'psupsellmaster' ),
			__( 'to continue', 'psupsellmaster' ),
		);
	}
	?>
	<?php if ( ! empty( $message ) ) : ?>
	<div class="error">
		<p> <?php echo wp_kses_post( $message ); ?></p>
	</div>
	<?php endif; ?>
	<?php
}

/**
 * Gets the upsells data.
 */
function psupsellmaster_admin_ajax_get_upsells() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	$start_time = microtime( true );

	$search = ( isset( $_REQUEST['search']['value'] ) ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['search']['value'] ) ) ) : 0;
	$start  = ( isset( $_REQUEST['start'] ) ) ? (int) $_REQUEST['start'] : 0;
	$limit  = ( isset( $_REQUEST['length'] ) ) ? (int) $_REQUEST['length'] : 0;
	$filter = ( isset( $_REQUEST['f'] ) ) ? json_decode( stripslashes( sanitize_text_field( wp_unslash( $_REQUEST['f'] ) ) ), true ) : array();

	$result_array = array(
		'draw'            => isset( $_GET['draw'] ) ? ( (int) $_GET['draw'] + 1 ) : time(),
		'recordsTotal'    => 0,
		'recordsFiltered' => 0,
		'data'            => array(),
	);

	$sql         = PsUpsellMaster_Database::prepare( 'SELECT COUNT(*) FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );
	$total_items = (int) PsUpsellMaster_Database::get_var( $sql );

	$where = 'WHERE 1 = 1 ';

	if ( ! empty( $search ) ) {

		if ( is_numeric( $search ) ) {

			$where .= 'AND ((res.id = ' . (int) $search . ') OR (res.order_id = ' . (int) $search . '))';

		} else {

			$where .= 'AND ( '
					. PsUpsellMaster_Database::prepare( "(res.product_id IN (SELECT ID FROM %i AS wp1 WHERE wp1.post_title like '%{$search}%'))", PsUpsellMaster_Database::get_table_name( 'posts' ) )
					. PsUpsellMaster_Database::prepare( " OR (res.base_product_id IN (SELECT ID FROM %i AS wp2 WHERE wp2.post_title like '%{$search}%'))", PsUpsellMaster_Database::get_table_name( 'posts' ) )
					. PsUpsellMaster_Database::prepare( " OR (res.customer_id IN (SELECT ID FROM %i AS wpec WHERE wpec.`name` like '%{$search}%'))", PsUpsellMaster_Database::get_table_name( 'edd_customers' ) )
					. ' )';

		}
	}

	if ( isset( $_GET['f'] ) && is_array( $filter ) ) {
		// Set the category taxonomy.
		$category_taxonomy = '';

		// Set the tag taxonomy.
		$tag_taxonomy = '';

		// Set the price meta key.
		$price_meta_key = '';

		if ( defined( 'WC_VERSION' ) ) {
			// Set the category taxonomy.
			$category_taxonomy = 'product_cat';

			// Set the tag taxonomy.
			$tag_taxonomy = 'product_tag';

			// Set the meta key.
			$price_meta_key = '_regular_price';

		} elseif ( defined( 'EDD_VERSION' ) ) {
			// Set the category taxonomy.
			$category_taxonomy = 'download_category';

			// Set the tag taxonomy.
			$tag_taxonomy = 'download_tag';

			// Set the meta key.
			$price_meta_key = 'edd_price';
		}

		// Set the condition.
		$condition = '';

		// base product conditions.
		if ( array_key_exists( 'bp', $filter ) && ( count( $filter['bp'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['bp'] ), '%d' ) );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND res.base_product_id IN ( {$placeholders} )",
				$filter['bp']
			);
		}

		if ( array_key_exists( 'bc', $filter ) && ( count( $filter['bc'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['bc'] ), '%d' ) );

			// Set the sql terms.
			$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $filter['bc'] );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND ( res.base_product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$category_taxonomy
			);
		}

		if ( array_key_exists( 'bt', $filter ) && ( count( $filter['bt'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['bt'] ), '%d' ) );

			// Set the sql terms.
			$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $filter['bt'] );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND ( res.base_product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$tag_taxonomy
			);
		}

		if ( ! empty( $filter['custom_taxonomies'] ) ) {

			if ( ! empty( $filter['custom_taxonomies']['base'] ) ) {

				// Loop through the custom taxonomies.
				foreach ( $filter['custom_taxonomies']['base'] as $custom_taxonomy => $terms ) {
					// Check if the terms is empty.
					if ( empty( $terms ) ) {
						// Continue the loop.
						continue;
					}

					// Set the placeholders.
					$placeholders = implode( ', ', array_fill( 0, count( $terms ), '%d' ) );

					// Set the sql terms.
					$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $terms );

					// Add the condition.
					$condition .= PsUpsellMaster_Database::prepare(
						" AND ( res.base_product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
						PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
						PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
						$custom_taxonomy
					);
				}
			}

			if ( ! empty( $filter['custom_taxonomies']['upsell'] ) ) {

				// Loop through the custom taxonomies.
				foreach ( $filter['custom_taxonomies']['upsell'] as $custom_taxonomy => $terms ) {

					// Check if the terms is empty.
					if ( empty( $terms ) ) {
						// Continue the loop.
						continue;
					}

					// Set the placeholders.
					$placeholders = implode( ', ', array_fill( 0, count( $terms ), '%d' ) );

					// Set the sql terms.
					$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $terms );

					// Add the condition.
					$condition .= PsUpsellMaster_Database::prepare(
						" AND ( res.product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
						PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
						PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
						$custom_taxonomy
					);
				}
			}
		}

		// upsell conditions.
		if ( array_key_exists( 'up', $filter ) && ( count( $filter['up'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['up'] ), '%d' ) );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND res.product_id IN ( {$placeholders} )",
				$filter['up']
			);
		}

		if ( array_key_exists( 'uc', $filter ) && ( count( $filter['uc'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['uc'] ), '%d' ) );

			// Set the sql terms.
			$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $filter['uc'] );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND ( res.product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$category_taxonomy
			);
		}

		if ( array_key_exists( 'ut', $filter ) && ( count( $filter['ut'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['ut'] ), '%d' ) );

			// Set the sql terms.
			$sql_terms = PsUpsellMaster_Database::prepare( "`tax`.`term_id` IN ( {$placeholders} )", $filter['ut'] );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND ( res.product_id IN ( SELECT object_id FROM %i AS rel WHERE rel.term_taxonomy_id IN ( SELECT term_taxonomy_id FROM %i AS tax WHERE ( tax.taxonomy = %s ) AND ( {$sql_terms} ) ) ) )",
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$category_taxonomy
			);
		}

		// related conditions.
		if ( ! empty( $filter['rp'] ) && is_array( $filter['rp'] ) ) {
			$related_products_list = implode( ',', $filter['rp'] );

			$order_items_sql_query = 'SELECT 1 FROM DUAL';

			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				$order_items_sql_select  = 'SELECT 1';
				$order_items_sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `woim`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
				$order_items_sql_join    = array();
				$order_items_sql_join[]  = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `woi` ON `woim`.order_item_id = `woi`.order_item_id', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_items' ) );
				$order_items_sql_join    = implode( ' ', $order_items_sql_join );
				$order_items_sql_where   = array();
				$order_items_sql_where[] = 'WHERE 1 = 1';
				$order_items_sql_where[] = 'AND `woi`.`order_id` = res.`order_id`';
				$order_items_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woi`.`order_item_type` = %s', 'line_item' );
				$order_items_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woim`.`meta_key` = %s', '_product_id' );
				$order_items_sql_where[] = 'AND `woim`.`meta_value` <> res.`product_id`';
				$order_items_sql_where[] = "AND `woim`.`meta_value` IN ( {$related_products_list} )";
				$order_items_sql_where   = implode( ' ', $order_items_sql_where );
				$order_items_sql_query   = "( {$order_items_sql_select} {$order_items_sql_from} {$order_items_sql_join} {$order_items_sql_where} )";

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				$order_items_sql_select  = 'SELECT 1';
				$order_items_sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `eoi`', PsUpsellMaster_Database::get_table_name( 'edd_order_items' ) );
				$order_items_sql_where   = array();
				$order_items_sql_where[] = 'WHERE 1 = 1';
				$order_items_sql_where[] = 'AND `eoi`.`order_id` = res.`order_id`';
				$order_items_sql_where[] = 'AND `eoi`.`product_id` <> res.`product_id`';
				$order_items_sql_where[] = "AND `eoi`.`product_id` IN ( {$related_products_list} )";
				$order_items_sql_where   = implode( ' ', $order_items_sql_where );
				$order_items_sql_query   = "( {$order_items_sql_select} {$order_items_sql_from} {$order_items_sql_where} )";
			}

			$rel_condition = "( EXISTS ( {$order_items_sql_query} ) )";

			if ( ! empty( $condition ) ) {
				$condition .= " AND ( {$rel_condition} )";
			} else {
				$condition = "( {$rel_condition} )";
			}
		}

		// customer.
		if ( array_key_exists( 'cu', $filter ) && ( count( $filter['cu'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['cu'] ), '%d' ) );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND res.customer_id IN ( {$placeholders} )",
				$filter['cu']
			);
		}

		// location.
		if ( array_key_exists( 'loc', $filter ) && ( count( $filter['loc'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['loc'] ), '%s' ) );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND res.location IN ( {$placeholders} )",
				$filter['loc']
			);
		}

		// type.
		if ( array_key_exists( 'typ', $filter ) && ( count( $filter['typ'] ) > 0 ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $filter['typ'] ), '%s' ) );

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				" AND res.type IN ( {$placeholders} )",
				$filter['typ']
			);
		}

		// base product price from.
		if ( array_key_exists( 'prf', $filter ) && ! empty( $filter['prf'] ) ) {

			$prf = (float) $filter['prf'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND ( res.base_product_id IN ( SELECT post_id FROM %i WHERE ( meta_key = %s ) AND ( CAST( meta_value AS DECIMAL( 10, 6 ) ) >= %f ) ) )',
				PsUpsellMaster_Database::get_table_name( 'postmeta' ),
				$price_meta_key,
				$prf
			);
		}

		// base product price to.
		if ( array_key_exists( 'prt', $filter ) && ! empty( $filter['prt'] ) ) {

			$prt = (float) $filter['prt'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND ( res.base_product_id IN ( SELECT post_id FROM %i WHERE ( meta_key = %s ) AND ( CAST( meta_value AS DECIMAL( 10, 6 ) ) <= %f ) ) )',
				PsUpsellMaster_Database::get_table_name( 'postmeta' ),
				$price_meta_key,
				$prt
			);
		}

		// sale value  product price from.
		if ( array_key_exists( 'saf', $filter ) && ! empty( $filter['saf'] ) ) {

			$saf = (float) $filter['saf'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND res.amount >= %f',
				$saf
			);
		}

		// sale value product price to.
		if ( array_key_exists( 'sat', $filter ) && ! empty( $filter['sat'] ) ) {

			$sat = (float) $filter['sat'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND res.amount <= %f',
				$sat
			);
		}

		// date from.
		if ( array_key_exists( 'dtf', $filter ) && ! empty( $filter['dtf'] ) ) {

			$dtf = $filter['dtf'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND res.created_at >= %s',
				"$dtf 00:00:00"
			);
		}

		// date to.
		if ( array_key_exists( 'dtt', $filter ) && ! empty( $filter['dtt'] ) ) {

			$dtt = $filter['dtt'];

			// Add the condition.
			$condition .= PsUpsellMaster_Database::prepare(
				' AND res.created_at < DATE_ADD( %s, INTERVAL 1 DAY )',
				"$dtt 00:00:00"
			);
		}

		if ( ! empty( $condition ) ) {
			$where .= " {$condition}";
		}
	}

	if ( defined( 'EDD_VERSION' ) ) {
		$customer_field = PsUpsellMaster_Database::prepare( '(SELECT `name` FROM %i WHERE id = res.customer_id) AS `customer`, ', PsUpsellMaster_Database::get_table_name( 'edd_customers' ) );
	} else {
		$customer_field = PsUpsellMaster_Database::prepare( '(SELECT `display_name` FROM %i WHERE id = res.customer_id) AS `customer`, ', PsUpsellMaster_Database::get_table_name( 'users' ) );
	}

	$order_items_sql_query = 'SELECT 0 FROM DUAL';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$order_items_sql_select  = "SELECT GROUP_CONCAT( DISTINCT `woim`.`meta_value` SEPARATOR ',' ) AS `related_products`";
		$order_items_sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `woim`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
		$order_items_sql_join    = array();
		$order_items_sql_join[]  = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `woi` ON `woim`.order_item_id = `woi`.order_item_id', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_items' ) );
		$order_items_sql_join    = implode( ' ', $order_items_sql_join );
		$order_items_sql_where   = array();
		$order_items_sql_where[] = 'WHERE 1 = 1';
		$order_items_sql_where[] = 'AND `woi`.`order_id` = res.`order_id`';
		$order_items_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woi`.`order_item_type` = %s', 'line_item' );
		$order_items_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woim`.`meta_key` = %s', '_product_id' );
		$order_items_sql_where[] = 'AND `woim`.`meta_value` <> res.`product_id`';
		$order_items_sql_where   = implode( ' ', $order_items_sql_where );
		$order_items_sql_query   = "( {$order_items_sql_select} {$order_items_sql_from} {$order_items_sql_join} {$order_items_sql_where} )";

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$order_items_sql_select  = "SELECT GROUP_CONCAT( DISTINCT `eoi`.`product_id` SEPARATOR ',' ) AS `related_products`";
		$order_items_sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `eoi`', PsUpsellMaster_Database::get_table_name( 'edd_order_items' ) );
		$order_items_sql_where   = array();
		$order_items_sql_where[] = 'WHERE 1 = 1';
		$order_items_sql_where[] = 'AND `eoi`.`order_id` = res.`order_id`';
		$order_items_sql_where[] = 'AND `eoi`.`product_id` <> res.`product_id`';
		$order_items_sql_where   = implode( ' ', $order_items_sql_where );
		$order_items_sql_query   = "( {$order_items_sql_select} {$order_items_sql_from} {$order_items_sql_where} )";
	}

	$sql = 'SELECT *, '
			. $customer_field
			. PsUpsellMaster_Database::prepare( '(SELECT `post_title` FROM %i AS p1 WHERE p1.ID = res.product_id) AS `upsell`, ', PsUpsellMaster_Database::get_table_name( 'posts' ) )
			. "res.base_product_id AS `base_products`,\n"
			. "{$order_items_sql_query} AS related_products \n"
			. PsUpsellMaster_Database::prepare( "FROM %i AS res\n", PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. $where . "\n";

	$order_by = ' ORDER BY res.id DESC';

	if ( isset( $_REQUEST['order'] ) ) {
		$order_by = ' ORDER BY ';

		// Get the input order.
		$input_order = map_deep( wp_unslash( $_REQUEST['order'] ), 'sanitize_text_field' );

		foreach ( $input_order as $order_item ) {
			$order_column = filter_var( $order_item['column'], FILTER_VALIDATE_INT );

			if ( 1 === $order_column ) {
				$order_by .= trim( 'res.created_at ' . $order_item['dir'] );
			} elseif ( 2 === $order_column ) {
				$order_by .= trim( '`upsell` ' . $order_item['dir'] );
			} elseif ( 3 === $order_column ) {
				$order_by .= trim( PsUpsellMaster_Database::prepare( '( SELECT post_title FROM %i WHERE ID = res.base_product_id ) ', PsUpsellMaster_Database::get_table_name( 'posts' ) ) . $order_item['dir'] );
			} elseif ( 4 === $order_column ) {
				$order_by .= trim( '`customer` ' . $order_item['dir'] );
			} elseif ( 5 === $order_column ) {
				$order_by .= trim( 'res.location ' . $order_item['dir'] );
			} elseif ( 6 === $order_column ) {
				$order_by .= trim( 'res.source ' . $order_item['dir'] );
			} elseif ( 7 === $order_column ) {
				$order_by .= trim( 'res.type ' . $order_item['dir'] );
			} elseif ( 8 === $order_column ) {
				$order_by .= trim( 'res.view ' . $order_item['dir'] );
			} elseif ( 9 === $order_column ) {
				$order_by .= trim( 'res.order_id ' . $order_item['dir'] );
			} elseif ( 11 === $order_column ) {
				$order_by .= trim( 'res.amount ' . $order_item['dir'] );
			} else {
				$order_by .= trim( 'res.id ' . $order_item['dir'] );
			}
		}
	}

	$sql .= $order_by;

	if ( $limit > 0 ) {
		$sql .= " LIMIT $start, $limit";
	}

	$query = PsUpsellMaster_Database::get_results( $sql, ARRAY_A );

	foreach ( $query as $row ) {
		$upsell_edit_url = get_edit_post_link( $row['product_id'] );

		if ( empty( $upsell_edit_url ) ) {
			$upsell_edit_url = '#';
		}

		$upsell_view_url      = get_permalink( $row['product_id'] );
		$upsell_product_title = get_the_title( $row['product_id'] );

		$upsell_customer_fullname = $row['customer'];
		$upsell_customer_url      = '#';

		if ( defined( 'WC_VERSION' ) ) {
			$upsell_customer_url = site_url() . '/wp-admin/user-edit.php?user_id=' . $row['customer_id'];
			$payment_url         = site_url() . '/wp-admin/post.php?post=' . $row['order_id'] . '&action=edit';
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$upsell_customer_url = site_url() . '/wp-admin/edit.php?post_type=download&page=edd-customers&view=overview&id=' . $row['customer_id'];
			$payment_url         = site_url() . '/wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $row['order_id'];
		}

		// related.
		$related_html         = '';
		$str_related_products = '';

		if ( ! empty( $row['related_products'] ) ) {
			$str_related_products = trim( $row['related_products'] );
		}

		$related_products = explode( ',', $str_related_products );
		$related_products = array_filter( $related_products );

		if ( ! empty( $related_products ) ) {

			foreach ( $related_products as $related_product_id ) {
				$related_html .= '<a class="psupsellmaster-link" href="' . get_permalink( $related_product_id ) . '" target="_blank">' . get_the_title( $related_product_id ) . '<a/><br/>';
			}
		}

		if ( empty( $related_html ) ) {
			$related_html = '&#8212;';
		}

		$price_id = (int) $row['variation_id'] - 1;

		if ( $price_id <= 0 ) {
			$price_id = 0;
		}

		$price_option_html = '';

		if ( defined( 'WC_VERSION' ) ) {

			$variable_prices    = array();
			$woo_product_id     = ( $price_id > 0 ) ? $price_id : $row['product_id'];
			$woo_product_object = wc_get_product( $woo_product_id );

			if ( is_object( $woo_product_object ) ) {
				$price_option_html = trim( wp_strip_all_tags( wc_price( $woo_product_object->get_price() ) ) );
			}
		}

		if ( defined( 'EDD_VERSION' ) ) {

			$variable_prices   = edd_get_variable_prices( $row['product_id'] );
			$variable_prices   = is_array( $variable_prices ) ? $variable_prices : array();
			$price_option_html = '';

			if ( count( $variable_prices ) > 0 ) {

				foreach ( $variable_prices as $array_price ) {

					$idx = isset( $array_price['index'] ) ? (int) $array_price['index'] : 0;

					if ( $idx === $price_id ) {
						$price_option_html = $array_price['name'];
						break;
					}
				}
			} else {

				$price_option_html = edd_currency_filter( edd_format_amount( (float) get_post_meta( $row['product_id'], 'edd_price', true ) ) );

			}
		}

		if ( ! empty( $price_option_html ) ) {
			$upsell_product_title .= ' - ' . $price_option_html;
		}

		$date_format        = 'Y/m/d';
		$time_format        = 'h:i a';
		$upsell_create_date = gmdate( $date_format, strtotime( $row['created_at'] ) );

		$sales_value_price = '';

		if ( defined( 'WC_VERSION' ) ) {
			$sales_value_price = trim( wp_strip_all_tags( wc_price( $row['amount'] ) ) );
		}

		if ( defined( 'EDD_VERSION' ) ) {
			$sales_value_price = edd_currency_filter( edd_format_amount( $row['amount'] ) );
		}

		$base_product_ids    = trim( $row['base_products'] );
		$base_product_html   = '';
		$array_base_products = explode( ',', $base_product_ids );

		foreach ( $array_base_products as $base_product_id ) {

			if ( (int) $base_product_id <= 0 ) {
				continue;
			}

			$base_product_edit_url = get_edit_post_link( $base_product_id );

			if ( empty( $base_product_edit_url ) ) {
				$base_product_edit_url = '#';
			}

			$base_product_view_url = get_permalink( $base_product_id );
			$base_product_title    = get_the_title( $base_product_id );
			$base_product_html    .=
				'<div class="psupsellmaster_upsells_base_product">' .
				'<div class="psupsellmaster_upsells_edit_view_container">' .
				'<div class="psupsellmaster_upsells_title"><a class="psupsellmaster-link" href="' . $base_product_view_url . '" target="_blank">' . $base_product_title . '</a></div>' .
				'<div class="psupsellmaster_upsells_edit_view" style="display: none"><a class="psupsellmaster-link" href="' . $base_product_view_url . '" target="_blank">' . __( 'View', 'psupsellmaster' ) . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;' .
				'<a class="psupsellmaster-link" href="' . $base_product_edit_url . '" target="_blank">' . __( 'Edit', 'psupsellmaster' ) . '</a></div></div>' .
				'</div>';

		}

		if ( empty( $base_product_html ) ) {
			$base_product_html = '&#8212;';
		}

		// Define the customer html.
		$customer_html = '';

		// Check if the customer name is empty.
		if ( empty( $upsell_customer_fullname ) ) {
			// Set the customer html.
			$customer_html = '&#8212;';
			// Check if the customer url is empty.
		} elseif ( empty( $upsell_customer_url ) ) {
			// Set the customer html.
			$customer_html = $upsell_customer_fullname;
			// Otherwise...
		} else {
			// Set the customer html.
			$customer_html = "<a class='psupsellmaster-link' href='{$upsell_customer_url}' target='_blank'>{$upsell_customer_fullname}</a>";
		}

		$data_location = ! empty( $row['location'] ) ? $row['location'] : '';

		// Check the location.
		if ( 'download' === $data_location ) {
			// Replace the location display.
			$data_location = 'product';
		}

		// Get the location label.
		$location_label = psupsellmaster_get_product_location_label( $data_location );

		// Get the source label.
		$source_label = psupsellmaster_get_product_source_label( $row['source'] );

		// Get the type label.
		$type_label = psupsellmaster_get_purchase_type_label( $row['type'] );

		// Get the view label.
		$view_label = psupsellmaster_get_product_view_label( $row['view'] );

		$result_array['data'][] = array(
			// upsell ID.
			$row['id'],
			// Date.
			$upsell_create_date,
			// upsell.
			'<div class="psupsellmaster_upsells_edit_view_container">' .
			'<div class="psupsellmaster_upsells_title"><a class="psupsellmaster-link" href="' . $upsell_view_url . '" target="_blank">' . $upsell_product_title . '</a></div>' .
			'<div class="psupsellmaster_upsells_edit_view" style="display: none"><a class="psupsellmaster-link" href="' . $upsell_view_url . '" target="_blank">' . __( 'View', 'psupsellmaster' ) . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;' .
			'<a class="psupsellmaster-link" href="' . $upsell_edit_url . '" target="_blank">' . __( 'Edit', 'psupsellmaster' ) . '</a></div></div>',
			// base product.
			$base_product_html,
			// Customer HTML.
			$customer_html,
			// Location.
			$location_label,
			$source_label,
			$type_label,
			$view_label,
			// Payment ID.
			'<a class="psupsellmaster-link" href="' . $payment_url . '" target="_blank">' . $row['order_id'] . '</a>',
			// Related.
			$related_html,
			// Upsell Value.
			$sales_value_price,
		);

	}

	// Define the tables and aliases.

	$table_edd_orders  = PsUpsellMaster_Database::get_table_name( 'edd_orders' );
	$table_pu_results  = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );
	$table_wp_postmeta = PsUpsellMaster_Database::get_table_name( 'postmeta' );
	$table_wp_posts    = PsUpsellMaster_Database::get_table_name( 'posts' );
	$alias_edd_orders  = 'o';
	$alias_pu_results  = 'r';
	$alias_wp_postmeta = 'm';
	$alias_wp_posts    = 'p';

	// Build the default SQL where.
	$default_sql_where = str_replace( 'res.', "{$alias_pu_results}.", $where );

	// Build the SQL query - aggregate functions for results.

	$sql_select   = array();
	$sql_select[] = 'COUNT( * ) AS count_results';
	$sql_select[] = "COUNT( DISTINCT {$alias_pu_results}.product_id ) AS countd_upsell_id";
	$sql_select[] = "COUNT( DISTINCT {$alias_pu_results}.order_id ) AS countd_payment_id";
	$sql_select[] = "COUNT( DISTINCT {$alias_pu_results}.customer_id ) AS countd_customer_id";
	$sql_select[] = "SUM( {$alias_pu_results}.amount ) AS sum_sales_value";
	$sql_select[] = "AVG( {$alias_pu_results}.amount ) AS avg_sales_value";
	$sql_select[] = "MIN( {$alias_pu_results}.amount ) AS min_sales_value";
	$sql_select[] = "MAX( {$alias_pu_results}.amount ) AS max_sales_value";
	$sql_select[] = "MIN( {$alias_pu_results}.created_at ) AS min_created";
	$sql_select[] = "MAX( {$alias_pu_results}.created_at ) AS max_created";
	$sql_select   = implode( ', ', $sql_select );
	$sql_select   = "SELECT {$sql_select}";
	$sql_from     = "FROM {$table_pu_results} AS {$alias_pu_results}";
	$sql_where    = array();
	$sql_where[]  = "{$default_sql_where}";
	$sql_where    = implode( ' ', $sql_where );
	$sql_query    = "{$sql_select} {$sql_from} {$sql_where}";

	// Execute the SQL query.
	$sql_results = PsUpsellMaster_Database::get_row( $sql_query );

	// Parse the SQL query results.

	$sanitized_results = array();

	$sanitized_results['count_results']      = intval( $sql_results->count_results );
	$sanitized_results['countd_upsell_id']   = intval( $sql_results->countd_upsell_id );
	$sanitized_results['countd_payment_id']  = intval( $sql_results->countd_payment_id );
	$sanitized_results['countd_customer_id'] = intval( $sql_results->countd_customer_id );
	$sanitized_results['sum_sales_value']    = floatval( $sql_results->sum_sales_value );
	$sanitized_results['avg_sales_value']    = floatval( $sql_results->avg_sales_value );
	$sanitized_results['min_sales_value']    = floatval( $sql_results->min_sales_value );
	$sanitized_results['max_sales_value']    = floatval( $sql_results->max_sales_value );
	$sanitized_results['min_created']        = strtotime( ( is_string( $sql_results->min_created ) ? $sql_results->min_created : '' ) );
	$sanitized_results['max_created']        = strtotime( ( is_string( $sql_results->max_created ) ? $sql_results->max_created : '' ) );

	// Build the SQL query - base products.

	$sql_select = 'SELECT COUNT( DISTINCT base_product_id ) AS countd_base_product_id';
	$sql_from   = "FROM {$table_pu_results} AS {$alias_pu_results}";
	$sql_query  = "{$sql_select} {$sql_from} {$default_sql_where}";

	// Execute the SQL query.
	$sql_results = PsUpsellMaster_Database::get_row( $sql_query );

	// Parse the SQL query results.
	$sanitized_results['countd_base_product_id'] = intval( $sql_results->countd_base_product_id );

	// Build the SQL query - sub sql for results.

	$sub_sql_select  = 'SELECT 1';
	$sub_sql_from    = "FROM {$table_pu_results} AS {$alias_pu_results}";
	$sub_sql_where   = array();
	$sub_sql_where[] = "{$default_sql_where}";

	if ( defined( 'EDD_VERSION' ) ) {
		$sub_sql_where[] = "AND {$alias_pu_results}.order_id = {$alias_edd_orders}.id";
	} elseif ( defined( 'WC_VERSION' ) ) {
		$sub_sql_where[] = "AND {$alias_pu_results}.order_id = {$alias_wp_postmeta}.post_id";
	}

	$sub_sql_where = implode( ' ', $sub_sql_where );
	$sub_sql_query = "{$sub_sql_select} {$sub_sql_from} {$sub_sql_where}";

	// Build the SQL query - orders total.
	if ( defined( 'WC_VERSION' ) ) {
		$sql_select  = "SELECT SUM( {$alias_wp_postmeta}.meta_value ) AS sum_order_value_incl_upsells";
		$sql_from    = "FROM {$table_wp_postmeta} AS {$alias_wp_postmeta}";
		$sql_join    = array();
		$sql_join[]  = "INNER JOIN {$table_wp_posts} AS {$alias_wp_posts} ON {$alias_wp_postmeta}.post_id = {$alias_wp_posts}.ID";
		$sql_join    = implode( ' ', $sql_join );
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = "AND {$alias_wp_posts}.post_type = 'shop_order'";
		$sql_where[] = "AND {$alias_wp_postmeta}.meta_key = '_order_total'";
		$sql_where[] = "AND EXISTS ( {$sub_sql_query} )";
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_select} {$sql_from} {$sql_join} {$sql_where}";
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$sql_select  = "SELECT SUM( {$alias_edd_orders}.total ) AS sum_order_value_incl_upsells";
		$sql_from    = "FROM {$table_edd_orders} AS {$alias_edd_orders}";
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = "AND {$alias_edd_orders}.type = 'sale'";
		$sql_where[] = "AND EXISTS ( {$sub_sql_query} )";
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_select} {$sql_from} {$sql_where}";
	}

	// Execute the SQL query.
	$sql_results = PsUpsellMaster_Database::get_row( $sql_query );

	// Parse the SQL query results.
	$sanitized_results['sum_order_value_incl_upsells'] = floatval( $sql_results->sum_order_value_incl_upsells );

	// Records.
	$result_array['recordsTotal']    = $total_items;
	$result_array['recordsFiltered'] = intval( $sanitized_results['count_results'] );

	// Dates.
	$result_array['min_created'] = isset( $filter['dtf'] ) && ! empty( $filter['dtf'] ) ? $filter['dtf'] : gmdate( 'Y-m-d', $sanitized_results['min_created'] );
	$result_array['max_created'] = isset( $filter['dtt'] ) && ! empty( $filter['dtt'] ) ? $filter['dtt'] : gmdate( 'Y-m-d', $sanitized_results['max_created'] );

	// Days.
	$min_created  = date_create_from_format( 'Y-m-d', $result_array['min_created'] );
	$max_created  = date_create_from_format( 'Y-m-d', $result_array['max_created'] );
	$diff_created = date_diff( $max_created, $min_created );

	// Build response.

	$result_array['days_created']                     = ( $diff_created->days + 1 );
	$result_array['countd_base_product_id']           = $sanitized_results['countd_base_product_id'];
	$result_array['countd_customer_id']               = $sanitized_results['countd_customer_id'];
	$result_array['countd_payment_id']                = $sanitized_results['countd_payment_id'];
	$result_array['countd_upsell_id']                 = $sanitized_results['countd_upsell_id'];
	$result_array['sum_order_value_excl_upsells']     = $sanitized_results['sum_order_value_incl_upsells'] - $sanitized_results['sum_sales_value'];
	$result_array['sum_order_value_excl_upsells']     = $result_array['sum_order_value_excl_upsells'] > 0 ? $result_array['sum_order_value_excl_upsells'] : 0;
	$result_array['avg_sales_value_per_base_product'] = ! empty( $sanitized_results['countd_base_product_id'] ) ? ( $sanitized_results['sum_sales_value'] / $sanitized_results['countd_base_product_id'] ) : 0;
	$result_array['avg_sales_value_per_customer']     = ! empty( $sanitized_results['countd_customer_id'] ) ? ( $sanitized_results['sum_sales_value'] / $sanitized_results['countd_customer_id'] ) : 0;
	$result_array['avg_sales_value_per_day']          = ! empty( $result_array['days_created'] ) ? ( $sanitized_results['sum_sales_value'] / $result_array['days_created'] ) : 0;
	$result_array['avg_order_value_excl_upsells']     = ! empty( $sanitized_results['countd_payment_id'] ) ? ( $result_array['sum_order_value_excl_upsells'] / $sanitized_results['countd_payment_id'] ) : 0;
	$result_array['avg_order_value_incl_upsells']     = ! empty( $sanitized_results['countd_payment_id'] ) ? ( $sanitized_results['sum_order_value_incl_upsells'] / $sanitized_results['countd_payment_id'] ) : 0;
	$result_array['avg_sales_value_per_order']        = ! empty( $sanitized_results['countd_payment_id'] ) ? ( $sanitized_results['sum_sales_value'] / $sanitized_results['countd_payment_id'] ) : 0;
	$result_array['avg_upsells_per_product']          = ! empty( $sanitized_results['countd_upsell_id'] ) ? ( $sanitized_results['count_results'] / $sanitized_results['countd_upsell_id'] ) : 0;

	if ( defined( 'WC_VERSION' ) ) {
		$result_array['sum_sales_value']                  = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_sales_value'] ) ) );
		$result_array['avg_sales_value']                  = trim( wp_strip_all_tags( wc_price( $sanitized_results['avg_sales_value'] ) ) );
		$result_array['min_sales_value']                  = trim( wp_strip_all_tags( wc_price( $sanitized_results['min_sales_value'] ) ) );
		$result_array['max_sales_value']                  = trim( wp_strip_all_tags( wc_price( $sanitized_results['max_sales_value'] ) ) );
		$result_array['sum_order_value_incl_upsells']     = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_order_value_incl_upsells'] ) ) );
		$result_array['avg_sales_value_per_base_product'] = trim( wp_strip_all_tags( wc_price( $result_array['avg_sales_value_per_base_product'] ) ) );
		$result_array['avg_sales_value_per_customer']     = trim( wp_strip_all_tags( wc_price( $result_array['avg_sales_value_per_customer'] ) ) );
		$result_array['avg_sales_value_per_day']          = trim( wp_strip_all_tags( wc_price( $result_array['avg_sales_value_per_day'] ) ) );
		$result_array['avg_sales_value_per_order']        = trim( wp_strip_all_tags( wc_price( $result_array['avg_sales_value_per_order'] ) ) );
		$result_array['avg_order_value_excl_upsells']     = trim( wp_strip_all_tags( wc_price( $result_array['avg_order_value_excl_upsells'] ) ) );
		$result_array['avg_order_value_incl_upsells']     = trim( wp_strip_all_tags( wc_price( $result_array['avg_order_value_incl_upsells'] ) ) );
		$result_array['sum_order_value_excl_upsells']     = trim( wp_strip_all_tags( wc_price( $result_array['sum_order_value_excl_upsells'] ) ) );
		$result_array['avg_upsells_per_product']          = wc_format_decimal( $result_array['avg_upsells_per_product'], 2 );
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$result_array['sum_sales_value']                  = edd_currency_filter( edd_format_amount( $sanitized_results['sum_sales_value'] ) );
		$result_array['avg_sales_value']                  = edd_currency_filter( edd_format_amount( $sanitized_results['avg_sales_value'] ) );
		$result_array['min_sales_value']                  = edd_currency_filter( edd_format_amount( $sanitized_results['min_sales_value'] ) );
		$result_array['max_sales_value']                  = edd_currency_filter( edd_format_amount( $sanitized_results['max_sales_value'] ) );
		$result_array['sum_order_value_incl_upsells']     = edd_currency_filter( edd_format_amount( $sanitized_results['sum_order_value_incl_upsells'] ) );
		$result_array['avg_sales_value_per_base_product'] = edd_currency_filter( edd_format_amount( $result_array['avg_sales_value_per_base_product'] ) );
		$result_array['avg_sales_value_per_customer']     = edd_currency_filter( edd_format_amount( $result_array['avg_sales_value_per_customer'] ) );
		$result_array['avg_sales_value_per_day']          = edd_currency_filter( edd_format_amount( $result_array['avg_sales_value_per_day'] ) );
		$result_array['avg_sales_value_per_order']        = edd_currency_filter( edd_format_amount( $result_array['avg_sales_value_per_order'] ) );
		$result_array['avg_order_value_excl_upsells']     = edd_currency_filter( edd_format_amount( $result_array['avg_order_value_excl_upsells'] ) );
		$result_array['avg_order_value_incl_upsells']     = edd_currency_filter( edd_format_amount( $result_array['avg_order_value_incl_upsells'] ) );
		$result_array['sum_order_value_excl_upsells']     = edd_currency_filter( edd_format_amount( $result_array['sum_order_value_excl_upsells'] ) );
		$result_array['avg_upsells_per_product']          = edd_format_amount( $result_array['avg_upsells_per_product'] );
	}

	$result_array['range_sales_value'] = $result_array['min_sales_value'] . ' - ' . $result_array['max_sales_value'];

	// get chart data.
	$sql                             = 'SELECT SUM(amount), DATE(created_at) '
			. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. str_replace( 'res.', '', $where ) . ' '
			. 'GROUP BY DATE(created_at) '
			. 'ORDER BY 2';
	$result_array['chart_data']      = array();
	$result_array['currency_symbol'] = '$';
	$query_chart_data                = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$day              = 1;
	$array_chart_data = array();
	$current_date     = $result_array['min_created'];

	while ( strtotime( $current_date ) <= strtotime( $result_array['max_created'] ) ) {
		$array_chart_data[ $current_date ] = 0;
		$current_date                      = gmdate( 'Y-m-d', strtotime( $current_date . ' +1 day' ) );
	}

	foreach ( $query_chart_data as $row ) {
		$array_chart_data[ $row[1] ] = $row[0];
	}

	$current_date = $result_array['min_created'];

	while ( strtotime( $current_date ) <= strtotime( $result_array['max_created'] ) ) {

		$label = ( $result_array['days_created'] <= 7 ) ? $current_date : gmdate( 'j M', strtotime( $current_date ) );

		if ( defined( 'WC_VERSION' ) ) {
			$wc_price_args = array(
				'decimal_separator'  => '.',
				'thousand_separator' => '',
			);

			$result_array['chart_data'][] = array( psupsellmaster_woo_wc_price_no_markup( $array_chart_data[ $current_date ], $wc_price_args ), $label );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$result_array['chart_data'][] = array( $array_chart_data[ $current_date ], $label );
		}

		$current_date = gmdate( 'Y-m-d', strtotime( $current_date . ' +1 day' ) );

	}

	unset( $array_chart_data );
	unset( $query_chart_data );
	// get top stats.

	// upsells.
	$result_array['top_upsells'] = array();
	$sql                         = 'SELECT COUNT(*), SUM(amount), product_id AS upsell_id '
			. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. str_replace( 'res.', '', $where ) . ' '
			. 'GROUP BY product_id '
			. 'ORDER BY 2 DESC '
			. 'LIMIT 0,5';
	$query                       = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$sum   = 0;
	$total = 0;

	foreach ( $query as $row ) {

		$upsell_qty     = (int) $row[0];
		$upsell_sum     = (float) $row[1];
		$total         += $upsell_sum;
		$upsell_id      = $row[2];
		$upsell_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $upsell_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;
		$sum           += $upsell_percent;

		if ( defined( 'WC_VERSION' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $upsell_sum ) ) );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $upsell_sum ) );
		}

		$result_array['top_upsells'][] = array(
			$upsell_id,
			$current_sum,
			round( $upsell_percent ),
			get_the_title( $upsell_id ),
			get_permalink( $upsell_id ),
		);

	}

	$sum                               = round( $sum );
	$result_array['top_upsells_other'] = 100 - $sum;

	if ( defined( 'WC_VERSION' ) ) {
		$result_array['top_upsells_other_sum'] = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_sales_value'] - $total ) ) );
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$result_array['top_upsells_other_sum'] = edd_currency_filter( edd_format_amount( $sanitized_results['sum_sales_value'] - $total ) );
	}

	// base.
	$result_array['top_base'] = array();
	$sql                      = 'SELECT COUNT(*), SUM(res.amount), res.base_product_id '
			. PsUpsellMaster_Database::prepare( 'FROM %i AS res ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. str_replace( 'res.', '', $where ) . ' '
			. 'GROUP BY res.base_product_id '
			. 'ORDER BY 2 DESC '
			. 'LIMIT 0,5';
	$query                    = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$sum   = 0;
	$total = 0;

	foreach ( $query as $row ) {

		$base_qty     = (int) $row[0];
		$base_sum     = (float) $row[1];
		$total       += $base_sum;
		$base_id      = $row[2];
		$base_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $base_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;
		$sum         += $base_percent;

		if ( defined( 'WC_VERSION' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $base_sum ) ) );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $base_sum ) );
		}

		$result_array['top_base'][] = array(
			$base_id,
			$current_sum,
			round( $base_percent ),
			get_the_title( $base_id ),
			get_permalink( $base_id ),
		);

	}

	$sum                            = round( $sum );
	$result_array['top_base_other'] = 100 - $sum;

	if ( defined( 'WC_VERSION' ) ) {
		$result_array['top_base_other_sum'] = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_sales_value'] - $total ) ) );
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$result_array['top_base_other_sum'] = edd_currency_filter( edd_format_amount( $sanitized_results['sum_sales_value'] - $total ) );
	}

	// customers.
	$result_array['top_customers'] = array();

	if ( defined( 'WC_VERSION' ) ) {

		$sql = PsUpsellMaster_Database::prepare( 'SELECT COUNT(*), SUM(amount), customer_id, (SELECT `display_name` FROM %i AS u WHERE u.ID = customer_id)', PsUpsellMaster_Database::get_table_name( 'users' ) )
				. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
				. str_replace( 'res.', '', $where ) . ' '
				. 'GROUP BY customer_id '
				. 'ORDER BY 2 DESC '
				. 'LIMIT 0,5';

	} elseif ( defined( 'EDD_VERSION' ) ) {

		$sql = PsUpsellMaster_Database::prepare( 'SELECT COUNT(*), SUM(amount), customer_id, (SELECT `name` FROM %i WHERE id = customer_id)', PsUpsellMaster_Database::get_table_name( 'edd_customers' ) )
				. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
				. str_replace( 'res.', '', $where ) . ' '
				. 'GROUP BY customer_id '
				. 'ORDER BY 2 DESC '
				. 'LIMIT 0,5';

	}

	$query = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$sum   = 0;
	$total = 0;

	foreach ( $query as $row ) {

		$customer_qty     = (int) $row[0];
		$customer_sum     = (float) $row[1];
		$total           += $customer_sum;
		$customer_id      = $row[2];
		$customer_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $customer_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;
		$sum             += $customer_percent;

		if ( defined( 'WC_VERSION' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $customer_sum ) ) );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $customer_sum ) );
		}

		$customer_name = ! empty( $row[3] ) ? $row[3] : '—';

		$result_array['top_customers'][] = array(
			$customer_id,
			$current_sum,
			round( $customer_percent ),
			$customer_name,
			site_url() . '/wp-admin/edit.php?post_type=download&page=edd-customers&view=overview&id=' . $customer_id,
		);

	}

	$sum                                 = round( $sum );
	$result_array['top_customers_other'] = 100 - $sum;

	if ( defined( 'WC_VERSION' ) ) {
		$result_array['top_customers_other_sum'] = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_sales_value'] - $total ) ) );
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$result_array['top_customers_other_sum'] = edd_currency_filter( edd_format_amount( $sanitized_results['sum_sales_value'] - $total ) );
	}

	// orders.
	$result_array['top_orders'] = array();
	$sql                        = 'SELECT COUNT(*), SUM(amount), order_id '
			. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. str_replace( 'res.', '', $where ) . ' '
			. 'GROUP BY order_id '
			. 'ORDER BY 2 DESC '
			. 'LIMIT 0,5';
	$query                      = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$sum   = 0;
	$total = 0;

	foreach ( $query as $row ) {

		$order_qty     = (int) $row[0];
		$order_sum     = (float) $row[1];
		$total        += $order_sum;
		$order_id      = $row[2];
		$order_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $order_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;
		$sum          += $order_percent;

		if ( defined( 'WC_VERSION' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $order_sum ) ) );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $order_sum ) );
		}

		$result_array['top_orders'][] = array(
			$order_id,
			$current_sum,
			round( $order_percent ),
			$order_id,
			site_url() . '/wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $order_id,
		);

	}

	$sum                              = round( $sum );
	$result_array['top_orders_other'] = 100 - $sum;

	if ( defined( 'WC_VERSION' ) ) {
		$result_array['top_orders_other_sum'] = trim( wp_strip_all_tags( wc_price( $sanitized_results['sum_sales_value'] - $total ) ) );
	} elseif ( defined( 'EDD_VERSION' ) ) {
		$result_array['top_orders_other_sum'] = edd_currency_filter( edd_format_amount( $sanitized_results['sum_sales_value'] - $total ) );
	}

	// locations.
	$result_array['top_locations'] = array();
	$sql                           = 'SELECT COUNT(*), SUM(amount), `location` '
			. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) )
			. str_replace( 'res.', '', $where ) . ' '
			. 'GROUP BY `location` '
			. 'ORDER BY 2 DESC ';
	$query                         = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	$sum   = 0;
	$total = 0;

	foreach ( $query as $row ) {

		$location_qty     = (int) $row[0];
		$location_sum     = (float) $row[1];
		$total           += $location_sum;
		$location         = $row[2];
		$location_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $location_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;
		$sum             += $location_percent;

		if ( defined( 'WC_VERSION' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $location_sum ) ) );
		} elseif ( defined( 'EDD_VERSION' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $location_sum ) );
		}

		$result_array['top_locations'][] = array(
			$location,
			$current_sum,
			round( $location_percent ),
		);

	}

	//
	// Sources.
	//

	// Define the sources.
	$result_array['top_sources'] = array();

	// Set the SQL query.
	$sql_query = '';

	// Build the SQL query.
	$sql_query .= 'SELECT SUM( `amount` ) AS `sum_amount`, `source` ';
	$sql_query .= PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );
	$sql_query .= str_replace( 'res.', '', $where ) . ' ';
	$sql_query .= 'GROUP BY `source` ORDER BY 2 DESC';

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Define the sum and total.
	$sum   = 0;
	$total = 0;

	// Loop through the results.
	foreach ( $results as $row ) {
		// Get the source sum.
		$source_sum = (float) $row->sum_amount;

		// Sum the total.
		$total += $source_sum;

		// Get the source.
		$source = $row->source;

		// Set the percent.
		$source_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $source_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;

		// Sum the percent.
		$sum += $source_percent;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $source_sum ) ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $source_sum ) );
		}

		// Set the sources.
		$result_array['top_sources'][] = array(
			$source,
			$current_sum,
			round( $source_percent ),
		);
	}

	//
	// Types.
	//

	// Define the types.
	$result_array['top_types'] = array();

	// Set the SQL query.
	$sql_query = '';

	// Build the SQL query.
	$sql_query .= 'SELECT SUM( `amount` ) AS `sum_amount`, `type` ';
	$sql_query .= PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );
	$sql_query .= str_replace( 'res.', '', $where ) . ' ';
	$sql_query .= 'GROUP BY `type` ORDER BY 2 DESC';

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Define the sum and total.
	$sum   = 0;
	$total = 0;

	// Loop through the results.
	foreach ( $results as $row ) {
		// Get the type sum.
		$type_sum = (float) $row->sum_amount;

		// Sum the total.
		$total += $type_sum;

		// Get the type.
		$type = ! empty( $row->type ) ? $row->type : 'unknown';

		// Set the percent.
		$type_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $type_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;

		// Sum the percent.
		$sum += $type_percent;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $type_sum ) ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $type_sum ) );
		}

		// Set the types.
		$result_array['top_types'][] = array(
			$type,
			$current_sum,
			round( $type_percent ),
		);
	}

	//
	// Views.
	//

	// Define the views.
	$result_array['top_views'] = array();

	// Set the SQL query.
	$sql_query = '';

	// Build the SQL query.
	$sql_query .= 'SELECT SUM( `amount` ) AS `sum_amount`, `view` ';
	$sql_query .= PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );
	$sql_query .= str_replace( 'res.', '', $where ) . ' ';
	$sql_query .= 'GROUP BY `view` ORDER BY 2 DESC';

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Define the sum and total.
	$sum   = 0;
	$total = 0;

	// Loop through the results.
	foreach ( $results as $row ) {
		// Get the view sum.
		$view_sum = (float) $row->sum_amount;

		// Sum the total.
		$total += $view_sum;

		// Get the view.
		$view = ! empty( $row->view ) ? $row->view : 'unknown';

		// Set the percent.
		$view_percent = ( $sanitized_results['sum_sales_value'] > 0 ) ? $view_sum / $sanitized_results['sum_sales_value'] * 100.0 : 0;

		// Sum the percent.
		$sum += $view_percent;

		// Check if the WooCommerce plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			$current_sum = trim( wp_strip_all_tags( wc_price( $view_sum ) ) );

			// Check if the Easy Digital Downloads plugin is enabled.
		} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			$current_sum = edd_currency_filter( edd_format_amount( $view_sum ) );
		}

		// Set the views.
		$result_array['top_views'][] = array(
			$view,
			$current_sum,
			round( $view_percent ),
		);
	}

	$end = microtime( true ) - $start_time;

	// reset all what was already in output buffer to avoid bugs.
	ob_clean();

	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	ob_start();

	echo wp_json_encode( $result_array );

	ob_end_flush();

	wp_die();
}
add_action( 'wp_ajax_psupsellmaster_admin_ajax_get_upsells', 'psupsellmaster_admin_ajax_get_upsells' );

/**
 * Get the base products data.
 */
function psupsellmaster_admin_ajax_get_base_products() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the offset.
	$offset = isset( $_POST['start'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['start'] ) ), FILTER_VALIDATE_INT ) : false;
	$offset = false !== $offset ? $offset : 0;

	// Set the limit.
	$limit = isset( $_POST['length'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['length'] ) ), FILTER_VALIDATE_INT ) : false;
	$limit = false !== $limit ? $limit : 10;

	// Set the search.
	$search = isset( $_POST['search'] ) && isset( $_POST['search']['value'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['search']['value'] ) ) ) : '';

	// Set the filters.
	$filters = isset( $_GET['f'] ) ? json_decode( stripslashes( sanitize_text_field( wp_unslash( $_GET['f'] ) ) ), true ) : array();

	// Set the order column.
	$order_column = isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['column'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['order'][0]['column'] ) ), FILTER_VALIDATE_INT ) : false;
	$order_column = false !== $order_column ? $order_column : 0;

	// Set the order direction.
	$order_direction = isset( $_POST['order'] ) && isset( $_POST['order'][0] ) && isset( $_POST['order'][0]['dir'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['order'][0]['dir'] ) ) ) : '';
	$order_direction = in_array( $order_direction, array( 'ASC', 'DESC' ), true ) ? $order_direction : 'DESC';

	// Set the draw.
	$draw = isset( $_POST['draw'] ) ? filter_var( sanitize_text_field( wp_unslash( $_POST['draw'] ) ), FILTER_VALIDATE_INT ) : false;
	$draw = false !== $draw ? ( $draw + 1 ) : time();

	// Set the preferences.
	$preferences = array(
		'preferred_products'  => __( 'Preferred Products', 'psupsellmaster' ),
		'excluded_products'   => __( 'Excluded Products', 'psupsellmaster' ),
		'excluded_taxonomies' => array(),
	);

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects' );

	// Loop through the product taxonomies.
	foreach ( $product_taxonomies as $product_taxonomy ) {

		// Check if the taxonomy name is empty.
		if ( empty( $product_taxonomy->name ) ) {
			continue;
		}

		// Check if the taxonomy label is empty.
		if ( empty( $product_taxonomy->label ) ) {
			continue;
		}

		// Add the taxonomy to the list.
		$preferences['excluded_taxonomies'][ $product_taxonomy->name ] = $product_taxonomy->label;
	}

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( DISTINCT `psupsellmaster_scores`.`base_product_id` )
		FROM
			%i AS `psupsellmaster_scores`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' )
	);

	// Get the total items.
	$total_items = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql where.
	$sql_where = '';

	// Set the sql having.
	$sql_having = '';

	// Check if the search is not empty.
	if ( ! empty( $search ) ) {
		// Check if the search is numeric.
		if ( is_numeric( $search ) ) {
			// Set the sql where.
			$sql_where .= PsUpsellMaster_Database::prepare( ' AND `psupsellmaster_scores`.`base_product_id` = %d ', $search );
		} else {
			// Set the sql where.
			$sql_where .= PsUpsellMaster_Database::prepare(
				'
				AND
					EXISTS (
						SELECT
							1
						FROM
							%i AS `posts`
						WHERE
							1 = 1
						AND
							`posts`.`ID` = `psupsellmaster_scores`.`base_product_id`
						AND
							`posts`.`post_title` LIKE %s
					)
				',
				PsUpsellMaster_Database::get_table_name( 'posts' ),
				"%{$search}%"
			);
		}
	}

	// Get the product category taxonomy.
	$product_category_taxonomy = psupsellmaster_get_product_category_taxonomy();

	// Get the product tag taxonomy.
	$product_tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

	// Set the taxonomy filters.
	$taxonomy_filters = array();

	// Check if the filters for the custom taxonomies is not empty.
	if ( ! empty( $filters['custom_taxonomies'] ) ) {
		// Set the taxonomy filters.
		$taxonomy_filters = $filters['custom_taxonomies'];
	}

	// Check if the filters for the categories is not empty.
	if ( ! empty( $filters['cat'] ) ) {
		// Set the taxonomy filters.
		$taxonomy_filters[ $product_category_taxonomy ] = $filters['cat'];
	}

	// Check if the filters for the tags is not empty.
	if ( ! empty( $filters['tag'] ) ) {
		// Set the taxonomy filters.
		$taxonomy_filters[ $product_tag_taxonomy ] = $filters['tag'];
	}

	// Check if the taxonomy filters is not empty.
	if ( ! empty( $taxonomy_filters ) ) {
		// Loop through the taxonomy filters.
		foreach ( $taxonomy_filters as $taxonomy => $terms ) {
			// Check if the terms is empty.
			if ( empty( $terms ) ) {
				// Continue the loop.
				continue;
			}

			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $terms ), '%d' ) );

			// Set the sql terms.
			$sql_terms = PsUpsellMaster_Database::prepare( "`terms`.`term_id` IN ( {$placeholders} )", $terms );

			// Set the sql where.
			$sql_where .= PsUpsellMaster_Database::prepare(
				"
				AND
					EXISTS (
						SELECT
							1
						FROM
							%i AS `term_relationships`
						INNER JOIN
							%i AS `term_taxonomy`
						ON
							`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
						INNER JOIN
							%i AS `terms`
						ON
							`terms`.`term_id` = `term_taxonomy`.`term_id`
						WHERE
							1 = 1
						AND
							`term_relationships`.`object_id` = `psupsellmaster_scores`.`base_product_id`
						AND
							`term_taxonomy`.`taxonomy` = %s
						AND
							{$sql_terms}
					)
				",
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				PsUpsellMaster_Database::get_table_name( 'terms' ),
				$taxonomy
			);
		}
	}

	// Check if the date from filter is not empty.
	if ( ! empty( $filters['dtf'] ) ) {
		// Set the sql having.
		$sql_having .= PsUpsellMaster_Database::prepare(
			' AND MIN( `psupsellmaster_scores`.`updated_at` ) >= %s',
			$filters['dtf']
		);
	}

	// Check if the date to filter is not empty.
	if ( ! empty( $filters['dtt'] ) ) {
		// Set the sql having.
		$sql_having .= PsUpsellMaster_Database::prepare(
			' AND MAX( `psupsellmaster_scores`.`updated_at` ) <= %s',
			$filters['dtt']
		);
	}

	// Set the order by column.
	$order_by_column = '`base_products`.`post_title`';

	// Check the order column.
	if ( 2 === $order_column ) {
		// Set the order by column.
		$order_by_column = PsUpsellMaster_Database::prepare(
			'
			(
				SELECT
					`productmeta`.`meta_value`
				FROM
					%i AS `productmeta`
				WHERE
					1 = 1
				AND
					`productmeta`.`post_id` = `psupsellmaster_scores`.`base_product_id`
				AND
					`productmeta`.`meta_key` = %s
			)
			',
			PsUpsellMaster_Database::get_table_name( 'postmeta' ),
			'_psupsellmaster_scores_disabled'
		);
	}

	// Set the sql order by.
	$sql_order_by = "ORDER BY {$order_by_column} {$order_direction}";

	// Set the sql base.
	$sql_base = PsUpsellMaster_Database::prepare(
		"
		FROM
			%i AS `psupsellmaster_scores`
		INNER JOIN
			%i AS `base_products`
		ON
			`psupsellmaster_scores`.`base_product_id` = `base_products`.`ID`
		WHERE
			1 = 1
		{$sql_where}
		GROUP BY
			`psupsellmaster_scores`.`base_product_id`
		HAVING
			1 = 1
		{$sql_having}
		",
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ),
		PsUpsellMaster_Database::get_table_name( 'posts' )
	);

	// Set the sql query.
	$sql_query = (
		"
		SELECT
			COUNT( * )
		FROM (
			SELECT
				`psupsellmaster_scores`.`base_product_id`
			{$sql_base}
		) AS `items`
		"
	);

	// Get the filtered items.
	$filtered_items = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql limit.
	$sql_limit = -1 !== $limit ? PsUpsellMaster_Database::prepare( 'LIMIT %d, %d', $offset, $limit ) : '';

	// Set the sql query.
	$sql_query = (
		"
		SELECT
			`psupsellmaster_scores`.`base_product_id`,
			`base_products`.`post_title` AS `base_product_title`,
			MIN( `psupsellmaster_scores`.`updated_at` ) AS `min_updated_at`
		{$sql_base}
		{$sql_order_by}
		{$sql_limit}
		"
	);

	// Set the output.
	$output = array();

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the results.
	foreach ( $results as $result ) {
		// Get the base product id.
		$base_product_id = isset( $result->base_product_id ) ? filter_var( $result->base_product_id, FILTER_VALIDATE_INT ) : false;

		// Check if the base product id is false.
		if ( false === $base_product_id ) {
			// Continue the loop.
			continue;
		}

		// Get the is enabled.
		$is_enabled = ! filter_var( get_post_meta( $base_product_id, '_psupsellmaster_scores_disabled', true ), FILTER_VALIDATE_BOOLEAN );

		// Get the base product title.
		$base_product_title = isset( $result->base_product_title ) ? $result->base_product_title : '';

		// Get the min updated at.
		$min_updated_at = isset( $result->min_updated_at ) ? $result->min_updated_at : '';

		// Set the html row.
		$html_row = array();

		// Set the html column.
		$html_column = '<input type="checkbox" class="psupsellmaster_products_select_product" data-id="' . $base_product_id . '"/>';

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Set the edit url.
		$edit_url = get_edit_post_link( $base_product_id );

		// Check if the edit url is empty.
		if ( empty( $edit_url ) ) {
			// Set the edit url.
			$edit_url = '#';
		}

		// Set the view url.
		$view_url = get_permalink( $base_product_id );

		// Allow developers to filter this.
		$additional_data = apply_filters( 'psupsellmaster_admin_products_column_after', '', 'product_title', $base_product_id );

		// Set the html column.
		$html_column =
			'<div class="psupsellmaster_upsells_base_product">' .
				'<div class="psupsellmaster_upsells_edit_view_container">' .
					'<div class="psupsellmaster_upsells_title"><a class="psupsellmaster-link" href="' . esc_url( $view_url ) . '" target="_blank">' . esc_html( $base_product_title ) . '</a>' . $additional_data . '</div>' .
					'<div class="psupsellmaster_upsells_edit_view"><a class="psupsellmaster-link" href="' . esc_url( $view_url ) . '" target="_blank">' . __( 'View', 'psupsellmaster' ) . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;' .
					'<a class="psupsellmaster-link" href="' . esc_url( $edit_url ) . '" target="_blank">' . __( 'Edit', 'psupsellmaster' ) . '</a></div>' .
				'</div>' .
			'</div>';

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Set the html column.
		$html_column = '<a class="psupsellmaster-link psupsellmaster_enable" href="#" data-id="' . $base_product_id . '" data-status="disable"><i class="dashicons dashicons-yes-alt"></i></a>';

		if ( ! $is_enabled ) {
			// Set the html column.
			$html_column = '<a class="psupsellmaster-link psupsellmaster_enable" href="#" data-id="' . $base_product_id . '" data-status="enable"><i class="dashicons dashicons-dismiss"></i></a>';
		}

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Set the html column.
		$html_column = __( 'Not Available', 'psupsellmaster' );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			'
			SELECT
				`psupsellmaster_scores`.`upsell_product_id`
			FROM
				%i AS `psupsellmaster_scores`
			WHERE
				1 = 1
			AND
				`psupsellmaster_scores`.`base_product_id` = %d
			GROUP BY
				`psupsellmaster_scores`.`upsell_product_id`
			ORDER BY
				SUM( `psupsellmaster_scores`.`score` ) DESC
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ),
			$base_product_id
		);

		// Get the upsells.
		$upsells = PsUpsellMaster_Database::get_col( $sql_query );

		// Check if the upsells is not empty.
		if ( ! empty( $upsells ) ) {
			// Set the html column.
			$html_column = '<ul class="psupsellmaster-product-list">';

			// Loop through the upsells.
			foreach ( $upsells as $upsell_id ) {
				// Get the upsell product id.
				$upsell_product_id = filter_var( $upsell_id, FILTER_VALIDATE_INT );

				// Check if the upsell product id is false.
				if ( false === $upsell_product_id ) {
					// Continue the loop.
					continue;
				}

				// Get the upsell product title.
				$upsell_product_title = get_the_title( $upsell_product_id );

				// Get the upsell prices.
				$upsell_prices = psupsellmaster_get_product_price_range( $upsell_product_id, false );

				// Get the upsell price range.
				$upsell_price_range = psupsellmaster_get_price_range_text( $upsell_product_id, $upsell_prices );

				// Set the html column.
				$html_column .= '<li><a class="psupsellmaster-link psupsellmaster-open-scores-details" data-base-product-id="' . esc_attr( $base_product_id ) . '" data-upsell-product-id="' . esc_attr( $upsell_product_id ) . '" href="#">' . $upsell_product_title . '</a>&nbsp;' . $upsell_price_range . '</li>';
			}

			// Set the html column.
			$html_column .= '</ul>';
		}

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Set the html column.
		$html_column = '';

		// Loop through the preferences.
		foreach ( $preferences as $preference_key => $data ) {
			// Check if the field is.
			if ( 'excluded_taxonomies' === $preference_key ) {
				// Get the excluded taxonomies.
				$excluded_taxonomies = is_array( $data ) ? $data : array();

				// Loop through the taxonomies.
				foreach ( $excluded_taxonomies as $taxonomy_name => $taxonomy_label ) {
					// Set the meta key.
					$meta_key = "psupsellmaster_excluded_tax_{$taxonomy_name}";

					// Get the excluded terms.
					$excluded_terms = get_post_meta( $base_product_id, $meta_key );
					$excluded_terms = ! empty( $excluded_terms ) ? $excluded_terms : array();

					// Check if the excluded terms is not empty.
					if ( ! empty( $excluded_terms ) ) {
						// Set the html column.
						$html_column .= '<h3>' . __( 'Excluded', 'psupsellmaster' ) . ' ' . $taxonomy_label . '</h3>';

						// Loop through the excluded terms.
						foreach ( $excluded_terms as $excluded_term_id ) {
							// Get the edit term link.
							$edit_term_link = get_edit_term_link( $excluded_term_id );

							// Get the term name.
							$term_name = get_term( $excluded_term_id )->name;

							// Set the html column.
							$html_column .= '<a class="psupsellmaster-link" href="' . $edit_term_link . '">' . esc_html( $term_name ) . '</a><br/>';
						}
					}
				}
			} else {
				// Set the meta key.
				$meta_key = "psupsellmaster_{$preference_key}";

				// Get the preference ids.
				$preference_ids = get_post_meta( $base_product_id, $meta_key );
				$preference_ids = ! empty( $preference_ids ) ? $preference_ids : array();

				// Check if the preference ids is not empty.
				if ( ! empty( $preference_ids ) ) {
					// Set the html column.
					$html_column .= '<h3>' . $data . '</h3>';

					// Loop through the preference ids.
					foreach ( $preference_ids as $preference_id ) {
						// Get the edit post link.
						$edit_post_link = get_edit_post_link( $preference_id );

						// Get the preference title.
						$preference_title = get_the_title( $preference_id );

						// Get the preference prices.
						$preference_prices = psupsellmaster_get_product_price_range( $preference_id, false );

						// Get the preference range.
						$preference_range = psupsellmaster_get_price_range_text( $preference_id, $preference_prices );

						// Set the html column.
						$html_column .= '<a class="psupsellmaster-link" href="' . esc_url( $edit_post_link ) . '">' . esc_html( $preference_title ) . '</a>&nbsp;' . $preference_range . '<br/>';
					}
				}
			}
		}

		// Check if the html column is empty.
		if ( empty( $html_column ) ) {
			// Set the html column.
			$html_column = __( 'Not Available', 'psupsellmaster' );
		}

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Set the html column.
		$html_column = '';

		// Check if the min updated at is not empty.
		if ( ! empty( $min_updated_at ) ) {
			// Set the html column.
			$html_column = gmdate( 'Y/m/d', strtotime( $min_updated_at ) );
		}

		// Add the column to the row.
		array_push( $html_row, $html_column );

		// Add the row to the output.
		array_push( $output, $html_row );
	}

	// Get the background process last run date.
	$bp_last_run = get_option( 'psupsellmaster_bp_scores_last_run' );
	$bp_last_run = ! empty( $bp_last_run ) ? date_i18n( get_option( 'date_format' ), $bp_last_run ) : false;
	$bp_last_run = ! empty( $bp_last_run ) ? sprintf( '%s: %s', __( 'Last Run Date', 'psupsellmaster' ), $bp_last_run ) : __( 'Unknown', 'psupsellmaster' );

	// Set the response.
	$response = array();

	// Set the response: datatable.
	$response['datatable'] = array(
		'data'     => $output,
		'draw'     => $draw,
		'filtered' => $filtered_items,
		'total'    => $total_items,
	);

	// Set the response: dates.
	$response['dates'] = array(
		'bp_last_run' => $bp_last_run,
	);

	// Return the response.
	wp_send_json( $response );
}
add_action( 'wp_ajax_psupsellmaster_admin_ajax_get_base_products', 'psupsellmaster_admin_ajax_get_base_products' );

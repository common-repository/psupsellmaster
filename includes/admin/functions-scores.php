<?php
/**
 * Admin - Functions - Scores.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the scores of upsell products for a base product.
 */
function psupsellmaster_ajax_get_upsell_product_scores() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Get the base product id.
	$base_product_id = isset( $_GET['base_product_id'] ) ? filter_var( sanitize_text_field( wp_unslash( $_GET['base_product_id'] ) ), FILTER_VALIDATE_INT ) : false;

	// Get the upsell product id.
	$upsell_product_id = isset( $_GET['upsell_product_id'] ) ? filter_var( sanitize_text_field( wp_unslash( $_GET['upsell_product_id'] ) ), FILTER_VALIDATE_INT ) : false;

	// Check if either the base product id or the product id is empty.
	if ( empty( $base_product_id ) || empty( $upsell_product_id ) ) {
		wp_die();
	}

	$category_taxonomy = psupsellmaster_get_product_category_taxonomy();
	$tag_taxonomy      = psupsellmaster_get_product_tag_taxonomy();
	$terms_taxonomy    = array(
		$category_taxonomy,
		$tag_taxonomy,
	);

	$all_terms                     = wp_get_post_terms( $upsell_product_id, $terms_taxonomy );
	$download_tag_terms_array      = array();
	$download_category_terms_array = array();
	$download_tag_terms            = '';
	$download_category_terms       = '';
	$post_author_url               = '';
	$price_str                     = '';

	if ( ! is_wp_error( $all_terms ) && ! empty( $all_terms ) ) {

		foreach ( $all_terms as $term ) {

			$edit_term_link = get_edit_term_link( $term->term_id );
			$term_link_html = '<a href="' . $edit_term_link . '" target="_blank" title="' . __( 'Edit', 'psupsellmaster' ) . '">' . $term->name . '</a>';

			if ( $term->taxonomy === $category_taxonomy ) {
				$download_category_terms_array[] = $term_link_html;
			} elseif ( $term->taxonomy === $terms_taxonomy ) {
				$download_tag_terms_array[] = $term_link_html;
			}
		}

		if ( ! empty( $download_tag_terms_array ) ) {
			$download_tag_terms = implode( ', ', $download_tag_terms_array );
		}

		if ( ! empty( $download_category_terms_array ) ) {
			$download_category_terms = implode( ', ', $download_category_terms_array );
		}
	}

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {

		$product                  = wc_get_product( $upsell_product_id );
		$sales_stats              = psupsellmaster_get_wc_sales_stats( $upsell_product_id );
		$download_sales           = $sales_stats['qty'];
		$download_earnings        = trim( wp_strip_all_tags( wc_price( $sales_stats['total'] ) ) );
		$download_type            = ( $product->is_type( 'simple' ) ) ? __( 'Simple', 'psupsellmaster' ) : __( 'Variable', 'psupsellmaster' );
		$post                     = get_post( $upsell_product_id );
		$post_author_id           = (int) $post->post_author;
		$post_author_display_name = ( $post_author_id > 0 ) ? get_the_author_meta( 'display_name', $post_author_id ) : __( 'Unknown', 'psupsellmaster' );
		$post_author_url          = get_edit_user_link( $post_author_id );
		$post_author_url          = ( '' !== $post_author_url ) ? '<a href="' . esc_url( $post_author_url ) . '" title="Edit Vendor" target="_blank">' . esc_html( $post_author_display_name ) . '</a>' : '';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {

		$download          = new EDD_Download( $upsell_product_id );
		$download_sales    = $download->get_sales();
		$download_earnings = edd_currency_filter( edd_format_amount( $download->get_earnings() ) );
		$download_type     = $download->get_type();
		$post_author_id    = $download->post_author;

		if ( $post_author_id <= 0 ) {
			$post_author_id = (int) $download->post_author;
		}

		$post_author_display_name = get_the_author_meta( 'display_name', $post_author_id );

		if ( ! class_exists( 'EDD_Front_End_Submissions' ) ) {
			$post_author_url = get_edit_user_link( $post_author_id );
		} else {
			$vendor_obj = new FES_DB_Vendors();
			$vendor     = $vendor_obj->get_vendor_by( 'user_id', $post_author_id );

			if ( ! empty( $vendor ) && isset( $vendor->id ) ) {
				$post_author_url = add_query_arg(
					array(
						'page' => 'fes-vendors',
						'view' => 'overview',
						'id'   => $vendor->id,
					),
					get_admin_url( null, 'admin.php' )
				);
			}
		}

		$post_author_url = ( '' !== $post_author_url ) ? '<a href="' . esc_url( $post_author_url ) . '" title="Edit Vendor" target="_blank">' . esc_html( $post_author_display_name ) . '</a>' : '';
	}

	$price_str  = psupsellmaster_get_price_range_text( $upsell_product_id, psupsellmaster_get_product_price_range( $upsell_product_id, false ) );
	$post_url   = get_permalink( $upsell_product_id );
	$post_title = get_the_title( $upsell_product_id );
	?>

	<a href="<?php echo esc_url( get_edit_post_link( $upsell_product_id ) ); ?>" class="psupsellmaster-edit-tooltip-upsell" title="<?php esc_attr_e( 'Edit Download', 'psupsellmaster' ); ?>" target="_blank"><i class="dashicons dashicons-edit"></i></a>
	<a href="#" class="psupsellmaster-close-scores-details"><i class="dashicons dashicons-dismiss" title="Close Tooltip"></i></a>
	<ul class="psupsellmaster_upsell_details">
		<li>
			<p><span><strong><?php esc_html_e( 'Upsell', 'psupsellmaster' ); ?></strong></span><a href="<?php echo esc_url( $post_url ); ?>" target="_blank" title="<?php esc_attr_e( 'View Download', 'psupsellmaster' ); ?>"><?php echo esc_html( $post_title ); ?></a>
		</li>
		<?php if ( '' !== $download_category_terms ) : ?>
			<li>
				<p><span><strong><?php esc_html_e( 'Categories', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $download_category_terms ); ?></p>
			</li>
		<?php endif; ?>
		<?php if ( '' !== $download_tag_terms ) : ?>
			<li>
				<p><span><strong><?php esc_html_e( 'Tags', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $download_tag_terms ); ?></p>
			</li>
		<?php endif; ?>
		<?php if ( $post_author_id > 0 ) : ?>
			<li>
				<p><span><strong><?php esc_html_e( 'Vendor Name', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $post_author_url ); ?></p>
			</li>
		<?php endif; ?>
		<li>
			<p><span><strong><?php esc_html_e( 'Lifetime Sales', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $download_sales . ' (' . $download_earnings . ')' ); ?></p>
		</li>
		<li>
			<p><span><strong><?php esc_html_e( 'Product Type', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $download_type ); ?></p>
		</li>
		<?php if ( '' !== $price_str ) : ?>
			<li>
				<p><span><strong><?php esc_html_e( 'Price', 'psupsellmaster' ); ?></strong></span><?php echo wp_kses_post( $price_str ); ?></p>
			</li>
		<?php endif; ?>
		<?php
		// Get the product category taxonomy.
		$product_category_taxonomy = psupsellmaster_get_product_category_taxonomy();

		// Get the product tag taxonomy.
		$product_tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

		// Set the scores.
		$scores = array();

		// Set the stored scores.
		$stored_scores = array();

		// Set the select args.
		$select_args = array(
			'base_product_id'   => $base_product_id,
			'upsell_product_id' => $upsell_product_id,
		);

		// Get the stored records.
		$stored_records = psupsellmaster_db_scores_select( $select_args );

		// Loop through the stored scores.
		foreach ( $stored_records as $stored_record ) {
			// Get the criteria.
			$criteria = isset( $stored_record->criteria ) ? $stored_record->criteria : '';

			// Get the score.
			$score = isset( $stored_record->score ) ? filter_var( $stored_record->score, FILTER_VALIDATE_FLOAT ) : false;
			$score = false !== $score ? $score : 0;

			// Set the scores.
			$stored_scores[ $criteria ] = $score;
		}

		// Get the stored priorities.
		$stored_priorities = psupsellmaster_get_stored_priorities();

		// Set the score format.
		$score_format = "%'028.11f";

		// Add the preferred to the list.
		array_push( $stored_priorities, 'preferred' );

		// Loop through the stored priorities.
		foreach ( $stored_priorities as $priority_key ) {
			// Check if the priority key is empty.
			if ( empty( $priority_key ) ) {
				continue;
			}

			// Get the stored key.
			$stored_key = $priority_key;

			// Check if the stored key is equal to the product category taxonomy.
			if ( 'category' === $stored_key ) {
				// Set the stored key.
				$stored_key = "taxonomy_{$product_category_taxonomy}";
			}

			// Check if the stored key is equal to the product tag taxonomy.
			if ( 'tag' === $stored_key ) {
				// Set the stored key.
				$stored_key = "taxonomy_{$product_tag_taxonomy}";
			}

			// Set the score number.
			$score_number = 0;

			// Check if the stored key does exist for the stored scores.
			if ( isset( $stored_scores[ $stored_key ] ) ) {
				// Get the score number.
				$score_number = filter_var( $stored_scores[ $stored_key ], FILTER_VALIDATE_FLOAT );
				$score_number = ! empty( $score_number ) ? $score_number : 0;
			}

			// Check if the priority key is preferred and if its score number is empty.
			if ( 'preferred' === $priority_key && empty( $score_number ) ) {
				continue;
			}

			// Set the score item.
			$score_item = array(
				'key'    => $priority_key,
				'number' => $score_number,
			);

			// Add the score item to the scores list.
			array_push( $scores, $score_item );
		}

		// Sort the scores.
		uasort( $scores, 'psupsellmaster_scores_uasort_desc' );

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			'
			SELECT
				SUM( `psupsellmaster_scores`.`score` ) AS `total`
			FROM
				%i AS `psupsellmaster_scores`
			WHERE
				1 = 1
			AND
				`psupsellmaster_scores`.`base_product_id` = %d
			AND
				`psupsellmaster_scores`.`upsell_product_id` = %d
			',
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ),
			$base_product_id,
			$upsell_product_id
		);

		// Get the score total.
		$score_total = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_FLOAT );
		$score_total = ! empty( $score_total ) ? $score_total : 0;
		?>
		<li class="psupsellmaster-scores-criteria-details">
			<p><span><strong><?php esc_html_e( 'Total Score', 'psupsellmaster' ); ?></strong></span><?php echo esc_html( sprintf( $score_format, esc_attr( $score_total ) ) ); ?></p>
			<?php foreach ( $scores as $score ) : ?>
				<p><span><?php echo esc_html( psupsellmaster_get_priority_label( $score['key'] ) ); ?></span><?php echo esc_html( sprintf( $score_format, esc_attr( $score['number'] ) ) ); ?></p>
			<?php endforeach; ?>
		</li>
	</ul>

	<?php
	wp_die();
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_upsell_product_scores', 'psupsellmaster_ajax_get_upsell_product_scores' );

/**
 * Sort the scores.
 *
 * @param  array $score1 The first score.
 * @param  array $score2 The second score.
 * @return int The sort order.
 */
function psupsellmaster_scores_uasort_desc( $score1, $score2 ) {
	return $score2['number'] - $score1['number'];
}

/**
 * Sets the status of the scores for the products.
 */
function psupsellmaster_scores_set_status() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Check if the user has the required permissions.
	if ( ! current_user_can( 'manage_options' ) ) {
		// Send the error.
		wp_send_json_error();
	}

	// Check if the products is not set or is not an array.
	if ( ! isset( $_POST['products'] ) || ! is_array( $_POST['products'] ) ) {
		// Send the error.
		wp_send_json_error();
	}

	// Set the products.
	$products = array_map( 'sanitize_text_field', wp_unslash( $_POST['products'] ) );

	// Check if the products is empty.
	if ( empty( $products ) ) {
		// Send the error.
		wp_send_json_error();
	}

	// Get the status.
	$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : false;

	// Check if the products is empty.
	if ( ! in_array( $status, array( 'enabled', 'disabled' ), true ) ) {
		// Send the error.
		wp_send_json_error();
	}

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Loop through the products.
	foreach ( $products as $product ) {
		// Get the product id.
		$product_id = filter_var( $product, FILTER_VALIDATE_INT );

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Get the post type.
		$post_type = get_post_type( $product_id );

		// Check if the post type does not match the product post type.
		if ( $product_post_type !== $post_type ) {
			continue;
		}

		// Check if the status is disabled.
		if ( 'disabled' === $status ) {
			// Set the product meta.
			update_post_meta( $product_id, '_psupsellmaster_scores_disabled', true );

			// Otherwise...
		} else {
			// Delete the product meta.
			delete_post_meta( $product_id, '_psupsellmaster_scores_disabled' );
		}
	}

	// Send the response.
	wp_send_json( array( 'status' => 'OK' ) );
}
add_action( 'wp_ajax_psupsellmaster_scores_set_status', 'psupsellmaster_scores_set_status' );

/**
 * Renders the product scores settings in the product list table.
 *
 * @param string $column_name The column name.
 * @param int    $post_id The post id.
 */
function psupsellmaster_scores_render_columns( $column_name, $post_id ) {
	// Check if the post id is empty.
	if ( empty( $post_id ) ) {
		return false;
	}

	// Set the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type does not match the product post type.
	if ( $product_post_type !== $post_type ) {
		return false;
	}

	// Get the category taxonomy.
	$category_taxonomy = psupsellmaster_get_product_category_taxonomy();

	// Get the tag taxonomy.
	$tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

	// Set the is enabled.
	$is_enabled = ! filter_var( get_post_meta( $post_id, '_psupsellmaster_scores_disabled', true ), FILTER_VALIDATE_BOOLEAN );

	// Set the preferred products.
	$preferred_products = get_post_meta( $post_id, 'psupsellmaster_preferred_products' );
	$preferred_products = ! empty( $preferred_products ) ? $preferred_products : array();

	// Make sure there is at least one item in the list.
	array_push( $preferred_products, -1 );

	// Set the excluded products.
	$excluded_products = get_post_meta( $post_id, 'psupsellmaster_excluded_products' );
	$excluded_products = ! empty( $excluded_products ) ? $excluded_products : array();

	// Make sure there is at least one item in the list.
	array_push( $excluded_products, -1 );

	// Set the excluded categories.
	$excluded_categories = get_post_meta( $post_id, "psupsellmaster_excluded_tax_{$category_taxonomy}" );
	$excluded_categories = ! empty( $excluded_categories ) ? $excluded_categories : array();

	// Make sure there is at least one item in the list.
	array_push( $excluded_categories, -1 );

	// Set the excluded tags.
	$excluded_tags = get_post_meta( $post_id, "psupsellmaster_excluded_tax_{$tag_taxonomy}" );
	$excluded_tags = ! empty( $excluded_tags ) ? $excluded_tags : array();

	// Make sure there is at least one item in the list.
	array_push( $excluded_tags, -1 );

	// Set the taxonomy metas.
	$taxonomy_metas = array();

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies( 'names', false );

	// Loop through the product taxonomies.
	foreach ( $product_taxonomies as $product_taxonomy ) {
		// Check if the product taxonomy is empty.
		if ( empty( $product_taxonomy ) ) {
			continue;
		}

		// Set the meta key.
		$meta_key = "psupsellmaster_excluded_tax_{$product_taxonomy}";

		// Get the excluded terms.
		$excluded_terms = get_post_meta( $post_id, $meta_key );
		$excluded_terms = ! empty( $excluded_terms ) ? $excluded_terms : array();

		// Make sure there is at least one item in the list.
		array_push( $excluded_terms, -1 );

		// Set the taxonomy meta.
		$taxonomy_metas[ $product_taxonomy ] = $excluded_terms;
	}
	?>
	<div class="psupsellmaster-hidden-fields" style="display: none;">
		<input class="psupsellmaster-hidden-field psupsellmaster-hidden-field-enable-upsell" data-target-field=".psupsellmaster-field-toggle-scores" type="hidden" value="<?php echo esc_attr( $is_enabled ); ?>">
		<select class="psupsellmaster-hidden-field psupsellmaster-hidden-field-preferred-products" data-target-field=".psupsellmaster-field-preferred-products">
			<?php
			// Get the options.
			$options = psupsellmaster_get_product_label_value_pairs(
				array( 'post__in' => $preferred_products )
			)['items'];
			?>
			<?php foreach ( $options as $option ) : ?>
				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<select class="psupsellmaster-hidden-field psupsellmaster-hidden-field-excluded-products" data-target-field=".psupsellmaster-field-excluded-products">
			<?php
			// Get the options.
			$options = psupsellmaster_get_product_label_value_pairs(
				array( 'post__in' => $excluded_products )
			)['items'];
			?>
			<?php foreach ( $options as $option ) : ?>
				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<select class="psupsellmaster-hidden-field psupsellmaster-hidden-field-excluded-categories" data-target-field=".psupsellmaster-field-excluded-categories">
			<?php
			// Get the options.
			$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
				array(
					'include'  => $excluded_categories,
					'taxonomy' => $category_taxonomy,
				)
			)['items'];
			?>
			<?php foreach ( $options as $option ) : ?>
				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<select class="psupsellmaster-hidden-field psupsellmaster-hidden-field-excluded-tags" data-target-field=".psupsellmaster-field-excluded-tags">
			<?php
			// Get the options.
			$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
				array(
					'include'  => $excluded_tags,
					'taxonomy' => $tag_taxonomy,
				)
			)['items'];
			?>
			<?php foreach ( $options as $option ) : ?>
				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php foreach ( $taxonomy_metas as $taxonomy_name => $excluded_terms ) : ?>
			<?php $taxonomy_key_input = str_replace( '_', '-', $taxonomy_name ); ?>
			<select class="psupsellmaster-hidden-field psupsellmaster-hidden-field-excluded-<?php echo esc_attr( $taxonomy_key_input ); ?>" data-target-field=".psupsellmaster-field-excluded-<?php echo esc_attr( $taxonomy_key_input ); ?>">
				<?php
				// Get the options.
				$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
					array(
						'include'  => $excluded_terms,
						'taxonomy' => $taxonomy_name,
					)
				)['items'];
				?>
				<?php foreach ( $options as $option ) : ?>
					<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php endforeach; ?>
	</div>
	<?php
}
add_action( 'manage_posts_custom_column', 'psupsellmaster_scores_render_columns', 10, 2 );

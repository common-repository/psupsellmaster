<?php
/**
 * Admin - Functions - Edit Product.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the product meta box for upsells.
 *
 * @param WP_Post $post The post object.
 */
function psupsellmaster_render_product_meta_box_upsells( $post ) {
	// Get the product id.
	$product_id = isset( $post->ID ) ? $post->ID : 0;

	// Set the scores is enabled.
	$scores_is_disabled = ! empty( get_post_meta( $product_id, '_psupsellmaster_scores_disabled', true ) );

	// Set the display.
	$display = $scores_is_disabled ? 'none' : 'block';
	?>
	<div class="psupsellmaster-settings-upsells">
		<div class="psupsellmaster-fields-container">
			<div class="psupsellmaster_metabox_row" style="margin: 1em 0">
				<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left">
					<strong><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></strong>
				</div>
				<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
					<label class="psupsellmaster-checkbox-label" for="enable_upsell">
						<input <?php checked( ! $scores_is_disabled ); ?> class="psupsellmaster-field-toggle-scores psupsellmaster-trigger-slide-toggle" data-target-slide-toggle=".psupsellmaster-data-fields-container" id="enable_upsell" name="enable_upsell" type="checkbox" value="1" />
						<input name="psupsellmaster_nonce_upsells" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>" />
						<input class="psupsellmaster-scores-status" name="psupsellmaster_scores_status" type="hidden" value="<?php echo esc_attr( $scores_is_disabled ? 'disabled' : 'enabled' ); ?>" />
						<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
					</label>
				</div>
			</div>
			<div class="psupsellmaster-data-fields-container" style="display: <?php echo esc_attr( $display ); ?>">
				<?php do_action( 'psupsellmaster_product_meta_box_upsells_fields', $post ); ?>
				<?php
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
					$product_id
				);

				// Get the upsells.
				$upsells = PsUpsellMaster_Database::get_col( $sql_query );
				?>
				<?php if ( ! empty( $upsells ) ) : ?>
					<div class="psupsellmaster_metabox_row">
						<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left">
							<strong><?php esc_html_e( 'Upsells', 'psupsellmaster' ); ?></strong>
						</div>
						<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
							<ul class="psupsellmaster-list">
								<?php foreach ( $upsells as $upsell_id ) : ?>
									<?php
									$prices = psupsellmaster_get_product_price_range( $upsell_id, false );

									$price_text = psupsellmaster_get_price_range_text( $upsell_id, $prices );
									?>
									<li class="psupsellmaster-item">
										<a class="psupsellmaster-link psupsellmaster-open-scores-details" data-base-product-id="<?php echo esc_attr( $product_id ); ?>" data-upsell-product-id="<?php echo esc_attr( $upsell_id ); ?>" href="#">
											<?php echo esc_html( get_the_title( $upsell_id ) ); ?>
										</a>
										<span><?php echo esc_html( $price_text ); ?></span>
									</li>
								<?php endforeach; ?>
								<li class="psupsellmaster-item">
									<?php
									// Set the last run.
									$last_run = get_option( 'psupsellmaster_bp_scores_last_run' );
									$last_run = ! empty( $last_run ) ? date_i18n( get_option( 'date_format' ), $last_run ) : __( 'Unknown', 'psupsellmaster' );
									?>
									<span><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Last updated on', 'psupsellmaster' ), $last_run ); ?></span>
								</li>
								<li class="psupsellmaster-item">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-settings&view=upsells' ) ); ?>" target="_blank"><?php esc_html_e( 'Change Product Selection Settings', 'psupsellmaster' ); ?></a>
								</li>
							</ul>
						</div>
					</div>
					<div class="psupsellmaster_metabox_row">
						<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left"></div>
						<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
							<ul class="psupsellmaster-list">
								<li class="psupsellmaster-item">
									<a href="<?php echo esc_url( trailingslashit( get_permalink( $product_id ) ) . '#psupsellmaster-products-product-page' ); ?>" target="_blank"><?php esc_html_e( 'View Upsells on Product Page', 'psupsellmaster' ); ?></a>
								</li>
							</ul>
						</div>
					</div>
				<?php endif; ?>
				<div class="psupsellmaster_metabox_row">
					<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left">
						<strong><?php esc_html_e( 'Current Settings', 'psupsellmaster' ); ?></strong>
					</div>
					<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
						<?php
						// Set the settings.
						$settings = array(
							'display_type' => array(
								'value' => PsUpsellMaster_Settings::get( 'product_page_display_type' ),
								'label' => __( 'Carousel', 'psupsellmaster' ),
							),
							'show_type'    => array(
								'value' => PsUpsellMaster_Settings::get( 'product_page_show_type' ),
								'label' => __( 'Upsells', 'psupsellmaster' ),
							),
						);

						// Check the settings.
						if ( 'list' === $settings['display_type']['value'] ) {
							// Set the settings.
							$settings['display_type']['label'] = __( 'List', 'psupsellmaster' );
						}

						// Check the settings.
						if ( 'visits' === $settings['show_type']['value'] ) {
							// Set the settings.
							$settings['show_type']['label'] = __( 'Recently Viewed Products', 'psupsellmaster' );
						}
						?>
						<ul class="psupsellmaster-list">
							<li class="psupsellmaster-item">
								<span><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Display Type', 'psupsellmaster' ), $settings['display_type']['label'] ); ?></span>
							</li>
							<li class="psupsellmaster-item">
								<span><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Show', 'psupsellmaster' ), $settings['show_type']['label'] ); ?></span>
							</li>
							<li class="psupsellmaster-item">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-settings&view=product-page' ) ); ?>" target="_blank"><?php esc_html_e( 'Change Product Page Settings', 'psupsellmaster' ); ?></a>
							</li>
						</ul>
					</div>
				</div>
				<div class="psupsellmaster_metabox_row">
					<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left">
						<strong><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></strong>
					</div>
					<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
						<span class="psupsellmaster-paragraph"><?php esc_html_e( 'List of active and scheduled campaigns:', 'psupsellmaster' ); ?></span>
						<?php echo psupsellmaster_campaigns_render_planned_list(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Renders the product meta box upsells fields.
 *
 * @param WP_Post $post The post object.
 */
function psupsellmaster_render_product_meta_box_upsells_fields( $post ) {
	// Get the product id.
	$product_id = isset( $post->ID ) ? $post->ID : 0;

	// Get the preferred products.
	$preferred_products = get_post_meta( $product_id, 'psupsellmaster_preferred_products' );
	$preferred_products = ! empty( $preferred_products ) ? $preferred_products : array();
	$preferred_products = array_map( 'intval', $preferred_products );

	// Make sure there is at least one item in the list.
	array_push( $preferred_products, -1 );

	// Get the options.
	$options = psupsellmaster_get_product_label_value_pairs(
		array( 'post__in' => $preferred_products )
	)['items'];
	?>
	<div class="psupsellmaster_metabox_row">
		<div class="psupsellmaster_metabox_column psupsellmaster_metabox_left">
			<strong><?php esc_html_e( 'Preferred Products', 'psupsellmaster' ); ?></strong>
		</div>
		<div class="psupsellmaster_metabox_column psupsellmaster_metabox_right">
			<div class="psupsellmaster-form-field">
				<select class="psupsellmaster-select2" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="psupsellmaster_default_upsell_preferred_products" name="psupsellmaster_preferred_products[]" multiple="multiple">
					<?php foreach ( $options as $option ) : ?>
						<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'psupsellmaster_product_meta_box_upsells_fields', 'psupsellmaster_render_product_meta_box_upsells_fields' );

/**
 * Renders the product meta box for campaigns.
 *
 * @param WP_Post $post The post object.
 */
function psupsellmaster_render_product_meta_box_campaigns( $post ) {
	?>
	<div class="psupsellmaster-form-campaigns">
		<div class="psupsellmaster-form-rows">
			<?php do_action( 'psupsellmaster_product_meta_box_campaigns_fields', $post ); ?>
		</div>
		<div class="psupsellmaster-form-hidden">
			<input name="psupsellmaster_nonce_campaigns" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>" />
			<input name="psupsellmaster_campaigns" type="hidden" value="1" />
		</div>
	</div>
	<?php
}

/**
 * Renders the product meta box campaigns fields.
 *
 * @param WP_Post $post The post object.
 */
function psupsellmaster_render_product_meta_box_campaigns_fields( $post ) {
	// Get the product id.
	$product_id = isset( $post->ID ) ? $post->ID : 0;

	// Set the taxonomy.
	$taxonomy = 'psupsellmaster_product_tag';

	// Get the taxonomy object.
	$taxonomy_object = get_taxonomy( $taxonomy );

	// Check if the taxonomy was not found.
	if ( empty( $taxonomy_object ) ) {
		return false;
	}

	// Get the taxonomy label.
	$taxonomy_label = isset( $taxonomy_object->label ) ? $taxonomy_object->label : __( 'Tags', 'psupsellmaster' );

	// Get the options.
	$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
		array(
			'object_ids' => array( $product_id ),
			'taxonomy'   => $taxonomy,
		)
	)['items'];
	?>
	<div class="psupsellmaster-form-row">
		<div class="psupsellmaster-form-col psupsellmaster-form-col-label">
			<strong><?php echo esc_html( $taxonomy_label ); ?></strong>
		</div>
		<div class="psupsellmaster-form-col psupsellmaster-form-col-input">
			<div class="psupsellmaster-form-field">
				<select class="psupsellmaster-select2" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="psupsellmaster_product_tag" multiple="multiple" name="psupsellmaster_product_tags[]">
					<?php foreach ( $options as $option ) : ?>
						<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'psupsellmaster_product_meta_box_campaigns_fields', 'psupsellmaster_render_product_meta_box_campaigns_fields' );

/**
 * Save the data from the upsells meta box.
 *
 * @param int $post_id The post id.
 */
function psupsellmaster_save_meta_box_upsells( $post_id ) {
	// Check whether the post id is empty.
	if ( empty( $post_id ) ) {
		return;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster_nonce_upsells'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce_upsells'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	// Check if the scores status is not set.
	if ( ! isset( $_POST['psupsellmaster_scores_status'] ) ) {
		return;
	}

	// Set the status meta key.
	$status_meta_key = '_psupsellmaster_scores_disabled';

	// Set the scores status.
	$scores_status = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_scores_status'] ) );

	// Check if the scores status is disabled.
	if ( 'disabled' === $scores_status ) {
		// Update the meta key.
		update_post_meta( $post_id, $status_meta_key, true );

		// Otherwise...
	} else {
		// Delete the meta key.
		delete_post_meta( $post_id, $status_meta_key );
	}

	// Set the preferred products meta key.
	$preferred_products_meta_key = 'psupsellmaster_preferred_products';

	// Get the stored preferred products.
	$stored_preferred_products = get_post_meta( $post_id, $preferred_products_meta_key );
	$stored_preferred_products = ! empty( $stored_preferred_products ) ? $stored_preferred_products : array();

	// Get the preferred products.
	$preferred_products = array();

	// Check if the preferred products is not empty and is an array.
	if ( ! empty( $_POST['psupsellmaster_preferred_products'] ) && is_array( $_POST['psupsellmaster_preferred_products'] ) ) {
		// Set the preferred products.
		$preferred_products = array_map( 'sanitize_text_field', wp_unslash( $_POST['psupsellmaster_preferred_products'] ) );
	}

	// Get the removed preferred products.
	$removed_preferred_products = array_diff( $stored_preferred_products, $preferred_products );

	// Loop through the removed preferred products.
	foreach ( $removed_preferred_products as $removed_preferred_product ) {
		// Delete the meta key.
		delete_post_meta( $post_id, $preferred_products_meta_key, $removed_preferred_product );
	}

	// Get the added preferred products.
	$added_preferred_products = array_diff( $preferred_products, $stored_preferred_products );

	// Loop through the added preferred products.
	foreach ( $added_preferred_products as $preferred_product ) {
		// Add the meta key.
		add_post_meta( $post_id, $preferred_products_meta_key, $preferred_product );
	}

	do_action( 'psupsellmaster_save_meta_box_upsells', $post_id );
}
add_action( 'save_post_product', 'psupsellmaster_save_meta_box_upsells', 10, 2 );
add_action( 'save_post_download', 'psupsellmaster_save_meta_box_upsells', 10, 2 );

/**
 * Save the data from the campaigns meta box.
 *
 * @param int $post_id The post id.
 */
function psupsellmaster_save_meta_box_campaigns( $post_id ) {
	// Check whether the post id is empty.
	if ( empty( $post_id ) ) {
		return;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster_nonce_campaigns'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce_campaigns'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	// Check if the campaigns key is not set.
	if ( ! isset( $_POST['psupsellmaster_campaigns'] ) ) {
		return;
	}

	// Set the taxonomy.
	$taxonomy = 'psupsellmaster_product_tag';

	// Set the terms.
	$terms = array();

	// Check if the product tags is not empty and is an array.
	if ( isset( $_POST['psupsellmaster_product_tags'] ) && is_array( $_POST['psupsellmaster_product_tags'] ) ) {
		// Set the terms.
		$terms = array_map( 'intval', $_POST['psupsellmaster_product_tags'] );
		$terms = array_unique( array_filter( $terms ) );
	}

	// Set the terms.
	wp_set_post_terms( $post_id, $terms, $taxonomy );
}
add_action( 'save_post_product', 'psupsellmaster_save_meta_box_campaigns', 10, 2 );
add_action( 'save_post_download', 'psupsellmaster_save_meta_box_campaigns', 10, 2 );

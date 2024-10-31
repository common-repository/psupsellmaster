<?php
/**
 * Admin - Functions - Products.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the inline edit form for the product list.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_products_render_inline_edit( $args ) {
	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type is not valid.
	if ( $product_post_type !== $args['post_type'] ) {
		return false;
	}

	// Check if the column name or the post type are not valid.
	if ( 'price' !== $args['column_name'] ) {
		return false;
	}
	?>
	<div class="psupsellmaster-quick-edit-fieldset">
		<legend class="inline-edit-legend"><?php esc_html_e( 'Upsells', 'psupsellmaster' ); ?></legend>
		<div class="psupsellmaster-quick-edit-form">
			<div class="psupsellmaster-quick-edit-full-container">
				<div class="psupsellmaster-field-container psupsellmaster-field-container-enable-upsell">
					<label class="psupsellmaster-field-label" for="psupsellmaster_fields[enable_upsell]">
						<input class="psupsellmaster-field psupsellmaster-field-toggle-scores" id="psupsellmaster_fields[enable_upsell]" name="psupsellmaster_fields[enable_upsell]" type="checkbox" value="1">
						<input class="psupsellmaster-scores-status" name="psupsellmaster_fields[scores_status]" type="hidden">
						<span><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></span>
					</label>
				</div>
			</div>
			<div class="psupsellmaster-fields-container" style="display: none;">
				<input name="psupsellmaster_nonce_<?php echo esc_attr( $args['edit_action'] ); ?>" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>">
				<input name="psupsellmaster_action" type="hidden" value="<?php echo esc_attr( 'bulk_edit' === $args['edit_action'] ? 'bulk-edit' : 'quick-edit' ); ?>">
				<div class="psupsellmaster-quick-edit-container">
					<?php do_action( 'psupsellmaster_products_inline_edit_fields', $args ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Renders the inline edit form fields for the product list.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_products_render_inline_edit_fields( $args ) {
	?>
	<div class="psupsellmaster-field-container psupsellmaster-field-container-preferred-products">
		<label class="psupsellmaster-field-label"><?php esc_html_e( 'Preferred Products', 'psupsellmaster' ); ?></label>
		<select class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-preferred-products" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-context="<?php echo esc_attr( $args['edit_action'] ); ?>" data-id-bulk-edit="psupsellmaster_bulk_edit_fields[preferred_products]" data-id-quick-edit="psupsellmaster_quick_edit_fields[preferred_products]" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-select2-defer="true" multiple="multiple" name="psupsellmaster_fields[preferred_products][]"></select>
	</div>
	<?php
}
add_action( 'psupsellmaster_products_inline_edit_fields', 'psupsellmaster_products_render_inline_edit_fields' );

/**
 * Renders the quick edit form for a product.
 *
 * @param string $column_name The column name.
 * @param string $post_type   The post type.
 * @param string $taxonomy    The taxonomy.
 */
function psupsellmaster_products_render_quick_edit( $column_name, $post_type, $taxonomy ) {
	// Define the args.
	$args = array(
		'edit_action' => 'quick_edit',
		'column_name' => $column_name,
		'post_type'   => $post_type,
		'taxonomy'    => $taxonomy,
	);

	// Render the HTML.
	psupsellmaster_products_render_inline_edit( $args );
}
add_action( 'quick_edit_custom_box', 'psupsellmaster_products_render_quick_edit', 50, 3 );

/**
 * Renders the bulk edit form for the products.
 *
 * @param string $column_name The column name.
 * @param string $post_type   The post type.
 */
function psupsellmaster_products_render_bulk_edit( $column_name, $post_type ) {
	// Define the args.
	$args = array(
		'edit_action' => 'bulk_edit',
		'column_name' => $column_name,
		'post_type'   => $post_type,
		'taxonomy'    => false,
	);

	// Render the HTML.
	psupsellmaster_products_render_inline_edit( $args );
}
add_action( 'bulk_edit_custom_box', 'psupsellmaster_products_render_bulk_edit', 50, 2 );

/**
 * Saves the inline edit form for the products.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_products_save_inline_edit( $args ) {
	// Check if the post id is empty.
	if ( empty( $args['post_id'] ) ) {
		return false;
	}

	// Get the post type.
	$post_type = get_post_type( $args['post_id'] );

	// Define the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type is not valid.
	if ( $product_post_type !== $post_type ) {
		return false;
	}

	// Check if the current user can not edit the post.
	if ( ! current_user_can( 'edit_post', $args['post_id'] ) ) {
		return false;
	}

	// Check if the WordPress is doing autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	// Get the input scores status.
	$input_scores_status = $args['scores_status'];

	// Get the input preferred products.
	$input_preferred_products = $args['preferred_products'];

	// Set the status meta key.
	$status_meta_key = '_psupsellmaster_scores_disabled';

	// Check if the scores status is disabled.
	if ( 'disabled' === $input_scores_status ) {
		// Update the meta key.
		update_post_meta( $args['post_id'], $status_meta_key, true );

		// Otherwise...
	} else {
		// Delete the meta key.
		delete_post_meta( $args['post_id'], $status_meta_key );
	}

	// Set the preferred products meta key.
	$preferred_products_meta_key = 'psupsellmaster_preferred_products';

	// Get the stored preferred products.
	$stored_preferred_products = get_post_meta( $args['post_id'], $preferred_products_meta_key );
	$stored_preferred_products = ! empty( $stored_preferred_products ) ? $stored_preferred_products : array();

	// Get the preferred products.
	$preferred_products = array();

	// Check if the preferred products is not empty and is an array.
	if ( ! empty( $input_preferred_products ) && is_array( $input_preferred_products ) ) {
		// Set the preferred products.
		$preferred_products = array_map( 'sanitize_text_field', $input_preferred_products );
	}

	// Get the removed preferred products.
	$removed_preferred_products = array_diff( $stored_preferred_products, $preferred_products );

	// Loop through the removed preferred products.
	foreach ( $removed_preferred_products as $removed_preferred_product ) {
		// Delete the meta key.
		delete_post_meta( $args['post_id'], $preferred_products_meta_key, $removed_preferred_product );
	}

	// Get the added preferred products.
	$added_preferred_products = array_diff( $preferred_products, $stored_preferred_products );

	// Loop through the added preferred products.
	foreach ( $added_preferred_products as $preferred_product ) {
		// Add the meta key.
		add_post_meta( $args['post_id'], $preferred_products_meta_key, $preferred_product );
	}

	do_action( 'psupsellmaster_products_save_inline_edit', $args );
}

/**
 * Saves the quick edit form data for a product.
 *
 * @param int $post_id The post id.
 */
function psupsellmaster_products_save_quick_edit( $post_id ) {
	// Check if the action is not defined.
	if ( ! isset( $_POST['action'] ) ) {
		return false;
	}

	// Check if the action is not valid.
	if ( 'inline-save' !== $_POST['action'] ) {
		return false;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster_nonce_quick_edit'] ) ) {
		return false;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce_quick_edit'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return false;
	}

	// Check if the plugin specific action is not defined.
	if ( ! isset( $_POST['psupsellmaster_action'] ) ) {
		return false;
	}

	// Check if the plugin specific action is not valid.
	if ( 'quick-edit' !== $_POST['psupsellmaster_action'] ) {
		return false;
	}

	// Check if the fields is not set.
	if ( ! isset( $_POST['psupsellmaster_fields'] ) ) {
		return false;
	}

	// Get the fields.
	$fields = is_array( $_POST['psupsellmaster_fields'] ) ? map_deep( wp_unslash( $_POST['psupsellmaster_fields'] ), 'sanitize_text_field' ) : array();

	// Get the scores status.
	$scores_status = isset( $fields['scores_status'] ) ? sanitize_text_field( wp_unslash( $fields['scores_status'] ) ) : 'enabled';

	// Get the preferred products.
	$preferred_products = isset( $fields['preferred_products'] ) && is_array( $fields['preferred_products'] ) ? map_deep( wp_unslash( $fields['preferred_products'] ), 'sanitize_text_field' ) : array();

	// Set the args.
	$args = array(
		'edit_action'        => 'quick_edit',
		'post_id'            => $post_id,
		'scores_status'      => $scores_status,
		'preferred_products' => $preferred_products,
	);

	// Allow developers to filter this.
	$args = apply_filters( 'psupsellmaster_products_save_quick_edit_args', $args );

	// Save inline edit.
	psupsellmaster_products_save_inline_edit( $args );
}
add_action( 'save_post', 'psupsellmaster_products_save_quick_edit', 10, 1 );

/**
 * Saves the bulk edit form data for the products.
 *
 * @param int $post_id The post id.
 */
function psupsellmaster_products_save_bulk_edit( $post_id ) {
	// Check if the action is not defined.
	if ( ! isset( $_GET['action'] ) ) {
		return false;
	}

	// Check if the action is not valid.
	if ( 'edit' !== $_GET['action'] ) {
		return false;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_GET['psupsellmaster_nonce_bulk_edit'] ) ) {
		return false;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_GET['psupsellmaster_nonce_bulk_edit'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return false;
	}

	// Check if the plugin specific action is not defined.
	if ( ! isset( $_GET['psupsellmaster_action'] ) ) {
		return false;
	}

	// Check if the plugin specific action is not valid.
	if ( 'bulk-edit' !== $_GET['psupsellmaster_action'] ) {
		return false;
	}

	// Check if the fields is not set.
	if ( ! isset( $_GET['psupsellmaster_fields'] ) ) {
		return false;
	}

	// Get the fields.
	$fields = is_array( $_GET['psupsellmaster_fields'] ) ? map_deep( wp_unslash( $_GET['psupsellmaster_fields'] ), 'sanitize_text_field' ) : array();

	// Get the scores status.
	$scores_status = isset( $fields['scores_status'] ) ? sanitize_text_field( wp_unslash( $fields['scores_status'] ) ) : 'enabled';

	// Get the preferred products.
	$preferred_products = isset( $fields['preferred_products'] ) && is_array( $fields['preferred_products'] ) ? map_deep( wp_unslash( $fields['preferred_products'] ), 'sanitize_text_field' ) : array();

	// Set the args.
	$args = array(
		'post_id'            => $post_id,
		'scores_status'      => $scores_status,
		'preferred_products' => $preferred_products,
	);

	// Allow developers to filter this.
	$args = apply_filters( 'psupsellmaster_products_save_bulk_edit_args', $args );

	// Save inline edit.
	psupsellmaster_products_save_inline_edit( $args );
}
add_action( 'save_post', 'psupsellmaster_products_save_bulk_edit', 10, 1 );

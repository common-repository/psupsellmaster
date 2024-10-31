<?php
/**
 * Admin - Functions - Wizard.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an admin wizard step.
 *
 * @param string $step_key the step key.
 */
function psupsellmaster_admin_render_wizard_step( $step_key ) {
	// Set the key.
	$file_key = str_replace( '_', '-', $step_key );

	// Require the template.
	require_once PSUPSELLMASTER_DIR . "includes/admin/templates/wizard/{$file_key}.php";
}

/**
 * Save the admin wizard step: upsells.
 *
 * @param array $data The step data.
 */
function psupsellmaster_admin_setup_wizard_save_upsells( $data ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Get the logarithmic scale.
	$logarithmic_scale = isset( $data['logarithmic_scale'] ) ? sanitize_text_field( wp_unslash( $data['logarithmic_scale'] ) ) : false;
	$logarithmic_scale = filter_var( $logarithmic_scale, FILTER_VALIDATE_BOOLEAN );

	// Get the max weight.
	$max_weight = isset( $data['max_weight'] ) ? sanitize_text_field( wp_unslash( $data['max_weight'] ) ) : false;
	$max_weight = filter_var( str_replace( array( ',', '.' ), '', $max_weight ), FILTER_VALIDATE_INT );
	$max_weight = false !== $max_weight ? $max_weight : 0;

	// Get the priorities.
	$priorities = isset( $data['priorities'] ) ? array_map( 'sanitize_text_field', wp_unslash( $data['priorities'] ) ) : array();

	// Get the weights.
	$weights = isset( $data['weights'] ) ? array_map( 'sanitize_text_field', wp_unslash( $data['weights'] ) ) : array();
	$weights = str_replace( array( ',', '.' ), '', $weights );
	$weights = array_map( 'absint', $weights );

	// Set the settings.
	$settings['priority_logarithmic_scale'] = true === $logarithmic_scale ? 1 : 0;

	// Set the settings.
	$settings['priority_max_weight'] = $max_weight;

	// Set the settings.
	$settings['priority'] = $priorities;

	// Set the settings.
	$settings['weight_factor'] = $weights;

	// Set the settings.
	PsUpsellMaster_Settings::set( 'algorithm_logic', $settings );

	// Save the settings.
	PsUpsellMaster_Settings::save();

	// Set the queue.
	$queue = array();

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Add the item to the list.
		array_push( $queue, array( 'key' => 'edd_prices' ) );
	}

	// Add the item to the list.
	array_push( $queue, array( 'key' => 'scores' ) );

	// Update the option.
	update_option( 'psupsellmaster_bp_queue', $queue, false );

	// Set the transient.
	set_transient( 'psupsellmaster_setup_wizard_bp_queue', true );
}
add_action( 'psupsellmaster_admin_setup_wizard_save_upsells', 'psupsellmaster_admin_setup_wizard_save_upsells' );

/**
 * Save the admin wizard step: locations.
 *
 * @param array $data The step data.
 */
function psupsellmaster_admin_setup_wizard_save_locations( $data ) {
	// Get the locations.
	$locations = isset( $data['locations'] ) ? sanitize_text_field( wp_unslash( $data['locations'] ) ) : '';
	$locations = explode( ',', $locations );

	// Check the locations.
	if ( in_array( 'page_product', $locations, true ) ) {
		// Get the page product.
		$page_product = isset( $data['page_product'] ) ? sanitize_text_field( wp_unslash( $data['page_product'] ) ) : false;
		$page_product = filter_var( $page_product, FILTER_VALIDATE_BOOLEAN );

		// Set the settings.
		PsUpsellMaster_Settings::set( 'product_page_enable', $page_product );
	}

	// Check the locations.
	if ( in_array( 'page_checkout', $locations, true ) ) {
		// Get the page checkout.
		$page_checkout = isset( $data['page_checkout'] ) ? sanitize_text_field( wp_unslash( $data['page_checkout'] ) ) : false;
		$page_checkout = filter_var( $page_checkout, FILTER_VALIDATE_BOOLEAN );

		// Set the settings.
		PsUpsellMaster_Settings::set( 'checkout_page_enable', $page_checkout );
	}

	// Check the locations.
	if ( in_array( 'page_receipt', $locations, true ) ) {
		// Get the page receipt.
		$page_receipt = isset( $data['page_receipt'] ) ? sanitize_text_field( wp_unslash( $data['page_receipt'] ) ) : false;
		$page_receipt = filter_var( $page_receipt, FILTER_VALIDATE_BOOLEAN );

		// Set the settings.
		PsUpsellMaster_Settings::set( 'purchase_receipt_page_enable', $page_receipt );
	}

	// Check the locations.
	if ( in_array( 'popup_cart', $locations, true ) ) {
		// Get the popup cart.
		$popup_cart = isset( $data['popup_cart'] ) ? sanitize_text_field( wp_unslash( $data['popup_cart'] ) ) : false;
		$popup_cart = filter_var( $popup_cart, FILTER_VALIDATE_BOOLEAN );

		// Set the settings.
		PsUpsellMaster_Settings::set( 'add_to_cart_popup_enable', $popup_cart );
	}

	// Check the locations.
	if ( in_array( 'popup_exit', $locations, true ) ) {
		// Get the popup exit.
		$popup_exit = isset( $data['popup_exit'] ) ? sanitize_text_field( wp_unslash( $data['popup_exit'] ) ) : false;
		$popup_exit = filter_var( $popup_exit, FILTER_VALIDATE_BOOLEAN );

		// Set the settings.
		PsUpsellMaster_Settings::set( 'exit_intent_popup_enable', $popup_exit );
	}

	// Save the settings.
	PsUpsellMaster_Settings::save();
}
add_action( 'psupsellmaster_admin_setup_wizard_save_locations', 'psupsellmaster_admin_setup_wizard_save_locations' );

/**
 * Save the admin wizard step: campaigns.
 *
 * @param array $data The step data.
 */
function psupsellmaster_admin_setup_wizard_save_campaigns( $data ) {
	// Get the title.
	$title = isset( $data['title'] ) ? sanitize_text_field( wp_unslash( $data['title'] ) ) : '';

	// Get the status.
	$status = isset( $data['status'] ) ? sanitize_text_field( wp_unslash( $data['status'] ) ) : '';

	// Get the start date.
	$start_date = isset( $data['start_date'] ) ? sanitize_text_field( wp_unslash( $data['start_date'] ) ) : '';
	$start_date = str_replace( '/', '-', $start_date );

	// Get the end date.
	$end_date = isset( $data['end_date'] ) ? sanitize_text_field( wp_unslash( $data['end_date'] ) ) : '';
	$end_date = str_replace( '/', '-', $end_date );

	// Get the coupon code.
	$coupon_code = isset( $data['coupon_code'] ) ? sanitize_text_field( wp_unslash( $data['coupon_code'] ) ) : '';

	// Get the coupon type.
	$coupon_type = isset( $data['coupon_type'] ) ? sanitize_text_field( wp_unslash( $data['coupon_type'] ) ) : '';

	// Get the coupon amount.
	$coupon_amount = isset( $data['coupon_amount'] ) ? sanitize_text_field( wp_unslash( $data['coupon_amount'] ) ) : '';

	// Set the settings.
	$data = array(
		'title'         => $title,
		'status'        => $status,
		'start_date'    => $start_date,
		'end_date'      => $end_date,
		'coupon_code'   => $coupon_code,
		'coupon_type'   => $coupon_type,
		'coupon_amount' => $coupon_amount,
		'origin'        => 'wizard',
	);

	// Get the campaign id from the setup wizard.
	$campaign_id = psupsellmaster_campaigns_get_id_from_setup_wizard();

	// Check if the campaign id is not empty.
	if ( ! empty( $campaign_id ) ) {
		// Set the settings.
		$data['campaign_id'] = $campaign_id;

		// Get the coupons.
		$coupons = psupsellmaster_get_campaign_coupons( $campaign_id );

		// Get the coupon id.
		$coupon_id = array_shift( $coupons );

		// Check the coupon id.
		if ( ! empty( $coupon_id ) ) {
			// Set the settings.
			$data['coupon_id'] = $coupon_id;
		}
	}

	// Save the campaign.
	psupsellmaster_save_campaign( $data );
}
add_action( 'psupsellmaster_admin_setup_wizard_save_campaigns', 'psupsellmaster_admin_setup_wizard_save_campaigns' );

/**
 * Save the admin wizard step: summary.
 */
function psupsellmaster_admin_setup_wizard_save_summary() {
	// Update the option.
	update_option( 'psupsellmaster_admin_setup_wizard_status', 'completed', false );

	// Delete the transient.
	delete_transient( 'psupsellmaster_setup_wizard_bp_queue' );
}
add_action( 'psupsellmaster_admin_setup_wizard_save_summary', 'psupsellmaster_admin_setup_wizard_save_summary' );

/**
 * Render the wizard step: summary - upsells item.
 *
 * @param string $status The status.
 */
function psupsellmaster_admin_render_wizard_summary_item_scores( $status ) {
	?>
	<?php if ( 'success' === $status ) : ?>
		<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
	<?php elseif ( 'pending' === $status ) : ?>
		<div class="psupsellmaster-item-icon"><span class="psupsellmaster-icon"><span class="spinner is-active"></span></span></div>
	<?php else : ?>
		<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-no psupsellmaster-icon"></span></div>
	<?php endif; ?>
	<div class="psupsellmaster-item-label">
		<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Upsells Automatically Calculated', 'psupsellmaster' ); ?></p>
		<?php if ( 'error' === $status ) : ?>
			<p class="psupsellmaster-paragraph psupsellmaster-error">
				<?php
				/* translators: 1: Text, 2: Setup Wizard Step URL, 3: Text. */
				printf(
					'<strong>%1$s <a class="psupsellmaster-link" href="%2$s">%3$s</a>. %4$s.</strong>',
					esc_html__( 'Please', 'psupsellmaster' ),
					esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=upsells' ) ),
					esc_html__( 'double-check the Upsells step', 'psupsellmaster' ),
					esc_html__( 'The Upsells were not calculated', 'psupsellmaster' )
				);
				?>
			</p>
		<?php elseif ( 'pending' === $status ) : ?>
			<p class="psupsellmaster-paragraph psupsellmaster-pending">
				<?php
				/* translators: 1: Text, 2: Setup Wizard Step URL, 3: Text. */
				printf(
					'<strong>%1$s.</strong>',
					esc_html__( 'Please wait, the Upsells are being calculated', 'psupsellmaster' ),
				);
				?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

<?php
/**
 * Admin - Templates - Wizard - Locations.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the settings.
$settings = array(
	'page_product'  => filter_var( PsUpsellMaster_Settings::get( 'product_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'page_checkout' => filter_var( PsUpsellMaster_Settings::get( 'checkout_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'page_receipt'  => filter_var( PsUpsellMaster_Settings::get( 'purchase_receipt_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'popup_cart'    => filter_var( PsUpsellMaster_Settings::get( 'add_to_cart_popup_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'popup_exit'    => filter_var( PsUpsellMaster_Settings::get( 'exit_intent_popup_enable' ), FILTER_VALIDATE_BOOLEAN ),
);

// Set the locations.
$locations = array(
	'page_product'  => __( 'Product Page', 'psupsellmaster' ),
	'page_checkout' => __( 'Checkout Page', 'psupsellmaster' ),
	'popup_cart'    => __( 'Add to Cart Popup', 'psupsellmaster' ),
);

// Check if this is the pro version.
if ( psupsellmaster_is_pro() ) {
	// Set the locations.
	$locations = array(
		'page_product'  => __( 'Product Page', 'psupsellmaster' ),
		'page_checkout' => __( 'Checkout Page', 'psupsellmaster' ),
		'page_receipt'  => __( 'Purchase Receipt Page', 'psupsellmaster' ),
		'popup_cart'    => __( 'Add to Cart Popup', 'psupsellmaster' ),
		'popup_exit'    => __( 'Exit Intent Popup', 'psupsellmaster' ),
	);
}

?>
<form class="psupsellmaster-wizard-form" method="post">
	<div class="psupsellmaster-step-body">
		<div class="psupsellmaster-step-something1">
			<input name="redirect" type="hidden" value="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=campaigns' ) ); ?>" />
			<input name="step" type="hidden" value="locations" />
			<input name="locations" type="hidden" value="<?php echo esc_attr( implode( ',', array_keys( $locations ) ) ); ?>" />
			<h2 class="psupsellmaster-step-title"><?php esc_html_e( 'Locations', 'psupsellmaster' ); ?></h2>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Please define the Locations where Upsell Lists & Carousels shall be displayed.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'The Locations may be changed at any time later on.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'The Upsells may also be displayed through Shortcodes and Blocks on any page later on.', 'psupsellmaster' ); ?></p>
		</div>
		<div class="psupsellmaster-step-something2">
			<div class="psupsellmaster-form-content">
				<ul class="psupsellmaster-form-list">
					<?php foreach ( $locations as $location_key => $location_label ) : ?>
						<?php $checked = isset( $settings[ $location_key ] ) ? $settings[ $location_key ] : false; ?>
						<li class="psupsellmaster-list-item">
							<label class="psupsellmaster-field-label">
								<input <?php checked( $checked ); ?> class="psupsellmaster-field" name="<?php echo esc_attr( $location_key ); ?>" type="checkbox" />
								<span><?php echo esc_html( $location_label ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="psupsellmaster-step-footer">
		<a class="button psupsellmaster-button-link psupsellmaster-button-previous" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=upsells' ) ); ?>"><span>&#8592; <?php esc_html_e( 'Previous Step', 'psupsellmaster' ); ?></span></a>
		<a class="button psupsellmaster-button-link psupsellmaster-button-skip" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=campaigns' ) ); ?>"><span><?php esc_html_e( 'Skip Step', 'psupsellmaster' ); ?></span></a>
		<button class="button button-primary psupsellmaster-button psupsellmaster-button-save" type="submit"><span><?php esc_html_e( 'Save & Continue', 'psupsellmaster' ); ?></span></button>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</form>

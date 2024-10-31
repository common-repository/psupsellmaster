<?php
/**
 * LITE - Admin - Functions - AJAX.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Upgrade to PRO admin notices.
 */
function psupsellmaster_lite_admin_notices_upgrade_to_pro() {
	// Set the messages.
	$messages = array(
		__( 'Offer more suitable and effective Upsells to customers - Watch your revenues soar.', 'psupsellmaster' ),
		__( 'Calculate more tailored Upsells for all your products - No Limits!', 'psupsellmaster' ),
		__( 'Take control of your offers - Get more flexibility in calculating automatic upsells for your products.', 'psupsellmaster' ),
		__( 'Keep Upselling everywhere - Upsell use the purchase receipt page to upsell your products to existing customers.', 'psupsellmaster' ),
		__( 'Boost your Upsell visibility - Place offers in Sidebars to expand customer reach.', 'psupsellmaster' ),
		__( 'Showcase Upsells on Select Pages - Place your offers anywhere using Page Blocks.', 'psupsellmaster' ),
		__( 'Get more Promotion Templates - Create powerful, revenue-generating campaigns.', 'psupsellmaster' ),
		__( 'Run more discount campaigns - Expand your promotional holiday offers using more templates.', 'psupsellmaster' ),
		__( 'Efficiently manage discount codes - Create promotional campaign offers for your customers.', 'psupsellmaster' ),
		__( 'Maximize Revenue with consistent Upselling - Streamline your Upsell Strategy today.', 'psupsellmaster' ),
	);

	// Get a random key.
	$random_key = array_rand( $messages );

	// Get the message.
	$message = isset( $messages[ $random_key ] ) ? $messages[ $random_key ] : '';

	// Chech if the message is empty.
	if ( empty( $message ) ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p>
			<?php
			/* translators: 1: main message, 2: PRO version URL, 3: upgrade to pro message. */
			printf(
				'<strong>%s</strong>: %s <a href="%s" target="_blank"><strong>%s</strong></a>',
				esc_html__( 'UpsellMaster', 'psupsellmaster' ),
				esc_html( $message ),
				esc_url( PSUPSELLMASTER_PRODUCT_URL ),
				esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'psupsellmaster_lite_admin_notices_upgrade_to_pro' );

<?php
/**
 * LITE - Admin - Functions - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add html in the beginning of the campaign templates.
 */
function psupsellmaster_lite_campaign_templates_begin() {
	?>
	<p class="psupsellmaster-lite-notice">
		<?php
		printf(
			/* translators: 1: new blank campaign link. */
			esc_html__( 'Unlock an extensive collection of exclusive templates tailored for every annual event in the %s.', 'psupsellmaster' ),
			'<a href="' . esc_url( PSUPSELLMASTER_PRODUCT_URL ) . '" target="_blank"><strong>' . esc_html__( 'PRO version', 'psupsellmaster' ) . '</strong></a>'
		);
		?>
	</p>
	<?php
}
add_action( 'psupsellmaster_campaign_templates_begin', 'psupsellmaster_lite_campaign_templates_begin' );

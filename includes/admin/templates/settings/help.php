<?php
/**
 * Admin - Templates - Settings - Help.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div>
	<h2 style="marging:0px;"><?php esc_html_e( 'Help', 'psupsellmaster' ); ?></h2>
	<hr />
	<p>
		<?php
		printf(
			/* translators: 1: Documentation link. */
			esc_html__( "Please refer to the %s for more detailed information. Explore the documentation to learn how to maximize the plugin's features and optimize your upselling strategies.", 'psupsellmaster' ),
			'<a href="' . esc_url( PSUPSELLMASTER_DOCUMENTATION_URL ) . '" target="_blank">' . esc_html__( 'UpsellMaster documentation', 'psupsellmaster' ) . '</a>'
		);
		?>
	</p>
	<p>
		<?php
		printf(
			/* translators: 1: Open support ticket link. */
			esc_html__( 'If you require further assistance or have specific questions, our dedicated support team is ready to help. Please %s to reach out to us.', 'psupsellmaster' ),
			'<a href="' . esc_url( PSUPSELLMASTER_OPEN_TICKET_URL ) . '" target="_blank">' . esc_html__( 'open a support ticket', 'psupsellmaster' ) . '</a>'
		);
		?>
	</p>
</div>

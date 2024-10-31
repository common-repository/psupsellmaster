<?php
/**
 * Admin - Templates - Wizard - Start.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form class="psupsellmaster-wizard-form" method="post">
	<div class="psupsellmaster-step-body">
		<div class="psupsellmaster-step-something1">
			<h2 class="psupsellmaster-step-title"><?php esc_html_e( 'Start', 'psupsellmaster' ); ?></h2>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Congratulations for deciding to use UpsellMaster!', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( "UpsellMaster setup is fast and easy. We'll quickly walk you through some important settings. Don't worry: You can go back and change anything you do - at anytime.", 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'UpsellMaster allows showing Upsell Products throughout the whole Website and offers a data-driven algorithm to automatically calculate scores and select the most suitable Upsells for every single product. ', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Display Upsell Products, Recently Visited Products, and Campaign Products on the Website.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><strong><?php esc_html_e( 'Overall, UpsellMaster offers a comprehensive tool to manage your Upsells aiming to bump-up the average order value of all Webstore Customers.', 'psupsellmaster' ); ?></strong></p>
		</div>
	</div>
	<div class="psupsellmaster-step-footer">
		<a class="button button-primary psupsellmaster-button psupsellmaster-button-start" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=upsells' ) ); ?>"><span><?php esc_html_e( 'Start Wizard', 'psupsellmaster' ); ?></span></a>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</form>

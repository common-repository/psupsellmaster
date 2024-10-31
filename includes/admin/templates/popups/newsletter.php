<?php
/**
 * Admin - Templates - Popups - Newsletter.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the base plugin name.
$base_plugin_name = psupsellmaster_get_base_plugin_name();

// Set the auto attribute.
$auto = 'false';

// Get the installed at.
$installed_at = get_option( 'psupsellmaster_installed_at' );

// Set the days passed.
$days_passed = ( time() > ( intval( $installed_at ) + DAY_IN_SECONDS * 2 ) );

// Check the days passed.
if ( $days_passed ) {
	// Set the limit.
	$limit = 2;

	// Get the count.
	$count = get_option( 'psupsellmaster_newsletter_popup_count', 0 );

	// Check the limit.
	if ( $limit > $count ) {
		// Set the auto.
		$auto = 'true';

		// Update the option.
		update_option( 'psupsellmaster_newsletter_popup_count', ++$count, false );
	}
}
?>
<div class="psupsellmaster-modal psupsellmaster-fade" data-auto="<?php echo $auto; ?>" id="psupsellmaster-modal-newsletter" style="display: none;">
	<div class="psupsellmaster-modal-dialog psupsellmaster-modal-lg">
		<div class="psupsellmaster-modal-content">
			<div class="psupsellmaster-modal-body">
				<form class="psupsellmaster-modal-form" method="POST">
					<h3 class="psupsellmaster-modal-heading">
						<?php
						printf(
							/* translators: %1$s base plugin name. */
							esc_html__( 'Receive PRO Tips on how best to use Upsell Features in %1$s', 'psupsellmaster' ),
							esc_html( $base_plugin_name )
						);
						?>
					</h3>
					<p class="psupsellmaster-modal-paragraph">
						<?php
						printf(
							/* translators: %1$s b tag (bold text), %2$s b tag (bold text), %3$s base plugin name, %4$s plugin link, %5$s author link. */
							esc_html__( 'Receive %1$s on what are the %2$s to use in %3$s and how to best work with the %4$s by signing up for our newsletter from %5$s.', 'psupsellmaster' ),
							'<b>' . esc_html__( 'PRO tips', 'psupsellmaster' ) . '</b>',
							'<b>' . esc_html__( 'best upsell strategies', 'psupsellmaster' ) . '</b>',
							esc_html( $base_plugin_name ),
							'<a href="' . esc_url( PSUPSELLMASTER_PRODUCT_URL ) . '" target="_blank">UpsellMaster Plugin</a>',
							'<a href="https://www.pluginsandsnippets.com" target="_blank">Plugins & Snippets</a>'
						);
						?>
					</p>
					<p class="psupsellmaster-modal-paragraph">
						<?php
						printf(
							/* translators: %1$s b tag (bold text), %2$s b tag (bold text). */
							esc_html__( 'Learn also how to %1$s and uncover innovative approaches to drive %2$s through strategic upselling.', 'psupsellmaster' ),
							'<b>' . esc_html__( 'stay ahead of the competition', 'psupsellmaster' ) . '</b>',
							'<b>' . esc_html__( 'more conversions and increase profits', 'psupsellmaster' ) . '</b>'
						);
						?>
					</p>
					<?php if ( psupsellmaster_is_lite() && ! psupsellmaster_is_newsletter_subscribed() ) : ?>
						<h3 class="psupsellmaster-modal-heading psupsellmaster-modal-text-green">
							<?php esc_html_e( 'Sign-up to our Newsletter today and calculate up to 300 Upsells!', 'psupsellmaster' ); ?>
						</h3>
						<p class="psupsellmaster-modal-paragraph psupsellmaster-modal-text-green">
							<?php esc_html_e( 'Be able to offer more tailored Upsells by increasing the calculation limit to 300 - and watch your revenues soar!', 'psupsellmaster' ); ?>
						</p>
					<?php endif; ?>
					<input class="psupsellmaster-modal-field" required="required" type="email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
					<div class="psupsellmaster-modal-buttons">
						<a class="psupsellmaster-trigger-close-modal psupsellmaster-modal-link-close" href="#"><?php esc_html_e( 'Cancel', 'psupsellmaster' ); ?></a>
						<button class="button-primary"><?php esc_html_e( 'Subscribe', 'psupsellmaster' ); ?></button>
					</div>
				</form>
				<div class="psupsellmaster-modal-ajax-error" style="display: none;">
					<p class="psupsellmaster-modal-paragraph"><?php esc_html_e( 'There was an error in processing your request, please try again.', 'psupsellmaster' ); ?></p>
				</div>
				<div class="psupsellmaster-modal-ajax-success" style="display: none;">
					<h3 class="psupsellmaster-modal-heading"><?php esc_html_e( 'Thank you for signing up to our Newsletter!', 'psupsellmaster' ); ?></h3>
					<button class="button psupsellmaster-trigger-close-modal"><?php esc_html_e( 'Close', 'psupsellmaster' ); ?></button>
				</div>
				<div class="psupsellmaster-backdrop-spinner" style="display: none;">
					<div class="spinner is-active"></div>
				</div>
			</div>
		</div>
	</div>
</div>

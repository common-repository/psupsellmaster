<?php
/**
 * LITE - Admin - Templates - Newsletter.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the base plugin name.
$base_plugin_name = psupsellmaster_get_base_plugin_name();

?>
<div id="psupsellmaster-newsletter">
	<form class="psupsellmaster-newsletter-form" method="POST">
		<h3 class="psupsellmaster-newsletter-heading">
			<?php
			printf(
				/* translators: %1$s base plugin name. */
				esc_html__( 'Receive PRO Tips on how best to use Upsell Features in %1$s', 'psupsellmaster' ),
				esc_html( $base_plugin_name )
			);
			?>
		</h3>
		<p class="psupsellmaster-newsletter-paragraph">
			<?php
			printf(
				/* translators: %1$s b tag (bold text), %2$s b tag (bold text), %3$s base plugin name, %4$s plugin link, %5$s author link, %6$s br tag (line break), %7$s b tag (bold text), %8$s b tag (bold text). */
				esc_html__( 'Receive %1$s on what are the %2$s to use in %3$s and how to best work with the %4$s by signing up for our newsletter from %5$s.%6$sLearn also how to %7$s and uncover innovative approaches to drive %8$s through strategic upselling.', 'psupsellmaster' ),
				'<b>' . esc_html__( 'PRO tips', 'psupsellmaster' ) . '</b>',
				'<b>' . esc_html__( 'best upsell strategies', 'psupsellmaster' ) . '</b>',
				esc_html( $base_plugin_name ),
				'<a href="' . esc_url( PSUPSELLMASTER_PRODUCT_URL ) . '" target="_blank">UpsellMaster Plugin</a>',
				'<a href="https://www.pluginsandsnippets.com" target="_blank">Plugins & Snippets</a>',
				'<br />',
				'<b>' . esc_html__( 'stay ahead of the competition', 'psupsellmaster' ) . '</b>',
				'<b>' . esc_html__( 'more conversions and increase profits', 'psupsellmaster' ) . '</b>'
			);
			?>
		</p>
		<?php if ( psupsellmaster_is_lite() && ! psupsellmaster_is_newsletter_subscribed() ) : ?>
			<h3 class="psupsellmaster-newsletter-heading psupsellmaster-newsletter-text-green">
				<?php esc_html_e( 'Sign-up to our Newsletter today and calculate up to 300 Upsells!', 'psupsellmaster' ); ?>
			</h3>
			<p class="psupsellmaster-newsletter-paragraph psupsellmaster-newsletter-text-green">
				<?php esc_html_e( 'Be able to offer more tailored Upsells by increasing the calculation limit to 300 - and watch your revenues soar!', 'psupsellmaster' ); ?>
			</p>
		<?php endif; ?>
		<div class="psupsellmaster-newsletter-form-row">
			<input class="psupsellmaster-newsletter-field" required="required" type="email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">
			<div class="psupsellmaster-newsletter-actions">
				<button class="button-primary"><?php esc_html_e( 'Subscribe', 'psupsellmaster' ); ?></button>
			</div>
		</div>
	</form>
	<div class="psupsellmaster-newsletter-ajax-error" style="display: none;">
		<p class="psupsellmaster-newsletter-paragraph"><?php esc_html_e( 'There was an error in processing your request, please try again.', 'psupsellmaster' ); ?></p>
	</div>
	<div class="psupsellmaster-newsletter-ajax-success" style="display: none;">
		<h3 class="psupsellmaster-newsletter-heading"><?php esc_html_e( 'Thank you for signing up to our Newsletter!', 'psupsellmaster' ); ?></h3>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</div>

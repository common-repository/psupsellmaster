<?php
/**
 * Admin - Templates - Campaigns - Templates.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if the user can manage options.
if ( ! current_user_can( 'manage_options' ) ) {
	// Set the error and exit.
	wp_die( esc_html__( 'Sorry, you are not allowed to view campaign templates.' ) );
}

?>
<?php do_action( 'psupsellmaster_campaign_templates_begin' ); ?>
<p class="psupsellmaster-new-campaign-instructions">
	<?php
	printf(
		/* translators: 1: new blank campaign link. */
		esc_html__( 'Please choose a template or create a %s.', 'psupsellmaster' ),
		'<a class="psupsellmaster-btn-new-blank-campaign" href="' . esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=new' ) ) . '" target="_blank">' . esc_html__( 'new blank campaign', 'psupsellmaster' ) . '</a>'
	);
	?>
</p>
<?php do_action( 'psupsellmaster_campaign_templates_before' ); ?>
<?php if ( ! empty( $templates ) ) : ?>
	<ul class="psupsellmaster-templates-sections">
		<?php foreach ( $templates as $section => $section_templates ) : ?>
			<?php
			if ( empty( $section_templates ) ) {
				continue;
			}
			?>
			<li class="psupsellmaster-templates-section">
				<?php if ( 'core-lite' === $section ) : ?>
					<strong><?php esc_html_e( 'Core (Lite) Templates', 'psupsellmaster' ); ?></strong>
				<?php elseif ( 'core-pro' === $section ) : ?>
					<strong><?php esc_html_e( 'Core (PRO) Templates', 'psupsellmaster' ); ?></strong>
				<?php elseif ( 'stored' === $section ) : ?>
					<strong><?php esc_html_e( 'Stored Templates', 'psupsellmaster' ); ?></strong>
				<?php else : ?>
					<strong><?php esc_html_e( 'Unknown', 'psupsellmaster' ); ?></strong>
				<?php endif; ?>
				<hr />
				<?php if ( ! empty( $section_templates ) ) : ?>
					<ul class="psupsellmaster-templates">
						<?php foreach ( $section_templates as $key => $data ) : ?>
							<li class="psupsellmaster-template">
								<a class="psupsellmaster-template-link psupsellmaster-btn-new-campaign-from-template" data-template="<?php echo esc_attr( $key ); ?>" href="#">
									<?php if ( ! empty( $data['template']['thumbnail'] ) ) : ?>
										<img alt="<?php echo esc_attr( $data['template']['title'] ); ?>" class="psupsellmaster-template-thumbnail" src="<?php echo esc_url( $data['template']['thumbnail'] ); ?>" />
									<?php endif; ?>
									<?php if ( 'stored' === $section ) : ?>
										<span class="psupsellmaster-template-title"><?php echo esc_html( $data['template']['title'] ); ?></span>
									<?php endif; ?>
								</a>
								<?php if ( 'stored' === $section ) : ?>
									<a class="psupsellmaster-btn-delete-template" data-template="<?php echo esc_attr( $key ); ?>" href="#"><?php esc_html_e( 'Delete', 'psupsellmaster' ); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php do_action( 'psupsellmaster_campaign_templates_after' ); ?>
<?php do_action( 'psupsellmaster_campaign_templates_end' ); ?>

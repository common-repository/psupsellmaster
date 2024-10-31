<?php
/**
 * Admin - Templates - Wizard - Summary.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Set the statuses.
$statuses = array(
	'wizard'          => 'success',
	'priorities'      => 'pending',
	'products'        => 'success',
	'scores'          => 'pending',
	'tracking'        => 'success',
	'locations'       => 'pending',
	'features'        => 'success',
	'campaigns'       => 'success',
	'wizard_campaign' => 'pending',
	'settings'        => 'success',
);

// Get the algorithm logic.
$algorithm_logic = PsUpsellMaster_Settings::get( 'algorithm_logic' );

// Get the stored priorities.
$stored_priorities = $algorithm_logic['priority'];

// Get the stored weights.
$stored_weights = $algorithm_logic['weight_factor'];

// Set the statuses.
$statuses['priorities'] = ! empty( $stored_priorities[0] ) && ! empty( $stored_weights[0] );
$statuses['priorities'] = $statuses['priorities'] ? 'success' : 'error';

// Get the started.
$bp_queue_started = get_transient( 'psupsellmaster_setup_wizard_bp_queue' );
$bp_queue_started = filter_var( $bp_queue_started, FILTER_VALIDATE_BOOLEAN );

// Get the bp queue.
$bp_queue = get_option( 'psupsellmaster_bp_queue' );

// Set the statuses.
$statuses['scores'] = $bp_queue_started && empty( $bp_queue );
$statuses['scores'] = $statuses['scores'] ? 'success' : ( $bp_queue_started ? 'pending' : 'error' );

// Get the locations.
$locations = array(
	'page_product'  => filter_var( PsUpsellMaster_Settings::get( 'product_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'page_checkout' => filter_var( PsUpsellMaster_Settings::get( 'checkout_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'page_receipt'  => filter_var( PsUpsellMaster_Settings::get( 'purchase_receipt_page_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'popup_cart'    => filter_var( PsUpsellMaster_Settings::get( 'add_to_cart_popup_enable' ), FILTER_VALIDATE_BOOLEAN ),
	'popup_exit'    => filter_var( PsUpsellMaster_Settings::get( 'exit_intent_popup_enable' ), FILTER_VALIDATE_BOOLEAN ),
);

// Set the statuses.
$statuses['locations'] = in_array( true, $locations, true );
$statuses['locations'] = $statuses['locations'] ? 'success' : 'error';

// Set the statuses.
$statuses['wizard_campaign'] = ! empty( psupsellmaster_campaigns_get_id_from_setup_wizard() );
$statuses['wizard_campaign'] = $statuses['wizard_campaign'] ? 'success' : 'warning';

// Set the submit statuses (remove keys not used to submit the form).
$submit_statuses = array_diff_key( $statuses, array_flip( array( 'wizard_campaign' ) ) );

// Set the submit disabled.
$submit_disabled = ! empty( array_diff( $submit_statuses, array( 'success' ) ) );

?>
<form class="psupsellmaster-wizard-form" method="post">
	<div class="psupsellmaster-step-body">
		<div class="psupsellmaster-step-something1">
			<input name="redirect" type="hidden" value="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster' ) ); ?>" />
			<input name="step" type="hidden" value="summary" />
			<h2 class="psupsellmaster-step-title"><?php esc_html_e( 'Summary', 'psupsellmaster' ); ?></h2>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Are you ready to start the Upselling Journey aiming to enhance the Revenues of your Webstore?', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Please check the topics below to confirm the Website is ready to Start Upselling!', 'psupsellmaster' ); ?></p>
		</div>
		<div class="psupsellmaster-step-something2">
			<ul class="psupsellmaster-list">
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'wizard' ); ?>" data-status="<?php echo esc_attr( $statuses['wizard'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Setup Wizard Successfully Saved', 'psupsellmaster' ); ?></p></div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'priorities' ); ?>" data-status="<?php echo esc_attr( $statuses['priorities'] ); ?>">
					<?php if ( 'success' === $statuses['priorities'] ) : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<?php else : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-no psupsellmaster-icon"></span></div>
					<?php endif; ?>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Priority Criteria Defined', 'psupsellmaster' ); ?></p></div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'products' ); ?>" data-status="<?php echo esc_attr( $statuses['products'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Webstore Products Analyzed', 'psupsellmaster' ); ?></p></div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'scores' ); ?>" data-status="<?php echo esc_attr( $statuses['scores'] ); ?>">
					<?php psupsellmaster_admin_render_wizard_summary_item_scores( $statuses['scores'] ); ?>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'tracking' ); ?>" data-status="<?php echo esc_attr( $statuses['tracking'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Purchase Tracking Installed', 'psupsellmaster' ); ?></p></div>
				</li>
			</ul>
			<ul class="psupsellmaster-list">
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'locations' ); ?>" data-status="<?php echo esc_attr( $statuses['locations'] ); ?>">
					<?php if ( 'success' === $statuses['locations'] ) : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<?php else : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-no psupsellmaster-icon"></span></div>
					<?php endif; ?>
					<div class="psupsellmaster-item-label">
						<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Upsell Display Locations Defined', 'psupsellmaster' ); ?></p>
						<?php if ( 'success' !== $statuses['locations'] ) : ?>
							<p class="psupsellmaster-paragraph">
								<?php
								/* translators: 1: Text, 2: Setup Wizard Step URL, 3: Text. */
								printf(
									'<strong>%1$s <a class="psupsellmaster-link" href="%2$s">%3$s</a>. %4$s.</strong>',
									esc_html__( 'Please', 'psupsellmaster' ),
									esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=locations' ) ),
									esc_html__( 'double-check the Locations step', 'psupsellmaster' ),
									esc_html__( 'At least one Location should be defined', 'psupsellmaster' )
								);
								?>
							</p>
						<?php endif; ?>
					</div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'features' ); ?>" data-status="<?php echo esc_attr( $statuses['features'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Shortcodes & Blocks Available', 'psupsellmaster' ); ?></p></div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'campaigns' ); ?>" data-status="<?php echo esc_attr( $statuses['campaigns'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Campaigns Module Installed', 'psupsellmaster' ); ?></p></div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'wizard_campaign' ); ?>" data-status="<?php echo esc_attr( $statuses['wizard_campaign'] ); ?>">
					<?php if ( 'success' === $statuses['wizard_campaign'] ) : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<?php else : ?>
						<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-warning psupsellmaster-icon"></span></div>
					<?php endif; ?>
					<div class="psupsellmaster-item-label">
						<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Attractive Campaign Created', 'psupsellmaster' ); ?></p>
						<?php if ( 'success' !== $statuses['wizard_campaign'] ) : ?>
							<p class="psupsellmaster-paragraph">
								<?php
								/* translators: 1: Text, 2: Setup Wizard Step URL, 3: Text. */
								printf(
									'<strong>%1$s <a class="psupsellmaster-link" href="%2$s">%3$s</a>. %4$s.</strong>',
									esc_html__( 'Please', 'psupsellmaster' ),
									esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=campaigns' ) ),
									esc_html__( 'double-check the Campaigns step', 'psupsellmaster' ),
									esc_html__( 'The Campaign was not created', 'psupsellmaster' )
								);
								?>
							</p>
						<?php endif; ?>
					</div>
				</li>
				<li class="psupsellmaster-item" data-key="<?php echo esc_attr( 'settings' ); ?>" data-status="<?php echo esc_attr( $statuses['settings'] ); ?>">
					<div class="psupsellmaster-item-icon"><span class="dashicons dashicons-yes-alt psupsellmaster-icon"></span></div>
					<div class="psupsellmaster-item-label"><p class="psupsellmaster-paragraph"><?php esc_html_e( 'Advanced Settings Available', 'psupsellmaster' ); ?></p></div>
				</li>
			</ul>
		</div>
		<div class="psupsellmaster-step-something3">
			<p class="psupsellmaster-text-center"><?php esc_html_e( 'Happy Upselling!', 'psupsellmaster' ); ?></p>
			<br />
			<p class="psupsellmaster-text-center">
				<?php
				/* translators: 1: Text, 2: Product URL, 3: Text. */
				printf(
					'<strong>%1$s: <a class="psupsellmaster-link" href="%2$s" target="_blank">%3$s</a>.</strong>',
					esc_html__( 'Enhance your Upsells further and Unlock unlimited features', 'psupsellmaster' ),
					esc_url( PSUPSELLMASTER_PRODUCT_URL ),
					esc_html__( 'Get the PRO version', 'psupsellmaster' )
				);
				?>
			</p>
			<p class="psupsellmaster-text-center">
				<?php
				/* translators: 1: Text, 2: Anchor Documentation URL, 3: Anchor Text, 4: Text. */
				printf(
					'<span>%1$s <a class="psupsellmaster-link" href="%2$s" target="_blank">%3$s</a> %4$s.</span>',
					esc_html__( 'Please read the', 'psupsellmaster' ),
					esc_url( PSUPSELLMASTER_DOCUMENTATION_URL ),
					esc_html__( 'documentation', 'psupsellmaster' ),
					esc_html__( 'for more information', 'psupsellmaster' )
				);
				?>
			</p>
		</div>
	</div>
	<div class="psupsellmaster-step-footer">
		<a class="button psupsellmaster-button-link psupsellmaster-button-previous" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=campaigns' ) ); ?>">&#8592; <?php esc_html_e( 'Previous Step', 'psupsellmaster' ); ?></a>
		<button class="button button-primary psupsellmaster-button psupsellmaster-button-save" <?php disabled( $submit_disabled ); ?> type="submit"><span><?php esc_html_e( 'Start Upselling!', 'psupsellmaster' ); ?></span></button>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</form>

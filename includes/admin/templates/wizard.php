<?php
/**
 * Admin - Templates - Wizard.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Set the steps.
$steps = array(
	'start'     => __( 'Start', 'psupsellmaster' ),
	'upsells'   => __( 'Upsells', 'psupsellmaster' ),
	'locations' => __( 'Locations', 'psupsellmaster' ),
	'campaigns' => __( 'Campaigns', 'psupsellmaster' ),
	'summary'   => __( 'Summary', 'psupsellmaster' ),
);

// Get the current step.
$current_step = isset( $_GET['step'] ) ? sanitize_text_field( wp_unslash( $_GET['step'] ) ) : false;
$current_step = in_array( $current_step, array_keys( $steps ), true ) ? $current_step : 'start';

// Get the current step number.
$current_step_number = array_search( $current_step, array_keys( $steps ), true );
$current_step_number = false !== $current_step_number ? ( $current_step_number + 1 ) : 1;
?>
<div class="wrap">
	<div class="psupsellmaster-wizard">
		<h1 class="psupsellmaster-wizard-title"><?php esc_html_e( 'UpsellMaster - Setup Wizard', 'psupsellmaster' ); ?></h1>
		<ul class="psupsellmaster-wizard-progress">
			<?php $step_number = 1; ?>
			<?php foreach ( $steps as $step_key => $step_label ) : ?>
				<?php
				// Set the classes.
				$classes = array();

				// Add a class to the list.
				array_push( $classes, 'psupsellmaster-progress-step' );

				// Add a class to the list.
				array_push( $classes, 'psupsellmaster-step-' . $step_key );

				// Check the step number.
				if ( $current_step_number === $step_number ) {
					// Add a class to the list.
					array_push( $classes, 'psupsellmaster-active' );

					// Check the step number.
				} elseif ( $current_step_number > $step_number ) {
					// Add a class to the list.
					array_push( $classes, 'psupsellmaster-done' );
				}
				?>
				<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<a class="psupsellmaster-step-link" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=' . $step_key ) ); ?>">
						<span class="psupsellmaster-step-number"><?php echo esc_html( $step_number ); ?></span>
						<span class="psupsellmaster-step-label"><?php echo esc_html( $step_label ); ?></span>
					</a>
				</li>
				<?php ++$step_number; ?>
			<?php endforeach; ?>
		</ul>
		<div class="psupsellmaster-wizard-step psupsellmaster-step-<?php echo esc_attr( $current_step ); ?>">
			<?php psupsellmaster_admin_render_wizard_step( $current_step ); ?>
		</div>
	</div>
</div>

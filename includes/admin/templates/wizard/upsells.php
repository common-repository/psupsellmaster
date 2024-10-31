<?php
/**
 * Admin - Templates - Wizard - Upsells.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the algorithm logic.
$algorithm_logic = PsUpsellMaster_Settings::get( 'algorithm_logic' );

// Get the stored priorities.
$stored_priorities = $algorithm_logic['priority'];

// Get the stored weight factor.
$stored_weight_factor = $algorithm_logic['weight_factor'];

// Set the priority logarithmic scale.
$logarithmic_scale = false;

// Check if the plugin is the pro version.
if ( psupsellmaster_is_pro() ) {
	// Get the priority logarithmic scale.
	$logarithmic_scale = filter_var( $algorithm_logic['priority_logarithmic_scale'], FILTER_VALIDATE_BOOLEAN );
}

// Get the stored max weight.
$stored_max_weight = $algorithm_logic['priority_max_weight'];

// Get the priorities.
$priorities = psupsellmaster_get_priorities();

// Get the priority descriptions.
$priority_descriptions = psupsellmaster_get_priority_descriptions();

// Get the max priorities.
$max_priorities = min( psupsellmaster_settings_get_max_priorities(), 3 );

// Set the max weight (1 Trillion).
$max_weight = 1000000000000;
?>
<form class="psupsellmaster-wizard-form" method="post">
	<div class="psupsellmaster-step-body">
		<div class="psupsellmaster-step-something1">
			<input name="redirect" type="hidden" value="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=locations' ) ); ?>" />
			<input name="step" type="hidden" value="upsells" />
			<h2 class="psupsellmaster-step-title"><?php esc_html_e( 'Upsells', 'psupsellmaster' ); ?></h2>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Please define how to best select Upsells through the priorities by choosing the criteria and weights aiming to match the interest of the customers.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'The Upsell Algorithm automatically calculates the scores for all products and identify the most suitable Upsells for every single product.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Please set the initial criteria for the score system to get started. The priorities (criteria, weights, etc.) may be changed at any time later on.', 'psupsellmaster' ); ?></p>
		</div>
		<div class="psupsellmaster-step-something2">
			<div class="psupsellmaster-form-content">
				<div class="psupsellmaster-form-priorities">
					<div class="psupsellmaster-priorities-table">
						<div class="psupsellmaster-table-header">
							<div class="psupsellmaster-table-row">
								<div class="psupsellmaster-table-col psupsellmaster-table-col-label"></div>
								<div class="psupsellmaster-table-col psupsellmaster-table-col-criteria">
									<span><?php esc_html_e( 'Criteria', 'psupsellmaster' ); ?></span>
									<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The criteria to identify upsell products.', 'psupsellmaster' ); ?>"></span>
								</div>
								<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-number">
									<span><?php esc_html_e( 'Weight', 'psupsellmaster' ); ?></span>
									<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The weight is used to build the score of the upsell products.', 'psupsellmaster' ); ?>"></span>
								</div>
								<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-slider">
									<div class="psupsellmaster-table-col-fields">
										<?php if ( psupsellmaster_is_pro() ) : ?>
											<div class="psupsellmaster-table-col-field">
												<label>
													<input <?php checked( $logarithmic_scale ); ?> class="psupsellmaster-logarithmic-scale" data-max="<?php echo esc_attr( $max_weight ); ?>" name="logarithmic_scale" type="checkbox" />
													<span><?php esc_html_e( 'Enable Logarithmic Scale', 'psupsellmaster' ); ?></span>
													<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The logarithmic scale makes it easy to use large scales using the slider.', 'psupsellmaster' ); ?>"></span>
												</label>
											</div>
										<?php endif; ?>
										<div class="psupsellmaster-table-col-field">
											<label>
												<span><?php esc_html_e( 'Max Weight', 'psupsellmaster' ); ?></span>
												<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'This allows to use smaller ranges and sets the maximum weight when not using the logarithmic scale.', 'psupsellmaster' ); ?>"></span>
												<input autocomplete="off" class="psupsellmaster-max-weight" data-max="<?php echo esc_attr( $max_weight ); ?>" data-min="1" name="max_weight" type="text" value=<?php echo esc_attr( $stored_max_weight ); ?> />
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-table-body">
							<?php for ( $i = 0; $i < $max_priorities; $i++ ) : ?>
								<?php $stored_factor = isset( $stored_weight_factor[ $i ] ) ? $stored_weight_factor[ $i ] : 0; ?>
								<div class="psupsellmaster-table-row">
									<div class="psupsellmaster-table-col psupsellmaster-table-col-label">
										<label class="psupsellmaster-priority-label" for="psupsellmaster-priority-criteria-<?php echo esc_attr( $i ); ?>"><?php echo esc_html( sprintf( '%s %d', __( 'Priority', 'psupsellmaster' ), ( $i + 1 ) ) ); ?></label>
									</div>
									<div class="psupsellmaster-table-col psupsellmaster-table-col-criteria">
										<label class="psupsellmaster-priority-label-mobile" for="psupsellmaster-priority-criteria-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Criteria', 'psupsellmaster' ); ?></label>
										<select class="psupsellmaster-priority-field" id="psupsellmaster-priority-criteria-<?php echo esc_attr( $i ); ?>" name="priorities[]">
											<option value=""><?php esc_html_e( 'Select the Priority', 'psupsellmaster' ); ?></option>
											<?php foreach ( $priorities as $priority_key => $priority_label ) : ?>
												<?php $stored_priority = isset( $stored_priorities[ $i ] ) ? $stored_priorities[ $i ] : null; ?>
												<option <?php selected( $stored_priority, $priority_key ); ?> value="<?php echo esc_attr( $priority_key ); ?>"><?php echo esc_html( $priority_label ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-number">
										<label class="psupsellmaster-priority-label-mobile" for="psupsellmaster-priority-weight-number-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Weight', 'psupsellmaster' ); ?></label>
										<input autocomplete="off" class="psupsellmaster-range-number psupsellmaster-priority-field" data-max="<?php echo esc_attr( $max_weight ); ?>" data-min="0" name="weights[]" type="text" value=<?php echo esc_attr( $stored_factor ); ?> />
									</div>
									<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-slider">
										<input class="psupsellmaster-range-slider psupsellmaster-priority-field" data-log-max="13" data-log-min="0" data-log-step="1" data-normal-max="<?php echo esc_attr( $max_weight ); ?>" data-normal-min="0" id="psupsellmaster-priority-weight-number-<?php echo esc_attr( $i ); ?>" type="range" />
									</div>
								</div>
							<?php endfor; ?>
						</div>
					</div>
					<div class="psupsellmaster-priorities-info">
						<strong class="psupsellmaster-priorities-info-title"><?php esc_html_e( 'Priority Criterias', 'psupsellmaster' ); ?></strong>
						<?php foreach ( $priority_descriptions as $priority_key => $priority_description ) : ?>
							<?php $priority_label = $priorities[ $priority_key ]; ?>
							<p class="psupsellmaster-priorities-info-criteria">
								<strong class="psupsellmaster-priorities-info-label"><?php echo esc_html( "{$priority_label}:" ); ?></strong>
								<span class="psupsellmaster-priorities-info-description"><?php echo esc_html( $priority_description ); ?></span>
							</p>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="psupsellmaster-step-footer">
		<a class="button psupsellmaster-button-link psupsellmaster-button-previous" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=start' ) ); ?>"><span>&#8592; <?php esc_html_e( 'Previous Step', 'psupsellmaster' ); ?></span></a>
		<a class="button psupsellmaster-button-link psupsellmaster-button-skip" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=locations' ) ); ?>"><span><?php esc_html_e( 'Skip Step', 'psupsellmaster' ); ?></span></a>
		<button class="button button-primary psupsellmaster-button psupsellmaster-button-save" type="submit"><span><?php esc_html_e( 'Save & Continue', 'psupsellmaster' ); ?></span></button>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</form>

<?php
/**
 * Admin - Templates - Popups - Feedback.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="psupsellmaster-modal psupsellmaster-fade" id="psupsellmaster-modal-feedback" style="display: none;">
	<div class="psupsellmaster-modal-dialog psupsellmaster-modal-lg">
		<form class="psupsellmaster-modal-form" method="post">
			<div class="psupsellmaster-modal-content">
				<div class="psupsellmaster-modal-header">
					<h3 class="psupsellmaster-heading">
						<?php
						/* translators: %s: Plugin Name */
						printf( esc_html__( 'Quick feedback about %s', 'psupsellmaster' ), esc_html( PSUPSELLMASTER_NAME ) );
						?>
					</h3>
				</div>
				<div class="psupsellmaster-modal-body">
					<section class="psupsellmaster-section psupsellmaster-required">
						<h4 class="psupsellmaster-heading"><?php esc_html_e( 'Please let us know why you are deactivating the plugin.', 'psupsellmaster' ); ?></h4>
						<div class="psupsellmaster-validation">
							<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
						</div>
						<ul class="psupsellmaster-list">
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" name="reason_key" type="radio" value="short_period" />
									<span><?php esc_html_e( 'I only needed the plugin for a short period.', 'psupsellmaster' ); ?></span>
								</label>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" data-focus-target="#psupsellmaster-reason-better-plugin" data-toggle-target="#psupsellmaster-input-better-plugin" name="reason_key" type="radio" value="better_plugin" />
									<span><?php esc_html_e( 'I found a better plugin.', 'psupsellmaster' ); ?></span>
								</label>
								<div class="psupsellmaster-form-input psupsellmaster-hidden" data-display="block" id="psupsellmaster-input-better-plugin">
									<input autocomplete="off" class="psupsellmaster-field psupsellmaster-text psupsellmaster-required" id="psupsellmaster-reason-better-plugin" name="reason_data[better_plugin][text]" placeholder="<?php esc_attr_e( 'Kindly tell us which plugin did you find.', 'psupsellmaster' ); ?>" type="text" />
								</div>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" name="reason_key" type="radio" value="broke_site" />
									<span><?php esc_html_e( 'The plugin broke my site.', 'psupsellmaster' ); ?></span>
								</label>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" name="reason_key" type="radio" value="stopped_working" />
									<span><?php esc_html_e( 'The plugin suddenly stopped working.', 'psupsellmaster' ); ?></span>
								</label>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" name="reason_key" type="radio" value="no_longer_need" />
									<span><?php esc_html_e( 'I no longer need the plugin.', 'psupsellmaster' ); ?></span>
								</label>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" name="reason_key" type="radio" value="debug" />
									<span><?php esc_html_e( "It's a temporary deactivation. I'm just debugging an issue.", 'psupsellmaster' ); ?></span>
								</label>
							</li>
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-radio psupsellmaster-checkable psupsellmaster-required" data-focus-target="#psupsellmaster-reason-other" data-toggle-target="#psupsellmaster-input-other" name="reason_key" type="radio" value="other" />
									<span><?php esc_html_e( 'Other...', 'psupsellmaster' ); ?></span>
								</label>
								<div class="psupsellmaster-form-input psupsellmaster-hidden" data-display="block" id="psupsellmaster-input-other">
									<input autocomplete="off" class="psupsellmaster-field psupsellmaster-text psupsellmaster-required" id="psupsellmaster-reason-other" name="reason_data[other][text]" placeholder="<?php esc_attr_e( 'Kindly tell us the reason so we can improve.', 'psupsellmaster' ); ?>" type="text" />
								</div>
							</li>
						</ul>
					</section>
				</div>
				<div class="psupsellmaster-modal-footer">
					<div class="psupsellmaster-buttons">
						<button class="button button-secondary psupsellmaster-button psupsellmaster-button-skip" type="button"><?php esc_html_e( 'Skip & Deactivate', 'psupsellmaster' ); ?></button>
						<button class="button button-secondary psupsellmaster-button psupsellmaster-button-submit" type="submit"><?php esc_html_e( 'Submit & Deactivate', 'psupsellmaster' ); ?></button>
						<button class="button button-primary psupsellmaster-button psupsellmaster-button-cancel psupsellmaster-trigger-close-modal" type="button"><?php esc_html_e( 'Cancel', 'psupsellmaster' ); ?></button>
					</div>
				</div>
				<div class="psupsellmaster-backdrop-spinner" style="display: none;">
					<div class="spinner is-active"></div>
				</div>
			</div>
		</form>
	</div>
</div>

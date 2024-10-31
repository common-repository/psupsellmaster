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
						<?php esc_html_e( 'Quick Feedback', 'psupsellmaster' ); ?>
					</h3>
				</div>
				<div class="psupsellmaster-modal-body">
					<section class="psupsellmaster-section psupsellmaster-required">
						<ul class="psupsellmaster-list">
							<li class="psupsellmaster-item">
								<div class="psupsellmaster-form-input">
									<label class="psupsellmaster-label" for="psupsellmaster-reason"><?php esc_html_e( 'Why do you like to deactivate the UpsellMaster Plugin?', 'psupsellmaster' ); ?></label>
									<textarea class="psupsellmaster-field psupsellmaster-text psupsellmaster-required" id="psupsellmaster-reason" name="reason" rows="5"></textarea>
									<div class="psupsellmaster-validation">
										<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
									</div>
								</div>
							</li>
						</ul>
					</section>
					<section class="psupsellmaster-section">
						<h4 class="psupsellmaster-heading"><?php esc_html_e( 'Free Consultation Offer - How to Optimize your Upsells', 'psupsellmaster' ); ?></h4>
						<p class="psupsellmaster-paragraph"><?php esc_html_e( 'We are offering a Free 1/2 hour Zoom Call Consultation where we analyze your Website and show you the untapped Upsell Potential currently presented in your Webstore. Furthermore, we can also show you how to correctly use UpsellMaster to maximize your Upsells. Interested? Simply answer a few questions and we will get back to you.', 'psupsellmaster' ); ?></p>
						<ul class="psupsellmaster-list">
							<li class="psupsellmaster-item">
								<label class="psupsellmaster-label">
									<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable" data-toggle-focus="#psupsellmaster-help-revenue" data-toggle-target="#psupsellmaster-section-help" name="help[yes]" type="checkbox" value="yes" />
									<span><?php esc_html_e( 'I would like to schedule a Free call to analyze the Upside Potential of my Website and get a Demo of UpsellMaster', 'psupsellmaster' ); ?></span>
								</label>
							</li>
						</ul>
					</section>
					<section class="psupsellmaster-section psupsellmaster-hidden" data-display="flex" id="psupsellmaster-section-help">
						<section class="psupsellmaster-section">
							<div class="psupsellmaster-table">
								<div class="psupsellmaster-table-row">
									<div class="psupsellmaster-table-col">
										<label class="psupsellmaster-label" for="psupsellmaster-help-revenue"><strong><?php esc_html_e( 'Monthly $ Revenue from Upsells?', 'psupsellmaster' ); ?></strong></label>
									</div>
									<div class="psupsellmaster-table-col">
										<div class="psupsellmaster-form-input">
											<input autocomplete="off" class="psupsellmaster-field psupsellmaster-text psusellmaster-non-zero-float psupsellmaster-required" id="psupsellmaster-help-revenue" name="help[revenue]" placeholder="<?php echo esc_attr( 'USD/Month' ); ?>" type="text" />
											<div class="psupsellmaster-validation">
												<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="psupsellmaster-table-row">
									<div class="psupsellmaster-table-col">
										<label class="psupsellmaster-label" for="psupsellmaster-help-selection"><strong><?php esc_html_e( 'How do you select your Upsell Offers?', 'psupsellmaster' ); ?></strong></label>
									</div>
									<div class="psupsellmaster-table-col">
										<div class="psupsellmaster-form-input">
											<textarea class="psupsellmaster-field psupsellmaster-text psupsellmaster-required" id="psupsellmaster-help-selection" name="help[selection]" rows="5"></textarea>
											<div class="psupsellmaster-validation">
												<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
						<section class="psupsellmaster-section">
							<h4 class="psupsellmaster-heading"><?php esc_html_e( 'Where do you place Upsell Offers on your Website?', 'psupsellmaster' ); ?></h4>
							<div class="psupsellmaster-validation">
								<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
							</div>
							<ul class="psupsellmaster-list psupsellmaster-locations">
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="product_page" />
										<span><?php esc_html_e( 'Product Page', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="blog_pages" />
										<span><?php esc_html_e( 'Blog Pages', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<?php if ( psupsellmaster_is_plugin_active( 'woo' ) ) : ?>
									<li class="psupsellmaster-item">
										<label class="psupsellmaster-label">
											<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="cart_page" />
											<span><?php esc_html_e( 'Cart Page', 'psupsellmaster' ); ?></span>
										</label>
									</li>
								<?php endif; ?>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="checkout_page" />
										<span><?php esc_html_e( 'Checkout Page', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="receipt_page" />
										<span><?php esc_html_e( 'Purchase Receipt Page', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="add_to_cart_popup" />
										<span><?php esc_html_e( 'Add to Cart Popup', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="add_to_cart_popup" />
										<span><?php esc_html_e( 'Exit Intent Popup', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-required" name="help[locations][]" type="checkbox" value="widget" />
										<span><?php esc_html_e( 'Widgets in Sidebars', 'psupsellmaster' ); ?></span>
									</label>
								</li>
								<li class="psupsellmaster-item">
									<label class="psupsellmaster-label">
										<input class="psupsellmaster-field psupsellmaster-checkbox psupsellmaster-checkable psupsellmaster-exclusive psupsellmaster-required" name="help[locations][]" type="checkbox" value="none" />
										<span><?php esc_html_e( 'None', 'psupsellmaster' ); ?></span>
									</label>
								</li>
							</ul>
							<div class="psupsellmaster-form-input">
								<label class="psupsellmaster-label" for="psupsellmaster-help-comments"><?php esc_html_e( 'Please leave any comment which can be helpful to understand your current Upsell Strategy. How effective do you believe are your current Upsell Offers?', 'psupsellmaster' ); ?></label>
								<textarea class="psupsellmaster-field psupsellmaster-text psupsellmaster-required" id="psupsellmaster-help-comments" name="help[comments]" rows="5"></textarea>
								<div class="psupsellmaster-validation">
									<span><?php esc_html_e( 'Kindly provide the required input.', 'psupsellmaster' ); ?></span>
								</div>
							</div>
						</section>
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

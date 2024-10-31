<?php
/**
 * Admin - Templates - Settings - Upsells.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the algorithm logic.
$algorithm_logic = PsUpsellMaster_Settings::get( 'algorithm_logic' );

// Get the stored price range.
$stored_price_range = $algorithm_logic['price_range'];

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

// Get the product taxonomies.
$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects', false );

// Get the stored priority only.
$stored_priority_only = isset( $algorithm_logic['priority_only'] ) ? filter_var( $algorithm_logic['priority_only'], FILTER_VALIDATE_BOOLEAN ) : false;

// Get the max priorities.
$max_priorities = psupsellmaster_settings_get_max_priorities();

// Set the max weight (1 Trillion).
$max_weight = 1000000000000;
?>
<div>
	<h2 style="marging:0px;"><?php esc_html_e( 'Algorithm Logic', 'psupsellmaster' ); ?></h2>
	<hr />
	<table class="form-table">
		<tbody>

			<?php if ( psupsellmaster_is_plugin_active( 'edd' ) ) : ?>
				<tr valign="top">
					<th scope="row">
						<label for="bundles_only"><?php esc_html_e( 'Bundles Only', 'psupsellmaster' ); ?></label>
					</th>
					<td>
						<fieldset>
							<label for="bundles_only">
								<input name="algorithm_logic[bundles_only]" type="checkbox" id="bundles_only" value="1" <?php echo wp_kses_post( ( 1 === (int) $algorithm_logic['bundles_only'] ) ? 'checked' : '' ); ?> />
								<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( psupsellmaster_is_pro() ) : ?>
				<?php
				// Set the excluded categories.
				$excluded_categories = array();

				// Check if the excluded categories is not empty.
				if ( ! empty( $algorithm_logic['excluded_categories'] ) ) {
					// Set the excluded categories.
					$excluded_categories = array_map( 'intval', $algorithm_logic['excluded_categories'] );
				}

				// Make sure there is at least one item in the list.
				array_push( $excluded_categories, -1 );

				// Get the options.
				$options = psupsellmaster_get_product_category_term_label_value_pairs(
					array( 'include' => $excluded_categories )
				)['items'];
				?>
				<tr valign="top">
					<th scope="row">
						<label for="psupsellmaster_al_excluded_categories"><?php esc_html_e( 'Excluded Categories', 'psupsellmaster' ); ?></label>
					</th>
					<td>
						<select class="psupsellmaster-select2 psupsellmaster-al-excluded-categories" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_category_taxonomy() ); ?>" id="psupsellmaster_al_excluded_categories" multiple="multiple" name="algorithm_logic[excluded_categories][]">
							<?php foreach ( $options as $option ) : ?>
								<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php
				// Get the tags.
				$tag_list = psupsellmaster_get_product_tag_terms()['items'];

				// Set the excluded tags.
				$excluded_tags = array();

				// Check if the excluded tags is not empty.
				if ( ! empty( $algorithm_logic['excluded_tags'] ) ) {
					// Set the excluded tags.
					$excluded_tags = array_map( 'intval', $algorithm_logic['excluded_tags'] );
				}

				// Make sure there is at least one item in the list.
				array_push( $excluded_tags, -1 );

				// Get the options.
				$options = psupsellmaster_get_product_tag_term_label_value_pairs(
					array( 'include' => $excluded_tags )
				)['items'];
				?>
				<tr valign="top">
					<th scope="row">
						<label for="psupsellmaster_al_excluded_tags"><?php esc_html_e( 'Excluded Tags', 'psupsellmaster' ); ?></label>
					</th>
					<td>
						<select class="psupsellmaster-select2 psupsellmaster-al-excluded-tags" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_tag_taxonomy() ); ?>" id="psupsellmaster_al_excluded_tags" multiple="multiple" name="algorithm_logic[excluded_tags][]">
							<?php foreach ( $options as $option ) : ?>
								<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php foreach ( $product_taxonomies as $product_taxonomy ) : ?>
					<?php

					// Check if the taxonomy name is empty.
					if ( empty( $product_taxonomy->name ) ) {
						continue;
					}

					// Check if the taxonomy label are empty.
					if ( empty( $product_taxonomy->label ) ) {
						continue;
					}

					// Get the taxonomy name.
					$product_taxonomy_name = $product_taxonomy->name;

					// Get the taxonomy singular label.
					$product_taxonomy_label = $product_taxonomy->label;

					// Define the excluded terms.
					$excluded_terms = array();

					// Check if the excluded taxonomies is not empty.
					if ( ! empty( $algorithm_logic['excluded_taxonomies'] ) ) {

						// Check if the specific excluded taxonomy is not empty.
						if ( ! empty( $algorithm_logic['excluded_taxonomies'][ $product_taxonomy_name ] ) ) {
							// Set the excluded terms.
							$excluded_terms = array_map( 'intval', $algorithm_logic['excluded_taxonomies'][ $product_taxonomy_name ] );
						}
					}

					// Make sure there is at least one item in the list.
					array_push( $excluded_terms, -1 );

					// Get the options.
					$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
						array(
							'taxonomy' => $product_taxonomy_name,
							'include'  => $excluded_terms,
						)
					)['items'];
					?>
					<tr valign="top">
						<th scope="row">
							<label for="psupsellmaster_al_excluded_<?php echo esc_attr( $product_taxonomy_name ); ?>"><?php echo esc_html( sprintf( 'Excluded %s', $product_taxonomy_label ) ); ?></label>
						</th>
						<td>
							<select class="psupsellmaster-select2 psupsellmaster-al-excluded-taxonomies" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( $product_taxonomy_name ); ?>" id="psupsellmaster_al_excluded_<?php echo esc_attr( $product_taxonomy_name ); ?>" multiple="multiple" name="algorithm_logic[excluded_taxonomies][<?php echo esc_attr( $product_taxonomy_name ); ?>][]">
								<?php foreach ( $options as $option ) : ?>
									<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>

			<tr valign="top">
				<th scope="row">
					<label for="al_price_range_from"><?php esc_html_e( 'Price Range', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="al_price_range_from">
							<?php echo wp_kses_post( psupsellmaster_get_store_currency() ); ?>
							<input type="number" name="algorithm_logic[price_range][from]" id="al_price_range_from" placeholder="<?php esc_attr_e( 'Min', 'psupsellmaster' ); ?>" value="<?php echo esc_attr( $stored_price_range['from'] ); ?>" /> - <?php echo wp_kses_post( psupsellmaster_get_store_currency() ); ?> <input type="number" name="algorithm_logic[price_range][to]" id="al_price_range_to" placeholder="<?php esc_attr_e( 'Max', 'psupsellmaster' ); ?>" value="<?php echo esc_attr( $stored_price_range['to'] ); ?>" />
						</label>
					</fieldset>
				</td>
			</tr>

			<?php if ( psupsellmaster_is_pro() ) : ?>
				<tr valign="top">
					<th scope="row">
						<label for="priority_only"><?php esc_html_e( 'Matching Priorities Only', 'psupsellmaster' ); ?></label>
					</th>
					<td>
						<fieldset>
							<label for="priority_only">
								<input <?php checked( 1, $stored_priority_only ); ?> id="priority_only" name="algorithm_logic[priority_only]" type="checkbox" value="1" />
								<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<tr valign="top">
				<th scope="row">
					<span><?php esc_html_e( 'Priorities', 'psupsellmaster' ); ?></span>
				</th>
				<td>
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
													<input <?php checked( $logarithmic_scale ); ?> class="psupsellmaster-logarithmic-scale" data-max="<?php echo esc_attr( $max_weight ); ?>" name="algorithm_logic[priority_logarithmic_scale]" type="checkbox" />
													<span><?php esc_html_e( 'Enable Logarithmic Scale', 'psupsellmaster' ); ?></span>
													<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The logarithmic scale makes it easy to use large scales using the slider.', 'psupsellmaster' ); ?>"></span>
												</label>
											</div>
										<?php endif; ?>
										<div class="psupsellmaster-table-col-field">
											<label>
												<span><?php esc_html_e( 'Max Weight', 'psupsellmaster' ); ?></span>
												<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'This allows to use smaller ranges and sets the maximum weight when not using the logarithmic scale.', 'psupsellmaster' ); ?>"></span>
												<input autocomplete="off" class="psupsellmaster-max-weight" data-max="<?php echo esc_attr( $max_weight ); ?>" data-min="1" name="algorithm_logic[priority_max_weight]" type="text" value=<?php echo esc_attr( $stored_max_weight ); ?> />
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
										<select class="psupsellmaster-priority-field" id="psupsellmaster-priority-criteria-<?php echo esc_attr( $i ); ?>" name="algorithm_logic[priority][]">
											<option value=""><?php esc_html_e( 'Select the Priority', 'psupsellmaster' ); ?></option>
											<?php foreach ( $priorities as $priority_key => $priority_label ) : ?>
												<?php $stored_priority = isset( $stored_priorities[ $i ] ) ? $stored_priorities[ $i ] : null; ?>
												<option <?php selected( $stored_priority, $priority_key ); ?> value="<?php echo esc_attr( $priority_key ); ?>"><?php echo esc_html( $priority_label ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-number">
										<label class="psupsellmaster-priority-label-mobile" for="psupsellmaster-priority-weight-number-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Weight', 'psupsellmaster' ); ?></label>
										<input autocomplete="off" class="psupsellmaster-range-number psupsellmaster-priority-field" data-max="<?php echo esc_attr( $max_weight ); ?>" data-min="0" id="psupsellmaster-priority-weight-number-<?php echo esc_attr( $i ); ?>" name="algorithm_logic[weight_factor][]" type="text" value=<?php echo esc_attr( $stored_factor ); ?> />
									</div>
									<div class="psupsellmaster-table-col psupsellmaster-table-col-weight-slider">
										<input class="psupsellmaster-range-slider psupsellmaster-priority-field" data-log-max="13" data-log-min="0" data-log-step="1" data-normal-max="<?php echo esc_attr( $max_weight ); ?>" data-normal-min="0" type="range" />
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
				</td>
			</tr>
		</tbody>
	</table>
</div>

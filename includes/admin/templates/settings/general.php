<?php
/**
 * Admin - Templates - Settings - General.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the number of upsell.
$number_of_upsell = PsUpsellMaster_Settings::get( 'number_of_upsell_products' );

// Get the allowed max upsells.
$allowed_max_upsells = psupsellmaster_settings_get_max_upsells();

// Check if the limit has been reached.
if ( $number_of_upsell > $allowed_max_upsells ) {
	// Set the number of upsell.
	$number_of_upsell = $allowed_max_upsells;
}

// Get the data.
$recalculation_interval = PsUpsellMaster_Settings::get( 'recalculation_interval' );
$cleandata_interval     = PsUpsellMaster_Settings::get( 'cleandata_interval' );

// Get the batch size.
$batch_size = PsUpsellMaster_Settings::get( 'limit' );

// Check if this is the lite version.
if ( psupsellmaster_is_lite() ) {
	// Get the base products limit.
	$base_products_limit = psupsellmaster_get_feature_limit( 'base_products_count' );

	// Check if the limit has been reached.
	if ( $batch_size > $base_products_limit ) {
		// Set the batch size.
		$batch_size = $base_products_limit;
	}
}

// Get the author information.
$author_information = PsUpsellMaster_Settings::get( 'author_information' );

$auto_calculate_for_new_product = PsUpsellMaster_Settings::get( 'auto_calculate_for_new_product' );

$auto_calculate_on_product_update = PsUpsellMaster_Settings::get( 'auto_calculate_on_product_update' );

$add_rel_nofollow = PsUpsellMaster_Settings::get( 'add_rel_nofollow' );

$remove_data = PsUpsellMaster_Settings::get( 'remove_data' );

$interval_values = array(
	__( 'Daily', 'psupsellmaster' ),
	__( 'Weekly', 'psupsellmaster' ),
	__( 'Monthly', 'psupsellmaster' ),
	__( 'Never', 'psupsellmaster' ),
);

$cleanup_interval_values = array(
	__( '1 Month', 'psupsellmaster' ),
	__( '2 Months', 'psupsellmaster' ),
	__( '3 Months', 'psupsellmaster' ),
	__( '6 Months', 'psupsellmaster' ),
	__( '1 Year', 'psupsellmaster' ),
	__( '2 Years', 'psupsellmaster' ),
	__( '3 Years', 'psupsellmaster' ),
	__( 'Keep All', 'psupsellmaster' ),
);

$author_information_options = array(
	'all'   => __( 'Show Author', 'psupsellmaster' ),
	'image' => __( 'Show Image Only', 'psupsellmaster' ),
	'name'  => __( 'Show Name Only', 'psupsellmaster' ),
	'none'  => __( 'Hide Author', 'psupsellmaster' ),
);

// Get the max upsells.
$max_upsells = psupsellmaster_settings_get_max_upsells();
?>
<div>
	<h2 style="marging:0px;"><?php esc_html_e( 'General Settings', 'psupsellmaster' ); ?></h2>
	<hr />
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_number_of_upsell_products"><?php esc_html_e( 'Number of Upsells', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" id="psupsellmaster_number_of_upsell_products" name="number_of_upsell_products" value="<?php echo esc_attr( $number_of_upsell ); ?>" class="regular-text" max="<?php echo esc_attr( $max_upsells ); ?>" />
					<p class="description"><?php esc_html_e( 'Please define how many upsell products you wish to present per product', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<?php if ( psupsellmaster_is_pro() ) : ?>
				<tr valign="top">
					<th scope="row">
						<label for="psupsellmaster_recalculation_interval"><?php esc_html_e( 'Recalculation Interval', 'psupsellmaster' ); ?></label>
					</th>
					<td>
						<select class="regular-text" id="psupsellmaster_recalculation_interval" name="recalculation_interval">
							<?php

							foreach ( $interval_values as $interval ) {
								$interval_value = sanitize_title( $interval );
								$selected       = '';

								if ( $recalculation_interval === $interval_value ) {
									$selected = 'selected="selected"';
								}

								echo '<option value="' . esc_attr( $interval_value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $interval ) . '</option>';
							}

							?>
						</select>
						<p class="description"><?php esc_html_e( 'Please note: once you will change this value and save your settings, it will remove previous cron schedule for upsell recalculation and add new recalculation cron schedules as per selected value.', 'psupsellmaster' ); ?></p>
					</td>
				</tr>
			<?php endif; ?>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_limit"><?php esc_html_e( 'Batch size', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" id="psupsellmaster_limit" name="limit" value="<?php echo esc_attr( $batch_size ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Number of products to be processed in single call while calculating upsells', 'psupsellmaster' ); ?></p>
					<?php if ( psupsellmaster_is_lite() && ! psupsellmaster_is_newsletter_subscribed() ) : ?>
						<p class="psupsellmaster-text-green">
							<strong>
								<?php
								printf(
									'%s <a class="psupsellmaster-trigger-open-modal" data-target="#psupsellmaster-modal-newsletter" href="%s">%s</a>.',
									esc_html__( 'Limited to 50 Upsells? Upgrade for free! Calculate up to 300 Upsells by', 'psupsellmaster' ),
									'#psupsellmaster_limit',
									esc_html__( 'Signing-up to our Newsletter', 'psupsellmaster' ),
								);
								?>
							</strong>
						</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_cleandata_interval"><?php esc_html_e( 'Keep Upsell Data For', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" <?php disabled( psupsellmaster_is_lite() ); ?> id="psupsellmaster_cleandata_interval" name="cleandata_interval">
						<?php
						foreach ( $cleanup_interval_values as $interval ) {
							$interval_value = sanitize_title( $interval );
							$selected       = '';

							if ( $cleandata_interval === $interval_value ) {
								$selected = 'selected="selected"';
							}

							echo '<option value="' . esc_attr( $interval_value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $interval ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Auto clean up upsell data periodically', 'psupsellmaster' ); ?></p>
					<?php if ( psupsellmaster_is_lite() ) : ?>
						<p class="psupsellmaster-text-green">
							<strong>
								<?php
								/* translators: 1: message, 2: PRO version URL, 3: message. */
								printf(
									'%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a>',
									esc_html__( 'Want to keep Upsell Data forever?', 'psupsellmaster' ),
									esc_url( PSUPSELLMASTER_PRODUCT_URL ),
									esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
								);
								?>
							</strong>
						</p>
					<?php elseif ( psupsellmaster_is_pro() ) : ?>
						<a class="thickbox psupsellmaster-btn-clear-results" href="#TB_inline?&inlineId=psupsellmaster-modal-action-clear-results" title="<?php esc_attr_e( 'Please confirm the action', 'psupsellmaster' ); ?>"><?php esc_html_e( 'Clear Results', 'psupsellmaster' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_default_upsell_products"><?php esc_html_e( 'Default Upsells', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<?php
					// Set the extra attributes.
					$extra_attributes = '';

					// Check if this is the lite version.
					if ( psupsellmaster_is_lite() ) {
						// Get the limit.
						$limit_default_upsells = psupsellmaster_get_feature_limit( 'default_upsells' );

						// Set the extra attributes.
						$extra_attributes = array();

						// Set the extra attribute.
						$extra_attribute = 'data-multiple-limit=' . esc_attr( $limit_default_upsells );

						// Add the extra attribute to the list.
						array_push( $extra_attributes, $extra_attribute );

						// Set the extra attributes.
						$extra_attributes = implode( ' ', $extra_attributes );
					}

					// Get the default upsells.
					$default_upsells = PsUpsellMaster_Settings::get( 'default_upsell_products' );
					$default_upsells = is_array( $default_upsells ) ? array_map( 'intval', $default_upsells ) : array();

					// Make sure there is at least one item in the list.
					array_push( $default_upsells, -1 );

					// Get the options.
					$options = psupsellmaster_get_product_label_value_pairs(
						array( 'post__in' => $default_upsells )
					)['items'];
					?>
					<select class="psupsellmaster-select2" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" <?php echo wp_kses_post( $extra_attributes ); ?> data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="psupsellmaster_default_upsell_products" multiple="multiple" name="default_upsell_products[]">
						<?php foreach ( $options as $option ) : ?>
							<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Will be presented in case no other upsell or visited products are available', 'psupsellmaster' ); ?></p>
					<?php if ( psupsellmaster_is_lite() ) : ?>
						<p class="psupsellmaster-text-green">
							<strong>
								<?php
								/* translators: 1: Text, 2: Text, 3: PRO version URL, 4: Text. */
								printf(
									'%s %s: <a class="psupsellmaster-link" href="%s" target="_blank">%s</a>',
									esc_html__( 'Need to add more Upsells?', 'psupsellmaster' ),
									esc_html__( 'Unlock unlimited Default Upsells', 'psupsellmaster' ),
									esc_url( PSUPSELLMASTER_PRODUCT_URL ),
									esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
								);
								?>
							</strong>
						</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_author_information"><?php esc_html_e( 'Author Information', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_author_information" name="author_information">
						<?php foreach ( $author_information_options as $value => $label ) : ?>
							<option <?php selected( $author_information, $value ); ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Define whether to show or hide author information for each product in product lists and carousels.', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_auto_calculate_for_new_product"><?php esc_html_e( 'Auto calculate Upsells for newly published Products', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_auto_calculate_for_new_product">
							<input name="auto_calculate_for_new_product" type="checkbox" id="psupsellmaster_auto_calculate_for_new_product" value="1" <?php echo wp_kses_post( ( 1 === (int) $auto_calculate_for_new_product ) ? 'checked="checked"' : '' ); ?> />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_auto_calculate_on_product_update"><?php esc_html_e( 'Auto calculate Upsells upon Product Update', 'psupsellmaster' ); ?></label>
					<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Recalculating Upsells on every update leads to high resource usage, especially when using complex algorithms. We recommend disabling the auto recalculation upon update.', 'psupsellmaster' ); ?>"></span>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_auto_calculate_on_product_update">
							<input name="auto_calculate_on_product_update" type="checkbox" id="psupsellmaster_auto_calculate_on_product_update" value="1" <?php echo wp_kses_post( ( 1 === (int) $auto_calculate_on_product_update ) ? 'checked="checked"' : '' ); ?> />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_rel_nofollow"><?php esc_html_e( 'Add "nofollow" to all links', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_add_rel_nofollow">
							<input <?php checked( 1, $add_rel_nofollow ); ?> name="add_rel_nofollow" type="checkbox" id="psupsellmaster_add_rel_nofollow" value="1" />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
					<p class="description"><?php esc_html_e( 'Adds the rel="nofollow" HTML attribute to all links in this plugin\'s product lists/carousels.', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_remove_data"><?php esc_html_e( 'Remove Data on Uninstall', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_remove_data">
							<input <?php checked( 1, $remove_data ); ?> name="remove_data" type="checkbox" id="psupsellmaster_remove_data" value="1" />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
					<p class="description"><?php esc_html_e( 'Remove all data when the plugin is deleted.', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
</div>

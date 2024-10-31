<?php
/**
 * Admin - Templates - Settings - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the settings.
$settings = PsUpsellMaster_Settings::get( 'campaigns' );

?>
<div class="psupsellmaster-campaigns">
	<h2 style="marging:0px;"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></h2>
	<hr />
	<h3><?php esc_html_e( 'Prices', 'psupsellmaster' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="campaigns-prices-discount-text"><?php esc_html_e( 'Discount Text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<?php
					// Set the field id.
					$field_id = 'campaigns-prices-discount-text';

					// Set the field name.
					$field_name = 'campaigns[prices_discount_text]';

					// Get the field value.
					$field_value = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

					// Set the field settings.
					$field_settings = array(
						'editor_class'  => 'psupsellmaster-field psupsellmaster-field-discount-text',
						'media_buttons' => false,
						'textarea_name' => $field_name,
						'textarea_rows' => 1,
						'wpautop'       => true,
					);

					// Output the editor.
					wp_editor(
						stripslashes( $field_value ),
						$field_id,
						$field_settings,
					);
					?>
					<p class="description">
						<span><?php esc_html_e( 'Please enter the text that will be displayed in prices for products included in campaigns.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( 'Available tags:', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{old_price} - Displays the old product price (original price, without discounts).', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{new_price} - Displays the new product price (discounted price, with discounts).', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{discount_amount} - Displays the discount amount, either % or $ as per the campaign settings.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php esc_html_e( 'Triggers', 'psupsellmaster' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<?php
				// Get the setting.
				$conditions_products_count_type = isset( $settings['conditions_products_count_type'] ) ? $settings['conditions_products_count_type'] : false;
				$conditions_products_count_type = in_array( $conditions_products_count_type, array( 'distinct_products', 'total_products' ), true ) ? $conditions_products_count_type : 'distinct_products';
				?>
				<th scope="row">
					<label for="psupsellmaster-campaigns-conditions-products-count-type"><?php esc_html_e( 'Products Qty Type', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster-campaigns-conditions-products-count-type" name="campaigns[conditions_products_count_type]">
						<option <?php selected( $conditions_products_count_type, 'distinct_products' ); ?> value="distinct_products"><?php esc_html_e( 'Distinct Products Quantity', 'psupsellmaster' ); ?></option>
						<option <?php selected( $conditions_products_count_type, 'total_products' ); ?> value="total_products"><?php esc_html_e( 'Total Shopping Cart Quantity', 'psupsellmaster' ); ?></option>
					</select>
					<p class="description">
						<span><?php esc_html_e( 'Please specify whether the product count should be based on distinct products or the total shopping cart quantity.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="campaigns-conditions-products-min-text"><?php esc_html_e( 'Min Products Qty Text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<?php
					// Set the field id.
					$field_id = 'campaigns-conditions-products-min-text';

					// Set the field name.
					$field_name = 'campaigns[conditions_products_min_text]';

					// Get the field value.
					$field_value = isset( $settings['conditions_products_min_text'] ) ? $settings['conditions_products_min_text'] : '';

					// Set the field settings.
					$field_settings = array(
						'editor_class'  => 'psupsellmaster-field psupsellmaster-field-products-min-text',
						'media_buttons' => false,
						'textarea_name' => $field_name,
						'textarea_rows' => 1,
						'wpautop'       => true,
					);

					// Output the editor.
					wp_editor(
						stripslashes( $field_value ),
						$field_id,
						$field_settings,
					);
					?>
					<p class="description">
						<span><?php esc_html_e( 'Please enter the text that will be displayed at checkout when the customer has not yet met the minimum product quantity condition from eligible campaigns.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( 'Available tags:', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{cart_quantity} - Displays the current shopping cart product quantity.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{min_quantity} - Displays the minimum product quantity as per the campaign settings.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{gap_quantity} - Displays the difference between the minimum and the current product quantity in the shopping cart.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{discount_amount} - Displays the discount amount, either % or $ as per the campaign settings.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{coupon_code} - Displays the coupon code as per the campaign settings.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="campaigns-conditions-subtotal-min-text"><?php esc_html_e( 'Min Subtotal Text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<?php
					// Set the field id.
					$field_id = 'campaigns-conditions-subtotal-min-text';

					// Set the field name.
					$field_name = 'campaigns[conditions_subtotal_min_text]';

					// Get the field value.
					$field_value = isset( $settings['conditions_subtotal_min_text'] ) ? $settings['conditions_subtotal_min_text'] : '';

					// Set the field settings.
					$field_settings = array(
						'editor_class'  => 'psupsellmaster-field psupsellmaster-field-subtotal-min-text',
						'media_buttons' => false,
						'textarea_name' => $field_name,
						'textarea_rows' => 1,
						'wpautop'       => true,
					);

					// Output the editor.
					wp_editor(
						stripslashes( $field_value ),
						$field_id,
						$field_settings,
					);
					?>
					<p class="description">
						<span><?php esc_html_e( 'Please enter the text that will be displayed at checkout when the customer has not yet met the minimum subtotal condition from eligible campaigns.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( 'Available tags:', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{cart_subtotal} - Displays the current shopping cart subtotal.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{min_subtotal} - Displays the minimum subtotal as per the campaign settings.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{gap_subtotal} - Displays the difference between the minimum and the current subtotal in the shopping cart.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{discount_amount} - Displays the discount amount, either % or $ as per the campaign settings.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{coupon_code} - Displays the coupon code as per the campaign settings.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php esc_html_e( 'Coupons', 'psupsellmaster' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<?php
				// Get the setting.
				$coupons_allow_mix = isset( $settings['coupons_allow_mix'] ) ? filter_var( $settings['coupons_allow_mix'], FILTER_VALIDATE_INT ) : false;
				$coupons_allow_mix = in_array( $coupons_allow_mix, array( 0, 1 ), true ) ? $coupons_allow_mix : 1;
				?>
				<th scope="row">
					<label for="psupsellmaster-coupons-allow-mix"><?php esc_html_e( 'Allow Mixed Coupons', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster-coupons-allow-mix">
							<input <?php checked( 1, $coupons_allow_mix ); ?> id="psupsellmaster-coupons-allow-mix" name="campaigns[coupons_allow_mix]" type="checkbox" value="1" />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
					<p class="description">
						<span><?php esc_html_e( 'Allow Campaign Coupons to be used in conjunction with other Standard Coupons.', 'psupsellmaster' ); ?></span>
						<?php if ( psupsellmaster_is_plugin_active( 'edd' ) ) : ?>
							<?php
							// Get the EDD settings url.
							$edd_settings_url = admin_url( 'edit.php?post_type=download&page=edd-settings&tab=marketing' );
							?>
							<span>
								<?php
								printf(
									'%s <a href="%s" target="_blank">%s</a> %s.',
									esc_html__( 'Please note this only applies when', 'psupsellmaster' ),
									esc_url( $edd_settings_url ),
									esc_html__( 'Multiple Discounts (Coupons)', 'psupsellmaster' ),
									esc_html__( 'is allowed', 'psupsellmaster' )
								);
								?>
							</span>
						<?php endif; ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<?php
				// Get the setting.
				$coupons_multiple_behavior = isset( $settings['coupons_multiple_behavior'] ) ? $settings['coupons_multiple_behavior'] : false;
				$coupons_multiple_behavior = in_array( $coupons_multiple_behavior, array( 'all', 'campaigns', 'standard' ), true ) ? $coupons_multiple_behavior : 'all';
				?>
				<th scope="row">
					<label for="psupsellmaster-coupons-multiple-behavior"><?php esc_html_e( 'Multiple Coupons Behavior', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster-coupons-multiple-behavior" name="campaigns[coupons_multiple_behavior]">
						<option <?php selected( $coupons_multiple_behavior, 'all' ); ?> value="all"><?php esc_html_e( 'Allow for All Coupons', 'psupsellmaster' ); ?></option>
						<option <?php selected( $coupons_multiple_behavior, 'campaigns' ); ?> value="campaigns"><?php esc_html_e( 'Allow for Campaign Coupons only', 'psupsellmaster' ); ?></option>
					</select>
					<p class="description">
						<span><?php esc_html_e( 'Please specify whether multiple coupons are allowed for All Coupons or exclusively for Campaign Coupons.', 'psupsellmaster' ); ?></span>
						<?php if ( psupsellmaster_is_plugin_active( 'edd' ) ) : ?>
							<?php
							// Get the EDD settings url.
							$edd_settings_url = admin_url( 'edit.php?post_type=download&page=edd-settings&tab=marketing' );
							?>
							<span>
								<?php
								printf(
									'%s <a href="%s" target="_blank">%s</a> %s.',
									esc_html__( 'Please note this only applies when', 'psupsellmaster' ),
									esc_url( $edd_settings_url ),
									esc_html__( 'Multiple Discounts (Coupons)', 'psupsellmaster' ),
									esc_html__( 'is allowed', 'psupsellmaster' )
								);
								?>
							</span>
						<?php endif; ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php esc_html_e( 'Checkout Page', 'psupsellmaster' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="campaigns-page-checkout-discount-text"><?php esc_html_e( 'Discount Text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<?php
					// Set the field id.
					$field_id = 'campaigns-page-checkout-discount-text';

					// Set the field name.
					$field_name = 'campaigns[page_checkout_discount_text]';

					// Get the field value.
					$field_value = isset( $settings['page_checkout_discount_text'] ) ? $settings['page_checkout_discount_text'] : '';

					// Set the field settings.
					$field_settings = array(
						'editor_class'  => 'psupsellmaster-field psupsellmaster-field-discount-text',
						'media_buttons' => false,
						'textarea_name' => $field_name,
						'textarea_rows' => 1,
						'wpautop'       => true,
					);

					// Output the editor.
					wp_editor(
						stripslashes( $field_value ),
						$field_id,
						$field_settings,
					);
					?>
					<p class="description">
						<span><?php esc_html_e( 'Please enter the text that will be displayed in prices for products included in campaigns.', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( 'Available tags:', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{old_price} - Displays the old product price (original price, without discounts).', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{new_price} - Displays the new product price (discounted price, with discounts).', 'psupsellmaster' ); ?></span>
						<br />
						<span><?php esc_html_e( '{discount_amount} - Displays the discount amount, either % or $ as per the campaign settings.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php esc_html_e( 'Product Page', 'psupsellmaster' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<?php
				// Get the setting.
				$page_product_banner_position = isset( $settings['page_product_banner_position'] ) ? $settings['page_product_banner_position'] : false;
				$page_product_banner_position = in_array( $page_product_banner_position, array( 'content_before', 'content_after', 'excerpt_before', 'excerpt_after', 'none' ), true ) ? $page_product_banner_position : 'content_before';
				?>
				<th scope="row">
					<label for="psupsellmaster-page-product-banner-position"><?php esc_html_e( 'Banner Position', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster-page-product-banner-position" name="campaigns[page_product_banner_position]">
						<option <?php selected( $page_product_banner_position, 'content_before' ); ?> value="content_before"><?php esc_html_e( 'Before the Content', 'psupsellmaster' ); ?></option>
						<option <?php selected( $page_product_banner_position, 'content_after' ); ?> value="content_after"><?php esc_html_e( 'After the Content', 'psupsellmaster' ); ?></option>
						<option <?php selected( $page_product_banner_position, 'excerpt_before' ); ?> value="excerpt_before"><?php esc_html_e( 'Before the Excerpt', 'psupsellmaster' ); ?></option>
						<option <?php selected( $page_product_banner_position, 'excerpt_after' ); ?> value="excerpt_after"><?php esc_html_e( 'After the Excerpt', 'psupsellmaster' ); ?></option>
						<option <?php selected( $page_product_banner_position, 'none' ); ?> value="none"><?php esc_html_e( 'None', 'psupsellmaster' ); ?></option>
					</select>
					<p class="description">
						<span><?php esc_html_e( 'Please specify if and where the Campaign Banner will be displayed on the Product page.', 'psupsellmaster' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</div>

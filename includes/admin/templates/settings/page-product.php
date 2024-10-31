<?php
/**
 * Admin - Templates - Settings - Product Page.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the data.
$product_page_enable                  = PsUpsellMaster_Settings::get( 'product_page_enable' );
$product_page_display_type            = PsUpsellMaster_Settings::get( 'product_page_display_type' );
$product_page_show_type               = PsUpsellMaster_Settings::get( 'product_page_show_type' );
$product_page_label_title             = PsUpsellMaster_Settings::get( 'product_page_label_title' );
$product_page_label_cta_text          = PsUpsellMaster_Settings::get( 'product_page_label_cta_text' );
$product_page_max_cols                = PsUpsellMaster_Settings::get( 'product_page_max_cols' );
$product_page_max_prod                = PsUpsellMaster_Settings::get( 'product_page_max_prod' );
$product_page_max_per_author          = PsUpsellMaster_Settings::get( 'product_page_max_per_author' );
$product_page_addtocart_button        = PsUpsellMaster_Settings::get( 'product_page_addtocart_button' );
$product_page_title_length            = PsUpsellMaster_Settings::get( 'product_page_title_length' );
$product_page_short_description_limit = PsUpsellMaster_Settings::get( 'product_page_short_description_limit' );
$product_page_edd_position            = PsUpsellMaster_Settings::get( 'product_page_edd_position' );

$edd_position_options = array(
	1 => __( 'Position 1', 'psupsellmaster' ),
	2 => __( 'Position 2', 'psupsellmaster' ),
);

$add_to_cart_options = array(
	'highest-price-only' => __( 'Highest Price Only', 'psupsellmaster' ),
	'all-prices'         => __( 'All Prices', 'psupsellmaster' ),
);

$display_options = array(
	'carousel' => __( 'Carousel', 'psupsellmaster' ),
	'list'     => __( 'List', 'psupsellmaster' ),
);

$show_options = array(
	'upsells' => __( 'Upsells', 'psupsellmaster' ),
	'visits'  => __( 'Recently Viewed Products', 'psupsellmaster' ),
);

?>
<div class="psupsellmaster-fields-container">
	<h2 style="marging:0px;"><?php esc_html_e( 'Product Page', 'psupsellmaster' ); ?></h2>
	<hr />
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_enable"><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_product_page_enable">
							<input class="psupsellmaster-trigger-slide-toggle" data-target-slide-toggle=".psupsellmaster-data-fields-container" id="psupsellmaster_product_page_enable" name="product_page_enable" type="checkbox" value="1" <?php echo wp_kses_post( ( 1 === (int) $product_page_enable ) ? 'checked' : '' ); ?> />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table psupsellmaster-data-fields-container" <?php echo wp_kses_post( ( 1 !== (int) $product_page_enable ) ? 'style="display: none"' : '' ); ?>>
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_display_type"><?php esc_html_e( 'Display type', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_display_type" name="product_page_display_type">
						<?php

						foreach ( $display_options as $display_option_value => $display_option ) {
							$selected = '';

							if ( $product_page_display_type === $display_option_value ) {
								$selected = 'selected="selected"';
							}

							echo '<option value="' . esc_attr( $display_option_value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $display_option ) . '</option>';
						}

						?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_campaigns"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<p class="psupsellmaster-paragraph"><?php esc_html_e( 'List of active and scheduled campaigns:', 'psupsellmaster' ); ?></p>
					<?php echo psupsellmaster_campaigns_render_planned_list(); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_show_type"><?php esc_html_e( 'Show', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_show_type" name="product_page_show_type">
						<?php

						foreach ( $show_options as $show_option_value => $show_option ) {
							$selected = '';

							if ( $product_page_show_type === $show_option_value ) {
								$selected = 'selected="selected"';
							}

							echo '<option value="' . esc_attr( $show_option_value ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $show_option ) . '</option>';
						}

						?>
					</select>
					<p class="description">
						<?php
						/* translators: 1: Text, 2: Upsell Products Link URL, 3: Upsell Products Link Text, 4: Text, 5: Settings Link URL, 6: Settings Link Text. */
						printf(
							'%1$s <a class="psupsellmaster-link" href="%2$s" target="_blank">%3$s</a> %4$s <a class="psupsellmaster-link" href="%5$s" target="_blank">%6$s</a>.',
							esc_html__( 'You can either display Recently Viewed Products as of each individual Visitor or the carefully selected', 'psupsellmaster' ),
							esc_url( admin_url( 'admin.php?page=psupsellmaster-products' ) ),
							esc_html__( 'best suitable Upsells', 'psupsellmaster' ),
							esc_html__( 'as per our', 'psupsellmaster' ),
							esc_url( admin_url( 'admin.php?page=psupsellmaster-settings&view=upsells' ) ),
							esc_html__( 'selection algorithm', 'psupsellmaster' )
						);
						?>
					</p>
					<p class="description">
						<strong><?php esc_html_e( 'Please note that products from planned campaigns might be shown instead of products from this selection.', 'psupsellmaster' ); ?></strong>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_label_title"><?php esc_html_e( 'Title', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_product_page_label_title" name="product_page_label_title" value="<?php echo esc_attr( $product_page_label_title ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Please choose the text labels to be displayed', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_label_cta_text"><?php esc_html_e( 'Call to action text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_product_page_label_cta_text" name="product_page_label_cta_text" value="<?php echo esc_attr( $product_page_label_cta_text ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Please choose the text labels to be displayed', 'psupsellmaster' ); ?></p>
				</td>
			</tr>

			<tr id="psupsellmaster_product_page_products_per_page_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_products_per_page"><?php esc_html_e( 'Max Columns', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_products_per_page" name="product_page_max_cols">
						<?php for ( $i = 1; $i <= 8; $i++ ) : ?>
							<option <?php selected( intval( $product_page_max_cols ), $i ); ?> value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>

			<tr id="psupsellmaster_product_page_carousel_products_per_page_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_carousel_products_per_page"><?php esc_html_e( 'Max Products in Carousel', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_carousel_products_per_page" name="product_page_max_prod">
						<?php

						for ( $i = 1; $i <= 50; $i++ ) {
							$selected = '';

							if ( intval( $product_page_max_prod ) === $i ) {
								$selected = 'selected="selected"';
							}

							echo '<option value="' . esc_attr( $i ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $i ) . '</option>';
						}

						?>
					</select>
				</td>
			</tr>

			<tr id="psupsellmaster_product_page_products_per_author_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_products_per_author"><?php esc_html_e( 'Max Products per Author', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_products_per_author" name="product_page_max_per_author">

						<?php for ( $i = 0; $i <= 10; $i++ ) : ?>
							<option <?php selected( intval( $product_page_max_per_author ), $i ); ?> value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( ( 0 === $i ? __( 'Unlimited' ) : $i ) ); ?></option>
						<?php endfor; ?>

					</select>
				</td>
			</tr>

			<tr id="psupsellmaster_product_page_addtocart_button_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_product_page_addtocart_button"><?php esc_html_e( 'Add to Cart Button', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_product_page_addtocart_button" name="product_page_addtocart_button">
						<?php

						foreach ( $add_to_cart_options as $key => $value ) {
							$selected = '';

							if ( $product_page_addtocart_button === $key ) {
								$selected = 'selected="selected"';
							}

							echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
						}

						?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="product_page_title_length"><?php esc_html_e( 'Title Length', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" class="regular-text" id="product_page_title_length" name="product_page_title_length" value="<?php echo esc_attr( $product_page_title_length ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="product_page_short_description_limit"><?php esc_html_e( 'Description Length', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" class="regular-text" id="product_page_short_description_limit" name="product_page_short_description_limit" value="<?php echo esc_attr( $product_page_short_description_limit ); ?>">
				</td>
			</tr>

			<?php if ( defined( 'EDD_VERSION' ) ) : ?>
				<tr id="psupsellmaster_product_page_edd_position_tr" valign="top">
					<th scope="row">
						<label for="psupsellmaster_product_page_edd_position"><?php esc_html_e( 'Position', 'psupsellmaster' ); ?></label>
						<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Select the position where to show the List/Carousel on the Product Page. Depending on WordPress Theme, some Themes work better with position 1 than 2.', 'psupsellmaster' ); ?>"></span>
					</th>
					<td>
						<select class="regular-text" id="psupsellmaster_product_page_edd_position" name="product_page_edd_position">
							<?php

							for ( $i = 1; $i <= 2; $i++ ) {
								$selected = '';

								if ( intval( $product_page_edd_position ) === $i ) {
									$selected = 'selected="selected"';
								}

								echo '<option value="' . esc_attr( $i ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $edd_position_options[ $i ] ) . '</option>';
							}

							?>
						</select>
					</td>
				</tr>
			<?php endif; ?>

		</tbody>
	</table>
</div>
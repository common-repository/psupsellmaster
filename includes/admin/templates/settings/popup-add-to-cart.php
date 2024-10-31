<?php
/**
 * Admin - Templates - Settings - Add to Cart Popup.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the data.
$add_to_cart_popup_enable                  = PsUpsellMaster_Settings::get( 'add_to_cart_popup_enable' );
$add_to_cart_popup_display_type            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_display_type' );
$add_to_cart_popup_show_type               = PsUpsellMaster_Settings::get( 'add_to_cart_popup_show_type' );
$add_to_cart_popup_headline                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_headline' );
$add_to_cart_popup_tagline                 = PsUpsellMaster_Settings::get( 'add_to_cart_popup_tagline' );
$add_to_cart_popup_button_checkout         = PsUpsellMaster_Settings::get( 'add_to_cart_popup_button_checkout' );
$add_to_cart_popup_label_title             = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_title' );
$add_to_cart_popup_label_cta_text          = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_cta_text' );
$add_to_cart_popup_max_cols                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_cols' );
$add_to_cart_popup_max_prod                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_prod' );
$add_to_cart_popup_max_per_author          = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_per_author' );
$add_to_cart_popup_addtocart_button        = PsUpsellMaster_Settings::get( 'add_to_cart_popup_addtocart_button' );
$add_to_cart_popup_title_length            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_title_length' );
$add_to_cart_popup_short_description_limit = PsUpsellMaster_Settings::get( 'add_to_cart_popup_short_description_limit' );

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
	<h2 style="marging:0px;"><?php esc_html_e( 'Add to Cart Popup', 'psupsellmaster' ); ?></h2>
	<hr />
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_enable"><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<fieldset>
						<label for="psupsellmaster_add_to_cart_popup_enable">
							<input class="psupsellmaster-trigger-slide-toggle" data-target-slide-toggle=".psupsellmaster-data-fields-container" id="psupsellmaster_add_to_cart_popup_enable" name="add_to_cart_popup_enable" type="checkbox" value="1" <?php echo wp_kses_post( ( 1 === intval( $add_to_cart_popup_enable ) ? 'checked' : '' ) ); ?> />
							<?php esc_html_e( 'Yes', 'psupsellmaster' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table psupsellmaster-data-fields-container" <?php echo wp_kses_post( ( 1 !== intval( $add_to_cart_popup_enable ) ? 'style="display: none"' : '' ) ); ?>>
		<tbody>
			<?php
			// Get the excluded pages.
			$excluded_pages = PsUpsellMaster_Settings::get( 'add_to_cart_popup_excluded_pages' );
			$excluded_pages = is_array( $excluded_pages ) ? array_map( 'intval', $excluded_pages ) : array();

			// Make sure there is at least one item in the list.
			array_push( $excluded_pages, -1 );

			// Get the options.
			$options = psupsellmaster_get_page_label_value_pairs(
				array( 'post__in' => $excluded_pages )
			)['items'];
			?>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_excluded_pages"><?php esc_html_e( 'Excluded Pages', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="psupsellmaster-select2" data-ajax-action="psupsellmaster_get_pages" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-pages' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="psupsellmaster_add_to_cart_popup_excluded_pages" multiple="multiple" name="add_to_cart_popup_excluded_pages[]">
						<?php foreach ( $options as $option ) : ?>
							<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_display_type"><?php esc_html_e( 'Display type', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_display_type" name="add_to_cart_popup_display_type">

						<?php foreach ( $display_options as $display_option_value => $display_option ) : ?>
							<option <?php selected( $add_to_cart_popup_display_type, $display_option_value ); ?> value="<?php echo esc_attr( $display_option_value ); ?>"><?php echo esc_html( $display_option ); ?></option>
						<?php endforeach; ?>

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
					<label for="psupsellmaster_add_to_cart_popup_show_type"><?php esc_html_e( 'Show', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_show_type" name="add_to_cart_popup_show_type">

						<?php foreach ( $show_options as $show_option_value => $show_option ) : ?>
							<option <?php selected( $add_to_cart_popup_show_type, $show_option_value ); ?> value="<?php echo esc_attr( $show_option_value ); ?>"><?php echo esc_html( $show_option ); ?></option>
						<?php endforeach; ?>

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
					<label for="psupsellmaster_add_to_cart_popup_headline"><?php esc_html_e( 'Headline', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_add_to_cart_popup_headline" name="add_to_cart_popup_headline" value="<?php echo esc_attr( $add_to_cart_popup_headline ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_tagline"><?php esc_html_e( 'Tagline', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_add_to_cart_popup_tagline" name="add_to_cart_popup_tagline" value="<?php echo esc_attr( $add_to_cart_popup_tagline ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_button_checkout"><?php esc_html_e( 'Button', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_add_to_cart_popup_button_checkout" name="add_to_cart_popup_button_checkout" value="<?php echo esc_attr( $add_to_cart_popup_button_checkout ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_label_title"><?php esc_html_e( 'Title', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_add_to_cart_popup_label_title" name="add_to_cart_popup_label_title" value="<?php echo esc_attr( $add_to_cart_popup_label_title ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Please choose the text labels to be displayed', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_label_cta_text"><?php esc_html_e( 'Call to action text', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="text" id="psupsellmaster_add_to_cart_popup_label_cta_text" name="add_to_cart_popup_label_cta_text" value="<?php echo esc_attr( $add_to_cart_popup_label_cta_text ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Please choose the text labels to be displayed', 'psupsellmaster' ); ?></p>
				</td>
			</tr>
			<tr id="psupsellmaster_add_to_cart_popup_max_cols_tr"  valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_max_cols"><?php esc_html_e( 'Max Columns', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_max_cols" name="add_to_cart_popup_max_cols">
						<?php for ( $i = 1; $i <= 6; $i++ ) : ?>
							<option <?php selected( intval( $add_to_cart_popup_max_cols ), $i ); ?> value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr id="psupsellmaster_add_to_cart_popup_max_prod_tr"  valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_max_prod"><?php esc_html_e( 'Max Products in Carousel', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_max_prod" name="add_to_cart_popup_max_prod">
						<?php for ( $i = 1; $i <= 50; $i++ ) : ?>
							<option <?php selected( $add_to_cart_popup_max_prod, $i ); ?> value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr id="psupsellmaster_add_to_cart_popup_max_per_author_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_max_per_author"><?php esc_html_e( 'Max Products per Author', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_max_per_author" name="add_to_cart_popup_max_per_author">
						<?php for ( $i = 0; $i <= 10; $i++ ) : ?>
							<option <?php selected( intval( $add_to_cart_popup_max_per_author ), $i ); ?> value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( ( 0 === $i ? __( 'Unlimited' ) : $i ) ); ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr id="psupsellmaster_add_to_cart_popup_addtocart_button_tr" valign="top">
				<th scope="row">
					<label for="psupsellmaster_add_to_cart_popup_addtocart_button"><?php esc_html_e( 'Add to Cart Button', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<select class="regular-text" id="psupsellmaster_add_to_cart_popup_addtocart_button" name="add_to_cart_popup_addtocart_button">

						<?php foreach ( $add_to_cart_options as $key => $value ) : ?>
							<option <?php selected( $add_to_cart_popup_addtocart_button, $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>

					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="add_to_cart_popup_title_length"><?php esc_html_e( 'Title Length', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" class="regular-text" id="add_to_cart_popup_title_length" name="add_to_cart_popup_title_length" value="<?php echo esc_attr( $add_to_cart_popup_title_length ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="add_to_cart_popup_short_description_limit"><?php esc_html_e( 'Description Length', 'psupsellmaster' ); ?></label>
				</th>
				<td>
					<input type="number" class="regular-text" id="add_to_cart_popup_short_description_limit" name="add_to_cart_popup_short_description_limit" value="<?php echo esc_attr( $add_to_cart_popup_short_description_limit ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<?php
		printf(
			/* translators: 1: Open support ticket link. */
			esc_html__( 'Note for Developers: Please note that you can feed %1$s to the %2$s.', 'psupsellmaster' ),
			'<a href="' . esc_url( PSUPSELLMASTER_DOCUMENTATION_URL ) . '#hooks" target="_blank">' . esc_html__( 'product IDs via Webhook', 'psupsellmaster' ) . '</a>',
			esc_html__( 'popup', 'psupsellmaster' )
		);
		?>
	</p>
</div>

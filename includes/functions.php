<?php
/**
 * Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the plugin is the pro version or not.
 *
 * @return bool True if the plugin is the pro version, false otherwise.
 */
function psupsellmaster_is_pro() {
	return PsUpsellMaster::is_pro();
}

/**
 * Check whether the plugin is the lite version or not.
 *
 * @return bool True if the plugin is the lite version, false otherwise.
 */
function psupsellmaster_is_lite() {
	return ! PsUpsellMaster::is_pro();
}

/**
 * Check whether the newsletter has been subscribed or not.
 *
 * @return bool True if the newsletter has been subscribed, false otherwise.
 */
function psupsellmaster_is_newsletter_subscribed() {
	// Set the is subscribed.
	$is_subscribed = get_option( 'psupsellmaster_newsletter_subscribed', false );
	$is_subscribed = filter_var( $is_subscribed, FILTER_VALIDATE_BOOLEAN );

	// Return the is subscribed.
	return $is_subscribed;
}

/**
 * Check if a specific feature is active (available and enabled).
 *
 * @param string $key The feature key.
 * @return bool True if the feature is active, false otherwise.
 */
function psupsellmaster_feature_is_active( $key ) {
	// Set the is active.
	$is_active = false;

	// Check the key.
	switch ( $key ) {
		case 'page_product':
			// Set the is active.
			$is_active = filter_var( PsUpsellMaster_Settings::get( 'product_page_enable' ), FILTER_VALIDATE_BOOLEAN );
			break;
		case 'page_checkout':
			// Set the is active.
			$is_active = filter_var( PsUpsellMaster_Settings::get( 'checkout_page_enable' ), FILTER_VALIDATE_BOOLEAN );
			break;
		case 'popup_add_to_cart':
			// Set the is active.
			$is_active = filter_var( PsUpsellMaster_Settings::get( 'add_to_cart_popup_enable' ), FILTER_VALIDATE_BOOLEAN );
			break;
	}

	// Allow developers to filter this.
	$is_active = apply_filters( 'psupsellmaster_feature_is_active', $is_active, $key );

	// Return the is active.
	return $is_active;
}

/**
 * Prepare the content. Run shortcodes, strip all tags and trim.
 *
 * @param string $content The content.
 * @return string The content.
 */
function psupsellmaster_prepare_content( $content ) {
	ob_start();

	echo do_shortcode( $content );
	$content = trim( wp_strip_all_tags( ob_get_clean() ) );

	return $content;
}

/**
 * Render the product card.
 *
 * @param array $args The arguments.
 * @return string The html.
 */
function psupsellmaster_get_product_card( $args = array() ) {
	global $post;

	// Get the product id.
	$product_id = isset( $args['product_id'] ) ? $args['product_id'] : false;

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return;
	}

	// Get the excerpt.
	$excerpt = get_post_field( 'post_excerpt', $product_id );

	// Get the content.
	$content = get_post_field( 'post_content', $product_id );

	// Get the author id.
	$author_id = get_post_field( 'post_author', $product_id );

	// Get the author display name.
	$author_display_name = get_the_author_meta( 'display_name', $author_id );

	// Get the permalink.
	$permalink = get_the_permalink( $product_id );

	// Get the title.
	$title = get_the_title( $product_id );

	// Get the arg title_length.
	$title_length = ( isset( $args['title_length'] ) && '' !== $args['title_length'] ) ? filter_var( $args['title_length'], FILTER_VALIDATE_INT ) : false;
	$title_length = false !== $title_length ? $title_length : 50;

	// Get the arg addtocart_button.
	$addtocart_button = ( isset( $args['addtocart_button'] ) && 'highest-price-only' !== $args['addtocart_button'] ) ? $args['addtocart_button'] : 'highest-price-only';

	// Get the arg short_description_limit.
	$short_description_limit = ( isset( $args['short_description_limit'] ) && '' !== $args['short_description_limit'] ) ? $args['short_description_limit'] : 100;
	$short_description_limit = filter_var( $short_description_limit, FILTER_VALIDATE_INT );

	// Get the author information.
	$author_information = PsUpsellMaster_Settings::get( 'author_information' );

	// Set the card classes.
	$card_classes = array( 'psupsellmaster-product', 'psupsellmaster-product-card' );

	// Get the arg base_product_id.
	$base_product_id = $args['base_product_id'];

	// Set the nofollow.
	$nofollow = '';

	// Get the nofollow setting.
	$add_rel_nofollow = PsUpsellMaster_Settings::get( 'add_rel_nofollow' );
	$add_rel_nofollow = filter_var( $add_rel_nofollow, FILTER_VALIDATE_BOOLEAN );

	// Check if the nofollow setting is true.
	if ( true === $add_rel_nofollow ) {
		// Set the nofollow.
		$nofollow = ' rel="nofollow"';
	}

	// Check if the excerpt is empty.
	if ( empty( $excerpt ) ) {
		// Set the excerpt.
		$excerpt = trim( $content );
	}

	// Set the excerpt.
	$excerpt = psupsellmaster_prepare_content( $excerpt );

	// Set the content.
	$content = psupsellmaster_prepare_content( $content );

	// Set the excerpt text.
	$excerpt_text = '';

	// Check if the short description limit is not zero.
	if ( 0 !== $short_description_limit ) {

		// Check if the excerpt is shorter AND if the content is greater than the description limit.
		if ( ( strlen( $excerpt ) < $short_description_limit ) && ( strlen( $content ) > $short_description_limit ) ) {
			// Set the excerpt text.
			$excerpt_text  = substr( $content, 0, $short_description_limit ) . '...';
			$excerpt_text .= ' <a href="' . $permalink . '"' . $nofollow . '>Read more</a>';

			// Otherwise, check if the excerpt is not empty.
		} elseif ( ! empty( $excerpt ) ) {
			// Set the excerpt text.
			$excerpt_text  = substr( $excerpt, 0, $short_description_limit ) . '...';
			$excerpt_text .= ' <a href="' . $permalink . '"' . $nofollow . '>Read more</a>';
		}
	}

	// Check if the author display name length is greater than 20.
	if ( strlen( $author_display_name ) > 20 ) {
		// Set the author display name.
		$author_display_name = substr( $author_display_name, 0, 20 ) . '...';
	}

	// Set the author url.
	$author_url = psupsellmaster_get_author_url( $author_id );

	// Check if the length of the title is greater than the maximum allowed.
	if ( strlen( $title ) > $title_length ) {
		// Set the title.
		$title = substr( $title, 0, $title_length ) . '...';

		// Add a class to the card classes list.
		array_push( $card_classes, 'psupsellmaster-product-has-short-title' );
	}

	// Get the location.
	$location = isset( $args['location'] ) ? $args['location'] : '';

	// Get the source.
	$source = isset( $args['source'] ) ? $args['source'] : '';

	// Make sure to convert the source in case its value is wrong.
	if ( 'viewed' === $source ) {
		// Set the source.
		$source = 'visits';
	}

	// Get the view.
	$view = isset( $args['view'] ) ? $args['view'] : '';

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;
	$campaign_id = ! empty( $campaign_id ) ? $campaign_id : false;

	// Check if the campaign id is not empty.
	if ( ! empty( $campaign_id ) ) {
		// Set the source.
		$source = 'campaigns';
	}

	// New instance of the tracking class.
	$tracking = new PsUpsellMaster_Tracking();

	// Add the tracking hooks.
	$tracking->add_hooks( 'products' );

	// Set the tracking data.
	$tracking->set_campaign_id( $campaign_id );
	$tracking->set_base_product_id( $base_product_id );
	$tracking->set_location( $location );
	$tracking->set_source( $source );
	$tracking->set_view( $view );

	// Set the product url.
	$product_url = apply_filters( 'psupsellmaster_item_product_url', $permalink, $product_id );

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {

		// Check if the class does exist.
		if ( class_exists( 'WC_Template_Loader' ) ) {
			// Remove the filter (otherwise, it seems that product images are not loaded within blocks and widgets).
			remove_filter( 'post_thumbnail_html', array( 'WC_Template_Loader', 'unsupported_theme_single_featured_image_filter' ) );
		}
	}

	// Check if the author url is empty.
	if ( empty( $author_url ) ) {
		// Set the author url.
		$author_url = '#';

		// Check if the product url is not empty.
		if ( ! empty( $product_url ) ) {
			// Set the author url.
			$author_url = $product_url;
		}
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>">
		<div class="psupsellmaster-product-card-image">
			<a class="psupsellmaster-product-link" href="<?php echo esc_url( $product_url ); ?>" <?php echo wp_kses( $nofollow, array( 'a' => 'rel' ) ); ?>>
				<?php echo wp_kses_post( get_the_post_thumbnail( $product_id, array( 235, 235 ) ) ); ?>
			</a>
		</div>
		<div class="psupsellmaster-product-card-body">
			<div class="psupsellmaster-products-author" data-author-id="<?php echo esc_attr( $author_id ); ?>">
				<?php if ( in_array( $author_information, array( 'all', 'image', 'name' ), true ) ) : ?>
					<a href="<?php echo esc_url( $author_url ); ?>" <?php echo wp_kses( $nofollow, array( 'a' => 'rel' ) ); ?>>
						<?php if ( in_array( $author_information, array( 'all', 'image' ), true ) ) : ?>
							<?php echo wp_kses_post( get_avatar( $author_id, 40 ) ); ?>
						<?php endif; ?>
						<?php if ( in_array( $author_information, array( 'all', 'name' ), true ) ) : ?>
							<h6><?php echo esc_html( $author_display_name ); ?></h6>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</div>
			<div class="psupsellmaster-product-details">
				<a class="psupsellmaster-products-title psupsellmaster-product-link" href="<?php echo esc_url( $product_url ); ?>" <?php echo wp_kses( $nofollow, array( 'a' => 'rel' ) ); ?> title="<?php echo esc_attr( $title ); ?>">
					<h4><?php echo esc_html( $title ); ?></h4>
				</a>
				<div class="psupsellmaster-products-description">

					<?php if ( 0 !== $short_description_limit ) : ?>
						<p><?php echo wp_kses_post( $excerpt_text ); ?></p>
					<?php endif; ?>

				</div>
			</div>
		</div>
		<?php if ( psupsellmaster_is_plugin_active( 'woo' ) ) : ?>
			<div class="psupsellmaster_wc_product_cart_footer <?php echo esc_attr( 'highest-price-only' === $addtocart_button ? 'psupsellmaster-standard' : 'psupsellmaster-with-option' ); ?>">
				<?php
				// Get the product data.
				$product_data = get_post( $product_id );

				// Get the product.
				$product = wc_setup_product_data( $product_data );
				?>
				<?php if ( ! empty( $product ) ) : ?>
					<div class="product woocommerce add_to_cart_inline">
						<?php
						// Echo the price html.
						echo wp_kses_post( $product->get_price_html() );

						// Render the template.
						woocommerce_template_loop_add_to_cart();

						// Check the global post.
						if ( $post ) {
							// Restore the global post (product) data.
							wc_setup_product_data( $post );
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		<?php elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) : ?>
			<?php
			if ( 'highest-price-only' === $addtocart_button ) {
				$GLOBALS[ 'psupsellmaster_edd_price_mode_' . $product_id ] = ( 'highest-price-only' === $addtocart_button ) ? false : true;
			}
			?>
			<div class="psupsellmaster-product-card-footer">
				<?php
				if ( psupsellmaster_is_plugin_active( 'edd-wish-lists' ) ) {
					edd_wl_load_wish_list_link( $product_id );
				}
				?>
				<div class="psupsellmaster-product-add-to-cart <?php echo esc_attr( 'highest-price-only' === $addtocart_button ? 'psupsellmaster-standard' : 'psupsellmaster-with-option' ); ?>">
					<?php
					$purchase_link_args = array(
						'download_id' => $product_id,
						'class'       => 'psupsellmaster-cart-btn',
						'price'       => true,
						'source'      => 'psupsellmaster',
					);

					if ( 'highest-price-only' === $addtocart_button ) {
						$purchase_link_args['price_id'] = psupsellmaster_edd_get_highest_price_id( $product_id );
					}

					// There is a bug from EDD as of version 3.1.0.1.
					// so we need to remove a filter before calling.
					// the edd_get_purchase_link function.
					// and add the filter back after calling it.

					// Get the has edd bug.
					$has_edd_bug = has_filter( 'edd_purchase_link_args', 'EDD\Blocks\Downloads\maybe_update_purchase_links' );

					// Check if the filter bug does exist.
					if ( $has_edd_bug ) {
						// Remove filter.
						remove_filter( 'edd_purchase_link_args', 'EDD\Blocks\Downloads\maybe_update_purchase_links', 100 );
					}

					// Can't use wp_kses due BIG 3rd party plugins such as EDD and WOO using style attributes within tags...
					// => Why aren't they using these functions in the first place...!?
					echo edd_get_purchase_link( $purchase_link_args );

					// Check if the filter bug does exist.
					if ( $has_edd_bug ) {
						add_filter( 'edd_purchase_link_args', 'EDD\Blocks\Downloads\maybe_update_purchase_links', 100 );
					}
					?>
				</div>
			</div>
			<?php
			if ( 'highest-price-only' === $addtocart_button ) {
				unset( $GLOBALS[ 'psupsellmaster_edd_price_mode_' . $product_id ] );
			}
			?>
		<?php endif; ?>

	</div>
	<?php
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {

		// Check if the class does exist.
		if ( class_exists( 'WC_Template_Loader' ) ) {
			// Add the filter.
			add_filter( 'post_thumbnail_html', array( 'WC_Template_Loader', 'unsupported_theme_single_featured_image_filter' ) );
		}
	}

	// Remove the tracking hooks.
	$tracking->remove_hooks( 'products' );
}

/**
 * Filter the add_to_cart shortcode in WooCommerce.
 *
 * @param string $output The output.
 * @param string $tag The tag.
 * @param array  $attr The attr.
 * @return string The output.
 */
function psupsellmaster_do_shortcode_tag( $output, $tag, $attr ) {

	if ( ( defined( 'WC_VERSION' ) ) && ( 'add_to_cart' === $tag ) && ( isset( $attr['source'] ) && ( 'psupsellmaster_master' === $attr['source'] ) ) ) {

		// style adjustments for WC add to cart button.
		if ( preg_match( '/<p class=\\"saved-sale\\">(.*?)<\\/p>/s', $output, $ar_matches ) ) {
			$src    = $ar_matches[0];
			$dest   = str_replace( array( '<p ', '</p>' ), array( "<span style='padding-left: 1em; font-size: 80%;' ", '</span>' ), $src );
			$output = str_replace( $src, $dest, $output );
		}
	}

	return $output;
}
add_filter( 'do_shortcode_tag', 'psupsellmaster_do_shortcode_tag', 10, 3 );

/**
 * Get the store currency.
 *
 * @return string The store currency.
 */
function psupsellmaster_get_store_currency() {

	if ( defined( 'WC_VERSION' ) ) {
		return get_woocommerce_currency();
	}

	if ( defined( 'EDD_VERSION' ) ) {
		return edd_currency_filter();
	}

	return '';
}

/**
 * Get the product prices in WooCommerce.
 *
 * TODO: replace this old function with a new higher-quality function.
 *
 * @param int  $product_id The product id.
 * @param bool $sort_prices Whether or not to sort the prices.
 * @return array The prices.
 */
function psupsellmaster_get_wc_price_range( $product_id, $sort_prices = false ) {
	$prices  = array(
		'min'    => 0,
		'max'    => 0,
		'prices' => array(),
	);

	$product = wc_get_product( $product_id );

	if ( ! $product ) {
		return $prices;
	}

	if ( $product->is_type( 'simple' ) ) {

		$price = (float) $product->get_price();

		if ( $sort_prices ) {
			$prices[] = $price;
		}
		// Assign min and max price for simple product.
		$min_price = $price;
		$max_price = $price;

	} else {

		// variable or variation.
		$parent_id = (int) $product->get_parent_id();

		if ( $parent_id <= 0 ) {
			$parent_id = (int) $product_id;
		}

		$sql = PsUpsellMaster_Database::prepare(
			"SELECT ID FROM %i WHERE (post_status = %s) AND (post_parent = %d) AND (post_type = %s)",
			PsUpsellMaster_Database::get_table_name( 'posts' ),
			'publish',
			$parent_id,
			'product_variation'
		);

		$variations = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );
		$variations = ! empty( $variations ) ? $variations : array();

		$min_price = 0;
		$max_price = 0;

		foreach ( $variations as $row ) {

			$var_id            = (int) $row[0];
			$product_variation = new WC_Product_Variation( $var_id );
			$price             = (float) $product_variation->get_price();

			if ( ! in_array( $price, $prices, true ) ) {

				if ( $sort_prices ) {
					$prices[] = $price;
				}

				if ( $price > $max_price ) {
					$max_price = $price;
				}

				if ( ( $min_price <= 0 ) || ( $price < $min_price ) ) {
					$min_price = $price;
				}
			}
		}
	}

	if ( $sort_prices && ( count( $prices ) > 1 ) ) {
		sort( $prices );
	}

	if ( $sort_prices ) {
		$response = array(
			'min'    => $min_price,
			'max'    => $max_price,
			'prices' => $prices,
		);
	} else {
		$response = array(
			'min' => $min_price,
			'max' => $max_price,
		);
	}

	return $response;
}

/**
 * Get the product prices.
 *
 * TODO: replace this old function with a new higher-quality function.
 *
 * @param int  $product_id The product id.
 * @param bool $sort_prices Whether or not to sort the prices.
 * @return array The prices.
 */
function psupsellmaster_get_product_price_range( $product_id, $sort_prices = false ) {

	if ( defined( 'WC_VERSION' ) ) {
		return psupsellmaster_get_wc_price_range( $product_id, $sort_prices );
	}

	if ( defined( 'EDD_VERSION' ) ) {
		return psupsellmaster_get_edd_price_range( $product_id, $sort_prices );
	}

	return array();
}

/**
 * Get the sales stats in WooCommerce.
 *
 * @param int $product_id The product ID.
 * @return float The product gross revenue.
 */
function psupsellmaster_get_wc_sales_stats( $product_id ) {
	$sql = PsUpsellMaster_Database::prepare(
		'SELECT SUM(product_gross_revenue) AS `total`, COUNT(*) AS `qty` '
		. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'wc_order_product_lookup' ) )
		. 'WHERE product_id  = %d ',
		array( $product_id )
	);

	$query = PsUpsellMaster_Database::get_results( $sql, ARRAY_A );

	return $query[0];
}

/**
 * Get the product price text.
 *
 * @param int   $download_id The product id.
 * @param array $products_price The product price.
 * @return string The product price text.
 */
function psupsellmaster_get_price_range_text( $download_id, $products_price ) {

	$price_text = __( 'Free', 'psupsellmaster' );

	if ( ( $products_price['min'] > 0 ) && ( $products_price['max'] > 0 ) ) {

		if ( defined( 'WC_VERSION' ) ) {

			if ( ( $products_price['min'] > 0 ) && ( $products_price['max'] > 0 ) && ( $products_price['min'] !== $products_price['max'] ) ) {
				$price_text = psupsellmaster_get_currency_symbol() . psupsellmaster_woo_wc_price_no_markup( $products_price['min'] )
								. ' ' . __( 'to', 'psupsellmaster' )
								. ' ' . psupsellmaster_get_currency_symbol() . psupsellmaster_woo_wc_price_no_markup( $products_price['max'] );
			} elseif ( ( $products_price['min'] <= 0 ) && ( $products_price['max'] > 0 ) ) {
				$price_text = psupsellmaster_get_currency_symbol() . psupsellmaster_woo_wc_price_no_markup( $products_price['max'] );
			} else {
				$price_text = psupsellmaster_get_currency_symbol() . psupsellmaster_woo_wc_price_no_markup( $products_price['min'] );
			}
		} elseif ( defined( 'EDD_VERSION' ) ) {

			if ( ( $products_price['min'] > 0 ) && ( $products_price['max'] > 0 ) && ( $products_price['min'] !== $products_price['max'] ) ) {
				$price_text = edd_currency_filter( edd_format_amount( $products_price['min'] ) )
								. ' ' . __( 'to', 'psupsellmaster' )
								. ' ' . edd_currency_filter( edd_format_amount( $products_price['max'] ) );
			} elseif ( ( $products_price['min'] <= 0 ) && ( $products_price['max'] > 0 ) ) {
				$price_text = edd_currency_filter( edd_format_amount( $products_price['max'] ) );
			} else {
				$price_text = edd_currency_filter( edd_format_amount( $products_price['min'] ) );
			}
		}
	}

	return $price_text;
}

/**
 * Access denied.
 */
function psupsellmaster_access_denied() {
	die( esc_html__( 'Please login first', 'psupsellmaster' ) );
}

/**
 * Render the campaign banner data on the product page.
 * Position - Before the Excerpt.
 *
 * @param string $excerpt The excerpt.
 * @return string The excerpt.
 */
function psupsellmaster_render_campaign_banner_data_on_product_page_before_excerpt( $excerpt ) {
	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		// Return the excerpt.
		return $excerpt;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the position.
	$position = isset( $settings['page_product_banner_position'] ) ? $settings['page_product_banner_position'] : 'content_before';

	// Check the position.
	if ( 'excerpt_before' !== $position ) {
		// Return the excerpt.
		return $excerpt;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Start the buffer.
	ob_start();

	// Render the campaign banner data.
	psupsellmaster_render_single_eligible_campaign_banner_data_from_page( $args );

	// Set the excerpt.
	$excerpt = ob_get_clean() . $excerpt;

	// Return the excerpt.
	return $excerpt;
}
add_filter( 'the_excerpt', 'psupsellmaster_render_campaign_banner_data_on_product_page_before_excerpt' );

/**
 * Render the campaign banner data on the product page.
 * Position - After the Excerpt.
 *
 * @param string $excerpt The excerpt.
 * @return string The excerpt.
 */
function psupsellmaster_render_campaign_banner_data_on_product_page_after_excerpt( $excerpt ) {
	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		// Return the excerpt.
		return $excerpt;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the position.
	$position = isset( $settings['page_product_banner_position'] ) ? $settings['page_product_banner_position'] : 'content_before';

	// Check the position.
	if ( 'excerpt_after' !== $position ) {
		// Return the excerpt.
		return $excerpt;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Start the buffer.
	ob_start();

	// Render the campaign banner data.
	psupsellmaster_render_single_eligible_campaign_banner_data_from_page( $args );

	// Set the excerpt.
	$excerpt .= ob_get_clean();

	// Return the excerpt.
	return $excerpt;
}
add_filter( 'the_excerpt', 'psupsellmaster_render_campaign_banner_data_on_product_page_after_excerpt' );

/**
 * Render the campaign banner data on the product page.
 * Position - Before the Content.
 *
 * @param string $content The content.
 * @return string The content.
 */
function psupsellmaster_render_campaign_banner_data_on_product_page_before_content( $content ) {
	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		// Return the content.
		return $content;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the position.
	$position = isset( $settings['page_product_banner_position'] ) ? $settings['page_product_banner_position'] : 'content_before';

	// Check the position.
	if ( 'content_before' !== $position ) {
		// Return the content.
		return $content;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Start the buffer.
	ob_start();

	// Render the campaign banner data.
	psupsellmaster_render_single_eligible_campaign_banner_data_from_page( $args );

	// Set the content.
	$content = ob_get_clean() . $content;

	// Return the content.
	return $content;
}
add_filter( 'the_content', 'psupsellmaster_render_campaign_banner_data_on_product_page_before_content' );

/**
 * Render the campaign banner data on the product page.
 * Position - After the Content.
 *
 * @param string $content The content.
 * @return string The content.
 */
function psupsellmaster_render_campaign_banner_data_on_product_page_after_content( $content ) {
	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		// Return the content.
		return $content;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the position.
	$position = isset( $settings['page_product_banner_position'] ) ? $settings['page_product_banner_position'] : 'content_before';

	// Check the position.
	if ( 'content_after' !== $position ) {
		// Return the content.
		return $content;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Start the buffer.
	ob_start();

	// Render the campaign banner data.
	psupsellmaster_render_single_eligible_campaign_banner_data_from_page( $args );

	// Set the content.
	$content .= ob_get_clean();

	// Return the content.
	return $content;
}
add_filter( 'the_content', 'psupsellmaster_render_campaign_banner_data_on_product_page_after_content', 100 );

/**
 * Render the products by feature location.
 *
 * @param string $location The location.
 * @param array  $atts     The attributes.
 */
function psupsellmaster_render_products_by_feature_location( $location, $atts ) {
	$defaults = array(
		'type'           => 'carousel',
		'show'           => 'upsells',
		'max_cols'       => 1,
		'max_prod'       => 10,
		'max_per_author' => 0,
		'prices'         => 'all-prices',
		'title_length'   => 50,
		'desc_limit'     => 100,
		'title'          => __( 'Similar Products', 'psupsellmaster' ),
		'cta_text'       => __( 'Other customers were also interested in...', 'psupsellmaster' ),
	);

	// Check if the location is block or widget.
	if ( in_array( $location, array( 'block', 'widget' ), true ) ) {
		// Additional defaults.
		$defaults['source_product'] = 'yes';
	}

	$atts = ! empty( $atts ) ? $atts : array();
	$atts = array_merge( $defaults, $atts );

	// Get the enqueue scripts and styles filter value.
	$enqueue_scripts_styles = apply_filters( 'psupsellmaster_render_products_enqueue_scripts_styles', true, $location, $atts, $defaults );

	// Check if the scripts and styles should be enqueued.
	if ( true === $enqueue_scripts_styles ) {
		// Check if the location is shortcode.
		if ( 'shortcode' === $location ) {
			// Register the scripts (block-based themes won't work w/o this).
			psupsellmaster_register_scripts();
		}

		// Enqueue the scripts.
		psupsellmaster_enqueue_script( 'edd-wish-lists' );
		psupsellmaster_enqueue_script( 'edd-wish-lists-modal' );
		psupsellmaster_enqueue_script( 'main' );

		if ( 'carousel' === $atts['type'] ) {
			// Enqueue the scripts.
			psupsellmaster_enqueue_script( 'vendor-owl-carousel' );
			psupsellmaster_enqueue_script( 'products-carousel' );
		}

		// Enqueue the scripts.
		psupsellmaster_enqueue_script( 'products' );

		// Enqueue the styles.
		psupsellmaster_enqueue_style( 'main' );

		if ( 'carousel' === $atts['type'] ) {
			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'vendor-owl-carousel' );
			psupsellmaster_enqueue_style( 'vendor-owl-carousel-theme' );
		} else {
			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'grid' );
		}

		// Enqueue the styles.
		psupsellmaster_enqueue_style( 'products' );
	}

	// Start mapping the settings.

	$settings['addtocart_button']        = $atts['prices'];
	$settings['display_type']            = $atts['type'];
	$settings['label_cta_text']          = $atts['cta_text'];
	$settings['label_title']             = $atts['title'];
	$settings['max_cols']                = $atts['max_cols'];
	$settings['max_prod']                = $atts['max_prod'];
	$settings['max_per_author']          = $atts['max_per_author'];
	$settings['title_length']            = $atts['title_length'];
	$settings['short_description_limit'] = $atts['desc_limit'];
	$settings['show_type']               = $atts['show'];

	// Check if the source_product attribute exists.
	if ( isset( $atts['source_product'] ) ) {
		// Additional settings.
		$settings['source_product'] = $atts['source_product'];
	}

	// Get the algorithm logic setting.
	$algorithm_logic = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Set the algorithm logic setting.
	$settings['algorithm_logic'] = array();

	// Set the priority only setting.
	$settings['algorithm_logic']['priority_only'] = false;

	// Check if the priority only from the algorithm logic does exist.
	if ( isset( $algorithm_logic['priority_only'] ) ) {
		// Set the priority only.
		$settings['algorithm_logic']['priority_only'] = filter_var( $algorithm_logic['priority_only'], FILTER_VALIDATE_BOOLEAN );
	}

	// Check the location type.
	if ( 'block' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_feature_block( $settings );
	} elseif ( 'shortcode' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_feature_shortcode( $settings );
	} elseif ( 'widget' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_feature_widget( $settings );
	} elseif ( 'elementor_widget' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_feature_elementor_widget( $settings );
	}
}

/**
 * Returns the author URL.
 *
 * @param int $user_id The user ID.
 * @return string The author URL.
 */
function psupsellmaster_get_author_url( $user_id ) {
	// Set the author URL.
	$author_url = '';

	// Check if the EDD FES plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd-fes' ) ) {
		// Get the userdata.
		$userdata = get_userdata( $user_id );

		// Check if the userdata is not empty.
		if ( ! empty( $userdata ) ) {
			// Set the author URL.
			$author_url = EDD_FES()->vendors->get_vendor_store_url( $user_id );
		}
	}

	// Return the author URL.
	return $author_url;
}

/**
 * Get the priorities.
 *
 * @return array The priorities.
 */
function psupsellmaster_get_priorities() {
	// Set the priorities.
	$priorities = array();

	// Add a priority to the list.
	$priorities['lifetime-sales'] = __( 'Lifetime Sales', 'psupsellmaster' );

	// Add a priority to the list.
	$priorities['category'] = __( 'Category', 'psupsellmaster' );

	// Add a priority to the list.
	$priorities['tag'] = __( 'Tag', 'psupsellmaster' );

	// Allow developers to filter this.
	$priorities = apply_filters( 'psupsellmaster_priorities', $priorities );

	// Return the priorities.
	return $priorities;
}

/**
 * Get the priority descriptions.
 *
 * @return array The priority descriptions.
 */
function psupsellmaster_get_priority_descriptions() {
	// Set the descriptions.
	$descriptions = array();

	// Add a priority to the list.
	$descriptions['lifetime-sales'] = __( 'This criteria gives priority to products with a higher percentage of lifetime sales in the store.', 'psupsellmaster' );

	// Add a priority to the list.
	$descriptions['category'] = __( 'This criteria gives priority to products that have a higher number of shared Categories between them.', 'psupsellmaster' );

	// Add a priority to the list.
	$descriptions['tag'] = __( 'This criteria gives priority to products that have a higher number of shared Tags between them.', 'psupsellmaster' );

	// Allow developers to filter this.
	$descriptions = apply_filters( 'psupsellmaster_priority_descriptions', $descriptions );

	// Return the descriptions.
	return $descriptions;
}

/**
 * Get the stored priorities.
 *
 * @return array The stored priorities.
 */
function psupsellmaster_get_stored_priorities() {
	// Get the stored priorities.
	$stored_priorities = PsUpsellMaster_Settings::get( 'algorithm_logic' );
	$stored_priorities = isset( $stored_priorities['priority'] ) ? $stored_priorities['priority'] : array();

	// Return the stored priorities.
	return $stored_priorities;
}

/**
 * Get a priority label.
 *
 * @param string $priority_key The priority key.
 * @return string The priority label.
 */
function psupsellmaster_get_priority_label( $priority_key ) {
	// Set the priority label.
	$priority_label = '';

	// Check the priority key.
	if ( 'vendor' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Vendor', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'lifetime-sales' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Lifetime Sales', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'upsell-results' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Upsell Results', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'order-results' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Related Results', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'category' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Category', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'tag' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Tag', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'preferred' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'Prefered Product', 'psupsellmaster' );

		// Otherwise...
	} elseif ( 'upsells' === $priority_key ) {
		// Set the priority label.
		$priority_label = __( 'No Priority', 'psupsellmaster' );

		// Otherwise...
	} else {
		// Get the product taxonomies.
		$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects', false );

		// Loop through the product taxomomies.
		foreach ( $product_taxonomies as $product_taxonomy ) {

			// Check if the taxonomy name is empty.
			if ( empty( $product_taxonomy->name ) ) {
				continue;
			}

			// Check if the taxonomy labels are empty.
			if ( empty( $product_taxonomy->labels ) ) {
				continue;
			}

			// Check if the taxonomy singular label is empty.
			if ( empty( $product_taxonomy->labels->singular_name ) ) {
				continue;
			}

			// Get the taxonomy name.
			$product_taxonomy_name = $product_taxonomy->name;

			// Get the taxonomy singular label.
			$product_taxonomy_label = $product_taxonomy->labels->singular_name;

			// Set the product taxonomy key.
			$product_taxonomy_key = "taxonomy_{$product_taxonomy_name}";

			// Check if the priority key matches.
			if ( $product_taxonomy_key === $priority_key ) {
				// Set the priority label.
				$priority_label = $product_taxonomy_label;

				// Stop the loop.
				break;
			}
		}
	}

	// Check if the priority label is empty.
	if ( empty( $priority_label ) ) {
		// Set the priority label.
		$priority_label = __( 'Unknown', 'psupsellmaster' );
	}

	// Return the priority label.
	return $priority_label;
}

/**
 * Check if a plugin is installed.
 *
 * @param string $plugin_key The plugin key.
 * @return bool Whether or not the plugin is installed.
 */
function psupsellmaster_is_plugin_installed( $plugin_key ) {
	// Set the is installed.
	$is_installed = false;

	// Get the plugins.
	$plugins = get_plugins();

	// Get the plugin names.
	$names = wp_list_pluck( $plugins, 'Name' );

	// Check if the plugin key refers to the WooCommerce plugin.
	if ( 'woo' === $plugin_key ) {
		// Check if the plugin we are looking for is installed.
		$is_installed = in_array( 'WooCommerce', $names, true );

		// Check if the plugin key refers to the Easy Digital Downloads plugin.
	} elseif ( 'edd' === $plugin_key ) {
		// Check if the plugin we are looking for is installed.
		$is_installed = in_array( 'Easy Digital Downloads', $names, true );
	}

	// Return the is installed.
	return $is_installed;
}

/**
 * Get the plugin path.
 *
 * @param string $plugin_key The plugin key.
 * @return string|false The plugin path or false on failure.
 */
function psupsellmaster_get_plugin_path( $plugin_key ) {
	// Set the plugin path.
	$plugin_path = false;

	// Get the plugins.
	$plugins = get_plugins();

	// Get the plugin names.
	$names = wp_list_pluck( $plugins, 'Name' );

	// Check if the plugin key refers to the WooCommerce plugin.
	if ( 'woo' === $plugin_key ) {
		// Get the plugin path in case the plugin was found in the list.
		$plugin_path = array_search( 'WooCommerce', $names, true );

		// Check if the plugin key refers to the Easy Digital Downloads plugin.
	} elseif ( 'edd' === $plugin_key ) {
		// Get the plugin path in case the plugin was found in the list.
		$plugin_path = array_search( 'Easy Digital Downloads', $names, true );
	}

	// Return the plugin path.
	return $plugin_path;
}

/**
 * Remove duplicated array items - keep only the last occurrence.
 *
 * @param array $items The items.
 * @return array The items.
 */
function psupsellmaster_remove_array_duplicates( $items ) {
	// First, reverse the order of the items in the array.
	$items = array_reverse( $items );

	// Now, the array unique will keep only the first occurrence (reversed, which will actually be the last one).
	$items = array_unique( $items );

	// Last, reverse the order once again.
	$items = array_reverse( $items );

	// Return the items.
	return $items;
}

/**
 * Merge visitors that were stored with the same IP address and that were
 * updated within the last 5 minutes. This process is needed because when
 * the current user has no cookie, the first requests might store multiple
 * records in the database table due to concurrent requests.
 */
function psupsellmaster_merge_visits() {
	// Get the cookie.
	$cookie = psupsellmaster_get_current_visitor_cookie();

	// Check if the cookie is empty.
	if ( empty( $cookie ) ) {
		return false;
	}

	// Set all the visits.
	$all_visits = array();

	// Get the ip address.
	$ip_address = psupsellmaster_get_ip_address();
	$ip_address = ! empty( $ip_address ) ? $ip_address : '';

	// Build the SQL to find the multiple visitor records.
	$sql_select  = 'SELECT `v`.id, `v`.visits';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `v`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';

	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `v`.cookie <> %s', $cookie );

	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `v`.ip = %s', $ip_address );
	$sql_where[] = 'AND `v`.updated_at >= DATE_SUB( NOW(), INTERVAL 5 MINUTE )';
	$sql_where   = implode( ' ', $sql_where );
	$sql_orderby = 'ORDER BY `v`.updated_at';
	$sql_query   = "{$sql_select} {$sql_from} {$sql_where} {$sql_orderby}";
	$sql_results = PsUpsellMaster_Database::get_results( $sql_query );

	// Check if the sql results is empty.
	if ( empty( $sql_results ) ) {
		return false;
	}

	// Loop through the sql results.
	foreach ( $sql_results as $visitor ) {
		// Get the visitor id.
		$visitor_id = isset( $visitor->id ) ? filter_var( $visitor->id, FILTER_VALIDATE_INT ) : false;

		// Check if the visitor id is empty.
		if ( empty( $visitor_id ) ) {
			continue;
		}

		// Get the visits.
		$visits = ! empty( $visitor->visits ) ? json_decode( $visitor->visits ) : array();
		$visits = is_array( $visits ) ? $visits : array();

		// Check if the visits list is not empty.
		if ( ! empty( $visits ) ) {
			// Add a list of visits to the visit lists array.
			array_push( $all_visits, $visits );
		}

		// Set the delete where.
		$delete_where = array( 'id' => $visitor_id );

		// Delete this current database table record.
		psupsellmaster_db_visitors_delete( $delete_where );
	}

	// Check if the all visits is empty (meaning there are no visits to merge).
	if ( empty( $all_visits ) ) {
		return false;
	}

	// Build the SQL to find the correct visitor record.
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';
	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `v`.cookie = %s', $cookie );
	$sql_where   = implode( ' ', $sql_where );
	$sql_limit   = 'LIMIT 1';
	$sql_query   = "{$sql_select} {$sql_from} {$sql_where} {$sql_limit}";
	$sql_row     = PsUpsellMaster_Database::get_row( $sql_query );

	// Check if the sql row is not empty.
	if ( empty( $sql_row ) ) {
		return false;
	}

	// Get the visitor id.
	$visitor_id = isset( $sql_row->id ) ? filter_var( $sql_row->id, FILTER_VALIDATE_INT ) : false;

	// Check if the visitor id is empty.
	if ( empty( $visitor_id ) ) {
		return false;
	}

	// Get the visits.
	$visits = ! empty( $sql_row->visits ) ? json_decode( $sql_row->visits ) : array();
	$visits = is_array( $visits ) ? $visits : array();

	// Check if the visits list is not empty.
	if ( ! empty( $visits ) ) {
		// Add the visits to the all visits.
		array_unshift( $all_visits, $visits );
	}

	// Set the visits.
	$visits = array();

	// Loop through the visit lists.
	foreach ( $all_visits as $visits_list ) {

		// Loop through the visits.
		foreach ( $visits_list as $single_visit_id ) {
			// Get the visit id.
			$visit_id = filter_var( $single_visit_id, FILTER_VALIDATE_INT );

			// Check if the visit id is valid.
			if ( false !== $visit_id ) {
				// Add the visit id to the visits list.
				array_push( $visits, $visit_id );
			}
		}
	}

	// Remove duplicates - keep only the last occurrence.
	$visits = psupsellmaster_remove_array_duplicates( $visits );

	// Get the user id.
	$user_id = get_current_user_id();
	$user_id = ! empty( $user_id ) ? $user_id : 0;

	// Get the ip address.
	$ip_address = psupsellmaster_get_ip_address();
	$ip_address = ! empty( $ip_address ) ? $ip_address : '';

	// Set the update data.
	$update_data = array( 'visits' => wp_json_encode( $visits ) );

	// Check if the user id is not empty.
	if ( ! empty( $user_id ) ) {
		$update_data['user_id'] = $user_id;
	}

	// Check if the ip address is not empty.
	if ( ! empty( $ip_address ) ) {
		$update_data['ip'] = $ip_address;
	}

	// Set the update where.
	$update_where = array( 'id' => $visitor_id );

	// Update an existing visitor in the database.
	psupsellmaster_db_visitors_update( $update_data, $update_where );
}

/**
 * Get the product prices.
 *
 * @param int $product_id The product id.
 * @return array The product prices.
 */
function psupsellmaster_get_product_prices( $product_id ) {
	// Set product prices.
	$product_prices = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the product prices.
		$product_prices = psupsellmaster_woo_get_product_prices( $product_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the product prices.
		$product_prices = psupsellmaster_edd_get_product_prices( $product_id );
	}

	// Return the product prices.
	return $product_prices;
}

/**
 * Get the product category taxonomy.
 *
 * @return string|false The taxonomy or false on failure.
 */
function psupsellmaster_get_product_category_taxonomy() {
	// Set category taxonomy.
	$category_taxonomy = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the category taxonomy.
		$category_taxonomy = 'product_cat';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the category taxonomy.
		$category_taxonomy = 'download_category';
	}

	// Return the category taxonomy.
	return $category_taxonomy;
}

/**
 * Get the product tag taxonomy.
 *
 * @return string|false The taxonomy or false on failure.
 */
function psupsellmaster_get_product_tag_taxonomy() {
	// Set tag taxonomy.
	$tag_taxonomy = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the tag taxonomy.
		$tag_taxonomy = 'product_tag';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the tag taxonomy.
		$tag_taxonomy = 'download_tag';
	}

	// Return the tag taxonomy.
	return $tag_taxonomy;
}

/**
 * Check if the product post type is valid by post id.
 *
 * @param int $post_id The post id.
 * @return bool Whether or not the product post type is valid.
 */
function psupsellmaster_is_valid_product_post_type_by_post_id( $post_id ) {
	// Set is valid.
	$is_valid = false;

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Get the valid post type.
	$valid_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type is valid.
	if ( $valid_post_type === $post_type ) {
		$is_valid = true;
	}

	// Return is valid.
	return $is_valid;
}

/**
 * Get the order id from the purchase receipt page.
 *
 * @deprecated 1.7.46 Use psupsellmaster_get_receipt_order_id().
 * @return int|false The order id or false on failure.
 */
function psupsellmaster_get_order_id_from_page_purchase_receipt() {
	return psupsellmaster_get_receipt_order_id();
}

/**
 * Get the user id from an order.
 *
 * @param int $order_id The order id.
 * @return int|false The user id or false on failure.
 */
function psupsellmaster_get_user_id_by_order_id( $order_id ) {
	// Set the user id.
	$user_id = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$user_id = psupsellmaster_woo_get_user_id_by_order_id( $order_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$user_id = psupsellmaster_edd_get_user_id_by_order_id( $order_id );
	}

	// Return the user id.
	return $user_id;
}

/**
 * Get the admin customer url by user id.
 *
 * @param int $user_id The user id.
 * @return string The customer url.
 */
function psupsellmaster_get_admin_customer_url_by_user_id( $user_id ) {
	// Set the customer url.
	$customer_url = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$customer_url = psupsellmaster_woo_get_admin_customer_url_by_user_id( $user_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$customer_url = psupsellmaster_edd_get_admin_customer_url_by_user_id( $user_id );
	}

	// Return the customer url.
	return $customer_url;
}

/**
 * Get the admin customer name by order id.
 *
 * @param int $order_id The order id.
 * @return string The customer name.
 */
function psupsellmaster_get_customer_name_by_order_id( $order_id ) {
	// Set the customer name.
	$customer_name = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$customer_name = psupsellmaster_woo_get_customer_name_by_order_id( $order_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$customer_name = psupsellmaster_edd_get_customer_name_by_order_id( $order_id );
	}

	// Return the customer name.
	return $customer_name;
}

/**
 * Get the admin order url by order id.
 *
 * @param int $order_id The order id.
 * @return string The order url.
 */
function psupsellmaster_get_admin_order_url_by_order_id( $order_id ) {
	// Set the order url.
	$order_url = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$order_url = psupsellmaster_woo_get_admin_order_url_by_order_id( $order_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$order_url = psupsellmaster_edd_get_admin_order_url_by_order_id( $order_id );
	}

	// Return the order url.
	return $order_url;
}

/**
 * Get the products from the purchase receipt page.
 *
 * @deprecated 1.7.49 Use psupsellmaster_get_receipt_product_ids().
 * @return array The products.
 */
function psupsellmaster_get_products_from_page_purchase_receipt() {
	return psupsellmaster_get_receipt_product_ids();
}

/**
 * Get the products from the store cart.
 *
 * @deprecated 1.7.46 Use psupsellmaster_get_session_cart_product_ids().
 * @return array The products.
 */
function psupsellmaster_get_products_from_store_cart() {
	return psupsellmaster_get_session_cart_product_ids();
}

/**
 * Get the visits from the current user.
 *
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_current_user() {
	// Set the visits id list.
	$visits_id_list = array();

	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Check if the visitor id is empty.
	if ( empty( $visitor_id ) ) {
		return $visits_id_list;
	}

	// Build the SQL to get own user visits.
	$sql_select  = 'SELECT `t`.`visits`';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `t`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';
	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `t`.`id` = %d', $visitor_id );
	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `t`.`visits` <> %s', '' ); // Yes, empty is correct.
	$sql_where   = implode( ' ', $sql_where );
	$sql_limit   = 'LIMIT 1';
	$sql_query   = "{$sql_select} {$sql_from} {$sql_where} {$sql_limit}";

	$sql_row = PsUpsellMaster_Database::get_row( $sql_query );

	// Check if the result is not empty.
	if ( ! empty( $sql_row ) ) {
		$visits_id_list = ! empty( $sql_row->visits ) ? json_decode( $sql_row->visits ) : array();
		$visits_id_list = ! empty( $visits_id_list ) ? $visits_id_list : array();
	}

	// Reverse the order of the visits to show recently viewed first.
	$visits_id_list = array_reverse( $visits_id_list );

	// Remove empty and duplicate entries.
	$visits_id_list = array_unique( array_filter( $visits_id_list ) );

	// Return the visits id list.
	return $visits_id_list;
}

/**
 * Get the visits from the other users.
 *
 * @param int|false $max_visits The max visits.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_other_users( $max_visits = false ) {
	// Set the visits id list.
	$visits_id_list = array();

	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Build the SQL to get other user visits.
	$sql_select  = 'SELECT `t`.`visits`';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i AS `t`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';

	// Check if the visitor id is not empty.
	if ( ! empty( $visitor_id ) ) {
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `t`.`id` <> %d', $visitor_id );
	}

	$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `t`.`visits` <> %s', '' ); // Yes, empty is correct.
	$sql_where   = implode( ' ', $sql_where );
	$sql_orderby = 'ORDER BY `t`.updated_at DESC';
	$sql_query   = "{$sql_select} {$sql_from} {$sql_where} {$sql_orderby}";

	$sql_results = PsUpsellMaster_Database::get_results( $sql_query );

	// Set a maximum number of visits to get in order to avoid slowing things down.
	$count_visits = ! empty( $max_visits ) ? $max_visits : 100;

	// Check if the sql results is not empty.
	if ( ! empty( $sql_results ) ) {

		// Loop through the results.
		foreach ( $sql_results as $sql_result ) {
			$column_visits  = ! empty( $sql_result->visits ) ? json_decode( $sql_result->visits ) : array();
			$result_id_list = ! empty( $result_id_list ) ? $result_id_list : array();

			// Reverse the order of the visits to show recently viewed first.
			$result_id_list = array_reverse( $result_id_list );

			// Merge the result id list with the visits id list.
			$visits_id_list = array_merge( $visits_id_list, $result_id_list );

			// Remove empty and duplicate entries.
			$visits_id_list = array_unique( array_filter( $visits_id_list ) );

			// Check if the visits id list reached the maximum number of visits to get.
			if ( count( $visits_id_list ) >= $count_visits ) {
				// Get only the maximum number of visits.
				$visits_id_list = array_slice( $visits_id_list, 0, $count_visits );
			}
		}
	}

	// Return the visits id list.
	return $visits_id_list;
}

/**
 * Get the current product id from the product page.
 *
 * @deprecated 1.7.49 Use psupsellmaster_get_current_product_id().
 * @return int|false The product ID or false on failure.
 */
function psupsellmaster_get_current_product_id_from_product_page() {
	return psupsellmaster_get_current_product_id();
}

/**
 * Get the products list by source.
 *
 * @param string $source The source.
 * @param array  $args The arguments.
 * @return array The products list.
 */
function psupsellmaster_get_products_list_by_source( $source, $args = array() ) {
	// Set the products.
	$products = array();

	// Set the products id list.
	$products_id_list = array();

	// Check the source.
	switch ( $source ) {
		case 'store_cart':
			// Get the products from the store cart.
			$products_id_list = psupsellmaster_get_session_cart_product_ids();
			$products_id_list = ! empty( $products_id_list ) ? $products_id_list : array();

			// Exit the switch.
			break;

		case 'product':
			// Get the products from the current product.
			$products_id_list = psupsellmaster_get_current_product_id();
			$products_id_list = ! empty( $products_id_list ) ? array( $products_id_list ) : array();

			// Exit the switch.
			break;

		case 'order':
			// Get the products from the order related to the purchase receipt.
			$products_id_list = psupsellmaster_get_receipt_product_ids();
			$products_id_list = ! empty( $products_id_list ) ? $products_id_list : array();

			// Exit the switch.
			break;

		case 'user_visits':
			// Get the products from the current user visits.
			$products_id_list = psupsellmaster_get_visits_from_current_user();
			$products_id_list = ! empty( $products_id_list ) ? $products_id_list : array();

			// Exit the switch.
			break;

		case 'other_visits':
			// Get the products from other users visits.
			$products_id_list = psupsellmaster_get_visits_from_other_users();
			$products_id_list = ! empty( $products_id_list ) ? $products_id_list : array();

			// Exit the switch.
			break;

		case 'default':
			// Get the products from the settings.
			$products_id_list = PsUpsellMaster_Settings::get( 'default_upsell_products' );
			$products_id_list = is_array( $products_id_list ) ? $products_id_list : array();
			$products_id_list = ! empty( $products_id_list ) ? $products_id_list : array();

			// Exit the switch.
			break;

		case 'webhook':
			// Get the products from the webhook.
			$products_id_list = apply_filters( 'psupsellmaster_base_products_id_list', array(), $source, $args );
			$products_id_list = is_array( $products_id_list ) ? $products_id_list : array();
			$products_id_list = array_map( 'absint', $products_id_list );

			// Exit the switch.
			break;

	}

	// Loop through the products list.
	foreach ( $products_id_list as $product_id ) {
		// Set the product.
		$product = array(
			'product_id' => $product_id,
		);

		// Add the product to the list.
		array_push( $products, $product );
	}

	// Return the products.
	return $products;
}

/**
 * Get the upsells by products list.
 *
 * @param array $products_list The products list.
 * @return array The upsells list.
 */
function psupsellmaster_get_upsells_by_products_list( $products_list ) {
	// Set the upsells list.
	$upsells_list = array();

	// Get the products id list.
	$products_id_list = array_column( $products_list, 'product_id' );

	// Check if the products list is empty.
	if ( empty( $products_id_list ) ) {
		return $upsells_list;
	}

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $products_id_list ), '%d' ) );

	// Set the sql products.
	$sql_products = PsUpsellMaster_Database::prepare( "`psupsellmaster_scores`.`base_product_id` IN ( {$placeholders} )", $products_id_list );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`psupsellmaster_scores`.`base_product_id`,
			`psupsellmaster_scores`.`upsell_product_id`
		FROM
			%i AS `psupsellmaster_scores`
		WHERE
			1 = 1
		AND
			{$sql_products}
		AND
			NOT EXISTS (
				SELECT
					1
				FROM
					%i AS `postmeta`
				WHERE
					1 = 1
				AND
					`postmeta`.`post_id` = `psupsellmaster_scores`.`base_product_id`
				AND
					`postmeta`.`meta_key` = '_psupsellmaster_scores_disabled'
			)
		",
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ),
		PsUpsellMaster_Database::get_table_name( 'postmeta' )
	);

	// Get the rows.
	$rows = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Get the base product id.
		$base_product_id = ! empty( $row->base_product_id ) ? filter_var( $row->base_product_id, FILTER_VALIDATE_INT ) : false;

		// Check if the base product id is empty.
		if ( empty( $base_product_id ) ) {
			continue;
		}

		// Get the upsell product id.
		$upsell_product_id = ! empty( $row->upsell_product_id ) ? filter_var( $row->upsell_product_id, FILTER_VALIDATE_INT ) : false;

		// Check if the upsell product id is empty.
		if ( empty( $upsell_product_id ) ) {
			continue;
		}

		// Set the item.
		$item = array(
			'product_id'      => $upsell_product_id,
			'base_product_id' => $base_product_id,
		);

		// Add the item to the list.
		array_push( $upsells_list, $item );
	}

	// Return the upsells list.
	return $upsells_list;
}

/**
 * Merge current and additional upsells lists.
 *
 * @param array $args The arguments.
 * @return array The merged upsells.
 */
function psupsellmaster_get_merged_upsells_by_upsells_lists( $args ) {
	// Set the merged upsells.
	$merged_upsells = array();

	// Set the upsells id list.
	$upsells_id_list = array();

	// Get the current upsells.
	$current_upsells = ! empty( $args['current_upsells'] ) ? $args['current_upsells'] : array();

	// Get the additional upsells.
	$additional_upsells = ! empty( $args['additional_upsells'] ) ? $args['additional_upsells'] : array();

	// Set the all upsells.
	$all_upsells = array_merge( $current_upsells, $additional_upsells );

	// Loop through the all upsells.
	foreach ( $all_upsells as $data ) {
		// Get the product id.
		$product_id = isset( $data['product_id'] ) ? filter_var( $data['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the product id is already in the upsells id list.
		if ( in_array( $product_id, $upsells_id_list, true ) ) {
			continue;
		}

		// Add the product id to the upsells id list.
		array_push( $upsells_id_list, $product_id );

		// Add the data to the merged upsells.
		array_push( $merged_upsells, $data );
	}

	// Return the merged upsells.
	return $merged_upsells;
}

/**
 * Get the settings by location.
 *
 * @param string $location The location.
 * @return array The settings.
 */
function psupsellmaster_get_settings_by_location( $location ) {
	// Set the settings.
	$settings = array();

	// Check the location.
	if ( 'product' === $location ) {
		$settings['addtocart_button']        = PsUpsellMaster_Settings::get( 'product_page_addtocart_button' );
		$settings['display_type']            = PsUpsellMaster_Settings::get( 'product_page_display_type' );
		$settings['enable']                  = psupsellmaster_feature_is_active( 'page_product' );
		$settings['label_cta_text']          = PsUpsellMaster_Settings::get( 'product_page_label_cta_text' );
		$settings['label_title']             = PsUpsellMaster_Settings::get( 'product_page_label_title' );
		$settings['max_cols']                = PsUpsellMaster_Settings::get( 'product_page_max_cols' );
		$settings['max_prod']                = PsUpsellMaster_Settings::get( 'product_page_max_prod' );
		$settings['max_per_author']          = PsUpsellMaster_Settings::get( 'product_page_max_per_author' );
		$settings['title_length']            = PsUpsellMaster_Settings::get( 'product_page_title_length' );
		$settings['short_description_limit'] = PsUpsellMaster_Settings::get( 'product_page_short_description_limit' );
		$settings['show_type']               = PsUpsellMaster_Settings::get( 'product_page_show_type' );
	} elseif ( 'checkout' === $location ) {
		$settings['addtocart_button']        = PsUpsellMaster_Settings::get( 'checkout_page_addtocart_button' );
		$settings['display_type']            = PsUpsellMaster_Settings::get( 'checkout_page_display_type' );
		$settings['enable']                  = psupsellmaster_feature_is_active( 'page_checkout' );
		$settings['label_cta_text']          = PsUpsellMaster_Settings::get( 'checkout_page_label_cta_text' );
		$settings['label_title']             = PsUpsellMaster_Settings::get( 'checkout_page_label_title' );
		$settings['max_cols']                = PsUpsellMaster_Settings::get( 'checkout_page_max_cols' );
		$settings['max_prod']                = PsUpsellMaster_Settings::get( 'checkout_page_max_prod' );
		$settings['max_per_author']          = PsUpsellMaster_Settings::get( 'checkout_page_max_per_author' );
		$settings['title_length']            = PsUpsellMaster_Settings::get( 'checkout_page_title_length' );
		$settings['short_description_limit'] = PsUpsellMaster_Settings::get( 'checkout_page_short_description_limit' );
		$settings['show_type']               = PsUpsellMaster_Settings::get( 'checkout_page_show_type' );
	} elseif ( 'purchase_receipt' === $location ) {
		$settings['addtocart_button']        = PsUpsellMaster_Settings::get( 'purchase_receipt_page_addtocart_button' );
		$settings['display_type']            = PsUpsellMaster_Settings::get( 'purchase_receipt_page_display_type' );
		$settings['enable']                  = psupsellmaster_feature_is_active( 'page_purchase_receipt' );
		$settings['label_cta_text']          = PsUpsellMaster_Settings::get( 'purchase_receipt_page_label_cta_text' );
		$settings['label_title']             = PsUpsellMaster_Settings::get( 'purchase_receipt_page_label_title' );
		$settings['max_cols']                = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_cols' );
		$settings['max_prod']                = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_prod' );
		$settings['max_per_author']          = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_per_author' );
		$settings['title_length']            = PsUpsellMaster_Settings::get( 'purchase_receipt_page_title_length' );
		$settings['short_description_limit'] = PsUpsellMaster_Settings::get( 'purchase_receipt_page_short_description_limit' );
		$settings['show_type']               = PsUpsellMaster_Settings::get( 'purchase_receipt_page_show_type' );
	} elseif ( 'popup_add_to_cart' === $location ) {
		$settings['excluded_pages']          = PsUpsellMaster_Settings::get( 'add_to_cart_popup_excluded_pages' );
		$settings['addtocart_button']        = PsUpsellMaster_Settings::get( 'add_to_cart_popup_addtocart_button' );
		$settings['display_type']            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_display_type' );
		$settings['enable']                  = psupsellmaster_feature_is_active( 'popup_add_to_cart' );
		$settings['headline']                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_headline' );
		$settings['tagline']                 = PsUpsellMaster_Settings::get( 'add_to_cart_popup_tagline' );
		$settings['button_checkout']         = PsUpsellMaster_Settings::get( 'add_to_cart_popup_button_checkout' );
		$settings['label_cta_text']          = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_cta_text' );
		$settings['label_title']             = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_title' );
		$settings['max_cols']                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_cols' );
		$settings['max_prod']                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_prod' );
		$settings['max_per_author']          = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_per_author' );
		$settings['title_length']            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_title_length' );
		$settings['short_description_limit'] = PsUpsellMaster_Settings::get( 'add_to_cart_popup_short_description_limit' );
		$settings['show_type']               = PsUpsellMaster_Settings::get( 'add_to_cart_popup_show_type' );
	} elseif ( 'popup_exit_intent' === $location ) {
		$settings['excluded_pages']          = PsUpsellMaster_Settings::get( 'exit_intent_popup_excluded_pages' );
		$settings['addtocart_button']        = PsUpsellMaster_Settings::get( 'exit_intent_popup_addtocart_button' );
		$settings['display_type']            = PsUpsellMaster_Settings::get( 'exit_intent_popup_display_type' );
		$settings['enable']                  = psupsellmaster_feature_is_active( 'popup_exit_intent' );
		$settings['label_cta_text']          = PsUpsellMaster_Settings::get( 'exit_intent_popup_label_cta_text' );
		$settings['label_title']             = PsUpsellMaster_Settings::get( 'exit_intent_popup_label_title' );
		$settings['max_cols']                = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_cols' );
		$settings['max_prod']                = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_prod' );
		$settings['max_per_author']          = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_per_author' );
		$settings['max_shows']               = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_shows' );
		$settings['title_length']            = PsUpsellMaster_Settings::get( 'exit_intent_popup_title_length' );
		$settings['short_description_limit'] = PsUpsellMaster_Settings::get( 'exit_intent_popup_short_description_limit' );
		$settings['show_type']               = PsUpsellMaster_Settings::get( 'exit_intent_popup_show_type' );
	}

	// Get the algorithm logic setting.
	$algorithm_logic = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Set the algorithm logic setting.
	$settings['algorithm_logic'] = array();

	// Set the priority only setting.
	$settings['algorithm_logic']['priority_only'] = false;

	// Check if the priority only from the algorithm logic does exist.
	if ( isset( $algorithm_logic['priority_only'] ) ) {
		// Set the priority only.
		$settings['algorithm_logic']['priority_only'] = filter_var( $algorithm_logic['priority_only'], FILTER_VALIDATE_BOOLEAN );
	}

	// Return the settings.
	return $settings;
}

/**
 * Get upsells from tracking visitors.
 *
 * @param array $args The arguments.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_tracking_upsells( $args = array() ) {
	// Set the upsells list.
	$upsells_list = array();

	// Set the campaign args.
	$campaign_args = array(
		'locations' => array( $args['location'] ),
	);

	// Get the campaign data.
	$campaign_data = psupsellmaster_get_single_eligible_campaign_by_filters( $campaign_args );

	// Get the campaign id.
	$campaign_id = isset( $campaign_data['campaign_id'] ) ? filter_var( $campaign_data['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Get the campaign meta.
	$campaign_meta = isset( $campaign_data['campaign_meta'] ) ? $campaign_data['campaign_meta'] : array();

	// Check if there is a campaign and if it has selected products.
	if ( false !== $campaign_id && 'selected' === $campaign_meta['products_flag'] ) {
		// Get the campaign products.
		$campaign_products = isset( $campaign_data['products'] ) ? $campaign_data['products'] : array();

		// Loop through the campaign products.
		foreach ( $campaign_products as $product_id ) {
			// Set the product.
			$product = array(
				'campaign_id' => $campaign_id,
				'product_id'  => $product_id,
			);

			// Add the product to the list.
			array_push( $upsells_list, $product );
		}

		// Set the filter args.
		$filter_args = $args;

		// Add the upsells list as additional args.
		$filter_args['products'] = $upsells_list;

		// Filter out invalid products.
		$upsells_list = psupsellmaster_filter_valid_products( $filter_args );

		// Randomly sort the list.
		shuffle( $upsells_list );

		// Check the upsells list.
		if ( ! empty( $upsells_list ) ) {
			// Return the upsells list.
			return $upsells_list;
		}
	}

	// Get the settings from the arguments.
	$settings = ! empty( $args['settings'] ) ? $args['settings'] : array();

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['min_qty'] ) ? intval( $settings['min_qty'] ) : 1;

	// Get the products sources.
	$products_sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

	// Loop through the products sources.
	foreach ( $products_sources as $products_source ) {
		// Get the products list.
		$products_list = psupsellmaster_get_products_list_by_source( $products_source, $args );

		// Check the source because some are not base products.
		if ( in_array( $products_source, array( 'default' ), true ) ) {
			// Get the additional upsells list from the settings.
			$additional_upsells_list = $products_list;
		} else {
			// Get the additional upsells list.
			$additional_upsells_list = psupsellmaster_get_upsells_by_products_list( $products_list );
		}

		// Set the get merged args.
		$get_merged_args = $args;

		// Add the current upsells as additional args.
		$get_merged_args['current_upsells'] = $upsells_list;

		// Add the additional upsells as additional args.
		$get_merged_args['additional_upsells'] = $additional_upsells_list;

		// Merge the current upsells with additional upsells.
		$upsells_list = psupsellmaster_get_merged_upsells_by_upsells_lists( $get_merged_args );

		// Set the filter args.
		$filter_args = $args;

		// Add the upsells list as additional args.
		$filter_args['products'] = $upsells_list;

		// Filter out invalid products.
		$upsells_list = psupsellmaster_filter_valid_products( $filter_args );

		// Check if the minimum quantity of upsells requested was reached.
		if ( count( $upsells_list ) >= $min_qty_upsells ) {
			// Stop the loop.
			break;
		}
	}

	// Check if there is a campaign and if it has all products selected.
	if ( false !== $campaign_id && 'all' === $campaign_meta['products_flag'] ) {
		// Loop through the upsells list.
		foreach ( $upsells_list as $key => $data ) {
			// Set the campaign id.
			$upsells_list[ $key ]['campaign_id'] = $campaign_id;
		}
	}

	// Return the upsells list.
	return $upsells_list;
}

/**
 * Get the upsells from the block.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_feature_block( $settings ) {
	// Set the location.
	$location = 'block';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook', 'store_cart', 'user_visits', 'other_visits', 'default' ),
	);

	// Check if the source product is enabled.
	if ( isset( $settings['source_product'] ) && true === filter_var( $settings['source_product'], FILTER_VALIDATE_BOOLEAN ) ) {

		// Check the current page.
		if ( psupsellmaster_is_page_product() ) {
			// Get the sources.
			$sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

			// Set the source index.
			$source_index = intval( array_search( 'webhook', $sources, true ) ) + 1;

			// Add the product source after the webhook source.
			array_splice( $sources, $source_index, 0, 'product' );

			// Replace the sources.
			$args['sources'] = $sources;
		}
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the shortcode.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_feature_shortcode( $settings ) {
	// Set the location.
	$location = 'shortcode';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook', 'store_cart', 'user_visits', 'other_visits', 'default' ),
	);

	// Check the current page.
	if ( psupsellmaster_is_page_product() ) {
		// Get the sources.
		$sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

		// Set the source index.
		$source_index = intval( array_search( 'webhook', $sources, true ) ) + 1;

		// Add the product source after the webhook source.
		array_splice( $sources, $source_index, 0, 'product' );

		// Replace the sources.
		$args['sources'] = $sources;
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the widget.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_feature_widget( $settings ) {
	// Set the location.
	$location = 'widget';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_prod'] ) ? intval( $settings['max_prod'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook', 'store_cart', 'user_visits', 'other_visits', 'default' ),
	);

	// Check if the source product is enabled.
	if ( isset( $settings['source_product'] ) && true === filter_var( $settings['source_product'], FILTER_VALIDATE_BOOLEAN ) ) {

		// Check the current page.
		if ( psupsellmaster_is_page_product() ) {
			// Get the sources.
			$sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

			// Set the source index.
			$source_index = intval( array_search( 'webhook', $sources, true ) ) + 1;

			// Add the product source after the webhook source.
			array_splice( $sources, $source_index, 0, 'product' );

			// Replace the sources.
			$args['sources'] = $sources;
		}
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the elementor widget.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_feature_elementor_widget( $settings ) {
	// Set the location.
	$location = 'elementor_widget';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_prod'] ) ? intval( $settings['max_prod'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook', 'store_cart', 'user_visits', 'other_visits', 'default' ),
	);

	// Check if the source product is enabled.
	if ( isset( $settings['source_product'] ) && true === filter_var( $settings['source_product'], FILTER_VALIDATE_BOOLEAN ) ) {

		// Check the current page.
		if ( psupsellmaster_is_page_product() ) {
			// Get the sources.
			$sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

			// Set the source index.
			$source_index = intval( array_search( 'webhook', $sources, true ) ) + 1;

			// Add the product source after the webhook source.
			array_splice( $sources, $source_index, 0, 'product' );

			// Replace the sources.
			$args['sources'] = $sources;
		}
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the checkout page.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_page_checkout( $settings ) {
	// Set the location.
	$location = 'checkout';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'store_cart', 'default' ),
	);

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the product page.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_page_product( $settings ) {
	// Set the location.
	$location = 'product';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'product' ),
	);

	// Check if the priority only setting is false.
	if ( false === $settings['algorithm_logic']['priority_only'] ) {
		// Add other sources.
		array_push( $args['sources'], 'default' );
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the purchase receipt page.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_page_purchase_receipt( $settings ) {
	// Set the location.
	$location = 'purchase_receipt';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'order', 'default' ),
	);

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the add to cart popup.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_popup_add_to_cart( $settings ) {
	// Set the location.
	$location = 'popup_add_to_cart';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook' ),
	);

	// Check if the priority only setting is false.
	if ( false === $settings['algorithm_logic']['priority_only'] ) {
		// Add other sources.
		array_push( $args['sources'], 'store_cart' );
		array_push( $args['sources'], 'user_visits' );
		array_push( $args['sources'], 'other_visits' );
		array_push( $args['sources'], 'default' );
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Get the upsells from the exit intent popup.
 *
 * @param array $settings The settings.
 * @return array The upsells.
 */
function psupsellmaster_get_upsells_from_popup_exit_intent( $settings ) {
	// Set the location.
	$location = 'popup_exit_intent';

	// Set the minimum quantity of upsells to get.
	$min_qty_upsells = ! empty( $settings['max_cols'] ) ? intval( $settings['max_cols'] ) : 1;

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $min_qty_upsells,
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'webhook', 'store_cart', 'user_visits', 'other_visits', 'default' ),
	);

	// Check the current page.
	if ( psupsellmaster_is_page_product() ) {
		// Get the sources.
		$sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

		// Set the source index.
		$source_index = intval( array_search( 'webhook', $sources, true ) ) + 1;

		// Add the product source after the webhook source.
		array_splice( $sources, $source_index, 0, 'product' );

		// Replace the sources.
		$args['sources'] = $sources;
	}

	// Return the upsells.
	return psupsellmaster_get_upsells_from_tracking_upsells( $args );
}

/**
 * Merge current and additional visits lists.
 *
 * @param array $args The arguments.
 * @return array The merged visits.
 */
function psupsellmaster_get_merged_visits_by_visits_lists( $args ) {
	// Set the merged visits.
	$merged_visits = array();

	// Set the visits id list.
	$visits_id_list = array();

	// Get the current visits.
	$current_visits = ! empty( $args['current_visits'] ) ? $args['current_visits'] : array();

	// Get the additional visits.
	$additional_visits = ! empty( $args['additional_visits'] ) ? $args['additional_visits'] : array();

	// Merge the current visits with the additional visits.
	$all_visits = array_merge( $current_visits, $additional_visits );

	// Loop through the all visits.
	foreach ( $all_visits as $data ) {
		// Get the product id.
		$product_id = isset( $data['product_id'] ) ? filter_var( $data['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the product id is already in the visits id list.
		if ( in_array( $product_id, $visits_id_list, true ) ) {
			continue;
		}

		// Add the product id to the visits id list.
		array_push( $visits_id_list, $product_id );

		// Add the data to the merged visits.
		array_push( $merged_visits, $data );
	}

	// Return the merged visits.
	return $merged_visits;
}

/**
 * Filter out invalid products and return valid products only.
 *
 * @param array $args The arguments.
 * @return array The valid products.
 */
function psupsellmaster_filter_valid_products( $args ) {
	// Set the valid products.
	$valid_products = array();

	// Get the products list.
	$products_list = ! empty( $args['products'] ) ? $args['products'] : array();

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		// Return valid products.
		return $valid_products;
	}

	// Get the settings.
	$settings = ! empty( $args['settings'] ) ? $args['settings'] : array();

	// Set the remove products id list.
	$remove_products_id_list = array();

	// Check the page type.
	if ( psupsellmaster_is_page_purchase_receipt() ) {
		// Get the order products id list.
		$order_products_id_list = psupsellmaster_get_receipt_product_ids();

		// Add the order products id list to the remove products id list.
		$remove_products_id_list = array_merge( $remove_products_id_list, $order_products_id_list );

		// Check the page type.
	} elseif ( psupsellmaster_is_page_product() ) {
		// Get the current product id.
		$current_product_id = psupsellmaster_get_current_product_id();

		// Check if the current product id is valid.
		if ( false !== $current_product_id ) {
			// Add the current product id to the remove products id list.
			array_push( $remove_products_id_list, $current_product_id );
		}
	}

	// Get products id list from the store cart.
	$store_cart_products_id_list = psupsellmaster_get_session_cart_product_ids();

	// Add the store cart products id list to the remove products id list.
	$remove_products_id_list = array_merge( $remove_products_id_list, $store_cart_products_id_list );

	// Set the ignore products id list.
	$ignore_products_id_list = apply_filters( 'psupsellmaster_ignore_products_id_list', array() );
	$ignore_products_id_list = array_map( 'absint', $ignore_products_id_list );

	// Merge the lists.
	$remove_products_id_list = array_merge( $remove_products_id_list, $ignore_products_id_list );

	// Filter out empty and duplicate entries.
	$remove_products_id_list = array_unique( array_filter( $remove_products_id_list ) );

	// Loop through the products list.
	foreach ( $products_list as $product ) {
		// Get the product id.
		$product_id = isset( $product['product_id'] ) ? filter_var( $product['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the product id is in the remove products id list.
		if ( in_array( $product_id, $remove_products_id_list, true ) ) {
			continue;
		}

		// Add the product to the valid products.
		array_push( $valid_products, $product );
	}

	// Set the products list.
	$products_list = $valid_products;

	// Set the valid products.
	$valid_products = array();

	// Set the author products list.
	$author_products_list = array();

	// Get the setting max qty per author.
	$setting_max_qty_author = ! empty( $settings['max_qty_author'] ) ? intval( $settings['max_qty_author'] ) : 0;

	// Loop through the products list.
	foreach ( $products_list as $product ) {
		// Get the product id.
		$product_id = isset( $product['product_id'] ) ? filter_var( $product['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the product is not published.
		if ( 'publish' !== get_post_status( $product_id ) ) {
			continue;
		}

		// Check if the setting max per author is not empty.
		if ( ! empty( $setting_max_qty_author ) ) {
			// Get the product author id.
			$product_author_id = get_post_field( 'post_author', $product_id );
			$product_author_id = intval( $product_author_id );

			// Check if the product author id is empty.
			if ( empty( $product_author_id ) ) {
				continue;
			}

			// Check if the author id is empty within the list.
			if ( empty( $author_products_list[ $product_author_id ] ) ) {
				// Set the author products list.
				$author_products_list[ $product_author_id ] = array();
			}

			// Check if the total number of products per author was reached.
			if ( count( $author_products_list[ $product_author_id ] ) >= $setting_max_qty_author ) {
				continue;
			}

			// Add the product to the author products list.
			array_push( $author_products_list[ $product_author_id ], $product_id );
		}

		// Add the product to the valid products list.
		array_push( $valid_products, $product );
	}

	// Return valid products.
	return $valid_products;
}

/**
 * Render products by items list.
 *
 * @param array  $products_list The products list.
 * @param string $location The location.
 * @param array  $settings The settings.
 */
function psupsellmaster_render_products_by_items_list( $products_list, $location, $settings = array() ) {
	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Set the settings variables.
	$cta_text                = false;
	$section_title           = false;
	$display_type            = false;
	$show                    = false;
	$max_cols                = false;
	$max_products            = false;
	$max_per_author          = false;
	$addtocart_button        = false;
	$title_length            = false;
	$short_description_limit = false;

	// Check the location.
	if ( 'product' === $location ) {
		$cta_text                = PsUpsellMaster_Settings::get( 'product_page_label_cta_text' );
		$section_title           = PsUpsellMaster_Settings::get( 'product_page_label_title' );
		$display_type            = PsUpsellMaster_Settings::get( 'product_page_display_type' );
		$show                    = PsUpsellMaster_Settings::get( 'product_page_show_type' );
		$max_cols                = PsUpsellMaster_Settings::get( 'product_page_max_cols' );
		$max_products            = PsUpsellMaster_Settings::get( 'product_page_max_prod' );
		$max_products_per_author = PsUpsellMaster_Settings::get( 'product_page_max_per_author' );
		$addtocart_button        = PsUpsellMaster_Settings::get( 'product_page_addtocart_button' );
		$title_length            = PsUpsellMaster_Settings::get( 'product_page_title_length' );
		$short_description_limit = PsUpsellMaster_Settings::get( 'product_page_short_description_limit' );

		// Check the location.
	} elseif ( 'purchase_receipt' === $location ) {
		$cta_text                = PsUpsellMaster_Settings::get( 'purchase_receipt_page_label_cta_text' );
		$section_title           = PsUpsellMaster_Settings::get( 'purchase_receipt_page_label_title' );
		$display_type            = PsUpsellMaster_Settings::get( 'purchase_receipt_page_display_type' );
		$show                    = PsUpsellMaster_Settings::get( 'purchase_receipt_page_show_type' );
		$max_cols                = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_cols' );
		$max_products            = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_prod' );
		$max_products_per_author = PsUpsellMaster_Settings::get( 'purchase_receipt_page_max_per_author' );
		$addtocart_button        = PsUpsellMaster_Settings::get( 'purchase_receipt_page_addtocart_button' );
		$title_length            = PsUpsellMaster_Settings::get( 'purchase_receipt_page_title_length' );
		$short_description_limit = PsUpsellMaster_Settings::get( 'purchase_receipt_page_short_description_limit' );

		// Check the location.
	} elseif ( 'checkout' === $location ) {
		$cta_text                = PsUpsellMaster_Settings::get( 'checkout_page_label_cta_text' );
		$section_title           = PsUpsellMaster_Settings::get( 'checkout_page_label_title' );
		$display_type            = PsUpsellMaster_Settings::get( 'checkout_page_display_type' );
		$show                    = PsUpsellMaster_Settings::get( 'checkout_page_show_type' );
		$max_cols                = PsUpsellMaster_Settings::get( 'checkout_page_max_cols' );
		$max_products            = PsUpsellMaster_Settings::get( 'checkout_page_max_prod' );
		$max_products_per_author = PsUpsellMaster_Settings::get( 'checkout_page_max_per_author' );
		$addtocart_button        = PsUpsellMaster_Settings::get( 'checkout_page_addtocart_button' );
		$title_length            = PsUpsellMaster_Settings::get( 'checkout_page_title_length' );
		$short_description_limit = PsUpsellMaster_Settings::get( 'checkout_page_short_description_limit' );

		// Check the location.
	} elseif ( 'popup_add_to_cart' === $location ) {
		$cta_text                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_cta_text' );
		$section_title           = PsUpsellMaster_Settings::get( 'add_to_cart_popup_label_title' );
		$display_type            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_display_type' );
		$show                    = PsUpsellMaster_Settings::get( 'add_to_cart_popup_show_type' );
		$max_cols                = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_cols' );
		$max_products            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_prod' );
		$max_products_per_author = PsUpsellMaster_Settings::get( 'add_to_cart_popup_max_per_author' );
		$addtocart_button        = PsUpsellMaster_Settings::get( 'add_to_cart_popup_addtocart_button' );
		$title_length            = PsUpsellMaster_Settings::get( 'add_to_cart_popup_title_length' );
		$short_description_limit = PsUpsellMaster_Settings::get( 'add_to_cart_popup_short_description_limit' );

		// Check the location.
	} elseif ( 'popup_exit_intent' === $location ) {
		$cta_text                = PsUpsellMaster_Settings::get( 'exit_intent_popup_label_cta_text' );
		$section_title           = PsUpsellMaster_Settings::get( 'exit_intent_popup_label_title' );
		$display_type            = PsUpsellMaster_Settings::get( 'exit_intent_popup_display_type' );
		$show                    = PsUpsellMaster_Settings::get( 'exit_intent_popup_show_type' );
		$max_cols                = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_cols' );
		$max_products            = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_prod' );
		$max_products_per_author = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_per_author' );
		$max_shows               = PsUpsellMaster_Settings::get( 'exit_intent_popup_max_shows' );
		$addtocart_button        = PsUpsellMaster_Settings::get( 'exit_intent_popup_addtocart_button' );
		$title_length            = PsUpsellMaster_Settings::get( 'exit_intent_popup_title_length' );
		$short_description_limit = PsUpsellMaster_Settings::get( 'exit_intent_popup_short_description_limit' );

		// Otherwise...
	} else {
		$cta_text                = $settings['label_cta_text'];
		$section_title           = $settings['label_title'];
		$display_type            = $settings['display_type'];
		$show                    = $settings['show_type'];
		$max_cols                = $settings['max_cols'];
		$max_products            = $settings['max_prod'];
		$max_products_per_author = $settings['max_per_author'];
		$addtocart_button        = $settings['addtocart_button'];
		$title_length            = $settings['title_length'];
		$short_description_limit = $settings['short_description_limit'];
	}

	if ( $max_cols > 8 ) {
		$max_cols = 8;
	} elseif ( $max_cols <= 0 ) {
		$max_cols = 1;
	}

	if ( $max_products <= 0 ) {
		$max_products = $max_cols;
	}

	$product_card_args = array(
		'addtocart_button'        => $addtocart_button,
		'title_length'            => $title_length,
		'short_description_limit' => $short_description_limit,
		'location'                => $location,
		'source'                  => $show,
		'view'                    => $display_type,
	);

	$per_page = 'list' === $display_type ? $max_cols : $max_products;

	if ( 'widget' === $location && 'list' === $display_type ) {
		$per_page = $max_products;
	}

	// Get the products.
	$products = array_slice( $products_list, 0, $per_page );

	// Check if the products is empty.
	if ( empty( $products ) ) {
		return false;
	}

	// Set the campaigns.
	$campaigns = array();

	// Loop through the products.
	foreach ( $products as $product ) {
		// Get the campaign id.
		$campaign_id = isset( $product['campaign_id'] ) ? filter_var( $product['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Check if the campaign is not yet in the list.
		if ( ! in_array( $campaign_id, $campaigns, true ) ) {
			// Add the campaign to the list.
			array_push( $campaigns, $campaign_id );
		}
	}

	// Allow developers to filter this.
	$section_title = apply_filters( 'psupsellmaster_products_title', $section_title, $location );

	// Allow developers to filter this.
	$cta_text = apply_filters( 'psupsellmaster_products_cta', $cta_text, $location );

	// Check if the campaigns is not empty.
	if ( ! empty( $campaigns ) ) {
		// Set the event date.
		$event_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$event_date = $event_date->format( 'Y-m-d' );

		// Loop through the campaigns.
		foreach ( $campaigns as $campaign_id ) {
			// Set the event data.
			$event_data = array(
				'campaign_id' => $campaign_id,
				'event_date'  => $event_date,
				'event_name'  => 'impression',
				'location'    => $location,
				'quantity'    => 1,
			);

			// Increment the campaign events quantity.
			psupsellmaster_increase_campaign_events_quantity( $event_data );
		}

		// Get the campaign id (from the first campaign found).
		$campaign_id = reset( $campaigns );

		// Set the args.
		$args = array(
			'campaign_id' => $campaign_id,
			'location'    => $location,
		);

		// Render the campaign banner data.
		psupsellmaster_render_campaign_banner_data_from_locations( $args );
	}

	// Set the html id.
	$html_id = '';

	// Check the location.
	switch ( $location ) {
		case 'product':
			// Set the html_id.
			$html_id = 'psupsellmaster-products-product-page';
			break;
		case 'purchase_receipt':
			// Set the html_id.
			$html_id = 'psupsellmaster-products-purchase-receipt-page';
			break;
		case 'checkout':
			// Set the html_id.
			$html_id = 'psupsellmaster-products-checkout-page';
			break;
	}
	?>
	<div class="psupsellmaster psupsellmaster-product display-type-<?php echo esc_attr( $display_type ); ?>" id="<?php echo esc_attr( $html_id ); ?>" data-page="<?php echo esc_attr( $location ); ?>" data-display-type="<?php echo esc_attr( $display_type ); ?>" data-show="<?php echo esc_attr( $show ); ?>">
		<?php if ( empty( $campaigns ) ) : ?>
			<?php if ( ! empty( $section_title ) ) : ?>
				<h3 class="psupsellmaster-product-section-title"><?php echo esc_html( $section_title ); ?></h3>
			<?php endif; ?>

			<?php if ( ! empty( $cta_text ) ) : ?>
				<h6 class="psupsellmaster-product-section-tag-line"><?php echo esc_html( $cta_text ); ?></h6>
			<?php endif; ?>
		<?php endif; ?>
		<div class="psupsellmaster-product-<?php echo esc_attr( $display_type ); ?>">
			<?php if ( 'list' === $display_type ) : ?>
				<div class="psupsellmaster-<?php echo esc_attr( $display_type ); ?> psupsellmaster-container">
					<?php
					$sm_class = count( $products ) < 2 ? 'row-cols-sm-1' : 'row-cols-sm-2';
					$md_class = count( $products ) < 3 ? 'row-cols-md-2' : 'row-cols-md-3';
					$lg_class = "row-cols-lg-{$max_cols}";

					if ( in_array( $location, array( 'popup-add-to-cart', 'popup-exit-intent', 'widget' ), true ) ) {
						$sm_class = 'row-cols-sm-1';
						$md_class = 'row-cols-md-1';
					}

					?>
					<div class="psupsellmaster-row row-cols-1 <?php echo esc_attr( "{$sm_class} {$md_class} {$lg_class}" ); ?>">

						<?php foreach ( $products as $product ) : ?>
							<?php
							// Get the product id.
							$product_id = isset( $product['product_id'] ) ? filter_var( $product['product_id'], FILTER_VALIDATE_INT ) : false;

							// Check if the product id is empty.
							if ( empty( $product_id ) ) {
								continue;
							}

							// Get the campaign id.
							$campaign_id = isset( $product['campaign_id'] ) ? filter_var( $product['campaign_id'], FILTER_VALIDATE_INT ) : false;

							// Setup the post data.
							setup_postdata( $product_id );

							// Get the base product id.
							$base_product_id = isset( $product['base_product_id'] ) ? filter_var( $product['base_product_id'], FILTER_VALIDATE_INT ) : false;

							// Set the args.
							$product_card_args['product_id']      = $product_id;
							$product_card_args['base_product_id'] = $base_product_id;
							$product_card_args['campaign_id']     = $campaign_id;
							?>
							<div class="col card-mb">
								<?php psupsellmaster_get_product_card( $product_card_args ); ?>
							</div>
						<?php endforeach; ?>

						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			<?php else : ?>
				<div class="owl-carousel owl-theme psupsellmaster-owl-carousel" data-products-carousel="<?php echo esc_attr( $max_cols ); ?>" data-products-max="<?php echo esc_attr( count( $products ) ); ?>" data-type="<?php echo esc_attr( $location ); ?>">

					<?php foreach ( $products as $product ) : ?>
						<?php
						// Get the product id.
						$product_id = isset( $product['product_id'] ) ? filter_var( $product['product_id'], FILTER_VALIDATE_INT ) : false;

						// Check if the product id is empty.
						if ( empty( $product_id ) ) {
							continue;
						}

						// Setup the post data.
						setup_postdata( $product_id );

						// Get the campaign id.
						$campaign_id = isset( $product['campaign_id'] ) ? filter_var( $product['campaign_id'], FILTER_VALIDATE_INT ) : false;

						// Get the base product id.
						$base_product_id = isset( $product['base_product_id'] ) ? filter_var( $product['base_product_id'], FILTER_VALIDATE_INT ) : false;

						// Set the args.
						$product_card_args['product_id']      = $product_id;
						$product_card_args['base_product_id'] = $base_product_id;
						$product_card_args['campaign_id']     = $campaign_id;
						?>
						<div class="item">
							<?php psupsellmaster_get_product_card( $product_card_args ); ?>
						</div>
					<?php endforeach; ?>

					<?php wp_reset_postdata(); ?>
				</div>
				<div class="psupsellmaster-owl-carousel-nav">
					<a href="#" class="psupsellmaster-carousel-nav-prev" style="left: 0; z-index: 100000;"><span>&lsaquo;</span></a>
					<a href="#" class="psupsellmaster-carousel-nav-next" style="right: 0; z-index: 100000;"><span>&rsaquo;</span></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Get visits from tracking visitors.
 *
 * @param array $args The arguments.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_tracking_visitors( $args = array() ) {
	// Set the visits list.
	$visits_list = array();

	// Set the campaign args.
	$campaign_args = array(
		'locations' => array( $args['location'] ),
	);

	// Get the campaign data.
	$campaign_data = psupsellmaster_get_single_eligible_campaign_by_filters( $campaign_args );

	// Get the campaign id.
	$campaign_id = isset( $campaign_data['campaign_id'] ) ? filter_var( $campaign_data['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Get the campaign meta.
	$campaign_meta = isset( $campaign_data['campaign_meta'] ) ? $campaign_data['campaign_meta'] : array();

	// Check if there is a campaign and if it has selected products.
	if ( false !== $campaign_id && 'selected' === $campaign_meta['products_flag'] ) {
		// Get the campaign products.
		$campaign_products = isset( $campaign_data['products'] ) ? $campaign_data['products'] : array();

		// Loop through the campaign products.
		foreach ( $campaign_products as $product_id ) {
			// Set the product.
			$product = array(
				'campaign_id' => $campaign_id,
				'product_id'  => $product_id,
			);

			// Add the product to the list.
			array_push( $visits_list, $product );
		}

		// Set the filter args.
		$filter_args = $args;

		// Add the visits list as additional args.
		$filter_args['products'] = $visits_list;

		// Filter out invalid products.
		$visits_list = psupsellmaster_filter_valid_products( $filter_args );

		// Randomly sort the list.
		shuffle( $visits_list );

		// Check the visits list.
		if ( ! empty( $visits_list ) ) {
			// Return the visits list.
			return $visits_list;
		}
	}

	// Get the settings from the arguments.
	$settings = ! empty( $args['settings'] ) ? $args['settings'] : array();

	// Set the minimum quantity of visits to get.
	$min_qty_visits = ! empty( $settings['min_qty'] ) ? intval( $settings['min_qty'] ) : 1;

	// Get the products sources.
	$products_sources = ! empty( $args['sources'] ) ? $args['sources'] : array();

	// Loop through the products sources.
	foreach ( $products_sources as $products_source ) {
		// Get the products list.
		$products_list = psupsellmaster_get_products_list_by_source( $products_source, $args );

		// Set the get merged args.
		$get_merged_args = $args;

		// Add the current visits as additional args.
		$get_merged_args['current_visits'] = $visits_list;

		// Add the additional visits as additional args.
		$get_merged_args['additional_visits'] = $products_list;

		// Merge the current visits with additional visits.
		$visits_list = psupsellmaster_get_merged_visits_by_visits_lists( $get_merged_args );

		// Set the filter args.
		$filter_args = $args;

		// Add the visits list as additional args.
		$filter_args['products'] = $visits_list;

		// Filter out invalid products.
		$visits_list = psupsellmaster_filter_valid_products( $filter_args );

		// Check if the minimum quantity of visits requested was reached.
		if ( count( $visits_list ) >= $min_qty_visits ) {
			break;
		}
	}

	// Check there is a campaign and if it has all products selected.
	if ( false !== $campaign_id && 'all' === $campaign_meta['products_flag'] ) {
		// Loop through the upsells list.
		foreach ( $visits_list as $key => $data ) {
			// Set the campaign id.
			$visits_list[ $key ]['campaign_id'] = $campaign_id;
		}
	}

	// Return the visits list.
	return $visits_list;
}

/**
 * Get the visits from the block.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_feature_block( $settings ) {
	// Set the location.
	$location = 'block';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the shortcode.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_feature_shortcode( $settings ) {
	// Set the location.
	$location = 'shortcode';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the widget.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_feature_widget( $settings ) {
	// Set the location.
	$location = 'widget';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_prod'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the elementor widget.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_feature_elementor_widget( $settings ) {
	// Set the location.
	$location = 'elementor_widget';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_prod'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the checkout page.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_page_checkout( $settings ) {
	// Set the location.
	$location = 'checkout';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the product page.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_page_product( $settings ) {
	// Set the location.
	$location = 'product';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the purchase receipt page.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_page_purchase_receipt( $settings ) {
	// Set the location.
	$location = 'purchase_receipt';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the add to cart popup.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_popup_add_to_cart( $settings ) {
	// Set the location.
	$location = 'popup_add_to_cart';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Get the visits from the exit intent popup.
 *
 * @param array $settings The settings.
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_popup_exit_intent( $settings ) {
	// Set the location.
	$location = 'popup_exit_intent';

	// Set the maximum quantity of products per author to get.
	$max_qty_per_author = ! empty( $settings['max_per_author'] ) ? intval( $settings['max_per_author'] ) : 0;

	// Set the arguments.
	$args = array(
		'location' => $location,
		'settings' => array(
			'min_qty'        => $settings['max_cols'],
			'max_qty_author' => $max_qty_per_author,
		),
		'sources'  => array( 'user_visits', 'other_visits', 'default' ),
	);

	// Return the visits.
	return psupsellmaster_get_visits_from_tracking_visitors( $args );
}

/**
 * Render products from feature block.
 *
 * @param array $settings The settings.
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_feature_block( $settings ) {
	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_feature_block( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_feature_block( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, 'block', $settings );
}

/**
 * Render products from feature shortcode.
 *
 * @param array $settings The settings.
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_feature_shortcode( $settings ) {
	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_feature_shortcode( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_feature_shortcode( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, 'shortcode', $settings );
}

/**
 * Render products from feature widget.
 *
 * @param array $settings The settings.
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_feature_widget( $settings ) {
	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_feature_widget( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_feature_widget( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, 'widget', $settings );
}

/**
 * Render products from feature elementor widget.
 *
 * @param array $settings The settings.
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_feature_elementor_widget( $settings ) {
	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_feature_elementor_widget( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_feature_elementor_widget( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, 'elementor_widget', $settings );
}

/**
 * Render products from page purchase receipt.
 *
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_page_purchase_receipt() {
	// Set the page type.
	$page_type = 'purchase_receipt';

	// Get the page settings.
	$settings = psupsellmaster_get_settings_by_location( $page_type );

	// Check if the page is not enable.
	if ( empty( $settings['enable'] ) ) {
		return false;
	}

	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_page_purchase_receipt( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_page_purchase_receipt( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, $page_type );
}

/**
 * Render products from page product.
 *
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_page_product() {
	// Set the page type.
	$page_type = 'product';

	// Get the page settings.
	$settings = psupsellmaster_get_settings_by_location( $page_type );

	// Check if the page is not enable.
	if ( empty( $settings['enable'] ) ) {
		return false;
	}

	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_page_product( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_page_product( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, $page_type );
}

/**
 * Render products from page checkout.
 *
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_page_checkout() {
	// Set the page type.
	$page_type = 'checkout';

	// Get the page settings.
	$settings = psupsellmaster_get_settings_by_location( $page_type );

	// Check if the page is not enable.
	if ( empty( $settings['enable'] ) ) {
		return false;
	}

	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_page_checkout( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_page_checkout( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, $page_type );
}

/**
 * Render products from popup add to cart.
 *
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_popup_add_to_cart() {
	// Set the location.
	$location = 'popup_add_to_cart';

	// Get the popup settings.
	$settings = psupsellmaster_get_settings_by_location( $location );

	// Check if the popup is not enable.
	if ( empty( $settings['enable'] ) ) {
		return false;
	}

	// Check if the excluded pages is not empty.
	if ( ! empty( $settings['excluded_pages'] ) ) {

		// Check if the current page is excluded.
		if ( in_array( get_queried_object_id(), $settings['excluded_pages'], true ) ) {
			return false;
		}
	}

	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_popup_add_to_cart( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_popup_add_to_cart( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, $location );
}

/**
 * Render products from popup exit intent.
 *
 * @return string The HTML.
 */
function psupsellmaster_render_products_from_popup_exit_intent() {
	// Set the location.
	$location = 'popup_exit_intent';

	// Get the popup settings.
	$settings = psupsellmaster_get_settings_by_location( $location );

	// Check if the popup is not enable.
	if ( empty( $settings['enable'] ) ) {
		return false;
	}

	// Check if the excluded pages is not empty.
	if ( ! empty( $settings['excluded_pages'] ) ) {

		// Check if the current page is excluded.
		if ( in_array( get_queried_object_id(), $settings['excluded_pages'], true ) ) {
			return false;
		}
	}

	// Set the products list.
	$products_list = array();

	// Check the show type setting.
	if ( 'upsells' === $settings['show_type'] ) {
		$products_list = psupsellmaster_get_upsells_from_popup_exit_intent( $settings );
	} else {
		$products_list = psupsellmaster_get_visits_from_popup_exit_intent( $settings );
	}

	// Check if the products list is empty.
	if ( empty( $products_list ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_items_list( $products_list, $location );
}

/**
 * Render products by page location.
 *
 * @param string $location The location.
 */
function psupsellmaster_render_products_by_page_location( $location ) {
	// Check the page type.
	if ( 'product' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_page_product();
	} elseif ( 'purchase_receipt' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_page_purchase_receipt();
	} elseif ( 'checkout' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_page_checkout();
	}
}

/**
 * Render products by popup location.
 *
 * @param string $location The location.
 */
function psupsellmaster_render_products_by_popup_location( $location ) {
	// Check the popup type.
	if ( 'popup_add_to_cart' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_popup_add_to_cart();
	} elseif ( 'popup_exit_intent' === $location ) {
		// Render the products.
		psupsellmaster_render_products_from_popup_exit_intent();
	}
}

/**
 * Render products by location.
 *
 * @param string $location The location.
 * @param array  $args The arguments.
 */
function psupsellmaster_render_products_by_location( $location, $args = array() ) {
	// Set the pages.
	$pages = array(
		'checkout',
		'product',
		'purchase_receipt',
	);

	// Set the popups.
	$popups = array(
		'popup_add_to_cart',
		'popup_exit_intent',
	);

	// Set other features.
	$features = array(
		'block',
		'shortcode',
		'widget',
		'elementor_widget',
	);

	// Check if the location is related to pages.
	if ( in_array( $location, $pages, true ) ) {
		// Render the products.
		psupsellmaster_render_products_by_page_location( $location );

		// Check if the popup type is related to other features.
	} elseif ( in_array( $location, $popups, true ) ) {
		// Render the products.
		psupsellmaster_render_products_by_popup_location( $location );

		// Check if the location is related to other features.
	} elseif ( in_array( $location, $features, true ) ) {
		// Render the products.
		psupsellmaster_render_products_by_feature_location( $location, $args );
	}
}

/**
 * Filter the class name of the block.
 *
 * @param string $classname The classname.
 * @param string $block_name The block name.
 * @return string The classname.
 */
function psupsellmaster_wp_filter_widget_block_dynamic_classname( $classname, $block_name ) {

	// Check if the block is from this plugin.
	if ( 'psupsellmaster/upsellmaster-gutenberg-block' === $block_name ) {
		$classname = 'widget_block psupsellmaster-widget';
	}

	// Return the classname.
	return $classname;
}
add_filter( 'widget_block_dynamic_classname', 'psupsellmaster_wp_filter_widget_block_dynamic_classname', 10, 2 );

/**
 * Gets the product sources.
 *
 * @return array The product sources.
 */
function psupsellmaster_get_product_sources() {
	// Set the sources.
	$sources = array(
		'upsells'   => __( 'Upsells', 'psupsellmaster' ),
		'visits'    => __( 'Recently Viewed Products', 'psupsellmaster' ),
		'campaigns' => __( 'Campaigns', 'psupsellmaster' ),
	);

	// Return the sources.
	return $sources;
}

/**
 * Gets the product source label.
 *
 * @param string $key The product source key.
 * @return string The product source label.
 */
function psupsellmaster_get_product_source_label( $key ) {
	// Get the types.
	$types = psupsellmaster_get_product_sources();

	// Get the label.
	$label = ! empty( $types[ $key ] ) ? $types[ $key ] : $key;

	// Return the label.
	return $label;
}

/**
 * Gets the product locations.
 *
 * @return array The product locations.
 */
function psupsellmaster_get_product_locations() {
	// Set the locations.
	$locations = array(
		'checkout'          => __( 'Checkout Page', 'psupsellmaster' ),
		'product'           => __( 'Product Page', 'psupsellmaster' ),
		'purchase_receipt'  => __( 'Purchase Receipt Page', 'psupsellmaster' ),
		'widget'            => __( 'Widget', 'psupsellmaster' ),
		'shortcode'         => __( 'Shortcode', 'psupsellmaster' ),
		'block'             => __( 'Gutenberg Block', 'psupsellmaster' ),
		'elementor_widget'  => __( 'Elementor Widget', 'psupsellmaster' ),
		'popup_add_to_cart' => __( 'Add to Cart Popup', 'psupsellmaster' ),
		'popup_exit_intent' => __( 'Exit Intent Popup', 'psupsellmaster' ),
	);

	// Return the locations.
	return $locations;
}

/**
 * Gets the product location label.
 *
 * @param string $key The key.
 * @return string The label.
 */
function psupsellmaster_get_product_location_label( $key ) {
	// Get the locations.
	$locations = psupsellmaster_get_product_locations();

	// Get the label.
	$label = ! empty( $locations[ $key ] ) ? $locations[ $key ] : __( 'Unknown', 'psupsellmaster' );

	// Return the label.
	return $label;
}

/**
 * Gets the purchase types.
 *
 * @return array The types.
 */
function psupsellmaster_get_purchase_types() {
	// Set the types.
	$types = array(
		'direct'   => __( 'Direct', 'psupsellmaster' ),
		'indirect' => __( 'Indirect', 'psupsellmaster' ),
	);

	// Return the types.
	return $types;
}

/**
 * Gets the purchase type label.
 *
 * @param string $key The key.
 * @return string The label.
 */
function psupsellmaster_get_purchase_type_label( $key ) {
	// Get the types.
	$types = psupsellmaster_get_purchase_types();

	// Get the label.
	$label = ! empty( $types[ $key ] ) ? $types[ $key ] : __( 'Unknown', 'psupsellmaster' );

	// Return the label.
	return $label;
}

/**
 * Gets the product views.
 *
 * @return array The views.
 */
function psupsellmaster_get_product_views() {
	// Set the views.
	$views = array(
		'carousel' => __( 'Carousel', 'psupsellmaster' ),
		'list'     => __( 'List', 'psupsellmaster' ),
	);

	// Return the views.
	return $views;
}

/**
 * Gets the product view label.
 *
 * @param string $key The key.
 * @return string The label.
 */
function psupsellmaster_get_product_view_label( $key ) {
	// Get the views.
	$views = psupsellmaster_get_product_views();

	// Get the label.
	$label = ! empty( $views[ $key ] ) ? $views[ $key ] : __( 'Unknown', 'psupsellmaster' );

	// Return the label.
	return $label;
}

/**
 * Gets the customers.
 *
 * @return array The customers.
 */
function psupsellmaster_get_customers() {
	// Set the customers.
	$customers = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the query.
		$query = PsUpsellMaster_Database::prepare(
			"SELECT `id` as ID,  CONCAT('\"',`display_name`,'\" <',`user_email`,'>') AS `name` FROM %i ORDER BY 2",
			PsUpsellMaster_Database::get_table_name( 'users' )
		);

		// Get the customers.
		$customers = PsUpsellMaster_Database::get_results( $query );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the query.
		$query = PsUpsellMaster_Database::prepare(
			"SELECT `id` as ID,  CONCAT('\"',`name`,'\" <',`email`,'>') AS `name` FROM %i ORDER BY 2",
			PsUpsellMaster_Database::get_table_name( 'edd_customers' )
		);

		// Get the customers.
		$customers = PsUpsellMaster_Database::get_results( $query );
	}

	// Return the customers.
	return $customers;
}

/**
 * Checks if the add to cart should go to checkout.
 *
 * @return bool True if the add to cart should go to checkout, false otherwise.
 */
function psupsellmaster_add_to_cart_should_go_to_checkout() {
	// Set the should.
	$should = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the should.
		$should = 'yes' === get_option( 'woocommerce_cart_redirect_after_add' );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the should.
		$should = edd_straight_to_checkout();
	}

	// Return the should.
	return $should;
}

/**
 * Checks if the add to cart has AJAX.
 *
 * @return bool True if the add to cart has AJAX, false otherwise.
 */
function psupsellmaster_add_to_cart_has_ajax() {
	// Set the has.
	$has = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the has.
		$has = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the has.
		$has = true;
	}

	// Return the has.
	return $has;
}

/**
 * Gets the pages.
 *
 * @return array The pages.
 */
function psupsellmaster_get_frontend_pages() {
	// Set the pages.
	$pages = array(
		'product'  => 'psupsellmaster_is_page_product',
		'checkout' => 'psupsellmaster_is_page_checkout',
		'cart'     => 'psupsellmaster_is_page_cart',
		'receipt'  => 'psupsellmaster_is_page_purchase_receipt',
		'history'  => 'psupsellmaster_is_page_purchase_history',
	);

	// Return the pages.
	return $pages;
}

/**
 * Gets the current page.
 *
 * @return string|bool The current page or false if the current page is not found.
 */
function psupsellmaster_get_current_page() {
	// Set the current.
	$current = false;

	// Get the pages.
	$pages = psupsellmaster_get_frontend_pages();

	// Loop through the pages.
	foreach ( $pages as $key => $callback ) {

		// Check if the callback is callable.
		if ( is_callable( $callback ) ) {
			// Get the is page.
			$is_page = call_user_func( $callback );

			// Check if the is page is true.
			if ( $is_page ) {
				// Set the current.
				$current = $key;

				// Stop the loop.
				break;
			}
		}
	}

	// Return the current.
	return $current;
}

/**
 * Gets the cart uri.
 *
 * @return string The cart uri.
 */
function psupsellmaster_get_cart_uri() {
	// Set the cart uri.
	$cart_uri = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the cart uri.
		$cart_uri = wc_get_cart_url();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the cart uri (there is no cart uri for this plugin).
		$cart_uri = edd_get_checkout_uri();
	}

	// Return the cart uri.
	return $cart_uri;
}

/**
 * Gets the checkout uri.
 *
 * @return string The checkout uri.
 */
function psupsellmaster_get_checkout_uri() {
	// Set the checkout uri.
	$checkout_uri = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the checkout uri.
		$checkout_uri = wc_get_checkout_url();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the checkout uri.
		$checkout_uri = edd_get_checkout_uri();
	}

	// Return the checkout uri.
	return $checkout_uri;
}

/**
 * Adds a visit record into the database.
 */
function psupsellmaster_wp_add_visit() {
	// Get the product id.
	$product_id = get_the_ID();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return false;
	}

	// Get the post type.
	$post_type = get_post_type( $product_id );

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type is not the product post type.
	if ( $post_type !== $product_post_type ) {
		return false;
	}

	// Handle and fix/merge visits from concurrent requests.
	psupsellmaster_merge_visits();

	// Get the visits.
	$visits = psupsellmaster_get_visits_from_current_visitor();

	// Add the product id to the visits list.
	array_push( $visits, $product_id );

	// Remove duplicates - keep only the last occurrence.
	$visits = psupsellmaster_remove_array_duplicates( $visits );

	// Get the user id.
	$user_id = get_current_user_id();
	$user_id = ! empty( $user_id ) ? $user_id : 0;

	// Get the ip address.
	$ip_address = psupsellmaster_get_ip_address();
	$ip_address = ! empty( $ip_address ) ? $ip_address : '';

	// Set the update data.
	$update_data = array( 'visits' => wp_json_encode( $visits ) );

	// Check if the user id is not empty.
	if ( ! empty( $user_id ) ) {
		$update_data['user_id'] = $user_id;
	}

	// Check if the ip address is not empty.
	if ( ! empty( $ip_address ) ) {
		$update_data['ip'] = $ip_address;
	}

	// Set the update where.
	$update_where = array( 'id' => psupsellmaster_get_current_visitor_id() );

	// Update an existing visitor in the database.
	psupsellmaster_db_visitors_update( $update_data, $update_where );
}
add_action( 'wp', 'psupsellmaster_wp_add_visit', 20 );

/**
 * Gets a server value by key.
 *
 * @param string $key The server key.
 * @return string The server value.
 */
function psupsellmaster_get_server( $key ) {
	// Get the server value.
	$value = isset( $_SERVER[ $key ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) : false;

	// Return the server value.
	return $value;
}

/**
 * Gets the ip address.
 *
 * @return string The ip address.
 */
function psupsellmaster_get_ip_address() {
	// Get the ip address from REMOTE_ADDR.
	$remote_addr = psupsellmaster_get_server( 'REMOTE_ADDR' );

	// Get the ip address from HTTP_CLIENT_IP.
	$http_client_ip = psupsellmaster_get_server( 'HTTP_CLIENT_IP' );

	// Get the ip address from HTTP_X_FORWARDED_FOR.
	$http_x_forwarded_for = psupsellmaster_get_server( 'HTTP_X_FORWARDED_FOR' );

	// Get the ip address from HTTP_X_FORWARDED.
	$http_x_forwarded = psupsellmaster_get_server( 'HTTP_X_FORWARDED' );

	// Get the ip address from HTTP_FORWARDED_FOR.
	$http_forwarded_for = psupsellmaster_get_server( 'HTTP_FORWARDED_FOR' );

	// Get the ip address from HTTP_FORWARDED.
	$http_forwarded = psupsellmaster_get_server( 'HTTP_FORWARDED' );

	// Get the reliable ip address.
	$ip_address = $remote_addr;

	// Check if the HTTP_CLIENT_IP is not empty.
	if ( ! empty( $http_client_ip ) ) {
		// Get the ip address.
		$ip_address = $http_client_ip;

		// Check if the HTTP_X_FORWARDED_FOR is not empty.
	} elseif ( ! empty( $http_x_forwarded_for ) ) {
		// Get the ip address.
		$ip_address = $http_x_forwarded_for;

		// Check if the HTTP_X_FORWARDED is not empty.
	} elseif ( ! empty( $http_x_forwarded ) ) {
		// Get the ip address.
		$ip_address = $http_x_forwarded;

		// Check if the HTTP_FORWARDED_FOR is not empty.
	} elseif ( ! empty( $http_forwarded_for ) ) {
		// Get the ip address.
		$ip_address = $http_forwarded_for;

		// Check if the HTTP_FORWARDED is not empty.
	} elseif ( ! empty( $http_forwarded ) ) {
		// Get the ip address.
		$ip_address = $http_forwarded;
	}

	// Check if the ip address is empty.
	if ( empty( $ip_address ) ) {
		// Set the ip address to an empty string.
		$ip_address = '';

		// Otherwise...
	} else {
		// Sometimes the ip address might have duplicate entries.
		// so lets split the string into an array and remove them.

		// Explode the ip address (from string to an array).
		$ip_address = explode( ',', $ip_address );

		// Remove empty and duplicate entries.
		$ip_address = array_filter( array_unique( $ip_address ) );

		// Implode the ip address (from an array to a string.
		$ip_address = implode( ',', $ip_address );
	}

	// Return the ip address.
	return $ip_address;
}

/**
 * Returns the pages.
 *
 * @return WP_Post[]|false The pages or false on failure.
 */
function psupsellmaster_wp_get_pages() {
	// Get the pages.
	$pages = get_pages();

	// Return the pages.
	return $pages;
}

/**
 * Return the Greatest Common Divisor (GCD) of two numbers.
 *
 * @param float $x The x number.
 * @param float $y The y number.
 * @return float The GCD.
 */
function psupsellmaster_get_gcd( $x, $y ) {
	// Check if the y is in the list.
	if ( in_array( $y, array( 0, 1 ), true ) ) {
		// Return the x.
		return $x;
	}

	// Return the function call.
	return psupsellmaster_get_gcd( $y, $x % $y );
}

/**
 * Get the timezone.
 *
 * @return DateTimeZone The timezone.
 */
function psupsellmaster_get_timezone() {
	$timezone_string = get_option( 'timezone_string' );

	if ( ! empty( $timezone_string ) ) {
		return new DateTimeZone( $timezone_string );
	}

	$offset  = get_option( 'gmt_offset' );
	$sign    = $offset < 0 ? '-' : '+';
	$hours   = (int) $offset;
	$minutes = abs( ( $offset - (int) $offset ) * 60 );
	$offset  = sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes );

	return new DateTimeZone( $offset );
}

/**
 * Gets the currency symbol.
 *
 * @param string $currency The currency.
 * @return string The currency symbol.
 */
function psupsellmaster_get_currency_symbol( $currency = '' ) {
	// Set the symbol.
	$symbol = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the symbol.
		$symbol = get_woocommerce_currency_symbol( $currency );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the symbol.
		$symbol = edd_currency_filter( '', $currency );
	}

	// Return the symbol.
	return $symbol;
}

/**
 * Gets the excluded taxonomies from the settings.
 *
 * @return array the excluded taxonomies.
 */
function psupsellmaster_get_excluded_taxonomies_from_settings() {
	// Set the excluded taxonomies.
	$excluded_taxonomies = array();

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Check if the setting is not empty.
	if ( ! empty( $settings['excluded_taxonomies'] ) ) {
		// Set the excluded taxonomies.
		$excluded_taxonomies = is_array( $settings['excluded_taxonomies'] ) ? $settings['excluded_taxonomies'] : array();
	}

	// Check if there are excluded categories.
	if ( ! empty( $settings['excluded_categories'] ) ) {
		// Get the category taxonomy.
		$category_taxonomy = psupsellmaster_get_product_category_taxonomy();

		// Set the excluded terms.
		$excluded_taxonomies[ $category_taxonomy ] = $settings['excluded_categories'];
	}

	// Check if there are excluded tags.
	if ( ! empty( $settings['excluded_tags'] ) ) {
		// Get the tag taxonomy.
		$tag_taxonomy = psupsellmaster_get_product_tag_taxonomy();

		// Set the excluded terms.
		$excluded_taxonomies[ $tag_taxonomy ] = $settings['excluded_tags'];
	}

	// Return the excluded taxonomies.
	return $excluded_taxonomies;
}

/**
 * Gets the excluded taxonomies from the product received through the arguments.
 *
 * @param int $product_id the product id.
 * @return array the excluded taxonomies.
 */
function psupsellmaster_get_excluded_taxonomies_from_product( $product_id ) {
	// Set the excluded taxonomies.
	$excluded_taxonomies = array();

	// Set the meta keys.
	$meta_keys = array();

	// Set the base meta key.
	$base_meta_key = 'psupsellmaster_excluded_tax_';

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Loop through the product taxonomies.
	foreach ( $product_taxonomies as $product_taxonomy ) {
		// Set the meta key.
		$meta_key = "{$base_meta_key}{$product_taxonomy}";

		// Add the meta key to the meta keys.
		array_push( $meta_keys, $meta_key );
	}

	// Check if the meta keys is empty.
	if ( empty( $meta_keys ) ) {
		// Return the excluded taxonomies.
		return $excluded_taxonomies;
	}

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );

	// Set the sql meta keys.
	$sql_meta_keys = PsUpsellMaster_Database::prepare( "`postmeta`.`meta_key` IN ( {$placeholders} )", $meta_keys );

	// Set the sql products.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			REPLACE( `postmeta`.`meta_key`, %s, '' ) AS `taxonomy`,
			GROUP_CONCAT( `postmeta`.`meta_value` SEPARATOR ',' ) AS `terms`
		FROM
			%i AS `postmeta`
		WHERE
			1 = 1
		AND
			`postmeta`.`post_id` = %d
		AND
			{$sql_meta_keys}
		GROUP BY
			`postmeta`.`meta_key`
		",
		$base_meta_key,
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$product_id
	);

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the results.
	foreach ( $results as $result ) {
		// Set the taxonomy.
		$taxonomy = ! empty( $result->taxonomy ) ? $result->taxonomy : false;

		// Check if the taxonomy is empty.
		if ( empty( $taxonomy ) ) {
			// Continue the loop.
			continue;
		}

		// Set the terms.
		$terms = ! empty( $result->terms ) ? explode( ',', $result->terms ) : array();

		// Check if the terms is empty.
		if ( empty( $terms ) ) {
			// Continue the loop.
			continue;
		}

		// Set the excluded taxonomies.
		$excluded_taxonomies[ $taxonomy ] = $terms;
	}

	// Return the excluded taxonomies.
	return $excluded_taxonomies;
}

/**
 * Deletes old records as per the settings.
 *
 * @return bool|int False on failure, number of rows deleted on success.
 */
function psupsellmaster_maybe_delete_old_results() {
	// Set the intervals.
	$intervals = array(
		'1 MONTH' => '1-month',
		'2 MONTH' => '2-months',
		'3 MONTH' => '3-months',
		'6 MONTH' => '6-months',
		'1 YEAR'  => '1-year',
		'2 YEAR'  => '2-years',
		'3 YEAR'  => '3-years',
	);

	// Get the interval.
	$interval = PsUpsellMaster_Settings::get( 'cleandata_interval' );

	// Check if the interval is not valid.
	if ( ! in_array( $interval, $intervals, true ) ) {
		return false;
	}

	// Set the sql interval.
	$sql_interval = array_search( $interval, $intervals, true );

	// Set the deleted.
	$deleted = false;

	// Build the SQL to delete old results.
	$sql_delete  = 'DELETE';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';
	$sql_where[] = "AND updated_at < DATE_SUB( NOW(), INTERVAL {$sql_interval} )";
	$sql_where   = implode( ' ', $sql_where );
	$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";
	$deleted     = PsUpsellMaster_Database::query( $sql_query );

	// Check if the deleted is not empty.
	if ( ! empty( $deleted ) ) {
		// Set the meta database table.
		$meta_table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_resultmeta' );

		// Build the SQL to delete old resultmeta.
		$sql_delete  = 'DELETE';
		$sql_from    = "FROM {$meta_table_name}";
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = 'AND NOT EXISTS ( SELECT 1 FROM %i AS `r` WHERE `psupsellmaster_result_id` = `r`.`id` )';
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";

		// Delete the meta records.
		PsUpsellMaster_Database::query( $sql_query );
	}

	// Return the deleted.
	return $deleted;
}

/**
 * Get the product price range.
 *
 * @param int $product_id the product id.
 * @return array the price range.
 */
function psupsellmaster_get_price_range( $product_id ) {
	// Set the range.
	$range = array(
		'min' => 0,
		'max' => 0,
	);

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the range.
		$range = psupsellmaster_woo_get_price_range( $product_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the range.
		$range = psupsellmaster_edd_get_price_range( $product_id );
	}

	// Return the range.
	return $range;
}

/**
 * Get the base plugin name.
 *
 * @return string the base plugin name.
 */
function psupsellmaster_get_base_plugin_name() {
	// Set the name.
	$name = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the name.
		$name = __( 'WooCommerce', 'psupsellmaster' );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the name.
		$name = __( 'Easy Digital Downloads', 'psupsellmaster' );
	}

	// Return the name.
	return $name;
}

/**
 * Get the weekdays.
 *
 * @return array the weekdays.
 */
function psupsellmaster_get_weekdays() {
	// Set the weekdays.
	$weekdays = array(
		'monday'    => __( 'Monday', 'psupsellmaster' ),
		'tuesday'   => __( 'Tuesday', 'psupsellmaster' ),
		'wednesday' => __( 'Wednesday', 'psupsellmaster' ),
		'thursday'  => __( 'Thursday', 'psupsellmaster' ),
		'friday'    => __( 'Friday', 'psupsellmaster' ),
		'saturday'  => __( 'Saturday', 'psupsellmaster' ),
		'sunday'    => __( 'Sunday', 'psupsellmaster' ),
	);

	// Allow developers to filter this.
	$weekdays = apply_filters( 'psupsellmaster_weekdays', $weekdays );

	// Return the weekdays.
	return $weekdays;
}

/**
 * Round an amount.
 *
 * @param int $amount The amount.
 * @param int $precision The precision.
 * @return int The rounded amount.
 */
function psupsellmaster_round_amount( $amount, $precision = 2 ) {
	// Set the rounded.
	$rounded = filter_var( $amount, FILTER_VALIDATE_FLOAT );
	$rounded = false !== $rounded ? round( $rounded, $precision ) : 0;

	// Return the rounded.
	return $rounded;
}

/**
 * Safe divide two numbers.
 *
 * @param int $dividend The dividend.
 * @param int $divisor The divisor.
 * @return int The quotient.
 */
function psupsellmaster_safe_divide( $dividend, $divisor ) {
	// Set the quotient.
	$quotient = 0;

	// Check if the divisor is empty.
	if ( empty( $divisor ) ) {
		// Return the quotient.
		return $quotient;
	}

	// Set the quotient.
	$quotient = $dividend / $divisor;

	// Return the quotient.
	return $quotient;
}

/**
 * Format an integer amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_format_integer_amount( $amount ) {
	// Round the amount.
	$amount = psupsellmaster_round_amount( $amount, 0 );

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the amount.
		$amount = psupsellmaster_woo_format_integer_amount( $amount );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the amount.
		$amount = psupsellmaster_edd_format_integer_amount( $amount );
	}

	// Return the amount.
	return $amount;
}

/**
 * Format a decimal amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_format_decimal_amount( $amount ) {
	// Round the amount.
	$amount = psupsellmaster_round_amount( $amount );

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the amount.
		$amount = psupsellmaster_woo_format_decimal_amount( $amount );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the amount.
		$amount = psupsellmaster_edd_format_decimal_amount( $amount );
	}

	// Return the amount.
	return $amount;
}

/**
 * Format a currency amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_format_currency_amount( $amount ) {
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the amount.
		$amount = psupsellmaster_woo_format_currency_amount( $amount );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the amount.
		$amount = psupsellmaster_edd_format_currency_amount( $amount );
	}

	// Return the amount.
	return $amount;
}

/**
 * Format a percentage amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_format_percentage_amount( $amount ) {
	// Return the amount.
	return psupsellmaster_format_decimal_amount( $amount ) . '%';
}

/**
 * Get the WordPress formatted timezone.
 *
 * @return string The formatted timezone.
 */
function psupsellmaster_get_formatted_wp_timezone() {
	// Get the timezone.
	$timezone = wp_timezone();

	// Get the offset in seconds.
	$offset_seconds = $timezone->getOffset( new DateTime() );

	// Set the offset hours.
	$offset_hours = abs( intval( $offset_seconds / 3600 ) );

	// Set the offset minutes.
	$offset_minutes = abs( intval( ( $offset_seconds % 3600 ) / 60 ) );

	// Set the offset sign.
	$offset_sign = ( $offset_seconds >= 0 ) ? '+' : '-';

	// Format the timezone in the "UTC±[offset]" format.
	$formatted_timezone = 'UTC' . $offset_sign . sprintf( '%02d:%02d', $offset_hours, $offset_minutes );

	// Return the formatted timezone.
	return $formatted_timezone;
}

/**
 * Insert a product taxonomy term.
 *
 * @param array $args the arguments.
 * @return int|false the term id or false on failure.
 */
function psupsellmaster_insert_product_taxonomy_term( $args ) {
	// Set the term id.
	$term_id = false;

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Get the taxonomy.
	$taxonomy = isset( $args['taxonomy'] ) ? $args['taxonomy'] : false;

	// Check if the taxonomy is not valid.
	if ( ! in_array( $taxonomy, $product_taxonomies, true ) ) {
		// Return the term id.
		return $term_id;
	}

	// Set the save args.
	$save_args = array();

	// Check if the term name is set.
	if ( isset( $args['term_name'] ) ) {
		// Set the term name.
		$save_args['name'] = $args['term_name'];
	}

	// Check if the term slug is set.
	if ( isset( $args['term_slug'] ) ) {
		// Set the term slug.
		$save_args['slug'] = $args['term_slug'];
	}

	// Check if the term description is set.
	if ( isset( $args['term_description'] ) ) {
		// Set the term description.
		$save_args['description'] = $args['term_description'];
	}

	// Check if the term parent is set.
	if ( isset( $args['term_parent'] ) ) {
		// Set the term parent.
		$save_args['parent'] = $args['term_parent'];
	}

	// Get the update term id.
	$update_id = isset( $args['term_id'] ) ? filter_var( $args['term_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the update id is empty.
	if ( empty( $update_id ) ) {
		// Check if the term name is empty.
		if ( empty( $save_args['name'] ) ) {
			// Return the term id.
			return $term_id;
		}

		// Insert the term.
		$term_id = wp_insert_term( $save_args['name'], $taxonomy, $save_args );

		// Otherwise...
	} else {
		// Update the term.
		$term_id = wp_update_term( $update_id, $taxonomy, $save_args );
	}

	// Set the term id.
	$term_id = isset( $term_id['term_id'] ) ? $term_id['term_id'] : false;

	// Return the term id.
	return $term_id;
}

/**
 * Get the uploaded attachments.
 *
 * @return array the uploaded attachments.
 */
function psupsellmaster_get_uploaded_attachments() {
	// Get the attachments.
	$attachments = get_option( 'psupsellmaster_uploaded_attachments', array() );

	// Return the attachments.
	return $attachments;
}

/**
 * Update the uploaded attachments.
 *
 * @param array $attachments the attachments.
 */
function psupsellmaster_update_uploaded_attachments( $attachments ) {
	// Update the attachments.
	update_option( 'psupsellmaster_uploaded_attachments', $attachments );
}

/**
 * Get the event date.
 *
 * @param array $args the arguments.
 * @return string the event date.
 */
function psupsellmaster_get_event_date( $args ) {
	// Set the date.
	$date = array();

	// Get the event.
	$event = isset( $args['event'] ) ? $args['event'] : false;

	// Check if the event is empty.
	if ( empty( $event ) ) {
		// Return the date.
		return $date;
	}

	// Get the year.
	$year = isset( $args['year'] ) ? filter_var( $args['year'], FILTER_VALIDATE_INT ) : false;

	// Check if the year is empty.
	if ( empty( $year ) ) {
		// Set the year.
		$year = new DateTime( 'now', psupsellmaster_get_timezone() );
		$year = $year->format( 'Y' );
	}

	// Check the event.
	if ( 'black_friday' === $event ) {
		// Set the thanksgiving.
		$thanksgiving = strtotime( "fourth thursday of november {$year}" );

		// Set the black friday.
		$black_friday = strtotime( '+1 day', $thanksgiving );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $black_friday ),
			'day'   => gmdate( 'j', $black_friday ),
		);

		// Check the event.
	} elseif ( 'childrens_day' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 11,
			'day'   => 20,
		);

		// Check the event.
	} elseif ( 'christmas_day' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 12,
			'day'   => 25,
		);

		// Check the event.
	} elseif ( 'christmas_eve' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 12,
			'day'   => 24,
		);

		// Check the event.
	} elseif ( 'christmas_special' === $event ) {
		// Set the date.
		$date = array(
			'start' => array(
				'year'  => $year,
				'month' => 12,
				'day'   => 17,
			),
			'end'   => array(
				'year'  => $year,
				'month' => 12,
				'day'   => 31,
			),
		);

		// Check the event.
	} elseif ( 'cyber_monday' === $event ) {
		// Set the thanksgiving.
		$thanksgiving = strtotime( "fourth thursday of november {$year}" );

		// Set the cyber monday.
		$cyber_monday = strtotime( '+4 days', $thanksgiving );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $cyber_monday ),
			'day'   => gmdate( 'j', $cyber_monday ),
		);

		// Check the event.
	} elseif ( 'cyber_week' === $event ) {
		// Set the thanksgiving.
		$thanksgiving = strtotime( "fourth thursday of november {$year}" );

		// Set the cyber monday.
		$cyber_monday = strtotime( '+4 days', $thanksgiving );

		// Set the end date.
		$end_date = strtotime( '+6 days', $cyber_monday );

		// Set the date.
		$date = array(
			'start' => array(
				'year'  => $year,
				'month' => gmdate( 'n', $cyber_monday ),
				'day'   => gmdate( 'j', $cyber_monday ),
			),
			'end'   => array(
				'year'  => $year,
				'month' => gmdate( 'n', $end_date ),
				'day'   => gmdate( 'j', $end_date ),
			),
		);

		// Check the event.
	} elseif ( 'easter' === $event ) {
		// Set the easter dates.
		$easter_dates = array(
			2023 => array(
				'day'   => 9,
				'month' => 4,
			),
			2024 => array(
				'day'   => 31,
				'month' => 3,
			),
			2025 => array(
				'day'   => 20,
				'month' => 4,
			),
			2026 => array(
				'day'   => 5,
				'month' => 4,
			),
			2027 => array(
				'day'   => 28,
				'month' => 3,
			),
			2028 => array(
				'day'   => 16,
				'month' => 4,
			),
			2029 => array(
				'day'   => 1,
				'month' => 4,
			),
			2030 => array(
				'day'   => 21,
				'month' => 4,
			),
			2031 => array(
				'day'   => 13,
				'month' => 4,
			),
			2032 => array(
				'day'   => 28,
				'month' => 3,
			),
			2033 => array(
				'day'   => 17,
				'month' => 4,
			),
			2034 => array(
				'day'   => 9,
				'month' => 4,
			),
			2035 => array(
				'day'   => 25,
				'month' => 3,
			),
			2036 => array(
				'day'   => 13,
				'month' => 4,
			),
			2037 => array(
				'day'   => 5,
				'month' => 4,
			),
			2038 => array(
				'day'   => 25,
				'month' => 4,
			),
			2039 => array(
				'day'   => 10,
				'month' => 4,
			),
			2040 => array(
				'day'   => 1,
				'month' => 4,
			),
			2041 => array(
				'day'   => 21,
				'month' => 4,
			),
			2042 => array(
				'day'   => 6,
				'month' => 4,
			),
			2043 => array(
				'day'   => 29,
				'month' => 3,
			),
			2044 => array(
				'day'   => 17,
				'month' => 4,
			),
			2045 => array(
				'day'   => 9,
				'month' => 4,
			),
			2046 => array(
				'day'   => 25,
				'month' => 3,
			),
			2047 => array(
				'day'   => 14,
				'month' => 4,
			),
			2048 => array(
				'day'   => 5,
				'month' => 4,
			),
			2049 => array(
				'day'   => 18,
				'month' => 4,
			),
			2050 => array(
				'day'   => 10,
				'month' => 4,
			),
		);

		// Check if the year is in the easter dates.
		if ( isset( $easter_dates[ $year ] ) ) {
			// Set the date.
			$date = array(
				'year'  => $year,
				'month' => $easter_dates[ $year ]['month'],
				'day'   => $easter_dates[ $year ]['day'],
			);
		}

		// Check the event.
	} elseif ( 'fathers_day' === $event ) {
		// Set the fathers day.
		$fathers_day = strtotime( "third sunday of june {$year}" );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $fathers_day ),
			'day'   => gmdate( 'j', $fathers_day ),
		);

		// Check the event.
	} elseif ( 'halloween' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 10,
			'day'   => 31,
		);

		// Check the event.
	} elseif ( 'labor_day' === $event ) {
		// Set the labor day.
		$labor_day = strtotime( "first monday of september {$year}" );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $labor_day ),
			'day'   => gmdate( 'j', $labor_day ),
		);

		// Check the event.
	} elseif ( 'mothers_day' === $event ) {
		// Set the mothers day.
		$mothers_day = strtotime( "second sunday of may {$year}" );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $mothers_day ),
			'day'   => gmdate( 'j', $mothers_day ),
		);

		// Check the event.
	} elseif ( 'new_years_day' === $event ) {
		// Set the next year.
		$next_year = $year + 1;

		// Set the date.
		$date = array(
			'year'  => $next_year,
			'month' => 1,
			'day'   => 1,
		);

		// Check the event.
	} elseif ( 'new_years_eve' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 12,
			'day'   => 31,
		);

		// Check the event.
	} elseif ( 'new_years_special' === $event ) {
		// Set the next year.
		$next_year = $year + 1;

		// Set the date.
		$date = array(
			'start' => array(
				'year'  => $year,
				'month' => 12,
				'day'   => 24,
			),
			'end'   => array(
				'year'  => $next_year,
				'month' => 1,
				'day'   => 7,
			),
		);

		// Check the event.
	} elseif ( 'thanksgiving' === $event ) {
		// Set the thanksgiving.
		$thanksgiving = strtotime( "fourth thursday of november {$year}" );

		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => gmdate( 'n', $thanksgiving ),
			'day'   => gmdate( 'j', $thanksgiving ),
		);

		// Check the event.
	} elseif ( 'valentines_day' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 2,
			'day'   => 14,
		);

		// Check the event.
	} elseif ( 'womens_day' === $event ) {
		// Set the date.
		$date = array(
			'year'  => $year,
			'month' => 3,
			'day'   => 8,
		);
	}

	// Check if the date is not empty.
	if ( ! empty( $date ) ) {
		// Set the check.
		$check = $date;

		// Check if there is a start date.
		if ( isset( $date['start'] ) ) {
			// Set the check.
			$check = $date['start'];
		}

		// Set the full check.
		$full_check = "{$check['year']}-{$check['month']}-{$check['day']}";

		// Get the current datetime.
		$current_datetime = new DateTime( 'now', psupsellmaster_get_timezone() );

		// Check if the date is in the past.
		if ( strtotime( $full_check ) < strtotime( $current_datetime->format( 'Y-m-d' ) ) ) {
			// Set the next year.
			$next_year = $year + 1;

			// Set the args.
			$args = array(
				'event' => $event,
				'year'  => $next_year,
			);

			// Get the event date for the next year.
			$date = psupsellmaster_get_event_date( $args );
		}
	}

	// Return the date.
	return $date;
}

/**
 * Get a feature limit.
 *
 * @param string $key The feature key.
 * @return mixed The feature limit value.
 */
function psupsellmaster_get_feature_limit( $key ) {
	// Set the limit.
	$limit = false;

	// Check the key.
	switch ( $key ) {
		case 'base_products_count':
		case 'results_count':
			// Set the limit.
			$limit = 50;

			// Check if the newsletter is subscribed.
			if ( psupsellmaster_is_newsletter_subscribed() ) {
				// Set the limit.
				$limit = 300;
			}

			// Stop.
			break;

		case 'campaigns_count':
			// Set the limit.
			$limit = 1;

			// Stop.
			break;

		case 'default_upsells':
			// Set the limit.
			$limit = 3;

			// Stop.
			break;

		case 'upsells':
			// Set the limit.
			$limit = 3;

			// Stop.
			break;
	}

	// Allow developers to filter this.
	$limit = apply_filters( 'psupsellmaster_feature_limit', $limit, $key );

	// Return the limit.
	return $limit;
}

/**
 * Check if a feature limit has been reached.
 *
 * @param string $key The feature key.
 * @return bool True if the feature limit has been reached, false otherwise.
 */
function psupsellmaster_has_reached_feature_limit( $key ) {
	// Set the reached.
	$reached = false;

	// Check the key.
	switch ( $key ) {
		case 'base_products_count':
			// Get the count.
			$count = psupsellmaster_scores_get_base_product_count();

			// Get the limit.
			$limit = psupsellmaster_get_feature_limit( $key );

			// Check the limit.
			if ( $count >= $limit ) {
				// Set the reached.
				$reached = true;
			}

			// Stop.
			break;

		case 'campaigns_count':
			// Get the count.
			$count = psupsellmaster_db_campaigns_count();

			// Get the limit.
			$limit = psupsellmaster_get_feature_limit( $key );

			// Check if the limit has been reached.
			if ( $count >= $limit ) {
				// Set the reached.
				$reached = true;
			}

			// Stop.
			break;
	}

	// Allow developers to filter this.
	$reached = apply_filters( 'psupsellmaster_reached_feature_limit', $reached, $key );

	// Return the reached.
	return $reached;
}

/**
 * Check if a feature limit has been reached,
 * build related notices, and return the output.
 *
 * @param string $key The feature key.
 * @return string $output The output.
 */
function psupsellmaster_get_feature_limit_notices( $key ) {
	// Set the output.
	$output = false;

	// Check the key.
	if ( 'campaigns_count' !== $key ) {
		// Return the output.
		return $output;
	}

	// Check if the campaigns limit has been reached.
	if ( psupsellmaster_has_reached_feature_limit( 'campaigns_count' ) ) :
		// Start the output buffer.
		ob_start();

		?>
		<p>
			<?php
			/* translators: 1: message, 2: message, 3: message, 4: PRO version URL, 5: message. */
			printf(
				'<span>%s <strong>%s</strong> %s <a class="psupsellmaster-link" href="%s" target="_blank"><strong>%s</strong></a>.</span>',
				esc_html__( 'The campaigns limit has been reached.', 'psupsellmaster' ),
				esc_html__( 'Unlock unlimited campaigns', 'psupsellmaster' ),
				esc_html__( 'in the', 'psupsellmaster' ),
				esc_url( PSUPSELLMASTER_PRODUCT_URL ),
				esc_html__( 'PRO version', 'psupsellmaster' )
			);
			?>
		</p>
		<?php

		// Set the output.
		$output = ob_get_clean();
	endif;

	// Allow developers to filter this.
	$output = apply_filters( 'psupsellmaster_feature_limit_notices', $output, $key );

	// Return the output.
	return $output;
}

/**
 * Render the campaign condition notices in Easy Digital Downloads.
 */
function psupsellmaster_render_campaign_condition_notices() {
	// Set the notices.
	$notices = array();

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the texts.
	$texts = array(
		'products' => array(
			'count' => array(
				'min' => '',
			),
		),
		'subtotal' => array(
			'min' => '',
		),
	);

	// Check if the setting is set.
	if ( isset( $settings['conditions_products_min_text'] ) ) {
		// Set the texts.
		$texts['products']['count']['min'] = $settings['conditions_products_min_text'];
	}

	// Check if the setting is set.
	if ( isset( $settings['conditions_subtotal_min_text'] ) ) {
		// Set the texts.
		$texts['subtotal']['min'] = $settings['conditions_subtotal_min_text'];
	}

	// Get the campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns();

	// Loop through the campaigns.
	foreach ( $campaigns as $data ) {
		// Get the campaign id.
		$campaign_id = isset( $data['campaign_id'] ) ? filter_var( $data['campaign_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the campaign id is empty.
		if ( empty( $campaign_id ) ) {
			continue;
		}

		// Get the coupons.
		$coupons = isset( $data['coupons'] ) ? $data['coupons'] : array();

		// Get a single coupon.
		$coupon = array_shift( $coupons );

		// Get the coupon code.
		$coupon_code = isset( $coupon['code'] ) ? $coupon['code'] : false;

		// Check if the coupon code is empty.
		if ( empty( $coupon_code ) ) {
			continue;
		}

		// Check if the cart already has the coupon.
		if ( psupsellmaster_cart_has_coupon( $coupon_code ) ) {
			continue;
		}

		// Get the coupon type.
		$coupon_type = isset( $coupon['type'] ) ? $coupon['type'] : false;

		// Check if the coupon type is not valid.
		if ( ! in_array( $coupon_type, array( 'discount_percent', 'discount_fixed' ), true ) ) {
			continue;
		}

		// Get the coupon amount.
		$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

		// Check if the coupon amount is empty.
		if ( empty( $coupon_amount ) ) {
			continue;
		}

		// Set the formatted discount.
		$formatted_discount = 0;

		// Check the coupon type.
		if ( 'discount_percent' === $coupon_type ) {
			// Set the formatted discount.
			$formatted_discount = psupsellmaster_format_decimal_amount( $coupon_amount ) . '%';

			// Check the coupon type.
		} elseif ( 'discount_fixed' === $coupon_type ) {
			// Set the formatted discount.
			$formatted_discount = psupsellmaster_format_currency_amount( $coupon_amount );
		}

		// Get the conditions.
		$conditions = isset( $data['conditions'] ) ? $data['conditions'] : array();

		// Check if the conditions is empty.
		if ( empty( $conditions ) ) {
			continue;
		}

		// Check if the text is not empty.
		if ( ! empty( $texts['products']['count']['min'] ) ) {
			// Check the conditions.
			if ( isset( $conditions['products'] ) ) {
				// Check if the condition is set.
				if ( isset( $conditions['products']['count'] ) ) {
					// Set the products min.
					$products_min = false;

					// Check if the condition is set.
					if ( isset( $conditions['products']['count']['min'] ) ) {
						// Set the products min.
						$products_min = filter_var( $conditions['products']['count']['min'], FILTER_VALIDATE_INT );
					}

					// Check if the products min is valid.
					if ( false !== $products_min ) {
						// Get the cart products.
						$cart_products = psupsellmaster_get_session_cart_product_ids();

						// Get the cart quantity.
						$cart_quantity = count( $cart_products );

						// Check if cart quantity didn't reach the min.
						if ( $products_min > $cart_quantity ) {
							// Set the cart gap.
							$cart_gap = $products_min - $cart_quantity;

							// Set the notice.
							$notice = $texts['products']['count']['min'];

							// Replace the tags.
							$notice = str_replace( '{cart_quantity}', $cart_quantity, $notice );
							$notice = str_replace( '{min_quantity}', $products_min, $notice );
							$notice = str_replace( '{gap_quantity}', $cart_gap, $notice );
							$notice = str_replace( '{discount_amount}', $formatted_discount, $notice );
							$notice = str_replace( '{coupon_code}', $coupon_code, $notice );

							// Add the notice to the list.
							array_push( $notices, $notice );
						}
					}
				}
			}
		}

		// Check if the text is not empty.
		if ( ! empty( $texts['subtotal']['min'] ) ) {
			// Check the conditions.
			if ( isset( $conditions['subtotal'] ) ) {
				// Set the subtotal min.
				$subtotal_min = false;

				// Check if the condition is set.
				if ( isset( $conditions['subtotal']['min'] ) ) {
					// Set the subtotal min.
					$subtotal_min = filter_var( $conditions['subtotal']['min'], FILTER_VALIDATE_FLOAT );
				}

				// Check if the subtotal min is valid.
				if ( false !== $subtotal_min ) {
					// Get the cart subtotal.
					$cart_subtotal = psupsellmaster_get_session_cart_subtotal();

					// Check if the cart subtotal didn't reach the min.
					if ( $subtotal_min > $cart_subtotal ) {
						// Set the notice.
						$notice = $texts['subtotal']['min'];

						// Set the cart gap.
						$cart_gap = $subtotal_min - $cart_subtotal;

						// Set the formatted cart subtotal.
						$formatted_cart_subtotal = psupsellmaster_format_currency_amount( $cart_subtotal );

						// Set the formatted min subtotal.
						$formatted_min_subtotal = psupsellmaster_format_currency_amount( $subtotal_min );

						// Set the formatted gap.
						$formatted_gap = psupsellmaster_format_currency_amount( $cart_gap );

						// Replace the tags.
						$notice = str_replace( '{cart_subtotal}', $formatted_cart_subtotal, $notice );
						$notice = str_replace( '{min_subtotal}', $formatted_min_subtotal, $notice );
						$notice = str_replace( '{gap_subtotal}', $formatted_gap, $notice );
						$notice = str_replace( '{discount_amount}', $formatted_discount, $notice );
						$notice = str_replace( '{coupon_code}', $coupon_code, $notice );

						// Add the notice to the list.
						array_push( $notices, $notice );
					}
				}
			}
		}
	}

	// Check if the notices is empty.
	if ( empty( $notices ) ) {
		return false;
	}
	?>
	<tr class="psupsellmaster-cart-row">
		<td class="psupsellmaster-cart-col" colspan="3">
			<ul class="psupsellmaster-cart-notices">
				<?php foreach ( $notices as $notice ) : ?>
					<li class="psupsellmaster-cart-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</td>
	</tr>
	<?php
}

/**
 * Get products by selectors.
 *
 * @param array $args The arguments.
 * @return array The products.
 */
function psupsellmaster_get_products_by_selectors( $args ) {
	// Set the defaults.
	$defaults = array(
		'options' => array(
			'authors'    => array(),
			'products'   => array(),
			'taxonomies' => array(),
		),
	);

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the defaults.
		$defaults['options']['products_type'] = '';
	}

	// Parse the args.
	$args = wp_parse_args( $args, $defaults );

	// Get the options.
	$options = $args['options'];

	// Set the sql where.
	$sql_where = array();

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Add a condition to the sql where.
	array_push(
		$sql_where,
		PsUpsellMaster_Database::prepare(
			'AND `products`.`post_type` = %s',
			$product_post_type
		)
	);

	// Set the product status.
	$product_status = 'publish';

	// Add a condition to the sql where.
	array_push(
		$sql_where,
		PsUpsellMaster_Database::prepare(
			'AND `products`.`post_status` = %s',
			$product_status
		)
	);

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Check the products type.
		if ( ! empty( $options['products_type'] ) && 'bundle' === $options['products_type'] ) {
			// Set the meta key.
			$meta_key = '_edd_product_type';

			// Set the meta value.
			$meta_value = 'bundle';

			// Set the sql bundle.
			$sql_bundle = PsUpsellMaster_Database::prepare(
				'
				AND
					EXISTS (
						SELECT
							1
						FROM
							%i AS `postmeta`
						WHERE
							1 = 1
						AND
							`postmeta`.`post_id` = `products`.`ID`
						AND
							`postmeta`.`meta_key` = %s
						AND
							`postmeta`.`meta_value` = %s
					)
				',
				PsUpsellMaster_Database::get_table_name( 'postmeta' ),
				$meta_key,
				$meta_value
			);

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_bundle );
		}
	}

	// Check if there are items to include.
	if ( ! empty( $options['products']['include'] ) ) {
		// Set the items.
		$items = $options['products']['include'];

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Set the items.
			$items = array();
		}

		// Set the items.
		$items = array_map( 'absint', $items );
		$items = array_filter( array_unique( $items ) );

		// Check if the items is not empty.
		if ( ! empty( $items ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare( "AND `products`.`ID` IN ( {$placeholders} )", $items );

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_items );
		}
	}

	// Check if there are items to exclude.
	if ( ! empty( $options['products']['exclude'] ) ) {
		// Set the items.
		$items = $options['products']['exclude'];

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Set the items.
			$items = array();
		}

		// Set the items.
		$items = array_map( 'absint', $items );
		$items = array_filter( array_unique( $items ) );

		// Check if the items is not empty.
		if ( ! empty( $items ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare( "AND `products`.`ID` NOT IN ( {$placeholders} )", $items );

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_items );
		}
	}

	// Check if there are items to include.
	if ( ! empty( $options['authors']['include'] ) ) {
		// Set the items.
		$items = $options['authors']['include'];

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Set the items.
			$items = array();
		}

		// Set the items.
		$items = array_map( 'absint', $items );
		$items = array_filter( array_unique( $items ) );

		// Check if the items is not empty.
		if ( ! empty( $items ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare( "AND `products`.`post_author` IN ( {$placeholders} )", $items );

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_items );
		}
	}

	// Check if there are items to exclude.
	if ( ! empty( $options['authors']['exclude'] ) ) {
		// Set the items.
		$items = $options['authors']['exclude'];

		// Check if the items is not an array.
		if ( ! is_array( $items ) ) {
			// Set the items.
			$items = array();
		}

		// Set the items.
		$items = array_map( 'absint', $items );
		$items = array_filter( array_unique( $items ) );

		// Check if the items is not empty.
		if ( ! empty( $items ) ) {
			// Set the placeholders.
			$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare( "AND `products`.`post_author` NOT IN ( {$placeholders} )", $items );

			// Add a condition to the sql where.
			array_push( $sql_where, $sql_items );
		}
	}

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Loop through the product taxonomies.
	foreach ( $product_taxonomies as $product_taxonomy ) {
		// Check if the taxonomy is not set.
		if ( ! isset( $options['taxonomies'][ $product_taxonomy ] ) ) {
			continue;
		}

		// Set the taxonomy options.
		$taxonomy_options = $options['taxonomies'][ $product_taxonomy ];

		// Check if there are items to include.
		if ( ! empty( $taxonomy_options['include'] ) ) {
			// Set the items.
			$items = $taxonomy_options['include'];

			// Check if the items is not an array.
			if ( ! is_array( $items ) ) {
				// Set the items.
				$items = array();
			}

			// Set the items.
			$items = array_map( 'absint', $items );
			$items = array_filter( array_unique( $items ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare(
				'
				SELECT
					1
				FROM
					%i AS `term_relationships`
				LEFT JOIN
					%i AS `term_taxonomy`
				ON
					`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
				WHERE
					`term_taxonomy`.`taxonomy` = %s
				AND
					`term_relationships`.`object_id` = `products`.`ID`
				',
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$product_taxonomy
			);

			// Check if the items is not empty.
			if ( ! empty( $items ) ) {
				// Set the placeholders.
				$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

				// Set the sql items.
				$sql_items .= PsUpsellMaster_Database::prepare( "AND `term_taxonomy`.`term_id` IN ( {$placeholders} )", $items );

				// Add a condition to the sql where.
				array_push( $sql_where, "AND EXISTS ( {$sql_items} )" );
			}
		}

		// Check if there are items to exclude.
		if ( ! empty( $taxonomy_options['exclude'] ) ) {
			// Set the items.
			$items = $taxonomy_options['exclude'];

			// Check if the items is not an array.
			if ( ! is_array( $items ) ) {
				// Set the items.
				$items = array();
			}

			// Set the items.
			$items = array_map( 'absint', $items );
			$items = array_filter( array_unique( $items ) );

			// Set the sql items.
			$sql_items = PsUpsellMaster_Database::prepare(
				'
				SELECT
					1
				FROM
					%i AS `term_relationships`
				LEFT JOIN
					%i AS `term_taxonomy`
				ON
					`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
				WHERE
					`term_taxonomy`.`taxonomy` = %s
				AND
					`term_relationships`.`object_id` = `products`.`ID`
				',
				PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
				PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
				$product_taxonomy
			);

			// Check if the items is not empty.
			if ( ! empty( $items ) ) {
				// Set the placeholders.
				$placeholders = implode( ', ', array_fill( 0, count( $items ), '%d' ) );

				// Set the sql items.
				$sql_items .= PsUpsellMaster_Database::prepare( "AND `term_taxonomy`.`term_id` IN ( {$placeholders} )", $items );

				// Add a condition to the sql where.
				array_push( $sql_where, "AND NOT EXISTS ( {$sql_items} )" );
			}
		}
	}

	// Set the sql where.
	$sql_where = implode( ' ', $sql_where );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`products`.`ID`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		{$sql_where}
		",
		PsUpsellMaster_Database::get_table_name( 'posts' )
	);

	// Get the products.
	$products = PsUpsellMaster_Database::get_col( $sql_query );
	$products = is_array( $products ) ? array_map( 'absint', $products ) : array();
	$products = array_filter( array_unique( $products ) );

	// Check if there are price options.
	if ( ! empty( $options['prices'] ) ) {
		// Set the ids.
		$ids = $products;

		// Set the products.
		$products = array();

		// Loop through the ids.
		foreach ( $ids as $product_id ) {
			// Get the option min.
			$option_min = isset( $options['prices']['min'] ) ? filter_var( $options['prices']['min'], FILTER_VALIDATE_FLOAT ) : false;

			// Get the option max.
			$option_max = isset( $options['prices']['max'] ) ? filter_var( $options['prices']['max'], FILTER_VALIDATE_FLOAT ) : false;

			// Get the price range.
			$price_range = psupsellmaster_get_price_range( $product_id );

			// Check the option.
			if ( false !== $option_min && $option_min > $price_range['min'] ) {
				// Skip the product.
				continue;
			}

			// Check the option.
			if ( false !== $option_max && $option_max < $price_range['max'] ) {
				// Skip the product.
				continue;
			}

			// Add the products to the list.
			array_push( $products, $product_id );
		}
	}

	// Return the products.
	return $products;
}

/**
 * Assign multiple terms from multiple taxonomies to multiple objects.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_assign_object_taxonomy_terms( $args ) {
	// Get the objects.
	$objects = isset( $args['objects'] ) ? $args['objects'] : false;
	$objects = is_array( $objects ) ? $objects : array();

	// Check if the objects is empty.
	if ( empty( $objects ) ) {
		return;
	}

	// Get the taxonomies.
	$taxonomies = isset( $args['taxonomies'] ) ? $args['taxonomies'] : false;
	$taxonomies = is_array( $taxonomies ) ? $taxonomies : array();

	// Check if the taxonomies is empty.
	if ( empty( $taxonomies ) ) {
		return;
	}

	// Loop through the objects.
	foreach ( $objects as $object_id ) {
		// Loop through the taxonomies.
		foreach ( $taxonomies as $taxonomy => $terms ) {
			// Add the terms to the product.
			wp_set_object_terms( $object_id, $terms, $taxonomy, true );
		}
	}
}

/**
 * Handles multiple taxonomies and terms, both string and int types.
 * It checks each term type. If it is numeric, it will check if its a valid term ID.
 * If it is a string, then it retrieves the corresponding term ID.
 * If the term does not exist, it inserts a new term.
 * Returns a sanitized list with existing term IDs only.
 *
 * @param array $args The arguments.
 * @return array The terms.
 */
function psupsellmaster_insert_mixed_taxonomy_terms( $args ) {
	// Set the sanitized.
	$sanitized = array();

	// Get the taxonomies.
	$taxonomies = isset( $args['taxonomies'] ) ? $args['taxonomies'] : array();

	// Check the taxonomies.
	if ( empty( $taxonomies ) ) {
		return $sanitized;
	}

	// Loop through the taxonomies.
	foreach ( $taxonomies as $taxonomy => $terms ) {
		// Check if it is not an array.
		if ( ! is_array( $terms ) ) {
			continue;
		}

		// Loop through the terms.
		foreach ( $terms as $term_value ) {
			// Set the value.
			$value = trim( $term_value );

			// Check if the value is empty.
			if ( empty( $value ) ) {
				continue;
			}

			// Check if the value is numeric and does not exist.
			if ( is_numeric( $value ) ) {
				// Set the value.
				$value = filter_var( $value, FILTER_VALIDATE_INT );

				// Check if the value is invalid.
				if ( empty( $value ) || ! term_exists( $value, $taxonomy ) ) {
					continue;
				}

				// Otherwise...
			} else {
				// Set the term.
				$term = wp_insert_term( $value, $taxonomy );

				// Check if the term couldn't be inserted.
				if ( $term instanceof WP_Error ) {
					continue;
				}

				// Check if the term id is not set.
				if ( ! isset( $term['term_id'] ) ) {
					continue;
				}

				// Set the value.
				$value = filter_var( $term['term_id'], FILTER_VALIDATE_INT );
			}

			// Check if the value is empty.
			if ( empty( $value ) ) {
				continue;
			}

			// Check if the list is not set.
			if ( ! isset( $sanitized[ $taxonomy ] ) ) {
				// Set the list.
				$sanitized[ $taxonomy ] = array();
			}

			// Add the sanitized value.
			array_push( $sanitized[ $taxonomy ], $value );
		}
	}

	// Return the sanitized.
	return $sanitized;
}

/**
 * Get the date difference between two dates.
 *
 * @param DateTime     $datetime1 The first datetime.
 * @param DateTime     $datetime2 The second datetime. Default null (now).
 * @param DateTimeZone $timezone The timezone. Default null (UTC).
 * @return array The difference of the two dates.
 */
function psupsellmaster_get_datetime_difference( $datetime1, $datetime2 = null, $timezone = null ) {
	// Check the timezone.
	if ( empty( $timezone ) ) {
		// Get current date.
		$datetime2 = new DateTime( 'now', $timezone );
	}

	// Check the timezone.
	if ( empty( $timezone ) ) {
		// Set the timezone.
		$timezone = new DateTimeZone( 'UTC' );
	}

	// Get the interval (difference between the dates).
	$interval = $datetime1->diff( $datetime2 );

	// Set the difference.
	$difference = array(
		'years'   => $interval->y,
		'months'  => $interval->m,
		'days'    => $interval->d,
		'hours'   => $interval->h,
		'minutes' => $interval->i,
		'seconds' => $interval->s,
	);

	// Return the difference.
	return $difference;
}

/**
 * Format the date difference from array to string.
 *
 * @param array $difference The datetime difference.
 * @return string The formatted string.
 */
function psupsellmaster_format_datetime_difference( $difference ) {
	// Set the formatted.
	$formatted = array();

	// Set the labels.
	$labels = array(
		'singular' => array(
			'years'   => __( 'Year', 'psupsellmaster' ),
			'months'  => __( 'Month', 'psupsellmaster' ),
			'days'    => __( 'Day', 'psupsellmaster' ),
			'hours'   => __( 'Hour', 'psupsellmaster' ),
			'minutes' => __( 'Minute', 'psupsellmaster' ),
			'seconds' => __( 'Second', 'psupsellmaster' ),
		),
		'plural'   => array(
			'years'   => __( 'Years', 'psupsellmaster' ),
			'months'  => __( 'Months', 'psupsellmaster' ),
			'days'    => __( 'Days', 'psupsellmaster' ),
			'hours'   => __( 'Hours', 'psupsellmaster' ),
			'minutes' => __( 'Minutes', 'psupsellmaster' ),
			'seconds' => __( 'Seconds', 'psupsellmaster' ),
		),
	);

	// Check the years.
	if ( isset( $difference['years'] ) && $difference['years'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['years'] ? $labels['singular']['years'] : $labels['plural']['years'];
		$item = "{$difference['years']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Check the months.
	if ( isset( $difference['months'] ) && $difference['months'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['months'] ? $labels['singular']['months'] : $labels['plural']['months'];
		$item = "{$difference['months']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Check the days.
	if ( isset( $difference['days'] ) && $difference['days'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['days'] ? $labels['singular']['days'] : $labels['plural']['days'];
		$item = "{$difference['days']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Check the hours.
	if ( isset( $difference['hours'] ) && $difference['hours'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['hours'] ? $labels['singular']['hours'] : $labels['plural']['hours'];
		$item = "{$difference['hours']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Check the minutes.
	if ( isset( $difference['minutes'] ) && $difference['minutes'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['minutes'] ? $labels['singular']['minutes'] : $labels['plural']['minutes'];
		$item = "{$difference['minutes']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Check the seconds.
	if ( isset( $difference['seconds'] ) && $difference['seconds'] > 0 ) {
		// Set the item.
		$item = 1 === $difference['seconds'] ? $labels['singular']['seconds'] : $labels['plural']['seconds'];
		$item = "{$difference['seconds']} {$item}";

		// Add the item to the list.
		array_push( $formatted, $item );
	}

	// Set the formatted.
	$formatted = implode( ' ', $formatted );

	// Return the formatted.
	return $formatted;
}

/**
 * Delete the type-related transient on shutdown.
 */
function psupsellmaster_shutdown_type_changed() {
	// Delete the transient.
	delete_transient( 'psupsellmaster_type_changed' );
}
add_action( 'shutdown', 'psupsellmaster_shutdown_type_changed' );

/**
 * Return false.
 * Useful function to use as a hook.
 * Key difference between this function and __return_false:
 * Developers can safely remove this specific hook w/o affecting others.
 */
function psupsellmaster_return_false() {
	return false;
}

/**
 * Return true.
 * Useful function to use as a hook.
 * Key difference between this function and __return_true:
 * Developers can safely remove this specific hook w/o affecting others.
 */
function psupsellmaster_return_true() {
	return true;
}

/**
 * Get the allowed HTML tags and attributes for rendering products.
 *
 * @return array The allowed html.
 */
function psupsellmaster_get_products_allowed_html() {
	// Set the tags.
	$tags = array(
		'a' => array(
			'class' => true,
			'style' => true,
		),
		'button' => array(
			'class'               => true,
			'data-action'         => true,
			'data-download-id'    => true,
			'data-nonce'          => true,
			'data-price'          => true,
			'data-price-mode'     => true,
			'data-timestamp'      => true,
			'data-token'          => true,
			'data-variable-price' => true,
		),
		'div' => array(
			'class' => true,
		),
		'form' => array(
			'class'  => true,
			'id'     => true,
			'method' => true,
		),
		'img' => array(
			'class' => true,
		),
		'input' => array(
			'class'               => true,
			'data-action'         => true,
			'data-download-id'    => true,
			'data-nonce'          => true,
			'data-price'          => true,
			'data-price-mode'     => true,
			'data-timestamp'      => true,
			'data-token'          => true,
			'data-variable-price' => true,
			'id'                  => true,
			'min'                 => true,
			'name'                => true,
			'step'                => true,
			'type'                => true,
			'value'               => true,
		),
		'svg' => array(
			'class'       => true,
			'xmlns'       => true,
			'width'       => true,
			'height'      => true,
			'viewBox'     => true,
			'aria-hidden' => true,
		),
		'p' => array(
			'style' => true,
		),
		'path' => array(
			'd' => true,
		),
		'span' => array(
			'style' => true,
		),
	);

	// Get the allowed html.
	$allowed_html = wp_kses_allowed_html( 'post' );

	// Loop through the tags.
	foreach ( $tags as $tag => $attributes ) {
		// Set the allowed.
		$allowed_html[ $tag ] = isset( $allowed_html[ $tag ] ) && is_array( $allowed_html[ $tag ] ) ? $allowed_html[ $tag ] : array();

		// Loop through the attributes.
		foreach ( $attributes as $attribute => $allowed ) {
			// Set the allowed.
			$allowed_html[ $tag ][ $attribute ] = $allowed;
		}
	}

	// Return the allowed html.
	return $allowed_html;
}

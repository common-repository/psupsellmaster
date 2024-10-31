<?php
/**
 * Functions - Popups.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the popups.
 */
function psupsellmaster_footer_render_popups() {
	?>
	<?php // Render the modal: add to cart. ?>
	<div class="psupsellmaster-modal psupsellmaster-fade" id="psupsellmaster-modal-add-to-cart" style="display: none;">
		<div class="psupsellmaster-modal-dialog psupsellmaster-modal-lg">
			<div class="psupsellmaster-modal-content">
				<div class="psupsellmaster-modal-body">
					<div class="psupsellmaster-loader-container psupsellmaster-modal-loader" style="display: none;">
						<div class="psupsellmaster-loader"></div>
					</div>
					<div class="psupsellmaster-modal-btn-close-container">
						<button class="psupsellmaster-modal-btn-close psupsellmaster-trigger-close-modal" type="button">
							<svg fill="none" height="24" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
								<line x1="18" x2="6" y1="6" y2="18"></line>
								<line x1="6" x2="18" y1="6" y2="18"></line>
							</svg>
						</button>
					</div>
					<div class="psupsellmaster-modal-ajax-container"><?php psupsellmaster_popup_add_to_cart_render_on_load(); ?></div>
					<div class="psupsellmaster-popup-footer">
						<button class="psupsellmaster-trigger-close-modal" type="button"><?php esc_html_e( 'Close', 'psupsellmaster' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php // Render the modal: exit intent. ?>
	<div class="psupsellmaster-modal psupsellmaster-fade" id="psupsellmaster-modal-exit-intent" style="display: none;">
		<div class="psupsellmaster-modal-dialog psupsellmaster-modal-lg">
			<div class="psupsellmaster-modal-content">
				<div class="psupsellmaster-modal-body">
					<div class="psupsellmaster-loader-container psupsellmaster-modal-loader" style="display: none;">
						<div class="psupsellmaster-loader"></div>
					</div>
					<div class="psupsellmaster-modal-btn-close-container">
						<button class="psupsellmaster-modal-btn-close psupsellmaster-trigger-close-modal" type="button">
							<svg fill="none" height="24" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
								<line x1="18" x2="6" y1="6" y2="18"></line>
								<line x1="6" x2="18" y1="6" y2="18"></line>
							</svg>
						</button>
					</div>
					<div class="psupsellmaster-modal-ajax-container"></div>
					<div class="psupsellmaster-popup-footer">
						<button class="psupsellmaster-trigger-close-modal" type="button"><?php esc_html_e( 'Close', 'psupsellmaster' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="psupsellmaster-modal-backdrop psupsellmaster-fade" style="display: none;"></div>
	<?php
}
add_action( 'wp_footer', 'psupsellmaster_footer_render_popups' );

/**
 * Get the exit intent popup through AJAX.
 */
function psupsellmaster_ajax_get_popup_exit_intent() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Add the headers.
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Set the output.
	$output = array();

	// Set the output data.
	$output['data'] = array();

	// Start the buffer.
	ob_start();

	// Render the popup.
	psupsellmaster_popup_exit_intent_render();

	// Set the data content.
	$output['data']['content'] = ob_get_clean();

	// Set the output success.
	$output['success'] = true;

	// Send the output.
	echo wp_json_encode( $output );

	// Die.
	wp_die();
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_popup_exit_intent', 'psupsellmaster_ajax_get_popup_exit_intent' );
add_action( 'wp_ajax_nopriv_psupsellmaster_ajax_get_popup_exit_intent', 'psupsellmaster_ajax_get_popup_exit_intent' );

/**
 * Render the exit intent popup.
 */
function psupsellmaster_popup_exit_intent_render() {
	// Set the html.
	$html = false;

	// Get the max shows.
	$max_shows = filter_var( PsUpsellMaster_Settings::get( 'exit_intent_popup_max_shows' ), FILTER_VALIDATE_INT );
	$max_shows = 0 === $max_shows || ! empty( $max_shows ) ? $max_shows : -1;

	// Get the popup meta.
	$popup_meta = psupsellmaster_db_current_visitor_meta_select( 'popup_exit_intent', true );
	$popup_meta = ! empty( $popup_meta ) ? $popup_meta : array(
		'max'   => $max_shows,
		'count' => 0,
	);

	// Get the max.
	$max = isset( $popup_meta['max'] ) ? filter_var( $popup_meta['max'], FILTER_VALIDATE_INT ) : false;
	$max = ! empty( $max ) ? $max : 0;

	// Get the count.
	$count = isset( $popup_meta['count'] ) ? filter_var( $popup_meta['count'], FILTER_VALIDATE_INT ) : false;
	$count = ! empty( $count ) ? $count : 0;

	// Check if the count is lower than the max.
	if ( 0 === $max || $count < $max ) {
		// Start the buffer.
		ob_start();

		// Render the products.
		psupsellmaster_render_products_by_location( 'popup_exit_intent' );

		// Set the html.
		$html = ob_get_clean();

		// Check if the html is not empty.
		if ( ! empty( $html ) ) {
			// Set the count.
			$popup_meta['count'] = ++$count;

			// Set the popup meta.
			psupsellmaster_db_current_visitor_meta_update( 'popup_exit_intent', $popup_meta );
		}
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_popup_exit_intent_begin' );

	// Can't use wp_kses due BIG 3rd party plugins such as EDD and WOO using style attributes within tags...
	// => Why aren't they using these functions in the first place...!?
	echo $html;

	// Allow developers to use this.
	do_action( 'psupsellmaster_popup_exit_intent_end' );
}

/**
 * Filter the products id list.
 *
 * @param array $products_id_list The products id list.
 * @return array The products id list.
 */
function psupsellmaster_popup_add_to_cart_filter_products_id_list( $products_id_list ) {
	global $psupsellmaster_popups;

	// Get the add to cart.
	$popup_add_to_cart = isset( $psupsellmaster_popups['add_to_cart'] ) ? $psupsellmaster_popups['add_to_cart'] : array();

	// Get the products.
	$products = isset( $popup_add_to_cart['products'] ) ? $popup_add_to_cart['products'] : array();

	// Set the products.
	$products = array_column( $products, 'id' );
	$products = array_map( 'absint', $products );

	// Check if the products id list is empty.
	if ( empty( $products_id_list ) ) {
		$products_id_list = array();
	}

	// Check if the products id list is not an array.
	if ( ! is_array( $products_id_list ) ) {
		// Set the products id list to an array.
		$products_id_list = array( $products_id_list );
	}

	// Merge the products id list with the products.
	$products_id_list = array_merge( $products_id_list, $products );
	$products_id_list = array_map( 'absint', $products_id_list );

	// Remove duplicate and empty entries.
	$products_id_list = array_filter( array_unique( $products_id_list ) );

	// Return the products id list.
	return $products_id_list;
}

/**
 * Get the add to cart popup through AJAX.
 */
function psupsellmaster_ajax_get_popup_add_to_cart() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Add the headers.
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );

	// Set the output.
	$output = array(
		'success' => false,
	);

	// Get the products.
	$products = isset( $_POST['products'] ) ? map_deep( wp_unslash( $_POST['products'] ), 'sanitize_text_field' ) : false;
	$products = is_array( $products ) ? $products : array();

	// Set the output data.
	$output['data'] = array();

	// Set the args.
	$args = array(
		'context'  => 'open_on_add',
		'products' => $products,
	);

	// Start the buffer.
	ob_start();

	// Render the popup.
	psupsellmaster_popup_add_to_cart_render( $args );

	// Set the data content.
	$output['data']['content'] = ob_get_clean();

	// Set the output success.
	$output['success'] = true;

	// Send the output.
	echo wp_json_encode( $output );

	// Die.
	wp_die();
}
add_action( 'wp_ajax_psupsellmaster_ajax_get_popup_add_to_cart', 'psupsellmaster_ajax_get_popup_add_to_cart' );
add_action( 'wp_ajax_nopriv_psupsellmaster_ajax_get_popup_add_to_cart', 'psupsellmaster_ajax_get_popup_add_to_cart' );

/**
 * Render the add to cart popup.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_popup_add_to_cart_render( $args = array() ) {
	global $psupsellmaster_popups;

	// Get the context.
	$context = isset( $args['context'] ) && in_array( $args['context'], array( 'open_on_add', 'open_on_load' ), true ) ? $args['context'] : 'open_on_add';

	// Set the products.
	$products = isset( $args['products'] ) && is_array( $args['products'] ) ? $args['products'] : array();

	// Set the global.
	$psupsellmaster_popups['add_to_cart']['products'] = $products;

	// Get the product.
	$product = is_array( $products ) ? array_shift( $products ) : $products;

	// Get the product id.
	$product_id = isset( $product['id'] ) ? filter_var( $product['id'], FILTER_VALIDATE_INT ) : false;
	$product_id = ! empty( $product_id ) ? $product_id : 0;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the parent id.
		$parent_id = wp_get_post_parent_id( $product_id );

		// Set the product id.
		$product_id = ! empty( $parent_id ) ? $parent_id : $product_id;
	}

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return false;
	}

	// Get the variations.
	$variations = isset( $product['variations'] ) && is_array( $product['variations'] ) ? $product['variations'] : array();
	$variations = array_map( 'filter_var', $variations, array_fill( 0, count( $variations ), FILTER_VALIDATE_INT ) );

	// Add the filter.
	add_filter( 'psupsellmaster_base_products_id_list', 'psupsellmaster_popup_add_to_cart_filter_products_id_list', 6 );
	add_filter( 'psupsellmaster_ignore_products_id_list', 'psupsellmaster_popup_add_to_cart_filter_products_id_list', 6 );

	// Start the buffer.
	ob_start();

	// Render the products.
	psupsellmaster_render_products_by_location( 'popup_add_to_cart' );

	// Get the extra products.
	$extra_products = ob_get_clean();

	// Remove the filter.
	remove_filter( 'psupsellmaster_base_products_id_list', 'psupsellmaster_popup_add_to_cart_filter_products_id_list' );
	remove_filter( 'psupsellmaster_ignore_products_id_list', 'psupsellmaster_popup_add_to_cart_filter_products_id_list' );

	// Check if the extra products is empty.
	if ( empty( $extra_products ) ) {
		return false;
	}

	// Check if the context is open on add (ajax).
	if ( 'open_on_add' === $context ) {
		// Set the popup meta flag as done.
		psupsellmaster_db_current_visitor_meta_update( 'popup_add_to_cart_done', true );
	}

	// Delete the popup meta.
	psupsellmaster_db_current_visitor_meta_delete( 'popup_add_to_cart' );

	// Get the item title.
	$item_title = get_the_title( $product_id );
	$item_title = ! empty( $item_title ) ? $item_title : $product_id;

	// Set the item variations.
	$item_variations = array();

	// Set the thumbnail.
	$thumbnail = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the product.
		$product = wc_get_product( $product_id );

		// Check if the variations is empty.
		if ( empty( $variations ) ) {
			// Add the item to the list.
			array_push( $item_variations, array( 'price' => wc_price( $product->get_price() ) ) );

			// Otherwise...
		} else {
			// Loop through the variations.
			foreach ( $variations as $variation_id ) {
				// Get the variation.
				$variation = wc_get_product( $variation_id );

				// Check the variation.
				if ( ! $variation instanceof WC_Product ) {
					continue;
				}

				// Set the item.
				$item_variation = array(
					'price' => wc_price( $variation->get_price() ),
					'title' => get_the_excerpt( $variation_id ),
				);

				// Add the item to the list.
				array_push( $item_variations, $item_variation );
			}
		}

		// Check the product.
		if ( $product instanceof WC_Product ) {
			// Set the thumbnail.
			$thumbnail = $product->get_image();
		}

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Check if the product does not have variable prices.
		if ( ! edd_has_variable_prices( $product_id ) ) {
			// Add the item to the list.
			array_push( $item_variations, array( 'price' => edd_price( $product_id, false ) ) );

			// Otherwise...
		} elseif ( ! empty( $variations ) ) {
			// Loop through the variations.
			foreach ( $variations as $variation_id ) {
				// Set the item.
				$item_variation = array(
					'price' => edd_price( $product_id, false, $variation_id ),
					'title' => edd_get_price_option_name( $product_id, $variation_id ),
				);

				// Add the item to the list.
				array_push( $item_variations, $item_variation );
			}
		}

		// Check the thumbnail.
		if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $product_id ) ) {
			// Set the thumbnail.
			$thumbnail = get_the_post_thumbnail( $product_id, apply_filters( 'psupsellmaster_popup_add_to_cart_image_size', 'medium' ) );
		}
	}

	// Set the cart uri.
	$cart_uri = psupsellmaster_get_cart_uri();

	// Get the headline.
	$headline = PsUpsellMaster_Settings::get( 'add_to_cart_popup_headline' );
	$headline = apply_filters( 'psupsellmaster_popup_add_to_cart_headline', $headline );

	// Get the tagline.
	$tagline = PsUpsellMaster_Settings::get( 'add_to_cart_popup_tagline' );
	$tagline = apply_filters( 'psupsellmaster_popup_add_to_cart_tagline', $tagline );

	// Get the button checkout.
	$button_checkout = PsUpsellMaster_Settings::get( 'add_to_cart_popup_button_checkout' );
	$button_checkout = apply_filters( 'psupsellmaster_popup_add_to_cart_button_checkout', $button_checkout );

	// Allow developers to use this.
	do_action( 'psupsellmaster_popup_add_to_cart_begin', $context );
	?>
	<div class="psupsellmaster-added-container" data-context="<?php echo esc_attr( $context ); ?>">
		<?php if ( ! empty( $headline ) ) : ?>
			<div class="psupsellmaster-added-headline">
				<span><?php echo esc_html( $headline ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $tagline ) ) : ?>
			<div class="psupsellmaster-added-tagline">
				<span><?php echo esc_html( $tagline ); ?></span>
			</div>
		<?php endif; ?>
		<div class="psupsellmaster-added-product">
			<?php if ( ! empty( $thumbnail ) ) : ?>
				<div class="psupsellmaster-added-image">
					<?php echo wp_kses_post( $thumbnail ); ?>
				</div>
			<?php endif; ?>
			<div class="psupsellmaster-added-details">
				<?php if ( ! empty( $item_title ) ) : ?>
					<div class="psupsellmaster-added-title">
						<span><?php echo esc_html( $item_title ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $item_variations ) ) : ?>
					<ul class="psupsellmaster-list psupsellmaster-added-prices">
						<?php foreach ( $item_variations as $variation ) : ?>
							<li class="psupsellmaster-list-item">
								<?php if ( ! empty( $variation['title'] ) ) : ?>
									<div class="psupsellmaster-title"><?php echo esc_html( $variation['title'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $variation['price'] ) ) : ?>
									<div class="psupsellmaster-price">
										<?php echo wp_kses_post( $variation['price'] ); ?>
									</div>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( ! empty( $cart_uri ) && ! empty( $button_checkout ) ) : ?>
			<div class="psupsellmaster-added-checkout">
				<a class="button psupsellmaster-added-btn-checkout" href=<?php echo esc_url( $cart_uri ); ?>><?php echo esc_html( $button_checkout ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<?php

	// Allow developers to use this.
	do_action( 'psupsellmaster_popup_add_to_cart_before_products', $context );

	// Can't use wp_kses due BIG 3rd party plugins such as EDD and WOO using style attributes within tags...
	// => Why aren't they using these functions in the first place...!?
	echo $extra_products;

	// Allow developers to use this.
	do_action( 'psupsellmaster_popup_add_to_cart_end', $context );
}

/**
 * Render the add to cart popup on page load.
 */
function psupsellmaster_popup_add_to_cart_render_on_load() {
	// Set the open on load (never, but there are exceptions).
	$open_on_load = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the open on load (true, if no ajax).
		$open_on_load = ! psupsellmaster_add_to_cart_has_ajax();

		// Check if the open on load is false.
		if ( ! $open_on_load ) {
			// Set the open on load (true, if page is checkout or product).
			$open_on_load = psupsellmaster_is_page_checkout() || psupsellmaster_is_page_product();
		}

		// Check if the open on load is false.
		if ( ! $open_on_load ) {

			// Check if it should go straight to the checkout.
			if ( psupsellmaster_add_to_cart_should_go_to_checkout() ) {
				// Set the open on load (true, if it redirects to checkout and if page is cart).
				$open_on_load = psupsellmaster_is_page_cart();
			}
		}

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the open on load (true if page is checkout).
		$open_on_load = psupsellmaster_is_page_checkout();
	}

	// Check if the open on load is not true.
	if ( true !== $open_on_load ) {
		return false;
	}

	// Get the popup meta.
	$popup_meta = psupsellmaster_db_current_visitor_meta_select( 'popup_add_to_cart', true );

	// Get the stored products.
	$stored_products = isset( $popup_meta['products'] ) ? $popup_meta['products'] : array();
	$stored_products = is_array( $stored_products ) ? $stored_products : array();

	// Set the args.
	$args = array(
		'context'  => 'open_on_load',
		'products' => $stored_products,
	);

	// Render the popup.
	psupsellmaster_popup_add_to_cart_render( $args );
}

/**
 * Delete popup-related data on shutdown.
 */
function psupsellmaster_popup_shutdown() {
	// Check if it is a ajax request.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// Check if it is a cron request.
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return false;
	}

	/**
	 * Delete visitor meta related to the popup (add to cart).
	 * This is needed because sometimes this data is stored (when a
	 * product is added to the cart) but it is not always deleted;
	 * since the popup might not run in some pages.
	 * It depends on the settings in use. eg. excluded pages.
	 */

	// Delete the popup meta.
	psupsellmaster_db_current_visitor_meta_delete( 'popup_add_to_cart' );

	// Delete the popup meta flag.
	psupsellmaster_db_current_visitor_meta_delete( 'popup_add_to_cart_done' );
}
add_action( 'shutdown', 'psupsellmaster_popup_shutdown' );

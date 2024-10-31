<?php
/**
 * Functions - Scripts.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the scripts.
 */
function psupsellmaster_register_scripts() {
	// Set the js url.
	$js_url = PSUPSELLMASTER_URL . 'assets/js/';

	// Set the vendor url.
	$vendor_url = PSUPSELLMASTER_URL . 'assets/vendor/';

	// Set the file suffix.
	$file_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register the scripts.
	wp_register_script( 'psupsellmaster-script-main', "{$js_url}main{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-script-owl-carousel', "{$vendor_url}owl-carousel/owl.carousel{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-script-products', "{$js_url}products{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-script-products-carousel', "{$js_url}products-carousel{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-script-popup-add-to-cart', "{$js_url}popup-add-to-cart{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );

	// Allow developers to use this.
	do_action( 'psupsellmaster_register_scripts' );
}
add_action( 'wp_enqueue_scripts', 'psupsellmaster_register_scripts' );

/**
 * Register the styles.
 */
function psupsellmaster_register_styles() {
	// Set the css url.
	$css_url = PSUPSELLMASTER_URL . 'assets/css/';

	// Set the vendor url.
	$vendor_url = PSUPSELLMASTER_URL . 'assets/vendor/';

	// Set the file suffix.
	$file_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register the styles.
	wp_register_style( 'psupsellmaster-style-main', "{$css_url}main{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-style-owl-carousel', "{$vendor_url}owl-carousel/owl.carousel{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-style-owl-carousel-theme', "{$vendor_url}owl-carousel/owl.theme.default{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-style-grid', "{$css_url}grid{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-style-products', "{$css_url}products{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-style-popups', "{$css_url}popups{$file_suffix}.css", array(), PSUPSELLMASTER_VER );

	// Allow developers to use this.
	do_action( 'psupsellmaster_register_styles' );
}
add_action( 'wp_enqueue_scripts', 'psupsellmaster_register_styles' );

/**
 * Enqueue a script.
 *
 * @param string $script_key The script key.
 */
function psupsellmaster_enqueue_script( $script_key ) {
	// Check the script key.
	if ( 'main' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-script-main';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'ajax'         => array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'psupsellmaster-ajax-nonce' ),
				),
				'current'      => array(
					'page'    => psupsellmaster_get_current_page(),
					'product' => array( 'id' => get_the_ID() ),
				),
				'integrations' => array(
					'woo'       => psupsellmaster_is_plugin_active( 'woo' ),
					'edd'       => psupsellmaster_is_plugin_active( 'edd' ),
					'elementor' => psupsellmaster_is_plugin_active( 'elementor' ),
				),
				'plugin'       => array(
					'is_lite' => psupsellmaster_is_lite(),
					'is_pro'  => psupsellmaster_is_pro(),
					'version' => PSUPSELLMASTER_VER,
				),
				'server'       => array(
					'admin' => is_admin(),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_data_main = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'vendor-owl-carousel' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-script-owl-carousel' );

		// Check the script key.
	} elseif ( 'edd-wish-lists' === $script_key ) {
		// Check if the EDD Wish Lists plugin is enabled.
		if ( ( psupsellmaster_is_plugin_active( 'edd-wish-lists' ) ) ) {
			// Enqueue the script.
			wp_enqueue_script( 'edd-wl' );
		}

		// Check the script key.
	} elseif ( 'edd-wish-lists-modal' === $script_key ) {
		// Check if the EDD Wish Lists plugin is enabled.
		if ( ( psupsellmaster_is_plugin_active( 'edd-wish-lists' ) ) ) {
			// Enqueue the script.
			wp_enqueue_script( 'edd-wl-modal' );
		}

		// Check the script key.
	} elseif ( 'products' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-script-products' );

		// Check the script key.
	} elseif ( 'products-carousel' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-script-products-carousel' );

		// Check the script key.
	} elseif ( 'popup-add-to-cart' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-script-popup-add-to-cart';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

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

			//
			// Although there are similar function calls, the definitions.
			// for open on add + open on load are very separated here.
			// so it is much easier to understand the conditions.
			//

			// Set the open on add (always, but there are exceptions).
			$open_on_add = true;

			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				// Set the open on add (false, if no ajax).
				$open_on_add = psupsellmaster_add_to_cart_has_ajax();

				// Check if the open on add is true.
				if ( $open_on_add ) {
					// Set the open on add (false, if page is checkout).
					$open_on_add = ! psupsellmaster_add_to_cart_should_go_to_checkout();
				}

				// Check if the open on add is true.
				if ( $open_on_add ) {
					// Set the open on add (false, if page is checkout).
					$open_on_add = ! psupsellmaster_is_page_checkout();
				}

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				// Set the open on add (false, if it redirects to checkout).
				$open_on_add = ! psupsellmaster_add_to_cart_should_go_to_checkout();

				// Check if the open on add is true.
				if ( $open_on_add ) {
					// Set the open on add (false, if page is checkout).
					$open_on_add = ! psupsellmaster_is_page_checkout();
				}
			}

			// Set the script data.
			$script_data = array(
				'open_on_add'  => $open_on_add,
				'open_on_load' => $open_on_load,
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_data_popup_add_to_cart = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_enqueue_script', $script_key );
}

/**
 * Enqueue a style.
 *
 * @param string $style_key The style key.
 */
function psupsellmaster_enqueue_style( $style_key ) {
	// Check the style key.
	if ( 'main' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-main' );

		// Check the style key.
	} elseif ( 'vendor-owl-carousel' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-owl-carousel' );

		// Check the style key.
	} elseif ( 'vendor-owl-carousel-theme' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-owl-carousel-theme' );

		// Check the style key.
	} elseif ( 'grid' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-grid' );

		// Check the style key.
	} elseif ( 'products' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-products' );

		// Check the style key.
	} elseif ( 'popups' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-style-popups' );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_enqueue_style', $style_key );
}

/**
 * Enqueue the scripts and styles.
 */
function psupsellmaster_enqueue_scripts_styles() {
	// Check the current page.
	if ( psupsellmaster_is_page( 'checkout' ) || psupsellmaster_is_page( 'cart' ) ) {
		// Get the is enabled.
		$is_enabled = psupsellmaster_feature_is_active( 'page_checkout' );

		// Check if it is enabled.
		if ( $is_enabled ) {
			// Get the display type.
			$display_type = PsUpsellMaster_Settings::get( 'checkout_page_display_type' );

			// Enqueue the scripts.
			psupsellmaster_enqueue_script( 'edd-wish-lists' );
			psupsellmaster_enqueue_script( 'edd-wish-lists-modal' );
			psupsellmaster_enqueue_script( 'main' );
			psupsellmaster_enqueue_script( 'products' );

			// Check the display type.
			if ( 'carousel' === $display_type ) {
				// Enqueue the scripts.
				psupsellmaster_enqueue_script( 'vendor-owl-carousel' );
				psupsellmaster_enqueue_script( 'products-carousel' );
			}

			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'main' );

			// Check the display type.
			if ( 'carousel' === $display_type ) {
				// Enqueue the styles.
				psupsellmaster_enqueue_style( 'vendor-owl-carousel' );
				psupsellmaster_enqueue_style( 'vendor-owl-carousel-theme' );

				// Otherwise...
			} else {
				// Enqueue the styles.
				psupsellmaster_enqueue_style( 'grid' );
			}

			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'products' );
		}

		// Check the current page.
	} elseif ( psupsellmaster_is_page( 'product' ) ) {
		// Get the is enabled.
		$is_enabled = psupsellmaster_feature_is_active( 'page_product' );

		// Check if it is enabled.
		if ( $is_enabled ) {
			// Get the display type.
			$display_type = PsUpsellMaster_Settings::get( 'product_page_display_type' );

			// Enqueue the scripts.
			psupsellmaster_enqueue_script( 'edd-wish-lists' );
			psupsellmaster_enqueue_script( 'edd-wish-lists-modal' );
			psupsellmaster_enqueue_script( 'main' );
			psupsellmaster_enqueue_script( 'products' );

			// Check the display type.
			if ( 'carousel' === $display_type ) {
				// Enqueue the scripts.
				psupsellmaster_enqueue_script( 'vendor-owl-carousel' );
				psupsellmaster_enqueue_script( 'products-carousel' );
			}

			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'main' );

			// Check the display type.
			if ( 'carousel' === $display_type ) {
				// Enqueue the styles.
				psupsellmaster_enqueue_style( 'vendor-owl-carousel' );
				psupsellmaster_enqueue_style( 'vendor-owl-carousel-theme' );

				// Otherwise...
			} else {
				// Enqueue the styles.
				psupsellmaster_enqueue_style( 'grid' );
			}

			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'products' );
		}
	}

	// Set the should enqueue.
	$popup_add_to_cart_should_enqueue = false;

	// Get the is enabled.
	$popup_add_to_cart_is_enabled = psupsellmaster_feature_is_active( 'popup_add_to_cart' );

	// Check if the popup add to cart is enabled.
	if ( $popup_add_to_cart_is_enabled ) {
		// Get the excluded pages.
		$popup_add_to_cart_excluded_pages = PsUpsellMaster_Settings::get( 'add_to_cart_popup_excluded_pages' );
		$popup_add_to_cart_excluded_pages = is_array( $popup_add_to_cart_excluded_pages ) ? $popup_add_to_cart_excluded_pages : array();

		// Check if the excluded pages is empty.
		if ( empty( $popup_add_to_cart_excluded_pages ) ) {
			// Set the should enqueue.
			$popup_add_to_cart_should_enqueue = true;

			// Check the current page.
		} elseif ( ! in_array( get_queried_object_id(), $popup_add_to_cart_excluded_pages, true ) ) {
			// Set the should enqueue.
			$popup_add_to_cart_should_enqueue = true;
		}
	}

	// Check if it should enqueue the popup add to cart.
	if ( $popup_add_to_cart_should_enqueue ) {
		// Get the display type.
		$popup_add_to_cart_display_type = PsUpsellMaster_Settings::get( 'add_to_cart_popup_display_type' );

		// Enqueue the scripts.
		psupsellmaster_enqueue_script( 'edd-wish-lists' );
		psupsellmaster_enqueue_script( 'edd-wish-lists-modal' );
		psupsellmaster_enqueue_script( 'main' );
		psupsellmaster_enqueue_script( 'products' );

		// Check the display type.
		if ( 'carousel' === $popup_add_to_cart_display_type ) {
			// Enqueue the scripts.
			psupsellmaster_enqueue_script( 'vendor-owl-carousel' );
			psupsellmaster_enqueue_script( 'products-carousel' );
		}

		// Enqueue the scripts.
		psupsellmaster_enqueue_script( 'popup-add-to-cart' );

		// Enqueue the styles.
		psupsellmaster_enqueue_style( 'main' );
		psupsellmaster_enqueue_style( 'popups' );

		// Check the display type.
		if ( 'carousel' === $popup_add_to_cart_display_type ) {
			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'vendor-owl-carousel' );
			psupsellmaster_enqueue_style( 'vendor-owl-carousel-theme' );

			// Otherwise...
		} else {
			// Enqueue the styles.
			psupsellmaster_enqueue_style( 'grid' );
		}

		// Enqueue the styles.
		psupsellmaster_enqueue_style( 'products' );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_enqueue_scripts_styles' );
}
add_action( 'wp_enqueue_scripts', 'psupsellmaster_enqueue_scripts_styles', 20 );

/**
 * Enqueue the block assets.
 */
function psupsellmaster_enqueue_block_assets() {
	// Allow developers to use this.
	do_action( 'psupsellmaster_enqueue_block_assets' );
}
add_action( 'enqueue_block_assets', 'psupsellmaster_enqueue_block_assets' );

/**
 * Enqueue the block editor assets.
 */
function psupsellmaster_enqueue_block_editor_assets() {
	// Allow developers to use this.
	do_action( 'psupsellmaster_enqueue_block_editor_assets' );
}
add_action( 'enqueue_block_editor_assets', 'psupsellmaster_enqueue_block_editor_assets' );

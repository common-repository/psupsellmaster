/**
 * Products.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterProducts;

	PsUpsellmasterProducts = {
		attributes: {},
		events: {
			onAjaxComplete: function onAjaxComplete( event, xhr, settings ) {
				var data, reload;

				// Check if the current page is not checkout.
				if ( 'checkout' !== PsUpsellmaster.attributes.current.page ) {
					// Abort. It should reload only the checkout page (after adding to cart).
					return true;
				}

				// Set the reload.
				reload = false;

				// Check if the WooCommerce plugin is enabled.
				if ( PsUpsellmaster.attributes.integrations.woo ) {

					if ( '/?wc-ajax=add_to_cart' === settings.url ) {
						// Set the reload.
						reload = true
					}

					// Otherwise, check if the Easy Digital Downloads plugin is enabled.
				} else if ( PsUpsellmaster.attributes.integrations.edd ) {
					// Get the data.
					data = JSON.parse( '{"' + decodeURI( settings.data.replace( /&/g, '\",\"' ).replace( /=/g, '\":\"' ) ) + '"}' );

					if ( 'edd_add_to_cart' === data['action'] ) {
						// Set the reload.
						reload = true
					}
				}

				// Check if the reload is true.
				if ( true === reload ) {
					// Reload the page.
					PsUpsellmaster.functions.reload();
				}

			},
			onDocumentReady: function onDocumentReady() {
				PsUpsellmasterProducts.functions.start();
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterProducts.functions.registerAttributes();
				PsUpsellmasterProducts.functions.registerEvents();
			},
			refreshScreenEqualHeights: function refreshScreenEqualHeights() {

				// Check if the WooCommerce plugin is enable.
				if ( PsUpsellmaster.attributes.integrations.woo ) {
					// Set equal heights for the products elements.
					PsUpsellmasterProducts.functions.setProductsEqualHeights( '.psupsellmaster.psupsellmaster-product.display-type-list' );
				}

			},
			registerAttributes: function registerAttributes() {

				if ( 'undefined' !== typeof psupsellmaster_data_products ) {
					PsUpsellmasterProducts.attributes = psupsellmaster_data_products;
				}

				// Define the products.
				PsUpsellmaster.attributes.products = PsUpsellmasterProducts;
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterProducts.events.onDocumentReady );
				$( document ).on( 'ajaxComplete', PsUpsellmasterProducts.events.onAjaxComplete );
			},
			setEqualHeights: function setEqualHeights( elements ) {
				var maxHeight;

				// Define the max height.
				maxHeight = 0;

				elements.each(
					function () {
						var height;

						// Get height of current element.
						height = $( this ).height();

						// Define the max height.
						maxHeight = height > maxHeight ? height : maxHeight;
					}
				);

				// Check if the max height is zero.
				if ( 0 === maxHeight ) {
					return false;
				}

				// Set the max height for all elements.
				elements.height( maxHeight );
			},
			setProductsEqualHeights: function setProductsEqualHeights( selectors ) {
				// Loop through the sets of products.
				$( selectors ).each(
					function () {
						var body, card, footer, set, type;

						// Get the current set of products.
						set = $( this );

						// Get the set type.
						type = set.data( 'page' );

						// Check if the type is not widget.
						if ( 'widget' !== type ) {
							// Get the product card.
							card = set.find( '.psupsellmaster-product-card' );

							// Get the card body.
							body = card.find( '.psupsellmaster-product-card-body' );

							// Get the card footer.
							footer = card.find( '.psupsellmaster_wc_product_cart_footer' );

							// Set equal heights for title elements.
							PsUpsellmasterProducts.functions.setEqualHeights( body.find( '.psupsellmaster-products-title' ) );

							// Set equal heights for description elements.
							PsUpsellmasterProducts.functions.setEqualHeights( body.find( '.psupsellmaster-products-description' ) );

							// Set equal heights for add to cart button elements.
							PsUpsellmasterProducts.functions.setEqualHeights( footer.find( '.add_to_cart_button' ) );

							// Set equal heights for footer elements.
							PsUpsellmasterProducts.functions.setEqualHeights( footer );
						}

					}
				);
			},
			start: function start() {
				PsUpsellmasterProducts.functions.refreshScreenEqualHeights();
			},
		},
	};

	PsUpsellmasterProducts.functions.init();
} )( jQuery );

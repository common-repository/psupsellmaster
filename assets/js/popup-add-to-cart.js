/**
 * Popup Add to Cart.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAddToCartPopup;

	PsUpsellmasterAddToCartPopup = {
		attributes: {},
		events: {
			onClickBtnAddToCart: function onClickBtnAddToCart( event ) {
				// Check if the open on add is false.
				if ( ! PsUpsellmasterAddToCartPopup.attributes.open_on_add ) {
					// Abort.
					return;
				}

				// Check if there is a modal open.
				if ( $( 'body' ).hasClass( 'psupsellmaster-modal-open' ) ) {
					// Abort.
					return;
				}

				// Check if the popups lock is true.
				if ( true === PsUpsellmaster.attributes.popups.lock ) {
					// Abort.
					return;
				}

				// Get the button.
				var button = $( this );

				// Set the data.
				var data = new Object();

				// Set the product.
				var product = new Object();

				// Check if the WooCommerce plugin is enabled.
				if ( PsUpsellmaster.attributes.integrations.woo ) {
					// Set the product id.
					product.id = button.data( 'product_id' );

					// Check if the current page is product.
					if ( 'product' === PsUpsellmaster.attributes.current.page ) {
						// Check if the product id is equal to the current product id.
						// (meaning its the main product of product the page).
						if ( product.id === PsUpsellmaster.attributes.current.page ) {
							// Abort. It will open this popup on page load (just after the page is refreshed by WooCommerce).
							return;
						}
					}

					// Set the variations.
					product.variations = new Array( product.id );

					// Set the data products.
					data.products = new Array( product );

					// Otherwise, check if the Easy Digital Downloads plugin is enabled.
				} else if ( PsUpsellmaster.attributes.integrations.edd ) {
					// Set the product id.
					product.id = button.data( 'download-id' );

					// Set the product variations.
					product.variations = new Array();

					// Get the form.
					var form = button.parents( 'form' ).last();

					// Check the variable price attribute.
					if ( 'yes' === button.data( 'variable-price' ) ) {
						// Check if a hidden price option does exist.
						if ( form.find( '.edd_price_option_' + product.id + '[type="hidden"]' ).length > 0 ) {
							// Set the variations.
							product.variations[0] = $( '.edd_price_option_' + product.id, form ).val();
						} else {
							// Set the variations.
							product.variations = new Array();

							// Loop through the checked price options.
							form.find( '.edd_price_option_' + product.id + ':checked', form ).each(
								function ( index ) {
									// Set the variations.
									product.variations[ index ] = $( this ).val();
								}
							);
						}

					}

					// Set the data products.
					data.products = new Array( product );
				}

				// Get the popup.
				PsUpsellmasterAddToCartPopup.functions.get( data );
			},
			onDocumentReady: function onDocumentReady() {
				// Check if the open on load is false.
				if ( ! PsUpsellmasterAddToCartPopup.attributes.open_on_load ) {
					// Abort.
					return;
				}

				// Check if the popups lock is true.
				if ( true === PsUpsellmaster.attributes.popups.lock ) {
					// Abort.
					return;
				}

				// Open the popup.
				PsUpsellmasterAddToCartPopup.functions.open( 'open_on_load' );
			},
		},
		functions: {
			get: function get( data ) {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-add-to-cart' );

				// Get the header.
				var header = modal.find( '.psupsellmaster-modal-header' );

				// Get the title.
				var title = header.find( '.psupsellmaster-modal-title' );

				// Get the body.
				var body = modal.find( '.psupsellmaster-modal-body' );

				// Get the ajax container.
				var ajax = body.find( '.psupsellmaster-modal-ajax-container' );

				// Hide the title.
				title.hide();

				// Remove the ajax  html.
				ajax.html( '' );

				// Set the action.
				data.action = 'psupsellmaster_ajax_get_popup_add_to_cart';

				// Set the nonce.
				data.nonce = PsUpsellmaster.attributes.ajax.nonce;

				// Check if the popups lock is true.
				if ( true === PsUpsellmaster.attributes.popups.lock ) {
					return false;
				}

				// Set the popups lock to true.
				PsUpsellmaster.attributes.popups.lock = true;

				// Make the request.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'post',
						dataType: 'json',
						data: data,
					}
				).done(
					function ( response ) {
						// Check if the response data does exist.
						if ( 'undefined' !== typeof response.data ) {
							// Check if the response content does exist.
							if ( response.data.content && 0 !== response.data.content.length ) {
								// Set the ajax html.
								ajax.html( response.data.content );

								// Check if the Easy Digital Downloads plugin is enabled.
								if ( PsUpsellmaster.attributes.integrations.edd ) {
									// Add the edd-has-js class (edd only adds this class on page load).
									ajax.find( '.edd-add-to-cart:not(.edd-no-js)' ).addClass( 'edd-has-js' );
								}

								// Check if the response title does exist.
								if ( response.data.title && 0 !== response.data.title.length ) {
									// Set the title.
									title.html( response.data.title );

									// Show the title.
									title.show();
								}

								// Open the modal.
								PsUpsellmasterAddToCartPopup.functions.open( 'open_on_add' );
							}

						}

					}
				).always(
					function () {
						// Set the popups lock to false.
						PsUpsellmaster.attributes.popups.lock = false;
					}
				);
			},
			init: function init() {
				PsUpsellmasterAddToCartPopup.functions.registerAttributes();
				PsUpsellmasterAddToCartPopup.functions.registerEvents();
			},
			open: function open( context ) {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-add-to-cart' );

				// Get the body.
				var body = modal.find( '.psupsellmaster-modal-body' );

				// Get the ajax container.
				var ajax = body.find( '.psupsellmaster-modal-ajax-container' );

				// Get the added container.
				var added = ajax.find( '.psupsellmaster-added-container' );

				// Check if the context is open on load.
				if ( 'open_on_load' === context ) {

					// Check if the contexts don't match.
					if ( context !== added.attr( 'data-context' ) ) {
						return;
					}

					// Remove the attr data context.
					added.removeAttr( 'data-context' );
				}

				// Check if the Easy Digital Downloads plugin is enabled.
				if ( PsUpsellmaster.attributes.integrations.edd ) {
					// Add the edd-has-js class (edd only adds this class on page load).
					ajax.find( '.edd-add-to-cart:not(.edd-no-js)' ).addClass( 'edd-has-js' );
				}

				// Check if the type is carousel.
				if ( 'carousel' === ajax.find( '.psupsellmaster.psupsellmaster-product' ).data( 'display-type' ) ) {
					// Add the type carousel class.
					modal.addClass( 'psupsellmaster-type-carousel' );
				} else {
					// Remove the type carousel class.
					modal.removeClass( 'psupsellmaster-type-carousel' );
				}

				// Open the modal.
				PsUpsellmaster.functions.openModal( modal );

				// Check if the products is an object.
				if ( 'object' === typeof PsUpsellmaster.attributes.products ) {
					// Start the products.
					PsUpsellmaster.attributes.products.functions.start();
				}

				// Check if the products carousel is an object.
				if ( 'object' === typeof PsUpsellmaster.attributes.productsCarousel ) {
					// Start the carousels.
					PsUpsellmaster.attributes.productsCarousel.functions.start();
				}
			},
			registerAttributes: function registerAttributes() {
				if ( 'undefined' !== typeof psupsellmaster_data_popup_add_to_cart ) {
					PsUpsellmasterAddToCartPopup.attributes = psupsellmaster_data_popup_add_to_cart;
				}

				// Set the popups.
				PsUpsellmaster.attributes.popups = PsUpsellmaster.attributes.popups || new Object();

				// Set the popup add to cart.
				PsUpsellmaster.attributes.popups.addToCart = PsUpsellmasterAddToCartPopup;

				// Set the popups lock (to avoid showing more than one popup at the same time).
				PsUpsellmaster.attributes.popups.lock = false;
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAddToCartPopup.events.onDocumentReady );
				$( 'body' ).on( 'click', '.edd-add-to-cart', PsUpsellmasterAddToCartPopup.events.onClickBtnAddToCart );
				$( 'body' ).on( 'click', '.add_to_cart_button.product_type_simple', PsUpsellmasterAddToCartPopup.events.onClickBtnAddToCart );
				$( 'body' ).on( 'click', '.add_to_cart_button.ajax_add_to_cart', PsUpsellmasterAddToCartPopup.events.onClickBtnAddToCart );
			},
		},
	};

	PsUpsellmasterAddToCartPopup.functions.init();
} )( jQuery );

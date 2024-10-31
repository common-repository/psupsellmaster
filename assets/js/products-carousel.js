/**
 * Products Carousel.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterProductsCarousel;

	PsUpsellmasterProductsCarousel = {
		attributes: {},
		events: {
			onAddedToCart: function onAddedToCart( event ) {
				setTimeout(
					function () {
						// Start the carousels.
						PsUpsellmasterProductsCarousel.functions.start();
					},
					1500
				);
			},
			onChangeElementorEditMode: function onChangeElementorEditMode( event ) {
				// Start the carousels.
				PsUpsellmasterProductsCarousel.functions.start();
			},
			onClickBtnNext: function onClickBtnNext( event ) {
				event.preventDefault();

				// Navigate next.
				PsUpsellmasterProductsCarousel.functions.navigate( 'next' );
			},
			onClickBtnPrevious: function onClickBtnPrevious( event ) {
				event.preventDefault();

				// Navigate previous.
				PsUpsellmasterProductsCarousel.functions.navigate( 'previous' );
			},
			onDocumentReady: function onDocumentReady() {
				// Start the carousels.
				PsUpsellmasterProductsCarousel.functions.start();
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterProductsCarousel.functions.registerAttributes();
				PsUpsellmasterProductsCarousel.functions.registerEvents();
				PsUpsellmasterProductsCarousel.functions.registerEventsIntegrations();
			},
			navigate: function navigate( direction ) {
				$( document ).ready(
					function () {
						var dots, next, previous;

						// Get the dots.
						dots = $( '.psupsellmaster-owl-carousel .owl-dot' );

						// Check the direction.
						if ( 'next' === direction ) {
								// Get the next.
								next = dots.filter( '.active' ).next();

							// Check if it was not found.
							if ( ! next.length ) {
								// Get the next.
								next = dots.first();
							}

							// Trigger the click event.
							next.trigger( 'click' );
						} else {
							// Get the previous.
							previous = dots.filter( '.active' ).prev();

							// Check if it was not found.
							if ( ! previous.length ) {
								// Get the previous.
								previous = dots.last();
							}

							// Trigger the click event.
							previous.trigger( 'click' );
						}

					}
				);
			},
			refreshScreenEqualHeights: function refreshScreenEqualHeights() {
				// Check if the WooCommerce plugin is enable.
				if ( PsUpsellmaster.attributes.integrations.woo ) {
					// Set equal heights for the products elements.
					PsUpsellmaster.attributes.products.functions.setProductsEqualHeights( '.psupsellmaster.psupsellmaster-product.display-type-carousel' );
				}

			},
			registerAttributes: function registerAttributes() {
				if ( 'undefined' !== typeof psupsellmaster_data_products_carousel ) {
					PsUpsellmasterProductsCarousel.attributes = psupsellmaster_data_products_carousel;
				}

				// Define the products carousel.
				PsUpsellmaster.attributes.productsCarousel = PsUpsellmasterProductsCarousel;
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterProductsCarousel.events.onDocumentReady );
				$( 'body' ).on( 'added_to_cart', PsUpsellmasterProductsCarousel.events.onAddedToCart );
				$( document ).on( 'click', '.psupsellmaster-carousel-nav-prev', PsUpsellmasterProductsCarousel.events.onClickBtnPrevious );
				$( document ).on( 'click', '.psupsellmaster-carousel-nav-next', PsUpsellmasterProductsCarousel.events.onClickBtnNext );
			},
			registerEventsIntegrations: function registerEventsIntegrations() {
				// Check if the Elementor plugin is active.
				if ( PsUpsellmaster.attributes.integrations.elementor ) {
					// Check if the functions and properties exist.
					if ( window.elementor && window.elementorFrontend && window.elementorFrontend.isEditMode ) {
						// Register the events.
						window.elementor.on( 'change', onChangeElementorEditMode );
					}
				}
			},
			start: function start() {
				// Loop through the carousels.
				$( '.psupsellmaster-owl-carousel' ).each(
					function () {
						var carousel, count, max, settings, type;

						// Get the carousel.
						carousel = $( this );

						// Get the items max.
						max = parseInt( carousel.attr( 'data-products-max' ) );
						max = max && 0 < max ? max : 1;

						// Get the items count.
						count = parseInt( carousel.attr( 'data-products-carousel' ) );
						count = count && 0 < count ? count : 1;
						count = count > max ? max : count;

						// Get the type.
						type = carousel.attr( 'data-type' );

						// Define the settings.
						settings = {
							autoplay: true,
							autoplayHoverPause: true,
							autoplayTimeout: 5000,
							items: max,
							lazyLoad: true,
							loop: true,
							margin: 10,
							nav: false,
							onInitialized: function onInitialized() {
								PsUpsellmasterProductsCarousel.functions.refreshScreenEqualHeights();
							},
							responsive: {
								0: { items: 1 },
								413: { items: ( 'widget' === type ? 1 : ( 2 > count ? count : 2 ) ) },
								768: { items: ( 'widget' === type ? 1 : ( 3 > count ? count : 3 ) ) },
								992: { items: count },
							},
							responsiveClass: true,
						};

						// Start the carousel.
						carousel.owlCarousel( settings );
					}
				);
			},
		},
	};

	PsUpsellmasterProductsCarousel.functions.init();

	PsUpsellmaster.productsCarousel = PsUpsellmasterProductsCarousel;
} )( jQuery );

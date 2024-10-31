/**
 * Admin - Edit Product.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminEditProduct;

	PsUpsellmasterAdminEditProduct = {
		attributes: {},
		events: {
			onChangeSlideToggle: function onChangeSlideToggle( event ) {
				var container, element, selector, target;

				// Get the element.
				element = $( this );

				// Get the container.
				container = element.closest( '.psupsellmaster-fields-container' );

				// Get the selector.
				selector = element.data( 'target-slide-toggle' );

				// Get the target.
				target = container.find( selector );

				// Slide toggle.
				target.slideToggle();
			},
			onClickToggleScores: function onClickToggleScores( event ) {
				// Get the checkbox.
				var checkbox = $( this );

				// Get the container.
				var container = checkbox.closest( '.psupsellmaster-fields-container' );

				// Get the field.
				var field = container.find( '.psupsellmaster-scores-status' );

				// Set the value.
				field.val( checkbox.is( ':checked' ) ? 'enabled' : 'disabled' );
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminEditProduct.functions.registerEvents();
			},
			registerEvents: function registerEvents() {
				$( document ).on( 'change', '.psupsellmaster-field-toggle-scores', PsUpsellmasterAdminEditProduct.events.onClickToggleScores );
				$( document ).on( 'change', '.psupsellmaster-trigger-slide-toggle', PsUpsellmasterAdminEditProduct.events.onChangeSlideToggle );
			},
		},
	};

	PsUpsellmasterAdminEditProduct.functions.init();
} )( jQuery );

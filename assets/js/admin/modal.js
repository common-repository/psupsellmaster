/**
 * Admin - Modal.
 *
 * @package PsUpsellMaster.
 */

var PsUpsellmasterAdminModal;

( function ( $ ) {
	PsUpsellmasterAdminModal = {
		attributes: {},
		events: {
			onClickTriggerCloseModal: function onClickTriggerCloseModal( event ) {
				event.preventDefault();

				var element, target;

				// Get the element.
				element = $( this );

				// Get the target.
				target = $( element.data( 'target' ) );

				// If the target is not defined, get the closest modal.
				if ( 0 === target.length ) {
					// Set the target.
					target = element.closest( '.psupsellmaster-modal' );
				}

				// Trigger the before event.
				element.trigger( 'before-close.psupsellmaster.modal' );

				// Close the modal.
				PsUpsellmasterAdminModal.functions.closeModal( target );

				// Trigger the after event.
				element.trigger( 'after-close.psupsellmaster.modal' );
			},
			onClickTriggerOpenModal: function onClickTriggerOpenModal( event ) {
				event.preventDefault();

				var element, target;

				// Get the element.
				element = $( this );

				// Get the target.
				target = $( element.data( 'target' ) );

				// Trigger the before event.
				element.trigger( 'before-open.psupsellmaster.modal' );

				// Open the modal.
				PsUpsellmasterAdminModal.functions.openModal( target );

				// Trigger the after event.
				element.trigger( 'after-open.psupsellmaster.modal' );
			},
		},
		functions: {
			closeModal: function closeModal( target ) {
				var backdrop, body;

				// Get the body.
				body = $( 'body' );

				// Get the backdrop.
				backdrop = $( '.psupsellmaster-modal-backdrop' );

				// If the target is not defined, get all open modals.
				if ( ! target || 0 === target.length ) {
					// Set the target.
					target = body.find( '.psupsellmaster-modal.psupsellmaster-show' );
				}

				// Trigger the before event.
				target.trigger( 'before-close.psupsellmaster.modal' );

				// Remove classes.
				body.removeClass( 'psupsellmaster-modal-open' );
				target.removeClass( 'psupsellmaster-show' );
				backdrop.removeClass( 'psupsellmaster-show' );

				// Hide elements.
				target.hide();
				backdrop.hide();

				// Trigger the after event.
				target.trigger( 'after-close.psupsellmaster.modal' );
			},
			init: function init() {
				PsUpsellmasterAdminModal.functions.registerEvents();
			},
			openModal: function openModal( target ) {
				var backdrop, body;

				// Get the body.
				body = $( 'body' );

				// Get the backdrop.
				backdrop = $( '.psupsellmaster-modal-backdrop' );

				// Trigger the before event.
				target.trigger( 'before-open.psupsellmaster.modal' );

				// Add classes.
				body.addClass( 'psupsellmaster-modal-open' );
				target.addClass( 'psupsellmaster-show' );
				backdrop.addClass( 'psupsellmaster-show' );

				// Show elements.
				backdrop.show();
				target.show();

				// Trigger the after event.
				target.trigger( 'after-open.psupsellmaster.modal' );
			},
			registerEvents: function registerEvents() {
				$( document ).on( 'click', '.psupsellmaster-trigger-open-modal', PsUpsellmasterAdminModal.events.onClickTriggerOpenModal );
				$( document ).on( 'click', '.psupsellmaster-trigger-close-modal', PsUpsellmasterAdminModal.events.onClickTriggerCloseModal );
			},
		},
	};

	PsUpsellmasterAdminModal.functions.init();
} )( jQuery );

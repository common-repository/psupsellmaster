/**
 * Admin - Newsletter.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminNewsletter;

	// Set the object.
	PsUpsellmasterAdminNewsletter = {
		attributes: {},
		events: {
			onDocumentReady: function onDocumentReady() {
				// Maybe open the modal.
				PsUpsellmasterAdminNewsletter.functions.maybeOpenModal();
			},
			onSubmitNewsletter: function onSubmitNewsletter( event ) {
				event.preventDefault();

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-newsletter' );

				// Get the email.
				var email = modal.find( '.psupsellmaster-modal-field' ).val();

				// Set the data.
				var data = {
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					action: 'psupsellmaster_ajax_newsletter_subscribe',
					email: email,
				};

				// Get the spinner.
				var spinner = modal.find( '.psupsellmaster-backdrop-spinner' );

				// Show the spinner.
				spinner.show();

				// Send the request.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'POST',
						dataType: 'JSON',
						data: data,
						success: function () {
							// Hide the form.
							$( '.psupsellmaster-modal-form' ).hide();

							// Show the success message.
							$( '.psupsellmaster-modal-ajax-success' ).show();

							// Auto-close the modal after 5 seconds.
							window.setTimeout(
								function () {
									// Close the modal.
									PsUpsellmasterAdminModal.functions.closeModal( modal );

									// Hide the notice.
									$( '.psupsellmaster-modal-ajax-success' ).hide();

									// Show the form.
									$( '.psupsellmaster-modal-form' ).show();
								},
								5000
							);
						},
						error: function ( xhr, status, error ) {
							// Get the notice.
							var notice = $( '.psupsellmaster-modal-ajax-error' );

							// Show the notice.
							notice.show();

							// Auto-hide the notice after 3 seconds.
							setTimeout(
								function () {
									// Hide the notice.
									notice.hide();
								},
								3000
							);
						},
						complete: function ( xhr, status ) {
							// Hide the spinner.
							spinner.hide();
						},
					}
				);
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminNewsletter.functions.registerEvents();
			},
			maybeOpenModal: function maybeOpenModal() {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-newsletter' );

				// Check if the modal does not exist.
				if ( 0 === modal.length ) {
					return;
				}

				// Get the auto.
				var auto = 'true' === modal.attr( 'data-auto' );

				// Check the auto.
				if ( ! auto ) {
					return;
				}

				// Trigger a function after 5 seconds.
				window.setTimeout(
					function () {
						// Open the modal.
						PsUpsellmasterAdminModal.functions.openModal( modal );
					},
					5000
				);
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminNewsletter.events.onDocumentReady );
				$( document ).on( 'submit', '#psupsellmaster-modal-newsletter .psupsellmaster-modal-form', PsUpsellmasterAdminNewsletter.events.onSubmitNewsletter );
			},
		},
	};

	// Init.
	PsUpsellmasterAdminNewsletter.functions.init();
} )( jQuery );

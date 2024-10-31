/**
 * Admin - Wizard.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminWizard;

	PsUpsellmasterAdminWizard = {
		attributes: {},
		events: {
			onClickButton: function onClickButton( event ) {
				// Get the button.
				var button = $( this );

				// Get the form.
				var form = button.closest( '.psupsellmaster-wizard-form' );

				// Get the spinner.
				var spinner = form.find( '.psupsellmaster-backdrop-spinner' );

				// Show the spinner.
				spinner.show();
			},
			onDocumentReady: function onDocumentReady() {
				// Start the pikaday fields.
				PsUpsellmasterAdminWizard.functions.startPikadayFields();

				// Check the statuses.
				PsUpsellmasterAdminWizard.functions.checkStatuses();
			},
			onSubmitForm: function onSubmitForm( event ) {
				event.preventDefault();

				// Get the form.
				var form = $( this );

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_save_wizard',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					data: form.serialize(),
				};

				// Get the spinner.
				var spinner = form.find( '.psupsellmaster-backdrop-spinner' );

				// Show the spinner.
				spinner.show();

				// Send the request.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'POST',
						dataType: 'JSON',
						data: data,
						success: function ( response ) {
							// Check if it should redirect.
							if ( response.redirect ) {
								// Redirect to the url.
								window.location = response.redirect;

								// Otherwise...
							} else {
								// Hide the spinner.
								spinner.hide();
							}
						},
						error: function ( xhr, status, error ) {
							// Hide the spinner.
							spinner.hide();
						},
						complete: function ( xhr, status ) {},
					}
				);
			},
		},
		functions: {
			checkStatuses: function checkStatuses() {
				// Get the step.
				var step = $( '.psupsellmaster-wizard-step.psupsellmaster-step-summary' );

				// Check the step.
				if ( ! step.length ) {
					return;
				}

				// Get the body.
				var body = step.find( '.psupsellmaster-step-body' );

				// Get the has pending.
				var hasPending = !! body.find( '.psupsellmaster-item[data-status="pending"]' ).length;

				// Check if there is at least one item still pending.
				if ( hasPending ) {
					PsUpsellmasterAdminWizard.functions.getStatuses();
				}
			},
			getStatuses: function getStatuses() {
				// Get the body.
				var body = $( '.psupsellmaster-step-body' );

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_get_setup_wizard_statuses',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
				};

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
						// Check the response.
						if ( response.success && response.statuses ) {
							// Check the scores.
							if ( response.statuses.scores ) {
								// Get the item.
								var item = body.find( '.psupsellmaster-item[data-key="scores"]' );
	
								// Check the html.
								if ( response.statuses.scores.html ) {
									// Set the item html.
									item.html( response.statuses.scores.html );
								}
	
								// Check the plain.
								if ( response.statuses.scores.plain ) {
									// Set the item status.
									item.attr( 'data-status', response.statuses.scores.plain );
								}

								// Set the button.
								PsUpsellmasterAdminWizard.functions.setButtonStartUpselling();
							}
						}

						// Set a timeout to check the statuses again.
						setTimeout(
							function () {
								// Get the statuses.
								PsUpsellmasterAdminWizard.functions.checkStatuses();
							},
							5000
						);
					}
				);
			},
			init: function init() {
				PsUpsellmasterAdminWizard.functions.registerEvents();
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminWizard.events.onDocumentReady );
				$( document ).on( 'submit', '.psupsellmaster-wizard .psupsellmaster-wizard-form', PsUpsellmasterAdminWizard.events.onSubmitForm );
				$( document ).on( 'click', '.psupsellmaster-wizard .psupsellmaster-step-footer .psupsellmaster-button-link', PsUpsellmasterAdminWizard.events.onClickButton );
			},
			setButtonStartUpselling: function setButtonStartUpselling() {
				// Get the body.
				var body = $( '.psupsellmaster-step-body' );

				// Get the footer.
				var footer = $( '.psupsellmaster-step-footer' );

				// Get the button.
				var button = footer.find( '.psupsellmaster-button-save' );

				// Get the has error.
				var hasError = !! body.find( '.psupsellmaster-item[data-status="error"]' ).length;

				// Check if there is at least one item with an error.
				if ( hasError ) {
					// Disable the button.
					button.prop( 'disabled', true );
				} else {
					// Enable the button.
					button.prop( 'disabled', false );
				}
			},
			startPikadayFields: function startPikadayField() {
				// Get the fields.
				var fields = $( '.psupsellmaster-field-pikaday' );

				// Loop through the fields.
				fields.each(
					function () {
						var field;

						// Get the field.
						field = $( this );

						// Start the pikaday for this field.
						PsUpsellmasterAdminWizard.functions.startPikadayField( field );
					}
				);
			},
			startPikadayField: function startPikadayField( field ) {
				var settings;

				// Set the settings.
				settings = {
					field: field.get( 0 ),
					format: 'YYYY/MM/DD',
				};

				// Start the pikaday.
				new Pikaday( settings );
			},
		},
	};

	// Init.
	PsUpsellmasterAdminWizard.functions.init();
} )( jQuery );

/**
 * Admin - Feedback.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminFeedback;

	// Set the object.
	PsUpsellmasterAdminFeedback = {
		attributes: {},
		events: {
			onChangeCheckableField: function onChangeCheckableField( event ) {
				// Get the field.
				var field = $( this );

				// Get the item.
				var item = field.closest( '.psupsellmaster-item' );

				// Get the list.
				var list = item.closest( '.psupsellmaster-list' );

				// Get the form.
				var form = field.closest( '.psupsellmaster-modal-form' );

				// Check if the field is a checkbox or radio.
				if ( field.is( '.psupsellmaster-checkbox, .psupsellmaster-radio' ) ) {
					// Get the toggle target.
					var toggleTarget = form.find( field.attr( 'data-toggle-target' ) );

					// Check if the checkbox is checked.
					if ( field.is( ':checked' ) ) {
						// Get the items.
						var items = list.children( '.psupsellmaster-item' );

						// Check if the field is a checkbox.
						if ( field.is( '.psupsellmaster-checkbox' ) ) {
							// Check if the checkbox is exclusive.
							if ( field.hasClass( 'psupsellmaster-exclusive' ) ) {
								// Get the checkboxes.
								var checkboxes = items.find( '.psupsellmaster-checkbox' );

								// Loop through the checkboxes.
								checkboxes.not( field ).each( function() {
									// Get the checkbox.
									var checkbox = $( this );

									// Check if the item matches.
									if ( checkbox.closest( '.psupsellmaster-list' ).is( list ) ) {
										// Uncheck the checkbox.
										checkbox.prop( 'checked', false );
									}
								} );

								// Otherwise...
							} else {
								// Get the checkboxes.
								var checkboxes = items.find( '.psupsellmaster-checkbox.psupsellmaster-exclusive' );

								// Loop through the checkboxes.
								checkboxes.each( function() {
									// Get the checkbox.
									var checkbox = $( this );

									// Check if the item matches.
									if ( checkbox.closest( '.psupsellmaster-list' ).is( list ) ) {
										// Uncheck the checkbox.
										checkbox.prop( 'checked', false );
									}
								} );
							}

							// Otherwise...
						} else {
							// Get the radios.
							var radios = form.find( '[name="' + field.attr( 'name' ) + '"]' );

							// Loop through the radios.
							radios.each( function() {
								// Get the radio.
								var radio = $( this );

								// Hide the toggle target.
								form.find( radio.attr( 'data-toggle-target' ) ).hide();
							} );
						}

						// Show the toggle target.
						toggleTarget.css( 'display', toggleTarget.attr( 'data-display' ) );

						// Set the focus.
						form.find( field.attr( 'data-focus-target' ) ).first().focus();

						// Otherwise...
					} else {
						// Hide the toggle target.
						toggleTarget.hide();
					}

					// Get the status.
					var status = PsUpsellmasterAdminFeedback.functions.getFormStatus();
	
					// Check the status.
					if ( 'invalid' === status ) {
						// Validate the form.
						PsUpsellmasterAdminFeedback.functions.validateForm();
					}
				}
			},
			onClickButtonDeactivate: function onClickButtonDeactivate( event ) {
				// Get the button.
				var button = $( this );

				// Get the row.
				var row = button.closest( 'tr' );

				// Get the plugin.
				var plugin = row.attr( 'data-plugin' );

				// Check the plugin.
				if ( ! plugin.includes( '/psupsellmaster.php' ) ) {
					return;
				}

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Check if the modal does not exist.
				if ( 0 === modal.length ) {
					return;
				}

				// Prevent default.
				event.preventDefault();

				// Open the modal.
				PsUpsellmasterAdminModal.functions.openModal( modal );
			},
			onClickButtonSkip: function onClickButtonSkip( event ) {
				event.preventDefault();

				// Show the spinner.
				PsUpsellmasterAdminFeedback.functions.showBackdropSpinner();

				// Deactivate the plugin.
				PsUpsellmasterAdminFeedback.functions.deactivate();
			},
			onClickButtonSubmit: function onClickButtonSubmit( event ) {
				// Prevent default.
				event.preventDefault();

				// Maybe start the form.
				PsUpsellmasterAdminFeedback.functions.maybeStartForm();

				// Validate the form.
				if ( 'invalid' === PsUpsellmasterAdminFeedback.functions.validateForm() ) {
					// Stop the submission.
					return false;
				}

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Set the request data.
				var requestData = {
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					action: 'psupsellmaster_ajax_handle_feedback',
					form: form.serialize(),
				};

				// Show the spinner.
				PsUpsellmasterAdminFeedback.functions.showBackdropSpinner();

				// Send the request.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'POST',
						dataType: 'JSON',
						data: requestData,
						complete: function ( xhr, status ) {
							// Deactivate the plugin.
							PsUpsellmasterAdminFeedback.functions.deactivate();
						},
					}
				);
			},
			onCloseModal: function onCloseModal( event ) {
				// Set the status.
				PsUpsellmasterAdminFeedback.functions.resetForm();

				// Maybe switch the primary button.
				PsUpsellmasterAdminFeedback.functions.maybeSwitchPrimaryButton();
			},
			onInputNonZeroFloatAmount: function onInputNonZeroFloatAmount( event ) {
				// Get the target element.
				var targetElement = $( event.target );

				// Get the target value.
				var targetValue = targetElement.val();

				// Sanitize the target value.
				targetValue = parseFloat( targetValue ) || '';
				targetValue = targetValue.toString();

				// Set the sanitized value.
				targetElement.val( targetValue );
			},
			onInputNonZeroIntegerAmount: function onInputNonZeroIntegerAmount( event ) {
				// Get the target element.
				var targetElement = $( event.target );

				// Get the target value.
				var targetValue = targetElement.val();

				// Sanitize the target value.
				targetValue = parseInt( targetValue ) || '';
				targetValue = targetValue.toString();

				// Set the sanitized value.
				targetElement.val( targetValue );
			},
			onInputTextField: function onInputTextField( event ) {
				// Maybe start the form.
				PsUpsellmasterAdminFeedback.functions.maybeStartForm();

				// Get the status.
				var status = PsUpsellmasterAdminFeedback.functions.getFormStatus();

				// Check the status.
				if ( 'invalid' === status ) {
					// Validate the form.
					PsUpsellmasterAdminFeedback.functions.validateForm();
				}
			},
		},
		functions: {
			deactivate: function deactivate() {
				// Get the anchor.
				var anchor = $( '#the-list tr.active[data-plugin*="/psupsellmaster.php"] .row-actions .deactivate a' );

				// Get the href attribute.
				var href = anchor.attr( 'href' );

				// Check if the href attribute was found.
				if ( href ) {
					// Set the location.
					window.location = href;
				}
			},
			getFormStatus: function getFormStatus() {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Get the status.
				var status = form.attr( 'data-status' );

				// Return the status.
				return status;
			},
			init: function init() {
				PsUpsellmasterAdminFeedback.functions.registerEvents();
			},
			maybeStartForm: function maybeStartForm() {
				// Get the status.
				var status = PsUpsellmasterAdminFeedback.functions.getFormStatus();

				// Check the status.
				if ( ! status ) {
					// Set the status.
					PsUpsellmasterAdminFeedback.functions.setFormStatus( 'started' );

					// Maybe switch the primary button.
					PsUpsellmasterAdminFeedback.functions.maybeSwitchPrimaryButton();
				}
			},
			maybeSwitchPrimaryButton: function maybeSwitchPrimaryButton() {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Get the cancel button.
				var cancelButton = form.find( '.psupsellmaster-button-cancel' );

				// Get the submit button.
				var submitButton = form.find( '.psupsellmaster-button-submit' );

				// Get the status.
				var status = PsUpsellmasterAdminFeedback.functions.getFormStatus();

				// Check the status.
				if ( status ) {
					// Switch the classes.
					cancelButton.addClass( 'button-secondary' );
					cancelButton.removeClass( 'button-primary' );

					// Switch the classes.
					submitButton.addClass( 'button-primary' );
					submitButton.removeClass( 'button-secondary' );

					// Otherwise...
				} else {
					// Switch the classes.
					cancelButton.addClass( 'button-primary' );
					cancelButton.removeClass( 'button-secondary' );

					// Switch the classes.
					submitButton.addClass( 'button-secondary' );
					submitButton.removeClass( 'button-primary' );
				}
			},
			registerEvents: function registerEvents() {
				$( document ).on( 'change', '#psupsellmaster-modal-feedback .psupsellmaster-field.psupsellmaster-checkable', PsUpsellmasterAdminFeedback.events.onChangeCheckableField );
				$( document ).on( 'click', '#the-list .row-actions [id^="deactivate-"]', PsUpsellmasterAdminFeedback.events.onClickButtonDeactivate );
				$( document ).on( 'click', '#psupsellmaster-modal-feedback .psupsellmaster-button-skip', PsUpsellmasterAdminFeedback.events.onClickButtonSkip );
				$( document ).on( 'input', '#psupsellmaster-modal-feedback .psupsellmaster-field.psupsellmaster-text', PsUpsellmasterAdminFeedback.events.onInputTextField );
				$( document ).on( 'input', '#psupsellmaster-modal-feedback .psusellmaster-non-zero-float', PsUpsellmasterAdminFeedback.events.onInputNonZeroFloatAmount );
				$( document ).on( 'input', '#psupsellmaster-modal-feedback .psusellmaster-non-zero-integer', PsUpsellmasterAdminFeedback.events.onInputNonZeroIntegerAmount );
				$( document ).on( 'submit', '#psupsellmaster-modal-feedback .psupsellmaster-modal-form', PsUpsellmasterAdminFeedback.events.onClickButtonSubmit );
				$( document ).on( 'after-close.psupsellmaster.modal', '#psupsellmaster-modal-feedback', PsUpsellmasterAdminFeedback.events.onCloseModal );
			},
			resetForm: function resetForm( status ) {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Remove the attribute.
				form.removeAttr( 'data-status' );

				// Get the sections.
				var sections = modal.find( '.psupsellmaster-section' );

				// Remove the class.
				sections.removeClass( 'psupsellmaster-invalid' );

				// Get the inputs.
				var inputs = modal.find( '.psupsellmaster-form-input' );

				// Remove the class.
				inputs.removeClass( 'psupsellmaster-invalid' );

				// Get the hidden.
				var hidden = modal.find( '.psupsellmaster-hidden' );

				// Hide it.
				hidden.hide();

				// Uncheck fields.
				modal.find( '.psupsellmaster-checkbox, .psupsellmaster-radio' ).prop( 'checked', false );

				// Clear fields.
				modal.find( '.psupsellmaster-field:not(.psupsellmaster-checkbox, .psupsellmaster-radio)' ).val( '' );
			},
			setFormStatus: function setFormStatus( status ) {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Set the attribute.
				form.attr( 'data-status', status );
			},
			showBackdropSpinner: function showBackdropSpinner() {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the spinner.
				var spinner = modal.find( '.psupsellmaster-backdrop-spinner' );

				// Show the spinner.
				spinner.show();
			},
			validateForm: function validateForm() {
				// Set the status.
				var status = 'submitted';

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-feedback' );

				// Get the form.
				var form = modal.find( '.psupsellmaster-modal-form' );

				// Get the sections.
				var sections = form.find( '.psupsellmaster-section' );

				// Loop through the sections.
				sections.each( function() {
					// Get the section.
					var section = $( this );

					// Set the section status.
					var sectionStatus = 'valid';

					// Get the fields.
					var fields = section.find( '.psupsellmaster-field' );

					// Loop through the fields.
					fields.each( function() {
						// Get the field.
						var field = $( this );

						// Set the status.
						var fieldStatus = 'valid';

						// Check if the field is required and is visible.
						if ( field.hasClass( 'psupsellmaster-required' ) && field.is( ':visible' ) ) {
							// Set the status.
							fieldStatus = 'invalid';

							// Check if the field is a text field.
							if ( field.hasClass( 'psupsellmaster-text' ) ) {
								// Get the value.
								var value = field.val();

								// Check if the value is not empty.
								if ( value ) {
									// Set the status.
									fieldStatus = 'valid';
								}

								// Otherwise...
							} else {
								// Set the checkable.
								var checkable = field.hasClass( 'psupsellmaster-checkbox' ) || field.hasClass( 'psupsellmaster-radio' );

								// Check if the field is checkable.
								if ( checkable ) {
									// Check if the field is checked.
									if ( field.is( ':checked' ) ) {
										// Set the status.
										fieldStatus = 'valid';

										// Otherwise...
									} else {
										// Get the group.
										var group = section.find( '[name="' + field.attr( 'name' ) + '"]' );

										// Check if the group is checked.
										if ( group.is( ':checked' ) ) {
											// Set the status.
											fieldStatus = 'valid';
										}
									}
								}
							}
						}

						// Get the container.
						var input = field.closest( '.psupsellmaster-form-input' );

						// Check the status.
						if ( 'valid' === fieldStatus ) {
							// Remove the class.
							input.removeClass( 'psupsellmaster-invalid' );

							// Otherwise...
						} else {
							// Add the class.
							input.addClass( 'psupsellmaster-invalid' );

							// Set the status.
							sectionStatus = 'invalid';
						}
					} );

					// Check the status.
					if ( 'valid' === sectionStatus ) {
						// Remove the class.
						section.removeClass( 'psupsellmaster-invalid' );

						// Otherwise...
					} else {
						// Add the class.
						section.addClass( 'psupsellmaster-invalid' );

						// Set the status.
						status = 'invalid';
					}
				} );

				// Set the status.
				PsUpsellmasterAdminFeedback.functions.setFormStatus( status );

				// Return the status.
				return status;
			},
		},
	};

	// Init.
	PsUpsellmasterAdminFeedback.functions.init();
} )( jQuery );

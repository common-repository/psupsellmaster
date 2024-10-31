/**
 * Admin - Campaigns - Templates.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminCampaignsTemplates;

	PsUpsellmasterAdminCampaignsTemplates = {
		attributes: {},
		events: {
			onClickBtnDeleteTemplate: function onClickBtnDeleteTemplate( event ) {
				event.preventDefault();

				// Check the confirm.
				if ( ! confirm( PsUpsellmasterAdminCampaignsTemplates.data.texts.delete_item_confirm ) ) {
					// Return false.
					return false;
				}

				// Get the button.
				var button = $( this );

				// Get the modal.
				var modal = button.closest( '.psupsellmaster-modal' );

				// Get the ajax container.
				var ajax = modal.find( '.psupsellmaster-modal-ajax-container' );

				// Get the loader.
				var loader = modal.find( '.psupsellmaster-modal-loader' );

				// Get the id.
				var id = button.attr( 'data-template' );

				// Remove the ajax html.
				ajax.html( '' );

				// Show the loader.
				loader.show();

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_delete_campaign_templates',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					ids: new Array( id ),
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {},
						success: function ( response ) {},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {
							// Load the campaign templates.
							PsUpsellmasterAdminCampaignsTemplates.functions.loadCampaignTemplates();
						},
					}
				);
			},
			onClickBtnNewBlankCampaign: function onClickBtnNewBlankCampaign( event ) {
				// Get the button.
				var button = $( this );

				// Get the modal.
				var modal = button.closest( '.psupsellmaster-modal' );

				// Close the modal.
				PsUpsellmaster.functions.closeModal( modal );
			},
			onClickBtnNewCampaign: function onClickBtnNewCampaign( event ) {
				event.preventDefault();

				// Open the modal.
				PsUpsellmasterAdminCampaignsTemplates.functions.openModalNewCampaign();
			},
			onClickBtnNewCampaignFromTemplate: function onClickBtnNewCampaignFromTemplate( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the modal.
				var modal = button.closest( '.psupsellmaster-modal' );

				// Get the ajax container.
				var ajax = modal.find( '.psupsellmaster-modal-ajax-container' );

				// Get the loader.
				var loader = modal.find( '.psupsellmaster-modal-loader' );

				// Get the template.
				var template = button.attr( 'data-template' );

				// Remove the ajax html.
				ajax.html( '' );

				// Show the loader.
				loader.show();

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_create_campaign_from_template',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					template: template,
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {},
						success: function ( response ) {
							// Check the response.
							if ( response.html ) {
								// Set the html.
								ajax.html( response.html );

								// Check the response.
							} else if ( response.redirect ) {
								// Redirect to the url.
								window.open( response.redirect, '_blank' );

								// Close the modal.
								PsUpsellmaster.functions.closeModal( modal );
							}
						},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {
							// Hide the loader.
							loader.hide();
						}
					}
				);
			},
			onClickBtnSaveAsTemplate: function onClickBtnSaveAsTemplate( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-save-template' );

				// Get the fields.
				var fields = modal.find( '.psupsellmaster-modal-fields' );

				// Get the ajax container.
				var ajax = modal.find( '.psupsellmaster-modal-ajax-container' );

				// Get the id.
				var id = button.attr( 'data-campaign-id' );

				// Get the field id.
				var fieldId = modal.find( '.psupsellmaster-field-campaign-id' );

				// Get the field title.
				var fieldTitle = modal.find( '.psupsellmaster-field-template-title' );

				// Get the button save.
				var buttonSave = modal.find( '.psupsellmaster-btn-save-as-template-confirm' );

				// Set the id.
				fieldId.val( id );

				// Set the title.
				fieldTitle.val( '' );

				// Enable the save button.
				buttonSave.prop( 'disabled', false );

				// Hide the ajax container.
				ajax.hide();

				// Show the fields.
				fields.show();

				// Open the modal.
				PsUpsellmaster.functions.openModal( modal );

				// Focus on the title field.
				fieldTitle.trigger( 'focus' );
			},
			onSubmitFormSaveTemplate: function onSubmitFormSaveTemplate( event ) {
				event.preventDefault();

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-save-template' );

				// Get the fields.
				var fields = modal.find( '.psupsellmaster-modal-fields' );

				// Get the ajax container.
				var ajax = modal.find( '.psupsellmaster-modal-ajax-container' );

				// Get the loader.
				var loader = modal.find( '.psupsellmaster-modal-loader' );

				// Get the button save.
				var buttonSave = modal.find( '.psupsellmaster-btn-save-as-template-confirm' );

				// Get the id.
				var id = modal.find( '.psupsellmaster-field-campaign-id' ).val();

				// Get the title.
				var title = modal.find( '.psupsellmaster-field-template-title' ).val();

				// Disable the save button.
				buttonSave.prop( 'disabled', true );

				// Hide the fields.
				fields.hide();

				// Show the loader.
				loader.show();

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_save_campaign_as_template',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					id: id,
					title: title,
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {},
						success: function ( response ) {},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {
							// Hide the loader.
							loader.hide();

							// Check the response.
							if ( xhr.responseJSON && xhr.responseJSON.html ) {
								// Set the html.
								ajax.html( xhr.responseJSON.html );

								// Show the ajax container.
								ajax.show();
							}

							// Delay further actions.
							setTimeout(
								function () {
									// Close the modal.
									PsUpsellmaster.functions.closeModal( modal );
								},
								1500
							);
						},
					}
				);
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminCampaignsTemplates.functions.registerAttributes();
				PsUpsellmasterAdminCampaignsTemplates.functions.registerEvents();
			},
			loadCampaignTemplates: function loadCampaignTemplates() {
				// Get the modal.
				var modal = $( '#psupsellmaster-modal-new-campaign' );

				// Get the body.
				var body = modal.find( '.psupsellmaster-modal-body' );

				// Get the loader.
				var loader = body.find( '.psupsellmaster-modal-loader' );

				// Get the ajax container.
				var ajax = body.find( '.psupsellmaster-modal-ajax-container' );

				// Remove the ajax html.
				ajax.html( '' );

				// Show the loader.
				loader.show();

				// Set the data.
				var data = new Object();

				// Set the action.
				data.action = 'psupsellmaster_ajax_get_campaign_templates';

				// Set the nonce.
				data.nonce = PsUpsellmaster.attributes.ajax.nonce;

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
						if ( response.templates && 0 !== response.templates.length ) {
							// Set the ajax html.
							ajax.html( response.templates );
						}
					}
				).always(
					function () {
						// Set the popups lock to false.
						PsUpsellmaster.attributes.popups.lock = false;

						// Hide the loader.
						loader.hide();
					}
				);
			},
			openModalNewCampaign: function openModalNewCampaign() {
				// Check if the popups lock is true.
				if ( true === PsUpsellmaster.attributes.popups.lock ) {
					return false;
				}

				// Get the modal.
				var modal = $( '#psupsellmaster-modal-new-campaign' );

				// Load the campaign templates.
				PsUpsellmasterAdminCampaignsTemplates.functions.loadCampaignTemplates();

				// Open the modal.
				PsUpsellmaster.functions.openModal( modal );
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_campaigns_templates ) {
					// Set the attributes.
					PsUpsellmasterAdminCampaignsTemplates.data = psupsellmaster_admin_data_campaigns_templates;
				}
			},
			registerEvents: function registerEvents() {
				$( document ).on( 'click', '.psupsellmaster-btn-delete-template', PsUpsellmasterAdminCampaignsTemplates.events.onClickBtnDeleteTemplate );
				$( document ).on( 'click', '.psupsellmaster-btn-new-blank-campaign', PsUpsellmasterAdminCampaignsTemplates.events.onClickBtnNewBlankCampaign );
				$( document ).on( 'click', '.psupsellmaster-btn-new-campaign', PsUpsellmasterAdminCampaignsTemplates.events.onClickBtnNewCampaign );
				$( document ).on( 'click', '.psupsellmaster-btn-new-campaign-from-template', PsUpsellmasterAdminCampaignsTemplates.events.onClickBtnNewCampaignFromTemplate );
				$( document ).on( 'click', '.psupsellmaster-btn-save-as-template', PsUpsellmasterAdminCampaignsTemplates.events.onClickBtnSaveAsTemplate );
				$( document ).on( 'submit', '#psupsellmaster-modal-save-template', PsUpsellmasterAdminCampaignsTemplates.events.onSubmitFormSaveTemplate );
			},
		},
	};

	PsUpsellmasterAdminCampaignsTemplates.functions.init();
} )( jQuery );

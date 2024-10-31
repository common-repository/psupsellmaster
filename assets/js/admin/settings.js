/**
 * Admin - Settings.
 *
 * @package PsUpsellMaster.
 */

( function ( $, undefined ) {
	var PsUpsellmasterAdminSettings;

	PsUpsellmasterAdminSettings = {
		attributes: {},
		events: {
			onClickBtnClearResults: function onClickBtnClearResults( event ) {
				var body;

				body = $( 'body' );

				body.addClass( 'psupsellmaster-modal-open' );
				body.addClass( 'psupsellmaster-modal-clear-results' );
			},
			onClickBtnClearResultsCancel: function onClickBtnClearResultsCancel( event ) {
				event.preventDefault();

				// Close the thickbox modal.
				tb_remove();
			},
			onClickSettingsTabLink: function onClickSettingsTabLink( event ) {
				// Get the link.
				var link = $( this );

				// Get the tab.
				var tab = link.closest( '.psupsellmaster-settings-tab' );

				// Get the tabs.
				var tabs = tab.closest( '#psupsellmaster-settings-tabs' );

				// Check if it is an url tab.
				if ( tab.hasClass( 'psupsellmaster-settings-tab-url' ) ) {
					return;
				}

				// Remove the class.
				tabs.find( '.psupsellmaster-settings-tab' ).removeClass( 'psupsellmaster-selected' );

				// Add the class.
				tab.addClass( 'psupsellmaster-selected' );

				// Get the href.
				var href = link.attr( 'href' );

				// Set the cookie.
				PsUpsellmaster.functions.setCookie( 'psupsellmaster-settings-tab', href, 7 );
			},
			onDocumentReady: function onDocumentReady() {
				// Start the tabs.
				PsUpsellmasterAdminSettings.functions.startTabs();
			},
			onKeyupMaxInt: function onKeyupMaxInt( event ) {
				// Get the input.
				var input = $( this );

				// Get the max.
				var max = input.attr( 'max' );

				// Check if the max does not exist.
				if ( ! max ) {
					return;
				}

				// Get the value.
				var value = input.val();

				// Check if the value is greater than max.
				if ( parseInt( max ) < parseInt( value ) ) {
					// Set the value to max.
					input.val( max );

					// Show the alert.
					alert( PsUpsellmasterAdminSettings.attributes.texts.input_max_int.replace( '%d', max ) );
				}
			},
			onSubmitNewsletter: function onSubmitNewsletter( event ) {
				event.preventDefault();

				// Get the newsletter.
				var newsletter = $( '#psupsellmaster-newsletter' );

				// Get the email.
				var email = newsletter.find( '.psupsellmaster-newsletter-field' ).val();

				// Set the data.
				var data = {
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					action: 'psupsellmaster_ajax_newsletter_subscribe',
					email: email,
				};

				// Get the spinner.
				var spinner = newsletter.find( '.psupsellmaster-backdrop-spinner' );

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
							newsletter.find( '.psupsellmaster-newsletter-form' ).hide();

							// Show the notice.
							newsletter.find( '.psupsellmaster-newsletter-ajax-success' ).show();

							// Auto-hide the newsletter after 3 seconds.
							setTimeout(
								function () {
									// Hide the newsletter.
									newsletter.hide();
								},
								3000
							);

							// Check if this is the lite version.
							if ( PsUpsellmaster.attributes.plugin.is_lite ) {
								// Reload the window.
								window.location.reload();
							}
						},
						error: function ( xhr, status, error ) {
							// Get the notice.
							var notice = $( '.psupsellmaster-newsletter-ajax-error' );

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
			onTbUnload: function onTbUnload() {
				var body;

				body = $( 'body' );

				body.removeClass( 'psupsellmaster-modal-open' );
				body.removeClass( 'psupsellmaster-modal-clear-results' );
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminSettings.functions.registerAttributes();
				PsUpsellmasterAdminSettings.functions.registerEvents();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_settings ) {
					// Set the attributes.
					PsUpsellmasterAdminSettings.attributes = psupsellmaster_admin_data_settings;
				}
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminSettings.events.onDocumentReady );
				$( document ).on( 'click', '.psupsellmaster-settings-tab-link', PsUpsellmasterAdminSettings.events.onClickSettingsTabLink );
				$( document ).on( 'keyup', '#psupsellmaster_number_of_upsell_products', PsUpsellmasterAdminSettings.events.onKeyupMaxInt );
				$( document ).on( 'click', '.psupsellmaster-btn-clear-results-cancel', PsUpsellmasterAdminSettings.events.onClickBtnClearResultsCancel );
				$( document ).on( 'submit', '#psupsellmaster-newsletter .psupsellmaster-newsletter-form', PsUpsellmasterAdminSettings.events.onSubmitNewsletter );
				$( document ).on( 'tb_unload', '#TB_window', PsUpsellmasterAdminSettings.events.onTbUnload );
				$( 'body' ).on( 'click', '.psupsellmaster-btn-clear-results', PsUpsellmasterAdminSettings.events.onClickBtnClearResults );
			},
			startTabs: function startTabs() {
				// Get the tabs.
				var tabs = $( '#psupsellmaster-settings-tabs' );

				// Set the args.
				var args = {};

				// Get the selected tab if any.
				var selected = tabs.find( '.psupsellmaster-settings-tab.psupsellmaster-selected' );

				// Check the selected.
				if ( selected && selected.length ) {
					// Set the args.
					args.active = tabs.find( '.psupsellmaster-settings-tab' ).index( selected );
				}

				// Start the tabs.
				tabs.tabs( args );

				// Unbind the click event from url tabs.
				$( '.psupsellmaster-settings-tab-url .psupsellmaster-settings-tab-link' ).unbind( 'click' );

				// Get the href from the cookie.
				var href = PsUpsellmaster.functions.getCookie( 'psupsellmaster-settings-tab' );

				// Check if the href does not exist.
				if ( ! href ) {
					return;
				}

				// Get the params.
				var params = new URLSearchParams( window.location.href );

				// Check if the view param is set.
				if ( params.has( 'view' ) ) {
					return;
				}

				// Get the tab.
				var tab = tabs.find( '.psupsellmaster-settings-tab-link[href="' + href + '"]' );

				// Check if the tag does not exist.
				if ( ! tab ) {
					return;
				}

				// Show the tab.
				tab.trigger( 'click' );
			},
		},
	};

	PsUpsellmasterAdminSettings.functions.init();
} )( jQuery );

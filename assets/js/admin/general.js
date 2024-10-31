/**
 * Admin - General.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdmin;

	PsUpsellmasterAdmin = {
		events: {
			onClickNoticeDismiss: function onClickNoticeDismiss( event ) {
				jQuery.post(
					PsUpsellmasterAdmin.ajax.url,
					{ action: 'psupsellmaster_review_notice' }
				);
			},
			onDocumentReady: function onDocumentReady() {
				// Start the tooltips.
				PsUpsellmasterAdmin.functions.startTooltips( '.psupsellmaster-help-tip' );
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdmin.functions.registerEvents();
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdmin.events.onDocumentReady );
				$( document ).on( 'click', '#psupsellmaster-review .notice-dismiss', PsUpsellmasterAdmin.events.onClickNoticeDismiss );
			},
			startTooltips: function startTooltips( selector ) {
				// Get the elements.
				var elements = $( selector );

				// Check if there are elements.
				if ( elements.length > 0 ) {
					// Start the tooltips.
					elements.tooltip(
						{
							content: function () {
								return $( this ).prop( 'title' );
							},
							tooltipClass: 'psupsellmaster-ui-tooltip',
							position: {
								my: 'center top',
								at: 'center bottom+10',
								collision: 'flipfit',
							},
							hide: {
								duration: 200,
							},
							show: {
								duration: 200,
							},
						}
					);
				}
			},
		}
	};

	PsUpsellmasterAdmin.functions.init();
} )( jQuery );

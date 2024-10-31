/**
 * Admin - Scores.
 *
 * @package PsUpsellMaster.
 */

var PsUpsellmasterAdminScores;

( function ( $ ) {
	PsUpsellmasterAdminScores = {
		attributes: {},
		events: {
			onClickCloseScoresDetails: function onClickCloseScoresDetails( event ) {
				event.preventDefault();

				$( this ).closest( '.psupsellmaster-tooltip-upsell' ).remove();
			},
			onClickOpenScoresDetails: function onClickOpenScoresDetails( event ) {
				event.preventDefault();

				// Get the element.
				var element = $( this );

				// Get the base product id.
				var baseProductId = element.data( 'base-product-id' );

				// Get the upsell product id.
				var upsellProductId = element.data( 'upsell-product-id' );

				var data = {
					action: 'psupsellmaster_ajax_get_upsell_product_scores',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					base_product_id: baseProductId,
					upsell_product_id: upsellProductId,
				};

				// Make the ajax request to get the scores details.
				$.ajax(
					{
						type: 'get',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {
							$( element ).parent().find( '.psupsellmaster-tooltip-upsell' ).remove();
							$( element ).parent().append( '<div class="psupsellmaster-tooltip-upsell"><span class="spinner is-active"></span></div>' );
						},
						success: function ( response ) {
							$( element ).parent().find( '.psupsellmaster-tooltip-upsell' ).html( response );
						},
						error: function ( xhr, status, error ) {
							$( element ).parent().find( '.psupsellmaster-tooltip-upsell' ).remove();
						},
					}
				);
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminScores.functions.registerEvents();
			},
			getBackgroundProcessStatus: function getBackgroundProcessStatus() {
				// Get the progress container.
				var progress = $( '.psupsellmaster-scores-progress' );

				// Set the data.
				var data = {
					action: 'psupsellmaster_bp_ajax_get_scores_status',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
				};

				// Make the ajax request to get the status.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'post',
						dataType: 'json',
						data: data,
					}
				).done(
					function ( response ) {
						// Check if the data does exist.
						if ( response.success && 'undefined' !== typeof response.html ) {
							// Set the html.
							progress.html( response.html );
						}

						// Check if the response status is not none.
						if ( 'none' !== response.status ) {
							// Set a timeout to get the background process status again.
							setTimeout(
								function () {
									// Get the background process status.
									PsUpsellmasterAdminScores.functions.getBackgroundProcessStatus();
								},
								5000
							);
						}

						// Trigger the event.
						$( document ).trigger( 'psupsellmaster-get-bp-scores-status', response );
					}
				);
			},
			registerEvents: function registerEvents() {
				$( document ).on( 'click', '.psupsellmaster-open-scores-details', PsUpsellmasterAdminScores.events.onClickOpenScoresDetails );
				$( document ).on( 'click', '.psupsellmaster-close-scores-details', PsUpsellmasterAdminScores.events.onClickCloseScoresDetails );
			},
			startBackgroundProcess: function startBackgroundProcess( args = new Object() ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_bp_ajax_enqueue_scores',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
				};

				// Check if the products argument is set.
				if ( args.products ) {
					// Set the data.
					data.products = args.products;
				}

				// Make the ajax request to start the background process.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'post',
						dataType: 'json',
						data: data,
					}
				).done(
					function ( response ) {
						// Get the background process status.
						PsUpsellmasterAdminScores.functions.getBackgroundProcessStatus();
					}
				);
			},
			stopBackgroundProcess: function stopBackgroundProcess() {
				// Set the data.
				var data = {
					action: 'psupsellmaster_bp_ajax_scores_stop',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
				};

				// Make the ajax request to stop the background process.
				$.ajax(
					{
						url: PsUpsellmaster.attributes.ajax.url,
						type: 'post',
						dataType: 'json',
						data: data,
					}
				);
			},
		},
	};

	// Init.
	PsUpsellmasterAdminScores.functions.init();
} )( jQuery );

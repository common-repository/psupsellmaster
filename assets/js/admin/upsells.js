/**
 * Admin - Upsells.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminUpsells;

	PsUpsellmasterAdminUpsells = {
		attributes: {},
		events: {
			onClickBtnApplyBulkAction: function onClickBtnApplyBulkAction( event ) {
				// Set the products.
				var products = new Array();

				// Get the checkboxes.
				var checkboxes = $( '.psupsellmaster_products_select_product:checked' );

				checkboxes.each(
					function () {
						products.push( $( this ).attr( 'data-id' ) );
					}
				);

				if ( 0 === products.length ) {
					alert( psupsellmaster_admin_data_upsells.texts.msg_err_products_not_selected );

					return;
				}

				// Get the action.
				var action = $( '.psupsellmaster_bulk_action' ).val();

				if ( 0 === action.length ) {
					alert( psupsellmaster_admin_data_upsells.texts.msg_err_operation_not_selected );

					return;
				}

				if ( ! ( new Array( 'disable', 'enable', 'recalc' ) ).includes( action ) ) {
					return;
				}

				// Check if the action is recalc.
				if ( 'recalc' === action ) {
					// Check if the user confirms the action.
					if ( ! confirm( psupsellmaster_admin_data_upsells.texts.msg_confirm_recalculate_selected )) {
						return;
					}

					// Start the background process for these products.
					PsUpsellmasterAdminScores.functions.startBackgroundProcess(
						{
							products: products,
						}
					);

					// Otherwise...
				} else {
					// Set the scores status for these products.
					PsUpsellmasterAdminUpsells.functions.setScoresStatus(
						{
							products: products,
							status: 'disable' === action ? 'disabled' : 'enabled',
						}
					);
				}

				checkboxes.each(
					function () {
						$( this ).removeAttr( 'checked' );
					}
				);
			},
			onClickBtnResetDateFilters: function onClickBtnResetDateFilters( event ) {
				$( '#psupsellmaster_date_from' ).val( '' ).trigger( 'change' );
				$( '#psupsellmaster_date_to' ).val( '' ).trigger( 'change' );
			},
			onClickBtnResetFilters: function onClickBtnResetFilters( event ) {
				// Reset the select2.
				PsUpsellmaster.select2.functions.clearAll();

				$( '#psupsellmaster_btn_reset' ).trigger( 'click' );
				$( '#psupsellmaster_products_table' ).DataTable().ajax.url(
					PsUpsellmaster.attributes.ajax.url
					+ '?nonce=' + PsUpsellmaster.attributes.ajax.nonce
					+ '&action=psupsellmaster_admin_ajax_get_base_products'
				).load();
				$( '#psupsellmaster_search' ).val( '' ).trigger( 'search' );
			},
			onClickToggleProductStatus: function onClickToggleProductStatus( event ) {
				event.preventDefault();

				// Get the element.
				var element = $( this );

				// Make the ajax request to toggle the status.
				$.post(
					PsUpsellmaster.attributes.ajax.url,
					{
						action: 'psupsellmaster_scores_set_status',
						nonce: PsUpsellmaster.attributes.ajax.nonce,
						products: new Array( element.attr( 'data-id' ) ),
						status: 'disable' === element.attr( 'data-status' ) ? 'disabled' : 'enabled',
					},
					function ( response ) {
						if ( 'OK' === response.status ) {
							PsUpsellmasterAdminUpsells.instances.datatables.main.ajax.reload();
						} else {
							alert( response.msg );
						}
					}
				);
			},
			onDataTableAjaxData: function onDataTableAjaxData( data, settings ) {
				// Set the data.
				data = $.extend(
					true,
					data,
					{ nonce: PsUpsellmaster.attributes.ajax.nonce }
				);

				// Return the data.
				return data;
			},
			onDataTableAjaxDataSrc: function onDataTableAjaxDataSrc( json ) {
				// Check if the json datatable does not exist.
				if ( ! json.datatable ) {
					return json;
				}

				// Set the json draw.
				json.draw = json.datatable.draw || 0;

				// Set the json records total.
				json.recordsTotal = json.datatable.total || 0;

				// Set the json records filtered.
				json.recordsFiltered = json.datatable.filtered || 0;

				// Return the data.
				return json.datatable.data || json.datatable;
			},
			onDataTableButtonsAction: function onDataTableButtonsAction( event, dt, node, config ) {
				// Get the target.
				var target = $( event.target );

				// Check if the action is recalculate.
				if ( target.hasClass( 'psupsellmaster-btn-recalculate' ) ) {

					if ( ! confirm( psupsellmaster_admin_data_upsells.texts.msg_confirm_recalculate_all ) ) {
						return;
					}

					// Start the background process.
					PsUpsellmasterAdminScores.functions.startBackgroundProcess();

					// Disable the target button.
					target.prop( 'disabled', true ).addClass( 'disabled' );

					// Otherwise, check if the action is abort.
				} else if ( target.hasClass( 'psupsellmaster-btn-abort' ) ) {
					// Stop the background process.
					PsUpsellmasterAdminScores.functions.stopBackgroundProcess();

					// Disable the target button.
					target.prop( 'disabled', true ).addClass( 'disabled' );
				}
			},
			onDataTableCreatedCell: function onDataTableCreatedCell( cell ) {
				$( cell ).addClass( 'psupsellmaster-datatable-col' );
			},
			onDataTableCreatedRow: function onDataTableCreatedRow( row ) {
				$( row ).addClass( 'psupsellmaster-datatable-row' );
			},
			onDataTableInitComplete: function onDataTableInitComplete( event ) {
				// Set the button icons and texts.
				$( '.psupsellmaster-datatable-wrapper .psupsellmaster-btn-abort' ).html( '<i class="fa fa-times "></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_abort );
				$( '.psupsellmaster-datatable-wrapper .psupsellmaster-btn-recalculate' ).html( '<i class="fa fa-calculator "></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_recalculate );
				$( '.psupsellmaster-datatable-wrapper .buttons-copy' ).html( '<i class="fa fa-copy"></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_copy );
				$( '.psupsellmaster-datatable-wrapper .buttons-csv' ).html( '<i class="fa fa-file-csv"></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_csv );
				$( '.psupsellmaster-datatable-wrapper .buttons-excel' ).html( '<i class="fa fa-file-excel"></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_excel );
				$( '.psupsellmaster-datatable-wrapper .buttons-print' ).html( '<i class="fa fa-print"></i>&nbsp;' + PsUpsellmasterAdminUpsells.data.texts.datatable_btn_print );

				$( '#psupsellmaster_bulk_container' ).detach().prependTo( '#psupsellmaster_upsells_wrapper .dt-buttons' );
				$( '#psupsellmaster_bulk_container' ).show();

				$( document ).trigger( 'psupsellmaster-datatable-init-complete' );
				$( document ).off( 'psupsellmaster-datatable-init-complete', PsUpsellmasterAdminUpsells.events.onFirstDataTableInitComplete );
			},
			onDataTableUpdate: function onDataTableUpdate( event, settings, json, xhr ) {
				var date, response;

				// Set the response.
				response = json;

				// Check if the response does exist.
				if ( ! response ) {
					return;
				}

				// Get the date.
				date = response.dates.bp_last_run;

				// Refresh the last run date.
				PsUpsellmasterAdminUpsells.functions.refreshLastRunDate( date );
			},
			onDocumentReady: function onDocumentReady() {

				$( '.psupsellmaster_date' ).each(
					function () {
						var dt          = $( this );
						var date_picker = new Pikaday(
							{
								field: $( this ).get( 0 ),
								format: 'YYYY/MM/DD'
							}
						);
					}
				);

				$( '#psupsellmaster_btn_this_month' ).on(
					'click',
					function () {
						const startOfMonth = moment().startOf( 'month' ).format( 'YYYY/MM/DD' );
						const endOfMonth   = moment().endOf( 'month' ).format( 'YYYY/MM/DD' );
						$( '#psupsellmaster_date_from' ).val( startOfMonth ).trigger( 'change' );
						$( '#psupsellmaster_date_to' ).val( endOfMonth ).trigger( 'change' );
					}
				);

				$( '#psupsellmaster_btn_last_month' ).on(
					'click',
					function () {
						const startOfMonth = moment().subtract( 1, 'months' ).startOf( 'month' ).format( 'YYYY/MM/DD' );
						const endOfMonth   = moment().subtract( 1, 'months' ).endOf( 'month' ).format( 'YYYY/MM/DD' );
						$( '#psupsellmaster_date_from' ).val( startOfMonth ).trigger( 'change' );
						$( '#psupsellmaster_date_to' ).val( endOfMonth ).trigger( 'change' );
					}
				);

				$( '#psupsellmaster_btn_this_year' ).on(
					'click',
					function () {
						const start = moment().startOf( 'year' ).format( 'YYYY/MM/DD' );
						const end   = moment().endOf( 'year' ).format( 'YYYY/MM/DD' );
						$( '#psupsellmaster_date_from' ).val( start ).trigger( 'change' );
						$( '#psupsellmaster_date_to' ).val( end ).trigger( 'change' );
					}
				);

				$( '#psupsellmaster_btn_last_year' ).on(
					'click',
					function () {
						const start = moment().subtract( 1, 'years' ).startOf( 'year' ).format( 'YYYY/MM/DD' );
						const end   = moment().subtract( 1, 'years' ).endOf( 'year' ).format( 'YYYY/MM/DD' );
						$( '#psupsellmaster_date_from' ).val( start ).trigger( 'change' );
						$( '#psupsellmaster_date_to' ).val( end ).trigger( 'change' );
					}
				);

				$( '#psupsellmaster_btn_last_week' ).on(
					'click',
					function () {
						const start = moment().subtract( 1, 'week' ).startOf( 'week' ).format( 'YYYY/MM/DD' );
						const end   = moment().subtract( 1, 'week' ).endOf( 'week' ).format( 'YYYY/MM/DD' );
						$( '#psupsellmaster_date_from' ).val( start ).trigger( 'change' );
						$( '#psupsellmaster_date_to' ).val( end ).trigger( 'change' );
					}
				);

				$( '#psupsellmaster_btn_apply_filters' ).on(
					'click',
					function () {
						var button, container, customTaxonomies;

						// Get the button.
						button = $( this );

						// Get the container.
						container = button.closest( '#psupsellmaster_filters' );

						// Define the custom taxonomies.
						customTaxonomies = new Object();

						// Loop through the fields of custom taxonomy.
						container.find( '.psupsellmaster-field-custom-taxonomy' ).each(
							function () {
								var field, taxonomy, value;

								// Get the field.
								field = $( this );

								// Get the value.
								value = field.val();

								// Get the taxonomy.
								taxonomy = field.data( 'taxonomy' );

								// Set the taxonomy value.
								customTaxonomies[ taxonomy ] = value;
							}
						);

						var filter = {
							'cat': $( '#psupsellmaster_categories' ).val(),
							'tag': $( '#psupsellmaster_tags' ).val(),
							'dtf': ('' + $( '#psupsellmaster_date_from' ).val()).replace( /\//g, '-' ),
							'dtt': ('' + $( '#psupsellmaster_date_to' ).val()).replace( /\//g, '-' ),
							custom_taxonomies: customTaxonomies,
						};

						var new_url = PsUpsellmaster.attributes.ajax.url
								+ '?nonce=' + PsUpsellmaster.attributes.ajax.nonce
								+ '&action=psupsellmaster_admin_ajax_get_base_products'
								+ '&f=' + encodeURIComponent( JSON.stringify( filter ) );

						PsUpsellmasterAdminUpsells.instances.datatables.main.ajax.url( new_url ).load();
					}
				);

				$( '.psupsellmaster_select_all_products' ).on(
					'click',
					function (e) {
						$( '.psupsellmaster_products_select_product' ).trigger( 'click' );
					}
				);

				// Start the datatables.
				PsUpsellmasterAdminUpsells.functions.startDataTables();

				// Start getting the background process status.
				PsUpsellmasterAdminScores.functions.getBackgroundProcessStatus();
			},
			onFirstDataTableInitComplete: function onFirstDataTableInitComplete() {},
			onGetBpScoresStatus: function onGetBpScoresStatus( event, response ) {
				// Get the wrapper.
				var wrapper = $( '#psupsellmaster_upsells_wrapper' );

				// Get the buttons.
				var buttons = wrapper.find( '.dt-buttons' );

				// Check if the response status is none.
				if ( 'none' === response.status ) {
					// Hide the abort button.
					buttons.find( '.psupsellmaster-btn-abort' ).prop( 'disabled', true ).addClass( 'disabled' ).hide();

					// Show the start button.
					buttons.find( '.psupsellmaster-btn-recalculate' ).prop( 'disabled', false ).removeClass( 'disabled' ).show();

					// Check if the previous status is done or stopping (meaning it has ended, since now the status is none).
					if ( ( new Array( 'done', 'stopping' ) ).includes( PsUpsellmasterAdminUpsells.bps.scores.status ) ) {
						// Reload the datatable.
						PsUpsellmasterAdminUpsells.functions.reloadDataTable();
					}

					// Otherwise...
				} else {
					// Check if the response status is running.
					if ( 'running' === response.status ) {
						// Hide the start button.
						buttons.find( '.psupsellmaster-btn-recalculate' ).prop( 'disabled', true ).addClass( 'disabled' ).hide();

						// Show the abort button.
						buttons.find( '.psupsellmaster-btn-abort' ).prop( 'disabled', false ).removeClass( 'disabled' ).show();
					}
				}

				// Set the background process status.
				PsUpsellmasterAdminUpsells.bps.scores.status = response.status;
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminUpsells.functions.registerAttributes();
				PsUpsellmasterAdminUpsells.functions.registerEvents();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_upsells ) {
					// Set the attributes.
					PsUpsellmasterAdminUpsells.data = psupsellmaster_admin_data_upsells;
				}

				// Set the settings.
				PsUpsellmasterAdminUpsells.settings = {
					datatables: {
						main: new Object(),
					},
				};

				// Set the DataTable settings.
				PsUpsellmasterAdminUpsells.settings.datatables.main = {
					ajax: {
						data: PsUpsellmasterAdminUpsells.events.onDataTableAjaxData,
						dataSrc: PsUpsellmasterAdminUpsells.events.onDataTableAjaxDataSrc,
						type: 'POST',
						url: (
							PsUpsellmaster.attributes.ajax.url
							+ '?nonce=' + PsUpsellmaster.attributes.ajax.nonce
							+ '&action=psupsellmaster_admin_ajax_get_base_products'
						),
					},
					buttons: [
						{
							className: 'button button-primary psupsellmaster-btn-recalculate disabled',
							action: PsUpsellmasterAdminUpsells.events.onDataTableButtonsAction,
						},
						{
							className: 'button psupsellmaster-btn-abort disabled',
							action: PsUpsellmasterAdminUpsells.events.onDataTableButtonsAction,
						},
						{
							className: 'button',
							extend: 'copyHtml5',
						},
						{
							className: 'button',
							extend: 'print',
						},
						{
							className: 'button',
							extend: 'csvHtml5',
						},
						{
							className: 'button',
							extend: 'excelHtml5',
						},
					],
					columnDefs: [
						{
							className: 'dt-body-center',
							targets: [ 0, 2 ],
						},
						{
							className: 'dt-body-left',
							targets: [ 1, 3, 4 ],
							width: '25%',
						},
						{
							className: 'dt-body-right',
							targets: 5,
						},
						{
							orderable: false,
							targets: [ 0, 3, 4 ],
						},
						{
							createdCell: PsUpsellmasterAdminUpsells.events.onDataTableCreatedCell,
							targets: '_all',
						},
					],
					createdRow: PsUpsellmasterAdminUpsells.events.onDataTableCreatedRow,
					displayLength: 10,
					dom: 'Bfliptip',
					filter: true,
					initComplete: PsUpsellmasterAdminUpsells.events.onDataTableInitComplete,
					oLanguage: {
						sLengthMenu: PsUpsellmasterAdminUpsells.data.texts.datatable_length,
					},
					order: [ [ 1, 'asc' ] ],
					pageLength: 10,
					lengthMenu: [
						[ 10, 25, 50, 100, 250, 1000 ],
						[ 10, 25, 50, 100, 250, 1000 ],
					],
					paginate: true,
					pagingType: 'full_numbers',
					serverSide: true,
				};

				// Set the instances.
				PsUpsellmasterAdminUpsells.instances = {
					datatables: {
						main: null,
					},
				};

				// Set the background process data.
				PsUpsellmasterAdminUpsells.bps = {
					scores: {
						status: null,
					},
				};
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminUpsells.events.onDocumentReady );
				$( document ).on( 'psupsellmaster-datatable-init-complete', PsUpsellmasterAdminUpsells.events.onFirstDataTableInitComplete );
				$( document ).on( 'xhr.dt', '#psupsellmaster_products_table', PsUpsellmasterAdminUpsells.events.onDataTableUpdate );
				$( document ).on( 'psupsellmaster-get-bp-scores-status', PsUpsellmasterAdminUpsells.events.onGetBpScoresStatus );
				$( document ).on( 'click', '#psupsellmaster_btn_reset_filters', PsUpsellmasterAdminUpsells.events.onClickBtnResetFilters );
				$( document ).on( 'click', '#psupsellmaster_btn_reset', PsUpsellmasterAdminUpsells.events.onClickBtnResetDateFilters );
				$( document ).on( 'click', '.psupsellmaster_enable', PsUpsellmasterAdminUpsells.events.onClickToggleProductStatus );
				$( document ).on( 'click', '.psupsellmaster-btn-apply-bulk-action', PsUpsellmasterAdminUpsells.events.onClickBtnApplyBulkAction );
			},
			refreshLastRunDate: function refreshLastRunDate( date ) {
				var last;

				// Get the last.
				last = $( '.psupsellmaster-bp-last-run-date' );

				// Refresh the date.
				last.html( date );
			},
			reloadDataTable: function reloadDataTable() {
				// Reload the DataTable.
				PsUpsellmasterAdminUpsells.instances.datatables.main.ajax.reload();
			},
			setScoresStatus: function setScoresStatus( args = new Object() ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_scores_set_status',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
				};

				// Check if the products argument is set.
				if ( args.products ) {
					// Set the data.
					data.products = args.products;
				}

				// Check if the status argument is set.
				if ( args.status ) {
					// Set the data.
					data.status = args.status;
				}

				// Get the buttons.
				var buttons = $( '#psupsellmaster_upsells_wrapper .dt-button' );

				buttons.attr( 'disabled', 'disabled' );

				// Make the ajax request.
				$.post(
					PsUpsellmaster.attributes.ajax.url,
					data,
					function ( response ) {
						buttons.removeAttr( 'disabled', 'disabled' );

						PsUpsellmasterAdminUpsells.instances.datatables.main.ajax.reload();
					}
				);
			},
			startDataTables: function startDataTables() {
				// Start new datatable.
				PsUpsellmasterAdminUpsells.instances.datatables.main = $( '#psupsellmaster_products_table' ).DataTable( PsUpsellmasterAdminUpsells.settings.datatables.main );
			},
		},
	};

	PsUpsellmasterAdminUpsells.functions.init();
} )( jQuery );

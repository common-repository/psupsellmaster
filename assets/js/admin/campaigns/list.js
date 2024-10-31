/**
 * Admin - Campaigns - List.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminCampaignsList;

	PsUpsellmasterAdminCampaignsList = {
		attributes: {},
		events: {
			onChangeBulkActions: function onChangeBulkActions( event ) {
				// Get the select.
				var select = $( this );

				// Get the action.
				var action = select.val();

				// Get the wrapper.
				var wrapper = select.closest( '.psupsellmaster-datatable-wrapper' );

				// Get the button.
				var button = wrapper.find( '.psupsellmaster-btn-apply-bulk-action' );

				// Check if the action is not valid.
				if ( ! ( new Array( 'activate', 'deactivate', 'duplicate', 'delete' ) ).includes( action ) ) {
					// Reset the bulk actions.
					PsUpsellmasterAdminCampaignsList.functions.resetBulkActions();

					return false;
				}

				// Enable the button.
				button.prop( 'disabled', false );

				// Remove the disabled class.
				button.removeClass( 'disabled' );
			},
			onChangeCheckRow: function onChangeCheckRow( event ) {
				// Get the checkbox.
				var checkbox = $( this );

				// Get the table.
				var table = checkbox.closest( '.psupsellmaster-datatable' );

				// Get the rows.
				var rows = table.find( '.psupsellmaster-datatable-row' );

				// Get the checkboxes.
				var checkboxes = rows.find( '.psupsellmaster-check-row' );

				// Get the checked checkboxes.
				var checked = checkboxes.filter( ':checked' );

				// Get the check rows checkbox.
				var checkRows = table.find( '.psupsellmaster-check-rows' );

				// Set the checked status.
				checkRows.prop( 'checked', ( checked.length === checkboxes.length ) );
			},
			onChangeCheckRows: function onChangeCheckRows( event ) {
				// Get the checkbox.
				var checkbox = $( this );

				// Get the table.
				var table = checkbox.closest( '.psupsellmaster-datatable' );

				// Get the rows.
				var rows = table.find( '.psupsellmaster-datatable-row' );

				// Get the checkboxes.
				var checkboxes = rows.find( '.psupsellmaster-check-row' );

				// Set the checked status.
				checkboxes.prop( 'checked', checkbox.prop( 'checked' ) );
			},
			onChartJsScalesXTicksCallback: function onChartJsScalesXTicksCallback( value, index, ticks ) {
				// Get the label.
				var label = this.getLabelForValue( value ) || '';

				// Decode html entities.
				label = $( '<textarea/>' ).html( label ).text();

				// Return the label.
				return label;
			},
			onChartJsScalesAmountTicksCallback: function onChartJsScalesAmountTicksCallback( value, index, ticks ) {
				// Set the label.
				var label = PsUpsellmasterAdminCampaignsList.data.texts.currency_symbol;

				// Decode html entities.
				label = $( '<textarea/>' ).html( label ).text();

				// Set the label.
				label += value.toLocaleString();

				// Return the label.
				return label;
			},
			onChartJsSort: function onChartJsSort() {
				return -1;
			},
			onChartJsTooltipLabel: function onChartJsTooltipLabel( context ) {
				// Set the label.
				var label = '';

				// Check the y axis.
				if ( 'earnings' === context.dataset.yAxisID ) {
					// Set the label.
					label = PsUpsellmasterAdminCampaignsList.data.texts.currency_symbol;
				}

				// Get the label.
				label = `${context.dataset.label}: ${label}${context.raw}`;

				// Decode html entities.
				label = $( '<textarea/>' ).html( label ).text();

				// Return the label.
				return label;
			},
			onChartJsTooltipTitle: function onChartJsTooltipTitle( context ) {
				// Get the item.
				var item = context[0] || new Object();

				// Get the title.
				var title = item.label;

				// Decode html entities.
				title = $( '<textarea/>' ).html( title ).text();

				// Return the title.
				return title;
			},
			onClickBtnApplyBulkAction: function onClickBtnApplyBulkAction( event ) {
				// Get the button.
				var button = $( this );

				// Get the wrapper.
				var wrapper = button.closest( '.psupsellmaster-datatable-wrapper' );

				// Get the select.
				var select = wrapper.find( '.psupsellmaster-field-bulk-actions' );

				// Get the action.
				var action = select.val();

				// Check if the action is not valid.
				if ( ! ( new Array( 'activate', 'deactivate', 'duplicate', 'delete' ) ).includes( action ) ) {
					return false;
				}

				// Get the rows.
				var rows = wrapper.find( '.psupsellmaster-datatable-row' );

				// Get the checked checkboxes.
				var checked = rows.find( '.psupsellmaster-check-row:checked' );

				// Check the checked length.
				if ( 0 === checked.length ) {
					// Reset the bulk actions.
					PsUpsellmasterAdminCampaignsList.functions.resetBulkActions();

					// Show the alert.
					alert( PsUpsellmasterAdminCampaignsList.data.texts.bulk_actions_empty );

					// Return false.
					return false;
				}

				// Get the ids.
				var ids = checked.map(
					function () {
						// Get the row.
						var row = $( this ).closest( '.psupsellmaster-datatable-row' );

						// Get the id.
						var id = row.find( '.psupsellmaster-hidden-campaign-id' ).val();

						// Return the id.
						return id;
					}
				).get();

				// Check the action.
				if ( 'activate' === action ) {
					// Set the campaigns status to active.
					PsUpsellmasterAdminCampaignsList.functions.setCampaignsStatus( ids, 'active' );

					// Check the action.
				} else if ( 'deactivate' === action ) {
					// Set the campaigns status to inactive.
					PsUpsellmasterAdminCampaignsList.functions.setCampaignsStatus( ids, 'inactive' );

					// Check the action.
				} else if ( 'duplicate' === action ) {
					// Duplicate the campaigns.
					PsUpsellmasterAdminCampaignsList.functions.duplicateCampaigns( ids );

					// Check the action.
				} else if ( 'delete' === action ) {
					// Check the confirm.
					if ( ! confirm( PsUpsellmasterAdminCampaignsList.data.texts.bulk_actions_delete_confirm ) ) {
						// Reset the bulk actions.
						PsUpsellmasterAdminCampaignsList.functions.resetBulkActions();

						// Return false.
						return false;
					}

					// Delete the campaigns.
					PsUpsellmasterAdminCampaignsList.functions.deleteCampaigns( ids );
				}
			},
			onClickBtnApplyFilters: function onClickBtnApplyFilters( event ) {
				// Apply the filters.
				PsUpsellmasterAdminCampaignsList.functions.applyFilters();
			},
			onClickBtnRefreshDatetimeLeft: function onClickBtnRefreshDatetimeLeft( event ) {
				// Get the button.
				var button = $( this );

				// Get the row.
				var row = button.closest( '.psupsellmaster-datatable-row' );

				// Get the id.
				var id = row.find( '.psupsellmaster-hidden-campaign-id' ).val();

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_get_campaign_datetime_left',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					id: id,
				};

				// Add a class to the button.
				button.addClass( 'psupsellmaster-rotate' );

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {},
						success: function ( response ) {
							if ( 'html' in response ) {
								// Set the html.
								row.find( '.psupsellmaster-datetime-left' ).html( response.html );
							}
						},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {}
					}
				);
			},
			onClickBtnResetFilters: function onClickBtnResetFilters( event ) {
				// Reset the filters.
				PsUpsellmasterAdminCampaignsList.functions.resetFilters();
			},
			onClickBtnToggleFilters: function onClickBtnToggleFilters() {
				// Get the button.
				var button = $( this );

				// Get the section.
				var section = button.closest( '.psupsellmaster-section' );

				// Get the toggle.
				var toggle = section.find( '.psupsellmaster-filters-toggle' );

				// Toggle the filters.
				toggle.toggle();
			},
			onClickRowActionDuplicate: function onClickRowActionDuplicate( event ) {
				event.preventDefault();

				// Get the link.
				var link = $( this );

				// Get the row.
				var row = link.closest( '.psupsellmaster-datatable-row' );

				// Get the id.
				var id = row.find( '.psupsellmaster-hidden-campaign-id' ).val();

				// Duplicate the campaign.
				PsUpsellmasterAdminCampaignsList.functions.duplicateCampaigns( ( new Array( id ) ) );
			},
			onClickRowActionSetStatus: function onClickRowActionSetStatus( event ) {
				event.preventDefault();

				// Get the link.
				var link = $( this );

				// Get the row.
				var row = link.closest( '.psupsellmaster-datatable-row' );

				// Get the id.
				var id = row.find( '.psupsellmaster-hidden-campaign-id' ).val();

				// Get the status.
				var status = link.attr( 'data-status' );

				// Set the campaign status.
				PsUpsellmasterAdminCampaignsList.functions.setCampaignsStatus( ( new Array( id ) ), status );
			},
			onDataTableAjaxData: function onDataTableAjaxData( data, settings ) {
				// Set the data.
				data = $.extend(
					true,
					data,
					{
						action: PsUpsellmasterAdminCampaignsList.actions.main,
						nonce: PsUpsellmaster.attributes.ajax.nonce,
						filters: PsUpsellmasterAdminCampaignsList.settings.datatables.custom.main.ajax.data.filters,
					}
				);

				// Return the data.
				return data;
			},
			onDataTableAjaxDataSrc: function onDataTableAjaxDataSrc( json ) {
				// Check if the datatables does not exist.
				if ( ! json.datatables ) {
					return json;
				}

				// Check if the main datatable does not exist.
				if ( ! json.datatables.main ) {
					return json;
				}

				// Set the json draw.
				json.draw = json.datatables.main.draw || 0;

				// Set the json records total.
				json.recordsTotal = json.datatables.main.total || 0;

				// Set the json records filtered.
				json.recordsFiltered = json.datatables.main.filtered || 0;

				// Return the data.
				return json.datatables.main.data || json.datatables.main;
			},
			onDataTableCreatedCell: function onDataTableCreatedCell( cell ) {
				$( cell ).addClass( 'psupsellmaster-datatable-col' );
			},
			onDataTableCreatedRow: function onDataTableCreatedRow( row ) {
				$( row ).addClass( 'psupsellmaster-datatable-row' );
			},
			onDataTableDrawCallback: function onDataTableDrawCallback() {
				// Reset the bulk actions.
				PsUpsellmasterAdminCampaignsList.functions.resetBulkActions();

				// Reset the checkbox.
				$( '.psupsellmaster-check-rows' ).prop( 'checked', false );
			},
			onDataTableExportFormatBody: function onDataTableExportFormatBody( data, row, column, node ) {
				// Create a temporary DOM element to parse the HTML content.
				var temp = $( '<div>' ).html( data );

				// Find and remove elements with the class.
				temp.find( '.psupsellmaster-ignore-on-export' ).remove();

				// Get the modified HTML content after removal.
				var modified = temp.text();

				// Remove all empty lines (line breaks with no content or only spaces).
				modified = modified.replace( /^\s*\n/gm, '' );

				// Remove leading and trailing spaces.
				modified = modified.trim();

				// Return the modified HTML content.
				return modified;
			},
			onDataTableInitComplete: function onDataTableInitComplete( event ) {
				// Set the icons and texts for the datatable buttons.
				$( '.psupsellmaster-datatable-wrapper .buttons-copy' ).html( '<i class="fa fa-copy"></i>&nbsp;' + PsUpsellmasterAdminCampaignsList.data.texts.datatable_btn_copy );
				$( '.psupsellmaster-datatable-wrapper .buttons-csv' ).html( '<i class="fa fa-file-csv"></i>&nbsp;' + PsUpsellmasterAdminCampaignsList.data.texts.datatable_btn_csv );
				$( '.psupsellmaster-datatable-wrapper .buttons-excel' ).html( '<i class="fa fa-file-excel"></i>&nbsp;' + PsUpsellmasterAdminCampaignsList.data.texts.datatable_btn_excel );
				$( '.psupsellmaster-datatable-wrapper .buttons-print' ).html( '<i class="fa fa-print"></i>&nbsp;' + PsUpsellmasterAdminCampaignsList.data.texts.datatable_btn_print );

				// Get the datatable.
				var datatable = $( '.psupsellmaster-datatable' );

				// Get the wrapper.
				var wrapper = datatable.closest( '.psupsellmaster-datatable-wrapper' );

				// Get the bulk actions.
				var bulkActions = wrapper.find( '.psupsellmaster-bulk-actions' ).detach();

				// Prepend the bulk actions.
				wrapper.find( '.dt-buttons' ).prepend( bulkActions );

				// Show the bulk actions.
				bulkActions.show();

				// Trigger the event.
				$( document ).trigger( 'psupsellmaster-datatable-init-complete' );
			},
			onDataTableUpdate: function onDataTableUpdate( event, settings, json, xhr ) {
				// Set the response.
				var response = json;

				// Check if the response does exist.
				if ( ! response ) {
					return;
				}

				// Start the charts.
				PsUpsellmasterAdminCampaignsList.functions.startCharts( response );

				// Update the kpis.
				PsUpsellmasterAdminCampaignsList.functions.updateKpis( response );
			},
			onDocumentReady: function onDocumentReady() {
				// Start the pikaday fields.
				PsUpsellmasterAdminCampaignsList.functions.startPikadayFields();

				// Start the datatables.
				PsUpsellmasterAdminCampaignsList.functions.startDataTables();
			},
		},
		functions: {
			applyFilters: function applyFilters() {
				// Get the filters.
				var filters = PsUpsellmasterAdminCampaignsList.functions.getFilters();

				// Set the filters.
				PsUpsellmasterAdminCampaignsList.settings.datatables.custom.main.ajax.data.filters = filters;

				// Reload the datatable.
				PsUpsellmasterAdminCampaignsList.functions.reloadDataTable();
			},
			clearFilters: function clearFilters() {
				// Get the fields.
				var fields = $( '.psupsellmaster-filters .psupsellmaster-field' );

				// Reset the fields value.
				fields.val( null ).trigger( 'change' );
			},
			deleteCampaigns: function deleteCampaigns( ids ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_delete_campaigns',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					ids: ids,
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
							// Reload the datatable.
							PsUpsellmasterAdminCampaignsList.functions.reloadDataTable();
						}
					}
				);
			},
			resetBulkActions: function resetBulkActions() {
				// Get the wrapper.
				var wrapper = $( '.psupsellmaster-datatable-wrapper' );

				// Get the select.
				var select = wrapper.find( '.psupsellmaster-field-bulk-actions' );

				// Reset the select.
				select.val( '' );

				// Get the button.
				var button = wrapper.find( '.psupsellmaster-btn-apply-bulk-action' );

				// Disable the button.
				button.prop( 'disabled', true );

				// Add the disabled class.
				button.addClass( 'disabled' );
			},
			duplicateCampaigns: function duplicateCampaigns( ids ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_duplicate_campaigns',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					ids: ids,
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {},
						success: function ( response ) {
							// Check the redirect.
							if ( response.redirect && 1 === ids.length ) {
								// Redirect to the url.
								window.open( response.redirect, '_blank' );
							}
						},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {
							// Reload the datatable.
							PsUpsellmasterAdminCampaignsList.functions.reloadDataTable();
						}
					}
				);
			},
			getFilters: function getFilters() {
				// Set the filters.
				var filters = {
					authors: new Object(),
					campaign_date: {
						start: [],
						end: [],
					},
					carts: {
						gross_earnings: {
							min: [],
							max: [],
						},
						tax: {
							min: [],
							max: [],
						},
						discount: {
							min: [],
							max: [],
						},
						net_earnings: {
							min: [],
							max: [],
						},
						orders_qty: {
							min: [],
							max: [],
						},
						aov: {
							min: [],
							max: [],
						},
					},
					events: {
						add_to_cart: {
							min: [],
							max: [],
						},
						click: {
							min: [],
							max: [],
						},
						impression: {
							min: [],
							max: [],
						},
					},
					locations: [],
					products: new Object(),
					status: [],
					taxonomies: new Object(),
					weekdays: [],
				};

				// Get the fields.
				var fields = $( '.psupsellmaster-filters' );

				//
				// Authors.
				//

				// Set the filter value.
				filters.authors = fields.find( '.psupsellmaster-field-authors' ).val() || [];

				//
				// Campaign date.
				//

				// Set the filter value.
				filters.campaign_date.start = fields.find( '.psupsellmaster-field-campaign-date-start' ).val() || [];

				// Set the filter value.
				filters.campaign_date.end = fields.find( '.psupsellmaster-field-campaign-date-end' ).val() || [];

				//
				// Carts.
				//

				// Set the filter value.
				filters.carts.gross_earnings.min = fields.find( '.psupsellmaster-field-carts-gross-earnings-min' ).val() || [];

				// Set the filter value.
				filters.carts.gross_earnings.max = fields.find( '.psupsellmaster-field-carts-gross-earnings-max' ).val() || [];

				// Set the filter value.
				filters.carts.tax.min = fields.find( '.psupsellmaster-field-carts-tax-min' ).val() || [];

				// Set the filter value.
				filters.carts.tax.max = fields.find( '.psupsellmaster-field-carts-tax-max' ).val() || [];

				// Set the filter value.
				filters.carts.discount.min = fields.find( '.psupsellmaster-field-carts-discount-min' ).val() || [];

				// Set the filter value.
				filters.carts.discount.max = fields.find( '.psupsellmaster-field-carts-discount-max' ).val() || [];

				// Set the filter value.
				filters.carts.net_earnings.min = fields.find( '.psupsellmaster-field-carts-net-earnings-min' ).val() || [];

				// Set the filter value.
				filters.carts.net_earnings.max = fields.find( '.psupsellmaster-field-carts-net-earnings-max' ).val() || [];

				// Set the filter value.
				filters.carts.orders_qty.min = fields.find( '.psupsellmaster-field-carts-orders-qty-min' ).val() || [];

				// Set the filter value.
				filters.carts.orders_qty.max = fields.find( '.psupsellmaster-field-carts-orders-qty-max' ).val() || [];

				// Set the filter value.
				filters.carts.aov.min = fields.find( '.psupsellmaster-field-carts-aov-min' ).val() || [];

				// Set the filter value.
				filters.carts.aov.max = fields.find( '.psupsellmaster-field-carts-aov-max' ).val() || [];

				//
				// Events.
				//

				// Set the filter value.
				filters.events.impression.min = fields.find( '.psupsellmaster-field-events-impression-min' ).val() || [];

				// Set the filter value.
				filters.events.impression.max = fields.find( '.psupsellmaster-field-events-impression-max' ).val() || [];

				// Set the filter value.
				filters.events.click.min = fields.find( '.psupsellmaster-field-events-click-min' ).val() || [];

				// Set the filter value.
				filters.events.click.max = fields.find( '.psupsellmaster-field-events-click-max' ).val() || [];

				// Set the filter value.
				filters.events.add_to_cart.min = fields.find( '.psupsellmaster-field-events-add-to-cart-min' ).val() || [];

				// Set the filter value.
				filters.events.add_to_cart.max = fields.find( '.psupsellmaster-field-events-add-to-cart-max' ).val() || [];

				//
				// Locations.
				//

				// Set the filter value.
				filters.locations = fields.find( '.psupsellmaster-field-locations' ).val() || [];

				//
				// Products.
				//

				// Set the filter value.
				filters.products = fields.find( '.psupsellmaster-field-products' ).val() || [];

				//
				// Status.
				//

				// Set the filter value.
				filters.status = fields.find( '.psupsellmaster-field-status' ).val() || [];

				//
				// Weekdays.
				//

				// Set the filter value.
				filters.weekdays = fields.find( '.psupsellmaster-field-weekdays' ).val() || [];

				// Return the filters.
				return filters;
			},
			init: function init() {
				PsUpsellmasterAdminCampaignsList.functions.registerAttributes();
				PsUpsellmasterAdminCampaignsList.functions.registerEvents();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_campaigns_list ) {
					// Set the attributes.
					PsUpsellmasterAdminCampaignsList.data = psupsellmaster_admin_data_campaigns_list;
				}

				// Set the popups.
				PsUpsellmaster.attributes.popups = PsUpsellmaster.attributes.popups || new Object();

				// Set the actions.
				PsUpsellmasterAdminCampaignsList.actions = {
					main: 'psupsellmaster_ajax_get_campaigns_list_table',
				};

				// Set the settings.
				PsUpsellmasterAdminCampaignsList.settings = {
					chartjs: {
						default: new Object(),
							custom: {
								main: new Object(),
						},
					},
					datatables: {
						default: new Object(),
							custom: {
								main: new Object(),
						},
					},
				};

				// Set the chart settings: default.
				PsUpsellmasterAdminCampaignsList.settings.chartjs.default = {
					data: {
						datasets: [
							{
								backgroundColor: 'rgb(24, 128, 56)',
								borderColor: 'rgb(24, 128, 56)',
								data: [],
								label: '',
								order: 1,
								type: 'line',
								yAxisID: 'earnings',
						},
							{
								backgroundColor: 'rgb(217, 48, 37)',
								borderColor: 'rgb(217, 48, 37)',
								data: [],
								label: '',
								order: 3,
								type: 'line',
								yAxisID: 'orders',
						},
							{
								backgroundColor: 'rgb(26, 115, 232)',
								borderColor: 'rgb(26, 115, 232)',
								data: [],
								label: '',
								order: 2,
								type: 'line',
								yAxisID: 'carts',
						},
						],
						labels: [],
					},
					options: {
						interaction: {
							intersect: false,
							mode: 'index',
						},
						maintainAspectRatio: false,
						plugins: {
							legend: {
								labels: {
									sort: PsUpsellmasterAdminCampaignsList.events.onChartJsSort,
								},
							},
							tooltip: {
								callbacks: {
									label: PsUpsellmasterAdminCampaignsList.events.onChartJsTooltipLabel,
									title: PsUpsellmasterAdminCampaignsList.events.onChartJsTooltipTitle,
								},
								itemSort: PsUpsellmasterAdminCampaignsList.events.onChartJsSort,
							},
						},
						responsive: true,
						scales: {
							x: {
								ticks: {
									callback: PsUpsellmasterAdminCampaignsList.events.onChartJsScalesXTicksCallback,
								},
							},
							earnings: {
								position: 'left',
								ticks: {
									callback: PsUpsellmasterAdminCampaignsList.events.onChartJsScalesAmountTicksCallback,
								},
							},
							orders: {
								display: false,
								position: 'right',
								ticks: {
									precision: 0,
								},
							},
							carts: {
								position: 'right',
								ticks: {
									precision: 0,
								},
							},
						},
					},
				};

				// Set the chart settings: main.
				PsUpsellmasterAdminCampaignsList.settings.chartjs.custom.main = $.extend( true, new Object(), PsUpsellmasterAdminCampaignsList.settings.chartjs.default );

				// Set the datatable settings: default.
				PsUpsellmasterAdminCampaignsList.settings.datatables.default = {
					ajax: {
						data: PsUpsellmasterAdminCampaignsList.events.onDataTableAjaxData,
						dataSrc: PsUpsellmasterAdminCampaignsList.events.onDataTableAjaxDataSrc,
						type: 'POST',
						url: PsUpsellmaster.attributes.ajax.url,
					},
					buttons: [
						{
							className: 'button',
							extend: 'copyHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsList.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'print',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsList.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'csvHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsList.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'excelHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsList.events.onDataTableExportFormatBody,
								},
							},
						},
					],
					createdRow: PsUpsellmasterAdminCampaignsList.events.onDataTableCreatedRow,
					displayLength: 10,
					dom: 'Bliptip',
					drawCallback: PsUpsellmasterAdminCampaignsList.events.onDataTableDrawCallback,
					filter: true,
					initComplete: PsUpsellmasterAdminCampaignsList.events.onDataTableInitComplete,
					oLanguage: {
						sLengthMenu: PsUpsellmasterAdminCampaignsList.data.texts.datatable_length,
					},
					order: [ [ 3, 'asc' ] ],
					pageLength: 10,
					lengthMenu: [
						[ 10, 25, 50, 100, 250, 1000 ],
						[ 10, 25, 50, 100, 250, 1000 ],
					],
					paginate: true,
					pagingType: 'full_numbers',
					serverSide: true,
					responsive: {
						details: {
							target: '.psupsellmaster-toggle-details',
							type: 'column',
						},
					},
				};

				// Check if this is the pro version.
				if ( PsUpsellmaster.attributes.plugin.is_pro ) {
					// Set the datatable settings: default.
					PsUpsellmasterAdminCampaignsList.settings.datatables.default.columnDefs = [
						{
							className: 'dt-body-center',
							targets: [ 0 ],
						},
						{
							className: 'dt-body-left',
							targets: [ 1, 2, 3, 6 ],
						},
						{
							className: 'dt-body-right',
							targets: [ 4, 5, 7, 8, 9, 10, 11, 12, 13 ],
						},
						{
							orderable: false,
							targets: [ 0, 6 ],
						},
						{
							createdCell: PsUpsellmasterAdminCampaignsList.events.onDataTableCreatedCell,
							searchable: false,
							targets: '_all',
					},
					];

					// Otherwise...
				} else {
					// Set the datatable settings: default.
					PsUpsellmasterAdminCampaignsList.settings.datatables.default.columnDefs = [
						{
							className: 'dt-body-center',
							targets: [ 0 ],
						},
						{
							className: 'dt-body-left',
							targets: [ 1, 2, 3, 5 ],
						},
						{
							className: 'dt-body-right',
							targets: [ 4, 6, 7, 8, 9, 10, 11 ],
						},
						{
							orderable: false,
							targets: [ 0, 5 ],
						},
						{
							createdCell: PsUpsellmasterAdminCampaignsList.events.onDataTableCreatedCell,
							searchable: false,
							targets: '_all',
						},
					];
				}

				// Set the datatable settings: main.
				PsUpsellmasterAdminCampaignsList.settings.datatables.custom.main = $.extend(
					true,
					new Object(),
					PsUpsellmasterAdminCampaignsList.settings.datatables.default
				);

				// Set the instances.
				PsUpsellmasterAdminCampaignsList.instances = {
					charts: {
						main: null,
					},
					datatables: {
						main: null,
					},
				};
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminCampaignsList.events.onDocumentReady );
				$( document ).on( 'xhr.dt', '#psupsellmaster-datatable-campaigns', PsUpsellmasterAdminCampaignsList.events.onDataTableUpdate );
				$( document ).on( 'click', '#psupsellmaster-btn-toggle-filters', PsUpsellmasterAdminCampaignsList.events.onClickBtnToggleFilters );
				$( document ).on( 'click', '#psupsellmaster-btn-apply-filters', PsUpsellmasterAdminCampaignsList.events.onClickBtnApplyFilters );
				$( document ).on( 'click', '#psupsellmaster-btn-reset-filters', PsUpsellmasterAdminCampaignsList.events.onClickBtnResetFilters );
				$( document ).on( 'click', '.psupsellmaster-btn-apply-bulk-action', PsUpsellmasterAdminCampaignsList.events.onClickBtnApplyBulkAction );
				$( document ).on( 'change', '.psupsellmaster-field-bulk-actions', PsUpsellmasterAdminCampaignsList.events.onChangeBulkActions );
				$( document ).on( 'click', '.psupsellmaster-row-actions .psupsellmaster-duplicate', PsUpsellmasterAdminCampaignsList.events.onClickRowActionDuplicate );
				$( document ).on( 'click', '.psupsellmaster-row-actions .psupsellmaster-set-status', PsUpsellmasterAdminCampaignsList.events.onClickRowActionSetStatus );
				$( document ).on( 'click', '.psupsellmaster-check-rows', PsUpsellmasterAdminCampaignsList.events.onChangeCheckRows );
				$( document ).on( 'click', '.psupsellmaster-check-row', PsUpsellmasterAdminCampaignsList.events.onChangeCheckRow );
				$( document ).on( 'click', '.psupsellmaster-btn-refresh-datetime-left', PsUpsellmasterAdminCampaignsList.events.onClickBtnRefreshDatetimeLeft );
			},
			reloadDataTable: function reloadDataTable() {
				// Reload the DataTable.
				PsUpsellmasterAdminCampaignsList.instances.datatables.main.ajax.reload();
			},
			resetFilters: function resetFilters() {
				// Clear the filters.
				PsUpsellmasterAdminCampaignsList.functions.clearFilters();

				// Set the filters.
				PsUpsellmasterAdminCampaignsList.settings.datatables.custom.main.ajax.data.filters = new Object();

				// Reload the datatable.
				PsUpsellmasterAdminCampaignsList.functions.reloadDataTable();
			},
			setCampaignsStatus: function setCampaignsStatus( ids, status ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_set_campaigns_status',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					ids: ids,
					status: status,
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
							// Reload the datatable.
							PsUpsellmasterAdminCampaignsList.functions.reloadDataTable();
						},
					}
				);
			},
			startCharts: function startCharts( data ) {
				// Check if the chart does exist.
				if ( PsUpsellmasterAdminCampaignsList.instances.charts['main'] ) {
					try {
						// Attempt to destroy the chart.
						PsUpsellmasterAdminCampaignsList.instances.charts['main'].destroy();
					} catch ( exception ) {
					}
				}

				// Set the loop length.
				var loopLength = 0;

				// Set the chart data to an empty array.
				PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[0].data = new Array();
				PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[1].data = new Array();
				PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[2].data = new Array();

				// Set the chart labels to an empty array.
				PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.labels = new Array();

				// Set the loop length.
				var loopLength = data.charts['main'].items.length;

				// Loop through the items.
				for ( var i = 0; i < loopLength; i++ ) {
					// Add the chart data.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[0].data.push( data.charts['main'].items[ i ].earnings );

					// Add the chart data.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[1].data.push( data.charts['main'].items[ i ].carts );

					// Add the chart data.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[2].data.push( data.charts['main'].items[ i ].orders );

					// Add the chart label.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.labels.push( data.charts['main'].items[ i ].label );
				}

				// Set the loop length.
				var loopLength = data.charts['main'].legends.length;

				// Loop through the legends.
				for ( var i = 0; i < loopLength; i++ ) {
					// Set the label.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[0].label = data.charts['main'].legends[ i ].earnings;

					// Set the label.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[1].label = data.charts['main'].legends[ i ].carts;

					// Set the label.
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main'].data.datasets[2].label = data.charts['main'].legends[ i ].orders;
				}

				// Start a new chart.
				PsUpsellmasterAdminCampaignsList.instances.charts['main'] = new Chart(
					document.getElementById( 'psupsellmaster-campaigns-chart-main' ),
					PsUpsellmasterAdminCampaignsList.settings.chartjs.custom['main']
				);
			},
			startDataTables: function startDataTables() {
				// Get the datatable.
				var datatable = $( '#psupsellmaster-datatable-campaigns' );

				// Start new datatable.
				PsUpsellmasterAdminCampaignsList.instances.datatables.main = datatable.DataTable( PsUpsellmasterAdminCampaignsList.settings.datatables.custom.main );
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
						PsUpsellmasterAdminCampaignsList.functions.startPikadayField( field );
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
			updateKpis: function updateKpis( data ) {
				// Get the container.
				var container = $( '.psupsellmaster-kpis' );

				// Set the html.
				container.html( data.kpis );
			},
		},
	};

	PsUpsellmasterAdminCampaignsList.functions.init();
} )( jQuery );

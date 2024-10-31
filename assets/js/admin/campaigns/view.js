/**
 * Admin - Campaigns - View.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminCampaignsView;

	PsUpsellmasterAdminCampaignsView = {
		attributes: {},
		events: {
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
				var label = PsUpsellmasterAdminCampaignsView.data.texts.currency_symbol;

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
					label = PsUpsellmasterAdminCampaignsView.data.texts.currency_symbol;
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
			onClickBtnApplyFilters: function onClickBtnApplyFilters( event ) {
				// Apply the filters.
				PsUpsellmasterAdminCampaignsView.functions.applyFilters();
			},
			onClickBtnDuplicateCampaign: function onClickBtnDuplicateCampaign( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the id.
				var id = button.attr( 'data-campaign-id' );

				// Duplicate the campaign.
				PsUpsellmasterAdminCampaignsView.functions.duplicateCampaigns( ( new Array( id ) ) );
			},
			onClickBtnResetFilters: function onClickBtnResetFilters( event ) {
				// Reset the filters.
				PsUpsellmasterAdminCampaignsView.functions.resetFilters();
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
			onDataTableAjaxData: function onDataTableAjaxData( data, settings ) {
				// Set the data.
				data = $.extend(
					true,
					data,
					{
						action: 'psupsellmaster_ajax_get_campaign_carts',
						nonce: PsUpsellmaster.attributes.ajax.nonce,
						campaign_id: $( '#psupsellmaster-campaign-id' ).val(),
						filters: PsUpsellmasterAdminCampaignsView.settings.datatables.custom.main.ajax.data.filters,
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
				$( '.psupsellmaster-datatable-wrapper .buttons-copy' ).html( '<i class="fa fa-copy"></i>&nbsp;' + PsUpsellmasterAdminCampaignsView.data.texts.datatable_btn_copy );
				$( '.psupsellmaster-datatable-wrapper .buttons-csv' ).html( '<i class="fa fa-file-csv"></i>&nbsp;' + PsUpsellmasterAdminCampaignsView.data.texts.datatable_btn_csv );
				$( '.psupsellmaster-datatable-wrapper .buttons-excel' ).html( '<i class="fa fa-file-excel"></i>&nbsp;' + PsUpsellmasterAdminCampaignsView.data.texts.datatable_btn_excel );
				$( '.psupsellmaster-datatable-wrapper .buttons-print' ).html( '<i class="fa fa-print"></i>&nbsp;' + PsUpsellmasterAdminCampaignsView.data.texts.datatable_btn_print );

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
				PsUpsellmasterAdminCampaignsView.functions.startCharts( response );

				// Update the kpis.
				PsUpsellmasterAdminCampaignsView.functions.updateKpis( response );
			},
			onDocumentReady: function onDocumentReady() {
				// Start the pikaday fields.
				PsUpsellmasterAdminCampaignsView.functions.startPikadayFields();

				// Start the datatables.
				PsUpsellmasterAdminCampaignsView.functions.startDataTables();
			},
		},
		functions: {
			applyFilters: function applyFilters() {
				// Get the filters.
				var filters = PsUpsellmasterAdminCampaignsView.functions.getFilters();

				// Set the filters.
				PsUpsellmasterAdminCampaignsView.settings.datatables.custom.main.ajax.data.filters = filters;

				// Reload the datatable.
				PsUpsellmasterAdminCampaignsView.functions.reloadDataTable();
			},
			clearFilters: function clearFilters() {
				// Get the fields.
				var fields = $( '.psupsellmaster-filters .psupsellmaster-field' );

				// Reset the fields value.
				fields.val( null ).trigger( 'change' );
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
						complete: function ( xhr, status ) {}
					}
				);
			},
			getFilters: function getFilters() {
				// Set the filters.
				var filters = {
					carts: {
						date: {
							start: [],
							end: [],
						},
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
						quantity: {
							min: [],
							max: [],
						},
						aov: {
							min: [],
							max: [],
						},
					},
				};

				// Get the fields.
				var fields = $( '.psupsellmaster-filters' );

				//
				// Carts.
				//

				// Set the filter value.
				filters.carts.date.start = fields.find( '.psupsellmaster-field-carts-date-start' ).val() || [];

				// Set the filter value.
				filters.carts.date.end = fields.find( '.psupsellmaster-field-carts-date-end' ).val() || [];

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
				filters.carts.quantity.min = fields.find( '.psupsellmaster-field-carts-quantity-min' ).val() || [];

				// Set the filter value.
				filters.carts.quantity.max = fields.find( '.psupsellmaster-field-carts-quantity-max' ).val() || [];

				// Set the filter value.
				filters.carts.aov.min = fields.find( '.psupsellmaster-field-carts-aov-min' ).val() || [];

				// Set the filter value.
				filters.carts.aov.max = fields.find( '.psupsellmaster-field-carts-aov-max' ).val() || [];

				// Return the filters.
				return filters;
			},
			init: function init() {
				PsUpsellmasterAdminCampaignsView.functions.registerAttributes();
				PsUpsellmasterAdminCampaignsView.functions.registerEvents();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_campaigns_view ) {
					// Set the attributes.
					PsUpsellmasterAdminCampaignsView.data = psupsellmaster_admin_data_campaigns_view;
				}

				// Set the settings.
				PsUpsellmasterAdminCampaignsView.settings = {
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
				PsUpsellmasterAdminCampaignsView.settings.chartjs.default = {
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
									sort: PsUpsellmasterAdminCampaignsView.events.onChartJsSort,
								},
							},
							tooltip: {
								callbacks: {
									label: PsUpsellmasterAdminCampaignsView.events.onChartJsTooltipLabel,
									title: PsUpsellmasterAdminCampaignsView.events.onChartJsTooltipTitle,
								},
								itemSort: PsUpsellmasterAdminCampaignsView.events.onChartJsSort,
							},
						},
						responsive: true,
						scales: {
							x: {
								ticks: {
									callback: PsUpsellmasterAdminCampaignsView.events.onChartJsScalesXTicksCallback,
								},
							},
							earnings: {
								position: 'left',
								ticks: {
									callback: PsUpsellmasterAdminCampaignsView.events.onChartJsScalesAmountTicksCallback,
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
				PsUpsellmasterAdminCampaignsView.settings.chartjs.custom.main = $.extend( true, new Object(), PsUpsellmasterAdminCampaignsView.settings.chartjs.default );

				// Set the datatable settings: default.
				PsUpsellmasterAdminCampaignsView.settings.datatables.default = {
					ajax: {
						data: PsUpsellmasterAdminCampaignsView.events.onDataTableAjaxData,
						dataSrc: PsUpsellmasterAdminCampaignsView.events.onDataTableAjaxDataSrc,
						type: 'POST',
						url: PsUpsellmaster.attributes.ajax.url,
					},
					buttons: [
						{
							className: 'button',
							extend: 'copyHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsView.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'print',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsView.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'csvHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsView.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'excelHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsView.events.onDataTableExportFormatBody,
								},
							},
						},
					],
					columnDefs: [
						{
							className: 'dt-body-left',
							targets: [ 0, 1, 2 ],
						},
						{
							className: 'dt-body-right',
							targets: [ 3, 4, 5, 6, 7, 8, 9, 10 ],
						},
						{
							orderable: false,
							targets: [],
						},
						{
							createdCell: PsUpsellmasterAdminCampaignsView.events.onDataTableCreatedCell,
							targets: '_all',
					},
					],
					createdRow: PsUpsellmasterAdminCampaignsView.events.onDataTableCreatedRow,
					displayLength: 10,
					dom: 'Bliptip',
					filter: true,
					initComplete: PsUpsellmasterAdminCampaignsView.events.onDataTableInitComplete,
					oLanguage: {
						sLengthMenu: PsUpsellmasterAdminCampaignsView.data.texts.datatable_length,
					},
					order: [ [ 8, 'desc' ] ],
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

				// Set the datatable settings: main.
				PsUpsellmasterAdminCampaignsView.settings.datatables.custom.main = $.extend(
					true,
					new Object(),
					PsUpsellmasterAdminCampaignsView.settings.datatables.default
				);

				// Set the instances.
				PsUpsellmasterAdminCampaignsView.instances = {
					charts: {
						main: null,
					},
					datatables: {
						main: null,
					},
				};
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminCampaignsView.events.onDocumentReady );
				$( document ).on( 'xhr.dt', '#psupsellmaster-datatable-campaign-carts', PsUpsellmasterAdminCampaignsView.events.onDataTableUpdate );
				$( document ).on( 'click', '#psupsellmaster-btn-toggle-filters', PsUpsellmasterAdminCampaignsView.events.onClickBtnToggleFilters );
				$( document ).on( 'click', '#psupsellmaster-btn-apply-filters', PsUpsellmasterAdminCampaignsView.events.onClickBtnApplyFilters );
				$( document ).on( 'click', '#psupsellmaster-btn-reset-filters', PsUpsellmasterAdminCampaignsView.events.onClickBtnResetFilters );
				$( document ).on( 'click', '.psupsellmaster-btn-duplicate-campaign', PsUpsellmasterAdminCampaignsView.events.onClickBtnDuplicateCampaign );
			},
			reloadDataTable: function reloadDataTable() {
				// Reload the DataTable.
				PsUpsellmasterAdminCampaignsView.instances.datatables.main.ajax.reload();
			},
			resetFilters: function resetFilters() {
				// Clear the filters.
				PsUpsellmasterAdminCampaignsView.functions.clearFilters();

				// Set the filters.
				PsUpsellmasterAdminCampaignsView.settings.datatables.custom.main.ajax.data.filters = new Object();

				// Reload the datatable.
				PsUpsellmasterAdminCampaignsView.functions.reloadDataTable();
			},
			startCharts: function startCharts( data ) {
				// Check if the chart does exist.
				if ( PsUpsellmasterAdminCampaignsView.instances.charts['main'] ) {
					try {
						// Attempt to destroy the chart.
						PsUpsellmasterAdminCampaignsView.instances.charts['main'].destroy();
					} catch ( exception ) {
					}
				}

				// Set the loop length.
				var loopLength = 0;

				// Set the chart data to an empty array.
				PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[0].data = new Array();

				// Set the chart labels to an empty array.
				PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.labels = new Array();

				// Set the loop length.
				loopLength = data.charts['main'].items.length;

				// Loop through the items.
				for ( var i = 0; i < loopLength; i++ ) {
					// Add the chart data.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[0].data.push( data.charts['main'].items[ i ].earnings );

					// Add the chart data.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[1].data.push( data.charts['main'].items[ i ].carts );

					// Add the chart data.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[2].data.push( data.charts['main'].items[ i ].orders );

					// Add the chart label.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.labels.push( data.charts['main'].items[ i ].label );
				}

				// Set the loop length.
				loopLength = data.charts['main'].legends.length;

				// Loop through the legends.
				for ( var i = 0; i < loopLength; i++ ) {
					// Set the label.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[0].label = data.charts['main'].legends[ i ].earnings;

					// Set the label.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[1].label = data.charts['main'].legends[ i ].carts;

					// Set the label.
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main'].data.datasets[2].label = data.charts['main'].legends[ i ].orders;
				}

				// Start a new chart.
				PsUpsellmasterAdminCampaignsView.instances.charts['main'] = new Chart(
					document.getElementById( 'psupsellmaster-campaign-chart-main' ),
					PsUpsellmasterAdminCampaignsView.settings.chartjs.custom['main']
				);
			},
			startDataTables: function startDataTables() {
				// Start new datatable.
				PsUpsellmasterAdminCampaignsView.instances.datatables.main = $( '#psupsellmaster-datatable-campaign-carts' ).DataTable( PsUpsellmasterAdminCampaignsView.settings.datatables.custom.main );
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
						PsUpsellmasterAdminCampaignsView.functions.startPikadayField( field );
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

	PsUpsellmasterAdminCampaignsView.functions.init();
} )( jQuery );

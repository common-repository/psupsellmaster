/**
 * Admin - Results.
 *
 * @package PsUpsellMaster.
 */

var psupsellmaster_chart = null;

jQuery( document ).ready(
	function ( $ ) {
		$( '.psupsellmaster_date' ).each(
			function () {
				new Pikaday(
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
				const startOfMonth = moment().subtract( 1,'months' ).startOf( 'month' ).format( 'YYYY/MM/DD' );
				const endOfMonth   = moment().subtract( 1,'months' ).endOf( 'month' ).format( 'YYYY/MM/DD' );
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
				const start = moment().subtract( 1,'years' ).startOf( 'year' ).format( 'YYYY/MM/DD' );
				const end   = moment().subtract( 1,'years' ).endOf( 'year' ).format( 'YYYY/MM/DD' );
				$( '#psupsellmaster_date_from' ).val( start ).trigger( 'change' );
				$( '#psupsellmaster_date_to' ).val( end ).trigger( 'change' );
			}
		);

		$( '#psupsellmaster_btn_last_week' ).on(
			'click',
			function () {
				const start = moment().subtract( 1,'week' ).startOf( 'week' ).format( 'YYYY/MM/DD' );
				const end   = moment().subtract( 1,'week' ).endOf( 'week' ).format( 'YYYY/MM/DD' );
				$( '#psupsellmaster_date_from' ).val( start ).trigger( 'change' );
				$( '#psupsellmaster_date_to' ).val( end ).trigger( 'change' );
			}
		);

		$( '#psupsellmaster_btn_reset' ).on(
			'click',
			function () {
				$( '#psupsellmaster_date_from' ).val( '' ).trigger( 'change' );
				$( '#psupsellmaster_date_to' ).val( '' ).trigger( 'change' );
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
				customTaxonomies = { base: new Object(), upsell: new Object() };

				// Loop through the fields of custom taxonomy.
				container.find( '.psupsellmaster-field-custom-taxonomy' ).each(
					function () {
						var field, taxonomy, type, value;

						// Get the field.
						field = $( this );

						// Get the value.
						value = field.val();

						// Get the type.
						type = field.data( 'target-type' );

						// Get the taxonomy.
						taxonomy = field.data( 'taxonomy' );

						// Check if the targe type is base.
						if ( 'base' === type ) {
							// Set the base taxonomy value.
							customTaxonomies.base[ taxonomy ] = value;

							// Otherwise, it is upsell.
						} else {
							// Set the upsell taxonomy value.
							customTaxonomies.upsell[ taxonomy ] = value;
						}

					}
				);

				$( '#psupsellmaster_upsells_filter input[type="search"]' ).val( $( '#psupsellmaster_search' ).val() ).trigger( 'search' );

				var filter = {
					'bp': $( '#psupsellmaster_base_products' ).val(),
					'bc': $( '#psupsellmaster_base_categories' ).val(),
					'bt': $( '#psupsellmaster_base_tags' ).val(),
					'up': $( '#psupsellmaster_upsell_products' ).val(),
					'uc': $( '#psupsellmaster_upsell_categories' ).val(),
					'ut': $( '#psupsellmaster_upsell_tags' ).val(),
					'rp': $( '#psupsellmaster_related_products' ).val(),
					'cu': $( '#psupsellmaster_customers' ).val(),
					'loc': $( '#psupsellmaster_location' ).val(),
					'typ': $( '#psupsellmaster_source' ).val(),
					'prf': $( '#psupsellmaster_price_from' ).val(),
					'prt': $( '#psupsellmaster_price_to' ).val(),
					'saf': $( '#psupsellmaster_sale_from' ).val(),
					'sat': $( '#psupsellmaster_sale_to' ).val(),
					'dtf': ( '' + $( '#psupsellmaster_date_from' ).val() ).replace( /\//g,'-' ),
					'dtt': ( '' + $( '#psupsellmaster_date_to' ).val() ).replace( /\//g,'-' ),
					custom_taxonomies: customTaxonomies,
				};

				var new_url = PsUpsellmaster.attributes.ajax.url + '?nonce=' + PsUpsellmaster.attributes.ajax.nonce + '&action=psupsellmaster_admin_ajax_get_upsells&f='
						+ encodeURIComponent( JSON.stringify( filter ) );

				$( '#psupsellmaster_upsells' ).DataTable().ajax.url( new_url ).load();
				$( '#psupsellmaster_summary_details_filters_text_1' ).show();
				$( '#psupsellmaster_summary_details_filters_text_2' ).hide();

				var date_period = '';
				var from        = (filter.dtf.length > 0) ? moment( filter.dtf + ' 00:00:00', 'YYYY-MM-DD' ).format( 'MMMM Do' ) : '';
				var to          = (filter.dtt.length > 0) ? moment( filter.dtt + ' 00:00:00', 'YYYY-MM-DD' ).format( 'MMMM Do' ) : '';

				if (from.length > 0) {
					date_period += ' from ' + from;
				}

				if (to.length > 0) {
					date_period += ' to ' + to;
				}

				if ((from.length > 0) && (to.length > 0)) {
					var admission = moment( filter.dtf, 'YYYY-MM-DD' );
					var discharge = moment( filter.dtt, 'YYYY-MM-DD' );
					date_period  += ' (' + (parseInt( discharge.diff( admission, 'days' ) ) + 1) + ' days)';
				}

				$( '#psupsellmaster_summary_details_date_period' ).html( date_period );
			}
		);

		$( '#psupsellmaster_search' ).on(
			'keyup',
			function (e) {
				$( '#psupsellmaster_upsells_filter input[type="search"]' ).val( $( this ).val() ).trigger( 'change' );
			}
		);
		$( '#psupsellmaster_search' ).on(
			'search',
			function (e) {
				$( '#psupsellmaster_upsells_filter input[type="search"]' ).val( $( this ).val() ).trigger( 'search' );
			}
		);
		$( '.psupsellmaster_expand_filters_content' ).on(
			'click',
			function () {

				if ($( '#psupsellmaster_filters_content' ).hasClass( 'psupsellmaster_hidden' )) {
					$( '#psupsellmaster_filters_content' ).removeClass( 'psupsellmaster_hidden' );
				} else {
					$( '#psupsellmaster_filters_content' ).addClass( 'psupsellmaster_hidden' );
				}

			}
		);
	}
);

( function ( $ ) {
	var PsUpsellmasterAdminResults;

	PsUpsellmasterAdminResults = {
		attributes: {},
		events: {
			onClickBtnResetFilters: function onClickBtnResetFilters( event ) {
				// Reset the select2.
				PsUpsellmaster.select2.functions.clearAll();

				$( '.psupsellmaster_price' ).val( '' ).trigger( 'change' );
				$( '#psupsellmaster_btn_reset' ).trigger( 'click' );
				$( '#psupsellmaster_upsells' ).DataTable().ajax.url( PsUpsellmaster.attributes.ajax.url + '?nonce=' + PsUpsellmaster.attributes.ajax.nonce + '&action=psupsellmaster_admin_ajax_get_upsells' ).load();
				$( '#psupsellmaster_summary_details_filters_text_1' ).hide();
				$( '#psupsellmaster_summary_details_filters_text_2' ).show();
				$( '#psupsellmaster_upsells_filter input[type="search"]' ).val( '' ).trigger( 'search' );
				$( '#psupsellmaster_search' ).val( '' ).trigger( 'search' );
			},
			onDataTableCreatedCell: function onDataTableCreatedCell( cell ) {
				$( cell ).addClass( 'psupsellmaster-datatable-col' );
			},
			onDataTableCreatedRow: function onDataTableCreatedRow( row ) {
				$( row ).addClass( 'psupsellmaster-datatable-row' );
			},
			onDocumentReady: function onDocumentReady() {
				// Start the datatables.
				PsUpsellmasterAdminResults.functions.startDataTables();

				// Start the tooltips.
				PsUpsellmasterAdminResults.functions.startTooltips();
			},
		},
		functions: {
			attachTooltips: function attachTooltips( selector ) {
				var elements;

				// Get the selector.
				selector = selector || '.psupsellmaster-help-tip';

				// Get the elements.
				elements = $( selector );

				// Check if there are elements found.
				if ( 0 !== elements.length ) {
					// Start the tooltip for the elements.
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
			init: function init() {
				PsUpsellmasterAdminResults.functions.registerAttributes();
				PsUpsellmasterAdminResults.functions.registerEvents();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_results ) {
					// Set the attributes.
					PsUpsellmasterAdminResults.data = psupsellmaster_admin_data_results;
				}
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminResults.events.onDocumentReady );
				$( document ).on( 'click', '#psupsellmaster_btn_reset_filters', PsUpsellmasterAdminResults.events.onClickBtnResetFilters );
			},
			startDataTables: function startDataTables() {
				var datatables_options = {
					dom: 'Bfliptip',
					serverSide: true,
					filter: true,
					paginate: true,
					pagingType: 'full_numbers',
					pageLength: 100,
					lengthMenu:  [
						[ 10, 25, 50, 100, 250, 1000 ],
						[ 10, 25, 50, 100, 250, 1000 ],
					],
					displayLength: 100,
					oLanguage: {
						sLengthMenu: PsUpsellmasterAdminResults.data.texts.datatable_length,
					},
					order: [ [ 0, 'desc' ] ],
					buttons: [
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
							targets: 0,
							className: 'dt-body-right',
						},
						{
							targets: 1,
							className: 'dt-body-right',
						},
						{
							targets: 2,
							className: 'dt-body-left',
							width: '15%',
						},
						{
							targets: 3,
							className: 'dt-body-left',
							width: '15%',
						},
						{
							targets: 4,
							className: 'dt-body-left',
						},
						{
							targets: 5,
							className: 'dt-body-left',
						},
						{
							targets: 6,
							className: 'dt-body-left',
						},
						{
							targets: 7,
							className: 'dt-body-left',
						},
						{
							targets: 8,
							className: 'dt-body-left',
						},
						{
							targets: 9,
							className: 'dt-body-right',
						},
						{
							targets: 10,
							className: 'dt-body-left',
							orderable: false,
							width: '25%',
						},
						{
							targets: 11,
							className: 'dt-body-right',
						},
						{
							createdCell: PsUpsellmasterAdminResults.events.onDataTableCreatedCell,
							targets: '_all',
						},
					],
					createdRow: PsUpsellmasterAdminResults.events.onDataTableCreatedRow,
					initComplete: function () {
						// Set the button icons and texts.
						$( '.psupsellmaster-datatable-wrapper .buttons-copy' ).html( '<i class="fa fa-copy"></i>&nbsp;' + psupsellmaster_admin_data_results.texts.datatable_btn_copy );
						$( '.psupsellmaster-datatable-wrapper .buttons-csv' ).html( '<i class="fa fa-file-csv"></i>&nbsp;' + psupsellmaster_admin_data_results.texts.datatable_btn_csv );
						$( '.psupsellmaster-datatable-wrapper .buttons-excel' ).html( '<i class="fa fa-file-excel"></i>&nbsp;' + psupsellmaster_admin_data_results.texts.datatable_btn_excel );
						$( '.psupsellmaster-datatable-wrapper .buttons-print' ).html( '<i class="fa fa-print"></i>&nbsp;' + psupsellmaster_admin_data_results.texts.datatable_btn_print );
					},
					drawCallback: function (settings) {
						$( '#psupsellmaster_upsells td div.psupsellmaster_upsells_edit_view_container' ).each(
							function (e) {
								$( this ).closest( '.psupsellmaster_upsells_base_product' ).hover(
									function () {
										$( this ).find( '.psupsellmaster_upsells_edit_view' ).show();
									},
									function () {
										$( this ).find( '.psupsellmaster_upsells_edit_view' ).hide();
									}
								);
							}
						);
						$( '#psupsellmaster_upsells' ).css( {'width': '100%'} );
					},
					ajax: {
						url: PsUpsellmaster.attributes.ajax.url + '?nonce=' + PsUpsellmaster.attributes.ajax.nonce + '&action=psupsellmaster_admin_ajax_get_upsells'
					}
				};
		
				$( '#psupsellmaster_upsells' ).on(
					'xhr.dt',
					function ( e, settings, json, xhr ) {
		
						if ( ! json ) {
							return;
						}
		
						if ( ! ! psupsellmaster_chart ) {
							try {
								psupsellmaster_chart.destroy();
							} catch ( e ) {
							}
						}
		
						$( '#psupsellmaster-summary-chart' ).html( '' );
		
						const config = {
							type: 'line',
							data: {
								labels: [],
								datasets: [ {
									label: 'Chart: Daily',
									backgroundColor: 'rgb(255, 99, 132)',
									borderColor: 'rgb(255, 99, 132)',
									data: [],
								} ],
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								scales: {
									y: {
										ticks: {
											// Include a dollar sign in the ticks.
											callback: function ( value, index, values ) {
												return json.currency_symbol + value.toLocaleString();
											},
										},
									},
								},
							},
						};
		
						var chart_data_length = json.chart_data.length;
		
						for ( var i = 0; i < chart_data_length; i++ ) {
							config.data.labels.push( json.chart_data[ i ][1] );
							config.data.datasets[0].data.push( json.chart_data[ i ][0] );
						}
		
						psupsellmaster_chart = new Chart(
							document.getElementById( 'psupsellmaster-summary-chart' ),
							config
						);
		
						// Reset labels and values.
						$( '.psupsellmaster-summary-table-body .psupsellmaster-stats-table-row-label' ).html( '' );
						$( '.psupsellmaster-summary-table-body .psupsellmaster-stats-table-row-value' ).html( 0 + '&#37;' );
		
						// Set Summary: General.
		
						$( '.psupsellmaster-results-summary' ).eq( 0 ).find( '.psupsellmaster-summary-total-value' ).html( json.sum_sales_value );
		
						// Set Details: Upsells.
		
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_sales_value );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.recordsFiltered );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 2 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.countd_upsell_id );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 3 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_upsells_per_product );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 4 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.range_sales_value );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 5 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_sales_value );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 6 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.days_created );
						$( '.psupsellmaster-stats-table-details-upsells .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_sales_value_per_day );
		
						// Set Details: Base Products.
		
						$( '.psupsellmaster-stats-table-details-products .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_sales_value );
						$( '.psupsellmaster-stats-table-details-products .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.countd_base_product_id );
						$( '.psupsellmaster-stats-table-details-products .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_sales_value_per_base_product );
		
						// Set Details: Orders.
		
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.countd_payment_id );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_order_value_excl_upsells );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 2 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_sales_value );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 3 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_order_value_incl_upsells );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 4 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_order_value_excl_upsells );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 5 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_sales_value_per_order );
						$( '.psupsellmaster-stats-table-details-orders .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_order_value_incl_upsells );
		
						// Set Details: Customers.
		
						$( '.psupsellmaster-stats-table-details-customers .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.sum_sales_value );
						$( '.psupsellmaster-stats-table-details-customers .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.countd_customer_id );
						$( '.psupsellmaster-stats-table-details-customers .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.avg_sales_value_per_customer );
		
						// Set Top 5s: Upsells.
						var top_upsells_length = json.top_upsells.length;
		
						for ( var i = 0; i < top_upsells_length; i++ ) {
							var data, row;
		
							data = json.top_upsells[ i ];
							row  = $( '.psupsellmaster-stats-table-top-upsells .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( i );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '<a href="' + data[4] + '" target="_blank">' + data[3] + ' (' + data[1] + ')</a>' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-top-upsells .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.top_upsells_other_sum + ')' );
						$( '.psupsellmaster-stats-table-top-upsells .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.top_upsells_other + '&#37;' );
						$( '.psupsellmaster-stats-table-top-upsells .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Set Top 5s: Base Products.
						var top_base_length = json.top_base.length;
		
						for ( var i = 0; i < top_base_length; i++ ) {
							var data, row;
		
							data = json.top_base[ i ];
							row  = $( '.psupsellmaster-stats-table-top-base-products .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( i );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '<a href="' + data[4] + '" target="_blank">' + data[3] + ' (' + data[1] + ')</a>' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-top-base-products .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.top_base_other_sum + ')' );
						$( '.psupsellmaster-stats-table-top-base-products .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.top_base_other + '&#37;' );
						$( '.psupsellmaster-stats-table-top-base-products .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Set Top 5s: Customers.
						var top_customers_length = json.top_customers.length;
		
						for ( var i = 0; i < top_customers_length; i++ ) {
							var data, row;
		
							data = json.top_customers[ i ];
							row  = $( '.psupsellmaster-stats-table-top-customers .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( i );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '<a href="' + data[4] + '" target="_blank">' + data[3] + ' (' + data[1] + ')</a>' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-top-customers .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.top_customers_other_sum + ')' );
						$( '.psupsellmaster-stats-table-top-customers .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.top_customers_other + '&#37;' );
						$( '.psupsellmaster-stats-table-top-customers .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Set Top 5s: Orders.
						var top_orders_length = json.top_orders.length;
		
						for ( var i = 0; i < top_orders_length; i++ ) {
							var data, row;
		
							data = json.top_orders[ i ];
							row  = $( '.psupsellmaster-stats-table-top-orders .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( i );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '<a href="' + data[4] + '" target="_blank">' + data[3] + ' (' + data[1] + ')</a>' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-top-orders .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.top_orders_other_sum + ')' );
						$( '.psupsellmaster-stats-table-top-orders .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-value' ).html( json.top_orders_other + '&#37;' );
						$( '.psupsellmaster-stats-table-top-orders .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 1 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Locations.
						var top_locations_length = json.top_locations.length;
		
						for ( var i = 0; i < top_locations_length; i++ ) {
							var base, data, row;
		
							data = json.top_locations[ i ];
							base = 0;
		
							switch ( data[0] ) {
								case 'product':           base = 1; break;
								case 'checkout':          base = 2; break;
								case 'purchase_receipt':  base = 3; break;
								case 'widget':            base = 4; break;
								case 'shortcode':         base = 5; break;
								case 'block':             base = 6; break;
								case 'elementor_widget':  base = 7; break;
								case 'popup_add_to_cart': base = 8; break;
								case 'popup_exit_intent': base = 9; break;
							}
		
							if ( base < 1 ) {
								continue;
							}
		
							row = $( '.psupsellmaster-stats-table-details-locations .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( ( base - 1 ) );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + data[1] + ')' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-details-locations .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Sources.
						var top_sources_length = json.top_sources.length;
		
						for ( var i = 0; i < top_sources_length; i++ ) {
							var base, data, row;
		
							data = json.top_sources[ i ];
							base = 0;
		
							switch ( data[0] ) {
								case 'upsells': base = 1; break;
								case 'visits': base = 2; break;
								case 'campaigns': base = 3; break;
							}
		
							if ( base < 1 ) {
								continue;
							}
		
							row = $( '.psupsellmaster-stats-table-details-sources .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( ( base - 1 ) );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + data[1] + ')' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-details-sources .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Types.
						var top_types_length = json.top_types.length;
		
						for ( var i = 0; i < top_types_length; i++ ) {
							var base, data, row;
		
							data = json.top_types[ i ];
							base = 0;
		
							switch ( data[0] ) {
								case 'direct':   base = 1; break;
								case 'indirect': base = 2; break;
								case 'unknown':  base = 3; break;
							}
		
							if ( base < 1 ) {
								continue;
							}
		
							row = $( '.psupsellmaster-stats-table-details-types .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( ( base - 1 ) );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + data[1] + ')' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-details-types .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
						// Views.
						var top_views_length = json.top_views.length;
		
						for ( var i = 0; i < top_views_length; i++ ) {
							var base, data, row;
		
							data = json.top_views[ i ];
							base = 0;
		
							switch ( data[0] ) {
								case 'carousel': base = 1; break;
								case 'list':     base = 2; break;
								case 'unknown':  base = 3; break;
							}
		
							if ( base < 1 ) {
								continue;
							}
		
							row = $( '.psupsellmaster-stats-table-details-views .psupsellmaster-summary-table-body .psupsellmaster-summary-table-row' ).eq( ( base - 1 ) );
		
							row.find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + data[1] + ')' );
							row.find( '.psupsellmaster-stats-table-row-value' ).html( data[2] + '&#37;' );
						}
		
						$( '.psupsellmaster-stats-table-details-views .psupsellmaster-summary-table-footer .psupsellmaster-summary-table-row' ).eq( 0 ).find( '.psupsellmaster-stats-table-row-label' ).html( '&nbsp;(' + json.sum_sales_value + ')' );
		
					}
				).DataTable( datatables_options );
			},
			startTooltips: function startTooltips() {
				// Start the tooltips.
				PsUpsellmasterAdminResults.functions.attachTooltips();
			},
		},
	};

	PsUpsellmasterAdminResults.functions.init();
} )( jQuery );

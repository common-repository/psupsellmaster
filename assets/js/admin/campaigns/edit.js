/**
 * Admin - Campaigns - Edit.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminCampaignsEdit;

	PsUpsellmasterAdminCampaignsEdit = {
		attributes: {},
		events: {
			onChangeAnyLocationsSectionField: function onChangeAnyLocationsSectionField( event ) {
				// Update the location tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsCount();
			},
			onChangeAnyProductField: function onChangeAnyProductField( event ) {
				// Trigger the event.
				$( document ).trigger( 'change.psupsellmaster.section-products' );
			},
			onChangeAnyProductFieldSelect2: function onChangeAnyProductFieldSelect2( event ) {
				// Update the product tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateProductTabsCount();
			},
			onChangeAnyConditionFieldSelect2: function onChangeAnyConditionFieldSelect2( event ) {
				// Update the condition tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateConditionTabsCount();
			},
			onChangeFieldCouponsFlag: function onChangeFieldCouponsFlag( event ) {
				// Get the field.
				var field = $( this );

				// Get the value.
				var value = field.val();

				// Get the rows.
				var rows = field.closest( '.psupsellmaster-form-rows' );

				// Get the warning.
				var warning = rows.find( '.psupsellmaster-form-warning' );

				// Get the subsection.
				var subsection = field.closest( '.psupsellmaster-subsection-coupons' );

				// Get the field campaign.
				var fieldCampaign = subsection.find( '.psupsellmaster-form-field-coupon-code' );

				// Get the field standard.
				var fieldStandard = subsection.find( '.psupsellmaster-form-field-standard-coupon-id' );

				// Check the value.
				if ( 'campaign' === value ) {
					// Hide the warning.
					warning.hide();

					// Hide the field standard.
					fieldStandard.hide();

					// Show the field campaign.
					fieldCampaign.show();

					// Otherwise...
				} else {
					// Show the warning.
					warning.show();

					// Hide the field campaign.
					fieldCampaign.hide();

					// Show the field standard.
					fieldStandard.show();
				}
			},
			onChangeFieldLocations: function onChangeFieldLocations( event ) {
				// Update the location tabs.
				PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsDisplay();
			},
			onChangeFieldLocationsFlag: function onChangeFieldLocationsFlag( event ) {
				// Maybe toggle the location options.
				PsUpsellmasterAdminCampaignsEdit.functions.maybeToggleLocationOptions();

				// Update the location tabs.
				PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsDisplay();
			},
			onChangeFieldProductsFlag: function onChangeFieldProductsFlag( event ) {
				// Maybe toggle the product options.
				PsUpsellmasterAdminCampaignsEdit.functions.maybeToggleProductOptions();
			},
			onChangeSectionProducts: function onChangeSectionProducts( event ) {
				// Clear the timer.
				clearTimeout( PsUpsellmasterAdminCampaignsEdit.timers.datatables.main );

				PsUpsellmasterAdminCampaignsEdit.functions.setDataTableSource( 'preview' );

				// Delay a bit and reload the datatable.
				PsUpsellmasterAdminCampaignsEdit.timers.datatables.main = setTimeout(
					function () {
						// Reload the datatable.
						PsUpsellmasterAdminCampaignsEdit.functions.reloadDataTable();
					},
					2500
				);
			},
			onClickBtnAssignTags: function onClickBtnAssignTags( event ) {
				// Get the button.
				var button = $( this );

				// Get the spinner.
				var spinner = button.closest( '.psupsellmaster-form-btn' ).find( '.spinner' );

				// Set the args.
				var args = {
					callbacks: {
						beforeSend: function () {
							// Disable the button.
							button.prop( 'disabled', true );

							// Add the class.
							spinner.addClass( 'is-active' );
						},
						complete: function () {
							// Reload the datatable.
							PsUpsellmasterAdminCampaignsEdit.functions.reloadDataTable();

							// Enable the button.
							button.prop( 'disabled', false );

							// Remove the class.
							spinner.removeClass( 'is-active' );
						},
					},
				};

				// Assign the tags.
				PsUpsellmasterAdminCampaignsEdit.functions.assignTags( args );
			},
			onClickBtnConfirmDeleteCampaign: function onClickBtnConfirmDeleteCampaign( event ) {
				// Get the checkbox.
				var checkbox = $( this );

				// Get the action.
				var action = checkbox.closest( '.psupsellmaster-action' );

				// Get the button.
				var button = action.find( '.psupsellmaster-btn-delete-campaign' );

				// Check if the checkbox is checked.
				if ( checkbox.is( ':checked' ) ) {
					// Enable the button.
					button.prop( 'disabled', false );
				} else {
					// Disable the button.
					button.prop( 'disabled', true );
				}
			},
			onClickBtnCopyBannerURL: function onClickBtnCopyBannerURL( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the container.
				var container = button.closest( '.psupsellmaster-form-field' );

				// Get the field banner url.
				var fieldBannerUrl = container.find( '.psupsellmaster-field-banner-url' );

				// Copy the text to the clipboard.
				PsUpsellmaster.functions.copyToClipboard( fieldBannerUrl.val() );
			},
			onClickBtnDataTableRefresh: function onClickBtnDataTableRefresh( event ) {
				event.preventDefault();

				// Set the datatable source.
				PsUpsellmasterAdminCampaignsEdit.functions.setDataTableSource( 'preview' );

				// Reload the datatable.
				PsUpsellmasterAdminCampaignsEdit.functions.reloadDataTable();
			},
			onClickBtnDeleteCampaign: function onClickBtnDeleteCampaign( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the id.
				var id = button.attr( 'data-campaign-id' );

				// Delete the campaign.
				PsUpsellmasterAdminCampaignsEdit.functions.deleteCampaigns( ( new Array( id ) ) );
			},
			onClickBtnDuplicateCampaign: function onClickBtnDuplicateCampaign( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the id.
				var id = button.attr( 'data-campaign-id' );

				// Duplicate the campaign.
				PsUpsellmasterAdminCampaignsEdit.functions.duplicateCampaigns( ( new Array( id ) ) );
			},
			onClickBtnSelectBanner: function onClickBtnSelectBanner( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Set the settings.
				var settings = {
					button: {
						title: PsUpsellmasterAdminCampaignsEdit.data.texts.wp_media_btn_title,
					},
					multiple: false,
					title: PsUpsellmasterAdminCampaignsEdit.data.texts.wp_media_frame_title,
				};

				// Get the frame.
				var frame = wp.media( settings );

				// Bind the select event to the frame.
				frame.on(
					'select',
					function () {
						PsUpsellmasterAdminCampaignsEdit.events.onSelectBanner( frame, button );
					}
				);

				// Open the frame.
				frame.open();
			},
			onClickBtnRemoveBanner: function onClickBtnRemoveBanner( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the container.
				var container = button.closest( '.psupsellmaster-form-field' );

				// Set the field banner id value to empty.
				container.find( '.psupsellmaster-field-banner-id' ).val( '' );

				// Set the field banner url value to empty.
				container.find( '.psupsellmaster-field-banner-url' ).val( '' );

				// Get the banner type.
				var type = container.attr( 'data-banner-type' );

				// Get the form.
				var form = button.closest( '.psupsellmaster-form-edit-campaign' );

				// Get the defaults.
				var defaults = form.find( '.psupsellmaster-defaults' );

				// Get the default banner url.
				var url = defaults.find( '.psupsellmaster-default-' + type + '-banner-url' ).val();

				// Get the default banner title.
				var title = defaults.find( '.psupsellmaster-default-banner-title' ).val();

				// Get the default banner height.
				var height = defaults.find( '.psupsellmaster-default-' + type + '-banner-height' ).val();

				// Get the default banner width.
				var width = defaults.find( '.psupsellmaster-default-' + type + '-banner-width' ).val();

				// Set the banner image src attribute to default.
				container.find( '.psupsellmaster-banner-image' ).attr( 'src', url );

				// Set the banner image alt attribute to default.
				container.find( '.psupsellmaster-banner-image' ).attr( 'alt', title );

				// Set the banner image height attribute to default.
				container.find( '.psupsellmaster-banner-image' ).attr( 'height', height );

				// Set the banner image width attribute to default.
				container.find( '.psupsellmaster-banner-image' ).attr( 'width', width );

				// Update the location tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsCount();
			},
			onClickBtnUnassignPostTerm: function onClickBtnUnassignPostTerm( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the post id.
				var postId = button.attr( 'data-post-id' );

				// Get the taxonomy.
				var taxonomy = button.attr( 'data-taxonomy' );

				// Get the term id.
				var termId = button.attr( 'data-term-id' );

				// Set the args.
				var args = {
					postId: postId,
					taxonomy: taxonomy,
					termIds: ( new Array( termId ) ),
					callbacks: {
						success: function ( response ) {
							button.closest( '.psupsellmaster-item' ).remove();
						},
					},
				};

				// Unassign the post term.
				PsUpsellmasterAdminCampaignsEdit.functions.unassignPostTerms( args );
			},
			onClickRepeaterBtnAdd: function onClickRepeaterBtnAdd( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the repeater.
				var repeater = button.closest( '.psupsellmaster-repeater' );

				// Add repeater row.
				PsUpsellmasterAdminCampaignsEdit.functions.addRepeaterRow( repeater );
			},
			onClickConditionRepeaterBtnRemove: function onClickConditionRepeaterBtnRemove( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the row.
				var row = button.closest( '.psupsellmaster-repeater-row' );

				// Remove the row.
				PsUpsellmasterAdminCampaignsEdit.functions.removeRepeaterRow( row );

				// Update the condition tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateConditionTabsCount();
			},
			onClickProductRepeaterBtnRemove: function onClickProductRepeaterBtnRemove( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the row.
				var row = button.closest( '.psupsellmaster-repeater-row' );

				// Remove the row.
				PsUpsellmasterAdminCampaignsEdit.functions.removeRepeaterRow( row );

				// Update the product tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateProductTabsCount();

				// Trigger the event.
				$( document ).trigger( 'change.psupsellmaster.section-products' );
			},
			onClickRowActionExclude: function onClickRowActionExclude( event ) {
				event.preventDefault();

				// Get the button.
				var button = $( this );

				// Get the column.
				var column = button.closest( '.psupsellmaster-col' );

				// Get the product id.
				var productId = column.find( '.psupsellmaster-field-product-id' ).val();

				// Get the product title.
				var prodcutTitle = column.find( '.psupsellmaster-field-product-title' ).val();

				// Get the products section.
				var sectionProducts = $( '.psupsellmaster-tab-section[data-entity="products"]' );

				// Get the exclude section.
				var sectionExclude = sectionProducts.find( '.psupsellmaster-tab-section[data-type="exclude"]' );

				// Get the repeater.
				var repeater = sectionExclude.find( '.psupsellmaster-repeater' );

				// Get the rows.
				var rows = repeater.find( '.psupsellmaster-repeater-row' );

				// Set the found.
				var found = false;

				// Loop through the rows.
				rows.each(
					function () {
						// Get the row.
						var row = $( this );

						// Get the field.
						var field = row.find( '.psupsellmaster-field' );

						// Check if the option is already selected.
						if ( 0 !== field.find( 'option[value="' + productId + '"]:selected' ).length ) {
								// Set the found.
								found = true;
						}
					}
				);

				// Check if the option was found.
				if ( found ) {
					return;
				}

				// Get the last row.
				var row = repeater.find( '.psupsellmaster-repeater-row' ).last();

				// Get the field.
				var field = row.find( '.psupsellmaster-field' );

				// Check if the value is not empty.
				if ( field.val() ) {
					// Add a repeater row.
					PsUpsellmasterAdminCampaignsEdit.functions.addRepeaterRow( repeater );

					// Get the last row.
					row = repeater.find( '.psupsellmaster-repeater-row' ).last();

					// Get the field.
					field = row.find( '.psupsellmaster-field' );
				}

				// Get the flag field.
				var fieldFlag = $( '.psupsellmaster-field-products-flag[value="selected"]' );

				// Check the flag field.
				if ( ! fieldFlag.prop( 'checked' ) ) {
					// Set the flag field value.
					fieldFlag.prop( 'checked', true ).trigger( 'change' );
				}

				// Set the option.
				var option = $(
					'<option>',
					{
						value: productId,
						text: prodcutTitle,
					}
				);

				// Add the new option.
				field.append( option );

				// Set the field value.
				field.val( productId ).trigger( 'change' );
			},
			onDataTableAjaxData: function onDataTableAjaxData( data, settings ) {
				// Get the section.
				var productSelector = $( '.psupsellmaster-product-selector' );

				// Set the data.
				data = $.extend(
					true,
					data,
					{
						action: 'psupsellmaster_ajax_get_campaign_eligible_products',
						nonce: PsUpsellmaster.attributes.ajax.nonce,
						campaign_id: $( '#psupsellmaster-field-campaign-id' ).val(),
						options: PsUpsellmasterAdminCampaignsEdit.functions.getProductSelectorOptions( productSelector ),
						source: $( '.psupsellmaster-datatable-wrapper' ).attr( 'data-source' ),
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
				// Set the button icons and texts.
				$( '.psupsellmaster-datatable-wrapper .buttons-copy' ).html( '<i class="fa fa-copy"></i>&nbsp;' + PsUpsellmasterAdminCampaignsEdit.data.texts.datatable_btn_copy );
				$( '.psupsellmaster-datatable-wrapper .buttons-csv' ).html( '<i class="fa fa-file-csv"></i>&nbsp;' + PsUpsellmasterAdminCampaignsEdit.data.texts.datatable_btn_csv );
				$( '.psupsellmaster-datatable-wrapper .buttons-excel' ).html( '<i class="fa fa-file-excel"></i>&nbsp;' + PsUpsellmasterAdminCampaignsEdit.data.texts.datatable_btn_excel );
				$( '.psupsellmaster-datatable-wrapper .buttons-print' ).html( '<i class="fa fa-print"></i>&nbsp;' + PsUpsellmasterAdminCampaignsEdit.data.texts.datatable_btn_print );

				// Get the datatable.
				var datatable = $( '.psupsellmaster-datatable' );

				// Get the wrapper.
				var wrapper = datatable.closest( '.psupsellmaster-datatable-wrapper' );

				// Get the extra buttons.
				var extraButtons = wrapper.find( '.psupsellmaster-extra-buttons' ).detach();

				// Prepend the extra buttons.
				wrapper.find( '.dt-buttons' ).prepend( extraButtons );

				// Show the extra buttons.
				extraButtons.show();

				// Trigger the event.
				$( document ).trigger( 'psupsellmaster-datatable-init-complete' );
			},
			onDocumentReady: function onDocumentReady() {
				// Register the dynamic events.
				PsUpsellmasterAdminCampaignsEdit.functions.registerDynamicEvents();

				// Start the pikaday fields.
				PsUpsellmasterAdminCampaignsEdit.functions.startPikadayFields();

				// Start the datatables.
				PsUpsellmasterAdminCampaignsEdit.functions.startDataTables();

				// Start the tabs.
				PsUpsellmasterAdminCampaignsEdit.functions.startTabs();

				// Delay a bit so fields can finish loading eg. wp-editor.
				setTimeout(
					function () {
						// Update the product tabs count.
						PsUpsellmasterAdminCampaignsEdit.functions.updateProductTabsCount();

						// Update the condition tabs count.
						PsUpsellmasterAdminCampaignsEdit.functions.updateConditionTabsCount();

						// Update the location tabs count.
						PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsCount();

						// Update the location tabs.
						PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsDisplay();
					},
					2000
				);
			},
			onInputIntegerMaxAmount: function onInputIntegerMaxAmount( event ) {
				// Get the target.
				var target = $( event.target );

				// Check if the target does not have a type equal to number.
				if ( ! target.is( '[type="number"]' ) ) {
					return;
				}

				// Check if the target does not have a max attribute.
				if ( target.is( '[max="*"]' ) ) {
					return;
				}

				// Get the max attribute.
				max = parseInt( target.attr( 'max' ) );

				// Check if the type of the max is not a number.
				if ( typeof max !== 'number' ) {
					return;
				}

				// Get the value.
				var value = parseInt( target.val() );

				// Check if the type of the value is not a number.
				if ( typeof value !== 'number' ) {
					return;
				}

				// Check if the value is greater than the max.
				if ( value > max ) {
					// Set the value.
					target.val( max );
				}
			},
			onInputIntegerMinAmount: function onInputIntegerMinAmount( event ) {
				// Get the target.
				var target = $( event.target );

				// Check if the target does not have a type equal to number.
				if ( ! target.is( '[type="number"]' ) ) {
					return;
				}

				// Check if the target does not have a min attribute.
				if ( target.is( '[min="*"]' ) ) {
					return;
				}

				// Get the min attribute.
				min = parseInt( target.attr( 'min' ) );

				// Check if the type of the min is not a number.
				if ( typeof min !== 'number' ) {
					return;
				}

				// Get the value.
				var value = parseInt( target.val() );

				// Check if the type of the value is not a number.
				if ( typeof value !== 'number' ) {
					return;
				}

				// Check if the value is greater than the min.
				if ( value < min ) {
					// Set the value.
					target.val( min );
				}
			},
			onKeypressIntegerAmount: function onKeypressIntegerAmount( event ) {
				// Set the keys.
				var keys = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ];

				// Return true if the event key is valid, false otherwise.
				return -1 < keys.indexOf( event.key );
			},
			onSelectBanner: function onSelectBanner( frame, button ) {
				// Get the banner.
				var banner = frame.state().get( 'selection' ).first().toJSON();

				// Get the container.
				var container = button.closest( '.psupsellmaster-form-field' );

				// Set the field id value.
				container.find( '.psupsellmaster-field-banner-id' ).val( banner.id );

				// Set the field url value.
				container.find( '.psupsellmaster-field-banner-url' ).val( banner.url );

				// Set the banner image src attribute.
				container.find( '.psupsellmaster-banner-image' ).attr( 'src', banner.url );

				// Get the banner alt attribute.
				var alt = banner.alt || banner.title || banner.filename;

				// Set the banner image alt attribute.
				container.find( '.psupsellmaster-banner-image' ).attr( 'alt', alt );

				// Set the banner image height attribute.
				container.find( '.psupsellmaster-banner-image' ).attr( 'height', banner.height );

				// Set the banner image width attribute.
				container.find( '.psupsellmaster-banner-image' ).attr( 'width', banner.width );

				// Update the location tabs count.
				PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsCount();
			},
			onTabsBeforeActivate: function onTabsBeforeActivate( event, ui ) {
				// Get the action.
				var action = ui.newTab.attr( 'data-action' );

				// Check the action.
				if ( 'reset' === action ) {
					event.preventDefault();

					// Get the tab.
					var tab = $( this );

					// Get the container.
					var container = tab.closest( '.psupsellmaster-tabs' );

					// Get the key.
					var key = container.attr( 'data-key' );

					// Get the sections.
					var sections = container.children( '.psupsellmaster-tab-section' );

					// Loop through the sections.
					sections.each(
						function () {
							// Get the section.
							var section = $( this );

							// Check the key.
							if ( ( new Array( 'conditions', 'products' ).includes( key ) ) ) {
									// Reset the repeaters.
									PsUpsellmasterAdminCampaignsEdit.functions.resetRepeaters( section );

									// Reset the fields.
									PsUpsellmasterAdminCampaignsEdit.functions.resetFields( section );

									// Check the key.
							} else if ( 'locations' === key ) {
								// Reset the fields.
								PsUpsellmasterAdminCampaignsEdit.functions.resetFields( section );
							}

							// Update the tabs meta.
							PsUpsellmasterAdminCampaignsEdit.functions.updateTabsMeta( key );
						}
					);
				}
			},
		},
		functions: {
			addRepeaterRow: function addRepeaterRow( repeater ) {
				// Get the row.
				var row = repeater.find( '.psupsellmaster-repeater-row' ).last();

				// Destroy the select2.
				PsUpsellmaster.select2.functions.destroyAll( { container: row } );

				// Clone the row.
				var clone = row.clone();

				// Reset the fields.
				PsUpsellmasterAdminCampaignsEdit.functions.resetFields( clone );

				// Get the highest index.
				var highestIndex = PsUpsellmasterAdminCampaignsEdit.functions.getHighestRepeaterIndex( repeater );

				// Update the fields index.
				PsUpsellmasterAdminCampaignsEdit.functions.updateRowIndex( clone, ( highestIndex + 1 ) );

				// Get the rows.
				var rows = repeater.find( '.psupsellmaster-repeater-rows' );

				// Add the clone at the end of the rows.
				rows.append( clone );

				// Refresh the select2.
				PsUpsellmaster.select2.functions.refreshAll( { container: row } );

				// Refresh the select2.
				PsUpsellmaster.select2.functions.refreshAll( { container: clone } );
			},
			assignTags: function assignTags( args ) {
				// Get the section.
				var productSelector = $( '.psupsellmaster-product-selector' );

				// Get the options.
				var options = PsUpsellmasterAdminCampaignsEdit.functions.getProductSelectorOptions( productSelector );

				// Check the options.
				if ( ! options ) {
					return;
				}

				// Set the taxonomies.
				var taxonomies = new Object();

				// Loop through the fields.
				$( '.psupsellmaster-subsection-assign-tags .psupsellmaster-field' ).each(
					function () {
						// Get the field.
						var field = $( this );

						// Get the value.
						var value = field.val();

						// Check the value.
						if ( ! value ) {
								return;
						}

						// Get the taxonomy.
						var taxonomy = field.attr( 'data-taxonomy' );

						// Add the value to the list.
						taxonomies[ taxonomy ] = value;
					}
				);

				// Check the taxonomies.
				if ( ! taxonomies ) {
					return;
				}

				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_assign_multiple_product_terms',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					selectors: options,
					taxonomies: taxonomies,
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {
							if ( args.callbacks && args.callbacks.beforeSend ) {
								args.callbacks.beforeSend( xhr );
							}
						},
						success: function ( response ) {
							if ( args.callbacks && args.callbacks.success ) {
								args.callbacks.success( response );
							}
						},
						error: function ( xhr, status, error ) {
							if ( args.callbacks && args.callbacks.error ) {
								args.callbacks.error( xhr, status, error );
							}
						},
						complete: function ( xhr, status ) {
							if ( args.callbacks && args.callbacks.complete ) {
								args.callbacks.complete( xhr, status );
							}
						},
					}
				);
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
						success: function ( response ) {
							// Check the redirect.
							if ( response.redirect && 1 === ids.length ) {
								// Redirect to the url.
								window.location = response.redirect;
							}
						},
						error: function ( xhr, status, error ) {},
						complete: function ( xhr, status ) {},
					}
				);
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
						complete: function ( xhr, status ) {},
					}
				);
			},
			getHighestRepeaterIndex: function getHighestRepeaterIndex( repeater ) {
				// Set the highest.
				var highest = 0;

				// Get the rows.
				var rows = repeater.find( '.psupsellmaster-repeater-row' );

				// Loop through the rows.
				rows.each(
					function () {
						// Get the row.
						var row = $( this );

						// Get the index.
						var index = parseInt( row.attr( 'data-index' ) );

						// Check if the index is higher than the highest.
						if ( index > highest ) {
								// Set the highest.
								highest = index;
						}
					}
				);

				// Return the highest.
				return highest;
			},
			getProductSelectorOptions: function getProductSelectorOptions( productSelector ) {
				// Set the options.
				var options = {
					authors: new Object(),
					prices: new Object(),
					products: new Object(),
					products_flag: false,
					products_type: false,
					taxonomies: new Object(),
				};

				// Get the products flag.
				var productsFlag = productSelector.find( '.psupsellmaster-field-products-flag:checked' );

				// Set the options.
				options.products_flag = productsFlag.val().trim();

				// Get the form options.
				var formOptions = productSelector.find( '.psupsellmaster-form-options' );

				// Get the products type.
				var productsType = formOptions.find( '.psupsellmaster-field-products-type' );

				// Set the options.
				options.products_type = productsType.is( ':checked' ) ? productsType.val().trim() : false;

				// Get the entity.
				var entityAuthors = formOptions.find( '.psupsellmaster-tab-section[data-entity="authors"]' );

				// Get the include.
				var includeAuthors = entityAuthors.find( '.psupsellmaster-tab-section[data-type="include"]' );

				// Get the exclude.
				var excludeAuthors = entityAuthors.find( '.psupsellmaster-tab-section[data-type="exclude"]' );

				// Set the options.
				options.authors = {
					include: includeAuthors.find( '.psupsellmaster-select2' ).map(
						function () {
							return parseInt( ( $( this ).val() || '' ).trim() );
						}
					).get().filter( Number ),
				exclude: excludeAuthors.find( '.psupsellmaster-select2' ).map(
					function () {
						return parseInt( ( $( this ).val() || '' ).trim() );
					}
				).get().filter( Number ),
				};

				// Get the prices min.
				var pricesMin = formOptions.find( '.psupsellmaster-field-prices-min' );

				// Get the prices max.
				var pricesMax = formOptions.find( '.psupsellmaster-field-prices-max' );

				// Set the options.
				options.prices = {
					min: parseFloat( pricesMin.val().trim() ) || '',
					max: parseFloat( pricesMax.val().trim() ) || '',
				};

				// Get the entity.
				var entityProducts = formOptions.find( '.psupsellmaster-tab-section[data-entity="products"]' );

				// Get the include.
				var includeProducts = entityProducts.find( '.psupsellmaster-tab-section[data-type="include"]' );

				// Get the exclude.
				var excludeProducts = entityProducts.find( '.psupsellmaster-tab-section[data-type="exclude"]' );

				// Set the options.
				options.products = {
					include: includeProducts.find( '.psupsellmaster-select2' ).map(
						function () {
							return parseInt( ( $( this ).val() || '' ).trim() );
						}
					).get().filter( Number ),
				exclude: excludeProducts.find( '.psupsellmaster-select2' ).map(
					function () {
						return parseInt( ( $( this ).val() || '' ).trim() );
					}
				).get().filter( Number ),
				};

				// Get the entity.
				var entityTaxonomies = formOptions.find( '.psupsellmaster-tab-section[data-entity="taxonomies"]' );

				// Loop through the taxonomies.
				entityTaxonomies.each(
					function () {
						// Get the entity.
						var entity = $( this );

						// Get the taxonomy.
						var taxonomy = entity.attr( 'data-taxonomy' );

						// Get the include.
						var includeTerms = entity.find( '.psupsellmaster-tab-section[data-type="include"]' );

						// Get the exclude.
						var excludeTerms = entity.find( '.psupsellmaster-tab-section[data-type="exclude"]' );

						// Set the options.
						options.taxonomies[ taxonomy ] = {
							include: includeTerms.find( '.psupsellmaster-select2' ).map(
								function () {
									return parseInt( ( $( this ).val() || '' ).trim() );
								}
							).get().filter( Number ),
						exclude: excludeTerms.find( '.psupsellmaster-select2' ).map(
							function () {
								return parseInt( ( $( this ).val() || '' ).trim() );
							}
						).get().filter( Number ),
						};
					}
				);

				// Return the options.
				return options;
			},
			init: function init() {
				PsUpsellmasterAdminCampaignsEdit.functions.registerAttributes();
				PsUpsellmasterAdminCampaignsEdit.functions.registerEvents();
				PsUpsellmasterAdminCampaignsEdit.functions.run();
			},
			maybeToggleLocationOptions: function maybeToggleLocationOptions() {
				// Get the section.
				var section = $( '.psupsellmaster-section-locations' );

				// Get the field.
				var field = section.find( '.psupsellmaster-field-locations-flag:checked' );

				// Get the value.
				var value = field.val();

				// Get the options.
				var options = field.closest( '.psupsellmaster-form-group' ).find( '.psupsellmaster-form-options' );

				// Check the value.
				if ( 'all' === value ) {
					// Hide the options.
					options.hide();

					// Return early.
					return;
				}

				// Show the options.
				options.show();
			},
			maybeToggleProductOptions: function maybeToggleProductOptions() {
				// Get the section.
				var section = $( '.psupsellmaster-subsection-products-selection' );

				// Get the field.
				var field = section.find( '.psupsellmaster-field-products-flag:checked' );

				// Get the value.
				var value = field.val();

				// Get the options.
				var options = section.find( '.psupsellmaster-form-options' );

				// Check the value.
				if ( 'all' === value ) {
					// Hide the options.
					options.hide();

					// Return early.
					return;
				}

				// Show the options.
				options.show();
			},
			registerAttributes: function registerAttributes() {
				// Check the attributes from the server.
				if ( 'undefined' !== typeof psupsellmaster_admin_data_campaigns_edit ) {
					// Set the attributes.
					PsUpsellmasterAdminCampaignsEdit.data = psupsellmaster_admin_data_campaigns_edit;
				}

				// Set the timers.
				PsUpsellmasterAdminCampaignsEdit.timers = {
					datatables: {
						main: new Object(),
					},
				};

				// Set the settings.
				PsUpsellmasterAdminCampaignsEdit.settings = {
					datatables: {
						default: new Object(),
							custom: {
								main: new Object(),
						},
					},
				};

				// Set the datatable settings: default.
				PsUpsellmasterAdminCampaignsEdit.settings.datatables.default = {
					ajax: {
						data: PsUpsellmasterAdminCampaignsEdit.events.onDataTableAjaxData,
						dataSrc: PsUpsellmasterAdminCampaignsEdit.events.onDataTableAjaxDataSrc,
						type: 'POST',
						url: PsUpsellmaster.attributes.ajax.url,
					},
					buttons: [
						{
							className: 'button',
							extend: 'copyHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsEdit.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'print',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsEdit.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'csvHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsEdit.events.onDataTableExportFormatBody,
								},
							},
						},
						{
							className: 'button',
							extend: 'excelHtml5',
							exportOptions: {
								format: {
									body: PsUpsellmasterAdminCampaignsEdit.events.onDataTableExportFormatBody,
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
							orderable: false,
							targets: [ 2 ],
						},
						{
							createdCell: PsUpsellmasterAdminCampaignsEdit.events.onDataTableCreatedCell,
							targets: '_all',
						},
					],
					createdRow: PsUpsellmasterAdminCampaignsEdit.events.onDataTableCreatedRow,
					displayLength: 10,
					dom: 'Bfliptip',
					filter: true,
					initComplete: PsUpsellmasterAdminCampaignsEdit.events.onDataTableInitComplete,
					oLanguage: {
						sLengthMenu: PsUpsellmasterAdminCampaignsEdit.data.texts.datatable_length,
					},
					order: [ [ 0, 'asc' ] ],
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
				PsUpsellmasterAdminCampaignsEdit.settings.datatables.custom.main = $.extend(
					true,
					new Object(),
					PsUpsellmasterAdminCampaignsEdit.settings.datatables.default
				);

				// Set the instances.
				PsUpsellmasterAdminCampaignsEdit.instances = {
					datatables: {
						main: null,
					},
				};
			},
			registerDynamicEvents: function registerDynamicEvents() {
				// Loop through the fields.
				$( '.psupsellmaster-section-locations .psupsellmaster-field-description' ).each(
					function () {
						// Get the field.
						var field = $( this );

						// Get the editor.
						var editor = tinymce.get( field.attr( 'id' ) );

						// Check if the editor was not found.
						if ( ! editor ) {
								// Continue the loop.
								return true;
						}

						// Bind the change event to the editor.
						editor.on( 'change', PsUpsellmasterAdminCampaignsEdit.events.onChangeAnyLocationsSectionField );
					}
				);
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminCampaignsEdit.events.onDocumentReady );
				$( document ).on( 'click', '.psupsellmaster-section-promotion-conditions .psupsellmaster-repeater-btn-add', PsUpsellmasterAdminCampaignsEdit.events.onClickRepeaterBtnAdd );
				$( document ).on( 'click', '.psupsellmaster-subsection-products-selection .psupsellmaster-repeater-btn-add', PsUpsellmasterAdminCampaignsEdit.events.onClickRepeaterBtnAdd );
				$( document ).on( 'click', '.psupsellmaster-section-promotion-conditions .psupsellmaster-repeater-btn-remove', PsUpsellmasterAdminCampaignsEdit.events.onClickConditionRepeaterBtnRemove );
				$( document ).on( 'click', '.psupsellmaster-subsection-products-selection .psupsellmaster-repeater-btn-remove', PsUpsellmasterAdminCampaignsEdit.events.onClickProductRepeaterBtnRemove );
				$( document ).on( 'click', '.psupsellmaster-subsection-assign-tags .psupsellmaster-btn-assign-tags', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnAssignTags );
				$( document ).on( 'change', '.psupsellmaster-field-coupons-flag', PsUpsellmasterAdminCampaignsEdit.events.onChangeFieldCouponsFlag );
				$( document ).on( 'change', '.psupsellmaster-field-products-flag', PsUpsellmasterAdminCampaignsEdit.events.onChangeFieldProductsFlag );
				$( document ).on( 'change', '.psupsellmaster-subsection-products-selection .psupsellmaster-select2', PsUpsellmasterAdminCampaignsEdit.events.onChangeAnyProductFieldSelect2 );
				$( document ).on( 'change', '.psupsellmaster-subsection-products-selection .psupsellmaster-field', PsUpsellmasterAdminCampaignsEdit.events.onChangeAnyProductField );
				$( document ).on( 'change', '.psupsellmaster-section-locations .psupsellmaster-field', PsUpsellmasterAdminCampaignsEdit.events.onChangeAnyLocationsSectionField );
				$( document ).on( 'change', '.psupsellmaster-section-promotion-conditions .psupsellmaster-select2', PsUpsellmasterAdminCampaignsEdit.events.onChangeAnyConditionFieldSelect2 );
				$( document ).on( 'change', '.psupsellmaster-field-locations-flag', PsUpsellmasterAdminCampaignsEdit.events.onChangeFieldLocationsFlag );
				$( document ).on( 'change', '.psupsellmaster-field-locations', PsUpsellmasterAdminCampaignsEdit.events.onChangeFieldLocations );
				$( document ).on( 'click', '.psupsellmaster-btn-select-banner', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnSelectBanner );
				$( document ).on( 'click', '.psupsellmaster-btn-copy-banner-url', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnCopyBannerURL );
				$( document ).on( 'click', '.psupsellmaster-btn-remove-banner', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnRemoveBanner );
				$( document ).on( 'click', '.psupsellmaster-btn-duplicate-campaign', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnDuplicateCampaign );
				$( document ).on( 'click', '.psupsellmaster-field-confirm-delete-campaign', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnConfirmDeleteCampaign );
				$( document ).on( 'click', '.psupsellmaster-btn-delete-campaign', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnDeleteCampaign );
				$( document ).on( 'click', '.psupsellmaster-datatable-wrapper .psupsellmaster-btn-refresh', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnDataTableRefresh );
				$( document ).on( 'change.psupsellmaster.section-products', PsUpsellmasterAdminCampaignsEdit.events.onChangeSectionProducts );
				$( document ).on( 'input', '.psupsellmaster-field-priority', PsUpsellmasterAdminCampaignsEdit.events.onInputIntegerMaxAmount );
				$( document ).on( 'input', '.psupsellmaster-field-priority', PsUpsellmasterAdminCampaignsEdit.events.onInputIntegerMinAmount );
				$( document ).on( 'keypress', '.psupsellmaster-field-priority', PsUpsellmasterAdminCampaignsEdit.events.onKeypressIntegerAmount );
				$( document ).on( 'click', '.psupsellmaster-unassign-post-term', PsUpsellmasterAdminCampaignsEdit.events.onClickBtnUnassignPostTerm );
				$( document ).on( 'click', '.psupsellmaster-row-actions .psupsellmaster-exclude', PsUpsellmasterAdminCampaignsEdit.events.onClickRowActionExclude );
			},
			reloadDataTable: function reloadDataTable() {
				// Reload the DataTable.
				PsUpsellmasterAdminCampaignsEdit.instances.datatables.main.ajax.reload();
			},
			removeRepeaterRow: function removeRepeaterRow( row ) {
				// Get the repeater.
				var repeater = row.closest( '.psupsellmaster-repeater' );

				// Check if the repeater has more than one row.
				if ( 1 === repeater.find( '.psupsellmaster-repeater-row' ).length ) {
					// Reset the fields.
					PsUpsellmasterAdminCampaignsEdit.functions.resetFields( row );

					// Update the fields index.
					PsUpsellmasterAdminCampaignsEdit.functions.updateRowIndex( row );

					// Refresh the select2.
					PsUpsellmaster.select2.functions.refreshAll( { container: row } );

					// Return early.
					return;
				}

				// Remove the row.
				row.remove();
			},
			resetFields: function resetFields( container ) {
				// Get the fields.
				var fields = container.find( 'input:not(:checkbox,:radio), select, textarea' );

				// Reset the fields.
				fields.val( '' );

				// Trigger the change event.
				fields.trigger( 'change' );

				// Get the fields.
				fields = container.find( 'input:checkbox, input:radio' );

				// Loop through the fields.
				fields.each(
					function () {
						// Get the fields.
						var field = $( this );

						// Set the checked attribute.
						field.prop( 'checked', ( 'true' === field.attr( 'data-default-checked' ) ) );
					}
				);

				// Trigger the change event.
				fields.trigger( 'change' );
			},
			resetRepeater: function resetRepeater( repeater ) {
				// Remove all rows except the first.
				repeater.find( '.psupsellmaster-repeater-row' ).not( ':first' ).remove();

				// Get the row.
				var row = repeater.find( '.psupsellmaster-repeater-row' );

				// Reset the fields.
				PsUpsellmasterAdminCampaignsEdit.functions.resetFields( row );

				// Update the fields index.
				PsUpsellmasterAdminCampaignsEdit.functions.updateRowIndex( row );

				// Refresh the select2.
				PsUpsellmaster.select2.functions.refreshAll( { container: row } );
			},
			resetRepeaters: function resetRepeaters( container ) {
				// Loop through the repeaters.
				container.find( '.psupsellmaster-repeater' ).each(
					function () {
						// Reset the repeater.
						PsUpsellmasterAdminCampaignsEdit.functions.resetRepeater( $( this ) );
					}
				);
			},
			updateConditionTabsCount: function updateConditionTabsCount() {
				// Get the section.
				var section = $( '.psupsellmaster-section-promotion-conditions' );

				// Get the tabs.
				var tabs = section.find( '.psupsellmaster-tabs' ).first();

				// Get the header.
				var header = tabs.find( '.psupsellmaster-tabs-header' ).first();

				// Loop through the tabs.
				header.find( '.psupsellmaster-tab' ).each(
					function () {
						// Get the tab.
						var tab = $( this );

						// Get the key.
						var key = tab.find( 'a' ).attr( 'href' );

						// Get the target.
						var target = section.find( key );

						// Get the inner header.
						var innerHeader = target.find( '.psupsellmaster-tabs-header' ).first();

						// Set the count.
						var count = 0;

						// Loop through the inner tabs.
						innerHeader.find( '.psupsellmaster-tab' ).each(
							function () {
								// Get the inner tab.
								var innerTab = $( this );

								// Get the inner key.
								var innerKey = innerTab.find( 'a' ).attr( 'href' );

								// Get the inner target.
								var innerTarget = target.find( innerKey );

								// Get the inner count.
								var innerCount = innerTarget.find( '.psupsellmaster-select2 option:selected:not(:empty)' ).length;

								// Set the text.
								innerTab.find( '.psupsellmaster-count' ).text( '(' + innerCount + ')' );

								// Increase the count.
								count += innerCount;
							}
						);

						// Set the count.
						tab.find( '.psupsellmaster-count' ).text( '(' + count + ')' );
					}
				);
			},
			updateLocationTabsDisplay: function updateLocationTabsDisplay() {
				// Get the section.
				var section = $( '.psupsellmaster-section-locations' );

				// Get the locations flag.
				var flag = section.find( '.psupsellmaster-field-locations-flag:checked' ).val();

				// Get the locations.
				var locations = section.find( '.psupsellmaster-field-locations' ).val();

				// Get the tabs.
				var tabs = section.find( '.psupsellmaster-tabs' ).first();

				// Get the header.
				var header = tabs.find( '.psupsellmaster-tabs-header' ).first();

				// Loop through the tabs.
				header.find( '.psupsellmaster-tab' ).each(
					function () {
						// Get the tab.
						var tab = $( this );

						// Get the location.
						var location = tab.attr( 'data-location' );

						// Set the show.
						var show = true;

						// Check the flag.
						if ( 'all' !== flag && 0 !== locations.length ) {
								// Check the locations.
							if ( 'all' !== location && ! locations.includes( location ) ) {
								// Set the show.
								show = false;
							}
						}

						// Check the show.
						if ( show ) {
							// Show the tab.
							tab.show();
						} else {
							// Hide the tab.
							tab.hide();
						}
					}
				);

				// Check if the active tab is not visible.
				if ( header.find( '.psupsellmaster-tab.ui-tabs-active' ).is( ':hidden' ) ) {
					// Get the first visible tab.
					var tab = header.find( '.psupsellmaster-tab:visible' ).first();

					// Activate the tab.
					tabs.tabs( 'option', 'active', tab.index() );
				}
			},
			updateLocationTabsCount: function updateLocationTabsCount() {
				// Get the section.
				var section = $( '.psupsellmaster-section-locations' );

				// Get the header.
				var header = section.find( '.psupsellmaster-tabs-header' ).first();

				// Loop through the tabs.
				header.find( '.psupsellmaster-tab' ).each(
					function () {
						// Get the tab.
						var tab = $( this );

						// Get the key.
						var key = tab.find( 'a' ).attr( 'href' );

						// Get the target.
						var target = section.find( key );

						// Get the field description.
						var fieldDescription = target.find( '.psupsellmaster-form-field-description .psupsellmaster-field-description' );

						// Get the field desktop banner id.
						var fieldDesktopBannerId = target.find( '.psupsellmaster-form-field-desktop-banner .psupsellmaster-field' );

						// Get the field mobile banner id.
						var fieldMobileBannerId = target.find( '.psupsellmaster-form-field-mobile-banner .psupsellmaster-field' );

						// Get the field desktop banner url.
						var fieldDesktopBannerUrl = target.find( '.psupsellmaster-form-field-desktop-banner-link-url .psupsellmaster-field' );

						// Get the field mobile banner url.
						var fieldMobileBannerUrl = target.find( '.psupsellmaster-form-field-mobile-banner-link-url .psupsellmaster-field' );

						// Set the has data.
						var hasData = false;

						// Set the has data.
						hasData = hasData || ! ! tinymce.get( fieldDescription.attr( 'id' ) ).getContent().trim();
						hasData = hasData || ! ! ( parseInt( fieldDesktopBannerId.val() ) || 0 );
						hasData = hasData || ! ! ( parseInt( fieldMobileBannerId.val() ) || 0 );
						hasData = hasData || ! ! fieldDesktopBannerUrl.val().trim();
						hasData = hasData || ! ! fieldMobileBannerUrl.val().trim();

						// Check the has data.
						if ( hasData ) {
								// Set the text.
								tab.find( '.psupsellmaster-count' ).text( '(+)' );
						} else {
							// Set the text.
							tab.find( '.psupsellmaster-count' ).text( '' );
						}
					}
				);
			},
			updateProductTabsCount: function updateProductTabsCount() {
				// Get the section.
				var section = $( '.psupsellmaster-subsection-products-selection' );

				// Get the options.
				var options = section.find( '.psupsellmaster-form-options' );

				// Get the header.
				var header = options.find( '.psupsellmaster-tabs-header' ).first();

				// Loop through the tabs.
				header.find( '.psupsellmaster-tab' ).each(
					function () {
						// Get the tab.
						var tab = $( this );

						// Get the key.
						var key = tab.find( 'a' ).attr( 'href' );

						// Get the target.
						var target = section.find( key );

						// Get the inner header.
						var innerHeader = target.find( '.psupsellmaster-tabs-header' ).first();

						// Set the count.
						var count = 0;

						// Loop through the inner tabs.
						innerHeader.find( '.psupsellmaster-tab' ).each(
							function () {
								// Get the inner tab.
								var innerTab = $( this );

								// Get the inner key.
								var innerKey = innerTab.find( 'a' ).attr( 'href' );

								// Get the inner target.
								var innerTarget = target.find( innerKey );

								// Get the inner count.
								var innerCount = innerTarget.find( '.psupsellmaster-select2 option:selected:not(:empty)' ).length;

								// Set the text.
								innerTab.find( '.psupsellmaster-count' ).text( '(' + innerCount + ')' );

								// Increase the count.
								count += innerCount;
							}
						);

						// Set the count.
						tab.find( '.psupsellmaster-count' ).text( '(' + count + ')' );
					}
				);
			},
			updateRowIndex: function updateRowIndex( row, index = 0 ) {
				// Set the row index.
				row.attr( 'data-index', index );

				// Get the fields.
				var fields = row.find( 'input, select, textarea' );

				// Loop through the fields.
				fields.each(
					function () {
						// Get the field.
						var field = $( this );

						// Get the attribute id.
						var attributeId = field.attr( 'id' );

						// Check if the attribute id was found.
						if ( attributeId ) {
								// Replace the index within the attribute id.
								attributeId = attributeId.replace( /(\d+)/, index );

								// Set the attribute id.
								field.attr( 'id', attributeId );
						}

						// Get the attribute name.
						var attributeName = field.attr( 'name' );

						// Check if the attribute name was found.
						if ( attributeName ) {
							// Replace the index within the attribute name.
							attributeName = attributeName.replace( /\[(\d+)\]/, '[' + index + ']' );

							// Set the attribute name.
							field.attr( 'name', attributeName );
						}
					}
				);
			},
			updateTabsMeta: function updateTabsMeta( key ) {
				// Check the key.
				if ( 'conditions' === key ) {
					// Update the tabs count.
					PsUpsellmasterAdminCampaignsEdit.functions.updateConditionTabsCount();

				} else if ( 'locations' === key ) {
					// Update the tabs count.
					PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsCount();

					// Update the tabs display.
					PsUpsellmasterAdminCampaignsEdit.functions.updateLocationTabsDisplay();

				} else if ( 'products' === key ) {
					// Update the tabs count.
					PsUpsellmasterAdminCampaignsEdit.functions.updateProductTabsCount();
				}
			},
			run: function run() {
				// Focus on the title field.
				$( '.psupsellmaster-section-general .psupsellmaster-form-field-title .psupsellmaster-field' ).trigger( 'focus' );
			},
			setDataTableSource: function setDataTableSource( source ) {
				// Get the wrapper.
				var wrapper = $( '.psupsellmaster-datatable-wrapper' );

				// Set the source.
				wrapper.attr( 'data-source', source );
			},
			startDataTables: function startDataTables() {
				// Start new datatable.
				PsUpsellmasterAdminCampaignsEdit.instances.datatables.main = $( '#psupsellmaster-datatable-eligible-products' ).DataTable( PsUpsellmasterAdminCampaignsEdit.settings.datatables.custom.main );
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
						PsUpsellmasterAdminCampaignsEdit.functions.startPikadayField( field );
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
			startTabs: function startTabs() {
				// Start the tabs.
				$( '.psupsellmaster-tabs' ).tabs(
					{
						beforeActivate: PsUpsellmasterAdminCampaignsEdit.events.onTabsBeforeActivate,
					}
				);

				// Show the tabs.
				$( '.psupsellmaster-tabs' ).show();
			},
			unassignPostTerms: function unassignPostTerms( args ) {
				// Set the data.
				var data = {
					action: 'psupsellmaster_ajax_unassign_post_terms',
					nonce: PsUpsellmaster.attributes.ajax.nonce,
					post_id: args.postId,
					taxonomy: args.taxonomy,
					term_ids: args.termIds,
				};

				// Make the ajax request.
				$.ajax(
					{
						type: 'post',
						url: PsUpsellmaster.attributes.ajax.url,
						data: data,
						beforeSend: function ( xhr ) {
							if ( args.callbacks && args.callbacks.beforeSend ) {
								args.callbacks.beforeSend( xhr );
							}
						},
						success: function ( response ) {
							if ( args.callbacks && args.callbacks.success ) {
								args.callbacks.success( response );
							}
						},
						error: function ( xhr, status, error ) {
							if ( args.callbacks && args.callbacks.error ) {
								args.callbacks.error( xhr, status, error );
							}
						},
						complete: function ( xhr, status ) {
							if ( args.callbacks && args.callbacks.complete ) {
								args.callbacks.complete( xhr, status );
							}
						},
					}
				);
			},
		},
	};

	PsUpsellmasterAdminCampaignsEdit.functions.init();
} )( jQuery );

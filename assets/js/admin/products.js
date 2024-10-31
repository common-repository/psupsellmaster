/**
 * Admin - Products.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminProducts;

	PsUpsellmasterAdminProducts = {
		attributes: {},
		events: {
			handleSelect2BeforeBuild: function handleSelect2BeforeBuild( event, args ) {
				// Get the id attribute.
				const id = args.field.attr( 'id' );

				// Get the container.
				const container = args.field.closest( '.psupsellmaster-field-container' );

				// Get the label.
				const label = container.find( '.psupsellmaster-field-label' );

				// Check if the id is not defined.
				if ( undefined === id || 0 === id.length ) {
					// Get the context.
					const context = args.field.attr( 'data-context' );

					// Get the id from the data attribute.
					let dataId = args.field.attr( 'data-id-quick-edit' );

					// Check if the context is bulk edit.
					if ( 'bulk_edit' === context ) {
						// Get the id from the data attribute.
						dataId = args.field.attr( 'data-id-bulk-edit' );
					}

					/*
					In this scenario, setting the id attribute directly in HTML won't work.
					It leads to multiple elements having the same ID in WordPress.
					Select2 requires unique IDs to function correctly.
					*/

					args.field.attr( 'id', dataId );

					// Set the label for attribute.
					label.attr( 'for', dataId );
				}
			},
			onClickBtnQuickEdit: function onClickBtnQuickEdit( event ) {
				// Get the button.
				const button = $( this );

				// Get the row.
				const row = button.closest( 'tr' );

				// Get the table body.
				const tbody = button.closest( 'tbody' );

				// Get the quick edit box.
				const box = tbody.find( '.colspanchange' );

				// Set the quick edit fields data.
				PsUpsellmasterAdminProducts.functions.setQuickEditFieldsData( row, box );

				// Refresh the select2.
				PsUpsellmaster.select2.functions.refreshAll( { container: box } );
			},
			onClickFieldEnableUpsell: function onClickFieldEnableUpsell( event ) {
				// Get the checkbox.
				var checkbox = $( this );

				// Get the fieldset.
				var fieldset = checkbox.closest( '.psupsellmaster-quick-edit-fieldset' );

				// Get the status.
				var status = fieldset.find( '.psupsellmaster-scores-status' );

				// Get the container.
				var container = fieldset.find( '.psupsellmaster-fields-container' );

				// Check if the checkbox is checked.
				if ( checkbox.is( ':checked' ) ) {
					// Set the status value.
					status.val( 'enabled' );

					// Show the fields container.
					container.show();
				} else {
					// Set the status value.
					status.val( 'disabled' );

					// Hide the fields container.
					container.hide();
				}

			},
			onReady: function onReady() {
				// Build all the select2 fields within the container.
				PsUpsellmaster.select2.functions.buildAll( { container: $( '#bulk-edit' ) } );
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminProducts.functions.registerEvents();
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminProducts.events.onReady );
				$( document ).on( 'change', '.psupsellmaster-field-toggle-scores', PsUpsellmasterAdminProducts.events.onClickFieldEnableUpsell );
				$( document ).on( 'click', '#the-list .editinline', PsUpsellmasterAdminProducts.events.onClickBtnQuickEdit );
				$( document ).on( 'psupsellmaster.select2.beforeBuild', PsUpsellmasterAdminProducts.events.handleSelect2BeforeBuild )
			},
			setQuickEditFieldsData: function setQuickEditFieldsData( row, box ) {
				// Get the fields.
				const fields = row.find( '.psupsellmaster-hidden-field' );

				// Loop through the fields.
				fields.each(
					function () {
						// Get the field.
						const field = $( this );

						// Get the value.
						const value = field.val() || '';

						// Get the target.
						const target = box.find( field.data( 'target-field' ) );

						// Check the target.
						if ( ! target ) {
							// Skip.
							return;
						}

						// Check if the target is a checkbox.
						if ( target.is( ':checkbox' ) ) {
							// Set the target value.
							target.prop( 'checked', ! ! value ).trigger( 'change' );

							// Check if the target is a select.
						} else if ( target.is( 'select' ) ) {
							// Set the target options.
							target.html( field.html() ).trigger( 'change' );
						}
					}
				);
			},
		},
	};

	PsUpsellmasterAdminProducts.functions.init();
} )( jQuery );

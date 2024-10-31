( function( $, window, undefined ) {

	// Set the attributes.
	const attributes = {
		selector: '.psupsellmaster-select2',
	};

	// Set the events.
	const events = {
		onReady: function onReady() {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeOnReady' );

			// Build.
			functions.buildAll( { skipDeferred: true } );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterOnReady' );
		},
	};

	// Set the functions.
	const functions = {
		init: function init() {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeInit' );

			// Setup.
			functions.setup();

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterInit' );
		},
		build: function build( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeBuild', args );

			// Start the select2.
			args.field.select2( functions.getSettings( args ) );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterBuild', args );
		},
		buildAll: function buildAll( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeBuildAll', args );

			// Get the fields.
			const fields = args.container ? args.container.find( attributes.selector ) : $( attributes.selector );

			// Loop through the fields.
			fields.each( function() {
				// Get the field.
				const field = $( this );

				// Check the defer option.
				if ( args.skipDeferred && 'true' === field.attr( 'data-select2-defer' ) ) {
					// Skip.
					return;
				}

				// Build the field.
				functions.build( { ...args, field } );
			} );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterBuildAll', args );
		},
		clear: function clear( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeClear', args );

			// Clear the field.
			args.field.val( '' ).trigger( 'change' );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterClear', args );
		},
		clearAll: function clearAll( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeClearAll', args );

			// Get the fields.
			const fields = args.container ? args.container.find( attributes.selector ) : $( attributes.selector );

			// Loop through the fields.
			fields.each( function() {
				// Get the field.
				const field = $( this );

				// Clear the field.
				functions.clear( { ...args, field } );
			} );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterClearAll', args );
		},
		destroy: function destroy( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeDestroy', args );

			// Check if the field is using select2.
			if ( args.field.data( 'select2' ) ) {
				// Destroy the field.
				args.field.select2( 'destroy' );
			}

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterDestroy', args );
		},
		destroyAll: function destroyAll( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeDestroyAll', args );

			// Get the fields.
			const fields = args.container ? args.container.find( attributes.selector ) : $( attributes.selector );

			// Loop through the fields.
			fields.each( function() {
				// Get the field.
				const field = $( this );

				// Destroy the field.
				functions.destroy( { ...args, field } );
			} );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterDestroyAll', args );
		},
		getSettings: function getSettings( args = {} ) {
			// Set the settings.
			let settings = {
				width: '100%',
			};

			// Check if the field has a parent.
			const parent = args.field.closest( args.field.attr( 'data-args.container' ) );

			// Check if the field has a parent.
			if ( parent && 0 !== parent.length ) {
				settings.dropdownParent = parent;
			}

			// Check if the field has ajax action.
			if ( args.field.attr( 'data-ajax-action' ) ) {
				// Get the url.
				const url = args.field.attr( 'data-ajax-url' );

				// Set the ajax settings.
				settings.ajax = {
					data: function data( parameters ) {
						// Set the query.
						const query = new Object();

						// Set the action.
						query.action = args.field.attr( 'data-ajax-action' );

						// Set the nonce.
						query.nonce = args.field.attr( 'data-ajax-nonce' );

						// Set the page.
						query.page = parameters.page || 1;

						// Set the search.
						query.search = parameters.term;

						// Check if the field has a group.
						if ( args.field.attr( 'data-group' ) ) {
							// Set the group.
							query.group = args.field.attr( 'data-group' );
						}

						// Check if the field has a taxonomy.
						if ( args.field.attr( 'data-taxonomy' ) ) {
							// Set the taxonomy.
							query.taxonomy = args.field.attr( 'data-taxonomy' );
						}

						// Trigger the query event.
						args.field.trigger( 'psupsellmaster.select2.query', query );

						// Return the query.
						return query;
					},
					dataType: 'json',
					delay: 250,
					processResults: function processResults( response, parameters ) {
						// Set the options.
						const options = new Array();

						// Check if the response is fine.
						if ( response.success && response.items ) {
							// Loop through the response items.
							response.items.forEach(
								function ( item ) {
									// Add a new option to the options list.
									options.push( { id: item.value, text: item.label } );
								}
							);
						}

						// Set the more.
						const more = response.meta.total_pages > ( parameters.page || 1 );

						// Return the data.
						return {
							results: options,
							pagination: {
								more: more,
							},
						};
					},
					url: url,
				};

				// Get the min.
				const min = args.field.attr( 'data-ajax-input-min' );

				// Check if the field allows clearing values.
				if ( min ) {
					// Set the min.
					settings.minimumInputLength = min;
				}
			}

			// Check if the field allows clearing values.
			if ( 'true' === args.field.attr( 'data-clear' ) ) {
				// Set the clear.
				settings.allowClear = true;
			}

			// Check if the field allows custom values.
			if ( 'true' === args.field.attr( 'data-custom' ) ) {
				// Set the tags.
				settings.tags = true;
			}

			// Check if the field allows multiple values.
			if ( 'true' === args.field.attr( 'data-multiple' ) ) {
				// Set the multiple.
				settings.multiple = true;

				// Get the multiple limit.
				const multipleLimit = args.field.attr( 'data-multiple-limit' );
	
				// Check if the field has a limit for multiple values.
				if ( multipleLimit ) {
					// Set the limit.
					settings.maximumSelectionLength = multipleLimit;
				}
			}

			// Get the field placeholder.
			const placeholder = args.field.attr( 'data-placeholder' );

			// Check if the placeholder does exist.
			if ( placeholder ) {
				// Set the placeholder.
				settings.placeholder = placeholder;
			}

			// Allow developers to use this.
			args.field.trigger( 'psupsellmaster.select2.settings', settings );

			// Return the settings.
			return settings;
		},
		refresh: function refresh( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeRefresh', args );

			// Destroy the field.
			functions.destroy( args );

			// Build the field.
			functions.build( args );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterRefresh', args );
		},
		refreshAll: function refreshAll( args = {} ) {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeRefreshAll', args );

			// Get the fields.
			const fields = args.container ? args.container.find( attributes.selector ) : $( attributes.selector );

			// Loop through the fields.
			fields.each( function() {
				// Get the field.
				const field = $( this );

				// Refresh the field.
				functions.refresh( { ...args, field } );
			} );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterRefreshAll', args );
		},
		setup: function setup() {
			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.beforeSetup' );

			// Register the events.
			$( document ).on( 'psupsellmaster.ready', events.onReady );

			// Allow developers to use this.
			$( document ).trigger( 'psupsellmaster.select2.afterSetup' );
		},
	};

	// Set the select2.
	PsUpsellmaster.select2 = {
		attributes: attributes,
		events: events,
		functions: functions,
	};

	// Init.
	PsUpsellmaster.select2.functions.init();

} )( jQuery, window );

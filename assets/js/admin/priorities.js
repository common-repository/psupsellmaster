/**
 * Admin - Priorities.
 *
 * @package PsUpsellMaster.
 */

( function ( $ ) {
	var PsUpsellmasterAdminPriorities;

	PsUpsellmasterAdminPriorities = {
		attributes: {},
		events: {
			onChangeLogarithmicScale: function onChangeLogarithmicScale( event ) {
				// Get the field.
				var field = $( this );

				// Get the table.
				var table = field.closest( '.psupsellmaster-priorities-table' );

				// Get the max weight field.
				var max = table.find( '.psupsellmaster-max-weight' );

				// Check if the field is checked.
				if ( field.is( ':checked' ) ) {
					// Hide the max field.
					max.closest( '.psupsellmaster-table-col-field' ).hide();
				} else {
					// Show the max field.
					max.closest( '.psupsellmaster-table-col-field' ).show();
				}

				// Update the range number.
				PsUpsellmasterAdminPriorities.functions.updateRangeNumber();

				// Update the range slider.
				PsUpsellmasterAdminPriorities.functions.updateRangeSlider();

				// Trigger the change event to update the sliders.
				$( '.psupsellmaster-range-number' ).trigger( 'change' );
			},
			onChangeMaxWeight: function onChangeMaxWeight( event ) {
				// Update the range number.
				PsUpsellmasterAdminPriorities.functions.updateRangeNumber();

				// Update the range slider.
				PsUpsellmasterAdminPriorities.functions.updateRangeSlider();
			},
			onChangeRangeNumber: function onChangeRangeNumber( event ) {
				// Get the field.
				var field = $( this );

				// Get the table.
				var table = field.closest( '.psupsellmaster-priorities-table' );

				// Get the is logarithmic.
				var isLogarithmic = table.find( '.psupsellmaster-logarithmic-scale' ).is( ':checked' );

				// Set the value.
				var value = '';

				// Check if it is logarithmic.
				if ( isLogarithmic ) {
					// Get the value.
					value = field.val().replace( /[,.]/g, '' );
					value = Number( value ) || 0;
					value = ( new Array( 0, 1 ) ).includes( value ) ? value : Math.round( ( Math.log( value ) / Math.log( 10 ) ) + 1 );
				} else {
					// Get the value.
					value = field.val().replace( /[,.]/g, '' );
					value = Number( value ) || 0;
				}

				// Get the row.
				var row = field.closest( '.psupsellmaster-table-row' );

				// Get the slider field.
				var slider = row.find( '.psupsellmaster-range-slider' );

				// Update the slider field value.
				slider.val( value );
			},
			onChangeRangeSlider: function onChangeRangeSlider( event ) {
				// Get the field.
				var field = $( this );

				// Get the table.
				var table = field.closest( '.psupsellmaster-priorities-table' );

				// Get the is logarithmic.
				var isLogarithmic = table.find( '.psupsellmaster-logarithmic-scale' ).is( ':checked' );

				// Set the value.
				var value = '';

				// Check if it is logarithmic.
				if ( isLogarithmic ) {
					// Get the value.
					value = field.val().replace( /[,.]/g, '' );
					value = Number( value ) || 0;
					value = ( new Array( 0, 1 ) ).includes( value ) ? value : Math.pow( 10, ( value - 1 ) );
				} else {
					// Get the value.
					value = field.val().replace( /[,.]/g, '' );
					value = Number( value ) || 0;
				}

				// Get the row.
				var row = field.closest( '.psupsellmaster-table-row' );

				// Get the number field.
				var number = row.find( '.psupsellmaster-range-number' );

				// Update the number field value.
				number.val( value.toLocaleString() );
			},
			onChangeSlideToggle: function onChangeSlideToggle( event ) {
				// Get the element.
				var element = $( this );

				// Get the container.
				var container = element.closest( '.psupsellmaster-fields-container' );

				// Get the selector.
				var selector = element.data( 'target-slide-toggle' );

				// Get the target.
				var target = container.find( selector );

				// Slide toggle.
				target.slideToggle();
			},
			onDocumentReady: function onDocumentReady() {
				// Update the range slider.
				PsUpsellmasterAdminPriorities.functions.updateRangeSlider();

				// Refresh the screen.
				PsUpsellmasterAdminPriorities.functions.refreshScreen();
			},
			onKeyupLocale: function onKeyupLocale( event ) {
				// Get the field.
				var field = $( this );

				// Get the value.
				var value = field.val();

				// Remove all characters but digits from the value.
				value = value.replace( /[^0-9]/g, '' );
				value = Number( value ) || 0;

				// Get the max value.
				var max = field.data( 'max' );

				// Check if the max is not undefined.
				if ( undefined !== max ) {
					// Check if the value is greater than max.
					if ( value > max ) {
						// Set the value to max.
						value = max;
					}
				}

				// Get the min value.
				var min = field.data( 'min' );

				// Check if the min is not undefined.
				if ( undefined !== min ) {
					// Check if the value is lower than min.
					if ( value < min ) {
						// Set the value to min.
						value = min;
					}
				}

				// Update the number field value.
				field.val( value.toLocaleString() );
			},
		},
		functions: {
			init: function init() {
				PsUpsellmasterAdminPriorities.functions.registerEvents();
			},
			refreshScreen: function refreshScreen() {
				// Trigger the change event to hide or show fields.
				$( '.psupsellmaster-logarithmic-scale' ).trigger( 'change' );

				// Trigger the change event to update the sliders.
				$( '.psupsellmaster-range-number' ).trigger( 'change' );

				// Trigger the keyup event to update the number formats.
				$( '.psupsellmaster-range-number' ).trigger( 'keyup' );
				$( '.psupsellmaster-max-weight' ).trigger( 'keyup' );
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmasterAdminPriorities.events.onDocumentReady );
				$( document ).on( 'change', '.psupsellmaster-logarithmic-scale', PsUpsellmasterAdminPriorities.events.onChangeLogarithmicScale );
				$( document ).on( 'change', '.psupsellmaster-max-weight', PsUpsellmasterAdminPriorities.events.onChangeMaxWeight );
				$( document ).on( 'change', '.psupsellmaster-range-number', PsUpsellmasterAdminPriorities.events.onChangeRangeNumber );
				$( document ).on( 'change', '.psupsellmaster-range-slider', PsUpsellmasterAdminPriorities.events.onChangeRangeSlider );
				$( document ).on( 'keyup', '.psupsellmaster-max-weight', PsUpsellmasterAdminPriorities.events.onKeyupLocale );
				$( document ).on( 'keyup', '.psupsellmaster-range-number', PsUpsellmasterAdminPriorities.events.onKeyupLocale );
				$( document ).on( 'change', '.psupsellmaster-trigger-slide-toggle', PsUpsellmasterAdminPriorities.events.onChangeSlideToggle );
			},
			updateRangeNumber: function updateRangeNumber() {
				// Get the table.
				var table = $( '.psupsellmaster-priorities-table' );

				// Get the logarithmic field.
				var logarithmic = table.find( '.psupsellmaster-logarithmic-scale' );

				// Get the number field.
				var number = table.find( '.psupsellmaster-range-number' );

				// Set the max.
				var max = 0;

				// Check if the scale is logarithmic.
				if ( logarithmic.is( ':checked' ) ) {
					// Set the max.
					max = logarithmic.data( 'max' );
				} else {
					// Set the max.
					max = ( table.find( '.psupsellmaster-max-weight' ).val() || '' ).replace( /[,.]/g, '' );
					max = Number( max ) || logarithmic.data( 'max' );
				}

				// Set the number max attribute.
				number.data( 'max', max );

				// Trigger the keyup event to update the number fields according to the new max weight.
				number.trigger( 'keyup' );
			},
			updateRangeSlider: function updateRangeSlider() {
				// Get the table.
				var table = $( '.psupsellmaster-priorities-table' );

				// Get the logarithmic field.
				var logarithmic = table.find( '.psupsellmaster-logarithmic-scale' );

				// Get the slider field.
				var slider = table.find( '.psupsellmaster-range-slider' );

				// Set the max.
				var max = 0;

				// Set the min.
				var min = 0;

				// Set the step.
				var step = 0;

				// Check if the scale is logarithmic.
				if ( logarithmic.is( ':checked' ) ) {
					// Set the max.
					max = slider.data( 'log-max' );

					// Set the min.
					min = slider.data( 'log-min' );

					// Set the step.
					step = slider.data( 'log-step' );
				} else {
					// Set the max.
					max = ( table.find( '.psupsellmaster-max-weight' ).val() || '' ).replace( /[,.]/g, '' );
					max = Number( max ) || slider.data( 'normal-max' );

					// Set the min.
					min = slider.data( 'normal-min' );

					// Set the step.
					step = Math.ceil( max / 100 );
				}

				// Set the slider max attribute.
				slider.attr( 'max', max );

				// Set the slider min attribute.
				slider.attr( 'min', min );

				// Set the slider step attribute.
				slider.attr( 'step', step );
			},
		},
	};

	// Init.
	PsUpsellmasterAdminPriorities.functions.init();
} )( jQuery );

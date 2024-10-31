/**
 * Main.
 *
 * @package PsUpsellMaster.
 */

var PsUpsellmaster;

( function ( $ ) {
	PsUpsellmaster = {
		attributes: {},
		events: {
			onClickTriggerCloseModal: function onClickTriggerCloseModal( event ) {
				event.preventDefault();

				var element, target;

				// Get the element.
				element = $( this );

				// Get the target.
				target = $( element.data( 'target' ) );

				// If the target is not defined, get the closest modal.
				if ( 0 === target.length ) {
					// Set the target.
					target = element.closest( '.psupsellmaster-modal' );
				}

				// Trigger the before event.
				element.trigger( 'before-close.psupsellmaster.modal' );

				// Close the modal.
				PsUpsellmaster.functions.closeModal( target );

				// Trigger the after event.
				element.trigger( 'after-close.psupsellmaster.modal' );
			},
			onClickTriggerOpenModal: function onClickTriggerOpenModal( event ) {
				event.preventDefault();

				var element, target;

				// Get the element.
				element = $( this );

				// Get the target.
				target = $( element.data( 'target' ) );

				// Trigger the before event.
				element.trigger( 'before-open.psupsellmaster.modal' );

				// Open the modal.
				PsUpsellmaster.functions.openModal( target );

				// Trigger the after event.
				element.trigger( 'after-open.psupsellmaster.modal' );
			},
			onDocumentReady: function onDocumentReady() {
				// Allow developers to use this.
				$( document ).trigger( 'psupsellmaster.ready' );
			},
		},
		functions: {
			copyToClipboard: function copyToClipboard( text ) {
				// Check if the clipboard is supported.
				if ( navigator && navigator.clipboard && navigator.clipboard.writeText ) {
					// Copy the text to the clipboard.
					navigator.clipboard.writeText( text );

					// Otherwise...
				} else {
					// Create a textarea.
					var input = document.createElement( 'textarea' );

					// Set the input value.
					input.value = text;

					// Set the input style.
					input.style.position = 'fixed';

					// Append the input to the body.
					document.body.appendChild( input );

					// Select the input contents.
					input.select();

					// Execute the copy command.
					document.execCommand( 'copy' );

					// Remove the input from the body.
					document.body.removeChild( input );
				}
			},
			closeModal: function closeModal( target ) {
				var backdrop, body;

				// Get the body.
				body = $( 'body' );

				// Get the backdrop.
				backdrop = $( '.psupsellmaster-modal-backdrop' );

				// If the target is not defined, get all open modals.
				if ( ! target || 0 === target.length ) {
					// Set the target.
					target = body.find( '.psupsellmaster-modal.psupsellmaster-show' );
				}

				// Trigger the before event.
				target.trigger( 'before-close.psupsellmaster.modal' );

				// Remove classes.
				body.removeClass( 'psupsellmaster-modal-open' );
				target.removeClass( 'psupsellmaster-show' );
				backdrop.removeClass( 'psupsellmaster-show' );

				// Hide elements.
				target.hide();
				backdrop.hide();

				// Trigger the after event.
				target.trigger( 'after-close.psupsellmaster.modal' );
			},
			getCookie: function getCookie( name ) {
				// Set the cookie key.
				var cookieKey = name + '=';

				// Get the cookies.
				var cookies = document.cookie.split( ';' );

				// Set the loop length.
				var loopLength = cookies.length;

				// Loop through the cookies.
				for ( var i = 0; i < loopLength; i++ ) {
					// Get the cookie.
					var cookie = cookies[ i ];

					// Trim the cookie.
					while ( ' ' === cookie.charAt( 0 ) ) {
						// Remove the first character.
						cookie = cookie.substring( 1 );
					}

					// Check if the cookie was found.
					if ( 0 === cookie.indexOf( cookieKey ) ) {
						// Return the cookie value.
						return decodeURIComponent( cookie.substring( cookieKey.length ) );
					}
				}

				// Return null.
				return null;
			},
			init: function init() {
				PsUpsellmaster.functions.registerAttributes();
				PsUpsellmaster.functions.registerEvents();
			},
			openModal: function openModal( target ) {
				var backdrop, body;

				// Get the body.
				body = $( 'body' );

				// Get the backdrop.
				backdrop = $( '.psupsellmaster-modal-backdrop' );

				// Trigger the before event.
				target.trigger( 'before-open.psupsellmaster.modal' );

				// Add classes.
				body.addClass( 'psupsellmaster-modal-open' );
				target.addClass( 'psupsellmaster-show' );
				backdrop.addClass( 'psupsellmaster-show' );

				// Show elements.
				backdrop.show();
				target.show();

				// Trigger the after event.
				target.trigger( 'after-open.psupsellmaster.modal' );
			},
			registerAttributes: function registerAttributes() {

				if ( 'undefined' !== typeof psupsellmaster_data_main ) {
					PsUpsellmaster.attributes = psupsellmaster_data_main;
				}

				// Set the page attribute.
				PsUpsellmaster.attributes.page = { reload: false };
			},
			registerEvents: function registerEvents() {
				$( PsUpsellmaster.events.onDocumentReady );
				$( document ).on( 'click', '.psupsellmaster-trigger-open-modal', PsUpsellmaster.events.onClickTriggerOpenModal );
				$( document ).on( 'click', '.psupsellmaster-trigger-close-modal', PsUpsellmaster.events.onClickTriggerCloseModal );
			},
			reload: function reload() {

				// Check if the reload is already set to true.
				if ( PsUpsellmaster.attributes.page.reload ) {
					return false;
				}

				// Reload the page.
				window.location.reload();

				// Set the reload to true.
				PsUpsellmaster.attributes.page.reload = true;
			},
			removeCookie: function removeCookie( name ) {
				// Remove the cookie.
				document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
			},
			setCookie: function setCookie( name, value, days ) {
				// Create the expiration date.
				var expirationDate = new Date();

				// Set the expiration date.
				expirationDate.setDate( expirationDate.getDate() + days );

				// Set the cookie value.
				var cookieValue = encodeURIComponent( value ) + '; expires=' + expirationDate.toUTCString() + '; path=/';

				// Set the cookie.
				document.cookie = name + '=' + cookieValue;
			},
		},
	};

	PsUpsellmaster.functions.init();
} )( jQuery );

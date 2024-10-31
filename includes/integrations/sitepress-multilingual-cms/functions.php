<?php
/**
 * Integrations - WPML - Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run on init.
 * Compatibility with WPML.
 * The settings must be reloaded for the translation to work.
 */
function psupsellmaster_wpml_init() {
	// Reload the settings.
	PsUpsellMaster_Settings::load();
}
add_action( 'wpml_init', 'psupsellmaster_wpml_init' );

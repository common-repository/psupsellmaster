<?php
/**
 * Functions - Deprecated.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if a plugin is enabled.
 *
 * @deprecated 2.0.6 Use psupsellmaster_is_plugin_active instead.
 * @param string $plugin_key The plugin key.
 * @return bool Whether the plugin is enabled.
 */
function psupsellmaster_is_plugin_enabled( $plugin_key ) {
	return psupsellmaster_is_plugin_active( $plugin_key );
}

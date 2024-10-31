<?php
/**
 * LITE - Admin - Functions - WP Plugins.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add plugin action links to the WordPress plugins list.
 *
 * @param array $actions The plugin action links.
 * @return array The plugin action links.
 */
function psupsellmaster_lite_plugin_action_links( $actions ) {
	$upgrade_link = sprintf(
		/* translators: 1: tags start, 2: tags end. */
		__( '%1$sUpgrade to PRO!%2$s', 'psupsellmaster' ),
		'<a href="' . PSUPSELLMASTER_PRODUCT_URL . '" target="_blank" style="color:#10c026;"><b>',
		'</b></a>'
	);

	// Add the upgrade link to the beginning of the list.
	array_unshift( $actions, $upgrade_link );

	// Allow developers to filter this.
	$actions = apply_filters( 'psupsellmaster_lite_plugin_action_links', $actions );

	// Return the actions.
	return $actions;
}
add_filter( 'psupsellmaster_plugin_action_links', 'psupsellmaster_lite_plugin_action_links' );

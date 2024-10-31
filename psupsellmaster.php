<?php
/**
 * Plugin Name:       UpsellMaster
 * Plugin URI:        https://www.pluginsandsnippets.com/downloads/upsellmaster/
 * Description:       UpsellMaster increases website conversion by adding tailored upsell suggestions for WooCommerce and Easy Digital Downloads webshops. Choose between displaying upsells (selected automatically), time-limited discount campaigns or recently viewed products and systematically see an increase in order values.
 * Version:           2.0.21
 * Author:            Plugins & Snippets
 * Author URI:        https://pluginsandsnippets.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       psupsellmaster
 * Requires at least: 6.2
 * Tested up to:      6.5
 *
 * @package           PsUpsellMaster
 * @author            PluginsandSnippets.com
 * @copyright         All rights reserved Copyright (c) 2019, PluginsandSnippets.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $psupsellmaster_init;

// Set the global.
$psupsellmaster_init = (
	isset( $psupsellmaster_init ) && is_array( $psupsellmaster_init ) ?
	$psupsellmaster_init : array()
);

// Set the global data.
$psupsellmaster_init['basenames'] = (
	isset( $psupsellmaster_init['basenames'] ) && is_array( $psupsellmaster_init['basenames'] ) ?
	$psupsellmaster_init['basenames'] : array()
);

// Add the basename to the list.
array_push( $psupsellmaster_init['basenames'], plugin_basename( __FILE__ ) );

/**
 * Run on plugins_loaded.
 */
add_action(
	'plugins_loaded',
	function () {
		global $psupsellmaster_init;

		// Check if the global is not valid.
		if ( ! isset( $psupsellmaster_init ) ) {
			// Abort - do NOT init the plugin.
			return;
		}

		// Set the current basename.
		$psupsellmaster_init['current_basename'] = plugin_basename( __FILE__ );

		// Set the official basenames.
		$basenames = array(
			'lite' => 'psupsellmaster/psupsellmaster.php',
			'pro'  => 'psupsellmaster-pro/psupsellmaster.php',
		);

		// Set the unknown basename.
		$unknown_basename = ! in_array( plugin_basename( __FILE__ ), $basenames, true );

		// Set the multiple installs.
		$multiple_installs = 1 !== count( $psupsellmaster_init['basenames'] );

		// Set the is lite.
		$is_lite = plugin_basename( __FILE__ ) === $basenames['lite'];

		// Check if it should abort.
		if ( $unknown_basename || ( $multiple_installs && $is_lite ) ) {
			/**
			 * Run on admin_init.
			 */
			add_action(
				'admin_init',
				function() {
					// Deactivate the plugin.
					deactivate_plugins( array( plugin_basename( __FILE__ ) ) );
				}
			);

			// Include the files.
			include_once plugin_dir_path( __FILE__ ) . 'includes/functions-abort.php';

			// Abort - do NOT init the plugin.
			return;
		}

		// Set the base constants.
		define( 'PSUPSELLMASTER_VER', '2.0.21' );
		define( 'PSUPSELLMASTER_NAME', 'UpsellMaster' );
		define( 'PSUPSELLMASTER_FILE', __FILE__ );
		define( 'PSUPSELLMASTER_DIR', plugin_dir_path( __FILE__ ) );
		define( 'PSUPSELLMASTER_URL', plugin_dir_url( __FILE__ ) );

		// Require the main class.
		require_once PSUPSELLMASTER_DIR . 'includes/class-psupsellmaster.php';

		// Init the plugin.
		PsUpsellMaster::init();
	}
);

/**
 * Run on activation.
 */
register_activation_hook( __FILE__, function() {
	// Allow developers to use this.
	do_action( 'psupsellmaster_activate' );

	// Check the query parameters.
	if ( ! isset( $_GET['action'], $_GET['plugin'] ) ) {
		return;
	}

	// Check the query parameters.
	if ( 'activate' !== $_GET['action'] ) {
		return;
	}

	// Check the query parameters.
	if ( plugin_basename( __FILE__ ) !== $_GET['plugin'] ) {
		return;
	}

	// Set the trasient.
	set_transient( 'psupsellmaster_activate' , true, 3000 );
} );

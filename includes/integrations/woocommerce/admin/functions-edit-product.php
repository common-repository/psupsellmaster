<?php
/**
 * Integrations - WooCommerce - Admin - Functions - Edit Product.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a new tab to the product data metabox.
 *
 * @param array $tabs The tabs.
 * @return array The tabs.
 */
function psupsellmaster_woo_product_data_tabs( $tabs ) {
	// Set the tabs.
	$tabs['psupsellmaster_settings'] = array(
		'label'    => PSUPSELLMASTER_NAME,
		'target'   => 'psupsellmaster_product_data',
		'class'    => array(),
		'priority' => 70,
	);

	// Return the tabs.
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'psupsellmaster_woo_product_data_tabs', 10, 1 );

/**
 * Adds a new panel to the product data metabox.
 */
function psupsellmaster_woo_product_data_panels() {
	global $post;
	?>
	<div id="psupsellmaster_product_data" class="panel woocommerce_options_panel"><?php psupsellmaster_render_product_meta_box_upsells( $post ); ?></div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'psupsellmaster_woo_product_data_panels' );

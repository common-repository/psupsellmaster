<?php
/**
 * Integrations - WooCommerce - Functions - Pages.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if this is the purchase receipt page in WooCommerce.
 *
 * @return bool Whether or not this is the purchase receipt page.
 */
function psupsellmaster_woo_is_page_purchase_receipt() {
	return is_wc_endpoint_url( 'view-order' ) || is_wc_endpoint_url( 'order-received' );
}

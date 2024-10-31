<?php
/**
 * Integrations - Easy Digital Downloads - Functions - Pages.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if this is the purchase receipt page in Easy Digital Downloads.
 *
 * @return bool Whether or not this is the purchase receipt page.
 */
function psupsellmaster_edd_is_page_purchase_receipt() {
	return edd_is_success_page();
}

/**
 * Check if this is the purchase history page in Easy Digital Downloads.
 *
 * @return bool Whether or not this is the purchase history page.
 */
function psupsellmaster_edd_is_page_purchase_history() {
	return edd_is_purchase_history_page();
}

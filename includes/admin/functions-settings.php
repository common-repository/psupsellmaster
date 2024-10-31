<?php
/**
 * Admin - Functions - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the settings tabs.
 *
 * @return array the tabs.
 */
function psupsellmaster_admin_get_settings_tabs() {
	// Set the tabs.
	$tabs = array(
		'general'           => array(
			'label'    => __( 'General Settings', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_general',
			'slug'     => 'general',
		),
		'upsells'           => array(
			'label'    => __( 'Algorithm Logic', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_upsells',
			'slug'     => 'upsells',
		),
		'page_product'      => array(
			'label'    => __( 'Product Page', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_page_product',
			'slug'     => 'product-page',
		),
		'page_checkout'     => array(
			'label'    => __( 'Checkout Page', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_page_checkout',
			'slug'     => 'checkout-page',
		),
		'popup_add_to_cart' => array(
			'label'    => __( 'Add to Cart Popup', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_popup_add_to_cart',
			'slug'     => 'add-to-cart-popup',
		),
		'campaigns'         => array(
			'label'    => __( 'Campaigns', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_campaigns',
			'slug'     => 'campaigns',
		),
		'help'              => array(
			'label'    => __( 'Help', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_help',
			'slug'     => 'help',
		),
		'more'              => array(
			'label'    => __( 'More', 'psupsellmaster' ),
			'callback' => 'psupsellmaster_admin_render_settings_tab_more',
			'slug'     => 'more',
		),
	);

	// Allow developers to filter this.
	$tabs = apply_filters( 'psupsellmaster_admin_settings_tabs', $tabs );

	// Return the tabs.
	return $tabs;
}

/**
 * Render a settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab( $tab_key ) {
	// Set the key.
	$file_key = str_replace( '_', '-', $tab_key );

	// Require the template.
	require_once PSUPSELLMASTER_DIR . "includes/admin/templates/settings/{$file_key}.php";
}

/**
 * Render the general settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_general( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the upsells settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_upsells( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the page_product settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_page_product( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the page_checkout settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_page_checkout( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the popup_add_to_cart settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_popup_add_to_cart( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the campaigns settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_campaigns( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the help settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_help( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the more settings tab.
 *
 * @param string $tab_key the tab key.
 */
function psupsellmaster_admin_render_settings_tab_more( $tab_key ) {
	psupsellmaster_admin_render_settings_tab( $tab_key );
}

/**
 * Render the newsletter.
 */
function psupsellmaster_render_newsletter() {
	// Check if the newsletter is subscribed.
	if ( psupsellmaster_is_newsletter_subscribed() ) {
		return false;
	}

	require PSUPSELLMASTER_DIR . 'includes/admin/templates/newsletter.php';
}
add_action( 'psupsellmaster_before_settings', 'psupsellmaster_render_newsletter' );

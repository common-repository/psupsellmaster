<?php
/**
 * Admin - Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update the settings.
 */
function psupsellmaster_update_settings() {
	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster_nonce_settings'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce_settings'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	if ( isset( $_SERVER['REQUEST_URI'] ) && ( strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/wp-admin/admin.php' ) !== false )
			&& isset( $_REQUEST['page'] )
			&& ( 'psupsellmaster-settings' === sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) )
			&& ( isset( $_POST['number_of_upsell_products'] ) ) ) {

		PsUpsellMaster_Settings::update();

		$setting_link = '<a href="' . admin_url( 'admin.php' ) . '?page=psupsellmaster-products">'
			. __( 'Recalculate Upsells here', 'psupsellmaster' ) . '</a>';

		/* translators: 1: Setting Page Link */
		psupsellmaster_add_flash_notice( sprintf( __( 'Settings updated, %s ', 'psupsellmaster' ), $setting_link ), 'notice', true );
	}
}
add_action( 'admin_init', 'psupsellmaster_update_settings' );

/**
 * Render the newsletter popup.
 */
function psupsellmaster_render_newsletter_popup() {
	// Check if the newsletter is subscribed.
	if ( psupsellmaster_is_newsletter_subscribed() ) {
		return false;
	}

	// Set the show popup.
	$show_popup = false;

	// Check the admin page.
	if ( psupsellmaster_admin_is_page( 'wp-plugins' ) ) {
		// Set the show popup.
		$show_popup = true;

		// Check the admin page.
	} elseif ( psupsellmaster_admin_is_page( 'settings' ) ) {
		// Set the show popup.
		$show_popup = true;

		// Check the admin page.
	} elseif ( psupsellmaster_admin_is_page( 'results' ) ) {
		// Set the show popup.
		$show_popup = true;

		// Check the admin page.
	} elseif ( psupsellmaster_admin_is_page( 'upsells' ) ) {
		// Set the show popup.
		$show_popup = true;
	}

	// Check if the popup should not be shown.
	if ( ! $show_popup ) {
		return false;
	}

	// Require the template.
	require PSUPSELLMASTER_DIR . 'includes/admin/templates/popups/newsletter.php';

	// Require the template.
	require_once PSUPSELLMASTER_DIR . 'includes/admin/templates/popups/backdrop.php';
}
add_action( 'admin_footer', 'psupsellmaster_render_newsletter_popup' );


/**
 * Render the feedback popup.
 */
function psupsellmaster_render_feedback_popup() {
	// Set the show popup.
	$show_popup = false;

	// Check the admin page.
	if ( psupsellmaster_admin_is_page( 'wp-plugins' ) ) {
		// Set the show popup.
		$show_popup = true;
	}

	// Check if the popup should not be shown.
	if ( ! $show_popup ) {
		return false;
	}

	// Require the template.
	require PSUPSELLMASTER_DIR . 'includes/admin/templates/popups/feedback-v' . PSUPSELLMASTER_FEEDBACK_VERSION . '.php';

	// Require the template.
	require_once PSUPSELLMASTER_DIR . 'includes/admin/templates/popups/backdrop.php';
}
add_action( 'admin_footer', 'psupsellmaster_render_feedback_popup' );

/**
 * Check and run actions when the plugin type has been changed.
 * The type might be changed from Lite to PRO or from PRO to Lite.
 * It will run downgrade or upgrade actions as per the type.
 */
function psupsellmaster_admin_init_type_changed() {
	// Get the value.
	$value = get_transient( 'psupsellmaster_type_changed' );

	// Check if it refers to a downgrade (from PRO to Lite).
	if ( 'pro_lite' === $value ) {
		// Run downgrade actions.
		do_action( 'psupsellmaster_type_init_downgrade' );

		// Check if it refers to an upgrade (from Lite to PRO).
	} elseif ( 'lite_pro' === $value ) {
		// Run upgrade actions.
		do_action( 'psupsellmaster_type_init_upgrade' );
	}
}
add_action( 'admin_init', 'psupsellmaster_admin_init_type_changed' );

/**
 * Render clear results related notices.
 */
function psupsellmaster_admin_render_clear_results_notices() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Results cleared successfully.', 'psupsellmaster' ); ?></p>
		<button class="notice-dismiss" type="button">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'psupsellmaster' ); ?></span>
		</button>
	</div>
	<?php
}

/**
 * Clear the upsell results.
 */
function psupsellmaster_admin_clear_results() {
	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster_nonce_clear_results'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce_clear_results'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	// Truncate the results database table.
	$truncated = psupsellmaster_db_results_truncate();

	// Check the truncated.
	if ( $truncated ) {
		// Render the notices.
		add_action( 'admin_notices', 'psupsellmaster_admin_render_clear_results_notices' );
	}
}
add_action( 'admin_init', 'psupsellmaster_admin_clear_results' );

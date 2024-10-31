<?php
/**
 * Functions - Abort.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run on admin_notices.
 */
add_action(
	'admin_notices',
	function () {
		global $psupsellmaster_init;

		// Get the basenames.
		$basenames = array(
			'lite' => 'psupsellmaster/psupsellmaster.php',
			'pro'  => 'psupsellmaster-pro/psupsellmaster.php',
		);

		// Check if the basename is not allowed.
		if ( ! in_array( $psupsellmaster_init['current_basename'], $basenames, true ) ) {
			// Show the notice.
			printf(
				'<div class="notice notice-error">
					<p><strong>%1$s</strong></p>
					<p>%2$s.<br />%3$s <strong>%4$s</strong>.<br />%5$s <strong>%6$s</strong> %7$s <strong>%8$s</strong>.<br />%9$s.</p>
				</div>',
				esc_html__( 'Heads up!', 'psupsellmaster' ),
				esc_html__( 'UpsellMaster is not fully loaded due to an invalid installation', 'psupsellmaster' ),
				esc_html__( 'The current installation is', 'psupsellmaster' ),
				esc_html( $psupsellmaster_init['current_basename'] ),
				esc_html__( 'Please ensure the installation matches either', 'psupsellmaster' ),
				esc_html( $basenames['lite'] . ' (LITE)' ),
				esc_html__( 'or', 'psupsellmaster' ),
				esc_html( $basenames['pro'] . ' (PRO)' ),
				esc_html__( 'Kindly (1) uninstall the plugin and (2) install the official version', 'psupsellmaster' )
			);
		}

		// Check if there are multiple installs.
		if ( 1 !== count( $psupsellmaster_init['basenames'] ) ) {
			// Check if the basename refers to the lite version.
			if ( $psupsellmaster_init['current_basename'] === $basenames['lite'] ) {
				// Show the notice.
				printf(
					'<div class="notice notice-warning">
						<p><strong>%1$s</strong></p>
						<p>%2$s</p>
					</div>',
					esc_html__( 'Heads up!', 'psupsellmaster' ),
					esc_html__( 'Your site already has UpsellMaster PRO activated. If you want to switch to UpsellMaster LITE, please first go to Plugins → Installed Plugins and deactivate UpsellMaster PRO. Then, you can activate UpsellMaster LITE.', 'psupsellmaster' )
				);
			}
		}
	},
	500
);

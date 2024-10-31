<?php
/**
 * Admin - Templates - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the settings tabs.
$settings_tabs = psupsellmaster_admin_get_settings_tabs();

// Set the tab prefix.
$tab_prefix = 'psupsellmaster-settings-tab-';

// Includes the thickbox so we can use the wp-admin modal window.
add_thickbox();

?>
<div class="wrap">
	<h1><?php esc_html_e( 'UpsellMaster Settings', 'psupsellmaster' ); ?></h1>
	<div class="psupsellmaster-settings">
		<?php do_action( 'psupsellmaster_before_settings' ); ?>
		<form method="post">
			<input name="psupsellmaster_nonce_settings" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>" />
			<div id="psupsellmaster-settings-tabs">
				<ul id="psupsellmaster-settings-tabs-header">
					<?php foreach ( $settings_tabs as $tab_key => $tab_data ) : ?>
						<?php
						// Set the tab target.
						$tab_target = "#{$tab_prefix}{$tab_key}";

						// Set the item class.
						$item_class = array( 'psupsellmaster-settings-tab' );

						// Check if the tab has a url.
						if ( isset( $tab_data['url'] ) ) {
							// Set the tab target.
							$tab_target = $tab_data['url'];

							// Add a class to the list.
							array_push( $item_class, 'psupsellmaster-settings-tab-url' );
						}

						// Check the view parameter.
						if ( isset( $_GET['view'] ) ) {
							// Check the view parameter.
							if ( $tab_data['slug'] === $_GET['view'] ) {
								// Add a class to the list.
								array_push( $item_class, 'psupsellmaster-selected' );
							}
						}
						?>
						<li class="<?php echo esc_attr( implode( ' ', $item_class ) ); ?>">
							<a class="psupsellmaster-settings-tab-link" href="<?php echo esc_url( $tab_target ); ?>" target="_blank"><?php echo esc_html( $tab_data['label'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php foreach ( $settings_tabs as $tab_key => $tab_data ) : ?>
					<?php $tab_target = "{$tab_prefix}{$tab_key}"; ?>
					<div id="<?php echo esc_attr( $tab_target ); ?>">
						<?php
						// Check if a callback is set and if it is callable.
						if ( isset( $tab_data['callback'] ) && is_callable( $tab_data['callback'] ) ) {
							// Call the callback.
							call_user_func( $tab_data['callback'], $tab_key, $tab_data );
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
			<div style="display: flex; margin-top: 1.5em; height: 2em; align-items: center;">
				<?php submit_button( __( 'Save Changes', 'psupsellmaster' ), 'primary', 'submit', false ); ?>
				&nbsp;&nbsp;
				<a href="<?php admin_url( 'admin.php' ); ?>?page=psupsellmaster-products"><?php esc_html_e( 'Recalculate Upsells here', 'psupsellmaster' ); ?></a>
			</div>
		</form>
		<?php do_action( 'psupsellmaster_after_settings' ); ?>
	</div>
	<div id="psupsellmaster-modal-action-clear-results" style="display: none;">
		<div class="wrap">
			<?php do_action( 'psupsellmaster_modal_action_clear_results_before' ); ?>
			<form method="post">
				<input name="psupsellmaster_nonce_clear_results" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>" />
				<div class="psupsellmaster-modal-body">
					<p><?php esc_html_e( 'Are you sure you want to proceed and clear all results data?' ); ?></p>
				</div>
				<div class="psupsellmaster-modal-footer">
					<input class="button button-primary psupsellmaster-btn-clear-results-confirm" type="submit" value="<?php esc_attr_e( 'Confirm', 'psupsellmaster' ); ?>">
					<button class="button button-secondary psupsellmaster-btn-clear-results-cancel" type="button"><?php esc_html_e( 'Cancel', 'psupsellmaster' ); ?></button>
				</div>
			</form>
			<?php do_action( 'psupsellmaster_modal_action_clear_results_after' ); ?>
		</div>
	</div>
</div>

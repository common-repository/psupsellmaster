<?php
/**
 * Admin - Functions - Scripts.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the scripts.
 */
function psupsellmaster_admin_register_scripts() {
	// Set the js url.
	$js_url = PSUPSELLMASTER_URL . 'assets/js/';

	// Set the vendor url.
	$vendor_url = PSUPSELLMASTER_URL . 'assets/vendor/';

	// Set the file suffix.
	$file_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register the scripts.
	wp_register_script( 'psupsellmaster-admin-script-vendor-jszip', "{$vendor_url}jszip/jszip{$file_suffix}.js", array(), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-pdfmake', "{$vendor_url}pdfmake/pdfmake{$file_suffix}.js", array(), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-pdfmake-vfs-fonts', "{$vendor_url}pdfmake/vfs_fonts.js", array(), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables', "{$vendor_url}datatables/js/dataTables{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables-datatables', "{$vendor_url}datatables/js/dataTables.dataTables{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables-buttons', "{$vendor_url}datatables/extensions/buttons/js/dataTables.buttons{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables-buttons-html5', "{$vendor_url}datatables/extensions/buttons/js/buttons.html5{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables-buttons-print', "{$vendor_url}datatables/extensions/buttons/js/buttons.print{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-datatables-responsive', "{$vendor_url}datatables/extensions/responsive/js/dataTables.responsive{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-select2', "{$vendor_url}select2/js/select2.full{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-pikaday', "{$vendor_url}pikaday/pikaday{$file_suffix}.js", array(), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-vendor-chart', "{$vendor_url}chart-js/chart.umd{$file_suffix}.js", array(), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-main', "{$js_url}main{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-select2', "{$js_url}admin/select2{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-modal', "{$js_url}admin/modal{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-general', "{$js_url}admin/general{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-wizard', "{$js_url}admin/wizard{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-priorities', "{$js_url}admin/priorities{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-newsletter', "{$js_url}admin/newsletter{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-edit-product', "{$js_url}admin/edit-product{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-products', "{$js_url}admin/products{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-feedback', "{$js_url}admin/feedback{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-results', "{$js_url}admin/results{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-upsells', "{$js_url}admin/upsells{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-scores', "{$js_url}admin/scores{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-settings', "{$js_url}admin/settings{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-campaigns-list', "{$js_url}admin/campaigns/list{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-campaigns-edit', "{$js_url}admin/campaigns/edit{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-campaigns-view', "{$js_url}admin/campaigns/view{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );
	wp_register_script( 'psupsellmaster-admin-script-campaigns-templates', "{$js_url}admin/campaigns/templates{$file_suffix}.js", array( 'jquery-core' ), PSUPSELLMASTER_VER, true );

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_register_scripts' );
}
add_action( 'admin_enqueue_scripts', 'psupsellmaster_admin_register_scripts' );

/**
 * Register the styles.
 */
function psupsellmaster_admin_register_styles() {
	// Set the css url.
	$css_url = PSUPSELLMASTER_URL . 'assets/css/';

	// Set the vendor url.
	$vendor_url = PSUPSELLMASTER_URL . 'assets/vendor/';

	// Set the file suffix.
	$file_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register the styles.
	wp_register_style( 'psupsellmaster-admin-style-vendor-jquery-ui', "{$vendor_url}jquery-ui/jquery-ui{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-vendor-datatables', "{$vendor_url}datatables/css/dataTables.dataTables{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-vendor-datatables-responsive', "{$vendor_url}datatables/extensions/responsive/css/responsive.dataTables{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-vendor-select2', "{$vendor_url}select2/css/select2{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-vendor-pikaday', "{$vendor_url}pikaday/pikaday{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-vendor-font-awesome', "{$vendor_url}font-awesome/css/all{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-main', "{$css_url}main{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-datatables', "{$css_url}admin/datatables{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-select2', "{$css_url}admin/select2{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-modal', "{$css_url}admin/modal{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-general', "{$css_url}admin/general{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-wizard', "{$css_url}admin/wizard{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-priorities', "{$css_url}admin/priorities{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-newsletter', "{$css_url}admin/newsletter{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-feedback', "{$css_url}admin/feedback{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-edit-product', "{$css_url}admin/edit-product{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-products', "{$css_url}admin/products{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-results', "{$css_url}admin/results{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-upsells', "{$css_url}admin/upsells{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-scores', "{$css_url}admin/scores{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-settings', "{$css_url}admin/settings{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-campaigns-list', "{$css_url}admin/campaigns/list{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-campaigns-edit', "{$css_url}admin/campaigns/edit{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-campaigns-view', "{$css_url}admin/campaigns/view{$file_suffix}.css", array(), PSUPSELLMASTER_VER );
	wp_register_style( 'psupsellmaster-admin-style-campaigns-templates', "{$css_url}admin/campaigns/templates{$file_suffix}.css", array(), PSUPSELLMASTER_VER );

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_register_styles' );
}
add_action( 'admin_enqueue_scripts', 'psupsellmaster_admin_register_styles' );

/**
 * Enqueue a script.
 *
 * @param string $script_key The script key.
 */
function psupsellmaster_admin_enqueue_script( $script_key ) {
	// Check the script key.
	if ( 'main' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-main';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'ajax'         => array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'psupsellmaster-ajax-nonce' ),
				),
				'integrations' => array(
					'woo'       => psupsellmaster_is_plugin_active( 'woo' ),
					'edd'       => psupsellmaster_is_plugin_active( 'edd' ),
					'elementor' => psupsellmaster_is_plugin_active( 'elementor' ),
				),
				'plugin'       => array(
					'is_lite' => psupsellmaster_is_lite(),
					'is_pro'  => psupsellmaster_is_pro(),
					'version' => PSUPSELLMASTER_VER,
				),
				'server'       => array(
					'admin' => is_admin(),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_data_main = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'select2' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-select2' );

		// Check the script key.
	} elseif ( 'modal' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-modal';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );
		}

		// Check the script key.
	} elseif ( 'wizard' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-wizard';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );
		}

		// Check the script key.
	} elseif ( 'priorities' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-priorities';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );
		}

		// Check the script key.
	} elseif ( 'newsletter' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-newsletter';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );
		}

		// Check the script key.
	} elseif ( 'campaigns-list' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-campaigns-list';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'bulk_actions_delete_confirm' => __( 'Are you sure you want to delete the selected campaigns?', 'psupsellmaster' ),
					'bulk_actions_empty'          => __( 'Please select some campaigns first.', 'psupsellmaster' ),
					'currency_symbol'             => psupsellmaster_get_currency_symbol(),
					'datatable_btn_copy'          => __( 'Copy', 'psupsellmaster' ),
					'datatable_btn_csv'           => __( 'CSV', 'psupsellmaster' ),
					'datatable_btn_excel'         => __( 'Excel', 'psupsellmaster' ),
					'datatable_btn_print'         => __( 'Print', 'psupsellmaster' ),
					'datatable_length'            => sprintf( '%s: _MENU_', __( 'Show', 'psupsellmaster' ) ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_campaigns_list = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'campaigns-edit' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-campaigns-edit';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'datatable_btn_copy'   => __( 'Copy', 'psupsellmaster' ),
					'datatable_btn_csv'    => __( 'CSV', 'psupsellmaster' ),
					'datatable_btn_excel'  => __( 'Excel', 'psupsellmaster' ),
					'datatable_btn_print'  => __( 'Print', 'psupsellmaster' ),
					'datatable_length'     => sprintf( '%s: _MENU_', __( 'Show', 'psupsellmaster' ) ),
					'wp_media_btn_title'   => __( 'Select', 'psupsellmaster' ),
					'wp_media_frame_title' => __( 'Choose a Banner', 'psupsellmaster' ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_campaigns_edit = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'campaigns-view' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-campaigns-view';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'currency_symbol'     => psupsellmaster_get_currency_symbol(),
					'datatable_btn_copy'  => __( 'Copy', 'psupsellmaster' ),
					'datatable_btn_csv'   => __( 'CSV', 'psupsellmaster' ),
					'datatable_btn_excel' => __( 'Excel', 'psupsellmaster' ),
					'datatable_btn_print' => __( 'Print', 'psupsellmaster' ),
					'datatable_length'    => sprintf( '%s: _MENU_', __( 'Show', 'psupsellmaster' ) ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_campaigns_view = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'campaigns-templates' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-campaigns-templates';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'delete_item_confirm' => __( 'Are you sure you want to delete it?', 'psupsellmaster' ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_campaigns_templates = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'general' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-general' );

		// Check the script key.
	} elseif ( 'edit-product' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-edit-product' );

		// Check the script key.
	} elseif ( 'products' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-products' );

		// Check the script key.
	} elseif ( 'scores' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-scores' );

		// Check the script key.
	} elseif ( 'results' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-results';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'datatable_btn_copy'  => __( 'Copy', 'psupsellmaster' ),
					'datatable_btn_csv'   => __( 'CSV', 'psupsellmaster' ),
					'datatable_btn_excel' => __( 'Excel', 'psupsellmaster' ),
					'datatable_btn_print' => __( 'Print', 'psupsellmaster' ),
					'datatable_length'    => sprintf( '%s: _MENU_', __( 'Show', 'psupsellmaster' ) ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_results = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'upsells' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-upsells';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					'datatable_btn_abort'              => __( 'Abort', 'psupsellmaster' ),
					'datatable_btn_recalculate'        => __( 'Recalculate', 'psupsellmaster' ),
					'datatable_btn_copy'               => __( 'Copy', 'psupsellmaster' ),
					'datatable_btn_csv'                => __( 'CSV', 'psupsellmaster' ),
					'datatable_btn_excel'              => __( 'Excel', 'psupsellmaster' ),
					'datatable_btn_print'              => __( 'Print', 'psupsellmaster' ),
					'datatable_length'                 => sprintf( '%s: _MENU_', __( 'Show', 'psupsellmaster' ) ),
					'msg_err_operation_not_selected'   => __( 'You should select bulk action', 'psupsellmaster' ),
					'msg_confirm_recalculate_selected' => __( 'Do you want to recalculate upsells for selected products?', 'psupsellmaster' ),
					'msg_confirm_recalculate_all'      => __( 'Do you want to recalculate upsells for all products?', 'psupsellmaster' ),
					'msg_err_products_not_selected'    => __( 'You should select some products', 'psupsellmaster' ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_upsells = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'settings' === $script_key ) {
		// Set the script handle.
		$script_handle = 'psupsellmaster-admin-script-settings';

		// Check if the script is not enqueued.
		if ( ! wp_script_is( $script_handle, 'enqueued' ) ) {
			// Enqueue the script.
			wp_enqueue_script( $script_handle );

			// Set the script data.
			$script_data = array(
				'texts' => array(
					/* translators: 1: maximum input length. */
					'input_max_int' => __( 'Please enter a value that is no more than %d.', 'psupsellmaster' ),
				),
			);

			// Encode the script data.
			$script_data = wp_json_encode( $script_data );
			$script_data = "const psupsellmaster_admin_data_settings = {$script_data};";

			// Send the data to the script.
			wp_add_inline_script( $script_handle, $script_data, 'before' );
		}

		// Check the script key.
	} elseif ( 'vendor-jszip' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-jszip' );

		// Check the script key.
	} elseif ( 'vendor-pdfmake' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-pdfmake' );

		// Check the script key.
	} elseif ( 'vendor-pdfmake-vfs-fonts' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-pdfmake-vfs-fonts' );

		// Check the script key.
	} elseif ( 'vendor-datatables' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-datatables' );

		// Check the script key.
	} elseif ( 'vendor-datatables-buttons' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-datatables-buttons' );

		// Check the script key.
	} elseif ( 'vendor-datatables-buttons-html5' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-datatables-buttons-html5' );

		// Check the script key.
	} elseif ( 'vendor-datatables-buttons-print' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-datatables-buttons-print' );

		// Check the script key.
	} elseif ( 'vendor-datatables-responsive' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-datatables-responsive' );

		// Check the script key.
	} elseif ( 'vendor-select2' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-select2' );

		// Check the script key.
	} elseif ( 'vendor-pikaday' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-pikaday' );

		// Check the script key.
	} elseif ( 'vendor-chart' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-vendor-chart' );

		// Check the script key.
	} elseif ( 'wp-jquery-ui-tabs' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'jquery-ui-tabs' );

		// Check the script key.
	} elseif ( 'wp-jquery-ui-tooltip' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'jquery-ui-tooltip' );

		// Check the script key.
	} elseif ( 'wp-moment' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'moment' );

		// Check the script key.
	} elseif ( 'feedback' === $script_key ) {
		// Enqueue the script.
		wp_enqueue_script( 'psupsellmaster-admin-script-feedback' );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_enqueue_script', $script_key );
}

/**
 * Enqueue a style.
 *
 * @param string $style_key The style key.
 */
function psupsellmaster_admin_enqueue_style( $style_key ) {
	// Check the style key.
	if ( 'main' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-main' );

		// Check the style key.
	} elseif ( 'datatables' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-datatables' );

		// Check the style key.
	} elseif ( 'select2' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-select2' );

		// Check the style key.
	} elseif ( 'modal' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-modal' );

		// Check the style key.
	} elseif ( 'wizard' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-wizard' );

		// Check the style key.
	} elseif ( 'priorities' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-priorities' );

		// Check the style key.
	} elseif ( 'newsletter' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-newsletter' );

		// Check the style key.
	} elseif ( 'campaigns-list' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-campaigns-list' );

		// Check the style key.
	} elseif ( 'campaigns-edit' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-campaigns-edit' );

		// Check the style key.
	} elseif ( 'campaigns-view' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-campaigns-view' );

		// Check the style key.
	} elseif ( 'campaigns-templates' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-campaigns-templates' );

		// Check the style key.
	} elseif ( 'general' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-general' );

		// Check the style key.
	} elseif ( 'edit-product' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-edit-product' );

		// Check the style key.
	} elseif ( 'edit-product' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-edit-product' );

		// Check the style key.
	} elseif ( 'products' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-products' );

		// Check the style key.
	} elseif ( 'scores' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-scores' );

		// Check the style key.
	} elseif ( 'results' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-results' );

		// Check the style key.
	} elseif ( 'upsells' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-upsells' );

		// Check the style key.
	} elseif ( 'settings' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-settings' );

		// Check the style key.
	} elseif ( 'vendor-jquery-ui' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-jquery-ui' );

		// Check the style key.
	} elseif ( 'vendor-datatables' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-datatables' );

		// Check the style key.
	} elseif ( 'vendor-datatables-responsive' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-datatables-responsive' );

		// Check the style key.
	} elseif ( 'vendor-select2' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-select2' );

		// Check the style key.
	} elseif ( 'vendor-pikaday' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-pikaday' );

		// Check the style key.
	} elseif ( 'vendor-font-awesome' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-vendor-font-awesome' );

		// Check the style key.
	} elseif ( 'feedback' === $style_key ) {
		// Enqueue the style.
		wp_enqueue_style( 'psupsellmaster-admin-style-feedback' );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_enqueue_style', $style_key );
}

/**
 * Enqueue the scripts and styles.
 */
function psupsellmaster_admin_enqueue_scripts_styles() {
	// Check the current page.
	if ( psupsellmaster_admin_is_page( 'campaigns', 'list' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'vendor-chart' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'campaigns-list' );
		psupsellmaster_admin_enqueue_script( 'campaigns-templates' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'main' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'campaigns-list' );
		psupsellmaster_admin_enqueue_style( 'campaigns-templates' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'new' ) ) {
		// Enqueue the scripts.
		wp_enqueue_media();

		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tabs' );
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'campaigns-edit' );
		psupsellmaster_admin_enqueue_script( 'campaigns-templates' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-jquery-ui' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'main' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'campaigns-edit' );
		psupsellmaster_admin_enqueue_style( 'campaigns-templates' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'edit' ) ) {
		// Enqueue the scripts.
		wp_enqueue_media();

		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tabs' );
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tooltip' );
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'campaigns-edit' );
		psupsellmaster_admin_enqueue_script( 'campaigns-templates' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-jquery-ui' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'main' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'campaigns-edit' );
		psupsellmaster_admin_enqueue_style( 'campaigns-templates' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'campaigns', 'view' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'vendor-chart' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'campaigns-view' );
		psupsellmaster_admin_enqueue_script( 'campaigns-templates' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-datatables-responsive' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'campaigns-view' );
		psupsellmaster_admin_enqueue_style( 'campaigns-templates' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'wizard' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tooltip' );
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'priorities' );
		psupsellmaster_admin_enqueue_script( 'wizard' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'priorities' );
		psupsellmaster_admin_enqueue_style( 'wizard' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'products', 'edit' ) || psupsellmaster_admin_is_page( 'products', 'new' ) ) {
		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Enqueue the scripts.
			psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		}

		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'scores' );
		psupsellmaster_admin_enqueue_script( 'edit-product' );

		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Enqueue the styles.
			psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		}

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'edit-product' );
		psupsellmaster_admin_enqueue_style( 'scores' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'products', 'list-table' ) ) {
		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Enqueue the scripts.
			psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		}

		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'products' );

		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Enqueue the styles.
			psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		}

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'products' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'results' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tooltip' );
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'vendor-chart' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'modal' );
		psupsellmaster_admin_enqueue_script( 'newsletter' );
		psupsellmaster_admin_enqueue_script( 'results' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'modal' );
		psupsellmaster_admin_enqueue_style( 'newsletter' );
		psupsellmaster_admin_enqueue_style( 'results' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'upsells' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'wp-moment' );
		psupsellmaster_admin_enqueue_script( 'vendor-jszip' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake' );
		psupsellmaster_admin_enqueue_script( 'vendor-pdfmake-vfs-fonts' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-html5' );
		psupsellmaster_admin_enqueue_script( 'vendor-datatables-buttons-print' );
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'modal' );
		psupsellmaster_admin_enqueue_script( 'newsletter' );
		psupsellmaster_admin_enqueue_script( 'scores' );
		psupsellmaster_admin_enqueue_script( 'upsells' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-datatables' );
		psupsellmaster_admin_enqueue_style( 'vendor-font-awesome' );
		psupsellmaster_admin_enqueue_style( 'vendor-pikaday' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'datatables' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'modal' );
		psupsellmaster_admin_enqueue_style( 'newsletter' );
		psupsellmaster_admin_enqueue_style( 'scores' );
		psupsellmaster_admin_enqueue_style( 'upsells' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'settings' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'vendor-select2' );
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tabs' );
		psupsellmaster_admin_enqueue_script( 'wp-jquery-ui-tooltip' );
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'select2' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'modal' );
		psupsellmaster_admin_enqueue_script( 'newsletter' );
		psupsellmaster_admin_enqueue_script( 'priorities' );
		psupsellmaster_admin_enqueue_script( 'settings' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'vendor-jquery-ui' );
		psupsellmaster_admin_enqueue_style( 'vendor-select2' );
		psupsellmaster_admin_enqueue_style( 'select2' );
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'modal' );
		psupsellmaster_admin_enqueue_style( 'newsletter' );
		psupsellmaster_admin_enqueue_style( 'priorities' );
		psupsellmaster_admin_enqueue_style( 'settings' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'wp-plugins' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'general' );
		psupsellmaster_admin_enqueue_script( 'modal' );
		psupsellmaster_admin_enqueue_script( 'feedback' );
		psupsellmaster_admin_enqueue_script( 'newsletter' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'general' );
		psupsellmaster_admin_enqueue_style( 'modal' );
		psupsellmaster_admin_enqueue_style( 'feedback' );
		psupsellmaster_admin_enqueue_style( 'newsletter' );

		// Check the current page.
	} elseif ( psupsellmaster_admin_is_page( 'wp-widgets' ) ) {
		// Enqueue the scripts.
		psupsellmaster_admin_enqueue_script( 'main' );
		psupsellmaster_admin_enqueue_script( 'general' );

		// Enqueue the styles.
		psupsellmaster_admin_enqueue_style( 'general' );
	}

	// Allow developers to use this.
	do_action( 'psupsellmaster_admin_enqueue_scripts_styles' );
}
add_action( 'admin_enqueue_scripts', 'psupsellmaster_admin_enqueue_scripts_styles', 20 );

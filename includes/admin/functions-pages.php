<?php
/**
 * Admin - Functions - Pages.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the menus.
 *
 * @return array The menus.
 */
function psupsellmaster_admin_get_menus() {
	// Set the menus.
	$menus = array(
		'main' => array(
			'page_title' => __( 'Upsells', 'psupsellmaster' ),
			'menu_title' => __( 'Upsells', 'psupsellmaster' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'psupsellmaster',
			'callback'   => 'psupsellmaster_admin_render_page_results',
			'icon_url'   => 'dashicons-cart',
			'position'   => 5,
		),
	);

	// Get the wizard.
	$wizard = get_option( 'psupsellmaster_admin_setup_wizard_status' );

	// Check if the wizard is not completed.
	if ( 'completed' !== $wizard ) {
		// Set the menus.
		$menus['main'] = array(
			'page_title' => __( 'Setup Wizard', 'psupsellmaster' ),
			'menu_title' => __( 'Upsells', 'psupsellmaster' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'psupsellmaster-wizard',
			'callback'   => 'psupsellmaster_admin_render_page_wizard',
			'icon_url'   => 'dashicons-cart',
			'position'   => 5,
		);
	}

	// Allow developers to filter this.
	$menus = apply_filters( 'psupsellmaster_admin_menus', $menus );

	// Return the menus.
	return $menus;
}

/**
 * Get the submenus.
 *
 * @return array The submenus.
 */
function psupsellmaster_admin_get_submenus() {
	// Set the submenus.
	$submenus = array(
		'campaigns' => array(
			'parent_slug' => 'psupsellmaster',
			'page_title'  => __( 'Campaigns', 'psupsellmaster' ),
			'menu_title'  => __( 'Campaigns', 'psupsellmaster' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'psupsellmaster-campaigns',
			'callback'    => 'psupsellmaster_admin_render_page_campaigns',
			'position'    => 20,
		),
		'products'  => array(
			'parent_slug' => 'psupsellmaster',
			'page_title'  => __( 'Products', 'psupsellmaster' ),
			'menu_title'  => __( 'Products', 'psupsellmaster' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'psupsellmaster-products',
			'callback'    => 'psupsellmaster_admin_render_page_products',
			'position'    => 30,
		),
		'settings'  => array(
			'parent_slug' => 'psupsellmaster',
			'page_title'  => __( 'Settings', 'psupsellmaster' ),
			'menu_title'  => __( 'Settings', 'psupsellmaster' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'psupsellmaster-settings',
			'callback'    => 'psupsellmaster_admin_render_page_settings',
			'position'    => 50,
		),
	);

	// Allow developers to filter this.
	$submenus = apply_filters( 'psupsellmaster_admin_submenus', $submenus );

	// Sort the submenus.
	uasort( $submenus, 'psupsellmaster_admin_sort_menus' );

	// Return the submenus.
	return $submenus;
}

/**
 * Sort the menus by position.
 *
 * @param array $menu1 The first menu.
 * @param array $menu2 The second menu.
 * @return int The sorted menus.
 */
function psupsellmaster_admin_sort_menus( $menu1, $menu2 ) {
	// Return the sorted menus.
	return $menu1['position'] - $menu2['position'];
}

/**
 * Add the admin menus and submenus.
 */
function psupsellmaster_admin_menu() {
	// Get the menus.
	$menus = psupsellmaster_admin_get_menus();

	// Get the submenus.
	$submenus = psupsellmaster_admin_get_submenus();

	// Loop through the menus.
	foreach ( $menus as $menu_data ) {
		// Add the menu.
		add_menu_page(
			$menu_data['page_title'],
			$menu_data['menu_title'],
			$menu_data['capability'],
			$menu_data['menu_slug'],
			$menu_data['callback'],
			$menu_data['icon_url'],
			$menu_data['position']
		);
	}

	// Loop through the submenus.
	foreach ( $submenus as $submenu_data ) {
		// Add the submenu.
		add_submenu_page(
			$submenu_data['parent_slug'],
			$submenu_data['page_title'],
			$submenu_data['menu_title'],
			$submenu_data['capability'],
			$submenu_data['menu_slug'],
			$submenu_data['callback'],
			$submenu_data['position']
		);
	}
}
add_action( 'admin_menu', 'psupsellmaster_admin_menu' );

/**
 * Render an admin page.
 *
 * @param string $key The page key.
 */
function psupsellmaster_admin_render_page( $key ) {
	// Set the path.
	$path = PSUPSELLMASTER_DIR . "includes/admin/templates/{$key}.php";

	// Allow developers to filter this.
	$path = apply_filters( 'psupsellmaster_admin_render_page', $path, $key );

	// Include the file.
	require_once $path;
}

/**
 * Render the admin wizard page.
 */
function psupsellmaster_admin_render_page_wizard() {
	// Render the page.
	psupsellmaster_admin_render_page( 'wizard' );
}

/**
 * Render the admin campaigns page.
 */
function psupsellmaster_admin_render_page_campaigns() {
	// Get the view.
	$view = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';

	// Set the template.
	$template = 'list';

	// Check the view.
	if ( in_array( $view, array( 'edit', 'new' ), true ) ) {
		// Get the template.
		$template = 'edit';

		// Check the view.
	} elseif ( 'view' === $view ) {
		// Get the template.
		$template = 'view';
	}

	// Render the page.
	psupsellmaster_admin_render_page( "campaigns/{$template}" );
}

/**
 * Render the admin results page.
 */
function psupsellmaster_admin_render_page_results() {
	// Render the page.
	psupsellmaster_admin_render_page( 'results' );
}

/**
 * Render the admin products page.
 */
function psupsellmaster_admin_render_page_products() {
	// Render the page.
	psupsellmaster_admin_render_page( 'products' );
}

/**
 * Render the admin settings page.
 */
function psupsellmaster_admin_render_page_settings() {
	// Render the page.
	psupsellmaster_admin_render_page( 'settings' );
}

/**
 * Run procedures when loading the header of an admin page.
 */
function psupsellmaster_admin_header() {
	// Check if the current admin page is the wizard.
	if ( psupsellmaster_admin_is_page( 'wizard' ) ) {
		// Remove all admin notices.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}
}
add_action( 'in_admin_header', 'psupsellmaster_admin_header' );

/**
 * Checks if the received page is an admin page.
 *
 * @param string $passed_page The page to check.
 * @param string $passed_view The view to check.
 * @return bool True if the received page is an admin page, false otherwise.
 */
function psupsellmaster_admin_is_page( $passed_page = '', $passed_view = '' ) {
	global $pagenow, $typenow;

	// Define the found.
	$found = false;

	// Get the input data.
	$post_type = isset( $_GET['post_type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) : false;
	$page      = isset( $_GET['page'] ) ? strtolower( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : false;
	$view      = isset( $_GET['view'] ) ? strtolower( sanitize_text_field( wp_unslash( $_GET['view'] ) ) ) : false;

	// Define the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the passed page is campaigns.
	if ( 'campaigns' === $passed_page ) {
		// Check if the page is campaigns.
		if ( 'admin.php' === $pagenow && 'psupsellmaster-campaigns' === $page ) {
			// Check the view.
			if ( 'edit' === $passed_view && 'edit' === $view ) {
				// Set the found.
				$found = true;

				// Check the view.
			} elseif ( 'new' === $passed_view && 'new' === $view ) {
				// Set the found.
				$found = true;

				// Check the view.
			} elseif ( 'view' === $passed_view && 'view' === $view ) {
				// Set the found.
				$found = true;

				// Check the view.
			} elseif ( 'tags' === $passed_view && 'tags' === $view ) {
				// Set the found.
				$found = true;

				// Check the view.
			} elseif ( 'list' === $passed_view && empty( $view ) ) {
				// Set the found.
				$found = true;

				// Check the view.
			} elseif ( empty( $passed_view ) ) {
				// Set the found.
				$found = true;
			}
		}

		// Otherwise, check if the passed page is wizard.
	} elseif ( 'wizard' === $passed_page ) {
		// Check if the page is related to wizard.
		if ( 'admin.php' === $pagenow && 'psupsellmaster-wizard' === $page ) {
			// Set the found.
			$found = true;
		}

		// Otherwise, check if the passed page is products.
	} elseif ( 'products' === $passed_page ) {
		// Check if the page is related to product.
		if ( $product_post_type === $typenow || $product_post_type === $post_type ) {
			// Check the passed view.
			if ( 'list-table' === $passed_view ) {

				// Check the pagenow.
				if ( 'edit.php' === $pagenow ) {
					$found = true;
				}

				// Check the passed view.
			} elseif ( 'edit' === $passed_view ) {

				// Check the pagenow.
				if ( 'post.php' === $pagenow ) {
					$found = true;
				}

				// Check the passed view.
			} elseif ( 'new' === $passed_view ) {

				// Check the pagenow.
				if ( 'post-new.php' === $pagenow ) {
					$found = true;
				}

				// Otherwise...
			} else {
				$found = true;
			}
		}

		// Otherwise, check if the passed page is results.
	} elseif ( 'results' === $passed_page ) {
		// Check if the pagenow is admin.
		if ( 'admin.php' === $pagenow ) {

			// Check if the page is upsellmaster.
			if ( 'psupsellmaster' === $page ) {
				// Set the found.
				$found = true;
			}
		}

		// Otherwise, check if the passed page is upsells.
	} elseif ( 'upsells' === $passed_page ) {
		// Check if the pagenow is admin.
		if ( 'admin.php' === $pagenow ) {

			// Check if the page is upsells products.
			if ( 'psupsellmaster-products' === $page ) {
				// Set the found.
				$found = true;
			}
		}

		// Otherwise, check if the passed page is settings.
	} elseif ( 'settings' === $passed_page ) {
		// Check if the pagenow is admin.
		if ( 'admin.php' === $pagenow ) {

			// Check if the page is upsellmaster settings.
			if ( 'psupsellmaster-settings' === $page ) {
				// Set the found.
				$found = true;
			}
		}

		// Otherwise, check if the passed page is plugins.
	} elseif ( 'wp-plugins' === $passed_page ) {
		// Get the current screen.
		$current_screen = get_current_screen();

		// Check if the current screen id is plugins.
		if ( ! empty( $current_screen->id ) && 'plugins' === $current_screen->id ) {
			// Set the found.
			$found = true;
		}

		// Otherwise, check if the passed page is widgets.
	} elseif ( 'wp-widgets' === $passed_page ) {
		// Get the current screen.
		$current_screen = get_current_screen();

		// Check if the current screen id is widgets.
		if ( ! empty( $current_screen->id ) && 'widgets' === $current_screen->id ) {
			// Set the found.
			$found = true;
		}
	}

	// Allow developers to filter this.
	$found = apply_filters( 'psupsellmaster_admin_is_page', $found, $page, $view, $passed_page, $passed_view );

	// Return the found.
	return $found;
}

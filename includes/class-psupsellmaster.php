<?php
/**
 * Class - Main.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsUpsellMaster class.
 */
class PsUpsellMaster {
	/**
	 * The background processes.
	 *
	 * @var array
	 */
	public static $background;

	/**
	 * The controllers.
	 *
	 * @var array
	 */
	public static $controllers;

	/**
	 * Is pro?
	 *
	 * @var boolean
	 */
	public static $is_pro;

	/**
	 * Init.
	 */
	public static function init() {
		self::maybe_pro();
		self::define_constants();
		self::includes();
		self::setup();
		self::hooks();
		self::background();
	}

	/**
	 * Maybe include pro files.
	 */
	public static function maybe_pro() {
		// Set the is pro.
		self::$is_pro = false;

		// Set the lite functions path.
		$lite_path = PSUPSELLMASTER_DIR . 'includes/lite/functions.php';

		// Set the pro functions path.
		$pro_path = PSUPSELLMASTER_DIR . 'includes/pro/functions.php';

		// Check if the pro file does exist.
		if ( file_exists( $pro_path ) ) {
			// Require the file.
			require_once $pro_path;

			// Set the is pro.
			self::$is_pro = true;

			// Otherwise, check if the lite file does exist.
		} elseif ( file_exists( $lite_path ) ) {
			// Require the file.
			require_once $lite_path;
		}
	}

	/**
	 * Define the constants.
	 */
	private static function define_constants() {
		define( 'PSUPSELLMASTER_API_URL', 'https://www.pluginsandsnippets.com/' );
		define( 'PSUPSELLMASTER_DOCUMENTATION_URL', 'https://www.pluginsandsnippets.com/knowledge-base/upsellmaster-setup-documentation/' );
		define( 'PSUPSELLMASTER_OPEN_TICKET_URL', 'https://www.pluginsandsnippets.com/open-ticket/' );
		define( 'PSUPSELLMASTER_SUPPORT_URL', 'https://www.pluginsandsnippets.com/support/' );
		define( 'PSUPSELLMASTER_PRODUCT_URL', 'https://www.pluginsandsnippets.com/downloads/upsellmaster/' );
		define( 'PSUPSELLMASTER_REVIEW_URL', 'https://www.pluginsandsnippets.com/downloads/upsellmaster/#edd-reviews' );
		define( 'PSUPSELLMASTER_PURCHASES_URL', 'https://www.pluginsandsnippets.com/purchases/' );
		define( 'PSUPSELLMASTER_NEWSLETTER_URL', 'https://www.pluginsandsnippets.com/?ps-subscription-request=1' );
		define( 'PSUPSELLMASTER_STORE_PRODUCT_ID', 25558 );
		define( 'PSUPSELLMASTER_AUTHOR', 'Plugins & Snippets' );
		define( 'PSUPSELLMASTER_FEEDBACK_VERSION', 2 );

		do_action( 'psupsellmaster_define_constants' );
	}

	/**
	 * Return the is pro.
	 */
	public static function is_pro() {
		return self::$is_pro;
	}

	/**
	 * Include necessary scripts
	 */
	public static function includes() {
		// Allow developers to use this.
		do_action( 'psupsellmaster_includes_before' );

		// Require base functions and classes.
		require_once PSUPSELLMASTER_DIR . 'includes/functions-base.php';
		require_once PSUPSELLMASTER_DIR . 'includes/class-psupsellmaster-settings.php';
		require_once PSUPSELLMASTER_DIR . 'includes/class-psupsellmaster-tracking.php';

		// Require the background process classes.
		require_once PSUPSELLMASTER_DIR . 'includes/background/class-psupsellmaster-wp-async-request.php';
		require_once PSUPSELLMASTER_DIR . 'includes/background/class-psupsellmaster-wp-background-process.php';
		require_once PSUPSELLMASTER_DIR . 'includes/background/class-psupsellmaster-background-process.php';

		// Require the background process functions.
		require_once PSUPSELLMASTER_DIR . 'includes/background/functions.php';
		require_once PSUPSELLMASTER_DIR . 'includes/background/functions-scores.php';
		require_once PSUPSELLMASTER_DIR . 'includes/background/functions-analytics-orders.php';
		require_once PSUPSELLMASTER_DIR . 'includes/background/functions-analytics-upsells.php';

		// Require general functions.
		require_once PSUPSELLMASTER_DIR . 'includes/functions-cookies.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-sessions.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-settings.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-pages.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions.php';

		// Require the database functions.
		require_once PSUPSELLMASTER_DIR . 'includes/database/class-psupsellmaster-database.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-upgrades.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-campaigns.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-visitors.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-interests.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-results.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-analytics-orders.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-analytics-upsells.php';
		require_once PSUPSELLMASTER_DIR . 'includes/database/functions-scores.php';

		// Require other plugin functions.
		require_once PSUPSELLMASTER_DIR . 'includes/functions-campaigns.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-orders.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-receipts.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-products.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-tracking.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-popups.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-scores.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-wp-cron.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-scripts.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-deprecated.php';
		require_once PSUPSELLMASTER_DIR . 'includes/functions-blocks.php';

		// Include scripts.
		if ( is_admin() ) {
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-scripts.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-feedback.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-pages.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-wizard.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-settings.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-campaigns.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-analytics.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-scores.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-products.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-edit-product.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-ajax.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/functions-wp-plugins.php';
			require_once PSUPSELLMASTER_DIR . 'includes/admin/register.php';
		}

		// Check if the Easy Digital Downloads plugin is active.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions.php';
		}

		// Check if the WooCommerce plugin is active.
		if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
			require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions.php';
		}

		// Check if the WPML plugin is active.
		if ( psupsellmaster_is_plugin_active( 'wpml' ) ) {
			require_once PSUPSELLMASTER_DIR . 'includes/integrations/sitepress-multilingual-cms/functions.php';
		}

		// Check if the WPML plugin is active.
		if ( psupsellmaster_is_plugin_active( 'vczapi-woocommerce-addon' ) ) {
			require_once PSUPSELLMASTER_DIR . 'includes/integrations/vczapi-woocommerce-addon/functions.php';
		}

		// Allow developers to use this.
		do_action( 'psupsellmaster_includes_after' );
	}

	/**
	 * Setup the basics.
	 */
	public static function setup() {
		// Init the settings.
		PsUpsellMaster_Settings::init();

		// Allow developers to use this.
		do_action( 'psupsellmaster_setup' );
	}

	/**
	 * Add action and filter hooks.
	 */
	public static function hooks() {
		self::load_textdomain();

		if ( is_admin() ) {
			do_action( 'psupsellmaster_hooks_admin' );
		}

		$review_time = (int) PsUpsellMaster_Settings::get( 'review_time' );

		if ( $review_time <= 0 ) {
			$review_time = time() + 7 * DAY_IN_SECONDS;
			PsUpsellMaster_Settings::set( 'review_time', $review_time, true );
		}

		add_action( 'plugin_row_meta', 'PsUpsellMaster::plugin_row_meta', 10, 2 );
		add_action( 'plugin_action_links', 'PsUpsellMaster::plugin_action_links', 10, 2 );
		add_action( 'admin_notices', 'psupsellmaster_display_flash_notices', 12 );

		$dismiss_review_notice = (int) PsUpsellMaster_Settings::get( 'dismiss_review_notice' );

		if (
			( is_admin() ) &&
			( $review_time < time() ) &&
			( ! $dismiss_review_notice )
		) {
			add_action( 'admin_notices', 'PsUpsellMaster::notice_review' );
		}

		add_action( 'wp_ajax_psupsellmaster_review_notice', 'PsUpsellMaster::dismiss_review_notice' );
	}

	/**
	 * Load the textdomain.
	 */
	public static function load_textdomain() {
		$path = basename( __DIR__ ) . '/languages';

		load_plugin_textdomain( 'psupsellmaster', false, $path );
	}

	/**
	 * Instantiate the background process classes.
	 */
	protected static function background() {
		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Create a new background process instance.
			static::$background['edd_prices'] = new PsUpsellMaster_Background_Process(
				'bp_edd_prices',
				'psupsellmaster_bp_edd_prices_run_batch'
			);
		}

		// Create a new background process instance.
		static::$background['scores'] = new PsUpsellMaster_Background_Process(
			'bp_scores',
			'psupsellmaster_bp_scores_run_batch'
		);

		// Create a new background process instance.
		static::$background['analytics_orders'] = new PsUpsellMaster_Background_Process(
			'bp_analytics_orders',
			'psupsellmaster_bp_analytics_orders_run_batch'
		);

		// Create a new background process instance.
		static::$background['analytics_upsells'] = new PsUpsellMaster_Background_Process(
			'bp_analytics_upsells',
			'psupsellmaster_bp_analytics_upsells_run_batch'
		);
	}

	/**
	 * Add plugin action links to the WordPress plugins list.
	 *
	 * @param  array  $actions    The plugin action links.
	 * @param  string $plugin_file The plugin file name.
	 * @return array              The plugin action links.
	 */
	public static function plugin_action_links( $actions, $plugin_file ) {
		// Check if the plugin file is not this plugin.
		if ( plugin_basename( PSUPSELLMASTER_FILE ) !== $plugin_file ) {
			// Return the actions.
			return $actions;
		}

		// Set the setting page link.
		$setting_page_link = admin_url( 'admin.php?page=psupsellmaster-settings' );

		// Set the settings link.
		/* translators: %1$s: open anchor tag, 2: close anchor tag. */
		$settings_link = sprintf( __( '%1$s Settings %2$s', 'psupsellmaster' ), '<a href="' . $setting_page_link . '">', '</a>' );

		// Add the settings link to the beginning of the list.
		array_unshift( $actions, $settings_link );

		// Allow developers to filter this.
		$actions = apply_filters( 'psupsellmaster_plugin_action_links', $actions );

		// Return the actions.
		return $actions;
	}

	/**
	 * Add links to the plugin meta row.
	 *
	 * @param array  $plugin_meta The plugin meta.
	 * @param string $plugin_file The plugin file.
	 */
	public static function plugin_row_meta( $plugin_meta, $plugin_file ) {
		// Check if the plugin file is not this plugin.
		if ( plugin_basename( PSUPSELLMASTER_FILE ) !== $plugin_file ) {
			// Return the plugin meta.
			return $plugin_meta;
		}

		// Set additional links.
		$plugin_meta[] = '<a href="' . PSUPSELLMASTER_DOCUMENTATION_URL . '" target="_blank">' . esc_html__( 'Documentation', 'psupsellmaster' ) . '</a>';
		$plugin_meta[] = '<a href="' . PSUPSELLMASTER_OPEN_TICKET_URL . '" target="_blank">' . esc_html__( 'Open Support Ticket', 'psupsellmaster' ) . '</a>';
		$plugin_meta[] = '<a href="' . PSUPSELLMASTER_REVIEW_URL . '" target="_blank">' . esc_html__( 'Post Review', 'psupsellmaster' ) . '</a>';

		// Return the plugin meta.
		return $plugin_meta;
	}

	/**
	 * Ask the user to leave a review for the plugin.
	 */
	public static function notice_review() {
		// Get the current user.
		$current_user = wp_get_current_user();

		// Set the user name.
		$user_name = __( 'WordPress User', 'psupsellmaster' );

		if ( ! empty( $current_user->display_name ) ) {
			$user_name = ' ' . $current_user->display_name;
		}

		// Set the html.
		$html = '<div id="psupsellmaster-review" class="notice notice-info is-dismissible"><p>' .
			sprintf( esc_html__( "Hi %1\$s, Thank you for using %2\$s. Please don't forget to rate our plugin. We sincerely appreciate your feedback.", 'psupsellmaster' ), esc_html( $user_name ), '<b>' . esc_html( PSUPSELLMASTER_NAME ) . '</b>' ) .
			'<br><a target="_blank" href="' . esc_url( PSUPSELLMASTER_REVIEW_URL ) . '" class="button-secondary">' . esc_html__( 'Post Review', 'psupsellmaster' ) . '</a></p></div>';

		echo wp_kses_post( $html );
	}

	/**
	 * Disables the notice about leaving a review.
	 */
	public static function dismiss_review_notice() {
		PsUpsellMaster_Settings::set( 'dismiss_review_notice', 1, true );
		wp_die();
	}
}

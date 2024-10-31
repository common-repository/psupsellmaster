<?php
/**
 * Class - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsUpsellMaster_Settings class.
 */
class PsUpsellMaster_Settings {
	/**
	 * The settings data.
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * The default settings data.
	 *
	 * @var array
	 */
	private static $defaults = array();

	/**
	 * Return the definitions for the settings.
	 * We are using this definitions function besides the defaults function,
	 * because the settings are defined as key => value pairs (arrays), but some default values are plain arrays as well.
	 * Therefore we are using the '#type' and 'default' keys to define groups that are not exactly default values,
	 * but another set/group of settings. That way we can get defaults later and parse the stored data.
	 *
	 * @return array The definitions.
	 */
	protected static function get_definitions() {
		// Set the definitions.
		$definitions = array(
			'license_key'                               => '',
			'number_of_upsell_products'                 => 3,
			'limit'                                     => 50,
			'cleandata_interval'                        => '1-month',
			'default_upsell_products'                   => array(),
			'author_information'                        => 'all',
			'auto_calculate_for_new_product'            => 1,
			'auto_calculate_on_product_update'          => 0,
			'add_rel_nofollow'                          => 0,
			'remove_data'                               => 0,
			'review_time'                               => 0,
			'dismiss_review_notice'                     => 0,
			'flash_notices'                             => array(),
			'algorithm_logic'                           => array(
				'#type'   => 'group',
				'default' => array(
					'bundles_only'        => 0,
					'price_range'         => array(
						'from' => '',
						'to'   => '',
					),
					'priority'            => array(
						0 => 'lifetime-sales',
						1 => 'category',
						2 => 'tag',
					),
					'priority_max_weight' => 100000,
					'weight_factor'       => array(
						0 => 100000,
						1 => 10000,
						2 => 1000,
					),
				),
			),
			'product_page_enable'                       => 1,
			'product_page_display_type'                 => 'carousel',
			'product_page_show_type'                    => 'upsells',
			'product_page_label_title'                  => __( 'Similar Products', 'psupsellmaster' ),
			'product_page_label_cta_text'               => __( 'Other customers where also interested in...', 'psupsellmaster' ),
			'product_page_max_cols'                     => 3,
			'product_page_max_prod'                     => 50,
			'product_page_max_per_author'               => 0,
			'product_page_addtocart_button'             => 'all-prices',
			'product_page_title_length'                 => 50,
			'product_page_short_description_limit'      => 100,
			'product_page_edd_position'                 => 1,
			'checkout_page_enable'                      => 1,
			'checkout_page_display_type'                => 'carousel',
			'checkout_page_show_type'                   => 'upsells',
			'checkout_page_label_title'                 => __( 'Similar Products', 'psupsellmaster' ),
			'checkout_page_label_cta_text'              => __( 'Other customers where also interested in...', 'psupsellmaster' ),
			'checkout_page_max_cols'                    => 3,
			'checkout_page_max_prod'                    => 50,
			'checkout_page_max_per_author'              => 0,
			'checkout_page_addtocart_button'            => 'all-prices',
			'checkout_page_title_length'                => 50,
			'checkout_page_short_description_limit'     => 100,
			'add_to_cart_popup_enable'                  => 1,
			'add_to_cart_popup_excluded_pages'          => array(),
			'add_to_cart_popup_display_type'            => 'carousel',
			'add_to_cart_popup_show_type'               => 'upsells',
			'add_to_cart_popup_headline'                => __( 'Product Added to Cart', 'psupsellmaster' ),
			'add_to_cart_popup_tagline'                 => __( 'The following product was just added to your cart.', 'psupsellmaster' ),
			'add_to_cart_popup_button_checkout'         => __( 'Checkout', 'psupsellmaster' ),
			'add_to_cart_popup_label_title'             => __( 'How about adding something more?', 'psupsellmaster' ),
			'add_to_cart_popup_label_cta_text'          => __( 'Have you considered also these products?', 'psupsellmaster' ),
			'add_to_cart_popup_max_cols'                => 3,
			'add_to_cart_popup_max_prod'                => 50,
			'add_to_cart_popup_max_per_author'          => 0,
			'add_to_cart_popup_addtocart_button'        => 'all-prices',
			'add_to_cart_popup_title_length'            => 50,
			'add_to_cart_popup_short_description_limit' => 50,
			'campaigns'                                 => array(
				'#type'   => 'group',
				'default' => array(
					'prices_discount_text'           => '<strong><span style="color: #ff0000;"><del>{old_price}</del></span> <span style="color: #008000;">{new_price}</span> (<span style="color: #008000;">{discount_amount} ' . __( 'OFF', 'psupsellmaster' ) . '</span>)</strong>',
					'conditions_products_count_type' => 'distinct_products',
					'conditions_products_min_text'   => '<strong><span style="color: #ff0000;">' . sprintf( '%s {min_quantity} %s {discount_amount} %s!', __( 'Purchase at least', 'psupsellmaster' ), __( 'products to qualify for', 'psupsellmaster' ), __( 'OFF', 'psupsellmaster' ) ) . '</span></strong>',
					'conditions_subtotal_min_text'   => '<strong><span style="color: #ff0000;">' . sprintf( '%s {min_subtotal} %s {discount_amount} %s!', __( 'Purchase at least', 'psupsellmaster' ), __( 'products to qualify for', 'psupsellmaster' ), __( 'OFF', 'psupsellmaster' ) ) . '</span></strong>',
					'coupons_allow_mix'              => 1,
					'coupons_multiple_behavior'      => 'all',
					'page_product_banner_position'   => 'content_before',
					'page_checkout_discount_text'    => '<strong><span style="color: #ff0000;">' . sprintf( '%s {discount_amount} %s!', __( 'Subject to', 'psupsellmaster' ), __( 'OFF', 'psupsellmaster' ) ) . '</span> (<span style="color: #ff0000;"><del>{old_price}</del></span> <span style="color: #008000;">{new_price}</span>)</strong>',
				),
			),
		);

		// Allow developers to filter this.
		$definitions = apply_filters( 'psupsellmaster_settings_definitions', $definitions );

		// Return the definitions.
		return $definitions;
	}

	/**
	 * Return the default settings based on the definitions.
	 *
	 * @param array $definitions The definitions.
	 * @return array The default settings.
	 */
	protected static function get_defaults( $definitions = array() ) {
		// Set the defaults.
		$defaults = array();

		// Get the definitions.
		$definitions = ! empty( $definitions ) ? $definitions : self::get_definitions();

		// Loop through the definitions.
		foreach ( $definitions as $key => $definition ) {
			// Set the value.
			$value = $definition;

			// Check if the definition is a group.
			if ( is_array( $definition ) && isset( $definition['#type'] ) && 'group' === $definition['#type'] ) {
				// Set the value.
				$value = array();

				// Check if the definition has a default value.
				if ( isset( $definition['default'] ) ) {
					// Set the value.
					$value = self::get_defaults( $definition['default'] );
				}
			}

			// Set the default value for the key.
			$defaults[ $key ] = $value;
		}

		// Return the defaults.
		return $defaults;
	}

	/**
	 * Parse the data based on the definitions.
	 *
	 * @param array $data     The data to parse.
	 * @param array $settings The settings to use for parsing.
	 * @return array The parsed data.
	 */
	protected static function parse_data( $data = array(), $settings = array() ) {
		// Get the settings.
		$settings = ! empty( $settings ) ? $settings : self::get_definitions();

		// Loop through the settings.
		foreach ( $settings as $key => $setting_value ) {
			// Set the default value.
			self::$defaults[ $key ] = $setting_value;

			// Set the value.
			$value = $setting_value;

			// Check if the setting key is set in the data.
			if ( isset( $data[ $key ] ) ) {
				// Set the value.
				$value = $data[ $key ];
			}

			// Check if the setting value is a group and not a default value.
			if ( is_array( $setting_value ) && isset( $setting_value['#type'] ) && 'group' === $setting_value['#type'] ) {
				// Set the value.
				$value = array();

				// Check if the setting value has a default value.
				if ( isset( $setting_value['default'] ) ) {
					// Set the value by parsing the inner data (recursion).
					$value = self::parse_data( isset( $data[ $key ] ) ? $data[ $key ] : array(), $setting_value['default'] );
				}
			}

			// Set the setting value.
			$settings[ $key ] = $value;
		}

		// Return the settings.
		return $settings;
	}

	/**
	 * Init.
	 */
	public static function init() {
		// Load the data.
		self::load();
	}

	/**
	 * Get a value.
	 *
	 * @param string $field The field key.
	 * @return mixed The value.
	 */
	public static function get( $field ) {
		// Get the value.
		$value = array_key_exists( $field, self::$data ) ? self::$data[ $field ] : null;

		// Allow developers to filter this.
		$value = apply_filters( 'psupsellmaster_settings_get_value', $value, $field, self::$data );

		// Return the value.
		return $value;
	}

	/**
	 * Load the data.
	 */
	public static function load() {
		// Set the defaults.
		self::$defaults = self::get_defaults();

		// Get the stored.
		$stored = get_option( 'psupsellmaster_settings', array() );

		// Set the data.
		self::$data = self::parse_data( $stored );
	}

	/**
	 * Set a value.
	 *
	 * @param string  $field The field key.
	 * @param mixed   $value The field value.
	 * @param boolean $save Whether or not it should save the settings.
	 */
	public static function set( $field, $value, $save = false ) {
		self::$data[ $field ] = $value;

		if ( $save ) {
			self::save();
		}
	}

	/**
	 * Save the settings.
	 */
	public static function save() {
		update_option( 'psupsellmaster_settings', self::$data, 'yes' );
	}

	/**
	 * Update the settings.
	 */
	public static function update() {
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

		// Set the binary keys.
		$binary_keys = array(
			'add_rel_nofollow',
			'remove_data',
			'auto_calculate_for_new_product',
			'auto_calculate_on_product_update',
			'product_page_enable',
			'checkout_page_enable',
			'add_to_cart_popup_enable',
		);

		// Set the array keys.
		$array_keys = array(
			'add_to_cart_popup_excluded_pages',
			'default_upsell_products',
		);

		// Get the keys.
		$keys = array_keys( self::get_definitions() );

		// Loop through the keys.
		foreach ( $keys as $key ) {
			// Set the isset.
			$key_isset = isset( $_POST[ $key ] );

			// Get the current value.
			$current_value = self::$data[ $key ];

			// Set the new value.
			$new_value = $current_value;

			// Set the sanitized value.
			$sanitized_value = null;

			// Check if the key is set.
			if ( $key_isset ) {
				// Get the sanitized value.
				$sanitized_value = map_deep( wp_unslash( $_POST[ $key ] ), 'sanitize_text_field' );

				// Get the new value.
				$new_value = $sanitized_value;

				// Check the key.
				if ( 'algorithm_logic' === $key ) {
					// Check if the subkey is not set.
					if ( ! isset( $sanitized_value['bundles_only'] ) ) {
						// Set the value.
						$new_value['bundles_only'] = 0;
					}

					// Check if the subkey is not set.
					if ( ! isset( $sanitized_value['price_range'] ) ) {
						// Set the value.
						$new_value['price_range'] = self::$defaults[ $key ]['price_range'];

						// Otherwise...
					} else {
						if ( ! isset( $sanitized_value['price_range']['from'] ) ) {
							// Set the value.
							$new_value['price_range']['from'] = '';
						}

						if ( ! isset( $sanitized_value['price_range']['to'] ) ) {
							// Set the value.
							$new_value['price_range']['to'] = '';
						}
					}

					// Check if the subkey is not set.
					if ( ! isset( $sanitized_value['priority'] ) ) {
						// Set the value.
						$new_value['priority'] = self::$defaults[ $key ]['priority'];
					}

					// Check if the subkey is set.
					if ( isset( $sanitized_value['priority_max_weight'] ) ) {
						// Set the value.
						$new_value['priority_max_weight'] = absint( str_replace( array( ',', '.' ), '', $sanitized_value['priority_max_weight'] ) );

						// Otherwise...
					} else {
						// Set the value.
						$new_value['priority_max_weight'] = 0;
					}

					// Check if the subkey is set.
					if ( isset( $sanitized_value['weight_factor'] ) ) {
						// Set the value.
						$new_value['weight_factor'] = array_map( 'absint', str_replace( array( ',', '.' ), '', $sanitized_value['weight_factor'] ) );

						// Otherwise...
					} else {
						// Set the value.
						$new_value['weight_factor'] = self::$defaults[ $key ]['weight_factor'];
					}

					// Check the key.
				} elseif ( 'campaigns' === $key ) {
					// Get the sanitized value.
					$sanitized_value = map_deep( wp_unslash( $_POST[ $key ] ), 'wp_kses_post' );

					// Set the new value.
					$new_value = $sanitized_value;

					// Check if the subkey is not set.
					if ( ! isset( $sanitized_value['coupons_allow_mix'] ) ) {
						// Set the value.
						$new_value['coupons_allow_mix'] = 0;
					}

					// Check the key.
				}

				// Check the key.
			} elseif ( in_array( $key, $array_keys, true ) ) {
				// Set the value.
				$new_value = array();

				// Check the key.
			} elseif ( in_array( $key, $binary_keys, true ) ) {
				// Set the value.
				$new_value = 0;
			}

			// Set the data.
			self::$data[ $key ] = apply_filters( 'psupsellmaster_settings_update_key', $new_value, $key, $sanitized_value, $current_value, $key_isset );
		}

		// Allow developers to filter this.
		self::$data = apply_filters( 'psupsellmaster_settings_update', self::$data, $keys );

		// Update the option.
		update_option( 'psupsellmaster_settings', self::$data, 'yes' );
	}

	/**
	 * Delete a field.
	 *
	 * @param string $field The field key.
	 */
	public static function delete( $field ) {
		if ( array_key_exists( $field, self::$data ) ) {
			unset( self::$data[ $field ] );

			update_option( 'psupsellmaster_settings', self::$data, 'yes' );
		}
	}
}

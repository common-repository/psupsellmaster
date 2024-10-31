<?php
/**
 * Class - Tracking.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsUpsellMaster_Tracking class.
 */
class PsUpsellMaster_Tracking {

	/**
	 * The base product id.
	 *
	 * @var int
	 */
	protected $base_product_id = 0;

	/**
	 * The campaign id.
	 *
	 * @var int
	 */
	protected $campaign_id = 0;

	/**
	 * The location.
	 *
	 * @var string
	 */
	protected $location = '';

	/**
	 * The nonce.
	 *
	 * @var string
	 */
	protected $nonce = '';

	/**
	 * The source.
	 *
	 * @var string
	 */
	protected $source = '';

	/**
	 * The view.
	 *
	 * @var string
	 */
	protected $view = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Set the nonce.
		$this->set_nonce( wp_create_nonce( 'psupsellmaster-nonce' ) );
	}

	/**
	 * Add the hooks.
	 *
	 * @param string $type The type of hooks to add.
	 */
	public function add_hooks( $type = 'all' ) {
		// Check the type.
		if ( in_array( $type, array( 'all', 'products' ), true ) ) {
			add_filter( 'psupsellmaster_item_product_url', array( $this, 'item_product_url' ) );

			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'products_woo_after_add_to_cart_button' ) );
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'products_woo_loop_add_to_cart_link' ) );

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				add_action( 'edd_purchase_link_end', array( $this, 'products_edd_purchase_link_end' ) );
			}
		}

		// Check the type.
		if ( in_array( $type, array( 'all', 'single_product' ), true ) ) {
			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_product_woo_after_add_to_cart_button' ) );
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'single_product_woo_loop_add_to_cart_link' ), 10, 2 );

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				add_action( 'edd_purchase_link_end', array( $this, 'single_product_edd_purchase_link_end' ) );
			}
		}
	}

	/**
	 * Get the base product id.
	 *
	 * @return int The base product id.
	 */
	public function get_base_product_id() {
		// Return the base product id.
		return $this->base_product_id;
	}

	/**
	 * Get the campaign id.
	 *
	 * @return int The campaign id.
	 */
	public function get_campaign_id() {
		// Return the campaign id.
		return $this->campaign_id;
	}

	/**
	 * Get the location.
	 *
	 * @return string The location.
	 */
	public function get_location() {
		// Return the location.
		return $this->location;
	}

	/**
	 * Get the nonce.
	 *
	 * @return string The nonce.
	 */
	public function get_nonce() {
		// Return the nonce.
		return $this->nonce;
	}

	/**
	 * Get the source.
	 *
	 * @return string The source.
	 */
	public function get_source() {
		// Return the source.
		return $this->source;
	}

	/**
	 * Get the view.
	 *
	 * @return string The view.
	 */
	public function get_view() {
		// Return the view.
		return $this->view;
	}

	/**
	 * Add the tracking attributes to the product url.
	 *
	 * @param string $product_url The product url.
	 * @return string The product url.
	 */
	public function item_product_url( $product_url ) {
		// Set the args.
		$args = array(
			'psupsellmaster_nonce'           => $this->get_nonce(),
			'psupsellmaster_base_product_id' => $this->get_base_product_id(),
			'psupsellmaster_campaign_id'     => $this->get_campaign_id(),
			'psupsellmaster_location'        => $this->get_location(),
			'psupsellmaster_source'          => $this->get_source(),
			'psupsellmaster_view'            => $this->get_view(),
		);

		// Set the product url.
		$product_url = add_query_arg( $args, $product_url );

		// Return the product url.
		return $product_url;
	}

	/**
	 * Add the tracking attributes to the add to cart link.
	 */
	public function products_edd_purchase_link_end() {
		?>
		<input name="psupsellmaster[nonce]" type="hidden" value="<?php echo esc_attr( $this->get_nonce() ); ?>">
		<input name="psupsellmaster[base_product_id]" type="hidden" value="<?php echo esc_attr( $this->get_base_product_id() ); ?>">
		<input name="psupsellmaster[campaign_id]" type="hidden" value="<?php echo esc_attr( $this->get_campaign_id() ); ?>">
		<input name="psupsellmaster[location]" type="hidden" value="<?php echo esc_attr( $this->get_location() ); ?>">
		<input name="psupsellmaster[source]" type="hidden" value="<?php echo esc_attr( $this->get_source() ); ?>">
		<input name="psupsellmaster[view]" type="hidden" value="<?php echo esc_attr( $this->get_view() ); ?>">
		<?php
	}

	/**
	 * Add the tracking attributes to the add to cart button.
	 */
	public function products_woo_after_add_to_cart_button() {
		global $product;
		?>
		<input name="psupsellmaster[nonce]" type="hidden" value="<?php echo esc_attr( $this->get_nonce() ); ?>">
		<input name="psupsellmaster[base_product_id]" type="hidden" value="<?php echo esc_attr( $this->get_base_product_id() ); ?>">
		<input name="psupsellmaster[campaign_id]" type="hidden" value="<?php echo esc_attr( $this->get_campaign_id() ); ?>">
		<input name="psupsellmaster[location]" type="hidden" value="<?php echo esc_attr( $this->get_location() ); ?>">
		<input name="psupsellmaster[source]" type="hidden" value="<?php echo esc_attr( $this->get_source() ); ?>">
		<input name="psupsellmaster[view]" type="hidden" value="<?php echo esc_attr( $this->get_view() ); ?>">
		<?php
	}

	/**
	 * Add the tracking attributes to the add to cart link.
	 *
	 * @param string $sprintf The sprintf.
	 * @return string The sprintf.
	 */
	public function products_woo_loop_add_to_cart_link( $sprintf ) {
		// Set the attributes.
		$attributes = array(
			'nonce'           => $this->get_nonce(),
			'base_product_id' => $this->get_base_product_id(),
			'campaign_id'     => $this->get_campaign_id(),
			'location'        => $this->get_location(),
			'source'          => $this->get_source(),
			'view'            => $this->get_view(),
		);

		// Set the html.
		$html = wp_json_encode( $attributes );
		$html = "data-psupsellmaster='{$html}' data-psupsellmaster_nonce='{$this->get_nonce()}'";

		// Set the sprintf.
		$sprintf = preg_replace( '/(<a\b[^><]*)>/i', "$1 {$html}>", $sprintf );

		// Return the sprintf.
		return $sprintf;
	}

	/**
	 * Set the base product id.
	 *
	 * @param int $base_product_id The base product id.
	 */
	public function set_base_product_id( $base_product_id ) {
		// Set the base product id.
		$this->base_product_id = $base_product_id;
	}

	/**
	 * Set the campaign id.
	 *
	 * @param int $campaign_id The campaign id.
	 */
	public function set_campaign_id( $campaign_id ) {
		// Set the campaign id.
		$this->campaign_id = $campaign_id;
	}

	/**
	 * Set the location.
	 *
	 * @param string $location The location.
	 */
	public function set_location( $location ) {
		// Set the location.
		$this->location = $location;
	}

	/**
	 * Set the location.
	 *
	 * @param string $nonce The nonce.
	 */
	public function set_nonce( $nonce ) {
		// Set the nonce.
		$this->nonce = $nonce;
	}

	/**
	 * Set the source.
	 *
	 * @param string $source The source.
	 */
	public function set_source( $source ) {
		// Set the source.
		$this->source = $source;
	}

	/**
	 * Set the view.
	 *
	 * @param string $view The view.
	 */
	public function set_view( $view ) {
		// Set the view.
		$this->view = $view;
	}

	/**
	 * Add the tracking attributes to the add to cart link.
	 *
	 * @param int $product_id The product id.
	 */
	public function single_product_edd_purchase_link_end( $product_id ) {
		// Get the single product id.
		$single_product_id = get_the_ID();

		// Check if the product id does not match.
		if ( $single_product_id !== $product_id ) {
			return false;
		}
		?>
		<input name="psupsellmaster[nonce]" type="hidden" value="<?php echo esc_attr( $this->get_nonce() ); ?>">
		<input name="psupsellmaster[base_product_id]" type="hidden" value="<?php echo esc_attr( $this->get_base_product_id() ); ?>">
		<input name="psupsellmaster[campaign_id]" type="hidden" value="<?php echo esc_attr( $this->get_campaign_id() ); ?>">
		<input name="psupsellmaster[location]" type="hidden" value="<?php echo esc_attr( $this->get_location() ); ?>">
		<input name="psupsellmaster[source]" type="hidden" value="<?php echo esc_attr( $this->get_source() ); ?>">
		<input name="psupsellmaster[view]" type="hidden" value="<?php echo esc_attr( $this->get_view() ); ?>">
		<?php
	}

	/**
	 * Add the tracking attributes to the add to cart button.
	 */
	public function single_product_woo_after_add_to_cart_button() {
		global $product;

		// Get the product id.
		$product_id = $product->get_id();

		// Get the single product id.
		$single_product_id = get_the_ID();

		// Check if the product id does not match.
		if ( $single_product_id !== $product_id ) {
			return false;
		}
		?>
		<input name="psupsellmaster[nonce]" type="hidden" value="<?php echo esc_attr( $this->get_nonce() ); ?>">
		<input name="psupsellmaster[base_product_id]" type="hidden" value="<?php echo esc_attr( $this->get_base_product_id() ); ?>">
		<input name="psupsellmaster[campaign_id]" type="hidden" value="<?php echo esc_attr( $this->get_campaign_id() ); ?>">
		<input name="psupsellmaster[location]" type="hidden" value="<?php echo esc_attr( $this->get_location() ); ?>">
		<input name="psupsellmaster[source]" type="hidden" value="<?php echo esc_attr( $this->get_source() ); ?>">
		<input name="psupsellmaster[view]" type="hidden" value="<?php echo esc_attr( $this->get_view() ); ?>">
		<?php
	}

	/**
	 * Add the tracking attributes to the add to cart link.
	 *
	 * @param string $sprintf The sprintf.
	 * @param object $product The product.
	 * @return string The sprintf.
	 */
	public function single_product_woo_loop_add_to_cart_link( $sprintf, $product ) {
		// Get the product id.
		$product_id = $product->get_id();

		// Get the single product id.
		$single_product_id = get_the_ID();

		// Check if the product id does not match.
		if ( $single_product_id !== $product_id ) {
			// Return the sprintf.
			return $sprintf;
		}

		// Set the attributes.
		$attributes = array(
			'nonce'           => $this->get_nonce(),
			'base_product_id' => $this->get_base_product_id(),
			'campaign_id'     => $this->get_campaign_id(),
			'location'        => $this->get_location(),
			'source'          => $this->get_source(),
			'view'            => $this->get_view(),
		);

		// Set the html.
		$html = wp_json_encode( $attributes );
		$html = "data-psupsellmaster='{$html}'";

		// Set the sprintf.
		$sprintf = preg_replace( '/(<a\b[^><]*)>/i', "$1 {$html}>", $sprintf );

		// Return the sprintf.
		return $sprintf;
	}

	/**
	 * Remove the hooks.
	 *
	 * @param string $type The type of hooks to remove.
	 */
	public function remove_hooks( $type = 'all' ) {
		// Check the type.
		if ( in_array( $type, array( 'all', 'products' ), true ) ) {
			remove_filter( 'psupsellmaster_item_product_url', array( $this, 'item_product_url' ) );

			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				remove_action( 'woocommerce_after_add_to_cart_button', array( $this, 'products_woo_after_add_to_cart_button' ) );
				remove_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'products_woo_loop_add_to_cart_link' ) );

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				remove_action( 'edd_purchase_link_end', array( $this, 'products_edd_purchase_link_end' ) );
			}
		}

		// Check the type.
		if ( in_array( $type, array( 'all', 'single_product' ), true ) ) {
			// Check if the WooCommerce plugin is enabled.
			if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
				remove_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_product_woo_after_add_to_cart_button' ) );
				remove_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'single_product_woo_loop_add_to_cart_link' ) );

				// Otherwise, check if the Easy Digital Downloads plugin is enabled.
			} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
				remove_action( 'edd_purchase_link_end', array( $this, 'single_product_edd_purchase_link_end' ) );
			}
		}
	}
}

<?php
/**
 * Admin - Functions - Feedback.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send the feedback email.
 * Please note that several texts are not using translation functions.
 * Some texts should not be translated as we are sending an email in English.
 */
function psupsellmaster_feedback_send_email( $data ) {
	// Get the current user.
	$current_user = wp_get_current_user();

	// Set the to.
	$to = 'info@pluginsandsnippets.com';

	// Set the subject.
	$subject = sprintf(
		'%s (%s - %s %s)',
		'Plugin Feedback',
		'Deactivation',
		'Version',
		psupsellmaster_get_constant( 'PSUPSELLMASTER_FEEDBACK_VERSION' )
	);

	// Set the body.
	$body = '';

	// Set the rows.
	$rows = array(
		array(
			'label' => 'Plugin Name',
			'value' => psupsellmaster_get_constant( 'PSUPSELLMASTER_NAME' ),
		),
		array(
			'label' => 'Plugin Version',
			'value' => psupsellmaster_get_constant( 'PSUPSELLMASTER_VER' ),
		),
		array(
			'label' => 'Admin Name',
			'value' => $current_user->display_name,
		),
		array(
			'label' => 'Admin Email',
			'value' => get_option( 'admin_email' ),
		),
		array(
			'label' => 'Website',
			'value' => get_site_url(),
		),
		array(
			'label' => 'Website Language',
			'value' => get_bloginfo( 'language' ),
		),
		array(
			'label' => 'WordPress Version',
			'value' => get_bloginfo( 'version' ),
		),
		array(
			'label' => 'PHP Version',
			'value' => psupsellmaster_get_constant( 'PHP_VERSION' ),
		),
	);

	// Check if the Easy Digital Downloads plugin is active.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the extra.
		$extra = array(
			array(
				'label' => 'EDD Version',
				'value' => psupsellmaster_get_constant( 'EDD_VERSION' ),
			),
		);

		// Merge the rows.
		$rows = array_merge( $rows, $extra );
	}

	// Check if the WooCommerce plugin is active.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the extra.
		$extra = array(
			array(
				'label' => 'WC Version',
				'value' => psupsellmaster_get_constant( 'WC_VERSION' ),
			),
		);

		// Merge the rows.
		$rows = array_merge( $rows, $extra );
	}

	// Check the feedback version.
	if ( 2 === psupsellmaster_get_constant( 'PSUPSELLMASTER_FEEDBACK_VERSION' ) ) {
		// Set the row.
		$row = array(
			'label' => 'Reason',
			'value' => null,
		);

		// Check the data.
		if ( ! empty( $data['reason'] ) ) {
			// Set the row.
			$row['value'] = $data['reason'];
		}

		// Add the row to the list.
		array_push( $rows, $row );

		// Check the data.
		if ( ! empty( $data['help'] ) ) {
			// Set the row.
			$row = array(
				'label' => 'Help Wanted',
				'value' => 'No',
			);

			// Check the data.
			if ( ! empty( $data['help']['yes'] ) ) {
				// Set the row.
				$row['value'] = 'Yes';
			}

			// Add the row to the list.
			array_push( $rows, $row );

			// Set the row.
			$row = array(
				'label' => 'Estimated Upsells Revenue (USD/Month)',
				'value' => null,
			);

			// Check the data.
			if ( ! empty( $data['help']['revenue'] ) ) {
				// Set the row.
				$row['value'] = $data['help']['revenue'];
			}

			// Add the row to the list.
			array_push( $rows, $row );

			// Set the row.
			$row = array(
				'label' => 'Upsells Selection',
				'value' => null,
			);

			// Check the data.
			if ( ! empty( $data['help']['selection'] ) ) {
				// Set the row.
				$row['value'] = $data['help']['selection'];
			}

			// Add the row to the list.
			array_push( $rows, $row );

			// Set the location keys.
			$location_keys = array();

			// Check if the data.
			if ( ! empty( $data['help']['locations'] ) && is_array( $data['help']['locations'] ) ) {
				// Set the location keys.
				$location_keys = $data['help']['locations'];
			}

			// Set the location labels.
			$location_labels = array();

			// Loop through the locations.
			foreach ( $location_keys as $location_key ) {
				// Set the location label.
				$location_label = null;

				// Check the location key.
				switch ( $location_key ) {
					case 'product_page':
						// Set the location label.
						$location_label = 'Product Page';
						break;
					case 'blog_pages':
						// Set the location label.
						$location_label = 'Blog Pages';
						break;
					case 'cart_page':
						// Set the location label.
						$location_label = 'Cart Page';
						break;
					case 'checkout_page':
						// Set the location label.
						$location_label = 'Checkout Page';
						break;
					case 'receipt_page':
						// Set the location label.
						$location_label = 'Purchase Receipt Page';
						break;
					case 'add_to_cart_popup':
						// Set the location label.
						$location_label = 'Add to Cart Popup';
						break;
					case 'exit_intent_popup':
						// Set the location label.
						$location_label = 'Exit Intent Popup';
						break;
					case 'widget':
						// Set the location label.
						$location_label = 'Widgets in Sidebars';
						break;
					default:
						// Set the location label.
						$location_label = 'None';
				}

				// Check if the location label is empty.
				if ( empty( $location_label ) ) {
					continue;
				}

				// Add the location label to the list.
				array_push( $location_labels, $location_label );
			}

			// Set the row.
			$row = array(
				'label' => 'Upsell Locations',
				'value' => implode( ', ', $location_labels ),
			);

			// Add the row to the list.
			array_push( $rows, $row );

			// Set the row.
			$row = array(
				'label' => 'Comments',
				'value' => null,
			);

			// Check the data.
			if ( ! empty( $data['help']['comments'] ) ) {
				// Set the row.
				$row['value'] = $data['help']['comments'];
			}

			// Add the row to the list.
			array_push( $rows, $row );
		}

		// Otherwise...
	} else {
		// Set the reason key.
		$reason_key = null;

		// Check the data.
		if ( ! empty( $data['reason_key'] ) ) {
			// Set the reason key.
			$reason_key = $data['reason_key'];

			// Set the reason label.
			$reason_label = null;

			// Set the reason text.
			$reason_text = null;

			// Check the reason.
			switch ( $reason_key ) {
				case 'short_period':
					// Set the reason label.
					$reason_label = 'I only needed the plugin for a short period.';

					break;
				case 'better_plugin':
					// Set the reason label.
					$reason_label = 'I found a better plugin.';

					// Check if the reason data is empty.
					if ( empty( $data['reason_data'] ) ) {
						break;
					}

					// Check the reason key is empty.
					if ( empty( $data['reason_data'][ $reason_key ] ) ) {
						break;
					}

					// Check if the text is empty.
					if ( empty( $data['reason_data'][ $reason_key ]['text'] ) ) {
						break;
					}

					// Set the reason text.
					$reason_text = $data['reason_data'][ $reason_key ]['text'];

					break;
				case 'broke_site':
					// Set the reason label.
					$reason_label = 'The plugin broke my site.';

					break;
				case 'stopped_working':
					// Set the reason label.
					$reason_label = 'The plugin suddenly stopped working.';

					break;
				case 'no_longer_need':
					// Set the reason label.
					$reason_label = 'I no longer need the plugin.';

					break;
				case 'debug':
					// Set the reason label.
					$reason_label = "It's a temporary deactivation. I'm just debugging an issue.";

					break;
				case 'other':
					// Set the reason label.
					$reason_label = 'Other';

					// Check if the reason data is empty.
					if ( empty( $data['reason_data'] ) ) {
						break;
					}

					// Check the reason key is empty.
					if ( empty( $data['reason_data'][ $reason_key ] ) ) {
						break;
					}

					// Set the reason data.
					$reason_data = $data['reason_data'][ $reason_key ];

					// Check if the text is empty.
					if ( empty( $reason_data['text'] ) ) {
						break;
					}

					// Set the reason text.
					$reason_text = $reason_data['text'];

					break;
			}

			// Check if the reason label is not empty.
			if ( ! empty( $reason_label ) ) {
				// Set the extra.
				$extra = array(
					array(
						'label' => 'Reason',
						'value' => $reason_label,
					),
				);

				// Merge the rows.
				$rows = array_merge( $rows, $extra );

				// Check if the reason text is not empty.
				if ( ! empty( $reason_text ) ) {
					// Set the extra.
					$extra = array(
						array(
							'label' => 'Reason Info',
							'value' => $reason_text,
						),
					);

					// Merge the rows.
					$rows = array_merge( $rows, $extra );
				}
			}
		}
	}

	// Loop through the rows.
	foreach ( $rows as $row ) {
		// Check the label.
		if ( empty( $row['label'] ) ) {
			continue;
		}

		// Set the label.
		$label = $row['label'];

		// Set the value.
		$value = '&mdash;';

		// Check the value.
		if ( ! empty( $row['value'] ) ) {
			// Set the value.
			$value = $row['value'];
		}

		// Set the body.
		$body .= '<p><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '</p>';
	}

	// Set the headers.
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	// Send the email.
	$success = wp_mail( $to, $subject, $body, $headers );

	// Return the success.
	return $success;
}

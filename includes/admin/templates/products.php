<?php
/**
 * Admin - Templates - Products.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the product taxonomies.
$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects', false );
?>

<div class="wrap psupsellmaster-products">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Upsell Products', 'psupsellmaster' ); ?></h1>
	<hr class="wp-header-end">
	<div id="psupsellmaster_filters">
		<div class="psupsellmaster-fields-container">
			<div class="psupsellmaster-field-container">
				<select id="psupsellmaster_categories" class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Products Categories', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_category_taxonomy() ); ?>" multiple="multiple"></select>
			</div>
			<div class="psupsellmaster-field-container">
				<select id="psupsellmaster_tags" class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Products Tags', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_tag_taxonomy() ); ?>" multiple="multiple"></select>
			</div>
			<?php foreach ( $product_taxonomies as $product_taxonomy ) : ?>
				<?php

				// Check if the taxonomy name is empty.
				if ( empty( $product_taxonomy->name ) ) {
					continue;
				}

				// Check if the taxonomy label is empty.
				if ( empty( $product_taxonomy->label ) ) {
					continue;
				}

				// Get the taxonomy name.
				$product_taxonomy_name = $product_taxonomy->name;

				// Get the taxonomy label.
				$product_taxonomy_label = $product_taxonomy->label;
				?>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_<?php echo esc_attr( $product_taxonomy_name ); ?>" class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-custom-taxonomy" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php echo esc_attr( $product_taxonomy_label ); ?>" data-taxonomy="<?php echo esc_attr( $product_taxonomy_name ); ?>" multiple="multiple"></select>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="psupsellmaster_row">
			<input autocomplete="off" id="psupsellmaster_date_from" class="psupsellmaster_date" type="text" placeholder="<?php esc_attr_e( 'Date From', 'psupsellmaster' ); ?>" />
			<input autocomplete="off" id="psupsellmaster_date_to" class="psupsellmaster_date" type="text" placeholder="<?php esc_attr_e( 'Date To', 'psupsellmaster' ); ?>" />
			<button id="psupsellmaster_btn_this_month" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'This Month', 'psupsellmaster' ); ?></button>
			<button id="psupsellmaster_btn_last_month" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'Last Month', 'psupsellmaster' ); ?></button>
			<button id="psupsellmaster_btn_this_year" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'This Year', 'psupsellmaster' ); ?></button>
			<button id="psupsellmaster_btn_last_year" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'Last Year', 'psupsellmaster' ); ?></button>
			<button id="psupsellmaster_btn_last_week" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'Last Week', 'psupsellmaster' ); ?></button>
			<button id="psupsellmaster_btn_reset" class="psupsellmaster_btn_date_selector"><?php esc_html_e( 'Reset', 'psupsellmaster' ); ?></button>

			<div class="psupsellmaster_right">
				<button id="psupsellmaster_btn_reset_filters"><?php esc_html_e( 'Reset', 'psupsellmaster' ); ?></button>
				<button id="psupsellmaster_btn_apply_filters"><?php esc_html_e( 'Apply', 'psupsellmaster' ); ?></button>
			</div>
		</div>
	</div>
	<div class="psupsellmaster-scores-progress"></div>
	<?php if ( psupsellmaster_is_lite() ) : ?>
		<?php if ( ! psupsellmaster_is_newsletter_subscribed() ) : ?>
			<div>
				<p class="psupsellmaster-text-green">
				<strong>
					<?php
					printf(
						'%s <a class="psupsellmaster-trigger-open-modal" data-target="#psupsellmaster-modal-newsletter" href="%s">%s</a>.',
						esc_html__( 'Limited to 50 Upsells (Base Products)? Upgrade for free! Calculate up to 300 Upsells by', 'psupsellmaster' ),
						'#psupsellmaster_limit',
						esc_html__( 'Signing-up to our Newsletter', 'psupsellmaster' ),
					);
					?>
				</strong>
				</p>
			</div>
		<?php else : ?>
			<div>
				<p class="psupsellmaster-text-green">
					<strong>
						<?php
						/* translators: 1: message, 2: message, 3: PRO version URL, 4: message. */
						printf(
							'%s %s: <a class="psupsellmaster-link" href="%s" target="_blank">%s</a>',
							esc_html__( 'Limited to 300 Upsells (Base Products)?', 'psupsellmaster' ),
							esc_html__( 'Calculate unlimited Upsells', 'psupsellmaster' ),
							esc_url( PSUPSELLMASTER_PRODUCT_URL ),
							esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
						);
						?>
					</strong>
				</p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="psupsellmaster-datatable-wrapper" id="psupsellmaster_upsells_wrapper">
		<div class="psupsellmaster-table-title-container">
			<h3 class="psupsellmaster-table-title"><?php esc_html_e( 'Products - Upsells & Scores', 'psupsellmaster' ); ?></h3>
			<small class="psupsellmaster-bp-last-run-date"></small>
		</div>
		<table id="psupsellmaster_products_table">
			<thead>
				<tr>
					<th class="dt-center" data-dt-order="disable"><input type="checkbox" class="psupsellmaster_select_all_products" /></th>
					<th class="dt-left"><?php esc_html_e( 'Product', 'psupsellmaster' ); ?></th>
					<th class="dt-center"><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></th>
					<th class="dt-left" data-dt-order="disable"><?php esc_html_e( 'Upsell Products', 'psupsellmaster' ); ?></th>
					<th class="dt-left" data-dt-order="disable"><?php esc_html_e( 'Preferences', 'psupsellmaster' ); ?></th>
					<th class="dt-left"><?php esc_html_e( 'Calculated Date', 'psupsellmaster' ); ?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div class="psupsellmaster-bulk-actions" id="psupsellmaster_bulk_container" style="display: none;">
	<select class="psupsellmaster_bulk_action" style="float: left; margin-right: 0.3em;">
		<option value="" selected="selected"><?php esc_html_e( 'Bulk Actions', 'psupsellmaster' ); ?></option>
		<option value="recalc"><?php esc_html_e( 'Recalculate Upsells', 'psupsellmaster' ); ?></option>
		<option value="enable"><?php esc_html_e( 'Enable', 'psupsellmaster' ); ?></option>
		<option value="disable"><?php esc_html_e( 'Disable', 'psupsellmaster' ); ?></option>
	</select>
	<button class="dt-button button psupsellmaster-btn-apply-bulk-action" type="button"><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;<?php esc_html_e( 'Apply', 'psupsellmaster' ); ?></button>
</div>

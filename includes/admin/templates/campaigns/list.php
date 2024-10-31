<?php
/**
 * Admin - Templates - Campaigns - List.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if the user can manage options.
if ( ! current_user_can( 'manage_options' ) ) {
	// Set the error and exit.
	wp_die( esc_html__( 'Sorry, you are not allowed to view campaigns.', 'psupsellmaster' ) );
}

// Set the notices.
$notices = array();

// Check if this is the lite version.
if ( psupsellmaster_is_lite() ) {
	// Set the notice.
	$notice = sprintf(
		'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank"><strong>%s</strong></a></span>',
		esc_html__( 'PRO Tip', 'psupsellmaster' ),
		esc_html__( 'Unlock the ability to run multiple active campaigns simultaneously.', 'psupsellmaster' ),
		esc_url( PSUPSELLMASTER_PRODUCT_URL ),
		esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
	);

	// Add a notice to the list.
	array_push( $notices, $notice );
}

?>
<div class="wrap psupsellmaster-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></h1>
	<?php if ( psupsellmaster_is_pro() ) : ?>
		<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ); ?>" target="_blank"><?php esc_html_e( 'Tag Manager', 'psupsellmaster' ); ?></a>
	<?php endif; ?>
	<a class="page-title-action psupsellmaster-btn-new-campaign" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=new' ) ); ?>" target="_blank"><?php esc_html_e( 'New Campaign', 'psupsellmaster' ); ?></a>
	<hr class="wp-header-end" />
	<?php if ( ! empty( $notices ) ) : ?>
		<ul class="psupsellmaster-notices">
			<?php foreach ( $notices as $notice ) : ?>
				<li class="psupsellmaster-notice">
					<?php echo wp_kses_post( $notice ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<div class="psupsellmaster-sections">
		<section class="psupsellmaster-section">
			<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Performance', 'psupsellmaster' ); ?></h3>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-campaigns-charts">
				<div class="psupsellmaster-chart-wrapper">
					<canvas class="psupsellmaster-chart" id="psupsellmaster-campaigns-chart-main"></canvas>
				</div>
			</div>
		</section>
		<section class="psupsellmaster-section">
			<h3 class="psupsellmaster-section-title"><?php esc_html_e( "KPI's", 'psupsellmaster' ); ?></h3>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-kpis"></div>
		</section>
		<section class="psupsellmaster-section">
			<div class="psupsellmaster-section-header">
				<h3 class="psupsellmaster-section-title">
					<span><?php esc_html_e( 'Filters', 'psupsellmaster' ); ?></span>
					<button id="psupsellmaster-btn-toggle-filters" type="button"><i class="fas fa-expand-alt"></i></button>
				</h3>
				<?php if ( psupsellmaster_is_lite() ) : ?>
					<p class="psupsellmaster-paragraph">
						<strong class="psupsellmaster-text-green">
							<?php
							/* translators: 1: Text, 2: PRO version URL, 3: Text. */
							printf(
								'%s: <a class="psupsellmaster-link" href="%s" target="_blank">%s</a>',
								esc_html__( 'Get tons of excellent filters to better compare and customize Campaign Reports data', 'psupsellmaster' ),
								esc_url( PSUPSELLMASTER_PRODUCT_URL ),
								esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
							);
							?>
						</strong>
					</p>
				<?php endif; ?>
			</div>
			<div class="psupsellmaster-filters psupsellmaster-filters-toggle" style="display: none;">
				<hr class="psupsellmaster-separator" />
				<div class="psupsellmaster-fields-container">
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-pikaday psupsellmaster-field-campaign-date-start" placeholder="<?php esc_attr_e( 'Campaign Date From', 'psupsellmaster' ); ?>" type="text" />
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-pikaday psupsellmaster-field-campaign-date-end" placeholder="<?php esc_attr_e( 'Campaign Date To', 'psupsellmaster' ); ?>" type="text" />
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<select class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-status" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Status', 'psupsellmaster' ); ?>" multiple="multiple">
							<?php $options = psupsellmaster_get_campaign_status_label_value_pairs()['items']; ?>
							<?php foreach ( $options as $option ) : ?>
								<option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<select class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-locations" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Locations', 'psupsellmaster' ); ?>" multiple="multiple">
						<?php $options = psupsellmaster_get_campaign_location_label_value_pairs()['items']; ?>
							<?php foreach ( $options as $option ) : ?>
								<option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-carts-gross-earnings-min" min="0" placeholder="<?php esc_attr_e( 'Gross Earnings From', 'psupsellmaster' ); ?>" type="number" />
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-carts-gross-earnings-max" min="0" placeholder="<?php esc_attr_e( 'Gross Earnings To', 'psupsellmaster' ); ?>" type="number" />
					</div>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-tax-min" min="0" placeholder="<?php esc_attr_e( 'Taxes From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-tax-max" min="0" placeholder="<?php esc_attr_e( 'Taxes To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-discount-min" min="0" placeholder="<?php esc_attr_e( 'Discounts From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-discount-max" min="0" placeholder="<?php esc_attr_e( 'Discounts To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-net-earnings-min" min="0" placeholder="<?php esc_attr_e( 'Net Earnings From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-net-earnings-max" min="0" placeholder="<?php esc_attr_e( 'Net Earnings To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-orders-qty-min" min="0" placeholder="<?php esc_attr_e( 'Orders From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-orders-qty-max" min="0" placeholder="<?php esc_attr_e( 'Orders To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-aov-min" min="0" placeholder="<?php esc_attr_e( 'AOV From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-aov-max" min="0" placeholder="<?php esc_attr_e( 'AOV To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-impression-min" min="0" placeholder="<?php esc_attr_e( 'Impression Events From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-impression-max" min="0" placeholder="<?php esc_attr_e( 'Impression Events To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-click-min" min="0" placeholder="<?php esc_attr_e( 'Click Events From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-click-max" min="0" placeholder="<?php esc_attr_e( 'Click Events To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-add-to-cart-min" min="0" placeholder="<?php esc_attr_e( 'Add to Cart Events From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-events-add-to-cart-max" min="0" placeholder="<?php esc_attr_e( 'Add to Cart Events To', 'psupsellmaster' ); ?>" type="number" />
						</div>
					<?php endif; ?>
					<div class="psupsellmaster-field-container psupsellmaster-filters-actions">
						<button id="psupsellmaster-btn-reset-filters"><?php esc_html_e( 'Reset', 'psupsellmaster' ); ?></button>
						<button id="psupsellmaster-btn-apply-filters"><?php esc_html_e( 'Apply', 'psupsellmaster' ); ?></button>
					</div>
				</div>
			</div>
		</section>
		<section class="psupsellmaster-section">
			<div class="psupsellmaster-section-header">
				<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></h3>
				<?php if ( psupsellmaster_is_lite() ) : ?>
					<p class="psupsellmaster-paragraph">
						<strong class="psupsellmaster-text-green">
							<?php
							/* translators: 1: Text, 2: PRO version URL, 3: Text, 4: Text. */
							printf(
								'%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s',
								esc_html__( 'Limited at only 1 Discount Campaign?', 'psupsellmaster' ),
								esc_url( PSUPSELLMASTER_PRODUCT_URL ),
								esc_html__( 'Upgrade to PRO', 'psupsellmaster' ),
								esc_html__( 'and offer more!', 'psupsellmaster' )
							);
							?>
						</strong>
					</p>
				<?php endif; ?>
				<a class="button psupsellmaster-btn-new-campaign" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=new' ) ); ?>" target="_blank"><?php esc_html_e( 'New Campaign', 'psupsellmaster' ); ?></a>
			</div>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-datatable-wrapper">
				<table class="psupsellmaster-datatable" id="psupsellmaster-datatable-campaigns">
					<thead>
						<tr>
							<th class="dt-center" data-dt-order="disable"><input class="psupsellmaster-check-rows" type="checkbox" /></th>
							<th class="dt-left"><?php esc_html_e( 'Campaign', 'psupsellmaster' ); ?></th>
							<th class="dt-left"><?php esc_html_e( 'Coupon', 'psupsellmaster' ); ?></th>
							<th class="dt-left"><?php esc_html_e( 'Status', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Priority', 'psupsellmaster' ); ?></th>
							<?php if ( psupsellmaster_is_pro() ) : ?>
								<th class="dt-right"><?php esc_html_e( 'Products', 'psupsellmaster' ); ?></th>
							<?php endif; ?>
							<th class="dt-left" data-dt-order="disable"><?php esc_html_e( 'Locations', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Impressions', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Clicks', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Add to Cart', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Orders', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Discounts', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Net Earnings', 'psupsellmaster' ); ?></th>
							<?php if ( psupsellmaster_is_pro() ) : ?>
								<th class="dt-right"><?php esc_html_e( 'AOV', 'psupsellmaster' ); ?></th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<div class="psupsellmaster-bulk-actions" style="display: none;">
					<select class="psupsellmaster-field-bulk-actions">
						<option value=""><?php esc_html_e( 'Bulk Actions', 'psupsellmaster' ); ?></option>
						<option value="activate"><?php esc_html_e( 'Activate', 'psupsellmaster' ); ?></option>
						<option value="deactivate"><?php esc_html_e( 'Deactivate', 'psupsellmaster' ); ?></option>
						<option value="duplicate"><?php esc_html_e( 'Duplicate', 'psupsellmaster' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'psupsellmaster' ); ?></option>
					</select>
					<button class="dt-button button disabled psupsellmaster-btn-apply-bulk-action" disabled="disabled" type="button"><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;<?php esc_html_e( 'Apply', 'psupsellmaster' ); ?></button>
				</div>
			</div>
		</section>
	</div>
</div>

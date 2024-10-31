<?php
/**
 * Admin - Templates - Campaigns - View.
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
	wp_die( esc_html__( 'Sorry, you are not allowed to view this campaign.', 'psupsellmaster' ) );
}

// Get the campaign id.
$campaign_id = isset( $_GET['campaign'] ) ? filter_var( sanitize_text_field( wp_unslash( $_GET['campaign'] ) ), FILTER_VALIDATE_INT ) : false;
$campaign_id = false !== $campaign_id ? $campaign_id : 0;

// Check if the campaign does not exist.
if ( ! psupsellmaster_campaign_exists( $campaign_id ) ) {
	// Set the error and exit.
	wp_die( esc_html__( 'You attempted to view a campaign that does not exist.', 'psupsellmaster' ) );
}

// Get the campaign data.
$campaign_data = psupsellmaster_get_campaign( $campaign_id );

// Get the campaign title.
$campaign_title = isset( $campaign_data['title'] ) ? $campaign_data['title'] : '';

?>
<div class="wrap psupsellmaster-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'View Campaign', 'psupsellmaster' ); ?></h1>
	<?php if ( psupsellmaster_is_pro() ) : ?>
		<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ); ?>" target="_blank"><?php esc_html_e( 'Tag Manager', 'psupsellmaster' ); ?></a>
	<?php endif; ?>
	<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns' ) ); ?>" target="_blank"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></a>
	<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Campaign', 'psupsellmaster' ); ?></a>
	<a class="page-title-action psupsellmaster-btn-duplicate-campaign" data-campaign-id="<?php echo esc_attr( $campaign_id ); ?>" href="#"><?php esc_html_e( 'Duplicate Campaign', 'psupsellmaster' ); ?></a>
	<hr class="wp-header-end" />
	<div class="psupsellmaster-sections">
		<section class="psupsellmaster-section">
			<div class="psupsellmaster-section-header">
				<h3 class="psupsellmaster-section-title">
					<span><?php esc_html_e( 'Campaign Performance:', 'psupsellmaster' ); ?></span>
					<span><?php echo esc_html( $campaign_title ); ?></span>
				</h3>
				<div class="psupsellmaster-section-buttons">
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ); ?>" target="_blank"><?php esc_html_e( 'Tag Manager', 'psupsellmaster' ); ?></a>
					<?php endif; ?>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns' ) ); ?>" target="_blank"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=edit&campaign=' . $campaign_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Campaign', 'psupsellmaster' ); ?></a>
				</div>
			</div>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-campaign-charts">
				<div class="psupsellmaster-chart-wrapper">
					<canvas class="psupsellmaster-chart" id="psupsellmaster-campaign-chart-main"></canvas>
				</div>
			</div>
		</section>
		<section class="psupsellmaster-section">
			<h3 class="psupsellmaster-section-title"><?php esc_html_e( "KPI's", 'psupsellmaster' ); ?></h3>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-kpis"></div>
		</section>
		<section class="psupsellmaster-section">
			<h3 class="psupsellmaster-section-title">
				<span><?php esc_html_e( 'Filters', 'psupsellmaster' ); ?></span>
				<button id="psupsellmaster-btn-toggle-filters" type="button"><i class="fas fa-expand-alt"></i></button>
			</h3>
			<div class="psupsellmaster-filters psupsellmaster-filters-toggle" style="display: none;">
				<hr class="psupsellmaster-separator" />
				<div class="psupsellmaster-fields-container">
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-pikaday psupsellmaster-field-carts-date-start" placeholder="<?php esc_attr_e( 'Date From', 'psupsellmaster' ); ?>" type="text" />
					</div>
					<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
						<input class="psupsellmaster-field psupsellmaster-field-pikaday psupsellmaster-field-carts-date-end" placeholder="<?php esc_attr_e( 'Date To', 'psupsellmaster' ); ?>" type="text" />
					</div>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-gross-earnings-min" min="0" placeholder="<?php esc_attr_e( 'Gross Earnings From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-gross-earnings-max" min="0" placeholder="<?php esc_attr_e( 'Gross Earnings To', 'psupsellmaster' ); ?>" type="number" />
						</div>
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
							<input class="psupsellmaster-field psupsellmaster-field-carts-quantity-min" min="0" placeholder="<?php esc_attr_e( 'Products From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-quantity-max" min="0" placeholder="<?php esc_attr_e( 'Products To', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-aov-min" min="0" placeholder="<?php esc_attr_e( 'AOV From', 'psupsellmaster' ); ?>" type="number" />
						</div>
						<div class="psupsellmaster-field-container psupsellmaster-field-container-half-width">
							<input class="psupsellmaster-field psupsellmaster-field-carts-aov-max" min="0" placeholder="<?php esc_attr_e( 'AOV To', 'psupsellmaster' ); ?>" type="number" />
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
			<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Campaign Carts', 'psupsellmaster' ); ?></h3>
			<hr class="psupsellmaster-separator" />
			<div class="psupsellmaster-datatable-wrapper">
				<table class="psupsellmaster-datatable" id="psupsellmaster-datatable-campaign-carts">
					<thead>
						<tr>
							<th class="dt-left"><?php esc_html_e( 'Date', 'psupsellmaster' ); ?></th>
							<th class="dt-left"><?php esc_html_e( 'Customer', 'psupsellmaster' ); ?></th>
							<th class="dt-left"><?php esc_html_e( 'Cart Type', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Order ID', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Cart Value', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Gross Earnings', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Taxes', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Discounts', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Net Earnings', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'Number of Products', 'psupsellmaster' ); ?></th>
							<th class="dt-right"><?php esc_html_e( 'AOV', 'psupsellmaster' ); ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="psupsellmaster-hidden">
				<input id="psupsellmaster-campaign-id" name="campaign_id" type="hidden" value="<?php echo esc_attr( $campaign_id ); ?>" />
			</div>
		</section>
	</div>
</div>

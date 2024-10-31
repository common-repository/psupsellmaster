<?php
/**
 * Admin - Templates - Results.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the customers.
$customer_list = psupsellmaster_get_customers();

// Get the product taxonomies.
$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects', false );

// Get the upsell locations.
$location_list = psupsellmaster_get_product_locations();

// Get the upsell sources.
$source_list = psupsellmaster_get_product_sources();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Upsell Results', 'psupsellmaster' ); ?></h1>
	<div class="psupsellmaster-results-summary" id="psupsellmaster_summary_main">
		<h3><?php esc_html_e( 'Upsell Summary - ', 'psupsellmaster' ); ?><span class="psupsellmaster-summary-total-value"></span><?php esc_html_e( ' Sales from Upsells', 'psupsellmaster' ); ?></h3>
		<div id="psupsellmaster_summary_details">
			<div>
				<?php esc_html_e( 'For the period', 'psupsellmaster' ); ?>
				<span id="psupsellmaster_summary_details_date_period"></span>
			</div>
			<div>
				<?php esc_html_e( 'Filters:', 'psupsellmaster' ); ?>
				<span id="psupsellmaster_summary_details_filters_text_1"><?php esc_html_e( 'Used', 'psupsellmaster' ); ?></span>
				<span id="psupsellmaster_summary_details_filters_text_2"><?php esc_html_e( 'No Filters', 'psupsellmaster' ); ?></span>
			</div>
			<div class="psupsellmaster-summary-container">
				<div class="psupsellmaster-summary-chart-container">
					<canvas class="psupsellmaster-chart" id="psupsellmaster-summary-chart"></canvas>
				</div>
				<div class="psupsellmaster-summary-tables-container">
					<div class="psupsellmaster-summary-table psupsellmaster-stats-table-top-records psupsellmaster-stats-table-top-upsells">
						<div class="psupsellmaster-summary-table-header">
							<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Top 5 Upsells', 'psupsellmaster' ); ?></span></div></div>
						</div>
						<div class="psupsellmaster-summary-table-body">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table-footer">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col">
									<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Other', 'psupsellmaster' ); ?></span>
									<span class="psupsellmaster-stats-table-row-label"></span>
								</div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col">
									<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
									<span class="psupsellmaster-stats-table-row-label"></span>
								</div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
							</div>
						</div>
					</div>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-top-records psupsellmaster-stats-table-top-base-products">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Top 5 Base Products', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Other', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-top-records psupsellmaster-stats-table-top-customers">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Top 5 Customers', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Other', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-top-records psupsellmaster-stats-table-top-orders">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Top 5 Orders', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label"></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Other', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-upsells">
						<div class="psupsellmaster-summary-table-header">
							<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Upsells', 'psupsellmaster' ); ?></span></div></div>
						</div>
						<div class="psupsellmaster-summary-table-body">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Value', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Number of Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Products', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsells per Upsell Product', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Value Range', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsell Value', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Days #', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table-footer">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsell Value per Day', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
					</div>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-products">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Base Products', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Value', 'psupsellmaster' ); ?></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Number of Base Products', 'psupsellmaster' ); ?></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsell Value per Base Product', 'psupsellmaster' ); ?></span></div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-orders">
						<div class="psupsellmaster-summary-table-header">
							<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Orders', 'psupsellmaster' ); ?></span></div></div>
						</div>
						<div class="psupsellmaster-summary-table-body">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Number of Orders incl. Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Order Value w/o Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Value', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total Order Value incl. Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Order Value excl. Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsell Value per Order', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table-footer">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Order Value incl. Upsells', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
					</div>
					<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-customers">
						<div class="psupsellmaster-summary-table-header">
							<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Customers', 'psupsellmaster' ); ?></span></div></div>
						</div>
						<div class="psupsellmaster-summary-table-body">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsell Value', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Number of Customers', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table-footer">
							<div class="psupsellmaster-summary-table-row">
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Average Upsell Value per Customer', 'psupsellmaster' ); ?></span></div>
								<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value"></span></div>
							</div>
						</div>
					</div>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-locations">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Locations', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Product Page', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Checkout Page', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Purchase Receipt Page', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Widget', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Shortcode', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Gutenberg Block', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Elementor Widget', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Add to Cart Popup', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Exit Intent Popup', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-sources">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Sources', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Upsells', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Recently Viewed Products', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-types">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Types', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed">
											<span><?php esc_html_e( 'Direct', 'psupsellmaster' ); ?></span>
											<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The customer purchased an upsell by adding it to the cart directly through the base product page.', 'psupsellmaster' ); ?>"></span>
										</span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed">
											<span><?php esc_html_e( 'Indirect', 'psupsellmaster' ); ?></span>
											<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The customer purchased an upsell by visiting the upsell product page before adding it to the cart.', 'psupsellmaster' ); ?>"></span>
										</span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed">
											<span><?php esc_html_e( 'Unknown', 'psupsellmaster' ); ?></span>
											<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( "This data is unknown because some old records don't have it stored.", 'psupsellmaster' ); ?>"></span>
										</span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
						<div class="psupsellmaster-summary-table psupsellmaster-stats-table-details-views">
							<div class="psupsellmaster-summary-table-header">
								<div class="psupsellmaster-summary-table-row"><div class="psupsellmaster-summary-table-col"><span><?php esc_html_e( 'Views', 'psupsellmaster' ); ?></span></div></div>
							</div>
							<div class="psupsellmaster-summary-table-body">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Carousel', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'List', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed">
											<span><?php esc_html_e( 'Unknown', 'psupsellmaster' ); ?></span>
											<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( "This data is unknown because some old records don't have it stored.", 'psupsellmaster' ); ?>"></span>
										</span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">0&#37;</span></div>
								</div>
							</div>
							<div class="psupsellmaster-summary-table-footer">
								<div class="psupsellmaster-summary-table-row">
									<div class="psupsellmaster-summary-table-col">
										<span class="psupsellmaster-stats-table-row-label-fixed"><?php esc_html_e( 'Total', 'psupsellmaster' ); ?></span>
										<span class="psupsellmaster-stats-table-row-label"></span>
									</div>
									<div class="psupsellmaster-summary-table-col"><span class="psupsellmaster-stats-table-row-value">100&#37;</span></div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div id="psupsellmaster_filters">
		<h3><?php esc_html_e( 'Filters', 'psupsellmaster' ); ?><i class="fas fa-expand-alt psupsellmaster_expand_filters_content"></i></h3>
		<div id="psupsellmaster_filters_content" class="psupsellmaster_hidden">
			<div class="psupsellmaster-fields-container">
				<div class="psupsellmaster-field-container psupsellmaster-field-container-full-width">
					<select id="psupsellmaster_base_products" class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Base Products', 'psupsellmaster' ); ?>" multiple="multiple"></select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_base_categories" class="psupsellmaster_categories psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Base Product Categories', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_category_taxonomy() ); ?>" multiple="multiple"></select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_base_tags" class="psupsellmaster_tags psupsellmaster_right psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Base Product Tags', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_tag_taxonomy() ); ?>" multiple="multiple"></select>
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
						<select id="psupsellmaster_base_<?php echo esc_attr( $product_taxonomy_name ); ?>" class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-custom-taxonomy" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php echo esc_attr( sprintf( 'Base %s', $product_taxonomy_label ) ); ?>" data-target-type="base" data-taxonomy="<?php echo esc_attr( $product_taxonomy_name ); ?>" multiple="multiple"></select>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="psupsellmaster-fields-container">
				<div class="psupsellmaster-field-container psupsellmaster-field-container-full-width">
					<select id="psupsellmaster_upsell_products" class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Upsell Products', 'psupsellmaster' ); ?>" multiple="multiple"></select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_upsell_categories" class="psupsellmaster_categories psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Upsell Product Categories', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_category_taxonomy() ); ?>" multiple="multiple"></select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_upsell_tags" class="psupsellmaster_tags psupsellmaster_right psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Upsell Product Tags', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( psupsellmaster_get_product_tag_taxonomy() ); ?>" multiple="multiple"></select>
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
						<select id="psupsellmaster_upsell_<?php echo esc_attr( $product_taxonomy_name ); ?>" class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-custom-taxonomy" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php echo esc_attr( sprintf( 'Upsell %s', $product_taxonomy_label ) ); ?>" data-target-type="upsell" data-taxonomy="<?php echo esc_attr( $product_taxonomy_name ); ?>" multiple="multiple"></select>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="psupsellmaster-fields-container">
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_related_products" class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Related Products', 'psupsellmaster' ); ?>" multiple="multiple"></select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_customers" class="psupsellmaster_customers psupsellmaster-select2 psupsellmaster-field" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Customers', 'psupsellmaster' ); ?>" multiple="multiple">
						<?php foreach ( $customer_list as $customer ) : ?>
							<option value="<?php echo esc_attr( $customer->ID ); ?>"><?php echo esc_html( $customer->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="psupsellmaster-fields-container">
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_location" class="psupsellmaster_location psupsellmaster-select2 psupsellmaster-field" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Upsell Location', 'psupsellmaster' ); ?>" multiple="multiple">
						<?php foreach ( $location_list as $location_key => $location_label ) : ?>
							<option value="<?php echo esc_attr( $location_key ); ?>"><?php echo esc_html( $location_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="psupsellmaster-field-container">
					<select id="psupsellmaster_source" class="psupsellmaster_source psupsellmaster-select2 psupsellmaster-field" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Upsell Source', 'psupsellmaster' ); ?>" multiple="multiple">
						<?php foreach ( $source_list as $source_key => $source_label ) : ?>
							<option value="<?php echo esc_attr( $source_key ); ?>"><?php echo esc_html( $source_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="psupsellmaster_row">
				<input id="psupsellmaster_search" type="search" placeholder="<?php esc_attr_e( 'Search', 'psupsellmaster' ); ?>" />
			</div>
			<div class="psupsellmaster_row">
				<input id="psupsellmaster_price_from" class="psupsellmaster_price" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Product Price From', 'psupsellmaster' ); ?>" />
				<input id="psupsellmaster_price_to" class="psupsellmaster_price" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Product Price To', 'psupsellmaster' ); ?>" />
				<input id="psupsellmaster_sale_from" class="psupsellmaster_price" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Sale Value From', 'psupsellmaster' ); ?>" />
				<input id="psupsellmaster_sale_to" class="psupsellmaster_price psupsellmaster_right" type="number" min="0" step="1" placeholder="<?php esc_attr_e( 'Sale Value To', 'psupsellmaster' ); ?>" />
			</div>
			<div class="psupsellmaster_row">
				<input id="psupsellmaster_date_from" class="psupsellmaster_date" type="text" placeholder="<?php esc_attr_e( 'Date From', 'psupsellmaster' ); ?>" />
				<input id="psupsellmaster_date_to" class="psupsellmaster_date" type="text" placeholder="<?php esc_attr_e( 'Date To', 'psupsellmaster' ); ?>" />
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
	</div>
	<?php if ( psupsellmaster_is_lite() ) : ?>
		<?php if ( ! psupsellmaster_is_newsletter_subscribed() ) : ?>
			<div>
				<p class="psupsellmaster-text-green">
					<strong>
						<?php
						printf(
							'%s <a class="psupsellmaster-trigger-open-modal" data-target="#psupsellmaster-modal-newsletter" href="%s">%s</a>.',
							esc_html__( 'Limited to 50 Upsell Results? Upgrade for free! Unlock up to 300 Upsell Results by', 'psupsellmaster' ),
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
							esc_html__( 'Limited to 300 Upsell Results?', 'psupsellmaster' ),
							esc_html__( 'Unlock unlimited Upsell Results', 'psupsellmaster' ),
							esc_url( PSUPSELLMASTER_PRODUCT_URL ),
							esc_html__( 'Upgrade to PRO!', 'psupsellmaster' )
						);
						?>
					</strong>
				</p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="psupsellmaster-table-container" id="psupsellmaster_results">
		<h3><?php esc_html_e( 'Results', 'psupsellmaster' ); ?></h3>
		<div class="psupsellmaster-datatable-wrapper">
			<table id="psupsellmaster_upsells" class="">
				<thead>
					<tr>
						<th class="dt-right"><?php esc_html_e( 'ID', 'psupsellmaster' ); ?></th>
						<th class="dt-right"><?php esc_html_e( 'Date', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Upsell Product', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Base Product', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Customer Name', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Location', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Source', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'Type', 'psupsellmaster' ); ?></th>
						<th class="dt-left"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></th>
						<th class="dt-right"><?php esc_html_e( 'Order ID', 'psupsellmaster' ); ?></th>
						<th class="dt-left" data-dt-order="disable"><?php esc_html_e( 'Related Products', 'psupsellmaster' ); ?></th>
						<th class="dt-right"><?php esc_html_e( 'Upsell Value', 'psupsellmaster' ); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

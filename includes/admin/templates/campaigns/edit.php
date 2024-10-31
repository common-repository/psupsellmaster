<?php
/**
 * Admin - Templates - Campaigns - Edit.
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
	wp_die( esc_html__( 'Sorry, you are not allowed to edit this campaign.', 'psupsellmaster' ) );
}

// Get the view.
$view = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';

// Check if the view is invalid.
if ( ! in_array( $view, array( 'edit', 'new' ), true ) ) {
	// Set the error and exit.
	wp_die( esc_html__( 'You attempted to access a view that does not exist.', 'psupsellmaster' ) );
}

// Check if this is the lite version.
if ( psupsellmaster_is_lite() ) {
	// Check the view.
	if ( 'new' === $view ) {
		// Check the limit.
		$limit = psupsellmaster_get_feature_limit_notices( 'campaigns_count' );

		// Check if the limit is not empty.
		if ( ! empty( $limit ) ) {
			// Set the error and exit.
			wp_die( wp_kses_post( $limit ) );
		}
	}
}

// Get the campaign id.
$campaign_id = isset( $_GET['campaign'] ) ? filter_var( sanitize_text_field( wp_unslash( $_GET['campaign'] ) ), FILTER_VALIDATE_INT ) : false;
$campaign_id = false !== $campaign_id ? $campaign_id : 0;

// Check if the view is edit and the campaign does not exist.
if ( 'edit' === $view && ! psupsellmaster_campaign_exists( $campaign_id ) ) {
	// Set the error and exit.
	wp_die( esc_html__( 'You attempted to edit a campaign that does not exist.', 'psupsellmaster' ) );
}

// Get the current timezone.
$current_timezone = new DateTime( 'now', psupsellmaster_get_timezone() );
$current_timezone = $current_timezone->format( 'T' );

// Get the product post type.
$product_post_type = psupsellmaster_get_product_post_type();

// Get the stored campaign.
$stored_campaign = psupsellmaster_get_campaign( $campaign_id );

// Get the stored title.
$stored_title = isset( $stored_campaign['title'] ) ? $stored_campaign['title'] : '';

// Get the stored status.
$stored_status = isset( $stored_campaign['status'] ) ? $stored_campaign['status'] : '';

// Get the stored priority.
$stored_priority = isset( $stored_campaign['priority'] ) ? $stored_campaign['priority'] : 10;

// Get the stored start date.
$stored_start_date = isset( $stored_campaign['start_date'] ) ? $stored_campaign['start_date'] : '';
$stored_start_date = '0000-00-00 00:00:00' !== $stored_start_date ? $stored_start_date : '';

// Check if the stored start date is not empty.
if ( ! empty( $stored_start_date ) ) {
	// Set the stored start date.
	$stored_start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $stored_start_date, ( new DateTimeZone( 'UTC' ) ) );

	// Check if the stored start date is valid.
	if ( $stored_start_date instanceof DateTime ) {
		// Set the stored start date timezone.
		$stored_start_date->setTimezone( psupsellmaster_get_timezone() );

		// Set the stored start date.
		$stored_start_date = $stored_start_date->format( 'Y-m-d' );
	}

	// Check if the stored start date is empty.
	if ( empty( $stored_start_date ) ) {
		// Set the stored start date.
		$stored_start_date = '';
	}
}

// Get the stored end date.
$stored_end_date = isset( $stored_campaign['end_date'] ) ? $stored_campaign['end_date'] : '';
$stored_end_date = '0000-00-00 00:00:00' !== $stored_end_date ? $stored_end_date : '';

// Check if the stored end date is not empty.
if ( ! empty( $stored_end_date ) ) {
	// Set the stored end date.
	$stored_end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $stored_end_date, ( new DateTimeZone( 'UTC' ) ) );

	// Check if the stored end date is valid.
	if ( $stored_end_date instanceof DateTime ) {
		// Set the stored end date timezone.
		$stored_end_date->setTimezone( psupsellmaster_get_timezone() );

		// Set the stored end date.
		$stored_end_date = $stored_end_date->format( 'Y-m-d' );
	}

	// Check if the stored end date is empty.
	if ( empty( $stored_end_date ) ) {
		// Set the stored end date.
		$stored_end_date = '';
	}
}

// Get the stored created at.
$stored_created_at = isset( $stored_campaign['created_at'] ) ? $stored_campaign['created_at'] : '';
$stored_created_at = '0000-00-00 00:00:00' !== $stored_created_at ? $stored_created_at : '';

// Get the stored updated at.
$stored_updated_at = isset( $stored_campaign['updated_at'] ) ? $stored_campaign['updated_at'] : '';
$stored_updated_at = '0000-00-00 00:00:00' !== $stored_updated_at ? $stored_updated_at : '';

// Get the stored coupon.
$stored_coupon = psupsellmaster_db_campaign_coupons_get_row_by( 'campaign_id', $campaign_id );

// Get the stored coupon id.
$stored_coupon_id = isset( $stored_coupon->coupon_id ) ? $stored_coupon->coupon_id : '';

// Get the stored coupon code.
$stored_coupon_code = isset( $stored_coupon->code ) ? $stored_coupon->code : strtoupper( 'psupsellmaster' );

// Get the stored coupon type.
$stored_coupon_type = isset( $stored_coupon->type ) ? $stored_coupon->type : '';

// Get the stored coupon amount.
$stored_coupon_amount = isset( $stored_coupon->amount ) ? $stored_coupon->amount : '';
$stored_coupon_amount = 'new' !== $view ? $stored_coupon_amount : 10;
$stored_coupon_amount = psupsellmaster_round_amount( $stored_coupon_amount );

// Get the stored origin.
$stored_origin = psupsellmaster_db_campaign_meta_select( $campaign_id, 'origin', true );
$stored_origin = ! empty( $stored_origin ) ? $stored_origin : 'user';

// Get the stored products type.
$stored_products_type = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_type', true );
$stored_products_type = ! empty( $stored_products_type ) ? $stored_products_type : 'all';

// Get the stored weekdays flag.
$stored_weekdays_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'weekdays_flag', true );
$stored_weekdays_flag = ! empty( $stored_weekdays_flag ) ? $stored_weekdays_flag : 'all';

// Get the stored products flag.
$stored_products_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'products_flag', true );
$stored_products_flag = ! empty( $stored_products_flag ) ? $stored_products_flag : 'selected';

// Get the stored locations flag.
$stored_locations_flag = psupsellmaster_db_campaign_meta_select( $campaign_id, 'locations_flag', true );
$stored_locations_flag = ! empty( $stored_locations_flag ) ? $stored_locations_flag : 'all';

// Get the stored prices.
$stored_prices = psupsellmaster_db_campaign_meta_select( $campaign_id, 'prices', true );
$stored_prices = ! empty( $stored_prices ) ? $stored_prices : array();

// Set the limit.
$limit = 20;

// Get the stored weekdays.
$stored_weekdays = psupsellmaster_get_campaign_weekdays( $campaign_id );

// Get the weekdays.
$weekdays = psupsellmaster_get_weekdays();

// Get th stored locations.
$stored_locations = psupsellmaster_get_campaign_locations( $campaign_id );

// Get the locations.
$locations = psupsellmaster_campaigns_get_locations();

// Get the product taxonomies.
$product_taxonomies = psupsellmaster_get_product_taxonomies( 'objects' );

// Get the campaign statuses.
$campaign_statuses = psupsellmaster_campaigns_get_statuses();

// Get the stored conditions.
$stored_conditions = psupsellmaster_get_campaign_conditions( $campaign_id );

// Get the stored products.
$stored_products = psupsellmaster_get_campaign_products( $campaign_id );

// Get the stored authors.
$stored_authors = psupsellmaster_get_campaign_authors( $campaign_id );

// Get the stored taxonomies.
$stored_taxonomies = psupsellmaster_get_campaign_taxonomies_terms( $campaign_id );

// Get the stored synced terms.
$stored_synced_terms = psupsellmaster_get_campaign_synced_taxonomy_terms( $campaign_id, 'psupsellmaster_product_tag' );

// Set the desktop banner placeholder width.
$desktop_banner_placeholder_width = 1200;

// Set the desktop banner placeholder height.
$desktop_banner_placeholder_height = 180;

// Set the mobile banner placeholder width.
$mobile_banner_placeholder_width = 600;

// Set the mobile banner placeholder height.
$mobile_banner_placeholder_height = 100;

// Set the desktop banner placeholder url.
$desktop_banner_placeholder_url = PSUPSELLMASTER_URL . 'assets/images/admin/campaigns/placeholder-desktop.png';

// Set the mobile banner placeholder url.
$mobile_banner_placeholder_url = PSUPSELLMASTER_URL . 'assets/images/admin/campaigns/placeholder-mobile.png';

// Set the banner placeholder title.
$banner_placeholder_title = __( 'Banner Placeholder', 'psupsellmaster' );

// Set the notices.
$notices = array();

// Check the campaign origin.
if ( 'template' === $stored_origin ) {
	// Check if there is a start or end date.
	if ( ! empty( $stored_start_date ) || ! empty( $stored_end_date ) ) {
		// Check if the campaign has not been updated since its creation.
		if ( $stored_created_at === $stored_updated_at ) {
			// Set the notice.
			$notice = sprintf(
				'<strong>%s.</strong> <span>%s <strong>%s</strong> %s.</span>',
				esc_html__( 'Please note that this campaign is template-based', 'psupsellmaster' ),
				esc_html__( 'It is a good practice to double-check the data such as the', 'psupsellmaster' ),
				esc_html__( 'start and end dates', 'psupsellmaster' ),
				esc_html__( 'to ensure they meet your specific requirements', 'psupsellmaster' )
			);

			// Add a notice to the list.
			array_push( $notices, $notice );
		}
	}
}

// Set the notice.
$notice = sprintf(
	'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s. %s!</span>',
	esc_html__( 'PRO Tip', 'psupsellmaster' ),
	esc_html__( 'Make sure to check out the', 'psupsellmaster' ),
	esc_url( 'https://www.pluginsandsnippets.com/knowledge-base/the-ultimate-calendar-for-digital-marketing-events/' ),
	esc_html__( 'Ultimate Calendar for Digital Marketing Events', 'psupsellmaster' ),
	esc_html__( 'to explore a full curated list of global events', 'psupsellmaster' ),
	esc_html__( 'It\'s a great resource for determining the ideal start and end dates for your promotions', 'psupsellmaster' )
);

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
}

// Add a notice to the list.
array_push( $notices, $notice );

?>
<div class="wrap psupsellmaster-wrap">
	<?php if ( 'new' === $view ) : ?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'New Campaign', 'psupsellmaster' ); ?></h1>
	<?php else : ?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Campaign', 'psupsellmaster' ); ?></h1>
	<?php endif; ?>
	<?php if ( psupsellmaster_is_pro() ) : ?>
		<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ); ?>" target="_blank"><?php esc_html_e( 'Tag Manager', 'psupsellmaster' ); ?></a>
	<?php endif; ?>
	<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns' ) ); ?>" target="_blank"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></a>
	<?php if ( ! empty( $campaign_id ) ) : ?>
		<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=view&campaign=' . $campaign_id ) ); ?>" target="_blank"><?php esc_html_e( 'View Campaign', 'psupsellmaster' ); ?></a>
		<a class="page-title-action psupsellmaster-btn-duplicate-campaign" data-campaign-id="<?php echo esc_attr( $campaign_id ); ?>" href="#"><?php esc_html_e( 'Duplicate Campaign', 'psupsellmaster' ); ?></a>
		<a class="page-title-action psupsellmaster-btn-save-as-template" data-campaign-id="<?php echo esc_attr( $campaign_id ); ?>" href="#"><?php esc_html_e( 'Save as Template', 'psupsellmaster' ); ?></a>
	<?php endif; ?>
	<hr class="wp-header-end" />
	<form class="psupsellmaster-form-edit-campaign" method="post">
		<input name="nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-nonce' ) ); ?>" />
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
			<section class="psupsellmaster-section psupsellmaster-section-general">
				<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'General', 'psupsellmaster' ); ?></h3>
				<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please enter the data for this campaign.', 'psupsellmaster' ); ?></p>
				<hr class="psupsellmaster-separator" />
				<div class="psupsellmaster-form-row">
					<div class="psupsellmaster-form-field psupsellmaster-form-field-title">
						<label><strong><?php esc_html_e( 'Title', 'psupsellmaster' ); ?></strong></label>
						<input class="psupsellmaster-field" name="title" type="text" value="<?php echo esc_attr( stripslashes( $stored_title ) ); ?>" />
					</div>
					<div class="psupsellmaster-form-field psupsellmaster-form-field-status">
						<label><strong><?php esc_html_e( 'Status', 'psupsellmaster' ); ?></strong></label>
						<select class="psupsellmaster-field" name="status">
							<?php foreach ( $campaign_statuses as $status_key => $status_label ) : ?>
								<option <?php disabled( $status_key, 'expired' ); ?> <?php selected( $stored_status, $status_key ); ?> value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="psupsellmaster-form-field psupsellmaster-form-field-priority">
						<label>
							<strong><?php esc_html_e( 'Priority', 'psupsellmaster' ); ?></strong>
							<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Please enter a value between 1 and 100. Lower values indicate higher priority.', 'psupsellmaster' ); ?>"></span>
						</label>
						<input class="psupsellmaster-field psupsellmaster-field-priority" max="100" min="1" name="priority" step="1" type="number" value="<?php echo esc_attr( $stored_priority ); ?>" />
					</div>
					<div class="psupsellmaster-form-field psupsellmaster-form-field-start-date">
						<label>
							<strong><?php esc_html_e( 'Start Date', 'psupsellmaster' ); ?></strong>
							<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php printf( '%s %s', esc_attr__( 'Please enter the date in your WordPress Timezone.', 'psupsellmaster' ), esc_attr( $current_timezone ) ); ?>"></span>
						</label>
						<input autocomplete="off" class="psupsellmaster-field psupsellmaster-field-pikaday" name="start_date" type="text" value="<?php echo esc_attr( $stored_start_date ); ?>" />
					</div>
					<div class="psupsellmaster-form-field psupsellmaster-form-field-end-date">
						<label>
							<strong><?php esc_html_e( 'End Date', 'psupsellmaster' ); ?></strong>
							<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php printf( '%s %s', esc_attr__( 'Please enter the date in your WordPress Timezone.', 'psupsellmaster' ), esc_attr( $current_timezone ) ); ?>"></span>
						</label>
						<input autocomplete="off" class="psupsellmaster-field psupsellmaster-field-pikaday" name="end_date" type="text" value="<?php echo esc_attr( $stored_end_date ); ?>" />
					</div>
					<div class="psupsellmaster-form-field psupsellmaster-form-field-weekdays">
						<label><strong><?php esc_html_e( 'Weekdays', 'psupsellmaster' ); ?></strong> <?php esc_html_e( '(leave empty for all)', 'psupsellmaster' ); ?></label>
						<select class="psupsellmaster-select2 psupsellmaster-field" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="weekdays[]" multiple="multiple" name="weekdays[]">
							<?php foreach ( $weekdays as $weekday_key => $weekday_label ) : ?>
								<option <?php selected( in_array( $weekday_key, $stored_weekdays, true ) ); ?> value="<?php echo esc_attr( $weekday_key ); ?>"><?php echo esc_html( $weekday_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</section>
			<section class="psupsellmaster-section psupsellmaster-section-benefits">
				<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Promotion Benefit', 'psupsellmaster' ); ?></h3>
				<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please choose the promotion benefit details for this campaign.', 'psupsellmaster' ); ?></p>
				<hr class="psupsellmaster-separator" />
				<div class="psupsellmaster-subsections">
					<section class="psupsellmaster-subsection psupsellmaster-subsection-coupons">
						<div class="psupsellmaster-form-rows">
							<div class="psupsellmaster-form-row">
								<div class="psupsellmaster-form-field psupsellmaster-form-field-coupons-flag">
									<label><strong><?php esc_html_e( 'Coupon', 'psupsellmaster' ); ?></strong></label>
									<div class="psupsellmaster-form-field-radio">
										<label><input checked="checked" class="psupsellmaster-field psupsellmaster-field-coupons-flag" name="coupons_flag" type="radio" value="campaign" /><?php esc_html_e( 'Campaign Coupon', 'psupsellmaster' ); ?></label>
										<label><input class="psupsellmaster-field psupsellmaster-field-coupons-flag" name="coupons_flag" type="radio" value="standard" /><?php esc_html_e( 'Standard Coupon', 'psupsellmaster' ); ?></label>
									</div>
								</div>
								<div class="psupsellmaster-form-field psupsellmaster-form-field-coupon-code">
									<label><strong><?php esc_html_e( 'Coupon Code', 'psupsellmaster' ); ?></strong></label>
									<input class="psupsellmaster-field" name="coupon_code" type="text" value="<?php echo esc_attr( $stored_coupon_code ); ?>" />
								</div>
								<div class="psupsellmaster-form-field psupsellmaster-form-field-standard-coupon-id" style="display: none;">
									<label><strong><?php esc_html_e( 'Standard Coupon Code', 'psupsellmaster' ); ?></strong></label>
									<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_coupons" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-coupons' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-group="standard" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="standard_coupon_id" name="standard_coupon_id"></select>
								</div>
								<div class="psupsellmaster-form-field psupsellmaster-form-field-coupon-type">
									<label><strong><?php esc_html_e( 'Coupon Type', 'psupsellmaster' ); ?></strong></label>
									<select class="psupsellmaster-field" name="coupon_type">
										<option <?php selected( $stored_coupon_type, 'discount_percentage' ); ?> value="discount_percentage"><?php esc_html_e( 'Discount - Percentage', 'psupsellmaster' ); ?></option>
										<option <?php selected( $stored_coupon_type, 'discount_fixed' ); ?> value="discount_fixed"><?php esc_html_e( 'Discount - Fixed', 'psupsellmaster' ); ?></option>
									</select>
								</div>
								<div class="psupsellmaster-form-field psupsellmaster-form-field-coupon-amount">
									<label><strong><?php esc_html_e( 'Coupon Amount', 'psupsellmaster' ); ?></strong></label>
									<input class="psupsellmaster-field" name="coupon_amount" step="any" type="number" value="<?php echo esc_attr( $stored_coupon_amount ); ?>" />
								</div>
							</div>
							<div class="psupsellmaster-form-row psupsellmaster-form-warning" style="display: none;">
								<strong class="psupsellmaster-warning"><?php esc_html_e( 'Please note that when the Standard Coupon is selected, the Campaign will take over the existing coupon, and all its data will be overwritten going forward. Therefore when the campaign is saved, it will become a Campaign Coupon.', 'psupsellmaster' ); ?></strong>
							</div>
						</div>
					</section>
				</div>
			</section>
			<section class="psupsellmaster-section psupsellmaster-section-products">
				<div class="psupsellmaster-subsections">
					<section class="psupsellmaster-subsection psupsellmaster-subsection-products-selection psupsellmaster-product-selector">
						<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Product Selector', 'psupsellmaster' ); ?></h3>
						<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please choose the products that are entitled to the benefits from this campaign.', 'psupsellmaster' ); ?></p>
						<hr class="psupsellmaster-separator" />
						<div class="psupsellmaster-form-field psupsellmaster-form-field-products-flag">
							<label><input <?php checked( $stored_products_flag, 'all' ); ?> class="psupsellmaster-field psupsellmaster-field-products-flag" name="products_flag" type="radio" value="all" /><?php esc_html_e( 'All products', 'psupsellmaster' ); ?></label>
							<label><input <?php checked( $stored_products_flag, 'selected' ); ?> class="psupsellmaster-field psupsellmaster-field-products-flag" name="products_flag" type="radio" value="selected" /><?php esc_html_e( 'Selected products', 'psupsellmaster' ); ?></label>
						</div>
						<div class="psupsellmaster-form-options" <?php echo wp_kses_post( 'all' === $stored_products_flag ? 'style="display: none;"' : '' ); ?>>
							<p><?php esc_html_e( 'Navigate through the tabs to include or exclude items.', 'psupsellmaster' ); ?></p>
							<?php
							// Set the sections tabs.
							$sections_tabs = array(
								'include' => __( 'Include', 'psupsellmaster' ),
								'exclude' => __( 'Exclude', 'psupsellmaster' ),
							);
							?>
							<div class="psupsellmaster-tabs psupsellmaster-tabs-vertical" data-key="products" style="display: none;">
								<ul class="psupsellmaster-tabs-header">
									<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-1"><?php esc_html_e( 'Products', 'psupsellmaster' ); ?> <span class="psupsellmaster-count"></span></a></li>
									<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-2"><?php esc_html_e( 'Authors', 'psupsellmaster' ); ?> <span class="psupsellmaster-count"></span></a></li>
									<?php if ( psupsellmaster_is_pro() ) : ?>
										<?php $count_tabs = 3; ?>
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
											?>
											<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>"><?php echo esc_html( $product_taxonomy->label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php ++$count_tabs; ?>
										<?php endforeach; ?>
									<?php endif; ?>
									<li class="psupsellmaster-tab psupsellmaster-tab-reset" data-action="reset"><a class="psupsellmaster-btn-reset" href="#reset"><?php esc_html_e( 'Clear All', 'psupsellmaster' ); ?></a></li>
								</ul>
								<section class="psupsellmaster-tab-section" data-entity="products" id="psupsellmaster-fieldset-tab-1">
									<?php
									// Set the notices.
									$notices = array();

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
										esc_html__( 'Quickly', 'psupsellmaster' ),
										esc_url( admin_url( "edit.php?post_type={$product_post_type}" ) ),
										esc_html__( 'manage products', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' )
									);

									// Add a notice to the list.
									array_push( $notices, $notice );
									?>
									<?php if ( ! empty( $notices ) ) : ?>
										<ul class="psupsellmaster-notices">
											<?php foreach ( $notices as $notice ) : ?>
												<li class="psupsellmaster-notice">
													<?php echo wp_kses_post( $notice ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
									<div class="psupsellmaster-tabs">
										<ul class="psupsellmaster-tabs-header">
											<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
												<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-1-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php endforeach; ?>
										</ul>
										<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
											<section class="psupsellmaster-tab-section" data-type="<?php echo esc_attr( $tab_key ); ?>" id="psupsellmaster-fieldset-tab-1-<?php echo esc_attr( $tab_key ); ?>">
												<?php if ( 'include' === $tab_key ) : ?>	
													<div class="psupsellmaster-form-rows">
														<?php if ( psupsellmaster_is_plugin_active( 'edd' ) ) : ?>
															<div class="psupsellmaster-form-row">
																<div class="psupsellmaster-form-field psupsellmaster-form-field-products-type">
																	<label><input <?php checked( $stored_products_type, 'bundle' ); ?> class="psupsellmaster-field psupsellmaster-field-products-type" data-default-checked="false" name="products_type" type="checkbox" value="bundle"><?php esc_html_e( 'Bundles Only', 'psupsellmaster' ); ?></label>
																</div>
															</div>
														<?php endif; ?>
														<div class="psupsellmaster-form-row">
															<div class="psupsellmaster-form-field psupsellmaster-form-field-prices-min">
																<?php $stored_price_min = isset( $stored_prices['min'] ) ? $stored_prices['min'] : ''; ?>
																<label>
																	<strong>
																		<?php
																		/* translators: 1: field label, 2: currency symbol. */
																		printf(
																			'%s (%s)',
																			esc_html__( 'Minimum Price', 'psupsellmaster' ),
																			esc_html( psupsellmaster_get_currency_symbol() )
																		);
																		?>
																	</strong>
																</label>
																<input class="psupsellmaster-field psupsellmaster-field-prices-min" min="0" name="prices[min]" step="1" type="number" value="<?php echo esc_attr( $stored_price_min ); ?>" />
															</div>
															<div class="psupsellmaster-form-field psupsellmaster-form-field-prices-max">
																<?php $stored_price_max = isset( $stored_prices['max'] ) ? $stored_prices['max'] : ''; ?>
																<label>
																	<strong>
																		<?php
																		/* translators: 1: field label, 2: currency symbol. */
																		printf(
																			'%s (%s)',
																			esc_html__( 'Maximum Price', 'psupsellmaster' ),
																			esc_html( psupsellmaster_get_currency_symbol() )
																		);
																		?>
																	</strong>
																</label>
																<input class="psupsellmaster-field psupsellmaster-field-prices-max" min="0" name="prices[max]" step="1" type="number" value="<?php echo esc_attr( $stored_price_max ); ?>" />
															</div>
														</div>
													</div>
												<?php endif; ?>
												<div class="psupsellmaster-repeater">
													<div class="psupsellmaster-repeater-rows">
														<?php
														// Set the count stored.
														$count_stored = 0;

														// Make sure the include array key has at least 1 entry.
														if ( empty( $stored_products['include'] ) ) {
															// Set the include array key.
															$stored_products['include'] = array( array() );
														}

														// Make sure the exclude array key has at least 1 entry.
														if ( empty( $stored_products['exclude'] ) ) {
															// Set the exclude array key.
															$stored_products['exclude'] = array( array() );
														}
														?>
														<?php foreach ( $stored_products[ $tab_key ] as $stored_data ) : ?>
															<?php
															// Get the stored product id.
															$stored_product_id = isset( $stored_data['product_id'] ) ? $stored_data['product_id'] : '';

															// Set the options.
															$options = array();

															// Check the stored product id.
															if ( ! empty( $stored_product_id ) ) {
																// Get the options.
																$options = psupsellmaster_get_product_label_value_pairs(
																	array( 'post__in' => array( $stored_product_id ) )
																)['items'];
															}
															?>
															<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																<div class="psupsellmaster-repeater-row-content">
																	<div class="psupsellmaster-form-field">
																		<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="products[<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][product_id]" name="products[<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][product_id]">
																			<?php foreach ( $options as $option ) : ?>
																				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>
																<div class="psupsellmaster-repeater-row-actions">
																	<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																</div>
															</div>
															<?php ++$count_stored; ?>
														<?php endforeach; ?>
													</div>
													<div class="psupsellmaster-repeater-actions">
														<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
													</div>
												</div>
											</section>
										<?php endforeach; ?>
									</div>
								</section>
								<section class="psupsellmaster-tab-section" data-entity="authors" id="psupsellmaster-fieldset-tab-2">
									<?php
									// Set the notices.
									$notices = array();

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
										esc_html__( 'Quickly', 'psupsellmaster' ),
										esc_url( admin_url( 'users.php' ) ),
										esc_html__( 'manage authors', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' )
									);

									// Add a notice to the list.
									array_push( $notices, $notice );

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Tool', 'psupsellmaster' ),
										esc_html__( 'Easily', 'psupsellmaster' ),
										esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ),
										esc_html__( 'check which products are associated with specific authors', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' ),
									);

									// Add a notice to the list.
									array_push( $notices, $notice );
									?>
									<?php if ( ! empty( $notices ) ) : ?>
										<ul class="psupsellmaster-notices">
											<?php foreach ( $notices as $notice ) : ?>
												<li class="psupsellmaster-notice">
													<?php echo wp_kses_post( $notice ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
									<div class="psupsellmaster-tabs">
										<ul class="psupsellmaster-tabs-header">
											<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
												<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-2-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php endforeach; ?>
										</ul>
										<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
											<section class="psupsellmaster-tab-section" data-type="<?php echo esc_attr( $tab_key ); ?>" id="psupsellmaster-fieldset-tab-2-<?php echo esc_attr( $tab_key ); ?>">
												<div class="psupsellmaster-repeater">
													<div class="psupsellmaster-repeater-rows">
														<?php
														// Set the count stored.
														$count_stored = 0;

														// Make sure the include array key has at least 1 entry.
														if ( empty( $stored_authors['include'] ) ) {
															// Set the include array key.
															$stored_authors['include'] = array( array() );
														}

														// Make sure the exclude array key has at least 1 entry.
														if ( empty( $stored_authors['exclude'] ) ) {
															// Set the exclude array key.
															$stored_authors['exclude'] = array( array() );
														}
														?>
														<?php foreach ( $stored_authors[ $tab_key ] as $stored_data ) : ?>
															<?php
															// Get the stored author id.
															$stored_author_id = isset( $stored_data['author_id'] ) ? $stored_data['author_id'] : '';

															// Set the options.
															$options = array();

															// Check the stored author id.
															if ( ! empty( $stored_author_id ) ) {
																// Get the options.
																$options = psupsellmaster_get_product_author_label_value_pairs(
																	array( 'include' => array( $stored_author_id ) )
																)['items'];
															}
															?>
															<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																<div class="psupsellmaster-repeater-row-content">
																	<div class="psupsellmaster-form-field">
																		<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_product_authors" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-product-authors' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="authors[<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][author_id]" name="authors[<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][author_id]">
																			<?php foreach ( $options as $option ) : ?>
																				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>
																<div class="psupsellmaster-repeater-row-actions">
																	<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																</div>
															</div>
															<?php ++$count_stored; ?>
														<?php endforeach; ?>
													</div>
													<div class="psupsellmaster-repeater-actions">
														<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
													</div>
												</div>
											</section>
										<?php endforeach; ?>
									</div>
								</section>
								<?php if ( psupsellmaster_is_pro() ) : ?>
									<?php $count_tabs = 3; ?>
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
										$taxonomy_name = $product_taxonomy->name;

										// Get the taxonomy label.
										$taxonomy_label = $product_taxonomy->label;

										// Check if the stored taxonomies is empty for this taxonomy.
										if ( empty( $stored_taxonomies[ $taxonomy_name ] ) ) {
											// Set the stored taxonomies.
											$stored_taxonomies[ $taxonomy_name ] = array(
												'include' => array( array() ),
												'exclude' => array( array() ),
											);

											// Otherwise...
										} else {
											// Make sure the include array key has at least 1 entry.
											if ( empty( $stored_taxonomies[ $taxonomy_name ]['include'] ) ) {
												// Set the include array key.
												$stored_taxonomies[ $taxonomy_name ]['include'] = array( array() );
											}

											// Make sure the exclude array key has at least 1 entry.
											if ( empty( $stored_taxonomies[ $taxonomy_name ]['exclude'] ) ) {
												// Set the exclude array key.
												$stored_taxonomies[ $taxonomy_name ]['exclude'] = array( array() );
											}
										}
										?>
										<section class="psupsellmaster-tab-section" data-entity="taxonomies" data-taxonomy="<?php echo esc_attr( $taxonomy_name ); ?>" id="psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>">
											<?php
											// Set the notices.
											$notices = array();

											// Set the notice.
											$notice = sprintf(
												'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
												esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
												esc_html__( 'Quickly', 'psupsellmaster' ),
												esc_url( admin_url( "edit-tags.php?post_type={$product_post_type}&taxonomy={$taxonomy_name}" ) ),
												esc_html__( 'manage', 'psupsellmaster' ),
												esc_html( $taxonomy_label ),
												esc_html__( 'at any time', 'psupsellmaster' )
											);

											// Add a notice to the list.
											array_push( $notices, $notice );

											// Set the notice.
											$notice = sprintf(
												'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
												esc_html__( 'PRO Tool', 'psupsellmaster' ),
												esc_html__( 'Easily', 'psupsellmaster' ),
												esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ),
												esc_html__( 'check which products are associated with specific', 'psupsellmaster' ),
												esc_html( $taxonomy_label ),
												esc_html__( 'at any time', 'psupsellmaster' ),
											);

											// Add a notice to the list.
											array_push( $notices, $notice );
											?>
											<?php if ( ! empty( $notices ) ) : ?>
												<ul class="psupsellmaster-notices">
													<?php foreach ( $notices as $notice ) : ?>
														<li class="psupsellmaster-notice">
															<?php echo wp_kses_post( $notice ); ?>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
											<div class="psupsellmaster-tabs">
												<ul class="psupsellmaster-tabs-header">
													<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
														<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
													<?php endforeach; ?>
												</ul>
												<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
													<section class="psupsellmaster-tab-section" data-type="<?php echo esc_attr( $tab_key ); ?>" id="psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>-<?php echo esc_attr( $tab_key ); ?>">
														<div class="psupsellmaster-repeater">
															<div class="psupsellmaster-repeater-rows">
																<?php $count_stored = 0; ?>
																<?php foreach ( $stored_taxonomies[ $taxonomy_name ][ $tab_key ] as $stored_data ) : ?>
																	<?php
																	// Get the stored term id.
																	$stored_term_id = isset( $stored_data['term_id'] ) ? $stored_data['term_id'] : '';

																	// Set the options.
																	$options = array();

																	// Check the stored term id.
																	if ( ! empty( $stored_term_id ) ) {
																		// Get the options.
																		$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
																			array(
																				'include'  => array( $stored_term_id ),
																				'taxonomy' => $taxonomy_name,
																			)
																		)['items'];
																	}
																	?>
																	<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																		<div class="psupsellmaster-repeater-row-content">
																			<div class="psupsellmaster-form-field">
																				<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy_name ); ?>" id="taxonomies[<?php echo esc_attr( $taxonomy_name ); ?>][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][term_id]" name="taxonomies[<?php echo esc_attr( $taxonomy_name ); ?>][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][term_id]">
																					<?php foreach ( $options as $option ) : ?>
																						<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																					<?php endforeach; ?>
																				</select>
																			</div>
																		</div>
																		<div class="psupsellmaster-repeater-row-actions">
																			<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																		</div>
																	</div>
																	<?php ++$count_stored; ?>
																<?php endforeach; ?>
															</div>
															<div class="psupsellmaster-repeater-actions">
																<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
															</div>
														</div>
													</section>
												<?php endforeach; ?>
											</div>
										</section>
										<?php ++$count_tabs; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					</section>
					<section class="psupsellmaster-subsection psupsellmaster-subsection-selected-products">
						<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Selected Products', 'psupsellmaster' ); ?></h3>
						<p class="psupsellmaster-section-subtitle">
							<span>
								<?php esc_html_e( 'The following products have been selected to receive the promotion benefits (e.g. a discount) as per the selection above.', 'psupsellmaster' ); ?>
							</span>
							<?php if ( psupsellmaster_is_pro() ) : ?>
								<span>
									<strong><?php esc_html_e( 'Feel free to tag them by assigning them to an UpsellMaster tag for future reference.', 'psupsellmaster' ); ?></strong>
								</span>
							<?php endif; ?>
						</p>
						<hr class="psupsellmaster-separator">
						<div class="psupsellmaster-datatable-wrapper" data-source="stored">
							<table class="psupsellmaster-datatable" id="psupsellmaster-datatable-eligible-products">
								<thead>
									<tr>
										<th class="dt-left"><?php esc_html_e( 'Product', 'psupsellmaster' ); ?></th>
										<th class="dt-left"><?php esc_html_e( 'Author', 'psupsellmaster' ); ?></th>
										<th class="dt-left"><?php esc_html_e( 'UpsellMaster Tags', 'psupsellmaster' ); ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<div class="psupsellmaster-extra-buttons" style="display: none;">
								<button class="dt-button button psupsellmaster-btn-refresh" type="button"><i class="fa fa-sync-alt" aria-hidden="true"></i>&nbsp;<?php esc_html_e( 'Refresh', 'psupsellmaster' ); ?></button>
							</div>
						</div>
					</section>
					<?php if ( psupsellmaster_is_pro() ) : ?>
						<section class="psupsellmaster-subsection psupsellmaster-subsection-assign-tags">
							<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'UpsellMaster Tags', 'psupsellmaster' ); ?></h3>
							<p class="psupsellmaster-section-subtitle">
								<span>
									<?php esc_html_e( 'Easily assign UpsellMaster Tags to the selected products.', 'psupsellmaster' ); ?>
								</span>
							</p>
							<hr class="psupsellmaster-separator">
							<div class="psupsellmaster-form-rows">
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-synced-tags">
										<?php
										// Set the taxonomy name.
										$taxonomy_name = 'psupsellmaster_product_tag';

										// Set the options.
										$options = array();

										// Check if the stored terms is not empty.
										if ( ! empty( $stored_synced_terms ) ) {
											// Make sure there is at least one item in the list.
											array_push( $stored_synced_terms, -1 );

											// Get the options.
											$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
												array(
													'include'  => $stored_synced_terms,
													'taxonomy' => $taxonomy_name,
												)
											)['items'];
										}
										?>
										<label><strong><?php esc_html_e( 'Assign UpsellMaster Tags', 'psupsellmaster' ); ?></strong></label>
										<select class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-synced-tags" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-clear="true" data-custom="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy_name ); ?>" id="synced[taxonomies][<?php echo esc_attr( $taxonomy_name ); ?>][]" multiple="multiple" name="synced[taxonomies][<?php echo esc_attr( $taxonomy_name ); ?>][]">
											<?php foreach ( $options as $option ) : ?>
												<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-btn">
										<button class="button psupsellmaster-btn-assign-tags" type="button"><?php esc_html_e( 'Assign', 'psupsellmaster' ); ?></button>
										<span class="spinner"></span>
									</div>
								</div>
							</div>
							<?php
							// Set the notices.
							$notices = array();

							// Set the notice.
							$notice = sprintf(
								'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
								esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
								esc_html__( 'Quickly', 'psupsellmaster' ),
								esc_url( admin_url( "edit-tags.php?post_type={$product_post_type}&taxonomy={psupsellmaster_product_tag}" ) ),
								esc_html__( 'manage', 'psupsellmaster' ),
								esc_html__( 'UpsellMaster Tags', 'psupsellmaster' ),
								esc_html__( 'at any time', 'psupsellmaster' )
							);

							// Add a notice to the list.
							array_push( $notices, $notice );

							// Set the notice.
							$notice = sprintf(
								'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
								esc_html__( 'PRO Tool', 'psupsellmaster' ),
								esc_html__( 'Easily', 'psupsellmaster' ),
								esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ),
								esc_html__( 'check which products are associated with specific', 'psupsellmaster' ),
								esc_html__( 'UpsellMaster Tags', 'psupsellmaster' ),
								esc_html__( 'at any time', 'psupsellmaster' ),
							);

							// Add a notice to the list.
							array_push( $notices, $notice );
							?>
							<?php if ( ! empty( $notices ) ) : ?>
								<ul class="psupsellmaster-notices">
									<?php foreach ( $notices as $notice ) : ?>
										<li class="psupsellmaster-notice">
											<?php echo wp_kses_post( $notice ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</section>
					<?php endif; ?>
				</div>
			</section>
			<?php if ( psupsellmaster_is_pro() ) : ?>
				<section class="psupsellmaster-section psupsellmaster-section-promotion-conditions">
					<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Promotion Triggers', 'psupsellmaster' ); ?></h3>
					<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please choose the purchase conditions that need to be fulfilled by the customer in order to qualify for the campaign benefit. The available criteria refer to the number of products to be added to customers\'s shopping cart, the minimum value of the cart, and what product combinations qualify.', 'psupsellmaster' ); ?></p>
					<hr class="psupsellmaster-separator" />
					<div class="psupsellmaster-subsections">
						<section class="psupsellmaster-subsection">
							<div class="psupsellmaster-form-row">
								<div class="psupsellmaster-form-field psupsellmaster-form-field-products-count-min">
									<label>
										<strong><?php esc_html_e( 'Minimum Products Qty', 'psupsellmaster' ); ?></strong>
										<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Please enter the required minimum quantity. Please refer to the general settings for the specific interpretation of the quantity, either as Distinct Products or Total Products.', 'psupsellmaster' ); ?>"></span>
									</label>
									<input class="psupsellmaster-field" min="0" name="conditions[products][count][min]" step="1" type="number" value="<?php echo esc_attr( $stored_conditions['products']['count']['min'] ); ?>" />
								</div>
								<div class="psupsellmaster-form-field psupsellmaster-form-field-subtotal-min">
									<label>
										<strong>
											<?php
											/* translators: 1: field label, 2: currency symbol. */
											printf(
												'%s (%s)',
												esc_html__( 'Minimum Subtotal', 'psupsellmaster' ),
												esc_html( psupsellmaster_get_currency_symbol() )
											);
											?>
										</strong>
									</label>
									<input class="psupsellmaster-field" min="0" name="conditions[subtotal][min]" step="any" type="number" value="<?php echo esc_attr( $stored_conditions['subtotal']['min'] ); ?>" />
								</div>
							</div>
						</section>
						<section class="psupsellmaster-subsection">
							<hr class="psupsellmaster-separator" />
							<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please choose the product conditions for this campaign. Leave it empty for no product requirements in the store cart.', 'psupsellmaster' ); ?></p>
							<?php
							// Set the sections tabs.
							$sections_tabs = array(
								'include' => __( 'Include', 'psupsellmaster' ),
								'exclude' => __( 'Exclude', 'psupsellmaster' ),
							);
							?>
							<div class="psupsellmaster-tabs psupsellmaster-tabs-vertical" data-key="conditions" style="display: none;">
								<ul class="psupsellmaster-tabs-header">
									<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-1"><?php esc_html_e( 'Products', 'psupsellmaster' ); ?> <span class="psupsellmaster-count"></span></a></li>
									<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-2"><?php esc_html_e( 'Authors', 'psupsellmaster' ); ?> <span class="psupsellmaster-count"></span></a></li>
									<?php if ( psupsellmaster_is_pro() ) : ?>
										<?php $count_tabs = 3; ?>
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
											?>
											<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>"><?php echo esc_html( $product_taxonomy->label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php ++$count_tabs; ?>
										<?php endforeach; ?>
									<?php endif; ?>
									<li class="psupsellmaster-tab psupsellmaster-tab-reset" data-action="reset"><a class="psupsellmaster-btn-reset" href="#reset"><?php esc_html_e( 'Clear All', 'psupsellmaster' ); ?></a></li>
								</ul>
								<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-1">									
									<?php
									// Set the notices.
									$notices = array();

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
										esc_html__( 'Quickly', 'psupsellmaster' ),
										esc_url( admin_url( "edit.php?post_type={$product_post_type}" ) ),
										esc_html__( 'manage products', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' )
									);

									// Add a notice to the list.
									array_push( $notices, $notice );
									?>
									<?php if ( ! empty( $notices ) ) : ?>
										<ul class="psupsellmaster-notices">
											<?php foreach ( $notices as $notice ) : ?>
												<li class="psupsellmaster-notice">
													<?php echo wp_kses_post( $notice ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
									<div class="psupsellmaster-tabs">
										<ul class="psupsellmaster-tabs-header">
											<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
												<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-1-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php endforeach; ?>
										</ul>
										<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
											<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-1-<?php echo esc_attr( $tab_key ); ?>">
												<div class="psupsellmaster-repeater">
													<div class="psupsellmaster-repeater-rows">
														<?php
														// Set the count stored.
														$count_stored = 0;

														// Make sure the include array key has at least 1 entry.
														if ( empty( $stored_conditions['products']['include'] ) ) {
															// Set the include array key.
															$stored_conditions['products']['include'] = array( array() );
														}

														// Make sure the exclude array key has at least 1 entry.
														if ( empty( $stored_conditions['products']['exclude'] ) ) {
															// Set the exclude array key.
															$stored_conditions['products']['exclude'] = array( array() );
														}
														?>
														<?php foreach ( $stored_conditions['products'][ $tab_key ] as $stored_data ) : ?>
															<?php
															// Get the stored product id.
															$stored_product_id = isset( $stored_data['product_id'] ) ? $stored_data['product_id'] : '';

															// Set the options.
															$options = array();

															// Check the stored product id.
															if ( ! empty( $stored_product_id ) ) {
																// Get the options.
																$options = psupsellmaster_get_product_label_value_pairs(
																	array( 'post__in' => array( $stored_product_id ) )
																)['items'];
															}
															?>
															<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																<div class="psupsellmaster-repeater-row-content">
																	<div class="psupsellmaster-form-field">
																		<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_products" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-products' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="conditions[products][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][product_id]" name="conditions[products][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][product_id]">
																			<?php foreach ( $options as $option ) : ?>
																				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>
																<div class="psupsellmaster-repeater-row-actions">
																	<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																</div>
															</div>
															<?php ++$count_stored; ?>
														<?php endforeach; ?>
													</div>
													<div class="psupsellmaster-repeater-actions">
														<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
													</div>
												</div>
											</section>
										<?php endforeach; ?>
									</div>
								</section>
								<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-2">
									<?php
									// Set the notices.
									$notices = array();

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
										esc_html__( 'Quickly', 'psupsellmaster' ),
										esc_url( admin_url( 'users.php' ) ),
										esc_html__( 'manage authors', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' )
									);

									// Add a notice to the list.
									array_push( $notices, $notice );

									// Set the notice.
									$notice = sprintf(
										'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s</a> %s.</span>',
										esc_html__( 'PRO Tool', 'psupsellmaster' ),
										esc_html__( 'Easily', 'psupsellmaster' ),
										esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ),
										esc_html__( 'check which products are associated with specific authors', 'psupsellmaster' ),
										esc_html__( 'at any time', 'psupsellmaster' ),
									);

									// Add a notice to the list.
									array_push( $notices, $notice );
									?>
									<?php if ( ! empty( $notices ) ) : ?>
										<ul class="psupsellmaster-notices">
											<?php foreach ( $notices as $notice ) : ?>
												<li class="psupsellmaster-notice">
													<?php echo wp_kses_post( $notice ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
									<div class="psupsellmaster-tabs">
										<ul class="psupsellmaster-tabs-header">
											<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
												<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-2-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
											<?php endforeach; ?>
										</ul>
										<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
											<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-2-<?php echo esc_attr( $tab_key ); ?>">
												<div class="psupsellmaster-repeater">
													<div class="psupsellmaster-repeater-rows">
														<?php
														// Set the count stored.
														$count_stored = 0;

														// Make sure the include array key has at least 1 entry.
														if ( empty( $stored_conditions['authors']['include'] ) ) {
															// Set the include array key.
															$stored_conditions['authors']['include'] = array( array() );
														}

														// Make sure the exclude array key has at least 1 entry.
														if ( empty( $stored_conditions['authors']['exclude'] ) ) {
															// Set the exclude array key.
															$stored_conditions['authors']['exclude'] = array( array() );
														}
														?>
														<?php foreach ( $stored_conditions['authors'][ $tab_key ] as $stored_data ) : ?>
															<?php
															// Get the stored author id.
															$stored_author_id = isset( $stored_data['author_id'] ) ? $stored_data['author_id'] : '';

															// Set the options.
															$options = array();

															// Check the stored author id.
															if ( ! empty( $stored_author_id ) ) {
																// Get the options.
																$options = psupsellmaster_get_product_author_label_value_pairs(
																	array( 'include' => array( $stored_author_id ) )
																)['items'];
															}
															?>
															<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																<div class="psupsellmaster-repeater-row-content">
																	<div class="psupsellmaster-form-field">
																		<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_product_authors" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-product-authors' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="conditions[authors][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][author_id]" name="conditions[authors][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][author_id]">
																			<?php foreach ( $options as $option ) : ?>
																				<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>
																<div class="psupsellmaster-repeater-row-actions">
																	<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																</div>
															</div>
															<?php ++$count_stored; ?>
														<?php endforeach; ?>
													</div>
													<div class="psupsellmaster-repeater-actions">
														<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
													</div>
												</div>
											</section>
										<?php endforeach; ?>
									</div>
								</section>
								<?php if ( psupsellmaster_is_pro() ) : ?>
									<?php $count_tabs = 3; ?>
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
										$taxonomy_name = $product_taxonomy->name;

										// Get the taxonomy label.
										$taxonomy_label = $product_taxonomy->label;

										// Check if the stored taxonomies is empty for this taxonomy.
										if ( empty( $stored_conditions['taxonomies'][ $taxonomy_name ] ) ) {
											// Set the stored taxonomies.
											$stored_conditions['taxonomies'][ $taxonomy_name ] = array(
												'include' => array( array() ),
												'exclude' => array( array() ),
											);

											// Otherwise...
										} else {
											// Make sure the include array key has at least 1 entry.
											if ( empty( $stored_conditions['taxonomies'][ $taxonomy_name ]['include'] ) ) {
												// Set the include array key.
												$stored_conditions['taxonomies'][ $taxonomy_name ]['include'] = array( array() );
											}

											// Make sure the exclude array key has at least 1 entry.
											if ( empty( $stored_conditions['taxonomies'][ $taxonomy_name ]['exclude'] ) ) {
												// Set the exclude array key.
												$stored_conditions['taxonomies'][ $taxonomy_name ]['exclude'] = array( array() );
											}
										}
										?>
										<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>">
											<?php
											// Set the notices.
											$notices = array();

											// Set the notice.
											$notice = sprintf(
												'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
												esc_html__( 'PRO Shortcut', 'psupsellmaster' ),
												esc_html__( 'Quickly', 'psupsellmaster' ),
												esc_url( admin_url( "edit-tags.php?post_type={$product_post_type}&taxonomy={$taxonomy_name}" ) ),
												esc_html__( 'manage', 'psupsellmaster' ),
												esc_html( $taxonomy_label ),
												esc_html__( 'at any time', 'psupsellmaster' )
											);

											// Add a notice to the list.
											array_push( $notices, $notice );

											// Set the notice.
											$notice = sprintf(
												'<strong>%s:</strong> <span>%s <a class="psupsellmaster-link" href="%s" target="_blank">%s %s</a> %s.</span>',
												esc_html__( 'PRO Tool', 'psupsellmaster' ),
												esc_html__( 'Easily', 'psupsellmaster' ),
												esc_url( admin_url( 'admin.php?page=psupsellmaster-campaigns&view=tags' ) ),
												esc_html__( 'check which products are associated with specific', 'psupsellmaster' ),
												esc_html( $taxonomy_label ),
												esc_html__( 'at any time', 'psupsellmaster' ),
											);

											// Add a notice to the list.
											array_push( $notices, $notice );
											?>
											<?php if ( ! empty( $notices ) ) : ?>
												<ul class="psupsellmaster-notices">
													<?php foreach ( $notices as $notice ) : ?>
														<li class="psupsellmaster-notice">
															<?php echo wp_kses_post( $notice ); ?>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
											<div class="psupsellmaster-tabs">
												<ul class="psupsellmaster-tabs-header">
													<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
														<li class="psupsellmaster-tab"><a href="#psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>-<?php echo esc_attr( $tab_key ); ?>"><?php echo esc_html( $tab_label ); ?> <span class="psupsellmaster-count"></span></a></li>
													<?php endforeach; ?>
												</ul>
												<?php foreach ( $sections_tabs as $tab_key => $tab_label ) : ?>
													<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>-<?php echo esc_attr( $tab_key ); ?>">
														<div class="psupsellmaster-repeater">
															<div class="psupsellmaster-repeater-rows">
																<?php $count_stored = 0; ?>
																<?php foreach ( $stored_conditions['taxonomies'][ $taxonomy_name ][ $tab_key ] as $stored_data ) : ?>
																	<?php
																	// Get the stored term id.
																	$stored_term_id = isset( $stored_data['term_id'] ) ? $stored_data['term_id'] : '';

																	// Set the options.
																	$options = array();

																	// Check the stored term id.
																	if ( ! empty( $stored_term_id ) ) {
																		// Get the options.
																		$options = psupsellmaster_get_taxonomy_term_label_value_pairs(
																			array(
																				'include'  => array( $stored_term_id ),
																				'taxonomy' => $taxonomy_name,
																			)
																		)['items'];
																	}
																	?>
																	<div class="psupsellmaster-repeater-row" data-index="<?php echo esc_attr( $count_stored ); ?>">
																		<div class="psupsellmaster-repeater-row-content">
																			<div class="psupsellmaster-form-field">
																				<select class="psupsellmaster-select2 psupsellmaster-field" data-ajax-action="psupsellmaster_get_taxonomy_terms" data-ajax-nonce="<?php echo esc_attr( wp_create_nonce( 'psupsellmaster-ajax-get-taxonomy-terms' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy_name ); ?>" id="conditions[taxonomies][<?php echo esc_attr( $taxonomy_name ); ?>][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][term_id]" name="conditions[taxonomies][<?php echo esc_attr( $taxonomy_name ); ?>][<?php echo esc_attr( $tab_key ); ?>][<?php echo esc_attr( $count_stored ); ?>][term_id]">
																					<?php foreach ( $options as $option ) : ?>
																						<option <?php selected( true ); ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
																					<?php endforeach; ?>
																				</select>
																			</div>
																		</div>
																		<div class="psupsellmaster-repeater-row-actions">
																			<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-remove" type="button"><?php esc_html_e( 'Remove', 'psupsellmaster' ); ?></button>
																		</div>
																	</div>
																	<?php ++$count_stored; ?>
																<?php endforeach; ?>
															</div>
															<div class="psupsellmaster-repeater-actions">
																<button class="button psupsellmaster-repeater-btn psupsellmaster-repeater-btn-add" type="button"><?php esc_html_e( 'Add', 'psupsellmaster' ); ?></button>
															</div>
														</div>
													</section>
												<?php endforeach; ?>
											</div>
										</section>
										<?php ++$count_tabs; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</section>
				</section>
			<?php endif; ?>
			<section class="psupsellmaster-section psupsellmaster-section-product-page">
				<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Product Page', 'psupsellmaster' ); ?></h3>
				<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please enter the data that will be displayed on the Product Page for all Selected Products.', 'psupsellmaster' ); ?></p>
				<hr class="psupsellmaster-separator" />
				<?php
				// Get the product page options.
				$product_page = psupsellmaster_db_campaign_meta_select( $campaign_id, 'product_page', true );
				$product_page = is_array( $product_page ) ? $product_page : array();

				// Get the desktop banner id.
				$desktop_banner_id = isset( $product_page['desktop_banner_id'] ) ? $product_page['desktop_banner_id'] : '';
				$desktop_banner_id = filter_var( $desktop_banner_id, FILTER_VALIDATE_INT );
				$desktop_banner_id = false !== $desktop_banner_id ? $desktop_banner_id : 0;

				// Get the desktop banner link url.
				$desktop_banner_link_url = isset( $product_page['desktop_banner_link_url'] ) ? $product_page['desktop_banner_link_url'] : '';

				// Get the banner data.
				$desktop_banner_data = psupsellmaster_get_campaign_banner_data_by_id( $desktop_banner_id );

				// Set the desktop banner url.
				$desktop_banner_url = $desktop_banner_placeholder_url;

				// Check if the desktop banner url is not empty.
				if ( ! empty( $desktop_banner_data['url'] ) ) {
					// Get the desktop banner url.
					$desktop_banner_url = $desktop_banner_data['url'];
				}

				// Set the desktop banner height.
				$desktop_banner_height = $desktop_banner_placeholder_height;

				// Check if the desktop banner height is not empty.
				if ( ! empty( $desktop_banner_data['height'] ) ) {
					// Set the desktop banner height.
					$desktop_banner_height = $desktop_banner_data['height'];
				}

				// Set the desktop banner width.
				$desktop_banner_width = $desktop_banner_placeholder_width;

				// Check if the desktop banner width is not empty.
				if ( ! empty( $desktop_banner_data['width'] ) ) {
					// Set the desktop banner width.
					$desktop_banner_width = $desktop_banner_data['width'];
				}

				// Set the desktop banner title.
				$desktop_banner_title = $banner_placeholder_title;

				// Check if the desktop banner alt is not empty.
				if ( ! empty( $desktop_banner_data['alt'] ) ) {
					// Set the desktop banner title.
					$desktop_banner_title = $desktop_banner_data['alt'];

					// Check if the desktop banner title is not empty.
				} elseif ( ! empty( $desktop_banner_data['title'] ) ) {
					// Set the desktop banner title.
					$desktop_banner_title = $desktop_banner_data['title'];

					// Check if the desktop banner name is not empty.
				} elseif ( ! empty( $desktop_banner_data['name'] ) ) {
					// Set the desktop banner title.
					$desktop_banner_title = $desktop_banner_data['name'];
				}

				// Get the mobile banner id.
				$mobile_banner_id = isset( $product_page['mobile_banner_id'] ) ? $product_page['mobile_banner_id'] : '';
				$mobile_banner_id = filter_var( $mobile_banner_id, FILTER_VALIDATE_INT );
				$mobile_banner_id = false !== $mobile_banner_id ? $mobile_banner_id : 0;

				// Get the mobile banner link url.
				$mobile_banner_link_url = isset( $product_page['mobile_banner_link_url'] ) ? $product_page['mobile_banner_link_url'] : '';

				// Get the banner data.
				$mobile_banner_data = psupsellmaster_get_campaign_banner_data_by_id( $mobile_banner_id );

				// Set the mobile banner url.
				$mobile_banner_url = $mobile_banner_placeholder_url;

				// Check if the mobile banner url is not empty.
				if ( ! empty( $mobile_banner_data['url'] ) ) {
					// Get the mobile banner url.
					$mobile_banner_url = $mobile_banner_data['url'];
				}

				// Set the mobile banner height.
				$mobile_banner_height = $mobile_banner_placeholder_height;

				// Check if the mobile banner height is not empty.
				if ( ! empty( $mobile_banner_data['height'] ) ) {
					// Set the mobile banner height.
					$mobile_banner_height = $mobile_banner_data['height'];
				}

				// Set the mobile banner width.
				$mobile_banner_width = $mobile_banner_placeholder_width;

				// Check if the mobile banner width is not empty.
				if ( ! empty( $mobile_banner_data['width'] ) ) {
					// Set the mobile banner width.
					$mobile_banner_width = $mobile_banner_data['width'];
				}

				// Set the mobile banner title.
				$mobile_banner_title = $banner_placeholder_title;

				// Check if the mobile banner alt is not empty.
				if ( ! empty( $mobile_banner_data['alt'] ) ) {
					// Set the mobile banner title.
					$mobile_banner_title = $mobile_banner_data['alt'];

					// Check if the mobile banner title is not empty.
				} elseif ( ! empty( $mobile_banner_data['title'] ) ) {
					// Set the mobile banner title.
					$mobile_banner_title = $mobile_banner_data['title'];

					// Check if the mobile banner name is not empty.
				} elseif ( ! empty( $mobile_banner_data['name'] ) ) {
					// Set the mobile banner title.
					$mobile_banner_title = $mobile_banner_data['name'];
				}

				// Get the description.
				$description = isset( $product_page['description'] ) ? $product_page['description'] : '';
				?>
				<div class="psupsellmaster-form-container">
					<div class="psupsellmaster-form-row">
						<div class="psupsellmaster-form-field psupsellmaster-form-field-description">
							<label><strong><?php esc_html_e( 'Description', 'psupsellmaster' ); ?></strong></label>
							<?php
							// Set the field id.
							$field_id = 'product-page--description';

							// Set the field name.
							$field_name = 'product_page[description]';

							// Set the field settings.
							$field_settings = array(
								'editor_class'  => 'psupsellmaster-field psupsellmaster-field-description',
								'media_buttons' => false,
								'textarea_name' => $field_name,
								'textarea_rows' => 1,
								'wpautop'       => true,
							);

							// Output the editor.
							wp_editor(
								stripslashes( $description ),
								$field_id,
								$field_settings,
							);
							?>
						</div>
					</div>
					<div class="psupsellmaster-form-row"></div>
					<div class="psupsellmaster-form-row">
						<div class="psupsellmaster-form-field psupsellmaster-form-field-desktop-banner" data-banner-type="desktop">
							<label><strong><?php esc_html_e( 'Desktop Banner', 'psupsellmaster' ); ?></strong></label>
							<input class="psupsellmaster-field psupsellmaster-field-banner-id" name="product_page[desktop_banner_id]" type="hidden" value="<?php echo esc_attr( $desktop_banner_id ); ?>" />
							<input class="psupsellmaster-field psupsellmaster-field-banner-url" name="product_page[desktop_banner_url]" type="hidden" value="<?php echo esc_attr( ( ! empty( $desktop_banner_id ) ? $desktop_banner_url : '' ) ); ?>" />
							<img alt="<?php echo esc_attr( $desktop_banner_title ); ?>" class="psupsellmaster-banner-image" height="<?php echo esc_attr( $desktop_banner_height ); ?>" src="<?php echo esc_url( $desktop_banner_url ); ?>" width="<?php echo esc_attr( $desktop_banner_width ); ?>" />
							<div class="psupsellmaster-banner-actions">
								<button class="button psupsellmaster-btn-select-banner" type="button"><?php esc_html_e( 'Select banner', 'psupsellmaster' ); ?></button>
								<button class="button psupsellmaster-btn-copy-banner-url" type="button"><?php esc_html_e( 'Copy URL', 'psupsellmaster' ); ?></button>
								<button class="button psupsellmaster-btn-danger psupsellmaster-btn-remove-banner" type="button"><?php esc_html_e( 'Remove banner', 'psupsellmaster' ); ?></button>
							</div>
						</div>
					</div>
					<div class="psupsellmaster-form-row">
						<div class="psupsellmaster-form-field psupsellmaster-form-field-desktop-banner-link-url" data-banner-type="desktop">
							<label><strong><?php esc_html_e( 'Desktop Banner Link URL', 'psupsellmaster' ); ?></strong> <small><?php esc_html_e( '(e.g. Landing Page URL)', 'psupsellmaster' ); ?></small></label>
							<input class="psupsellmaster-field psupsellmaster-field-banner-link-url" name="product_page[desktop_banner_link_url]" type="url" value="<?php echo esc_attr( $desktop_banner_link_url ); ?>" />
						</div>
					</div>
					<div class="psupsellmaster-form-row"></div>
					<div class="psupsellmaster-form-row">
						<div class="psupsellmaster-form-field psupsellmaster-form-field-mobile-banner" data-banner-type="mobile">
							<label><strong><?php esc_html_e( 'Mobile Banner', 'psupsellmaster' ); ?></strong></label>
							<input class="psupsellmaster-field psupsellmaster-field-banner-id" name="product_page[mobile_banner_id]" type="hidden" value="<?php echo esc_attr( $mobile_banner_id ); ?>" />
							<input class="psupsellmaster-field psupsellmaster-field-banner-url" name="product_page[mobile_banner_url]" type="hidden" value="<?php echo esc_attr( ( ! empty( $mobile_banner_id ) ? $mobile_banner_url : '' ) ); ?>" />
							<img alt="<?php echo esc_attr( $mobile_banner_title ); ?>" class="psupsellmaster-banner-image" height="<?php echo esc_attr( $mobile_banner_height ); ?>" src="<?php echo esc_url( $mobile_banner_url ); ?>" width="<?php echo esc_attr( $mobile_banner_width ); ?>" />
							<div class="psupsellmaster-banner-actions">
								<button class="button psupsellmaster-btn-select-banner" type="button"><?php esc_html_e( 'Select banner', 'psupsellmaster' ); ?></button>
								<button class="button psupsellmaster-btn-copy-banner-url" type="button"><?php esc_html_e( 'Copy URL', 'psupsellmaster' ); ?></button>
								<button class="button psupsellmaster-btn-danger psupsellmaster-btn-remove-banner" type="button"><?php esc_html_e( 'Remove banner', 'psupsellmaster' ); ?></button>
							</div>
						</div>
					</div>
					<div class="psupsellmaster-form-row">
						<div class="psupsellmaster-form-field psupsellmaster-form-field-mobile-banner-link-url" data-banner-type="mobile">
							<label><strong><?php esc_html_e( 'Mobile Banner Link URL', 'psupsellmaster' ); ?></strong> <small><?php esc_html_e( '(e.g. Landing Page URL)', 'psupsellmaster' ); ?></small></label>
							<input class="psupsellmaster-field psupsellmaster-field-banner-link-url" name="product_page[mobile_banner_link_url]" type="url" value="<?php echo esc_attr( $mobile_banner_link_url ); ?>" />
						</div>
					</div>
				</div>
			</section>
			<section class="psupsellmaster-section psupsellmaster-section-locations">
				<h3 class="psupsellmaster-section-title"><?php esc_html_e( 'Display Options', 'psupsellmaster' ); ?></h3>
				<p class="psupsellmaster-section-subtitle"><?php esc_html_e( 'Please choose the locations to showcase campaign products.', 'psupsellmaster' ); ?></p>
				<hr class="psupsellmaster-separator" />
				<?php $location_tabs = array( 'all' => __( 'All Selected Locations', 'psupsellmaster' ) ) + $locations; ?>
				<div class="psupsellmaster-form-group">
					<div class="psupsellmaster-form-field psupsellmaster-form-field-locations-flag">
						<label><input <?php checked( $stored_locations_flag, 'all' ); ?> class="psupsellmaster-field psupsellmaster-field-locations-flag" name="locations_flag" type="radio" value="all" /><?php esc_html_e( 'All locations', 'psupsellmaster' ); ?></label>
						<label><input <?php checked( $stored_locations_flag, 'selected' ); ?> class="psupsellmaster-field psupsellmaster-field-locations-flag" name="locations_flag" type="radio" value="selected" /><?php esc_html_e( 'Selected locations', 'psupsellmaster' ); ?></label>
					</div>
					<div class="psupsellmaster-form-options" <?php echo wp_kses_post( 'all' === $stored_locations_flag ? 'style="display: none;"' : '' ); ?>>
						<br />
						<div class="psupsellmaster-form-field psupsellmaster-form-field-locations">
							<select class="psupsellmaster-select2 psupsellmaster-field psupsellmaster-field-locations" data-clear="true" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Choose...', 'psupsellmaster' ); ?>" id="locations[]" multiple="multiple" name="locations[]">
								<?php foreach ( $locations as $location_key => $location_label ) : ?>
									<option <?php selected( in_array( $location_key, $stored_locations, true ) ); ?> value="<?php echo esc_attr( $location_key ); ?>"><?php echo esc_html( $location_label ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<p><?php esc_html_e( 'Please use the "All Selected Locations" tab to specify data for all selected locations. Additionaly, specify per-location data using the location tabs in order to overwrite general data.', 'psupsellmaster' ); ?></p>
				<div class="psupsellmaster-tabs psupsellmaster-tabs-vertical" data-key="locations" style="display: none;">
					<ul class="psupsellmaster-tabs-header">
						<?php $count_tabs = 1; ?>
						<?php foreach ( $location_tabs as $location_key => $location_label ) : ?>
							<li class="psupsellmaster-tab" data-location="<?php echo esc_attr( $location_key ); ?>"><a href="#psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>"><?php echo esc_html( $location_label ); ?> <span class="psupsellmaster-count"></span></a></li>
							<?php ++$count_tabs; ?>
						<?php endforeach; ?>
					</ul>
					<?php $count_tabs = 1; ?>
					<?php foreach ( $location_tabs as $location_key => $location_label ) : ?>
						<?php
						// Get the desktop banner id.
						$desktop_banner_id = psupsellmaster_get_campaign_display_option( $campaign_id, $location_key, 'desktop_banner_id' );
						$desktop_banner_id = filter_var( $desktop_banner_id, FILTER_VALIDATE_INT );
						$desktop_banner_id = false !== $desktop_banner_id ? $desktop_banner_id : 0;

						// Get the desktop banner link url.
						$desktop_banner_link_url = psupsellmaster_get_campaign_display_option( $campaign_id, $location_key, 'desktop_banner_link_url' );

						// Get the banner data.
						$desktop_banner_data = psupsellmaster_get_campaign_banner_data_by_id( $desktop_banner_id );

						// Set the desktop banner url.
						$desktop_banner_url = $desktop_banner_placeholder_url;

						// Check if the desktop banner url is not empty.
						if ( ! empty( $desktop_banner_data['url'] ) ) {
							// Get the desktop banner url.
							$desktop_banner_url = $desktop_banner_data['url'];
						}

						// Set the desktop banner height.
						$desktop_banner_height = $desktop_banner_placeholder_height;

						// Check if the desktop banner height is not empty.
						if ( ! empty( $desktop_banner_data['height'] ) ) {
							// Set the desktop banner height.
							$desktop_banner_height = $desktop_banner_data['height'];
						}

						// Set the desktop banner width.
						$desktop_banner_width = $desktop_banner_placeholder_width;

						// Check if the desktop banner width is not empty.
						if ( ! empty( $desktop_banner_data['width'] ) ) {
							// Set the desktop banner width.
							$desktop_banner_width = $desktop_banner_data['width'];
						}

						// Set the desktop banner title.
						$desktop_banner_title = $banner_placeholder_title;

						// Check if the desktop banner alt is not empty.
						if ( ! empty( $desktop_banner_data['alt'] ) ) {
							// Set the desktop banner title.
							$desktop_banner_title = $desktop_banner_data['alt'];

							// Check if the desktop banner title is not empty.
						} elseif ( ! empty( $desktop_banner_data['title'] ) ) {
							// Set the desktop banner title.
							$desktop_banner_title = $desktop_banner_data['title'];

							// Check if the desktop banner name is not empty.
						} elseif ( ! empty( $desktop_banner_data['name'] ) ) {
							// Set the desktop banner title.
							$desktop_banner_title = $desktop_banner_data['name'];
						}

						// Get the mobile banner id.
						$mobile_banner_id = psupsellmaster_get_campaign_display_option( $campaign_id, $location_key, 'mobile_banner_id' );
						$mobile_banner_id = filter_var( $mobile_banner_id, FILTER_VALIDATE_INT );
						$mobile_banner_id = false !== $mobile_banner_id ? $mobile_banner_id : 0;

						// Get the mobile banner link url.
						$mobile_banner_link_url = psupsellmaster_get_campaign_display_option( $campaign_id, $location_key, 'mobile_banner_link_url' );

						// Get the banner data.
						$mobile_banner_data = psupsellmaster_get_campaign_banner_data_by_id( $mobile_banner_id );

						// Set the mobile banner url.
						$mobile_banner_url = $mobile_banner_placeholder_url;

						// Check if the mobile banner url is not empty.
						if ( ! empty( $mobile_banner_data['url'] ) ) {
							// Get the mobile banner url.
							$mobile_banner_url = $mobile_banner_data['url'];
						}

						// Set the mobile banner height.
						$mobile_banner_height = $mobile_banner_placeholder_height;

						// Check if the mobile banner height is not empty.
						if ( ! empty( $mobile_banner_data['height'] ) ) {
							// Set the mobile banner height.
							$mobile_banner_height = $mobile_banner_data['height'];
						}

						// Set the mobile banner width.
						$mobile_banner_width = $mobile_banner_placeholder_width;

						// Check if the mobile banner width is not empty.
						if ( ! empty( $mobile_banner_data['width'] ) ) {
							// Set the mobile banner width.
							$mobile_banner_width = $mobile_banner_data['width'];
						}

						// Set the mobile banner title.
						$mobile_banner_title = $banner_placeholder_title;

						// Check if the mobile banner alt is not empty.
						if ( ! empty( $mobile_banner_data['alt'] ) ) {
							// Set the mobile banner title.
							$mobile_banner_title = $mobile_banner_data['alt'];

							// Check if the mobile banner title is not empty.
						} elseif ( ! empty( $mobile_banner_data['title'] ) ) {
							// Set the mobile banner title.
							$mobile_banner_title = $mobile_banner_data['title'];

							// Check if the mobile banner name is not empty.
						} elseif ( ! empty( $mobile_banner_data['name'] ) ) {
							// Set the mobile banner title.
							$mobile_banner_title = $mobile_banner_data['name'];
						}

						// Get the description.
						$description = psupsellmaster_get_campaign_display_option( $campaign_id, $location_key, 'description' );
						$description = is_string( $description ) ? $description : '';
						?>
						<section class="psupsellmaster-tab-section" id="psupsellmaster-fieldset-tab-<?php echo esc_attr( $count_tabs ); ?>">
							<div class="psupsellmaster-form-container">
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-description">
										<label><strong><?php esc_html_e( 'Description', 'psupsellmaster' ); ?></strong></label>
										<?php
										// Set the field id.
										$field_id = 'display-options-' . esc_attr( $location_key ) . '-description';

										// Set the field name.
										$field_name = 'display_options[' . esc_attr( $location_key ) . '][description]';

										// Set the field settings.
										$field_settings = array(
											'editor_class' => 'psupsellmaster-field psupsellmaster-field-description',
											'media_buttons' => false,
											'textarea_name' => $field_name,
											'textarea_rows' => 1,
											'wpautop'      => true,
										);

										// Output the editor.
										wp_editor(
											stripslashes( $description ),
											$field_id,
											$field_settings,
										);
										?>
									</div>
								</div>
								<div class="psupsellmaster-form-row"></div>
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-desktop-banner" data-banner-type="desktop">
										<label><strong><?php esc_html_e( 'Desktop Banner', 'psupsellmaster' ); ?></strong></label>
										<input class="psupsellmaster-field psupsellmaster-field-banner-id" name="display_options[<?php echo esc_attr( $location_key ); ?>][desktop_banner_id]" type="hidden" value="<?php echo esc_attr( $desktop_banner_id ); ?>" />
										<input class="psupsellmaster-field psupsellmaster-field-banner-url" name="display_options[<?php echo esc_attr( $location_key ); ?>][desktop_banner_url]" type="hidden" value="<?php echo esc_attr( ( ! empty( $desktop_banner_id ) ? $desktop_banner_url : '' ) ); ?>" />
										<img alt="<?php echo esc_attr( $desktop_banner_title ); ?>" class="psupsellmaster-banner-image" height="<?php echo esc_attr( $desktop_banner_height ); ?>" src="<?php echo esc_url( $desktop_banner_url ); ?>" width="<?php echo esc_attr( $desktop_banner_width ); ?>" />
										<div class="psupsellmaster-banner-actions">
											<button class="button psupsellmaster-btn-select-banner" type="button"><?php esc_html_e( 'Select banner', 'psupsellmaster' ); ?></button>
											<button class="button psupsellmaster-btn-copy-banner-url" type="button"><?php esc_html_e( 'Copy URL', 'psupsellmaster' ); ?></button>
											<button class="button psupsellmaster-btn-danger psupsellmaster-btn-remove-banner" type="button"><?php esc_html_e( 'Remove banner', 'psupsellmaster' ); ?></button>
										</div>
									</div>
								</div>
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-desktop-banner-link-url" data-banner-type="desktop">
										<label><strong><?php esc_html_e( 'Desktop Banner Link URL', 'psupsellmaster' ); ?></strong> <small><?php esc_html_e( '(e.g. Landing Page URL)', 'psupsellmaster' ); ?></small></label>
										<input class="psupsellmaster-field psupsellmaster-field-banner-link-url" name="display_options[<?php echo esc_attr( $location_key ); ?>][desktop_banner_link_url]" type="url" value="<?php echo esc_attr( $desktop_banner_link_url ); ?>" />
									</div>
								</div>
								<div class="psupsellmaster-form-row"></div>
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-mobile-banner" data-banner-type="mobile">
										<label><strong><?php esc_html_e( 'Mobile Banner', 'psupsellmaster' ); ?></strong></label>
										<input class="psupsellmaster-field psupsellmaster-field-banner-id" name="display_options[<?php echo esc_attr( $location_key ); ?>][mobile_banner_id]" type="hidden" value="<?php echo esc_attr( $mobile_banner_id ); ?>" />
										<input class="psupsellmaster-field psupsellmaster-field-banner-url" name="display_options[<?php echo esc_attr( $location_key ); ?>][mobile_banner_url]" type="hidden" value="<?php echo esc_attr( ( ! empty( $mobile_banner_id ) ? $mobile_banner_url : '' ) ); ?>" />
										<img alt="<?php echo esc_attr( $mobile_banner_title ); ?>" class="psupsellmaster-banner-image" height="<?php echo esc_attr( $mobile_banner_height ); ?>" src="<?php echo esc_url( $mobile_banner_url ); ?>" width="<?php echo esc_attr( $mobile_banner_width ); ?>" />
										<div class="psupsellmaster-banner-actions">
											<button class="button psupsellmaster-btn-select-banner" type="button"><?php esc_html_e( 'Select banner', 'psupsellmaster' ); ?></button>
											<button class="button psupsellmaster-btn-copy-banner-url" type="button"><?php esc_html_e( 'Copy URL', 'psupsellmaster' ); ?></button>
											<button class="button psupsellmaster-btn-danger psupsellmaster-btn-remove-banner" type="button"><?php esc_html_e( 'Remove banner', 'psupsellmaster' ); ?></button>
										</div>
									</div>
								</div>
								<div class="psupsellmaster-form-row">
									<div class="psupsellmaster-form-field psupsellmaster-form-field-mobile-banner-link-url" data-banner-type="mobile">
										<label><strong><?php esc_html_e( 'Mobile Banner Link URL', 'psupsellmaster' ); ?></strong> <small><?php esc_html_e( '(e.g. Landing Page URL)', 'psupsellmaster' ); ?></small></label>
										<input class="psupsellmaster-field psupsellmaster-field-banner-link-url" name="display_options[<?php echo esc_attr( $location_key ); ?>][mobile_banner_link_url]" type="url" value="<?php echo esc_attr( $mobile_banner_link_url ); ?>" />
									</div>
								</div>
							</div>
						</section>
						<?php ++$count_tabs; ?>
					<?php endforeach; ?>
				</div>
			</section>
			<section class="psupsellmaster-section psupsellmaster-section-actions">
				<?php if ( ! empty( $campaign_id ) ) : ?>
					<div class="psupsellmaster-action psupsellmaster-action-delete-campaign">
						<button class="button psupsellmaster-btn-danger psupsellmaster-btn-delete-campaign" data-campaign-id="<?php echo esc_attr( $campaign_id ); ?>" disabled="disabled" type="button"><?php esc_html_e( 'Delete Campaign', 'psupsellmaster' ); ?></button>
						<label>
							<input class="psupsellmaster-field psupsellmaster-field-confirm-delete-campaign" type="checkbox" />
							<span><?php esc_html_e( 'Enable irreversible action. Are you sure?', 'psupsellmaster' ); ?></span>
						</label>
					</div>
				<?php endif; ?>
				<div class="psupsellmaster-action psupsellmaster-action-save-campaign">
					<input name="action" type="hidden" value="save_campaign" />
					<input class="psupsellmaster-field" id="psupsellmaster-field-campaign-id" name="campaign_id" type="hidden" value="<?php echo esc_attr( $campaign_id ); ?>" />
					<input class="psupsellmaster-field" id="psupsellmaster-field-coupon-id" name="coupon_id" type="hidden" value="<?php echo esc_attr( $stored_coupon_id ); ?>" />
					<input class="button button-primary psupsellmaster-btn-save-campaign" type="submit" value="<?php esc_attr_e( 'Save', 'psupsellmaster' ); ?>" />
					<div class="psupsellmaster-defaults" style="display: none;">
						<input class="psupsellmaster-default-desktop-banner-url" type="hidden" value="<?php echo esc_url( $desktop_banner_placeholder_url ); ?>" />
						<input class="psupsellmaster-default-desktop-banner-height" type="hidden" value="<?php echo esc_attr( $desktop_banner_placeholder_height ); ?>" />
						<input class="psupsellmaster-default-desktop-banner-width" type="hidden" value="<?php echo esc_attr( $desktop_banner_placeholder_width ); ?>" />
						<input class="psupsellmaster-default-mobile-banner-url" type="hidden" value="<?php echo esc_url( $mobile_banner_placeholder_url ); ?>" />
						<input class="psupsellmaster-default-mobile-banner-height" type="hidden" value="<?php echo esc_attr( $mobile_banner_placeholder_height ); ?>" />
						<input class="psupsellmaster-default-mobile-banner-width" type="hidden" value="<?php echo esc_attr( $mobile_banner_placeholder_width ); ?>" />
						<input class="psupsellmaster-default-banner-title" type="hidden" value="<?php echo esc_attr( $banner_placeholder_title ); ?>" />
					</div>
				</div>
			</section>
		</div>
	</form>
</div>

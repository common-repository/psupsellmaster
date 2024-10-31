<?php
/**
 * Admin - Templates - Wizard - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the current timezone.
$current_timezone = new DateTime( 'now', psupsellmaster_get_timezone() );
$current_timezone = $current_timezone->format( 'T' );

// Get the campaign statuses.
$campaign_statuses = psupsellmaster_campaigns_get_statuses();

// Get the campaign id.
$campaign_id = psupsellmaster_campaigns_get_id_from_setup_wizard();

// Get the stored campaign.
$stored_campaign = psupsellmaster_get_campaign( $campaign_id );

// Get the stored title.
$stored_title = isset( $stored_campaign['title'] ) ? $stored_campaign['title'] : __( 'Campaign from Setup Wizard', 'psupsellmaster' );

// Get the stored status.
$stored_status = isset( $stored_campaign['status'] ) ? $stored_campaign['status'] : '';

// Set the stored status.
$stored_status = ! empty( $stored_status ) ? $stored_status : 'inactive';

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

// Get the stored coupon.
$stored_coupon = psupsellmaster_db_campaign_coupons_get_row_by( 'campaign_id', $campaign_id );

// Get the stored coupon code.
$stored_coupon_code = isset( $stored_coupon->code ) ? $stored_coupon->code : strtoupper( 'promo' );

// Get the stored coupon type.
$stored_coupon_type = isset( $stored_coupon->type ) ? $stored_coupon->type : '';

// Get the stored coupon amount.
$stored_coupon_amount = isset( $stored_coupon->amount ) ? $stored_coupon->amount : 10;
$stored_coupon_amount = psupsellmaster_round_amount( $stored_coupon_amount );

?>
<form class="psupsellmaster-wizard-form" method="post">
	<div class="psupsellmaster-step-body">
		<div class="psupsellmaster-step-something1">
			<input name="redirect" type="hidden" value="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=summary' ) ); ?>" />
			<input name="step" type="hidden" value="campaigns" />
			<h2 class="psupsellmaster-step-title"><?php esc_html_e( 'Campaigns', 'psupsellmaster' ); ?></h2>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Now its time to run a first campaign by setting up a special promotion offer for all Customers.', 'psupsellmaster' ); ?></p>
			<p class="psupsellmaster-paragraph"><?php esc_html_e( 'Please set the general data for the first campaign. The campaign data may be changed at any time later on.', 'psupsellmaster' ); ?></p>
		</div>
		<div class="psupsellmaster-step-something2">
			<div class="psupsellmaster-form-fields">
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label"><strong><?php esc_html_e( 'Title', 'psupsellmaster' ); ?></strong></label>
					<input class="psupsellmaster-field" name="title" required="required" type="text" value="<?php echo esc_attr( $stored_title ); ?>" />
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label"><strong><?php esc_html_e( 'Status', 'psupsellmaster' ); ?></strong></label>
					<select class="psupsellmaster-field" name="status">
						<?php foreach ( $campaign_statuses as $status_key => $status_label ) : ?>
							<?php
							if ( in_array( $status_key, array( 'expired', 'scheduled' ), true ) ) {
								continue;
							}
							?>
							<option <?php selected( $stored_status, $status_key ); ?> value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label">
						<strong><?php esc_html_e( 'Start Date', 'psupsellmaster' ); ?></strong>
						<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php printf( '%s %s', esc_attr__( 'Please enter the date in your WordPress Timezone.', 'psupsellmaster' ), esc_attr( $current_timezone ) ); ?>"></span>
					</label>
					<input autocomplete="off" class="psupsellmaster-field psupsellmaster-field-pikaday" name="start_date" type="text" value="<?php echo esc_attr( $stored_start_date ); ?>" />
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label">
						<strong><?php esc_html_e( 'End Date', 'psupsellmaster' ); ?></strong>
						<span alt="f223" class="psupsellmaster-help-tip dashicons dashicons-editor-help" title="<?php printf( '%s %s', esc_attr__( 'Please enter the date in your WordPress Timezone.', 'psupsellmaster' ), esc_attr( $current_timezone ) ); ?>"></span>
					</label>
					<input autocomplete="off" class="psupsellmaster-field psupsellmaster-field-pikaday" name="end_date" type="text" value="<?php echo esc_attr( $stored_end_date ); ?>" />
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label"><strong><?php esc_html_e( 'Coupon Code', 'psupsellmaster' ); ?></strong></label>
					<input class="psupsellmaster-field" name="coupon_code" required="required" type="text" value="<?php echo esc_attr( $stored_coupon_code ); ?>" />
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label"><strong><?php esc_html_e( 'Coupon Type', 'psupsellmaster' ); ?></strong></label>
					<select class="psupsellmaster-field" name="coupon_type">
						<option <?php selected( $stored_coupon_type, 'discount_percentage' ); ?> value="discount_percentage"><?php esc_html_e( 'Discount - Percentage', 'psupsellmaster' ); ?></option>
						<option <?php selected( $stored_coupon_type, 'discount_fixed' ); ?> value="discount_fixed"><?php esc_html_e( 'Discount - Fixed', 'psupsellmaster' ); ?></option>
					</select>
				</div>
				<div class="psupsellmaster-form-field">
					<label class="psupsellmaster-form-label"><strong><?php esc_html_e( 'Coupon Amount', 'psupsellmaster' ); ?></strong></label>
					<input class="psupsellmaster-field" name="coupon_amount" required="required" step="any" type="number" value="<?php echo esc_attr( $stored_coupon_amount ); ?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="psupsellmaster-step-footer">
		<a class="button psupsellmaster-button-link psupsellmaster-button-previous" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=locations' ) ); ?>"><span>&#8592; <?php esc_html_e( 'Previous Step', 'psupsellmaster' ); ?></span></a>
		<a class="button psupsellmaster-button-link psupsellmaster-button-skip" href="<?php echo esc_url( admin_url( 'admin.php?page=psupsellmaster-wizard&step=summary' ) ); ?>"><span><?php esc_html_e( 'Skip Step', 'psupsellmaster' ); ?></span></a>
		<button class="button button-primary psupsellmaster-button psupsellmaster-button-save" type="submit"><span><?php esc_html_e( 'Save & Continue', 'psupsellmaster' ); ?></span></button>
	</div>
	<div class="psupsellmaster-backdrop-spinner" style="display: none;">
		<div class="spinner is-active"></div>
	</div>
</form>

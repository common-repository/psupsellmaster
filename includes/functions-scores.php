<?php
/**
 * Functions - Scores.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the count of how many base products
 * are stored in the scores database table.
 *
 * @return int The count.
 */
function psupsellmaster_scores_get_base_product_count() {
	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( DISTINCT `scores`.`base_product_id` )
		FROM
			%i AS `scores`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' )
	);

	// Get the count.
	$count = PsUpsellMaster_Database::get_var( $sql_query );
	$count = filter_var( $count, FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * Update the scores on product insert.
 *
 * @param int     $post_id The post id.
 * @param WP_Post $post    The post object.
 */
function psupsellmaster_scores_update_on_product_insert( $post_id, $post ) {
	// Check the post.
	if ( empty( $post_id ) || empty( $post ) ) {
		return;
	}

	// Check autosaves.
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_autosave( $post ) ) ) {
		return;
	}

	// Check revisions.
	if ( is_int( wp_is_post_revision( $post ) ) ) {
		return;
	}

	// Check permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Get the post status.
	$post_status = get_post_status( $post_id );

	// Check the post status.
	if ( 'publish' !== $post_status ) {
		return;
	}

	// Get the post meta.
	$meta = get_post_meta( $post_id, 'psupsellmaster_has_published', true );

	// Check the meta.
	if ( ! empty( $meta ) ) {
		return;
	}

	// Update the post meta.
	update_post_meta( $post_id, 'psupsellmaster_has_published', true );

	// Get the auto calculate setting.
	$setting_auto_calculate = PsUpsellMaster_Settings::get( 'auto_calculate_for_new_product' );
	$setting_auto_calculate = filter_var( $setting_auto_calculate, FILTER_VALIDATE_BOOLEAN );

	// Check the setting.
	if ( $setting_auto_calculate && 'publish' === $post_status ) {
		// Enqueue the background processes for the product.
		psupsellmaster_enqueue_scores_by_products( array( 'products' => array( $post_id ) ) );
	}

	// Set the transient.
	set_transient( "psupsellmaster_product_updated_{$post_id}", true, MINUTE_IN_SECONDS );

	// Remove the actions (avoid multiple calls).
	remove_action( 'save_post_product', 'psupsellmaster_scores_update_on_product_insert', 20 );
	remove_action( 'save_post_download', 'psupsellmaster_scores_update_on_product_insert', 20 );
}
add_action( 'save_post_product', 'psupsellmaster_scores_update_on_product_insert', 20, 2 );
add_action( 'save_post_download', 'psupsellmaster_scores_update_on_product_insert', 20, 2 );

/**
 * Update the scores on product update.
 *
 * @param int     $post_id The post id.
 * @param WP_Post $post    The post object.
 */
function psupsellmaster_scores_update_on_product_update( $post_id, $post ) {
	// Check the post.
	if ( empty( $post_id ) || empty( $post ) ) {
		return;
	}

	// Check autosaves.
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_autosave( $post ) ) ) {
		return;
	}

	// Check revisions.
	if ( is_int( wp_is_post_revision( $post ) ) ) {
		return;
	}

	// Check permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Get the post status.
	$post_status = get_post_status( $post_id );

	// Check the post status.
	if ( 'publish' !== $post_status ) {
		return;
	}

	// Get the post meta.
	$meta = get_post_meta( $post_id, 'psupsellmaster_has_published', true );

	// Check the meta.
	if ( empty( $meta ) ) {
		return;
	}

	/**
	 * Note: This hook may run multiple times within WordPress.
	 * To prevent redundant execution of our procedures, we use
	 * a transient that will block subsequent runs for 60 seconds.
	 */

	// Get the transient.
	$transient = get_transient( "psupsellmaster_product_updated_{$post_id}" );
	$transient = filter_var( $transient, FILTER_VALIDATE_BOOLEAN );

	// Check the transient.
	if ( ! empty( $transient ) ) {
		return;
	}

	// Get the auto calculate setting.
	$setting_auto_calculate = PsUpsellMaster_Settings::get( 'auto_calculate_on_product_update' );
	$setting_auto_calculate = filter_var( $setting_auto_calculate, FILTER_VALIDATE_BOOLEAN );

	// Check the setting.
	if ( $setting_auto_calculate ) {
		// Enqueue the background processes for the product.
		psupsellmaster_enqueue_scores_by_products( array( 'products' => array( $post_id ) ) );
	}

	// Set the transient.
	set_transient( "psupsellmaster_product_updated_{$post_id}", true, MINUTE_IN_SECONDS );

	// Remove the actions (avoid multiple calls).
	remove_action( 'save_post_product', 'psupsellmaster_scores_update_on_product_update', 20 );
	remove_action( 'save_post_download', 'psupsellmaster_scores_update_on_product_update', 20 );
}
add_action( 'save_post_product', 'psupsellmaster_scores_update_on_product_update', 20, 2 );
add_action( 'save_post_download', 'psupsellmaster_scores_update_on_product_update', 20, 2 );

/**
 * Delete score records from the database.
 *
 * @param int     $post_id The post id.
 * @param WP_Post $post    The post object.
 */
function psupsellmaster_scores_delete_on_product_trash( $post_id, $post ) {
	// Check whether the post or the post id is empty.
	if ( empty( $post ) || empty( $post_id ) ) {
		return;
	}

	// Check if the post status is not trash.
	if ( empty( $post->post_status ) || 'trash' !== $post->post_status ) {
		return;
	}

	// Delete the score records related to the base product id.
	psupsellmaster_db_scores_delete( array( 'base_product_id' => $post_id ) );

	// Remove the actions (avoid multiple calls).
	remove_action( 'save_post_product', 'psupsellmaster_scores_delete_on_product_trash', 20 );
	remove_action( 'save_post_download', 'psupsellmaster_scores_delete_on_product_trash', 20 );
}
add_action( 'save_post_product', 'psupsellmaster_scores_delete_on_product_trash', 20, 2 );
add_action( 'save_post_download', 'psupsellmaster_scores_delete_on_product_trash', 20, 2 );

/**
 * Delete score records from the database.
 *
 * @param int     $post_id The post id.
 * @param WP_Post $post    The post object.
 */
function psupsellmaster_scores_delete_on_product_delete( $post_id, $post ) {
	// Check whether the post or the post id is empty.
	if ( empty( $post ) || empty( $post_id ) ) {
		return;
	}

	// Check if the post type is not a valid product post type.
	if ( ! psupsellmaster_is_valid_product_post_type_by_post_id( $post_id ) ) {
		return;
	}

	// Delete the score records related to the base product id.
	psupsellmaster_db_scores_delete( array( 'base_product_id' => $post_id ) );

	// Remove the action (avoid multiple calls).
	remove_action( 'delete_post', 'psupsellmaster_scores_delete_on_product_delete', 20 );
}
add_action( 'delete_post', 'psupsellmaster_scores_delete_on_product_delete', 20, 2 );

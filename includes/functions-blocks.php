<?php
/**
 * Blocks - Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the blocks.
 *
 * @return array The blocks.
 */
function psupsellmaster_get_blocks() {
	// Set the blocks.
	$blocks = array();

	// Allow developers to filter this.
	$blocks = apply_filters( 'psupsellmaster_blocks', $blocks );

	// Return the blocks.
	return $blocks;
}

/**
 * Register the blocks.
 */
function psupsellmaster_register_blocks() {
	// Get the blocks.
	$blocks = psupsellmaster_get_blocks();

	// Loop through the blocks.
	foreach ( $blocks as $block ) {
		// Register the block.
		register_block_type( $block['path'], $block['args'] );
	}
}
// add_action( 'init', 'psupsellmaster_register_blocks' );

/**
 * Register block categories.
 *
 * @param array $block_categories The block categories.
 * @return array The block categories.
 */
function psupsellmaster_register_block_categories( $block_categories ) {
	// Set the categories.
	$categories = array(
		array(
			'slug'  => 'psupsellmaster',
			'title' => __( 'UpsellMaster', 'psupsellmaster' ),
			'icon'  => 'money-alt',
		)
	);

	// Allow developers to filter this.
	$categories = apply_filters( 'psupsellmaster_block_categories', $categories );

	// Merge the block categories.
	$block_categories = array_merge( $block_categories, $categories );

	// Return the block categories.
	return $block_categories;
}
add_filter( 'block_categories_all', 'psupsellmaster_register_block_categories' );

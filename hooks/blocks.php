<?php
/**
 * Gutenberg blocks
 *
 * @package capitalp
 */

/**
 * Add block scripts.
 */
add_action( 'enqueue_block_editor_assets', function() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'capitalp-interview', get_stylesheet_directory_uri() . '/assets/css/blocks/interview.css', [], $version );
	wp_enqueue_script( 'capitalp-interview', get_stylesheet_directory_uri() . '/assets/js/capitalp-interview-block.js', [ 'wp-editor' ], $version, true );
} );

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
	$users = new WP_User_Query( [
		'number'      => -1,
		'count_total' => false,
		'fields'      => [ 'ID', 'display_name' ],
	] );
	wp_localize_script( 'capitalp-interview', 'CapitalpInterview', [
		'users' => $users->get_results(),
	] );
} );

if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'capitalp/interview', [
		'render_callback' => function ( $attributes, $content = '' ) {
			return sprintf( '[capitalp_author user_id=%d]%s[/capitalp_author]', $attributes[ 'user' ], $content );
		},
	] );
}

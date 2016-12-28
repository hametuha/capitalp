<?php

/**
 * Change User contact method
 */
add_filter( 'user_contactmethods', function( $methods ) {
	return [
		'facebook' => 'Facebook(URL)',
		'twitter' => 'Twitter(URL)',
		'instagram' => 'Instagram(URL)',
		'github' =>  'Github(URL)',
	];
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_authors', function( $attributes = [], $content = '' ) {
	ob_start();
	get_template_part( 'template-parts/block/list', 'author' );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
} );

/**
 * Register short code UI
 */
add_action( 'register_shortcode_ui', function() {
	shortcode_ui_register_for_shortcode( 'capitalp_authors', [
		'label'     => 'Author List',
		'post_type' => [ 'page' ],
		'listItemImage' => 'dashicons-groups',
	] );
} );
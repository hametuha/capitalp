<?php
/**
 * Override WordPress default and Twenty Seventeen default
 *
 * @package capitalp
 */

// Remove excerpt link.
add_action( 'after_setup_theme', function(){
	remove_filter( 'excerpt_more', 'twentyseventeen_excerpt_more' );
} );

// Customize publicize.
add_filter( 'wpas_default_suffix', function( $suffix ){
	$suffix .= ' #capitalp ';
	return $suffix;
}, 10, 4 );

// Add mautic bar
add_action( 'admin_bar_menu', function( WP_Admin_Bar &$wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'parent' => 'site-name',
		'id'     => 'mautic',
		'title'  => 'Mautic',
		'href'   => 'https://capitalp.mautic.net/',
	) );
}, 11 );

add_action( 'template_redirect', function() {
	if ( 'twitter-card' == get_query_var( 'hametupack-template' ) ) {
		header( 'X-FRAME-OPTIONS: ALLOW-FROM https://twitter.com' );
	}
} );

// Add shortcake for WP Poll

/**
 * Register short code UI
 */
add_action( 'register_shortcode_ui', function () {
	// Author list.
	shortcode_ui_register_for_shortcode( 'poll', [
		'label'         => '投票ID',
		'post_type'     => [ 'post', 'page' ],
		'listItemImage' => '',
		'attrs'         => [
			[
				'label'    => '投票ID',
				'attr'     => 'id',
				'type'     => 'number',
			],
		],
	] );
} );

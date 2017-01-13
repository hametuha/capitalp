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

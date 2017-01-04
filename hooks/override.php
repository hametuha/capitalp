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

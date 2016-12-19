<?php
/**
 * Asset routine
 */


/**
 * Register scripts
 */
add_action( 'init', function() {
	// Register main theme style
	wp_register_style( 'twentyseventeen-style', get_template_directory_uri().'/style.css', [], wp_get_theme('twentyseventeen')->get('Version') );
	// Register this style
	wp_register_style( 'capitalp', get_stylesheet_directory_uri().'/assets/css/style.css', ['twentyseventeen-style'], wp_get_theme()->get( 'Version' ) );
} );


add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'capitalp' );
} );
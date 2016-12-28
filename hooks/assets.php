<?php
/**
 * Asset routine
 */


/**
 * Register scripts
 */
add_action( 'init', function() {

	$version = wp_get_theme()->get( 'Version' );

	// Register main theme style
	wp_register_style( 'twentyseventeen-style', get_template_directory_uri().'/style.css', [], wp_get_theme('twentyseventeen')->get('Version') );
	// Register this style
	wp_register_style( 'capitalp', get_stylesheet_directory_uri().'/assets/css/style.css', ['twentyseventeen-style'], $version );

	// Register JS
	wp_register_script( 'capitalp-tracker', get_stylesheet_directory_uri() .'/assets/js/tracker.js', ['jquery'], $version, true );
} );

/**
 * Register global assets
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'capitalp' );
} );

/**
 * Executed on playlist
 */
add_filter( 'ssp_media_player', function( $player, $src, $episode_id ) {
	wp_enqueue_script( 'capitalp-tracker' );
	$player .= sprintf(
		'<span class="capitalp-media-tracker" style="display: none;" data-src="%s" data-episode-id="%s"></span>',
		esc_attr( $src ),
		esc_attr( $episode_id )
	);
	return $player;
}, 10, 3 );
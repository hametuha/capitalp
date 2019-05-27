<?php
/**
 * Asset routine
 */



/**
 * Register scripts
 */
add_action( 'init', function () {

	$version = wp_get_theme()->get( 'Version' );

	// Register this style
	wp_register_style( 'capitalp', get_stylesheet_directory_uri() . '/assets/css/style.css', [ 'snow-monkey' ], $version );

	// Register JS
	wp_register_script( 'capitalp-tracker', get_stylesheet_directory_uri() . '/assets/js/tracker.js', [ 'jquery' ], $version, true );
	wp_register_script( 'capitalp-marketing', get_stylesheet_directory_uri() . '/assets/js/capital-marketing.js', [ 'jquery' ], $version, true );
	wp_register_script( 'capitalp-login', get_stylesheet_directory_uri() . '/assets/js/capitalp-login-link.js', [ 'wp-element', 'wp-api-fetch', 'wp-i18n', 'cookie-tasting-heartbeat' ], $version, true );
	wp_register_script( 'capitalp-contents', get_stylesheet_directory_uri() . '/assets/js/capitalp-contents.js', [ 'jquery-effects-highlight', 'capitalp-login' ], $version, true );
} );

// Editor Style
add_editor_style( 'assets/css/editor-style-capitalp.css' );

/**
 * Register global assets
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'capitalp' );
	wp_enqueue_script( 'capitalp-marketing' );
	wp_enqueue_script( 'capitalp-login' );
	wp_set_script_translations( 'capitalp-login', 'capitalp', get_stylesheet_directory() . '/languages' );
	if ( is_singular() ) {
		wp_enqueue_script( 'capitalp-contents' );
		wp_localize_script( 'capitalp-contents', 'CapitalpContents', [
			'postId' => get_queried_object_id(),
		] );
	}
} );

/**
 * Executed on playlist
 */
add_filter( 'ssp_media_player', function( $player, $src, $episode_id ) {
	wp_enqueue_script( 'capitalp-tracker' );
	$post = get_post( $episode_id );
	$post_author = $post->post_author;
	$tags = '';
	$terms = get_the_tags( $post->ID );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$tags = implode( ',', array_map( function( $term ) {
			return $term->term_id;
		}, $terms ) );
	}
	$player .= sprintf(
		'<span class="capitalp-media-tracker" style="display: none;" data-src="%s" data-episode-id="%d" data-episode-author="%d" data-episode-tags="%s"></span>',
		esc_attr( $src ),
		esc_attr( $episode_id ),
		$post_author,
		esc_attr( $tags )
	);
	return $player;
}, 10, 3 );

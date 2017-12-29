<?php
/**
 * Override WordPress default and Twenty Seventeen default
 *
 * @package capitalp
 */

// Customize publicize.
add_filter( 'wpas_default_suffix', function( $suffix ){
	$suffix .= ' #capitalp ';
	return $suffix;
}, 10, 4 );

// Allow external embed.
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

/**
 * Avoid oEmbed to work if wp_mail context.
 *
 * @param string $context
 */
add_action( 'hamail_body_before_send', function( $context ) {
	if ( 'html' === $context ) {
		wp_embed_unregister_handler( 'wp_oembed_blog_card' );
	}
} );

/**
 * Password reset mail
 *
 * @param string $message
 */
add_filter( 'retrieve_password_message', function( $message ) {
	return preg_replace( '#<(https?://.*)>#u', '$1', $message );
} );

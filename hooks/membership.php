<?php
/**
 * Membership related hooks
 */

// Load header css
add_action( 'login_enqueue_scripts', function() {
	wp_enqueue_style( 'login-header', get_stylesheet_directory_uri() . '/assets/css/login.css', [], wp_get_theme()->get('Version') );
} );

/**
 * Change login header url.
 */
add_filter( 'login_headerurl', function() {
	return home_url( '/' );
} );

/**
 * Add login message
 */
add_filter( 'login_message', function( $message ) {
	$message .= <<<HTML
<div class="cappy-login-notice">
新規登録はSNSアカウントが必要だワン！
</div>
HTML;
	return $message;
} );

/**
 * Prevent ofuse CSS
 */
add_filter( 'ofuse_should_load_style', '__return_false' );


add_filter( 'the_content', function( $content ) {
	if ( ! chiramise_should_check() ) {
		return $content;
	}
	if ( ! chiramise_can_read() ) {
		return $content;
	}
	// This content is members only.
	ob_start();
	get_template_part( 'template-parts/module/separator', 'more' );
	$block = implode( "\n", array_map( 'trim', explode( "\n", ob_get_contents() ) ) );
	ob_end_clean();
	return str_replace( '<!--more-->', $block, get_post()->post_content );
}, 2 );

<?php

/**
 * Move Jetpack share scripts to footer.
 */
add_action( 'wp_footer', function() {
	if ( has_action( 'wp_footer', 'sharing_add_footer' ) ) {
		remove_action( 'wp_footer', 'sharing_add_footer' );
		add_action( 'wp_footer', 'sharing_add_footer', 20 );
	}
}, 1 );

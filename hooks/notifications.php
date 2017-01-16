<?php
/**
 * Notification utilities
 *
 * @package capitalp
 */

// Send notification if post status is changed.
add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {
	if ( 'post' !== $post->post_type ) {
		return;
	}
	if ( ( 'publish' === $new_status ) && ( 'publish' !== $old_status ) ) {
		// Post is newly published.
		$content = '@channel 新しい記事が公開されました %s';
		do_action( 'hameslack', sprintf( $content, get_permalink( $post ) ) );
	}
}, 10, 3 );

<?php

/**
 * Detect if current page is English page.
 *
 * @return bool
 */
function capitalp_is_english_page() {
	return is_post_type_archive( 'en' ) || is_singular( 'en' );
}

/**
 * Returns parent post's ID
 *
 * @return int
 */
function capitalp_original_page() {
	if ( ! is_singular( 'en' ) ) {
		return 0;
	}
	return get_queried_object()->post_parent;
}

/**
 * Get translated post
 *
 * @return null|WP_Post
 */
function capitalp_translated_alternative() {
	if ( ! is_single() ) {
		return null;
	}
	foreach ( get_posts( [
		'post_type' => 'en',
	    'posts_per_page' => 1,
	    'post_status' => 'publish',
	    'post_parent' => get_queried_object_id(),
	] ) as $p ) {
		return $p;
	}
	return null;
}

/**
 * Detect if capitalists wants to be public.
 *
 * @param int $user_id
 * @return bool
 */
function capitalp_is_public_capitalist( $user_id ) {
	return ! get_user_meta( $user_id, 'hidden_capitalist', true );
}

<?php
/**
 * Series related functions.
 */

/**
 * Detect if tag is series.
 *
 * @param int|WP_Term $term
 * @return bool
 */
function capitalp_is_series( $term ) {
	$term = get_term( $term, 'post_tag' );
	if ( ! $term || is_wp_error( $term ) ) {
		return false;
	}
	return (bool) get_term_meta( $term->term_id, 'is_series', true );
}

/**
 * Get series tag for post.
 *
 * @param null|int|WP_Post $post
 * @return array|false|WP_Error
 */
function capitalp_get_series( $post = null ) {
	$post = get_post( $post );
	$tags = get_the_tags( $post->ID );
	if ( ! $tags || is_wp_error( $tags ) ) {
		return [];
	}
	$filtered = array_filter( $tags, function( $tag ) {
		return capitalp_is_series( $tag );
	} );
	return $filtered;
}

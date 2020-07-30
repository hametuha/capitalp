<?php
/**
 * Advertisement hooks
 *
 * @package capitalp
 */



/**
 * Add PR to title.
 *
 * @param string $title
 * @param null|int $post_id
 * @return string
 */
function capitalp_add_pr_to_title( $title, $post_id = null ) {
	$post = get_post( $post_id );
	if ( capitalp_is_pr( $post ) ) {
		$title = '[PR]' . $title;
	}
	return $title;
}
add_filter( 'the_title', 'capitalp_add_pr_to_title', 10, 2 );
add_filter( 'the_title_rss', 'capitalp_add_pr_to_title', 10 );

/**
 * Add pr to title for Yoast
 * @param string $title
 * @return string
 */
add_filter( 'wpseo_title', function( $title ) {
	if ( is_single() && capitalp_is_pr( get_queried_object() ) ) {
		$title = '[PR]' . $title;
	}
	return $title;
}, 20 );

/**
 * Add PR to title if yoast is deactivated.
 *
 * @param array $title
 * @return array
 */
add_filter( 'document_title_parts', function ( $title ) {
	if ( is_single() && capitalp_is_pr( get_queried_object() ) ) {
		$title['title'] = '[PR]' . $title['title'];
	}
	return $title;
} );

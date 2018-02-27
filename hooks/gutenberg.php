<?php
/**
 * Gutenberg related functions.
 */

/**
 * Prohibit post page.
 */
add_filter( 'gutenberg_can_edit_post_type', function( $can_edit, $post_type ) {
	switch ( $post_type ) {
		case 'bible':
			return true;
		default:
			return false;
	}
}, 10, 2 );

/**
 * Register post type "Bible" only which gutenberg can publish.
 */
add_action( 'init', function() {
	register_post_type( 'bible', [
		'label'  => '聖書',
		'public' => true,
		'capability_type' => 'post',
		'menu_icon' => 'dashicons-book-alt',
		'show_in_rest' => true,
	] );
} );

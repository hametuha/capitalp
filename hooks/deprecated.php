<?php

// Remove excerpt link.
add_action( 'after_setup_theme', function(){
	remove_filter( 'excerpt_more', 'twentyseventeen_excerpt_more' );
} );

<?php

/**
 * Change User contact method
 */
add_filter( 'user_contactmethods', function( $methods ) {
	return [
		'facebook' => 'Facebook(URL)',
		'twitter' => 'Twitter(URL)',
		'instagram' => 'Instagram(URL)',
		'github' =>  'Github(URL)',
	];
} );

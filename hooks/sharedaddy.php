<?php
/**
 * Add share service
 *
 * @param array $services
 * @return array
 */
add_filter( 'sharing_services', function ( $services ) {
	require_once get_stylesheet_directory() . '/classes/Cappy_Share_Hatebu.php';
	$services['hatebu'] = 'Cappy_Share_Hatebu';
	return $services;
} );


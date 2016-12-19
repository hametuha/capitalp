<?php
/**
 * Functions
 */


// Load all file
foreach ( [ 'functions', 'hooks' ] as $dir ) {
	$dir = __DIR__.'/'.$dir;
	if ( is_dir( $dir ) ) {
		foreach ( scandir( $dir ) as $file ) {
			if ( preg_match( '#^[^.].*\.php$#u', $file ) ) {
				require $dir.'/'.$file;
			}
		}
	}
}



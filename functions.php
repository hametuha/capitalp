<?php
/**
 * Functions
 */

// Load auto loader.
$autloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autloader ) ) {
	require $autloader;
}

// Load all file.
foreach ( [ 'functions', 'hooks' ] as $dir ) {
	$dir = __DIR__ . '/' . $dir;
	if ( is_dir( $dir ) ) {
		foreach ( scandir( $dir ) as $file ) {
			if ( ! preg_match( '#^[^.].*\.php$#u', $file ) ) {
				continue;
			}
			if ( 'deprecated.php' == $file ) {
				continue;
			}
			require $dir . '/' . $file;
		}
	}
}

// Load all commands on CLI.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	foreach ( scandir( __DIR__ . '/commands' ) as $command ) {
		// Check if this is PHP.
		if ( ! preg_match( '#^([^.].*)\.php$#', $command, $matches ) ) {
			continue;
		}
		// Load class file. File name must be class name.
		$class_name = $matches[1];
		require __DIR__ . "/commands/{$command}";
		// Check if Class Exists.
		if ( ! class_exists( $class_name ) ) {
			continue;
		}
		// Check if it has constant `command_name`.
		$reflection = new ReflectionClass( $class_name );
		if ( ! $reflection->hasConstant( 'command_name' ) || ! $reflection->isSubclassOf( 'WP_CLI_Command' ) ) {
			continue;
		}
		WP_CLI::add_command( $class_name::command_name, $class_name );
	}
}

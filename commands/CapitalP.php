<?php

/**
 * Command utility for Capital P.
 */
class CapitalP extends WP_CLI_Command {

	const command_name = 'capitalp';

	/**
	 * Display information about mime types.
	 *
	 * ## OPTIONS
	 *
	 * No options for this command.
	 */
	public function mimes() {
		$mimes = get_allowed_mime_types();
		$table = new \cli\Table();
		$table->setHeaders( [ '#', 'RegExp', 'Mime Type' ] );
		$i = 0;
		foreach ( $mimes as $reg => $mime_type ) {
			$table->addRow( [ $i, $reg, $mime_type ] );
			$i++;
		}
		$table->display();
	}
}

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
	
	/**
	 * Get CCP members
	 */
	public function members() {
		$members = ofuse_members();
		if ( ! $members ) {
			WP_CLI::error( 'メンバーが一人もいません……' );
		}
		$table = new cli\Table();
		$table->setHeaders( [ 'ID', 'Login', 'Name', 'Mail', 'FaceBook' ] );
		foreach ( $members as $member ) {
			/** @var WP_User $member */
			$row = [ $member->ID, $member->user_login, $member->display_name, $member->user_email ];
			if ( function_exists( 'gianism_is_user_connected_with' ) && gianism_is_user_connected_with( 'facebook', $member->ID ) ) {
				$row[] = gianism_get_facebook_id( $member->ID );
			} else {
				$row[] = 'X';
			}
			$table->addRow( $row );
		}
		$table->display();
	}
}

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
	
	/**
	 * Update CCP role.
	 *
	 * Paid member should be subscriber.
	 */
	public function update_role() {
		$table = new cli\Table();
		$table->setHeaders( [ 'No Change', 'Upgrade', 'Resign' ] );
		$body = capitalp_update_bulk_role();
		$table->addRow( $body );
		$table->display();
		WP_CLI::success( sprintf( 'Above is the CCP %d members status.', $body[0] + $body[1] + $body[2] ) );
	}
	
	/**
	 * Get analytics test.
	 *
	 *
	 * @synopsis [--start-date=<start-date>] [--end-date=<end-date>] [--metrics=<metrics>] [--max-results=<max-results>] [--dimensions=<dimensions>] [--filters=<filters>]
	 * @param array $args
	 * @param array $assoc
	 */
	public function analytics( $args, $assoc ) {
		$fetcher = capitalp_analytics();
		if ( ! $fetcher ) {
			WP_CLI::error( 'Failed to get Google Analytics fetcher. Please check setting.' );
		}
		$args = [];
		foreach ( wp_parse_args( $assoc, [
			'start-date'  => date_i18n( 'Y-m-d', strtotime( '7 days ago' ) ),
			'end-date'    => date_i18n( 'Y-m-d' ),
			'metrics'     => 'ga:pageviews',
			'max-results' => 20,
			'dimensions'  => 'ga:pagePath',
			'filters'     => '',
			'sort'        => '-ga:pageviews',
			'start-index' => '',
		] ) as $key => $value ) {
			switch ( $key ) {
				case 'start-date':
					$start_date = $value;
					break;
				case 'end-date':
					$end_date = $value;
					break;
				case 'metrics':
					$metrics = $value;
					break;
				default:
					if ( '' !== $value ) {
						$args[ $key ] = $value;
					}
					break;
			}
		}
		try {
			$result = $fetcher->fetch( $start_date, $end_date, $metrics, $args, true );
			if ( ! $result ) {
				WP_CLI::error( 'No data found.' );
			}
			$table = new cli\Table();
			$header = [];
			foreach ( [ $args['dimensions'], $metrics ] as $csv ) {
				foreach ( explode( ',', $csv ) as $col ) {
					if ( $col ) {
						$header[] = $col;
					}
				}
			}
			$table->setHeaders( $header );
			$table->setRows( $result );
			$table->display();
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
	
	
}

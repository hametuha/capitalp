<?php
/**
 * Loop related functions
 *
 * @package capitalp
 * @since 1.0.3
 */


/**
 * Get counter
 *
 * @since 1.0.3
 * @param string $action
 * @param int    $value
 *
 * @return int
 */
function capitalp_counter( $action = 'increment', $value = 1 ) {
	static $counter = 0;
	switch ( $action ) {
		case 'clear':
			$counter = 0;
			break;
		case 'set':
			$counter = $value;
			break;
		case 'increment':
			$counter += $value;
			break;
		default:
			// Do nothing.
			break;
	}
	return $counter;
}

/**
 * Get template counter.
 *
 * @param string $file
 * @return int
 */
function capitalp_template_counter( $file ) {
	static $counter = [];
	if ( ! isset( $counter[ $file ] ) ) {
		$counter[ $file ] = 0;
	}
	$counter[ $file ]++;
	return $counter[ $file ];
}

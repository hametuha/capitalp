<?php

/**
 * Get analytics fetcher
 *
 * @return \Gianism\Plugins\AnalyticsFetcher
 */
function capitalp_analytics() {
	try {
		if ( ! class_exists( 'Gianism\\Plugins\\AnalyticsFetcher' ) ) {
			return null;
		}
		return Gianism\Plugins\AnalyticsFetcher::get_instance();
	} catch ( Exception $e ) {
		return null;
	}
}

/**
 * Get latest ranking.
 *
 * @param $count
 * @param $start
 * @param string $end
 * @return array|WP_Error
 */
function capitalp_get_ranking( $count, $start, $end = 'now' ) {
	if ( 'now' === $end ) {
		$end = date_i18n( 'Y-m-d' );
	}
	$analytics = capitalp_analytics();
	if ( ! $analytics ) {
		return new WP_Error( 'error', 'APIと接続できません。' );
	}
	try {
		$result = $analytics->fetch( $start, $end, 'ga:pageviews', [
			'max-results' => $count,
			'dimensions' => 'ga:dimension1',
//			'filters' => '',
			'sort' => '-ga:pageviews',
		], true );
	} catch ( Exception $e ) {
		echo $e->getMessage();
	}
	return array_filter( array_map( function( $r ) {
		list( $post_id, $pv ) = $r;
		$post = get_post( $post_id );
		if ( ! $post ) {
			return null;
		} else {
			$post->pv = $pv;
			return $post;
		}
	}, $result ) );
}



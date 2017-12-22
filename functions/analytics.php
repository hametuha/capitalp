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



<?php
/**
 * jQueryをフッターに動かす
 */
add_action( 'init', function() {
	// 管理画面ではjQueryを削除できない。
	if ( is_admin() || 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
		return;
	}
	// 現在のバージョンとURIを保存。
	// CDNを使いたい方は$jquery_srcのURIを変更してもよい。
	global $wp_scripts;
	$jquery = $wp_scripts->registered['jquery-core'];
	$jquery_ver = $jquery->ver;
	$jquery_src = $jquery->src;
	// いったん削除
	wp_deregister_script( 'jquery' );
	wp_deregister_script( 'jquery-core' );
	// 登録しなおし
	wp_register_script( 'jquery', false, [ 'jquery-core' ], $jquery_ver, true );
	wp_register_script( 'jquery-core', $jquery_src, [], $jquery_ver, true );
} );

/**
 * Move Jetpack share scripts to footer.
 */
add_action( 'wp_footer', function() {
	if ( has_action( 'wp_footer', 'sharing_add_footer' ) ) {
		remove_action( 'wp_footer', 'sharing_add_footer' );
		add_action( 'wp_footer', 'sharing_add_footer', 20 );
	}
}, 1 );

/**
 * Move SSP javascript to footer.
 */
//add_filter( 'the_content', function( $content ) {
//	// Enabled only on single podcast page.
//	if ( ! is_single( get_the_ID() ) || ! in_category( 'podcast' ) ) {
//		return $content;
//	}
//	$content = preg_replace_callback( '#<script>.*</script>#us', function( $matches ) {
//		add_action( 'wp_footer', function() use ( $matches ) {
//			echo $matches[0];
//		}, 30 );
//		return '';
//	}, $content );
//	return $content;
//} );


/**
 * Add preloader to style tag.
 */
add_filter( 'style_loader_tag', function( $tag, $handle, $href, $media ) {
	if ( ! file_exists( get_stylesheet_directory() . '/assets/js/cssrelpreload.min.js' ) ) {
		return $tag;
	}
	if ( false !== array_search( $handle, [ 'snow-monkey' ] ) ) {
		return $tag;
	}
	if ( false === strpos( $href, home_url() ) ) {
		return $tag;
	}
	$pre_loader = str_replace( "rel='stylesheet'", 'rel="preload" as="style" onload="this.onload=null; this.rel=\'stylesheet\'"', $tag );
	return <<<HTML
{$pre_loader}
<noscript>{$tag}</noscript>
HTML;
}, 10, 4 );

/**
 * Render script loader css.
 */
add_action( 'wp_footer', function() {
	if ( ! file_exists( get_stylesheet_directory() . '/assets/js/cssrelpreload.min.js' ) ) {
		return;
	}
	printf( "<script>\n%s\n</script>", file_get_contents( get_stylesheet_directory() . '/assets/js/cssrelpreload.min.js' ) );
}, 100 );

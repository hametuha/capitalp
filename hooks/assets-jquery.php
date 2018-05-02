<?php
/**
 * jQueryをフッターに動かす
 */
add_action( 'init', function() {
	// 管理画面ではjQueryを削除できない。
	if ( is_admin() ) {
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

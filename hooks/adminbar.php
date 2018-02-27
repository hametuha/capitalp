<?php
/**
 * Adminbar related functions.
 */
add_action( 'admin_bar_menu', function ( WP_Admin_Bar &$admin_bar ) {
	// Add External sites.
	$admin_bar->add_group( [
		'id' => 'external',
		'title' => '外部サイト',
		'parent' => 'site-name',
	] );
	
	$admin_bar->add_menu( [
		'id' => 'slack',
		'parent' => 'external',
		'title' => 'Slack',
		'href' => 'https://capital-p.slack.com/',
		'group' => false,
		'meta' => [
			'target' => '_blank',
		],
	] );
	
	$admin_bar->add_menu( [
		'id' => 'sendgrid',
		'parent' => 'external',
		'title' => 'Sendgrid',
		'href' => 'https://sendgrid.kke.co.jp/app?p=login.index',
		'group' => false,
		'meta' => [
			'target' => '_blank',
		],
	] );
	
	$admin_bar->add_menu( [
		'id' => 'fb-page',
		'parent' => 'external',
		'title' => 'Facebook',
		'href' => 'https://www.facebook.com/capitalpjp/insights/?referrer=page_insights_tab_button',
		'group' => false,
		'meta' => [
			'target' => '_blank',
		],
	] );
}, 9999 );

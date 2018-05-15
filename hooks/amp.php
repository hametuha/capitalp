<?php

/**
 * AMPのJSを追加する
 */
add_action( 'amp_post_template_head', function() {
	?>
	<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
	<script async custom-element="amp-sticky-ad" src="https://cdn.ampproject.org/v0/amp-sticky-ad-1.0.js"></script>
	<?php
}, 10 );



/**
 * AMPのCSSを書き出す
 */
add_action( 'amp_post_template_css', function() {
	$css_path = get_stylesheet_directory() . '/assets/css/amp.css';
	if ( ! file_exists( $css_path ) ) {
		return;
	}
	$css = file_get_contents( $css_path );
	$css = preg_replace( '@/\*#.*\*/@u', '', $css );
	echo $css;
} );



/**
 * AMPでofuseを導線に変える
 */
add_action( 'pre_amp_render_post', function() {
	add_filter( 'the_content', function( $content ) {
		$contents = explode( '<div class="ofuse-instruction">', $content );
		if ( 2 === count( $contents ) ) {
			$content  = $contents[0];
			$url      = get_permalink();
			$src      = get_stylesheet_directory_uri() . '/assets/img/dog-blue.png';
			$content .= <<<HTML
<div class="cappy-amp-readmore">
	<amp-img src="{$src}" width="120" height="120"></amp-img>
	<p class="cappy-amp-readmore-description">
		この投稿の続きを読むためには、Capital Pでログインする必要があります。
	</p>
	<a class="cappy-amp-readmore-button" href="{$url}">続きを読む</a>
</div>
HTML;

		}
		return $content;
	}, 20 );
} );

/**
 * Podcastを除外する
 */
add_filter( 'amp_post_status_default_enabled', function( $enabled, $post ) {
	if ( 'post' === $post->post_type && in_category( 'podcast', $post ) ) {
		return false;
	}
	return $enabled;
}, 10, 2 );

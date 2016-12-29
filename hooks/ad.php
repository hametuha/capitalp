<?php
/**
 * Advertisement hooks
 *
 * @package capitalp
 */

/**
 * Show ad
 */
add_filter( 'the_content', function ( $content ) {
	if ( is_singular( 'post' ) ) {
		ob_start();
		?>
        <div class="ad-single-top">
            <span class="ad-title">SPONSORED LINK</span>
			<?php capitalp_ad( 'after_title' ) ?>
        </div>
		<?php
		$ad = ob_get_contents();
		ob_end_clean();
		$content = $ad . $content;
	}

	return $content;
} );

/**
 * Show advertisement after content
 */
add_filter( 'the_content', function ( $content ) {
	if ( is_singular( 'post' ) ) {
		ob_start();
		get_template_part( 'template-parts/block/ad', 'content' );
		$ad = ob_get_contents();
		ob_end_clean();
		$content .= $ad;
	}

	return $content;
}, 11 );

/**
 * Register sidebar for widget
 */
add_action( 'init', function () {
	register_sidebar( [
		'id'            => 'after-content',
		'name'          => 'コンテンツ直下',
		'description'   => 'コンテンツ直下に表示されます。一個ぐらいにしておいてください。',
		'before_widget' => '<aside class="ad-widget-content">',
		'before_title'  => '<h3 class="ad-widget-title">',
		'after_title'   => '</h3>',
		'after_widget'  => '</aside>',
	] );
}, 11 );

/**
 * Register widgetss
 */
add_action( 'widgets_init', function () {
	register_widget( CapitalP_WidgetAdsence::class );
} );

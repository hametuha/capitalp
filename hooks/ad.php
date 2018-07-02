<?php
/**
 * Advertisement hooks
 *
 * @package capitalp
 */


/**
 * Load ad template
 */
add_action( 'get_template_part_template-parts/entry-tags', function( $slug, $name ) {
	if ( is_singular( 'post' ) ) {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_content', chiramise_filter_content_function(), 1 );
		remove_filter( 'the_content', 'capitalp_chiramise_content', 2 );
		get_template_part( 'template-parts/block/ad', 'content' );
	}
}, 10, 2 );

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
 * Display infeed ads.
 */
add_action( 'get_template_part_template-parts/entry-summary', function($slug, $name) {
	if ( ( is_singular() || is_page() ) && ! is_front_page() ) {
		return;
	}
	static $counter = 0;
	$counter++;
	if ( ( 0 === $counter % 4 ) && ( 1 < $counter ) ) {
		?>
		<?php capitalp_ad( 'infeed' ) ?>
		</li>
		<li class="c-entries__item">
		<?php
	}
}, 10, 2 );

/**
 * Automatic ad
 */
add_action( 'wp_head', function() {
	?>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-0087037684083564",
        enable_page_level_ads: true
      });
	</script>
	<?php
} );

/**
 * Register positions.
 */
add_filter( 'taf_default_positions', function( $positions ) {
	$positions = array_merge( $positions, [
		'after_content' => [
			'name'        => 'コンテンツ直下',
			'description' => 'コンテンツ直下に表示されます。',
		],
	] );
	return $positions;
} );

<?php
/**
 * Advertisement hooks
 *
 * @package capitalp
 */



/**
 * Add PR to title.
 *
 * @param string $title
 * @param null|int $post_id
 * @return string
 */
function capitalp_add_pr_to_title( $title, $post_id = null ) {
	$post = get_post( $post_id );
	if ( capitalp_is_pr( $post ) ) {
		$title = '[PR]' . $title;
	}
	return $title;
}
add_filter( 'the_title', 'capitalp_add_pr_to_title', 10, 2 );
add_filter( 'the_title_rss', 'capitalp_add_pr_to_title', 10 );

/**
 * Add pr to title for Yoast
 * @param string $title
 * @return string
 */
add_filter( 'wpseo_title', function( $title ) {
	if ( is_single() && capitalp_is_pr( get_queried_object() ) ) {
		$title = '[PR]' . $title;
	}
	return $title;
}, 20 );

/**
 * Add PR to title if yoast is deactivated.
 *
 * @param array $title
 * @return array
 */
add_filter( 'document_title_parts', function ( $title ) {
	if ( is_single() && capitalp_is_pr( get_queried_object() ) ) {
		$title['title'] = '[PR]' . $title['title'];
	}
	return $title;
} );

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

<?php
/**
 * Single view template.
 *
 * @package AMP
 */

/**
 * Context.
 *
 * @var AMP_Post_Template $this
 */

$this->load_parts( [ 'html-start' ] );
?>

<amp-auto-ads
	type="adsense"
	data-ad-client="ca-pub-0087037684083564">
</amp-auto-ads>

<?php $this->load_parts( [ 'header' ] ); ?>

<article class="amp-wp-article">
	<header class="amp-wp-article-header">
		<h1 class="amp-wp-title"><?php echo esc_html( $this->get( 'post_title' ) ); ?></h1>
		<?php $this->load_parts( apply_filters( 'amp_post_article_header_meta', [ 'meta-author', 'meta-time' ] ) ); ?>
	</header>

	<?php $this->load_parts( [ 'featured-image' ] ); ?>
	
	<amp-ad width="100vw" height="320"
			type="adsense"
			data-ad-client="ca-pub-0087037684083564"
			data-ad-slot="8628945700"
			data-auto-format="rspv"
			data-full-width>
		<div overflow></div>
	</amp-ad>
	

	<div class="amp-wp-article-content">
		<?php echo $this->get( 'post_amp_content' ); // WPCS: XSS ok. Handled in AMP_Content::transform(). ?>
	</div>
	
	<amp-ad width="100vw" height=320
			type="adsense"
			data-ad-client="ca-pub-0087037684083564"
			data-ad-slot="6781511564"
			data-auto-format="rspv"
			data-full-width>
		<div overflow></div>
	</amp-ad>
	
	<footer class="amp-wp-article-footer">
		<?php $this->load_parts( apply_filters( 'amp_post_article_footer_meta', [ 'meta-taxonomy', 'meta-comments-link' ] ) ); ?>
	</footer>
</article>

<?php $this->load_parts( [ 'footer' ] ); ?>

<amp-sticky-ad layout="nodisplay">
	<amp-ad width="100vw"
			height="50"
			type="adsense"
			data-ad-client="ca-pub-0087037684083564"
			data-ad-slot="1170092238"
			data-auto-format="rspv"
			data-full-width>
		<div overflow></div>
	</amp-ad>
</amp-sticky-ad>

<amp-analytics type="googleanalytics">
	<script type="application/json">
		{
			"vars": {
				"account": "UA-1766751-12"
			},
			"extraUrlParams": {
				"cd1": "<?php echo get_queried_object_id(); ?>",
				"cd2": "<?php echo get_queried_object()->post_author; ?>"
			},
			"triggers": {
				"trackPageviewWithCustomUrl": {
					"on": "visible",
					"request": "pageview",
					"vars": {
						"title": "<?php echo wp_get_document_title(); ?>",
						"documentLocation": "<?php echo get_permalink( get_queried_object() ); ?>"
					}
				}
			}
		}
	</script>
</amp-analytics>

<?php
$this->load_parts( [ 'html-end' ] );

<?php
/**
 * Footer template part.
 *
 * @package AMP
 */

/**
 * Context.
 *
 * @var AMP_Post_Template $this
 */
?>
<footer class="amp-wp-footer">
	<div>
		<h2><?php echo esc_html( wptexturize( $this->get( 'blog_name' ) ) ); ?></h2>
		<span class="amp-wp-footer-tagline">
			<?= esc_html( get_bloginfo( 'description' ) ); ?>
		</span>
	</div>
</footer>

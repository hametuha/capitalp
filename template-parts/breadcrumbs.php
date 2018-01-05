<?php
include get_template_directory() . '/template-parts/breadcrumbs.php';
if ( ! ( is_tag() || is_category() ) ) {
	return;
}
?>
<div class="capitalp-series-container">
	<h1 class="capitalp-series-title"><?= esc_html( get_queried_object()->name ) ?></h1>
	<?php if ( get_queried_object()->description ) : ?>
	<p class="capitalp-series-desc">
		<?= nl2br( esc_html( get_queried_object()->description ) ) ?>
	</p>
	<?php endif; ?>
</div>
	



<div class="c-entry-aside">
	<h2 class="c-entry-aside__title">WordPress求人を探す</h2>
	<div class="job-search">
	
	<?php foreach ( [
		'type'    => '就労形態',
		'ability' => '職能',
		'feature' => '特徴',
	] as $taxonomy => $label ) :
		$terms = get_terms( [ 'taxonomy' => $taxonomy ] );
		if ( ! $terms || is_wp_error( $terms ) ) {
			continue;
		}
		?>
		<div class="job-search__taxonomy">
			<h3 class="c-widget__title job-search__title"><?= $label ?>から探す</h3>
			<p>
				<?php array_map( function( $term ) {
					printf( '<a href="%s" class="tag-cloud-link ">%s(%s)</a>', get_term_link( $term ), esc_html( $term->name ), $term->count > 99 ? '99+' : $term->count );
				}, $terms ) ?>
			</p>
		</div>
	<?php endforeach; ?>
	</div>
</div>

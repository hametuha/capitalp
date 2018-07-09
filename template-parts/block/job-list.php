<?php
// Get latest jobs.
$query_args = [
	'post_type'      => 'job',
	'posts_per_page' => 6,
	'post_status'    => 'publish',
];
if ( is_singular( 'job' ) ) {
	$query_args['post__not_in'] = [ get_queried_object_id() ];
}
$query = new WP_Query( $query_args );
if ( ! $query->have_posts() ) {
	return;
}
?>
<div class="job-container c-entry-aside">
	<h2 class="c-entry-aside__title">新着WordPress求人</h2>
	<div class="job-list__wrapper">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
		<div class="job-list__loop">
			<?php get_template_part( 'template-parts/entry-summary', 'job' ); ?>
		</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div><!-- //.job-list__wrapper -->
	<div class="wpaw-showcase__action">
		<a class="wpaw-showcase__more" href="<?php echo get_post_type_archive_link( 'job' ) ?>">求人一覧へ</a>
	</div>
</div><!-- //.job-container -->

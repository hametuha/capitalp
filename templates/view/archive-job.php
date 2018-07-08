<?php
/**
 * @package snow-monkey
 * @author inc2734
 * @license GPL-2.0+
 */
?>
<div class="c-entry">
	<header class="c-entry__header">
		<h1 class="c-entry__title">
			<?php echo capitalp_job_board_title() ?>
		</h1>
		
	</header>

	<p class="c-entry__header--description center">
		<?= esc_html( get_post_type_object( 'job' )->description ) ?>
	</p>

	<div class="c-entry__content">
		<div class="p-archive">
			
			<?php $archive_layout  = get_theme_mod( 'archive-layout' ); ?>

			<ul class="c-entries c-entries--<?php echo esc_attr( $archive_layout ); ?>">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<li class="c-entries__item">
						<?php get_template_part( 'template-parts/entry-summary', 'job' ); ?>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>

		<?php get_template_part( 'template-parts/pagination' ); ?>
		
	</div>
	
	<?php get_template_part( 'template-parts/block/job-search' ); ?>
</div>

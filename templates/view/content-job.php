<?php
/**
 * @package snow-monkey
 * @author inc2734
 * @license GPL-2.0+
 */
?>
<?php get_template_part( 'template-parts/title-top-widget-area' ); ?>

<article <?php post_class(); ?>>
	<?php if ( 'title-on-page-header' !== get_theme_mod( 'post-eyecatch' ) ) : ?>
		<header class="c-entry__header">
			<h1 class="c-entry__title">
				<?php the_title(); ?>
				<small class="c-entry__subtitle">WordPress求人</small>
			</h1>
			<div class="c-entry__meta">
				<?php get_template_part( 'template-parts/entry-meta' ); ?>
			</div>

		</header>
	<?php endif; ?>

	<?php do_action( 'snow_monkey_before_entry_content' ); ?>

	<div class="c-entry__content">
		
		<p class="c-entry__header--description">
			<?= esc_html( get_post_type_object( 'job' )->description ) ?>
		</p>

		<?php
		wpvc_get_template_part( 'template-parts/google-adsense', [
			'position' => 'content-top',
		] );
		?>

		<?php
		if ( get_option( 'mwt-display-contents-outline' ) ) {
			get_template_part( 'template-parts/contents-outline' );
		}
		?>
		
		<h2>募集要項</h2>
		<?= wpautop( wp_kses_post( get_the_excerpt() ) ) ?>
		<h2>応募資格</h2>
		<?php echo wpautop( tscfp( '_requirements' ) ) ?>
		<table>
			<tbody>
			<tr>
				<th>募集人数</th>
				<td><?= esc_html( tscfp( '_job_number' ) ) ?></td>
			</tr>
			<tr>
				<th>募集期限</th>
				<td>
					<?php
					$date_str = mysql2date( get_option( 'date_format' ), tscfp( '_job_expires' ) );
					if ( capitalp_job_is_open() ) {
						echo $date_str;
					} else {
						printf( '<del class="job-expired">%s</del><strong class="job-expired">募集終了</strong>', $date_str );
					}
					?>
				</td>
			</tr>
			<tr>
				<th>勤務地</th>
				<td><?= esc_html( tscfp( '_job_place' ) ) ?></td>
			</tr>
			<?php foreach ( [
				'ability' => '職能',
				'feature' => '特徴',
			] as $taxonomy => $label ) {
				?>
				<tr>
					<th><?= $label ?></th>
					<td>
						<?php
						$terms = get_the_terms( get_post(), $taxonomy );
						if ( $terms && ! is_wp_error( $terms ) ) :
							array_map( function( $term ) {
								printf( '<a href="%s" class="tag-cloud-link job-label job-label--%s">%s</a>', get_term_link( $term ), $term->taxonomy, esc_html( $term->name ) );
							}, $terms );
						else : ?>
							<span class="job-list__empty">---</span>
						<?php endif; ?>
					</td>
				</tr>
				<?php
			} ?>
			<tr>
				<th>待遇</th>
				<td>
					<?= esc_html( capitalp_job_reward() ) ?>
				</td>
			</tr>
			</tbody>
		</table>
		<?php get_template_part( 'template-parts/block/job-submission' ); ?>
		<?= apply_filters( 'the_content', 'この採用を友人に紹介するには、シェアをしてください。' ) ?>
	</div>

	<?php do_action( 'snow_monkey_after_entry_content' ); ?>

	<footer class="c-entry__footer">
		<?php
		if ( in_array( get_option( 'mwt-share-buttons-display-position' ), [ 'bottom', 'both' ] ) ) {
			get_template_part( 'template-parts/share-buttons' );
		}
		?>

		<?php get_template_part( 'template-parts/entry-tags' ); ?>

		<?php
		wpvc_get_template_part( 'template-parts/google-adsense', [
			'position' => 'content-bottom',
		] );
		?>

		<?php get_template_part( 'template-parts/like-me-box' ); ?>
	</footer>
</article>


<?php get_template_part( 'template-parts/block/job-list' ); ?>
<?php get_template_part( 'template-parts/block/job-search' ); ?>

<?php get_template_part( 'template-parts/contents-bottom-widget-area' ); ?>

<?php
if ( get_option( 'mwt-display-related-posts' ) ) {
	get_template_part( 'template-parts/related-posts' );
}
?>

<?php
if ( comments_open() || pings_open() || get_comments_number() ) {
	comments_template( '', true );
}

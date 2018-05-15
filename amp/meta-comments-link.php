<div class="cappy-amp-list-wrapper">

<h3 class="cappy-amp-list-title">新着記事</h3>
<?php
	$query = new WP_Query( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'post__not_in'   => [ get_queried_object_id() ],
		'posts_per_page' => 5,
	] );
?>
<ul class="cappy-amp-list">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$src = wp_get_attachment_image_src( get_post_thumbnail_id() );
		?>
		<li class="cappy-amp-list-item">
			<a href="<?php the_permalink(); ?>">
				<?php if ( $src ) : ?>
					<amp-img src="<?= esc_url( $src[0] ) ?>" alt="<?= esc_attr( get_the_title() ) ?>" class="cappy-amp-list-img"
						 width="150" height="150"
						 layout="responsive"></amp-img>
				<?php endif; ?>
				<div class="cappy-amp-list-body">
					<span class="cappy-amp-list-text">
						<?php the_title(); ?>
						<?php
						$cat = get_the_category();
						if ( $cat && ! is_wp_error( $cat ) ) :
							foreach ( $cat as $c ) : ?>
								<span class="cappy-amp-list-term"><?= esc_html( $c->name ) ?></span>
							<?php endforeach; endif; ?>
					</span>
					<span class="cappy-amp-list-date"><?php the_time( get_option( 'date_format' ) ) ?></span>
				</div>
			</a>
		</li>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</ul>
</div>

<div class="amp-wp-meta amp-wp-comments-link">
	<a href="<?php echo home_url( '/' ); ?>">
		Capital Pへ
	</a>
</div>

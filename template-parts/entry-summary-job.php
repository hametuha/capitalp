<a class="job-list__link" href="<?php the_permalink() ?>">

	<section class="c-entry-summary">

		<?php $terms = get_the_terms( get_post(), 'type' ); if ( $terms && ! is_wp_error( $terms ) ) : foreach ( $terms as $term ) : ?>
		<span class="c-entry-summary__term"><?= esc_html( $term->name ) ?></span>
		<?php break; endforeach; endif; ?>
		<div class="c-entry-summary__body">
			<header class="c-entry-summary__header">
				<h2 class="c-entry-summary__title job-list__title">
					<?php the_title() ?>
				</h2>
			</header>
			<div class="c-entry-summary__content job-list__content">
				<?php
				$excerpt = get_the_excerpt();
				$suffix = mb_strlen( $excerpt ) > 60 ? '&hellip;' : '';
				?>
				<?php echo esc_html( mb_substr( $excerpt, 0, 60 ) ) . $suffix; ?>
			</div>
			<ul class="job-list__keywords">
				<li>
					<i class="fa fa-calendar"></i>
					<?php if ( capitalp_job_is_open() ) : ?>
						応募期限: <?= mysql2date( get_option( 'date_format' ), tscfp( '_job_expires' ) ); ?>
					<?php else : ?>
						募集終了
					<?php endif; ?>
				</li>
				<li>
					<i class="fa fa-tag"></i>
					<?php
					$features = get_the_terms( get_post(), 'ability' );
					if ( $features && ! is_wp_error( $features ) ) :
						echo implode( ', ', array_map( function ( $feature ) {
							return esc_html( $feature->name );
						}, $features ) );
					else :
						echo '記載なし';
					endif;
					?>
				</li>
				<li>
					<i class="fa fa-yen"></i>
					<?= esc_html( capitalp_job_reward() ) ?>
				</li>
			</ul>
			<div class="c-entry-summary__meta">
				<ul class="c-meta">
					<?php $companies = get_the_terms( get_post(), 'company' ); if ( $companies && ! is_wp_error( $companies ) ): foreach ( $companies as $company ) : ?>
					<li class="c-meta__item c-meta__item--author">
						<?php if ( $attachment_id = get_term_meta( $company->term_id, 'image', true ) ) :?>
							<?= wp_get_attachment_image( $attachment_id, 'thumbnail', false, [
							'class' => 'avatar photo',
							] ); ?>
						<?php endif; ?>
						<?= esc_html( $company->name ); ?>
					</li>
					<?php endforeach; endif; ?>
				</ul>
			</div>
		</div>

	</section>
</a>

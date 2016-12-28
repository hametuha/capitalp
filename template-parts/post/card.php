<?php
$index = capitalp_counter();
?>
<div class="card">

    <a href="<?php the_permalink() ?>" class="card-link">
		<?php if ( has_post_thumbnail() ) : ?>
            <div class="card-thumbnail" style="background-image: url('<?= esc_attr( get_the_post_thumbnail_url( $post, 'large' ) ) ?>')">

            </div>
		<?php endif; ?>

        <div class="card-content">

            <h2 class="card-title"><?php the_title() ?></h2>

            <div class="card-meta">
                <span class="card-meta-item">
                    <?= get_avatar( get_the_author_meta('ID'), 24, '', get_the_author() ) ?>
                    <?php the_author() ?>
                </span>
                <span class="card-meta-item">
                    <?php the_time( get_option( 'date_format' ) . ' H:i' ) ?>
                </span>
                <?php
                foreach ( [ /* 'category' => 'folder-open', */ 'post_tag' => 'hashtag' ] as $taxonomy => $icon ) :
                    $terms = get_the_terms( $post, $taxonomy );
                    if ( ! $terms || is_wp_error( $terms ) ) {
                        continue;
                    }
                    ?>
                <span class="card-meta-item">
                    <?= twentyseventeen_get_svg( [ 'icon' => $icon ] ) ?>
                    <?= implode( ', ', array_map( function( $term ) {
                        return esc_html( $term->name );
                    }, $terms ) ) ?>
                </span>
                <?php endforeach; ?>
            </div>

            <div class="card-excerpt">
				<?php the_excerpt() ?>
            </div>
        </div>

    </a>

</div>
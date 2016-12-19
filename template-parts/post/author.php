<div class="author-wrapper">
	<?php echo get_avatar( get_the_author_meta( 'ID' ), 96, '', get_the_author(), [ 'class' => 'author-avatar' ] ) ?>
    <div class="author-content">
        <h2 class="author-title">
            <small>執筆者</small>
            <a class="author-name" href="<?= get_author_posts_url( get_the_author_meta( 'ID' ) ) ?>">
                <?php the_author() ?>
            </a>
        </h2>
        <div class="author-desc">
			<?php echo wpautop( get_the_author_meta( 'description' ) ) ?>
        </div>
        <div class="author-links">
            <?php
            $contact = [
                'wordpress' => get_the_author_meta( 'url' ),
            ];
            foreach ( [ 'facebook', 'twitter', 'instagram', 'github' ] as $sns ) {
                $contact[ $sns ] = get_the_author_meta( $sns );
            }
            foreach ( $contact as $icon => $url ) :
                if ( ! $url ) {
                    continue;
                }
            ?>
            <a target="_blank" class="author-link" href="<?= esc_url( $url ) ?>">
                <?= twentyseventeen_get_svg( [
                    'icon' => $icon,
                ] ) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="authors-list">

	<?php
	$users = new WP_User_Query( [
		'role'    => 'administrator',
		'orderby' => [ 'ID' => 'ASC' ],
	] );
	foreach ( $users->get_results() as $user ) :
	?>
		<div class="author-wrapper">

			<?= get_avatar( $user->ID, 96, '', $user->display_name, [ 'class' => 'author-avatar' ] ) ?>

			<div class="authors-content">
				<h2 class="author-title">
				    <a class="author-link-inline" href="<?= esc_url( get_author_posts_url( $user->ID, $user->nicename ) ) ?>">
                        <?= esc_html( $user->display_name ) ?> ›
				    </a>
                </h2>
				<div class="author-desc"><?= apply_filters( 'the_content', $user->description ) ?></div>
			</div>

		</div>
	<?php endforeach; ?>

</div>

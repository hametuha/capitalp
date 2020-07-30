<div class="authors-list">

	<?php
	$users = new WP_User_Query( [
		'role'    => 'administrator',
		'orderby' => [ 'ID' => 'ASC' ],
	] );
	foreach ( $users->get_results() as $user ) :
	?>
		<div class="author-wrapper">

			<?= get_avatar( $user->ID, 96, '', $user->display_name, [ 'class' => 'authors-avatar' ] ) ?>

			<div class="authors-content">
				<h2 class="authors-title">
					<?= esc_html( $user->display_name ) ?>
                </h2>
				<div class="authors-desc"><?= apply_filters( 'the_content', $user->description ) ?></div>
				<p>
					<a class="btn btn-secondary" href="<?= esc_url( get_author_posts_url( $user->ID, $user->nicename ) ) ?>">
						投稿一覧
					</a>
				</p>
			</div>

		</div>
	<?php endforeach; ?>

</div>

<div class="authors-list">

	<?php
	$users = new WP_User_Query(
		[
			'role'    => 'administrator',
			'orderby' => [ 'ID' => 'ASC' ],
		]
	);
	foreach ( $users->get_results() as $user ) :
		?>
		<div class="author-wrapper">

			<?php echo get_avatar( $user->ID, 96, '', $user->display_name, [ 'class' => 'authors-avatar' ] ); ?>

			<div class="authors-content">
				<h2 class="authors-title">
					<?php echo esc_html( $user->display_name ); ?>
				</h2>
				<div class="authors-desc"><?php echo apply_filters( 'the_content', $user->description ); ?></div>
				<p>
					<a class="btn btn-secondary" href="<?php echo esc_url( get_author_posts_url( $user->ID, $user->nicename ) ); ?>">
						投稿一覧
					</a>
				</p>
			</div>

		</div>
	<?php endforeach; ?>

</div>

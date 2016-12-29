<?php

/**
 * Change User contact method
 */
add_filter( 'user_contactmethods', function( $methods ) {
	return [
		'facebook' => 'Facebook(URL)',
		'twitter' => 'Twitter(URL)',
		'instagram' => 'Instagram(URL)',
		'github' =>  'Github(URL)',
	];
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_authors', function( $attributes = [], $content = '' ) {
	ob_start();
	get_template_part( 'template-parts/block/list', 'author' );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_author', function( $attributes = [], $content = '' ) {
	$author_id = get_the_author_meta( 'ID' );
	ob_start();
	$attributes = wp_parse_args( $attributes, [
		'user_id' => $author_id,
	] );
	$user = get_userdata( $attributes['user_id'] );
	$is_main = ( $author_id == $user->ID );
	?>
	<div class="bubble <?= $is_main ? 'main' : 'guest' ?>">
		<div class="bubble-meta">
			<a class="bubble-link" title="この執筆者の記事一覧を見る" href="<?= esc_url( get_author_posts_url( $user->ID ) ) ?>">
				<?= get_avatar( $user->ID, 60, '', $user->display_name, [ 'class' => 'bubble-avatar' ] ) ?>
			</a>
		</div>
		<div class="bubble-content">
			<span class="bubble-name">
				<?= esc_html( $user->display_name ) ?>
			</span>
			<?= wpautop( $attributes['content'] ) ?>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
} );

/**
 * Register short code UI
 */
add_action( 'register_shortcode_ui', function() {
	// Author list
	shortcode_ui_register_for_shortcode( 'capitalp_authors', [
		'label'     => 'Author List',
		'post_type' => [ 'page' ],
		'listItemImage' => 'dashicons-groups',
	] );
	// Interviews
	shortcode_ui_register_for_shortcode( 'capitalp_author', [
		'label'     => 'Comment Bubble',
		'post_type' => [ 'post' ],
		'listItemImage' => 'dashicons-groups',
		'attrs' => [
			[
			'label'    => '発言者',
			'attr'     => 'user_id',
			'type'     => 'user_select',
			'multiple' => false,
			],
			[
				'label' => '発言内容',
				'attr'  => 'content',
				'type'  => 'textarea',
			],
		],
	] );
} );
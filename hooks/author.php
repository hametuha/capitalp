<?php
/**
 * Author related hooks.
 *
 * @package capitalp
 */

/**
 * Change User contact method
 */
add_filter( 'user_contactmethods', function ( $methods ) {
	return [
		'facebook'  => 'Facebook(URL)',
		'twitter'   => 'Twitter(URL)',
		'instagram' => 'Instagram(URL)',
		'github'    => 'Github(URL)',
	];
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_authors', function ( $attributes = [], $content = '' ) {
	ob_start();
	get_template_part( 'template-parts/block/list', 'author' );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_interruption', function ( $attributes = [], $content = '' ) {
	$author_id = get_the_author_meta( 'ID' );
	ob_start();
	$attributes = wp_parse_args( $attributes, [
		'user_id'  => $author_id,
		'inserted' => '',
	] );
	$user       = get_userdata( $attributes['user_id'] );
	?>
    <ins class="notation" datetime="<?= mysql2date( DateTime::W3C, $attributes['inserted'] ) ?>">
        <div class="notation-meta">
            <a class="notation-link" title="この執筆者の記事一覧を見る" href="<?= esc_url( get_author_posts_url( $user->ID ) ) ?>">
				<?= esc_html( $user->display_name ) ?>
            </a>による追記
			<?php if ( $attributes['inserted'] ) : ?>
                <small class="notation-date">
                    @ <?= mysql2date( get_option( 'date_format' ), $attributes['inserted'] ) ?>
                </small>
			<?php endif; ?>
        </div>
        <div class="notation-content">
			<?= wpautop( wp_kses_post( $content ) ) ?>
        </div>
    </ins>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
} );

/**
 * Register short code
 */
add_shortcode( 'capitalp_author', function ( $attributes = [], $content = '' ) {
	static $users = [];
	$author_id = get_the_author_meta( 'ID' );
	ob_start();
	$attributes = wp_parse_args( $attributes, [
		'user_id' => $author_id,
	] );
	$user       = get_userdata( $attributes['user_id'] );
	$is_main    = ( $author_id == $user->ID );
	if ( $is_main ) {
		$index = 0;
	} else {
		$index = array_search( $user->ID, $users );
		if ( false === $index ) {
			$users[] = $user->ID;
			$index   = array_search( $user->ID, $users );
		}
		$index ++;
	}
	?>
    <div data-author-index="<?= esc_attr( $index ) ?>"
         class="bubble <?= $is_main ? 'main' : 'guest' ?> author-index-<?= $index ?>">
        <div class="bubble-meta">
            <a class="bubble-link" title="この執筆者の記事一覧を見る" href="<?= esc_url( get_author_posts_url( $user->ID ) ) ?>">
				<?= get_avatar( $user->ID, 60, '', $user->display_name, [ 'class' => 'bubble-avatar' ] ) ?>
            </a>
        </div>
        <div class="bubble-content">
			<span class="bubble-name">
				<?= esc_html( $user->display_name ) ?>
			</span>
			<?= wpautop( wp_kses_post( $content ) ) ?>
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
add_action( 'register_shortcode_ui', function () {
	// Author list.
	shortcode_ui_register_for_shortcode( 'capitalp_authors', [
		'label'         => '著者リスト',
		'post_type'     => [ 'page' ],
		'listItemImage' => 'dashicons-groups',
	] );
	// Insert.
	shortcode_ui_register_for_shortcode( 'capitalp_interruption', [
		'label'         => '別の著者による注釈',
		'post_type'     => [ 'post' ],
		'listItemImage' => 'dashicons-welcome-add-page',
		'inner_content' => [
			'label'       => '注釈内容',
			'description' => '自動で改行が入ります。',
		],
		'attrs'         => [
			[
				'label'    => '発言者',
				'attr'     => 'user_id',
				'type'     => 'user_select',
				'multiple' => false,
			],
			[
				'label' => '追記日',
				'attr'  => 'inserted',
				'type'  => 'date',
				'meta'  => [
					'placeholder' => 'YYYY-MM-DD',
				],
			],
		],
	] );
	// Interviews.
	shortcode_ui_register_for_shortcode( 'capitalp_author', [
		'label'         => '著者の発言',
		'post_type'     => [ 'post' ],
		'listItemImage' => 'dashicons-format-status',
		'inner_content' => [
			'label'       => '発言内容',
			'description' => '自動で改行が入ります。',
		],
		'attrs'         => [
			[
				'label'    => '発言者',
				'attr'     => 'user_id',
				'type'     => 'user_select',
				'multiple' => false,
			],
		],
	] );
} );

/**
 * Filter license request.
 */
add_filter( 'hameslack_user_can_request_invitation', function( $can, $user_id ) {
	return ofuse_is_user_valid( $user_id );
}, 10, 2 );

/**
 * Show instruction
 */
add_action( 'show_user_profile', function( $user ) {
	if ( ofuse_is_user_valid( $user->ID ) ) {
		// If user is valid, show nothing.
		return;
	}
	?>
	<h3>Slackへ参加</h3>
	<p class="description">
		<a href="<?= home_url( '/ccp' ) ?>" target="_blank">Capital Pのライセンス</a>をご購入いただくと、Slackに参加できます。
	</p>
	<p>
		<a href="<?= home_url( '/ccp' ) ?>" class="button" target="_blank">ライセンスを購入</a>
	</p>
	<?php
} );

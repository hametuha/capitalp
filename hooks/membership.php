<?php
/**
 * Membership related hooks
 */

// Load header css
add_action( 'login_enqueue_scripts', function() {
	wp_enqueue_style( 'login-header', get_stylesheet_directory_uri() . '/assets/css/login.css', [], wp_get_theme()->get('Version') );
} );

/**
 * Change login header url.
 */
add_filter( 'login_headerurl', function() {
	return home_url( '/' );
} );

/**
 * Add login message
 */
add_filter( 'login_message', function( $message ) {
	$message .= <<<HTML
<div class="cappy-login-notice">
新規登録はSNSアカウントでのログインが必要だワン！
</div>
HTML;
	return $message;
} );

/**
 * Prevent ofuse CSS
 */
add_filter( 'ofuse_should_load_style', '__return_false' );

/**
 * Add separator for member only contents
 */
function capitalp_chiramise_content( $content ) {
	if ( ! chiramise_should_check() ) {
		return $content;
	}
	if ( ! chiramise_can_read() ) {
		return $content;
	}
	// This content is members only.
	ob_start();
	get_template_part( 'template-parts/module/separator', 'more' );
	$block = implode( "\n", array_filter( array_map( 'trim', explode( "\n", ob_get_contents() ) ) ) );
	ob_end_clean();
	return str_replace( '<!--more-->', $block, get_post()->post_content );
}
add_filter( 'the_content', 'capitalp_chiramise_content', 2 );

/**
 * Display member list
 */
add_shortcode( 'capitalists', function( $args = [], $contents = '' ) {
	$members = ofuse_members();
	if ( ! $members ) {
		return '';
	}
	ob_start();
	foreach ( $members as $member ) {
		include get_stylesheet_directory() . '/template-parts/block/loop-member.php';
	}
	$block = sprintf(
		"<p class=\"capitalists-desc\">Capital Pは%d人のCapitalistsによって支えられています。</p>\n<div class=\"capitalists-wrapper\">\n%s\n</div>",
		count( $members ),
		implode( "\n", array_filter( array_map( 'trim', explode( "\n", ob_get_contents() ) ) ) )
	);
	ob_end_clean();
	return $block;
} );

/**
 * プロフィールページ
 */
add_action( 'ofuse_membership_table', function( $user ) {
	?>
	<tr>
		<th>
			<label for="hide_from_capitalist">プライバシー</label>
		</th>
		<td>
			<label>
				<input type="checkbox" id="hide_from_capitalist" name="hide_from_capitalist" value="1" <?php checked( ! capitalp_is_public_capitalist( $user->ID ) ) ?>/>
				支援者リストから隠す
			</label>
		</td>
	</tr>
	<?php
}, 11 );

// Update license
add_action( 'ofuse_member_profile_update', function( $user_id, $old_data ) {
	if ( isset( $_POST['hide_from_capitalist'] ) && $_POST['hide_from_capitalist'] ) {
		update_user_meta( $user_id, 'hidden_capitalist', 1 );
	} else {
		delete_user_meta( $user_id, 'hidden_capitalist' );
	}
}, 10, 2 );

add_action( 'admin_init', function() {
	// reading 設定ページへフィールドを追加する準備として
	// セクションを追加
	add_settings_section(
		'capitalp_membership',
		'Club Capital P',
		function() {
			?>
			<p class="description">Capital Pの有料会員クラブについての設定項目です。</p>
			<?php
		},
		'reading'
	);
	
	// その新しいセクションの中に
	// 新しい設定の名前と関数を指定しながらフィールドを追加
	add_settings_field(
		'capitalp_group_id',
		'Facebook Group ID',
		function() {
			?>
			<input type="text" name="capitalp_group_id" id="capitalp_group_id" value="<?= esc_attr( get_option( 'capitalp_group_id', '' ) ) ?>" />
			<?php
		},
		'reading',
		'capitalp_membership'
	);
	
	register_setting( 'reading', 'capitalp_group_id' );
} );

/**
 * change user status
 */
add_action( 'set_logged_in_cookie', function( $logged_in_cookie, $expire, $expiration, $user_id ) {
	capitalp_assign_role( $user_id );
}, 11, 4 );

/**
 * Hide others posts from contributor
 */
add_action( 'pre_get_posts', function( WP_Query &$wp_query ) {
	if ( ! $wp_query->is_main_query() || ! is_admin() || wp_doing_ajax() ) {
		return;
	}
	if ( current_user_can( 'contributor' ) ) {
		$wp_query->set( 'author', get_current_user_id() );
	}
} );

// Weekly cron
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'capitalp_membership' ) ) {
		wp_schedule_event( current_time( 'timestamp', true ), 'weekly', 'capitalp_membership' );
	}
} );

// Bulk check in a week.
add_action( 'capitalp_membership', 'capitalp_update_bulk_role' );

// Send slack notification if post is pending.
add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {
	if ( 'pending' == $new_status && 'pending' != $old_status && function_exists( 'hameslack_post' ) ) {
		hameslack_post( sprintf( '@channel 新しい投稿が公開待ちだワン！ %s', get_edit_post_link( $post->ID ) ) );
	}
}, 10, 3 );


/**
 * Add hamail user field/
 */
add_filter( 'hamail_user_field', function( $field, $user ) {
	$field['membership'] = (int) $field['membership'];
	$field['user_registered'] = mysql2date( 'm/d/Y', $field['user_registered'] );
	return $field;
}, 10, 2 );


/**
 * Post to slack if contact created.
 */
add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {
	if ( ( 'feedback' === $post->post_tpe ) && ( 'publish' == $new_status ) && ( 'publish' != $old_status ) ) {
		hameslack_post( sprintf( '@channel ユーザーから問い合わせがあったワン！ %s', admin_url( 'edit.php?post_type=feedback' ) ) );
	}
}, 10, 3 );

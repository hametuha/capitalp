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
新規登録はSNSアカウントが必要だワン！
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
add_filter( 'the_content', function( $content ) {
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
}, 2 );

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

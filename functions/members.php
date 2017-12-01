<?php
/**
 * Membership related functions.
 */

/**
 * Detect if capitalists wants to be public.
 *
 * @param int $user_id
 * @return bool
 */
function capitalp_is_public_capitalist( $user_id ) {
	return ! get_user_meta( $user_id, 'hidden_capitalist', true );
}

/**
 * Assign proper user.
 *
 * @param int $user_id
 * @return int
 */
function capitalp_assign_role( $user_id ) {
	$status = 0;
	$subject = 'Capital P 会員権限変更のお知らせ';
	$body = '';
	$user = null;
	$profile_url = admin_url( 'profile.php' );
	if ( ofuse_is_user_valid( $user_id ) ) {
		// This is valid user.
		if ( user_can( $user_id, 'subscriber' ) ) {
			// Make him/her contributor.
			$user = new WP_User( $user_id );
			$user->set_role( 'contributor' );
			$status = 1;
			$body = <<<TXT
{$user->display_name} 様

Capital Pです。
ご利用ありがとうございます。

有料会員の方は寄稿者としてCapital Pに記事を寄稿することができます。
ステータスの詳細は以下のURLよりご覧ください。

{$profile_url}

※このメールは自動送信です。
TXT;

		}
	} else {
		// This is invalid user.
		if ( user_can( $user_id, 'contributor' ) ) {
			// Make him/her subscriber.
			$user = new WP_User( $user_id );
			$user->set_role( 'subscriber' );
			$status = -1;
			$body = <<<TXT
{$user->display_name} 様

Capital Pです。
ご利用ありがとうございます。

{$user->display_name} さんのステータスが投稿者に変更されました。
ライセンスの有効期限切れなどが原因だと思われます。
ステータスの詳細は以下のURLよりご覧ください。

{$profile_url}

※このメールは自動送信です。

TXT;
		}
	}
	if ( $body && $user ) {
		wp_mail( $user->user_email, $subject, $body, [
			'From: ' . get_option( 'admin_email' ),
			'Reply-To: ' . get_option( 'admin_email' ),
		] );
	}
	return $status;
}

/**
 * Update capitalists role.
 *
 * @return array
 */
function capitalp_update_bulk_role() {
	$users = new WP_User_Query( [
		'role__in' => [ 'subscriber', 'contributor' ],
		'number' => -1,
	] );
	$body = [ 0, 0, 0 ];
	foreach ( $users->get_results() as $user ) {
		switch ( capitalp_assign_role( $user->ID ) ) {
			case 1:
				$body[1]++;
				break;
			case -1:
				$body[2]++;
				break;
			default:
				$body[0]++;
				break;
		}
	}
	return $body;
}

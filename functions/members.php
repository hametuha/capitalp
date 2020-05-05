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

/**
 * Get slack members.
 *
 * @return array
 */
function capitalp_get_slack_members() {
	if ( ! function_exists( 'hameslack_members' ) ) {
		return [];
	}
	// Grab users.
	$slack_users = [];
	foreach ( hameslack_members() as $member ) {
		$slack_users[ $member->name ] = [
			'slack_id' => $member->id,
			'wp_id' => 0,
			'real_name' => $member->real_name,
		];
	}
	global $wpdb;
	$query = <<<SQL
		SELECT user_id, meta_value FROM {$wpdb->usermeta}
		WHERE  meta_key   = 'slack'
		  AND  meta_value != ''
SQL;
	foreach ( $wpdb->get_results( $query ) as $user ) {
		$wp_name = $user->meta_value;
		if ( array_key_exists( $wp_name, $slack_users ) ) {
			// Search with name.
			$slack_users[ $wp_name ]['wp_id'] = (int) $user->user_id;
		} else {
			// Or else, check 1 by 1.
			foreach ( $slack_users as &$slack_user) {
				if ( $slack_user['real_name'] == $wp_name ) {
					$slack_user['wp_id'] = (int) $user->user_id;
					break 1;
				}
			}
		}
	}
	return $slack_users;
}

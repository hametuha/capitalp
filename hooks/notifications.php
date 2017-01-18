<?php
/**
 * Notification utilities
 *
 * @package capitalp
 */

// Send notification if post status is changed.
add_action( 'transition_post_status', function( $new_status, $old_status, $post ) {
	if ( 'post' !== $post->post_type ) {
		return;
	}
	if ( ( 'publish' === $new_status ) && ( 'publish' !== $old_status ) ) {
		// Post is newly published.
		$content = '@channel 新しく記事が公開されたワン！ %s';
		do_action( 'hameslack', sprintf( $content, get_permalink( $post ) ) );
	}
}, 10, 3 );

// Cappy says...
add_filter( 'hameslack_api_default_text', function() {
	return 'ワンワン！　よくわからないワン！';
} );

// Cappy do...
add_filter( 'hameslack_rest_response', function( $response, $request, $post ) {
	switch ( $post->post_name ) {
		case 'cappy-api':
			$text    = explode( "\n", trim( str_replace( 'cappy', '', $request['text'] ) ) );
			$command = array_shift( $text );
			foreach( [
				'#draft#' => function( $response, $request, $post ) use ( $text ) {
					$user_name = $request['user_name'];
					$user_query = new WP_User_Query( [
						'number' => 1,
						'meta_query' => [
							[
								'key' => 'slack',
								'value' => $user_name,
							],
						],
					] );
					$response['text'] = 'ダメだったワン！';
					if ( ! ( $users = $user_query->get_results() ) ) {
						$response['attachment'] = [
							[
								'color' => 'danger',
								'title' => '404 NOT FOUND',
								'text'  => '該当するユーザーがいませんでした',
							],
						];
						return $response;
					}
					// Try creating post.
					$title   = array_shift( $text );
					$content = implode( "\n", $text );
					$posts_arr = [
						'post_title' => $title,
						'post_content' => $content,
						'post_author' => $users[0]->ID,
						'post_type' => 'post',
						'post_status' => 'draft',
					];
					$post_id = wp_insert_post( $posts_arr, true );
					if ( is_wp_error( $post_id ) ) {
						$response['attachment'] = [
							[
								'color' => 'danger',
								'title' => $post_id->get_error_code(),
								'text'  => $post_id->get_error_message(),
							],
						];
					} else {
						$response['text'] = '下書きを作成したワン！';
						$response['attachments'] = [
							[
								'color' => 'success',
								'title' => get_the_title( $post_id ),
								'title_link'  => get_edit_post_link( $post_id, 'email' ),
								'author_name' => $users[0]->display_name,
								'text' => $content,
							],
						];
					}
					return $response;
				},
				'#debug#' => function( $response, $request, $post ){
					$response['text'] = 'こんな値を受け取ったワン！';
					$response['attachments'] = [
						[
							'title' => 'POSTリクエスト',
							'text' => var_export( $request, true ),
						],
					];
					return $response;
				},
				'#help#' => function ( $response, $request, $post ) {
					$response['text'] = '`cappy xxx` で利用できる命令はこれだワン！';
					$response['attachments'] = [
						[
							'title' => 'Cappyが理解できるコマンド',
							'fields' => [
								[
									'title' => 'help',
									'value' => 'いま実行してるワン！',
									'short' => true,
								],
								[
									'title' => 'debug',
									'value' => '投稿された内容を返すワン！',
									'short' => true,
								],
								[
									'title' => 'draft',
									'value' => '投稿された内容でWordPressに下書きを作成するワン！　1行目がタイトル、2行目以降が本文に設定されるワン！',
								],
							],
						],
					];
					return $response;
				},
				'#.*#' => function( $response, $request, $post ) {
					$response['text'] = 'なにをしていいかわからなかったワン！　cappy help って入力してほしいワン！';
					return $response;
				},
			] as $regex => $callback ) {
				if ( preg_match( $regex, $command ) ) {
					$response = call_user_func_array( $callback, [ $response, $request, $post ] );
					break;
				}
			}
			break;
		default:
			// Do nothing.
			break;
	}
	return $response;
}, 10, 3 );

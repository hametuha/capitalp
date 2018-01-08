<?php
/**
 * Job board related stuff.
 */

// Add post type.
add_action( 'init', function() {
	register_post_type( 'recruitment', [
		'label'  => '求人票',
		'public' => true,
		'capability_type' => 'page',
		'menu_icon' => 'dashicons-groups',
		'supports' => [ 'title', 'editor', 'author', 'thumbnail' ],
	] );
} );

// Add menu.
add_action( 'admin_menu', function() {
	add_menu_page( 'job-board', 'ジョブボード', 'contributor', 'job-board', function() {
		?>
		<div class="wrap">
			<h2>ジョブボード</h2>
			
			<div id="job-board-container" class="jb-container">
				<p>
					<input type="text" v-model="newTitle"/>
					<button type="button" v-on:click="addNew">新規追加</button>
				</p>
				<div v-if="!recruitment.length">
					データがありません。
				</div>
				<div v-if="recruitment.length">
					<ul>
						<li v-for="item in recruitment">
							#{{item.ID}} <strong>{{item.post_title}}</strong>
							<p>
								<button type="button" v-on:click="editPost(item.ID)">編集</button>
								<button type="button" v-on:click="removePost(item.ID)">削除</button>
							</p>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
		
		<?php
	}, 'dashicons-groups', 50 );
} );

// Load JS
add_action( 'admin_enqueue_scripts', function( $page ) {
	if ( 'toplevel_page_job-board' !== $page ) {
		return;
	}
	wp_enqueue_script( 'vue-js', 'https://cdn.jsdelivr.net/npm/vue', [], 'latest', true );
	wp_enqueue_script( 'capitalp-job-board-admin', get_stylesheet_directory_uri() . '/assets/js/job-board-admin.js', [ 'jquery', 'vue-js' ], wp_get_theme()->get( 'Version' ), true );
	wp_localize_script( 'capitalp-job-board-admin', 'JobBoardVars', [
		'endpoint'  => rest_url(),
		'nonce' => wp_create_nonce( 'wp_rest' ),
	] );
} );

add_action( 'rest_api_init', function() {
	
	register_rest_route( 'job-board/v1', 'recruitment', [
		[
			'methods' => 'GET',
			'permission_callback' => function() {
				return current_user_can( 'contributor' );
			},
			'args' => [
				'page' => [
					'required' => false,
					'default'  => 1,
					'sanitize_callback' => function( $var ) {
						return max( 1, (int) $var );
					},
				],
			],
			'callback' => function( WP_REST_Request $request ) {
				$user_id = get_current_user_id();
				$posts = get_posts( [
					'author' => $user_id,
					'post_type' => 'recruitment',
					'posts_per_page' => 20,
					'post_status' => 'any',
					'paged' => $request->get_param( 'page' ),
					'orderby' => [
						'date' => 'DESC',
					],
					'suppress_filters' => false,
				] );
				return new WP_REST_Response( $posts );
			},
		],
		[
			'methods' => 'POST',
			'permission_callback' => function() {
				return current_user_can( 'contributor' );
			},
			'args' => [
				'title' => [
					'required' => true,
					'validate_callback' => function( $var ) {
						return ! empty( $var );
					},
				],
			],
			'callback' => function( WP_REST_Request $request ) {
				$title = $request->get_param( 'title' );
				$result = wp_insert_post( [
					'post_type'   => 'recruitment',
					'post_status' => 'draft',
					'post_author' => get_current_user_id(),
					'post_title'  => $title,
				], true );
				if ( is_wp_error( $result ) ) {
					return $result;
				} else {
					return new WP_REST_Response( get_post( $result ) );
				}
			}
		],
	] );
	
	register_rest_route( 'job-board/v1', 'recruitment/(?P<id>\d+)', [
		[
			'methods' => 'DELETE',
			'permission_callback' => function( WP_REST_Request $request ) {
				$post_id = $request->get_param( 'id' );
				$post = get_post( $post_id );
				if ( ! $post || ( 'recruitment' !== $post->post_type ) || ( get_current_user_id() != $post->post_author ) ) {
					return new WP_Error( 'permission_error', 'この求人票を修正する権限がありません。', [
						'status' => 403,
					] );
				}
				return true;
			},
			'args' => [
				'id' => [
					'required' => true,
					'validate_callback' => function( $var ) {
						return is_numeric( $var ) ?: new WP_Error( 'invalid_argument', 'IDは数字です。', [
							'status' => 400,
						] );
					},
				],
			],
			'callback' => function( WP_REST_Request $request ) {
				$post_id = $request->get_param( 'id' );
				$result = wp_delete_post( $post_id, true );
				if ( ! $result ) {
					return new WP_Error( 'failed_delete', '削除に失敗しました。', [
						'status' => 500,
					] );
				} else {
					return new WP_REST_Response( [
						'success' => true,
						'message' => '求人票を削除しました。',
					] );
				}
			},
		],
	] );
} );

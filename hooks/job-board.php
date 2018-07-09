<?php
/**
 * Job board realted hooks
 */

/**
 * Load TSCF
 */
if ( class_exists( 'Tarosky\\TSCF\\Bootstrap' ) ) {
	Tarosky\TSCF\Bootstrap::instance()->load_text_domain();
}

/**
 * Register post type and terms.
 */
add_action( 'init', function () {
	// Add Job board post type.
	$supports = [ 'title', 'thumbnail' ];
	if ( current_user_can( 'edit_others_posts' ) ) {
		$supports[] = 'author';
	}
	register_post_type( 'job', [
		'label'             => 'ジョブボード',
		'description'       => 'Capital Pが集めたWordPress専門の求人情報です。スポットの案件から長期雇用まで、WordPressに絞った案件をお届けします。',
		'menu_icon'         => 'dashicons-id-alt',
		'public'            => true,
		'has_archive'       => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => false,
		'capability_type'   => 'post',
		'supports'          => $supports,
	] );
	$args = [
		'label' => '申し込み',
		'public' => false,
		'show_ui' => true,
		'capability_type' => 'post',
		'show_in_menu' => 'edit.php?post_type=job',
	];
	if ( ! current_user_can( 'manage_options' ) ) {
		$args['capabilities'] = [
			'create_posts' => 'create_submissions',
		];
	}
	register_post_type( 'submission', $args );
	register_taxonomy( 'company', [ 'job' ], [
		'label'             => '企業',
		'hierarchical'      => true,
		'show_in_nav_menus' => false,
		'show_admin_column' => true,
		'meta_box_cb'       => function ( $post ) {
			$terms = get_terms( [
				'taxonomy'   => 'company',
				'hide_empty' => false,
			] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					printf(
						'<p><label class="selectit"><input value="%1$d" type="radio" name="tax_input[company][]" id="in-type-%1$d" %3$s> %2$s</label></p>',
						$term->term_id,
						esc_html( $term->name ),
						checked( has_term( $term, $term->taxonomy, $post ), true, false )
					);
				}
			} else {
				echo '<p>登録されていません。</p>';
			}
		},
	] );
	// Add job type.
	register_taxonomy( 'type', [ 'job' ], [
		'label'             => '労働形態',
		'hierarchical'      => true,
		'show_in_nav_menus' => false,
		'show_admin_column' => true,
		'meta_box_cb'       => function ( $post ) {
			$terms = get_terms( [
				'taxonomy'   => 'type',
				'hide_empty' => false,
			] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					printf(
						'<p><label class="selectit"><input value="%1$d" type="radio" name="tax_input[type][]" id="in-type-%1$d" %3$s> %2$s</label></p>',
						$term->term_id,
						esc_html( $term->name ),
						checked( has_term( $term, $term->taxonomy, $post ), true, false )
					);
				}
			} else {
				echo '<p>登録されていません。</p>';
			}
		},
	] );
	// Add ability
	register_taxonomy( 'ability', [ 'job' ], [
		'label'             => '職能',
		'hierarchical'      => true,
		'show_in_nav_menus' => false,
		'show_admin_column' => true,
	] );
	
	// Add keywords.
	register_taxonomy( 'feature', ['job'], [
		'label'             => '特徴',
		'hierarchical'      => true,
		'show_in_nav_menus' => false,
		'show_admin_column' => true,
	] );
} );


/**
 * Change enter title here.
 */
add_filter( 'enter_title_here', function( $title, $post ) {
	if ( 'job' === $post->post_type ) {
		$title = 'タイトル例・WordPressプラグイン開発者募集';
	}
	return $title;
}, 10, 2 );

/**
 * Display error messages
 */
add_action( 'edit_form_after_title', function( $post ) {
	if ( 'job' !== $post->post_type ) {
		return;
	}
	$errors = capitalp_job_errors( $post )->get_error_messages();
	if ( ! $errors ) {
		return;
	}
	printf(
		'<div class="error"><p><strong>%d件のエラーがあります！</strong></p><ol><li>%s</li></ol></div>',
		count( $errors ),
		implode( '</li><li>', array_map( 'esc_html', $errors ) )
	);
} );

/**
 * Add style
 */
add_action( 'admin_head', function() {
	echo <<<HTML
<style>
.column-job-status{
	width: 3em;
	text-align: center;
}
</style>
HTML;

} );

/**
 * Add post custom columns
 */
add_filter( 'manage_job_posts_columns', function( $columns ) {
	$new_column = [];
	foreach ( $columns as $key => $label ) {
		if ( 0 === strpos( $key, 'wpseo' ) ) {
			// Do nothing.
		} else {
			$new_column[ $key ] = $label;
		}
	}
	$new_column['job-status'] = '状態';
	return $new_column;
}, 20 );

/**
 * Render column content
 */
add_action( 'manage_job_posts_custom_column', function( $column, $post_id ) {
	switch ( $column ) {
		case 'job-status':
			if ( capitalp_job_valid( $post_id ) ) {
				$color = 'green';
				$icon  = 'yes';
			} else {
				$color = 'red';
				$icon  = 'no';
			}
			printf( '<span class="dashicons dashicons-%s" style="color: %s"></span>', $icon, $color );
			break;
		default:
			// Do nothing.
			break;
	}
}, 10, 2 );

/**
 * Fix post name
 *
 * @param string $post_link
 * @param WP_Post $post
 * @return string
 */
add_filter(
	'post_type_link', function ( $post_link, $post ) {
	if ( 'job' === $post->post_type ) {
		$post_link = home_url( 'job/' . $post->ID );
	}
	return $post_link;
}, 10, 2 );

/**
 * Add rewrite rules.
 */
add_filter(
	'rewrite_rules_array', function ( $rules ) {
	return array_merge( [
		'job/(\d+)/?$' => 'index.php?p=$matches[1]&post_type=job',
	], $rules );
} );

/**
 * Prohibit quick edit.
 */
add_filter(
	'post_row_actions', function ( $actions, $post ) {
	if ( 'job' === $post->post_type && ! current_user_can( 'edit_others_posts' ) ) {
		if ( isset( $actions[ 'inline hide-if-no-js'] ) ) {
			unset( $actions[ 'inline hide-if-no-js' ] );
		}
	}
	return $actions;
}, 10, 2 );

/**
 * Remove bulk edit
 */
add_filter( 'bulk_actions-edit-job', function( $actions ) {
	if ( isset( $actions[ 'edit' ] ) ) {
		unset( $actions[ 'edit' ] );
	}
	return $actions;
} );

/**
 * Register job board endpoint
 */
add_action( 'rest_api_init', function() {
	register_rest_route( 'capitalp/v1', 'job/(?P<job_id>\d+)', [
		[
			'methods' => 'POST',
			'args'    => [
				'job_id' => [
					'required' => true,
					'validate_callback' => function( $var ) {
						return 'job' === get_post_type( $var );
					},
				],
			],
			'permission_callback' => function() {
				return current_user_can( 'read' );
			},
			'callback' => function( WP_REST_Request $request ) {
				$job = get_post( $request->get_param( 'job_id' ) );
				if ( 'publish' !== $job->post_status || ! capitalp_job_is_open( $job ) ) {
					return new WP_Error( 'job_is_closed', 'この求人は現在募集をしていません。', [
						'status'   => 403,
						'response' => 403,
					] );
				}
				$response = capitalp_submit_job( $job, get_current_user_id() );
				return is_wp_error( $response ) ? $response : new WP_REST_Response( $response );
			},
		],
	] );
} );

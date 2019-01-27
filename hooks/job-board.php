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
		'meta_box_cb'       => 'capitalp_taxonomy_meta_box',
	] );
	// Add job type.
	register_taxonomy( 'type', [ 'job' ], [
		'label'             => '労働形態',
		'hierarchical'      => true,
		'show_in_nav_menus' => false,
		'show_admin_column' => true,
		'meta_box_cb'       => 'capitalp_taxonomy_meta_box',
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
 * Render taxonomy meta box.
 *
 * @internal
 * @param WP_Post $post
 * @param $args
 */
function capitalp_taxonomy_meta_box( $post, $args ) {
	$taxonomy = $args['args']['taxonomy'];
	$terms = get_terms( [
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
	] );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			printf(
				'<p><label class="selectit"><input value="%1$d" type="radio" name="tax_input[%4$s][]" id="in-%4$s-%1$d" %3$s> %2$s</label></p>',
				$term->term_id,
				esc_html( $term->name ),
				checked( has_term( $term, $term->taxonomy, $post ), true, false ),
				$taxonomy
			);
		}
	} else {
		echo '<p>登録されていません。</p>';
	}
}



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

// Show company field
add_action( 'company_edit_form_fields', function( $term, $taxonomy ) {
	?>
	<tr>
		<th><label for="company_url">Webサイト</label></th>
		<td>
			<?php wp_nonce_field( 'company_update', '_companynonce', false ); ?>
			<input id="company_url" name="company_url" type="url" class="regular-text" value="<?= esc_attr( get_term_meta( $term->term_id, '_url', true ) ) ?>" />
		</td>
	</tr>
	<?php
}, 10, 2 );

// Save company field.
add_action( 'edit_term', function( $term_id, $tt_id, $taxonomy ) {
	if ( ( 'company' === $taxonomy ) && isset( $_REQUEST['_companynonce'] ) && wp_verify_nonce( $_REQUEST['_companynonce'], 'company_update' ) ) {
		update_term_meta( $term_id, '_url', $_POST['company_url'] );
	}
}, 10, 3 );

// Override JSON-LD
add_filter( 'inc2734_wp_seo_json_ld', function( $json ) {
	if ( ! is_singular( 'job' ) ) {
		return $json;
	}
	// Customize JSON-LD.
	$json['@type'] = 'JobPosting';
	/** @var WP_Post $post */
	$post                = get_queried_object();
	$json['title']       = get_the_title( $post );
	$json['datePosted']  = mysql2date( DateTime::ATOM, $post->post_date );
	$json['description'] = wpautop( $post->post_excerpt );
	// Set date inforamtion.
	$expires = get_post_meta( $post->ID, '_job_expires', true );
	if ( $expires ) {
		$json['validThrough'] = $expires . 'T23:59:59+09:00';
	}
	// Requirements.
	$json['experienceRequirements'] = get_post_meta( $post->ID, '_requirements', true );
	// Set job type.
	$terms = get_the_terms( $post, 'type' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$json['employmentType'] = array_map( function( $term ) {
			return strtoupper( str_replace( '-', '_', $term->slug ) );
		}, $terms );
	}
	// Company.
	$companies = get_the_terms( $post, 'company' );
	if ( $companies && ! is_wp_error( $companies ) ) {
		foreach ( $companies as $company ) {
			$json['hiringOrganization'] = [
				'@type'  => 'Organization',
				'name'   => $company->name,
				'sameAs' => get_term_meta( $company->term_id, '_url', true ),
			];
		}
	}
	// Set salary.
	$max = get_post_meta( $post->ID, '_max_salary', true ) * 10000;
	$min = get_post_meta( $post->ID, '_min_salary', true ) * 10000;
	if ( $max && $min ) {
		// Salary.
		$salary = [
			'@type'    => 'MonetaryAmount',
			'minValue' => $min,
			'maxValue' => $max,
			'unitText' => 'YEAR',
		];
	} else {
		$salary = [
			'@type'    => 'MonetaryAmount',
			'value'    => get_post_meta( $post->ID, '_job_reward', true ),
			'unitText' => strtoupper( get_post_meta( $post->ID, '_job_reward_type', true ) ),
		];
	}
	$salary['currency']     = 'JPY';
	$json['salaryCurrency'] = 'JPY';
	$json['baseSalary']     = $salary;
	// Set address.
	$address             = [
		'@type'           => 'PostalAddress',
		'streetAddress'   => implode( ' ', array_filter( [
			get_post_meta( $post->ID, '_job_street', true ),
			get_post_meta( $post->ID, '_job_bldg', true ),
		] ) ),
		'addressLocality' => get_post_meta( $post->ID, '_job_city', true ),
		'addressRegion'   => get_post_meta( $post->ID, '_job_pref', true ),
		'postalCode'      => get_post_meta( $post->ID, '_job_zip', true ),
		'addressCountry'  => get_post_meta( $post->ID, '_job_country', true ) ?: 'JP',
	];
	$json['jobLocation'] = [
		'@type'   => 'Place',
		'address' => $address,
	];
	// Is this remote work?
	if ( has_term( 'remote-work', 'feature', $post ) ) {
		$json['jobLocationType'] = 'TELECOMMUTE';
	}
	// Skill.
	$skills = get_the_terms( $post, 'ability' );
	if ( $skills && ! is_wp_error( $skills ) ) {
		$json['skills'] = implode( ', ', array_map( function( $skill ) {
			return $skill->name;
		}, $skills ) );
	}
	$json['workHours'] = get_post_meta( $post->ID, '_work_hours', true );
	$json['industry']  = 'WordPress';
	foreach ( [ 'author', 'publisher', 'headline', 'datePublished', 'dateModified' ] as $not_needed ) {
		if ( isset( $json[ $not_needed ] ) ) {
			unset( $json[ $not_needed ] );
		}
	}
	return $json;
}, 10, 2 );

<?php
/**
 * Job board related functions
 *
 * @capitalp
 */


/**
 * Check if job is valid.
 *
 * @param null|int|WP_Post $post
 * @return bool
 */
function capitalp_job_valid( $post = null ) {
	$errors = capitalp_job_errors( $post );
	return ! $errors->get_error_messages();
}

/**
 * Get job errors.
 *
 * @param null|int|WP_Post $post
 * @return WP_Error
 */
function capitalp_job_errors( $post = null ) {
	$errors = new WP_Error();
	$post   = get_post( $post );
	// Check required fields.
	if ( ! has_excerpt( $post ) ) {
		$errors->add( 'no_excerpt', '「職種・業務内容についての説明」が入力されていません。' );
	}
	foreach ( [
		'_requirements' => '応募資格',
		'_job_number'   => '募集人数',
		'_job_place'    => '勤務地',
		'_assignee'     => '担当者名',
		'_job_expires'  => '募集終了',
	] as $key => $label ) {
		if ( '' === get_post_meta( $post->ID, $key, true ) ) {
			$errors->add( 'no_' . $key, sprintf( '「%s」が入力されていません。必須項目です。', $label ) );
		}
	}
	// Check multi fields.
	foreach ( [
		'応募先'   => [ '_tel', '_email', '_url' ],
		'報酬・年収' => [ '_min_salary', '_job_reward' ],
	] as $label => $keys ) {
		$has_value = false;
		foreach ( $keys as $key ) {
			$value = get_post_meta( $post->ID, $key, true );
			if ( $value ) {
				$has_value = true;
				break;
			}
		}
		if ( ! $has_value ) {
			$errors->add( 'invalid_values', sprintf( '「%s」が入力されていません。', $label ) );
		}
	}
	return $errors;
}

/**
 * Get post is expired.
 *
 * @param null|int|WP_Post $post
 * @return bool
 */
function capitalp_is_job_expired( $post = null ) {
	$post = get_post( $post );
	return false;
}

/**
 * Get config value
 *
 * @param null $post
 * @return string
 */
function capitalp_get_reward_type( $post = null ) {
	static $config  = null;
	$post           = get_post( $post );
	if ( is_null( $config ) ) {
		$config = json_decode( file_get_contents( Tarosky\TSCF\Utility\Parser::instance()->config_file_path() ) );
	}
	$value = tscfp( '_job_reward_type', $post );
	if ( $config ) {
		foreach ( $config as $setting ) {
			if ( 'requirements' === $setting->name ) {
				foreach ( $setting->fields as $field ) {
					if ( '_job_reward_type' === $field->name ) {
						foreach ( $field->options as $key => $label ) {
							if ( $key === $value ) {
								return $label;
							}
						}
					}
				}
			}
		}
	}
	return '';
}

/**
 * Get reward
 *
 * @param null|int|WP_Post $post
 * @return mixed|string
 */
function capitalp_job_reward( $post = null ) {
	$post   = get_post( $post );
	$reward = '';
	if ( $min_salary = tscfp( '_min_salary', $post ) ) {
		// Is this salary?
		$reward = sprintf( '年収%s万円', number_format_i18n( $min_salary ) );
		if ( $max_salary = tscfp( '_max_salary', $post ) ) {
			$reward .= sprintf( '〜%s万円', number_format_i18n( $max_salary ) );
		}
	} elseif ( $job_reward = tscfp( '_job_reward' ) ) {
		// Or reward?
		$reward = capitalp_get_reward_type( $post ) . number_format_i18n( $job_reward ) . '円';
	} else {
		$reward = '記載なし';
	}
	return $reward;
}

/**
 * Detect if user has submitted to the job.
 *
 * @param null|int|WP_Post $post
 * @param int $user_id
 * @return WP_Post|null
 */
function capitalp_get_submission( $post = null, $user_id = 0 ) {
	$post = get_post( $post );
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	foreach ( get_posts( [
		'post_type'      => 'submission',
		'post_parent'    => $post->ID,
		'author'         => $post->post_author,
		'posts_per_page' => 1,
		'meta_query'     => [
			[
				'key'   => '_job_submitter',
				'value' => $user_id,
			],
		],
	] ) as $submission ) {
		return $submission;
	}
	return null;
}

/**
 * Submit to job
 *
 * @param null|int|WP_Post $post
 * @param int              $user_id
 * @return WP_Post|WP_Error
 */
function capitalp_submit_job( $post = null, $user_id = 0 ) {
	$post = get_post( $post );
	// Check existing.
	$submission = capitalp_get_submission( $post, $user_id );
	if ( $submission && ! is_wp_error( $submission ) ) {
		return $submission;
	}
	// Create new one.
	$submission_id = wp_insert_post( [
		'post_type'   => 'submission',
		'post_parent' => $post->ID,
		'post_author' => $post->post_author,
		'post_status' => 'publish',
		'post_title'  => sprintf( '%s - %s', get_the_title( $post ), date_i18n( 'Y-m-d H:i:s' ) ),
	], true );
	if ( is_wp_error( $submission_id ) ) {
		return $submission_id;
	} else {
		update_post_meta( $submission_id, '_job_submitter', $user_id );
		return get_post( $submission_id );
	}
}

/**
 * Detect if job is open.
 *
 * @param null|int|WP_Post $post
 * @return bool
 */
function capitalp_job_is_open( $post = null ) {
	$date = tscfp( '_job_expires', $post );
	return $date >= date_i18n( 'Y-m-d' );
}

/**
 * Set title for job board.
 *
 * @return string
 */
function capitalp_job_board_title() {
	$title = [ snow_monkey_get_page_title_from_breadcrumbs() ];
	if ( is_post_type_archive( 'job' ) ) {
		array_unshift( $title, 'WordPress求人専門' );
	} elseif ( is_tax( [ 'ability', 'feature', 'type' ] ) ) {
		$term = get_queried_object();
		$taxonomy = get_taxonomy( $term->taxonomy );
		array_unshift( $title, sprintf( '%s別WordPress求人', $taxonomy->label ) );
	}
	
	return esc_html( implode( ' - ', $title ) );
}
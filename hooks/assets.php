<?php
/**
 * Asset routine
 */


/**
 * Map manifest handle (filename without extension) to the actual WP handle used in PHP.
 *
 * @return array<string, string>
 */
function capitalp_asset_handle_map() {
	return [
		'style'                    => 'capitalp',
		'login'                    => 'login-header',
		'interview'                => 'capitalp-interview',
		'tracker'                  => 'capitalp-tracker',
		'capital-marketing'        => 'capitalp-marketing',
		'capitalp-login-link'      => 'capitalp-login',
		'capitalp-interview-block' => 'capitalp-interview',
		'post-picker'              => 'cappy-post-selector',
		'job-board'                => 'capitalp-job-board',
	];
}

/**
 * Cross-package dependencies grab-deps cannot auto-detect.
 *
 * Source JS uses globals (jQuery, wp.element, etc.) instead of ES imports, so deps
 * that don't appear as named imports are augmented here. JSX runtime (react-jsx-runtime)
 * is detected by grab-deps and comes from wp-dependencies.json automatically.
 *
 * @return array<string, string[]>
 */
function capitalp_asset_extra_deps() {
	return [
		'capitalp'            => [ 'ku-mag' ],
		'capitalp-tracker'    => [ 'jquery' ],
		'capitalp-marketing'  => [ 'jquery' ],
		'capitalp-login'      => [ 'wp-element', 'wp-api-fetch', 'wp-i18n', 'cookie-tasting-heartbeat' ],
		'capitalp-contents'   => [ 'jquery-effects-highlight', 'capitalp-login' ],
		'capitalp-interview'  => [ 'wp-editor' ],
		'cappy-post-selector' => [ 'select2', 'wp-api' ],
		'capitalp-job-board'  => [ 'jquery' ],
	];
}

/**
 * Register scripts and styles from wp-dependencies.json.
 */
add_action(
	'init',
	function () {
		$manifest = get_stylesheet_directory() . '/wp-dependencies.json';
		if ( ! file_exists( $manifest ) ) {
			return;
		}
		$entries = json_decode( file_get_contents( $manifest ), true );
		if ( ! is_array( $entries ) ) {
			return;
		}

		$handle_map    = capitalp_asset_handle_map();
		$extra_deps    = capitalp_asset_extra_deps();
		$theme_version = wp_get_theme()->get( 'Version' );
		// Files read directly (not enqueued) — skip registration.
		$skip_handles = [ 'amp', 'editor-style-capitalp' ];

		foreach ( $entries as $entry ) {
			$manifest_handle = $entry['handle'] ?? '';
			if ( ! $manifest_handle || in_array( $manifest_handle, $skip_handles, true ) ) {
				continue;
			}
			$handle = $handle_map[ $manifest_handle ] ?? $manifest_handle;

			$rel_path = $entry['path'] ?? '';
			$abs_path = get_stylesheet_directory() . '/' . $rel_path;
			if ( ! $rel_path || ! file_exists( $abs_path ) ) {
				continue;
			}
			$url     = get_stylesheet_directory_uri() . '/' . $rel_path;
			$version = ! empty( $entry['hash'] ) ? $entry['hash'] : $theme_version;

			$deps = $entry['deps'] ?? [];
			if ( ! empty( $extra_deps[ $handle ] ) ) {
				$deps = array_values( array_unique( array_merge( $deps, $extra_deps[ $handle ] ) ) );
			}

			switch ( $entry['ext'] ?? '' ) {
				case 'js':
					wp_register_script(
						$handle,
						$url,
						$deps,
						$version,
						[
							'in_footer' => ! empty( $entry['footer'] ),
							'strategy'  => $entry['strategy'] ?? '',
						]
					);
					break;
				case 'css':
					wp_register_style( $handle, $url, $deps, $version, $entry['media'] ?? 'all' );
					break;
			}
		}
	}
);

// Editor Style
add_editor_style( 'assets/css/editor-style-capitalp.css' );

/**
 * Register global assets
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'capitalp' );
		wp_enqueue_script( 'capitalp-marketing' );
		wp_enqueue_script( 'capitalp-login' );
		wp_set_script_translations( 'capitalp-login', 'capitalp', get_stylesheet_directory() . '/languages' );
		if ( is_singular() ) {
			wp_enqueue_script( 'capitalp-contents' );
			wp_localize_script(
				'capitalp-contents',
				'CapitalpContents',
				[
					'postId' => get_queried_object_id(),
				]
			);
		}
	}
);

/**
 * Executed on playlist
 */
add_filter(
	'ssp_media_player',
	function ( $player, $src, $episode_id ) {
		wp_enqueue_script( 'capitalp-tracker' );
		$post        = get_post( $episode_id );
		$post_author = $post->post_author;
		$tags        = '';
		$terms       = get_the_tags( $post->ID );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$tags = implode(
				',',
				array_map(
					function ( $term ) {
						return $term->term_id;
					},
					$terms
				)
			);
		}
		$player .= sprintf(
			'<span class="capitalp-media-tracker" style="display: none;" data-src="%s" data-episode-id="%d" data-episode-author="%d" data-episode-tags="%s"></span>',
			esc_attr( $src ),
			esc_attr( $episode_id ),
			$post_author,
			esc_attr( $tags )
		);
		return $player;
	},
	10,
	3
);

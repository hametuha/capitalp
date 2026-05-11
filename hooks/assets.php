<?php
/**
 * Asset routine
 */

/**
 * Register scripts and styles from wp-dependencies.json.
 *
 * Handles and deps come from the source files' `@handle` / `@deps` annotations
 * (see src/js/*.js and src/scss/*.scss). grab-deps captures them at build time
 * and emits the manifest, so PHP just registers what's in it.
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

		$theme_version = wp_get_theme()->get( 'Version' );
		// Files read directly (not enqueued) — skip registration.
		$skip_handles = [ 'amp', 'editor-style-capitalp' ];

		foreach ( $entries as $entry ) {
			$handle = $entry['handle'] ?? '';
			if ( ! $handle || in_array( $handle, $skip_handles, true ) ) {
				continue;
			}
			$rel_path = $entry['path'] ?? '';
			$abs_path = get_stylesheet_directory() . '/' . $rel_path;
			if ( ! $rel_path || ! file_exists( $abs_path ) ) {
				continue;
			}
			$url     = get_stylesheet_directory_uri() . '/' . $rel_path;
			$version = ! empty( $entry['hash'] ) ? $entry['hash'] : $theme_version;
			$deps    = $entry['deps'] ?? [];

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

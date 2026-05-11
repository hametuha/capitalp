<?php
/**
 * Gutenberg blocks
 *
 * @package capitalp
 */

/**
 * Add block scripts.
 */
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style( 'capitalp-interview' );
		wp_enqueue_script( 'capitalp-interview' );
		$users = new WP_User_Query(
			[
				'number'      => -1,
				'count_total' => false,
				'fields'      => [ 'ID', 'display_name' ],
			]
		);
		wp_localize_script(
			'capitalp-interview',
			'CapitalpInterview',
			[
				'users' => $users->get_results(),
			]
		);
	}
);

if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'capitalp/interview',
		[
			'render_callback' => function ( $attributes, $content = '' ) {
				$attributes = wp_parse_args(
					$attributes,
					[
						'user_id' => 0,
					]
				);
				return sprintf( '[capitalp_author user_id=%d]%s[/capitalp_author]', $attributes['user_id'], $content );
			},
		]
	);
}

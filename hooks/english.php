<?php
/**
 * English post stuff
 */

// Add English posts.
add_action( 'init', function() {
	register_post_type( 'en', [
		'public' => true,
	    'label'  => 'English Posts',
	    'labels' => [
	    	'singular_name' => 'English Post',
	        'menu_name' => 'English',
	    ],
	    'menu_position' => 5,
	    'menu_icon' => 'dashicons-translation',
	    'supports'  => [ 'title', 'editor', 'thumbnails', 'author', 'revision' ],
	    'capability_type' => 'post',
	    'show_in_rest' => true,
	    'taxonomies' => [ 'category', 'post_tag' ],
	    'has_archive' => true,
	    'rewrite' => [
	    	'with_front' => false,
	    ],
	] );
} );

add_action( 'add_meta_boxes', function( $post_type ) {
	if ( 'en' === $post_type ) {
		add_meta_box( 'english', '英語設定', function( $post ) {
			// Set parent pages.
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'cappy-post-selector', get_stylesheet_directory_uri() . '/assets/js/post-picker.js', [ 'select2', 'wp-api' ], wp_get_theme()->get( 'Version' ), true );
			add_action( 'admin_footer', function() {
				?>
				<style>
					.select2-container{
						max-width: 100%;
					}
				</style>
				<?php
			} );
			?>
			<p>
			<label>
				オリジナルの日本語投稿<br />
				<select name="post_parent" class="cappy-post-picker" style="max-width: 100%;">
					<option value="0" <?php selected( ! $post->post_parent ) ?>>未設定</option>
					<?php if ( $post->post_parent ) : ?>
						<option value="<?= esc_attr( $post->post_parent ) ?>" selected>
							<?= get_the_title( $post->post_parent ) ?>
						</option>
					<?php endif; ?>
				</select>
			</label>
			</p>
			<p class="description">
				オリジナル投稿が存在する場合は設定してください。
			</p>

			<?php
		}, 'en', 'side', 'high' );
	}
} );

/**
 * Template redirect.
 */
add_action( 'template_redirect', function() {
	if ( capitalp_is_english_page() ) {
		switch_to_locale( 'en_US' );
	}
} );

/**
 * Load template for english posts.
 */
add_action( 'wp_footer', function () {
	if ( !( is_single() || is_singular() || is_page() ) ) {
		return;
	}
	get_template_part( 'template-parts/module/lang', 'switcher' );
}, 9999 );

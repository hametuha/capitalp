<?php
/**
 * Treat specific tag as feature.
 */

/**
 * Display form field.
 */
add_action( 'edit_tag_form_fields', function( WP_Term $term ) {
	if ( 'post_tag' !== $term->taxonomy ) {
		return;
	}
	?>
	<tr>
		<th><label for="tag_is_series">シリーズ</label></th>
		<td>
			<?php wp_nonce_field( 'capitalp_tag_series', '_capitalptagnonce', false ) ?>
			<label>
				<input name="tag_is_series" id="tag_is_series" type="checkbox" value="1"<?php checked( capitalp_is_series( $term ) ) ?> />
				このタグは連載です。
			</label>
		</td>
	</tr>
	<?php
} );

/**
 * Save tag is series.
 */
add_action( 'edit_term', function( $term_id, $tt_id, $taxonomy ) {
	// Only on tag.
	if ( 'post_tag' !== $taxonomy ) {
		return;
	}
	// Check nonce.
	if ( ! isset( $_REQUEST['_capitalptagnonce'] ) || ! wp_verify_nonce( $_REQUEST['_capitalptagnonce'], 'capitalp_tag_series' ) ) {
		return;
	}
	// Update tag.
	update_term_meta( $term_id, 'is_series', (int) ( isset( $_REQUEST['tag_is_series'] ) && $_REQUEST['tag_is_series'] ) );
}, 10, 3 );

/**
 * Tag name in list table.
 *
 * @param string  $name
 * @param WP_Term $tag
 * @return string
 */
add_filter( 'term_name', function( $name, $tag ) {
	if ( ! isset( $tag->taxonomy ) || 'post_tag' !== $tag->taxonomy ) {
		return $name;
	}
	if ( capitalp_is_series( $tag ) ) {
		$name .= ' (series)';
	}
	return $name;
}, 10, 2 );

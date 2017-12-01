<?php
/**
 * Customize tag meta box
 *
 * @param array $args
 * @param string $taxonomy
 *
 * @return array
 */
add_filter( 'register_taxonomy_args', function ( $args, $taxonomy ) {
	if ( 'post_tag' == $taxonomy ) {
		$args['meta_box_cb'] = function ( $post ) {
			$tag_strings = [];
			$assigned_tags = get_the_tags( $post->ID );
			if ( $assigned_tags && ! is_wp_error( $assigned_tags ) ) {
				foreach ( $assigned_tags as $tag ) {
					$tag_strings[] = $tag->name;
				}
			}
			$tags = get_tags( [ 'hide_empty' => false ] );
			?>
			<style>
				.capitalp-inline-label{
					position: relative;
					display: inline-block;
					padding: 5px;
					overflow: hidden;
				}
				.capitalp-inline-label input {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50% -50%);
					opacity: 0;
				}
				.capitalp-inline-label span {
					padding: 5px 10px;
					background: #fff;
					color: #888;
					border-radius: 10px;
					border: 1px solid #888;
					font-family: monospace;
					transition: color .3s linear, background-color .3s linear, border-color .3s linear;
				}
				.capitalp-inline-label:hover span {
					background: #fff;
					border-color: #0073aa;
					color: #0073aa;
				}
				.capitalp-inline-label input:checked + span {
					color: #fff;
					border-color: #0073aa;
					background: #0073aa;
				}
				.capitalp-tag-extra{
					display: block;
					box-sizing: border-box;
					padding: 3px;
					width: 100%;
					margin-top: 10px;
				}
				.capitalp-tag-extra + span{
					color: #888;
				}
			</style>
			<script>
				jQuery(document).ready(function($){
				  var updateTag = function() {
				    var tags = [];
				    $.each($('#capitalp-tag-extra').val().split(','), function(idex, tag){
				      tag = $.trim(tag);
				      if (tag) {
				        tags.push(tag);
					  }
					});
				    $('.capitalp-inline-label input:checked').each(function(index, input){
				      tags.push($(input).val());
					});
				    $('#capitalp-tag-input').val(tags.join(','));
				  };
				  
				  $('.capitalp-inline-label').click(function(){
				    updateTag();
				  });
				  $('.capitalp-tag-extra').on('keyup', function(){
				    updateTag();
				  });
				});
			</script>
			<input id="capitalp-tag-input" type="hidden" name="tax_input[post_tag]" value="<?= esc_attr( implode( ',', $tag_strings ) ) ?>"/>
			<?php foreach ( $tags as $tag ) : ?>
				<label class="capitalp-inline-label">
					<input type="checkbox" style=""
						   value="<?= esc_attr( $tag->name ) ?>" <?php checked( in_array( $tag->name, $tag_strings ) ) ?>/>
					<span>
						<?= esc_html( $tag->name ) ?><small>(<?= number_format( $tag->count ) ?>)</small>
					</span>
				</label>
			<?php endforeach; ?>
			<label>
				<textarea class="capitalp-tag-extra" id="capitalp-tag-extra" rows="3" placeholder="タグ1, タグ2"></textarea>
				<span class="description">新しいタグはカンマ(,)区切りで入力してください</span>
			</label>
			<?php
		};
	}
	
	return $args;
}, 10, 2 );


add_shortcode( 'advent', function( $atts, $content = '' ) {
	$atts = shortcode_atts( [
		'slug' => '',
		'year' => '',
		'month' => '',
		'from' => 1,
		'to' => 31,
		'taxonomy' => 'post_tag',
		'title' => '',
	], $atts, 'advent' );
	$term = get_term_by( 'slug', $atts['slug'], $atts['taxonomy'] );
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}
	$title = $atts['title'] ? $atts['title'] : $term->name;
	$post_list = [];
	foreach ( get_posts( [
		'post_type'   => 'post',
		'post_status' => [ 'publish', 'pending', 'future' ],
		'year' => $atts['year'],
		'monthnum' => (int) $atts['month'],
		'tax_query' => [
			[
				'taxonomy' => $atts['taxonomy'],
				'terms'     => $term->term_id,
				'field'    => 'term_id',
			],
		],
		'posts_per_page' => -1,
		'suppress_filters' => false,``
	] ) as $post ) {
		$date = mysql2date( 'Y-m-d', $post->post_date );
		if ( ! isset( $post_list[ $date ] ) ) {
			$post_list[ $date ] = [];
		}
		$post_list[ $date ][] = $post;
	}
	ob_start();
	$start_date = sprintf( '%04d-%02d-%02d', $atts['year'], $atts['month'], 1 );
	$date = new DateTime( $start_date );
	
	$end_date = (int) $date->format( 't' );
	$week_pad = (int) $date->format( 'N' );
	$padding = $week_pad - 1;
	$current_date = 1;
	$row_no = 0;
	$counter = 0;
	$weeks = [];
	while( $current_date <= $end_date ) {
		if ( $padding ) {
			$weeks[$row_no][] = '';
			$counter++;
			$padding--;
		} else {
			$flag = ( $current_date >= $atts['from'] ) && ( $current_date <= $atts['to'] );
			$weeks[$row_no][] = [ $current_date, $flag ];
			$current_date++;
			$counter++;
		}
		if ( 0 === $counter % 7 ) {
			$row_no++;
		}
	}
	// Pad last week.
	$last_row = count( $weeks ) - 1;
	if ( ( $length = count( $weeks[ $last_row ] ) ) < 7 ) {
		for ( $i = $length; $i <= 7; $i++ ) {
			$weeks[ $last_row ][] = '';
		}
	}
	?>
	<table class="capitalp-calendar">
		<capition class="capitalp-calendar-title"><?= esc_html( $title ) ?></capition>
		<thead>
			<tr>
				<th><?php _e( 'Mon' ); ?></th>
				<th><?php _e( 'Tue' ); ?></th>
				<th><?php _e( 'Wed' ); ?></th>
				<th><?php _e( 'Thu' ); ?></th>
				<th><?php _e( 'Fri' ); ?></th>
				<th><?php _e( 'Sat' ); ?></th>
				<th><?php _e( 'Sun' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $weeks as $week ) : ?>
			<tr>
				<?php foreach ( $week as $day ) : ?>
					<?php if ( ! $day ) : ?>
						<td class="capitalp-calendar-empty">&nbsp;</td>
					<?php else :
						list( $day_no, $active ) = $day;
						$class_name = $active ? 'capitalp-calendar-date' : 'capitalp-calendar-empty';
						?>
						<td class="<?= esc_attr( $class_name ) ?>">
							<span class="capitalp-calendar-no"><?= esc_html( $day_no ) ?></span>
							<?php
							$current_date_string = sprintf( '%04d-%02d-%02d', $atts['year'], $atts['month'], $day_no );
							if ( isset( $post_list[ $current_date_string ] ) && $post_list[ $current_date_string ] ) {
								// Post exists!
								foreach ( $post_list[ $current_date_string ] as $post ) {
									$author = get_the_author_meta( 'display_name', $post->post_author );
									$avatar = get_avatar( $post->post_author, 96, '', $author, [
										'class' => 'capitalp-calendar-image',
										'title' => $author,
									] );
									if ( 'publish' == $post->post_status ) {
										printf( '<a class="capitalp-calendar-link" href="%s">%s <small>%s</small></a>', get_permalink( $post ), $avatar, get_the_title( $post ) );
									} else {
										printf( '<span class="capitalp-calendar-text">%s <small>%s</small></span>', $avatar, get_the_title( $post ) );
									}
								}
							} elseif ( $active ) {
								// Post empty!
								?>
								<span class="capitalp-calendar-text">
									<img src="<?= get_stylesheet_directory_uri() ?>/assets/img/dog-blue.png" class="capitalp-calendar-image" />
									<small>投稿なし</small>
								</span>
								<?php
							} else {
								// no post but ok.
							}
							?>
							
							<?php ?>
						</td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return implode( "\n", array_filter( array_map( 'trim', explode( "\n", $content ) ) ) );
} );



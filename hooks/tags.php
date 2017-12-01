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

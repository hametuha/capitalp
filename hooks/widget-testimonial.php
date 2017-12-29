<?php

/**
 * Testimonial Widget
 *
 * @package capitalp
 */
class CapitalP_WidgetTestimonial extends WP_Widget {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct(
			'testimonial_widget',
			'Capital P: 推薦文',
			[
				'description' => '推薦文を表示します。',
			]
		);
	}

	/**
	 * Add form for widget.
	 *
	 * @param  array $instance
	 * @return string
	 */
	public function form( $instance ) {
		foreach ( [
			'title'   => [ 'タイトル', '', 'text' ],
			'count'   => [ '数', -1, 'number' ],
			'orderby' => [ '順序に使うもの', 'date', [
				'date'       => '日付',
				'menu_order' => 'ページ順序',
				'rand'       => 'ランダム',
			] ],
			'order'   => [ '順序', 'desc', [
				'desc'   => '降順',
				'asc'    => '昇順',
			] ],
				  ] as $key => $value ) {
			list( $label, $default, $type ) = $value;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $key ) ) ?>">
					<?php echo esc_html( $label ) ?>
				</label><br />
				<?php if ( is_array( $type ) ) : ?>
				<select id="<?php echo esc_attr( $this->get_field_id( $key ) ) ?>"
						name="<?php echo esc_attr( $this->get_field_name( $key ) ) ?>">
					<?php foreach ( $type as $val => $opt ) : ?>
					<option value="<?php echo esc_attr( $val ) ?>"<?php selected( $val, isset( $instance[ $key ] ) ? $instance[ $key ] : $default ) ?>>
						<?php echo esc_html( $opt ) ?>
					</option>
					<?php endforeach; ?>
				</select>
				<?php else : ?>
				<input type="<?php echo esc_attr( $type ) ?>" value="<?php echo esc_attr( isset( $instance[ $key ] ) ? $instance[ $key ] : $default ) ?>"
					   id="<?php echo esc_attr( $this->get_field_id( $key ) ) ?>"
					   name="<?php echo esc_attr( $this->get_field_name( $key ) ) ?>" />
				<?php endif; ?>
			</p>
			<?php
		}
	}
	
	/**
	 * Save.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Display widgets
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$params = wp_parse_args( $instance, [
			'title'   => '',
			'count'   => -1,
			'orderby' => 'date',
			'order'   => 'random',
		] );
		$post_arr = apply_filters( 'hametupack_testimonial_widget_args', [
			'post_type'        => 'jetpack-testimonial',
			'post_status'      => 'publish',
			'posts_per_page'   => $params['count'],
			'order'            => $params['order'],
			'orderby'          => $params['orderby'],
			'suppress_filters' => false,
		], $args, $instance );
		$testimonials = get_posts( $post_arr );
		if ( ! $testimonials ) {
			return;
		}
		echo $args['before_widget'];
		if ( $params['title'] ) {
			echo $args['before_title'] . esc_html( $params['title'] ) . $args['after_title'];
		}
		echo '<div class="widget-testimonial">';
		foreach ( $testimonials as $testimonial ) {
			?>
			<div class="widget-testimonial-item">
				<?php if ( has_post_thumbnail( $testimonial ) ) : ?>
					<?php echo get_the_post_thumbnail( $testimonial, 'post-thumbnail', [ 'class' => 'widget-testimonial-img' ] ) ?>
				<?php endif; ?>
				<blockquote class="widget-testimonial-quote">
					<?php echo wp_kses_post( wpautop( $testimonial->post_content ) ) ?>
					<cite class="widget-testimonial-cite"><?php echo wp_kses_post( $testimonial->post_title ) ?></cite>
				</blockquote>
			</div>
			<?php
		}
		echo '</div>';
		echo $args['after_widget'];
	}
}

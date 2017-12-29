<?php

/**
 * Class CapitalP_WidgetAdsence
 *
 * @package capitalp
 */
class CapitalP_WidgetAdsence extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @param string $id_base
	 * @param string $name
	 * @param array $widget_options
	 * @param array $control_options
	 */
	public function __construct(){
		parent::__construct(
			'adsense_widget',
			'Capital P: Google Adsence',
			[
				'description' => 'Google Adsenceの広告です。サイドバーの一番下とかに使ってください',
			]
		);
	}

	/**
	 * Show widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
        printf( '%sSponsored Link%s', $args['before_title'], $args['after_title'] );
        ?>
		      <div class="ad-sidebar-wrapper">
			<?php capitalp_ad( 'sidebar' ); ?>
		</div>
		<?php
		echo $args['after_widget'];

	}
}

<?php

/**
 * Register widgets
 */
add_action( 'widgets_init', function () {
	register_widget( CapitalP_WidgetAdsence::class );
	register_widget( CapitalP_WidgetTestimonial::class );
} );

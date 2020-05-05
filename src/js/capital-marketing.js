! function ( $ ) {
	'use strict';
	$( 'a[href]' ).each( function ( index, link ) {
		var href = $( link ).attr( 'href' );
		var regexp = /^https?:\/\/(akismet\.com|wordbench\.org|capitalp|hametuha|amazon|(.*\.)?wordpress\.org|itunes\.apple\.com|twitter\.com|www\.facebook.com|www\.instagram\.com|github\.com|wpdocs\.osdn\.jp|b\.hatena\.ne\.jp)/;

		if ( !regexp.test( href ) && /^https?:\/\//.test( href ) && !$( link ).parents( '.js-wp-oembed-blog-card' ).length ) {
			var parts = href.split( '?' );
			var args = {
				utm_source: 'capitalp',
				utm_campaign: 'SponsorUs',
				utm_medium: 'voluntary_link'
			};
			if ( 1 < parts.length ) {
				$.each( parts[ 1 ].split( '&' ), function ( i, param ) {
					var vars = param.split( '=' );
					args[ vars[ 0 ] ] = vars[ 1 ];
				} );
			}
			if ( /^https:\/\/(wordpress|jetpack|woocommerce)\.com/.test( href ) ) {
				args.aff = 4310;
			}
			var params = [];
			for ( var prop in args ) {
				if ( args.hasOwnProperty( prop ) ) {
					params.push( prop + '=' + args[ prop ] );
				}
			}
			parts[ 1 ] = params.join( '&' );
			$( link ).attr( 'href', parts.join( '?' ) );
		}
	} );
}( jQuery );

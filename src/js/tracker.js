/**
 * Description
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	// Track pod cast events
	$( '.podcast_player' ).each( function() {
		// Check if tracker exists.
		const $player = $( this );
		const $tracker = $player.find( '.capitalp-media-tracker' );
		if ( ! $tracker.length ) {
			return true;
		}
		const episodeId = $tracker.attr( 'data-episode-id' );
		// Save play count
		$player.find( 'audio,video' ).bind( 'play', function( e ) {
			try {
				gtag( 'event', 'audio_play', {
					episode: episodeId,
				} );
			} catch ( err ) {}
		} );
		// Save other event
		$.each( [ 'ended', 'pause', 'error', 'abort' ], function( i, eventType ) {
			$player.find( 'audio,video' ).bind( eventType, function( event ) {
				try {
					gtag( 'event', 'audio_' + eventType, {
						episode: episodeId,
					} );
				} catch ( err ) {}
			} );
		} );
	} );
} );

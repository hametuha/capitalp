/**
 * Description
 */

/*global ga:false */
/*global capitalP:false */

jQuery(document).ready(function ($) {

  "use strict";

  /**
   * Get URL
   *
   * @param {String} url
   * @returns {String}
   */
  var makeUrl = function( url ) {
    return url.replace(/^https?:\/\/[^\/]+/, '');
  };

  // Track pod cast events
  $('.podcast_player').each(function(index, player){
    // Check if tracker exists.
    var $player = $(this);
    var $tracker = $player.find('.capitalp-media-tracker');
    if ( !$tracker.length ) {
      return true;
    }
    // Add event listeners
    var played = false;
    var playCount = 0;
    var episodeId = $tracker.attr('data-episode-id');
    var episodeAuthor = $tracker.attr('data-episode-author');
    var episodeTags = $tracker.attr('data-episode-tags');
    var href = makeUrl( $tracker.attr('data-src') );
    // Save play count
    $player.find('audio,video').bind('play', function(e){
      try {
        gtag('event', 'audio_play', {
          episode: episodeId,
        });
      } catch (err) {}
    });
    // Save other event
    $.each(['ended', 'pause', 'error', 'abort'], function(i, eventType){
      $player.find('audio,video').bind(eventType, function(event){
        try {
          gtag('event', 'audio_' + eventType, {
            episode: episodeId,
          });
        } catch (err) {}
      });
    });
  });
});

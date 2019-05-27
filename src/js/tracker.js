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
      // First play
      if (!played) {
        played = true;
        try{
          window.capitalP.resetPostData();
          ga('send', {
            hitType: 'pageview',
            page: href.split('?')[0],
            location: href
          });
        }catch (err){}
      }
      try {
        var data = {
          hitType      : 'event',
          eventCategory: $(this).prop('tagName'),
          eventAction  : 'play',
          eventLabel   : episodeId,
          eventValue   : 1
        };
        ga('send', data);
      } catch (err) {}
    });
    // Save other event
    $.each(['ended', 'pause', 'error', 'abort'], function(i, eventType){
      $player.find('audio,video').bind(eventType, function(event){
        try {
          var data = {
            hitType      : 'event',
            eventCategory: $(this).prop('tagName'),
            eventAction  : eventType,
            eventLabel   : episodeId,
            eventValue   : 1
          };
          ga('send', data);
        } catch (err) {}
      });
    });
  });

  // Track download event
  $('.podcast_meta').on( 'click', 'a', function(e){
    var title = $(this).attr('title');
    var blank = '_blank' == $(this).attr('target');
    var href  = $(this).attr('href');
    try{
      var path = makeUrl(href);
      var event = {
        hitType: 'pageview',
        page: path.split('?')[0],
        location: path,
        hitCallback: function(){
          if (blank) {
            window.location.href = href;
          }
        }
      };
      window.capitalP.resetPostData();
      ga('send', event);
      if ( blank ) {
        e.preventDefault();
      }
    }catch(err){
      if ( window.console ) {
        window.console.log(err);
      }
    }
  });
});

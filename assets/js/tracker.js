jQuery(document).ready(function(r){"use strict";r(".podcast_player").each(function(t,a){var e=r(this),d=e.find(".capitalp-media-tracker");if(!d.length)return!0;var i=d.attr("data-episode-id");d.attr("data-episode-author"),d.attr("data-episode-tags"),d.attr("data-src").replace(/^https?:\/\/[^\/]+/,"");e.find("audio,video").bind("play",function(t){try{gtag("event","audio_play",{episode:i})}catch(t){}}),r.each(["ended","pause","error","abort"],function(t,a){e.find("audio,video").bind(a,function(t){try{gtag("event","audio_"+a,{episode:i})}catch(t){}})})})});
//# sourceMappingURL=map/tracker.js.map

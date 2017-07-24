/**
 * Description
 */

/*global wpApiSettings: true*/

(function ($) {
  'use strict';

  $(document).ready(function () {
    $('.cappy-post-picker').select2({
      placeholder       : "オリジナルがある場合は選択",
      ajax              : {
        url           : wpApiSettings.root + "wp/v2/posts",
        dataType      : 'json',
        delay         : 250,
        data          : function (params) {
          return {
            search  : params.term,
            orderby : 'title',
            order   : 'asc',
            _wpnonce: wpApiSettings.nonce
          };
        },
        processResults: function (data, params) {
          console.log(data, params);
          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          params.page = params.page || 1;

          return {
            results   : $.map(data, function(post){
              return {
                id: post.id,
                text: post.title.rendered
              };
            }),
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache         : true
      },
      escapeMarkup      : function (markup) {
        return markup;
      },
      minimumInputLength: 1
    });
  });
})(jQuery);

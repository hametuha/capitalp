jQuery(document).ready(function ($) {

  'use strict';

  $(document).find('a[href]').each(function(index, link){
    var href = $(this).attr('href');
    var regexp = /^https?:\/\/(capitalp|amazon|(.*\.)?wordpress\.org|twitter\.com|www\.facebook.com|instagram\.com|github\.com|wpdocs\.osdn\.jp)/;
    if ( ! href.test(regexp) && href.test(/^https?:\/\//)) {
      var parts = href.split( '?' );
      var args = [];
      if ( 1 < parts.length ) {
        $.each(parts[1].split('&'), function(i, param){
          var vars = param.split('=');
          switch(param[0]){
            case 'utm_source':
              break;
            default:
              args.push(param);
              break;
          }
        });
      }
      $.each([
        'utm_source=capitalp',
        'utm_campaign=SponsorUs',
        'utm_medium=voluntary_link'
      ], function(j, u){
        args.push(u);
      });
      parts[1] = args.join('&');
      $(this).attr('href', parts.join('?'));
    }
  });


});

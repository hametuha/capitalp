/*!
 * REST API request utility.
 *
 * wpdeps=jquery
 */

/*global CapitapRest: false*/

(function ($) {

  'use strict';

  $.extend({
    /**
     * Ajax request.
     *
     * @param {String} method
     * @param {String} endpoint
     * @param {Object} args
     * @return $.ajax
     */
    restApi: function (method, endpoint, args) {
      let url = CapitapRest.endpoint + endpoint;
      method = method.toUpperCase();
      let config = {
        method: method,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-WP-Nonce', CapitapRest.nonce);
        }
      };
      switch (method) {
        case 'POST':
        case 'PUT':
          // Add data as post body.
          config.data = args;
          break;
        default:
          // Add query string.
          let queryString = [];
          for (let prop in args) {
            if (args.hasOwnProperty(prop)) {
              queryString.push(prop + '=' + encodeURIComponent(args[prop]));
            }
          }
          if (queryString.length) {
            url += '?' + args;
          }
          break;
      }
      config.url = url;
      return $.ajax(config);
    }
  });

})(jQuery);

/*!
 * Job board related stuff.
 */

/*global Vue: false*/
/*global JobBoardVars: false*/

(function ($) {

  'use strict';

  console.log(JobBoardVars);

  /**
   * Ajax request.
   *
   * @param {String} method
   * @param {String} endpoint
   * @param {Object} args
   * @return $.ajax
   */
  var restApi = function( method, endpoint, args ) {
    var url = JobBoardVars.endpoint + endpoint;
    method = method.toUpperCase();
    var config = {
      method    : method,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', JobBoardVars.nonce);
      }
    };
    switch ( method ) {
      case 'POST':
      case 'PUSH':
        // Add data as post body.
        config.data = args;
        break;
      default:
        // Add query string.
        var queryString = [];
        for(var prop in args){
          if(args.hasOwnProperty(prop)){
            queryString.push(prop + '=' + encodeURIComponent(args[prop]));
          }
        }
        if(queryString.length){
          url += '?' + args;
        }
        break;
    }
    config.url = url;
    return $.ajax(config);
  };

  var app = new Vue({
    el: '#job-board-container',
    data: {
      recruitment: [],
      newTitle: ''
    },
    methods: {
      addNew: function(){
        var self = this;
        restApi('POST', 'job-board/v1/recruitment/', {
          title: this.newTitle
        }).done(function(response){
          self.recruitment.push(response);
          self.newTitle = '';
        }).fail(function(response){
          alert('失敗しました。');
          console.log(response);
        });
      },
      editPost: function(id){
        alert( id + 'を編集したいです');
      },
      removePost: function(id){
        if(!confirm('本当に削除してよろしいですか？')){
          return;
        }
        var self = this;
        restApi('DELETE', 'job-board/v1/recruitment/' + id, {}).done(function(){
          var index;
          for(var i = 0; i < self.recruitment.length; i++){
            if(id == self.recruitment[i].ID){
              index = i;
              break;
            }
          }
          self.recruitment.splice(index, 1);
        }).fail(function(response){
          alert('失敗しました。');
          console.log(response);
        });
      }
    },
    mounted: function () {
      var self = this;
      restApi('GET', 'job-board/v1/recruitment', {}).done(function(response){
        self.recruitment = response;
      }).fail(function(response){
        alert('error');
        console.log(response);
      });
    }
  });
})(jQuery);

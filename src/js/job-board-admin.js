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
  var restApi = function (method, endpoint, args) {
    var url = JobBoardVars.endpoint + endpoint;
    method = method.toUpperCase();
    var config = {
      method: method,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', JobBoardVars.nonce);
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
        var queryString = [];
        for (var prop in args) {
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
  };

  Vue.component('job-board-editor', {
    props: {
      post: {
        type: Object,
        required: true
      },
      newTitle: {
        type: String,
        default: ""
      },
      newContent: {
        type: String,
        default: ""
      },
    },
    methods: {
      publish: function() {
        var self = this;
        restApi( 'PUT', 'job-board/v1/recruitment/', {
          id: this.post.ID,
          status: 'publish'
        } ).done(function(response){
          self.post = response;
          self.$emit('post-changed', response);
        }).fail(function(){
          alert('エラー！');
        });

      },
      saveTitle: function(){
        var self = this;
        restApi( 'PUT', 'job-board/v1/recruitment/', {
          id: this.post.ID,
          title: this.newTitle,
          content: this.newContent
        } ).done(function(response){
          self.newTitle = '';
          self.newContent = '';
          self.post = response;
          self.$emit('post-changed', response);
        }).fail(function(){
          alert('エラー！');
        });
      },
    },
    template: '<div class="job-board-editor">' +
    '<strong>{{post.status}}</strong>' +
    '<p>{{post.post_title}}</p>' +
    '<p v-if="post.editable"><label>' +
      '<input type="text" v-model="newTitle" /><button type="button" v-on:click="saveTitle">保存</buttont>' +
    '</label></p>' +
    '<p>{{post.post_content}}</p>' +
    '<p v-if="post.editable"><label>' +
    '<textarea v-model="newContent"></textarea>' +
    '</label></p>' +
    '<button type="button" v-if="post.editable" v-on:click="publish">申請する</button>' +
    '</div>'
  });



  var app = new Vue({
    el: '#job-board-container',
    data: {
      recruitment: [],
      newTitle: '',
      post: null
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
        var post = null;
        for(var i = 0; i < this.recruitment.length; i++){
          if(id == this.recruitment[i].ID){
            post = this.recruitment[i];
            break;
          }
        }
        this.post = post;
      },
      finishEdit: function(){
        this.post = null;
      },
      postChangeHandler: function(post){
        for(var i = 0; i < this.recruitment.length; i++){
          if(this.recruitment[i].ID == post.ID){
            this.recruitment[i] = post;
            break;
          }
        }
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

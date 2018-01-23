/*!
 * Description
 *
 * wpdeps=capitalp-rest-api,vue-js
 */

/*global Vue: false*/

Vue.component('job-board-editor', {
  props: {
    post: {
      type: Object,
      required: true
    },
    newTitle: {
      type: String,
      default: ''
    },
    newContent: {
      type: String,
      default: ""
    },
  },
  methods: {
    publish: function() {
      let self = this;
      $.restApi( 'PUT', 'job-board/v1/recruitment/', {
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
      let self = this;
      $.restApi( 'PUT', 'job-board/v1/recruitment/', {
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
    }
  },
  template: `
    <div class="job-board-editor">
        <strong>{{post.status}}</strong>
        <p>{{post.post_title}}</p>
        <p v-if="post.editable">
            <label>
                <input type="text" v-model="newTitle" />
                <button type="button" v-on:click="saveTitle">保存</buttont>
            </label>
        </p>
        <p>{{post.post_content}}</p>
        <p v-if="post.editable">
            <label>
                <textarea v-model="newContent"></textarea>
            </label>
        </p>
        <button type="button" v-if="post.editable" v-on:click="publish">申請する</button>
    </div>
    `

});

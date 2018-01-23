/*!
 * Description
 *
 * wpdeps=capitalp-job-board-editor,capitalp-rest-api,vue-js
 */

/*global Vue: false*/

Vue.component('job-board-container', {
  data: function(){
    return {
      recruitment: [],
      newTitle: '',
      post: null
    };
  },
  template: `
  <div class="jb-container">
    <transition name="toggle">
        <div v-if="!post">
            <p>
                <input type="text" v-model="newTitle"/>
                <button type="button" v-on:click="addNew">新規追加</button>
            </p>
            <div v-if="!recruitment.length">
                データがありません。
            </div>
            <div v-if="recruitment.length">
                <ul>
                    <li v-for="item in recruitment">
                        #{{item.ID}} <strong>{{item.post_title}}</strong>
                        <p>
                            <button type="button" v-on:click="editPost(item.ID)">編集</button>
                            <button type="button" v-on:click="removePost(item.ID)">削除</button>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </transition>

    <transition name="toggle">
        <div v-if="post">
            <button type="button" v-on:click="finishEdit">編集終了</button>
            <job-board-editor :post="post" v-on:post-changed="postChangeHandler"></job-board-editor>
        </div>
    </transition>
  </div>
  `,
  methods: {
    addNew: function(){
      let self = this;
      $.restApi('POST', 'job-board/v1/recruitment/', {
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
      let post = null;
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
      let self = this;
      $.restApi('DELETE', 'job-board/v1/recruitment/' + id, {}).done(function(){
        let index;
        for(let i = 0; i < self.recruitment.length; i++){
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
    let self = this;
    $.restApi('GET', 'job-board/v1/recruitment', {}).done(function(response){
      self.recruitment = response;
    }).fail(function(response){
      alert('error');
      console.log(response);
    });
  }

});

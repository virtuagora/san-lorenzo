<template>
  <div>
    <h1 class="title is-4">Leave a comment!</h1>
    <article class="media">
      <figure class="media-left is-rounded">
        <p class="image is-64x64">
          <img src="assets/img/avatar.jpg" />
        </p>
      </figure>
      <div class="media-content" v-if="enabled">
        <div class="field" v-if="!sent">
          <textarea-autosize placeholder="Type something here..." ref="someName" v-model="someValue" :min-height="10" :max-height="350" @blur.native="onBlurTextarea"></textarea-autosize>
          <div class="level">
            <div class="level-right">
              <a @click="sendComment()" class="level-item button is-primary is-small">Enviar</a>
              <p class="level-item has-text-danger" v-show="emptyMessage"><small>You have to writte a message!</small></p>
            </div>
          </div>
        </div>
        <div class="notification is-info" v-else-if="sent && successMessage != ''">
          <span class="icon"><i class="mdi mdi-checkbox-marked-circle-outline"></i></span>{{successMessage}}
        </div>
        <div class="notification is-danger" v-else-if="sent && errorMessage != ''">
          {{errorMessage}}
        </div>
      </div>
      <div class="media-content" v-else>
        <div class="notification">
          <span class="icon"><i class="mdi mdi-checkbox-marked-circle-outline"></i></span>The comment session is disabled.
        </div>
      </div>
    </article>
  </div>
</template>

<script>
export default {
  name: "node-comment",
  props: {
     enabled:{type: String, required: true},
     enabled:{type: Boolean, required: false, default: true}
  },
  data() {
    return {
      sent: false,
      errorMessage: '',
      successMessage: '',
      someValue: "",
      loading: false,
      emptyMessage: false
    };
  },
  methods: {
    sendComment: function(){
      if(this.someValue != ''){
        this.loading = true
        // TODO Send comment trough API
        this.sent = true
        this.successMessage = 'Comment sent!'
      } else {
        this.emptyMessage = true
      }
    }
  }
};
</script>

<style lang="scss" scoped>
textarea {
  border: none;
  background-color: #e9e9e9;
  border-radius: 5px;
  padding: 8px;
  font-size: 0.9rem;
  width: 100%;
  margin-bottom: 10px;
}
</style>
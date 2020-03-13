<template>
  <article class="media">
   <Avatar :user="comment.author" class="media-left" size="48"></Avatar>
    <div class="media-content">
      <div class="content">
          <a :href="'/usuario/'+comment.author.id" class="has-text-black"><strong>{{comment.author.subject.display_name}}</strong></a> · <span class="is-size-7"><i>Publicado el {{(new Date(comment.created_at)).toLocaleDateString('es-AR')}}</i></span>
          <p class="nl2br" style="margin-bottom:0;">{{comment.content}}</p>
          <small>
            <like :show-text="true" :count="comment.votes" :like-url="likeUrl.replace(':com', this.comment.id)" ></like> ·
            <a @click="showReplyBox = !showReplyBox"><i class="fas fa-reply fa-lg fa-fw"></i> Responder</a></small>
      </div>
      <reply v-if="showReplyBox" :reply-url="replyUrl" @updateComments="updateComments"></reply>
      <reply-template v-for="reply in comment.replies" :key="reply.id" :comment="reply" :like-url="likeUrl"></reply-template>
    </div>
  </article>
</template>

<script>
import Like from "./Like";
import Reply from "./Reply";
import ReplyTemplate from "./ReplyTemplate";
import Avatar from "../../utils/Avatar"
export default {
  props: ["comment", "likeUrl", "replyUrl"],
  components: {
    Like,
    Reply,
    ReplyTemplate,
    Avatar
  },
  data(){
    return{
      showReplyBox: false
    }
  },
  methods: {
    updateComments: function(){
      this.$emit('updateComments')
    }
  }
};
</script>

<style>

</style>

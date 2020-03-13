<template>
  <article class="media">
    <Avatar :user="comment.author" class="media-left" size="24"></Avatar>
    <div class="media-content">
      <div class="content">
        <p class="">
          <a :href="'/usuario/'+comment.author.id" class="has-text-black"><strong>{{comment.author.subject.display_name}}</strong></a> · {{comment.content}} · <span class="is-size-7"><i>{{(new Date(comment.created_at)).toLocaleDateString('es-AR')}}</i></span>
        </p>
      </div>
    </div>
    <div class="media-right">
      <like :show-text="false" :count="comment.votes" :like-url="likeUrl.replace(':com', this.comment.id)" ></like>
    </div>
  </article>
</template>

<script>
import Like from "./Like";
import Avatar from "../../utils/Avatar"

export default {
  props: ['comment', 'likeUrl'],
  components: {
      Like,
      Avatar
    }
  }
</script>

<style>

</style>

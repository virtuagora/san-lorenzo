<template>
  <section>
    <h1 class="title is-3 has-text-centered">
      <i class="fa fa-comments fa-fw"></i> Comentarios</h1>
    <h1 class="subtitle is-4 has-text-centered">
      ¡Dejales uno!
    </h1>
    <article class="media" v-if="user != null && !response.ok">
      <Avatar :user="user" class="media-left" size="64" />
      <div class="media-content">
        <div class="field">
          <p class="control">
            <b-input v-model="comment" data-vv-name="comment" data-vv-as="'Comentario'" v-validate="'required|min:1|max:500'" type="textarea" minlength="10" maxlength="500" rows="2" :disabled="sendingComment" placeholder="Escriba aquí el comentario...">
            </b-input>
          </p>
        </div>
        <div class="field">
          <p class="control">
            <button @click="submitComment()" class="button is-primary is-pulled-right" :disabled="sendingComment || comment == ''" :class="{'is-loading': sendingComment}">Enviar comentario</button>
          </p>
        </div>
      </div>
    </article>
    <div class="notification is-info has-text-centered" v-else-if="user == null">
      Para dejar tu comentario, tenés que
      <a href="/login">
        <i class="fas fa-sign-in-alt fa-fw fa-lg"></i> Iniciar sesión</a>!
    </div>
    <div class="notification is-success has-text-centered" v-else-if="response.ok">
      <i class="fas fa-check fa-fw fa-lg"></i> Tu comentario ha sido enviado
    </div>
    <br>
    <div v-if="isLoading" class="notification">
      <br>
      <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
      <br>
    </div>
    <div v-else>
      <comment v-for="comment in comments" :comment="comment" :key="comment.id" :reply-url="replyUrl.replace(':com', comment.id)" :like-url="likeUrl" @updateComments="updateComments"></comment>
      <infinite-loading ref="infiniteLoading" @infinite="infiniteHandler">
        <div class="box has-text-centered" v-if="comments.length == 0" style="margin-top:15px;" slot="no-results">
          :(
          <h1 class="subtitle is-5 is-marginless">
            ¡No han hecho comentarios!
          </h1>
          ¡Dejales el tuyo para comenzar!
          <i class="em em-smiley"></i>
        </div>
        <span slot="no-more">
          <i class="fas fa-info-circle"></i> ¡Fín de los comentarios!
        </span>
      </infinite-loading>
    </div>
  </section>
</template>

<script>
import Comment from "./comment/CommentTemplate";
import Avatar from "../utils/Avatar";
import InfiniteLoading from "vue-infinite-loading";

export default {
  props: ["commentsUrl", "commentUrl", "replyUrl", "likeUrl"],
  data() {
    return {
      user: null,
      comment: "",
      comments: [],
      isLoading: false,
      sendingComment: false,
      response: {
        ok: false
      },
      paginator: {
        current_page: null,
        last_page: null,
        next_page_url: null,
        prev_page_url: null
      }
    };
  },
  components: {
    Comment,
    Avatar,
    InfiniteLoading
  },
  created: function() {
    this.user = this.$store.state.user;
  },
  methods: {
    getComments: function() {
      this.isLoading = true;
      this.$http
        .get(this.commentsUrl)
        .then(response => {
          // this.comments = this.comments.concat(response.data.data);
          this.comments = response.data.data;
          this.isLoading = false;
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error al obtener los comentarios. Recargue la página",
            type: "is-danger",
            actionText: "Cerrar"
          });
        });
    },
    resetEverything: function() {
      this.comments = [];
      this.paginator.current_page = null;
      this.paginator.last_page = null;
      this.paginator.next_page_url = null;
      this.paginator.prev_page_url = null;
      this.$nextTick(() => {
        this.$refs.infiniteLoading.$emit("$InfiniteLoading:reset");
      });
    },
    fillPaginator: function(data) {
      this.paginator.current_page = data.current_page;
      this.paginator.last_page = data.last_page;
      this.paginator.next_page_url = data.next_page_url;
      this.paginator.prev_page_url = data.prev_page_url;
    },
    infiniteHandler: function($state) {
      if (this.paginator.current_page == null) {
        this.$http
          .get(this.commentsUrl)
          .then(response => {
            if (response.data.data === undefined)
              throw { message: "Error en query" };
            this.comments = this.comments.concat(response.data.data);
            this.fillPaginator(response.data);
            $state.loaded();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message: "Error al obtener los comentarios",
              type: "is-danger",
              actionText: "Cerrar"
            });
            $state.complete();
          });
      } else if (this.paginator.next_page_url) {
        this.$http
          .get(this.paginator.next_page_url)
          .then(response => {
            if (response.data.data === undefined)
              throw { message: "Error en query" };
            this.comments = this.comments.concat(response.data.data);
            this.fillPaginator(response.data);
            $state.loaded();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message: "Error al obtener los comentarios",
              type: "is-danger",
              actionText: "Cerrar"
            });
            $state.complete();
          });
      } else {
        $state.complete();
      }
    },
    updateComments: function() {
      this.resetEverything();
    },
    submitComment: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error. Verifique los campos.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          console.log("sending");
          this.sendingComment = true;
          this.$http
            .post(this.commentUrl, this.payload)
            .then(response => {
              this.response.ok = true;
              this.$snackbar.open({
                message: "¡Tu comentario ha sido enviado!",
                type: "is-success",
                actionText: "Genial!"
              });
              this.getComments();
            })
            .catch(error => {
              this.$snackbar.open({
                message: "Error inesperado. No se pudo enviar el comentario.",
                type: "is-danger",
                actionText: "Reintentar"
              });
              this.sendingComment = false;
            });
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  },
  computed: {
    payload: function() {
      return {
        content: this.comment
      };
    }
  }
};
</script>

<style>

</style>

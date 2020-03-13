<template>
<section>

  <article class="media" v-if="user !== null && !response.ok">
    <Avatar :user="user" class="media-left" size="48" />
    <div class="media-content">
      <div class="field">
        <p class="control">
          <b-input v-model="comment" data-vv-name="comment" data-vv-as="'Respuesta'" v-validate="'required|min:1|max:500'" type="textarea" minlength="10" maxlength="500" rows="2" :disabled="sendingComment" placeholder="Escriba aquí la respuesta...">
          </b-input>
        </p>
      </div>
    </div>
    <div class="media-right">
      <button @click="submitReply()" class="button is-primary is-pulled-right" :disabled="sendingComment || comment == ''" :class="{'is-loading': sendingComment}">
        <span class="icon">
          <i class="fas fa-paper-plane"></i>
        </span>
      </button>
    </div>
  </article>
  <div class="notification is-info has-text-centered" v-else-if="user === null" style="padding: 5px;">
    Para dejar tu respuesta, tenés que
    <a href="/login">
      <i class="fas fa-sign-in-alt fa-fw fa-lg"></i>¡Iniciar sesión</a>!
  </div>
</section>
</template>

<script>
import Avatar from "../../utils/Avatar";
export default {
  props: ["replyUrl"],
  data() {
    return {
      user: null,
      comment: "",
      sendingComment: false,
      response: {
        ok: false
      },
      isLoading: false
    };
  },
  components: {
    Avatar
  },
  created: function() {
    this.user = this.$store.state.user;
  },
  methods: {
    submitReply: function() {
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
            .post(this.replyUrl, this.payload)
            .then(response => {
              this.response.ok = true;
              this.$emit('updateComments')
              this.$snackbar.open({
              message: "Tu respuesta ha sido enviada!",
              type: "is-success",
              actionText: "Genial!"
            });
            })
            .catch(error => {
              this.$snackbar.open({
                message: "Error inesperado. No se pudo enviar la respuesta.",
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

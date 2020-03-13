<template>
  <span>
    <a @click="sendLike" class="has-text-dark" v-show="!sent">
      <!-- <i class="far fa-heart fa-lg fa-fw" :class="{'animated bounceIn': lockLike}"></i> -->
      <img src="/assets/img/no-clap.svg" class="image is-inline" style="vertical-align: text-bottom; width:20px">
      ({{counter}})
      <span v-show="showText"> Aplaudir</span>
    </a>
    <a @click="deleteLike" class="has-text-link" v-show="sent">
      <!-- <i class="fas fa-heart fa-lg fa-fw" :class="{'animated bounceIn': lockLike}"></i> -->
      <img src="/assets/img/clap.svg" class="image is-inline" :class="{'animated bounceIn': lockLike}" style="vertical-align: text-bottom; width:20px">      
      ({{counter}})
      <span v-show="showText"> ¡Gracias!</span>
    </a>
    <b-modal :active.sync="showLogin" :width="640" scroll="keep">
      <div class="box has-text-centered">
        <i class="fas fa-heart fa-5x has-text-danger animated tada"></i>
        <h2 class="title is-600 is-size-4">Entrá en Ingenia+Virtuágora para poder participar
        </h2>
        <p>Vas a poder bancar los proyectos y dejar comentarios, ¡hasta podrias llegar a ser parte de un equipo!</p>
        <br>
              <a href="/login" class="button is-warning is-medium">
                <i class="fas fa-sign-in-alt fa-fw"></i>&nbsp;Iniciar sesión</a>
      </div>
    </b-modal>
  </span>
</template> 

<script>
export default {
  props: ["showText", "count", "likeUrl"],
  data() {
    return {
      sent: false,
      lockLike: false,
      counter: this.count,
      showLogin: false
    };
  },
  methods: {
    sendLike: function() {
      if (this.$store.state.user === null) {
        this.showLogin = true;
        return;
      }
      if (this.lockLike) {
        this.cooldown();
        return;
      }
      this.sent = true;
      this.lockLike = true;
       this.$http
        .post(this.likeUrl, {value: 1})
        .then(response => {
          this.sent = true;
          this.lockLike = true;
          this.counter = this.counter + 1
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado. No se pudo guardar tu aplauso.",
            type: "is-danger",
            actionText: "Reintentar"
          });
          this.sendingComment = false;
        });
      this.$snackbar.open({
        message: "¡Aplauso enviado!",
        type: "is-warning",
        actionText: "Genial!"
      });
    },
    deleteLike: function() {
      if (this.$store.state.user === null) {
        this.showLogin = true;
        return;
      }
      if (this.lockLike) {
        this.cooldown();
        return;
      }
      this.vote = false;
      this.lockLike = true;
      this.counter = this.counter - 1;
      this.$snackbar.open({
        message: "Me gusta eliminado",
        type: "is-warning",
        actionText: "Genial!"
      });
    },
    cooldown: function() {
      this.$snackbar.open({
        message:
          "¡Despacio! Vas a poder volver a hacerlo cuando recargues la página.",
        type: "is-warning",
        actionText: "Ok"
      });
    }
  }
};
</script>

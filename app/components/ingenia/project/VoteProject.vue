<template>
  <div class="has-text-centered-desktop-only ">
    <div class="columns is-mobile">
      <div class="column is-narrow">
        <b-tooltip label="Clic para dejar de bancar..." v-show="vote" position="is-left" type="is-white">
          <a @click="deleteVote()">
            <img src="/assets/img/ribbon-bancando.svg" class="ribbon-bancar" />
          </a>
        </b-tooltip>
        <b-tooltip label="¡Clic para bancar!" v-show="!vote" position="is-left" type="is-white">
          <a @click="sendVote()" @mouseover="isHovering = true" @mouseout="isHovering = false">
            <img src="/assets/img/ribbon-bancar.svg" class="ribbon-bancar" :class="{'animated rubberBand': isHovering}" style="opacity: 0.4" />
          </a>
        </b-tooltip>
      </div>
      <div class="column">
        <!-- <div class="list-of-avatars">
          <b-tooltip label="Guillermo Croppi">
            <figure class="image is-32x32 is-rounded">
              <a href="#">
                <img alt="Placeholder image" src="https://pbs.twimg.com/profile_images/908495954524983297/nSinJTho_400x400.jpg">
              </a>
            </figure>
          </b-tooltip>
          <b-tooltip label="Guillermo Croppi">
            <figure class="image is-32x32 is-rounded">
              <a href="#">
                <img alt="Placeholder image" src="https://pbs.twimg.com/profile_images/908111377495199745/NSSDkikD_400x400.jpg">
              </a>
            </figure>
          </b-tooltip>
          <b-tooltip label="Guillermo Croppi">
            <figure class="image is-32x32 is-rounded">
              <a href="#">
                <img alt="Placeholder image" src="https://pbs.twimg.com/profile_images/802586078620385280/A41MfrN4_400x400.jpg">
              </a>
            </figure>
          </b-tooltip>
          <b-tooltip label="Guillermo Croppi">
            <figure class="image is-32x32 is-rounded">
              <a href="#">
                <img alt="Placeholder image" src="https://pbs.twimg.com/profile_images/932316632369778688/XspSB7QY_400x400.jpg">
              </a>
            </figure>
          </b-tooltip>
        </div> -->
        <p class="title is-600 is-size-4-touch is-size-3-fullhd is-marginless has-text-warning"> {{counter}} personas</p>
        <p>bancan este proyecto
          <span v-show="!vote">
            <br>
            <b>¿Y vos?</b>
            <i class="em em-open_mouth"></i>
          </span>
        </p>
        <p class="has-text-warning" v-show="vote">
          <b>¡Vos tambien!</b>
          <i class="em em-smiley"></i>
        </p>
      </div>
    </div>
    <b-modal :active.sync="showThanks" :width="640" scroll="keep">
      <div class="box has-text-centered">
        <img src="/assets/img/muscle-color.svg" class="ribbon-bancar animated jackInTheBox" />
        <h2 class="title is-600 is-size-4-touch is-size-3-fullhd is-marginless has-text-link">¡Gracias por bancarnos!
          <i class="em em-smiley"></i>
          <i class="em em-heart"></i>
        </h2>
        <p>¡También podes bancar nuestro proyecto compartiendo en las redes sociales!</p>
        <br>
        <p class="has-text-link">
          <a href="javascript:shareOnFacebook()">
            <i class="fab fa-facebook fa-3x fa-fw"></i>
          </a>
          <a :href="'https://twitter.com/intent/tweet?text=¡Estamos participando de INGENIA y podes ayudarnos! ¡Entra en nuestro proyecto, registrate y bancanos con tú voto!&url=' + getLocation + '&hashtags=INGENIA,hayEquipo!'">
            <i class="fab fa-twitter fa-3x fa-fw"></i>
          </a>
          <a :href="'whatsapp://send?text=¡Estamos participando de INGENIA! ¡Y podes ayudarnos! ¡Entrá en nuestro proyecto, registrate y bancanos con tú voto! Ingresá entrando a ' + getLocation">
            <i class="fab fa-whatsapp fa-3x fa-fw"></i>
          </a>
        </p>
        <br>
      </div>
    </b-modal>
    <b-modal :active.sync="showLogin" :width="640" scroll="keep">
      <div class="box has-text-centered">
        <i class="fas fa-heart fa-5x has-text-danger animated tada"></i>
        <h2 class="title is-600 is-size-4 has-text-dark">Entrá en Ingenia+Virtuágora para poder participar
        </h2>
        <p>Vas a poder bancar los proyectos y dejar comentarios, ¡hasta podrias llegar a ser parte de un equipo!</p>
        <br>
        <a href="/login" class="button is-warning is-medium">
          <i class="fas fa-sign-in-alt fa-fw"></i>&nbsp;Iniciar sesión</a>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  props: ["voted", "voteUrl", "likes"],
  data() {
    return {
      vote: false,
      lockVoting: false,
      showThanks: false,
      showLogin: false,
      isHovering: false,
      counter: this.likes
    };
  },
  created: function() {
    this.vote = this.voted ? true : false;
  },
  methods: {
    sendVote: function() {
      if (this.$store.state.user === null) {
        this.showLogin = true;
        return;
      }
      if (this.lockVoting) {
        this.cooldown();
        return;
      }
      this.vote = true;
      this.showThanks = true;
      this.lockVoting = true;
      this.$http
        .post(this.voteUrl)
        .then(response => {
          this.counter = this.counter + 1
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado. ¡No se pudo enviar el voto!",
            type: "is-danger",
            actionText: "Reintentar"
          });
          this.vote = false;
          this.lockVoting = false;
        });
    },
    deleteVote: function() {
      if (this.$store.state.user === null) {
        this.showLogin = true;
        return;
      }
      if (this.lockVoting) {
        this.cooldown();
        return;
      }
      this.vote = false;
      this.lockVoting = true;
      this.$http
        .post(this.voteUrl)
        .then(response => {
          this.counter = this.counter - 1
          this.$snackbar.open({
            message: "Ya no bancas este proyecto",
            type: "is-success",
            actionText: "OK"
          });
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado. ¡No se pudo enviar el voto!",
            type: "is-danger",
            actionText: "Reintentar"
          });
          this.vote = true;
          this.lockVoting = false;
        });
    },
    cooldown: function() {
      this.$snackbar.open({
        message:
          "Recien enviaste tu voto. Vas a poder volver a votar recargando la página.",
        type: "is-warning",
        actionText: "Ok"
      });
    }
  },
  computed: {
    getLocation: function() {
      return window.location.href;
    }
  }
};
</script>

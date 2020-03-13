<template>
  <section class="has-text-centered">
    <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt="">
    <div class="notification is-warning has-text-centered">
      <p>
        <i class="fas fa-exclamation-triangle fa-lg"></i>
      </p>
      <p>Ocurrió un error durante el envio del voto o el servidor respondio con un error</p>
    </div>
    <div class="content">
<p>Para poder asegurar la consistencia de los datos, le pedimos que vuelva a comenzar el proceso de votación</p>
    </div>
    <div class="buttons is-centered">
      <button @click="restart" class="button is-primary is-medium is-rounded">
          <i class="fas fa-reply fa-fw"></i>&nbsp;Volver a comenzar el proceso
        </button>
    </div>
    <p class="is-size-7">Si el problema persiste o si queres reportar un problema, contactate con nosotros en nuestro <b><a href="https://www.facebook.com/PresupuestoParticipativoSanLorenzo/">Facebook</a></b> o enviando un email a <u><a href="mailto:presupuestoparticipativo.sl@gmail.com">presupuestoparticipativo.sl@gmail.com</a></u></p> 
    </section>
</template>

<script>
export default {
  data() {
    return {
    };
  },
  methods: {
    restart: function() {
      this.$store.commit('restartPublicVote')
      this.$router.push({ name: "PublicTutorial" });
    }
  }
};
</script>

<style>
</style>


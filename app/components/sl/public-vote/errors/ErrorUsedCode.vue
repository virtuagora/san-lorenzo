<template>
  <section class="has-text-centered">
    <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt="">
    <div class="notification is-warning has-text-centered">
      <p>
        <i class="fas fa-exclamation-triangle fa-lg"></i>
      </p>
      <p>El codigo ya ha sido usado</p>
    </div>
    <div class="content">
<p>Usted ya ha votado en el Presupuesto de San Lorenzo</p>
    <p class="">Si usted cree que esto es un error, contactatese con el equipo del Presupuesto Participativo, yendo a las mesas de votación, o en nuestro <b><a href="https://www.facebook.com/PresupuestoParticipativoSanLorenzo/">Facebook</a></b> o enviando un email a <u><a href="mailto:presupuestoparticipativo.sl@gmail.com">presupuestoparticipativo.sl@gmail.com</a></u></p> 
    </div>
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


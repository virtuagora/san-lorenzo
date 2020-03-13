<template>
  <section class="has-text-centered">
    <i class="fas fa-paper-plane fa-5x has-text-primary fa-fw animated rubberBand infinite"></i>
    <br>
    <br>
    <h2 class="title is-4">Enviando su voto...</h2>
    <h2 class="subtitle is-7">Aguarde unos segundos, usted será redirigido a la página de confirmación</h2>
    <form ref="sendingForm" :action="urlVote" method="POST">
      <input type="hidden" name="comunitarios" :value="payloadComunitarios" />
      <input type="hidden" name="institucionales" :value="payloadInstitucionales"/>
    </form>
  </section>
</template>

<script>
export default {
  props: ['urlVote'],
  mounted: function(){
    setTimeout(() => {
      this.$refs.sendingForm.submit();
    }, 1000);
  },
  computed: {
    voteComunitarios: function() {
      return this.$store.state.voteComunitarios;
    },
    voteInstitucionales: function() {
      return this.$store.state.voteInstitucionales;
    },
    payloadComunitarios: function(){
      return this.voteComunitarios.map(pro => {
        return pro.id
      }).join('&&')
    },
    payloadInstitucionales: function(){
      return this.voteInstitucionales.map(pro => {
        return pro.id
      }).join('&&')
    }
  }
}
</script>

<style>

</style>

<template>
  <section class="has-text-centered">
     <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt="">
    <div class="notification is-success"><i class="fas fa-check"></i>&nbsp;¡Tu voto ha sigo enviado de forma segura y privada!</div>
    <div class="content">
    <p>¡Gracias por haber participado del presupuesto participativo de San Lorenzo!</p>
    </div>
    <div class="buttons is-centered">
      <a href="/" v-if="goHome" class="button is-primary is-rounded is-medium"><i class="fas fa-home"></i>&nbsp;Ir a la página principal</a>
      <button @click="startAgain" v-else class="button is-primary is-rounded is-large fa-flip-horizontal"><i class="fas fa-redo"></i></button>
    </div>
  </section>
</template>

<script>
export default {
  data(){
    return {
      goHome: false
    }
  },
  mounted: function(){
    this.goHome = this.codeOnQuery
    this.$store.commit('resetPublicVote')
  },
  methods: {
    startAgain: function(){
      this.$router.push({name: 'PublicEnterCode'})
    }
  },
  computed: {
    codeOnQuery: function() {
      return this.$store.state.codeOnQuery;
    },
  }
}
</script>

<style>

</style>

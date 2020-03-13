<template>
  <section class="has-text-centered">
    <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt="">
    <h1 class="title is-2">¡Hola!</h1>
    <p>¡Gracias por participar del presupuesto participativo de San Lorenzo! Antes de comenzar, vamos a explicarte como funciona el proceso de votación.</p>
    <br>
    <button v-on:click.prevent="$router.push({ name: 'PrivateTutorial'})" class="button is-primary is-rounded is-medium">
      Continuar&nbsp;
      <i class="fas fa-arrow-right fa-fw "></i>
    </button>
  </section>
</template>

<script>
export default {
  
};
</script>

<style scoped>
section {
  width: 100%;
}
</style>

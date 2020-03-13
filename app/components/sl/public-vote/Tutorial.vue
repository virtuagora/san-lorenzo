<template>
  <section class="">
    <img src="/assets/img/pp-logo-horizontal.svg" class="image logo-small-pp-voting is-pulled-right" alt="">
    <h1 class="title is-3">Tutorial</h1>
    <hr>
    <article class="media">
      <div class="media-left">
        <h1 class="title is-2 is-marginless">1.</h1>
      </div>
      <div class="media-content">
        Primero se votan los proyectos <b>comunitarios</b>. Podés elegir <u>hasta</u> 3 proyectos comunitarios.
      </div>
    </article>
     <article class="media">
      <div class="media-left">
        <h1 class="title is-2 is-marginless">2.</h1>
      </div>
      <div class="media-content">
        Luego se votan los proyectos <b>institucionales</b>. Podés elegir <u>hasta</u> 3 proyectos institucionales.
      </div>
    </article>
     <article class="media">
      <div class="media-left">
        <h1 class="title is-2 is-marginless">3.</h1>
      </div>
      <div class="media-content">
       Vas a tener una oportunidad de poder revisar los proyectos elegidos. <i class="fas fa-exclamation-circle fa-fw"></i>&nbsp;Si cometiste un error o si queres hacer algún cambio, vas a poder volver atras y corregir tu elección
      </div>
    </article>
    <article class="media">
      <div class="media-left">
        <h1 class="title is-2 is-marginless">4.</h1>
      </div>
      <div class="media-content">
       Una vez que estes conforme con tu elección, falta solo hacer clic en el botón <span class="tag is-primary">¡Enviar!</span>
      </div>
    </article>
    <br>
    <div class="is-clearfix">
     <div class="is-clearfix">
    <button v-on:click.prevent="$router.push({ name: 'PublicStep1'})" class="button is-primary is-rounded is-medium is-pulled-right">
      ¡Ok! Continuar&nbsp;
      <i class="fas fa-arrow-right fa-fw "></i>
    </button>
    </div>
    </div>
  </section>
</template>

<script>
export default {};
</script>

<style scoped>
section {
  width: 100%;
}
</style>

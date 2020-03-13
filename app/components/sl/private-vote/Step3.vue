<template>
  <section>
    <img src="/assets/img/pp-logo-horizontal.svg" class="image logo-small-pp-voting is-pulled-right" alt="">
    <h1 class="title is-3">3. Confirmación</h1>
    <h1 class="subtitle is-5">Revisá y confirmá los proyectos que elegiste para votar.</h1>
    <hr>
    <img src="/assets/img/icons/mark-comunitario-round.svg" class="image is-48x48 is-pulled-right">
    <h1 class="title is-5">Estos son los proyectos COMUNITARIOS que elegiste</h1>
    <h1 class="subtitle is-6">(Podés cambiarlo antes de enviar tu voto)</h1>
      <div class="notification is-comunitario" v-for="voteProyecto in voteComunitarios" :key="voteProyecto.id">
        <h1 class="title is-4 is-marginless has-text-white">{{voteProyecto.name}}</h1>
        <p class="some-good-effects has-text-white">Por <span class="is-600">{{getWho(voteProyecto)}}</span> - {{getShortDescription(voteProyecto.objective,150)}}</p>
      </div>
      <b-message type="is-success" v-if="voteComunitarios.length == 0">
        <div class="has-text-centered">
        No seleccionaste proyectos comunitarios.<br><span class="is-size-7">Se considera tu voto como voto en blanco en esta categoría.</span>
        </div>
      </b-message>
      <button v-on:click.prevent="$router.push({ name: 'PrivateStep1'})" class="button is-success is-outlined is-fullwidth is-medium">
      <i class="fas fa-reply fa-fw "></i>&nbsp;Cambiar mi selección
    </button> 
      <hr>
      <img src="/assets/img/icons/mark-institucional-round.svg" class="image is-48x48 is-pulled-right">
    <h1 class="title is-5">Estos son los proyectos INSTITUCIONALES que elegiste</h1>
    <h1 class="subtitle is-6">(Podés cambiarlo antes de enviar tú voto)</h1>
      <div class="notification is-institucional" v-for="voteProyecto in voteInstitucionales" :key="voteProyecto.id">
        <h1 class="title is-4 is-marginless has-text-white">{{voteProyecto.name}}</h1>
        <p class="some-good-effects has-text-white">Por <span class="is-600">{{getWho(voteProyecto)}}</span> - {{getShortDescription(voteProyecto.objective,150)}}</p>
      </div>
      <b-message type="is-link" v-if="voteInstitucionales.length == 0">
        <div class="has-text-centered">
        No seleccionaste proyectos institucionales.<br><span class="is-size-7">Se considera tu voto como voto en blanco en esta categoría.</span>
        </div>
      </b-message>
      <button v-on:click.prevent="$router.push({ name: 'PrivateStep2'})" class="button is-link is-outlined is-fullwidth is-medium">
     <i class="fas fa-reply fa-fw "></i>&nbsp;Cambiar mi selección
    </button>
    <hr>
    <div class="is-clearfix">
    <button v-on:click.prevent="$router.push({ name: 'PrivateConfirm'})" class="button is-primary is-rounded is-medium is-pulled-right">
      ¡Confirmo mi elección!&nbsp;
      <i class="fas fa-arrow-right fa-fw "></i>
    </button>
    </div>
  </section>
</template>

<script>
import { indexOf, without } from 'lodash';

export default {
  data(){
    return {

    }
  },
  beforeMount: function() {

  },
  methods: { 
    getWho(project){
      if(project.type == 'institucion'){
        return project.organization_name
      }
        return project.author_names + ' ' + project.author_surnames
    },
    getDistrict: function(id) {
      switch (id) {
        case 1:
          return "Norte";
        case 2:
          return "Centro";
        case 3:
          return "Sur";
      }
    },
    getShortDescription: function(text, limit) {
      if (text.length > limit) {
        for (let i = limit; i > 0; i--) {
          if (
            text.charAt(i) === " " &&
            (text.charAt(i - 1) != "," ||
              text.charAt(i - 1) != "." ||
              text.charAt(i - 1) != ";")
          ) {
            return text.substring(0, i) + "...";
          }
        }
      } else {
        return text;
      }
    },
  },
  computed: {
    voteInstitucionales: function() {
      return this.$store.state.voteInstitucionales;
    },
    voteComunitarios: function() {
      return this.$store.state.voteComunitarios;
    }
  }
};
</script>

<style lang="scss" scoped>
.media-right .control{
  margin: 1px 0;
}
.is-checkbox{
  cursor: pointer;
}
.some-good-effects{
  line-height: normal;
  font-size:0.85rem;
}
</style>


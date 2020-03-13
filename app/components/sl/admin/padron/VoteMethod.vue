<template>
  <section>
    <citizen-header :citizen="citizen"></citizen-header>
    <div class="card">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 1 - Elegir metodo de votacion
        </h1>
        <h1 class="subtitle is-6">Pregunte la forma que el ciudadano prefiera votar</h1>
        <div class="columns" v-if="!method">
          <div class="column">
            <div class="box has-text-centered">
              <img src="/assets/img/icons/vote-paper.svg" alt class="image is-centered type-icon" />
              <h1 class="title is-5 is-marginless has-text-primary">Votar con PAPEL</h1>
              <p class="is-size-7">El clasico voto en papel.</p>
              <p
                class="is-size-7"
              >Una vez confirmado, el sistema marcará en el padrón que el ciudadano votó y podra entregarle el papel.</p>
              <br />
              <button
                @click="selectMethod('paper')"
                class="button is-medium is-primary is-fullwidth"
              >
                <i class="fas fa-arrow-right"></i>&nbsp;Elegir
              </button>
            </div>
          </div>
          <div class="column">
            <div class="box has-text-centered">
              <img src="/assets/img/icons/vote-tablet.svg" alt class="image is-centered type-icon" />
              <h1 class="title is-5 is-marginless has-text-primary">Votar con TABLET</h1>
              <p class="is-size-7">Se va a generar un código unico que deberá ingresar en la tablet.</p>
              <p class="is-size-7 has-text-link">
                <b>SIGA LAS INSTRUCCIONES EN PANTALLA</b>
              </p>
              <br />
              <button
                @click="selectMethod('tablet')"
                class="button is-medium is-primary is-fullwidth"
              >
                <i class="fas fa-arrow-right"></i>&nbsp;Elegir
              </button>
            </div>
          </div>
          <div class="column">
            <div class="box has-text-centered">
              <img src="/assets/img/icons/vote-link.svg" alt class="image is-centered type-icon" />
              <h1 class="title is-5 is-marginless has-text-primary">Votar en el CELULAR</h1>
              <p
                class="is-size-7"
              >Se le enviará por whatsapp al usuario el link para que entre a votar</p>

              <p class="is-size-7 has-text-link">
                <b>SIGA LAS INSTRUCCIONES EN PANTALLA</b>
              </p>
              <br />
              <button
                @click="selectMethod('link')"
                class="button is-medium is-primary is-fullwidth"
              >
                <i class="fas fa-arrow-right"></i>&nbsp;Elegir
              </button>
            </div>
          </div>
        </div>
        <div v-else>
          <div class="media">
            <div class="media-right">
              <img
                v-if="method == 'paper'"
                src="/assets/img/icons/vote-paper.svg"
                alt
                class="image selected-type-icon"
              />
              <img
                v-if="method == 'tablet'"
                src="/assets/img/icons/vote-tablet.svg"
                alt
                class="image selected-type-icon"
              />
              <img
                v-if="method == 'link'"
                src="/assets/img/icons/vote-link.svg"
                alt
                class="image selected-type-icon"
              />
            </div>
            <div class="media-content">
              <h1 v-if="method == 'paper'" class="title is-3 has-text-primary">Votar con PAPEL</h1>
              <h1 v-if="method == 'tablet'" class="title is-3 has-text-primary">Votar con TABLET</h1>
              <h1 v-if="method == 'link'" class="title is-3 has-text-primary">Votar en el CELULAR</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <paper-method v-if="method == 'paper'" :citizen="citizen" :urlParticipacion="urlParticipacion" @resetAll="resetAll"></paper-method>
    <tablet-method v-if="method == 'tablet'" :citizen="citizen" :urlParticipacion="urlParticipacion" :urlPublicVote="urlPublicVote" @resetAll="resetAll"></tablet-method>
    <link-method v-if="method == 'link'" :citizen="citizen" :urlParticipacion="urlParticipacion" :urlPublicVote="urlPublicVote" @resetAll="resetAll"></link-method>
  </section>
</template>

<script>
import CitizenHeader from "./CitizenHeader";
import PaperMethod from "./PaperMethod";
import TabletMethod from "./TabletMethod";
import LinkMethod from "./LinkMethod";
export default {
  props: ["citizen","urlParticipacion","urlPublicVote"],
  components: {
    CitizenHeader,
    PaperMethod,
    TabletMethod,
    LinkMethod
  },
  data() {
    return {
      method: null
    };
  },
  methods: {
    selectMethod: function(method) {
      this.method = method;
    },
    resetAll: function(){
      this.smethod = null
      this.$emit('resetAll')
    }
  }
};
</script>

<style lang="scss" scoped>
.type-icon {
  height: 80px;
  margin-bottom: 15px !important;
}
.selected-type-icon {
  height: 50px;
  margin-bottom: 15px;
  margin-right: 15px;
}
</style>

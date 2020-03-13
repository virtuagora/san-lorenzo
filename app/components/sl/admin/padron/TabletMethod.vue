<template>
  <section>
    <div class="card">
      <div class="card-content">
        <div class="columns">
          <div class="column">
            <h1 class="title is-5 has-text-link">
              <i class="fas fa-arrow-right"></i>&nbsp; Paso 2 - ¡Prepare la tablet!
            </h1>
            <h1 class="subtitle is-6">Tenga la tablet en mano</h1>
            <div class="content">
              <ol>
                <li>
                  En la tablet, entrar a la página
                  <b>
                    <a :href="urlPublicVote" class="has-text-link">{{urlPublicVote}}</a>
                  </b>
                </li>
                <li>Confirme con el usuario quiere participar con la tablet. Puede volver a elegir otro metodo de votación.</li>
                <li>Una vez que confirme, marquelo en el padron.</li>
                <li>
                  Al marcarlo en el padron,
                  <b>se le generara el código</b> para votar por la urna digital.
                </li>
              </ol>
            </div>
            <div class="notification is-danger">
              <i class="fas fa-exclamation-triangle"></i>&nbsp;Este código es de
              <b>UNICO USO E INTRANSFERIBLE</b>, luego de ser generado
              <b>NO PUEDE VOLVER A SER CONSULTADO</b>. Por lo tanto le recomendamos
              <b>NO CERRAR ESTA VENTANA</b> hasta que el usuario haya devuelto la tablet.
            </div>
          </div>
          <div class="column is-narrow has-text-centered">
            <qrcode :value="urlPublicVote" :options="{width: 200,margin:1}" class="myqr"></qrcode>
            <br />
            <a :href="urlPublicVote" class="has-text-link is-size-7">{{urlPublicVote}}</a>
          </div>
        </div>
            <button
              @click="showStepThree = true"
              v-if="!showStepThree"
              class="button is-medium is-primary is-fullwidth is-outlined"
            >
              <i class="fas fa-arrow-right"></i>&nbsp;Siguiente paso
            </button>
      </div>
    </div>
    <div class="card" v-if="showStepThree">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 3 - Confirmar participación en el padrón
        </h1>
        <div class="content">
          <p class="has-text-danger">Recuerde, una vez generado el código, el ciudadano no podra volver a elegir otra forma de votación</p>
        </div>
        <div class="columns" v-if="!response">
          <div class="column">
            <button @click="resetAll" class="button is-medium is-primary is-fullwidth is-outlined">
              <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
            </button>
          </div>
          <div class="column">
            <a
              @click="submitCitizen"
              class="button is-medium is-link is-fullwidth"
              :class="{'is-loading': loadingMarking}"
              :disabled="loadingMarking"
            >
              <i class="fas fa-arrow-right"></i>&nbsp;Confirmar participación
            </a>
          </div>
        </div>
        <section v-if="response && responseOk">
          <div class="notification is-success has-text-centered" style="margin-top:10px;">
            <i class="fas fa-check fa-fw"></i>&nbsp;¡Participacion guardada con exito!
          </div>
        </section>
        <section v-if="response && responseFail">
          <div class="notification is-danger has-text-centered" style="margin-top:10px;">
            <p>
              <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp;Ocurrio un error al guardar la participación
            </p>
            <p>Por favor, vuelva a comenzar todo el proceso de nuevo</p>
            <br />
            <button @click="resetAll" class="button is-medium is-white is-outlined">
              <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
            </button>
          </div>
        </section>
      </div>
    </div>
    <div class="card" v-if="showStepFour">
      <div class="card-content">
        <div class="columns">
          <div class="column">
            <h1 class="title is-5 has-text-link">
              <i class="fas fa-arrow-right"></i>&nbsp; Paso 4 - Ingresar codigo en tablet
            </h1>
            <h1 class="subtitle is-6">Ingrese el código y espere a que vote el ciudadano</h1>
            <div class="content">
              <p>Ingrese el siguiente código en la tablet</p>
            </div>
          </div>
          <div class="column is-narrow">
            <div class="notification is-black has-text-centered">
              <h1 class="subtitle is-6">Código</h1>
              <h1 class="title is-1 spaced-code">{{code}}</h1>
            </div>
          </div>
        </div>
       <button
          @click="showStepFive = true"
          v-if="!showStepFive"
          class="button is-medium is-primary is-fullwidth is-outlined"
        >
          <i class="fas fa-arrow-right"></i>&nbsp;El ciudadano devolvio la tablet
        </button>
      </div>
    </div>
    <div class="card" v-if="showStepFive">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 5 - Exito!
        </h1>
        <h1 class="subtitle is-6">Listo! Puede continuar con el siguiente ciudadano.</h1>
        <button @click="resetAll" class="button is-medium is-primary is-fullwidth">
          <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import VueQrcode from "@chenfengyuan/vue-qrcode";

export default {
  props: ["citizen", "urlParticipacion", "urlPublicVote"],
  data() {
    return {
      loadingMarking: false,
      response: false,
      responseFail: false,
      responseOk: false,
      showStepThree: false,
      showStepFour: false,
      showStepFive: false,
      code: null,
    };
  },
  components: {
    qrcode: VueQrcode
  },
  methods: {
    submitCitizen: function() {
      this.loadingMarking = true;
      this.$http
        .post(this.urlParticipacion, this.payload)
        .then(response => {
          this.$snackbar.open({
            message: "¡Participación guardada!",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.code = response.data.codigo
          this.responseOk = true;
          this.showStepFour = true;
        })
        .catch(error => {
          console.error(error.message);
          this.responseFail = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        })
        .finally(() => {
          this.loadingMarking = false;
          this.response = true;
        });
    },
    resetAll: function() {
      window.scrollTo(0,0);
      this.loadingMarking = false
      this.response = false
      this.responseFail = false
      this.responseOk = false
      this.showStepThree = false
      this.showStepFour = false
      this.showStepFive = false
      this.code = null
      this.$emit("resetAll");
    }
  },
  computed: {
    urlUrna: function() {
      return `${window.location.protocol}//${window.location.host}/urna`;
    },
    payload: function() {
      return {
        matricula: this.citizen.dni,
        tipo: 'tablet'
      };
    }
  }
};
</script>


<template>
  <section>
    <div class="card">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 2 - Confirmar participación en padron
        </h1>
        <h1 class="subtitle is-6">Confirme la participación del ciudadano</h1>
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
              :class="{'is-loading': loadingMarking }"
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
    <div class="card" v-if="showStepThree">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 3 - Entregar boleta de votación
        </h1>
        <h1
          class="subtitle is-6"
        >Entregue la boleta de votación al ciudadano, y cuando termine de votar, continue con el próximo paso</h1>
        <button
          @click="showStepFour = true"
          v-if="!showStepFour"
          class="button is-medium is-primary is-fullwidth is-outlined"
        >
          <i class="fas fa-arrow-right"></i>&nbsp;Siguiente paso
        </button>
      </div>
    </div>
    <div class="card" v-if="showStepFour">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 4 - Exito!
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
export default {
  props: ["citizen", "urlParticipacion"],
  data() {
    return {
      responseOk: false,
      response: false,
      responseFail: false,
      loadingMarking: false,
      showStepThree: false,
      showStepFour: false
    };
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
          this.responseOk = true;
          this.showStepThree = true;
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
      this.response = false;
      this.responseOk = false;
      this.responseFail = false;
      this.showStepThree = false;
      this.showStepFour = false;
      this.$emit("resetAll");
    }
  },
  computed: {
    payload: function() {
      return {
        matricula: this.citizen.dni,
        tipo: "paper"
      };
    }
  }
};
</script>


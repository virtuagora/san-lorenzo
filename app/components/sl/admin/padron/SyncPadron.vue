<template>
  <section>
    <div class="columns">
      <div class="column is-4">
        <h1 class="title is-3" :class="errors.has('numero')">Busqueda</h1>
        <h1 class="subtitle is-5">Ingrese el Nro. de identificación</h1>
      </div>
      <div class="column">
        <div class="field has-addons">
          <p class="control">
            <a class="button is-static is-large">
              <i class="fas fa-arrow-right fa-lg"></i>
            </a>
          </p>
          <p class="control is-expanded">
            <input v-model="numero" v-on:keyup.enter="search()" class="input is-large" type="text" data-vv-name="numero" data-vv-as="'Numero de matrícula'" v-validate="'required|integer|min:6|max:10'" placeholder="Número de identificación">
            <span class="help is-danger" v-show="errors.has('numero')">
              <i class="fas fa-times-circle fa-fw"></i> {{errors.first('numero')}}</span>
          </p>
          <p class="control">
            <a @click="search()" class="button is-primary is-large" :disabled="errors.count() > 0 || numero == ''">
              <i class="fas fa-search"></i>
            </a>
          </p>
        </div>
      </div>
    </div>
    <hr>
    <div v-if="showResults">
      <div class="notification is-light has-text-centered" v-if="isLoading">
        <i class="fas fa-spinner fa-lg fa-pulse"></i>
        <br> Cargando, un momento por favor
      </div>
      <div v-else>
        <div class="notification is-warning" v-show="results.length > 1">
          <i class="fas fa-exclamation-triangle fa-fw"></i> Se han encontrado multiples resultados para el mismo DNI. ¡Cuidado! Reportelo de ser necesario
        </div>
        <div class="box" v-for="result in results" :key="result.dni">
          <figure class="is-pulled-left" style="margin-right: 10px; margin-bottom: 10px;">
            <avatar :username="result.data.name" :size="64" />
          </figure>
          <div class="columns">
            <div class="column">
                <div class="tag is-link" v-if="result.subject">
                  <i class="fas fa-user fa-fw"></i>&nbsp;¡Registrado en la plataforma!
                </div>
              <h1 class="title is-4 is-marginless">{{result.data.name}}</h1>
              <h1 class="subtitle is-6 is-marginless">{{result.data.address}} - Año {{result.year}}</h1>
              <h1 class="subtitle is-6 is-marginless">{{result.data.doc_type}} -
                <b>{{result.dni}}</b>
              </h1>
              <h1 class="subtitle is-6 is-marginless" v-if="result.table">Mesa: {{result.table}} - Orden: {{result.orden}}</h1>
              <h1 class="subtitle is-6 is-marginless" v-else>(Agregado por sistema)</h1>
            </div>
            <div class="column">
              <div v-if="result.voted" class="notification is-success">
                <i class="far fa-grin fa-lg fa-fw"></i> ¡Ha participado!
              </div>
              <div v-else>
                <p>
                  Aún no ha votado en el presupuesto
                </p>
                <br>
                <p v-if="votable">
                  <button class="button is-primary" @click="markCitizen(result.data.name,result.dni)">
                    <i class="far fa-grin fa-fw fa-lg"></i>&nbsp;Guardar participacion</button>
                </p>
                <p v-else>
                  <button class="button is-primary is-outlined" disabled>
                    <i class="fas fa-exclamation-triangle fa-fw fa-lg"></i>&nbsp;Guardar participacion</button>
                    <span class="help is-danger">El periodo de votación no está disponible o ha terminado</span>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="notification has-text-centered" v-if="results.length == 0">
          <i class="fas fa-question-circle fa-fw"></i>&nbsp;No se ha encontrado ninguna entrada en el padrón.
          <br>
          <br>
          <a @click="addCitizen()" class="button is-dark">
            <i class="fas fa-user-plus"></i>&nbsp;Agregar un nuevo registro</a>
        </div>
      </div>
    </div>
    <div v-if="showMarkingProcess">
      <div class="notification is-warning has-text-centered" v-if="!response.ok" style="margin-top:10px;">
        ¿Confirma la participación de {{markingName}} ({{markingDNI}}) en el presupuesto?
        <br>
        <br>
        <div class="buttons is-centered" v-if="!loadingMarking">
          <a @click="submitMarking" class="button is-primary">
            <i class="fas fa-check fa-fw"></i>&nbsp;Confirmar</a>
          <a @click="cancelarMarking" class="button is-white">
            Cancelar</a>
        </div>
        <div v-else>
          <i class="fas fa-spinner fa-lg fa-pulse"></i>
          <br> Cargando, un momento por favor
        </div>
      </div>
    </div>
    <div v-if="showAddCitizen">
      <add-citizen :url="urlAddCitizen" :allow-restart="false" :url-participacion="urlParticipacion"></add-citizen>
    </div>
      <div class="notification is-success has-text-centered" v-if="response.ok" style="margin-top:10px;">
        <i class="fas fa-check fa-fw"></i>&nbsp;¡Participacion guardada con exito!
      </div>
  </section>
</template>

<script>
import Avatar from "../utils/Avatar";
import AddCitizen from "./AddCitizen"
export default {
  props: ["url", "urlAddCitizen","urlParticipacion","votable"],
  components: {
    Avatar,
    AddCitizen
  },
  data() {
    return {
      type: "DNI",
      showResults: false,
      showMarkingProcess: false,
      showAddCitizen: false,
      isLoading: false,
      numero: "",
      markingName: "",
      markingDNI: "",
      results: [],
      loadingMarking: false,
      response: {
        ok: false
      }
    };
  },
  methods: {
    search: function() {
      this.showAddCitizen = false
      this.showResults = false;
      this.response.ok = false;
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$buefy.snackbar.open({
            message: "Numero de matricula inválido",
            type: "is-danger",
            actionText: "Ok"
          });
        } else {
          console.log("Buscando..");
          this.isLoading = true;
          this.$http
            .get(this.urlGet)
            .then(response => {
              this.showResults = true;
              this.isLoading = false;
              this.results = response.data;
            })
            .catch(error => {
              this.isLoading = false;
              console.error(error);
              this.$buefy.snackbar.open({
                message: "Ocurrio un error inesperado. Recargue la página",
                type: "is-danger",
                actionText: "Ok"
              });
            });
        }
      });
    },
    markCitizen: function(name, dni){
      this.markingName = name
      this.markingDNI = dni
      this.showMarkingProcess = true
    },
    addCitizen: function(){
      this.showResults = false,
      this.showAddCitizen = true
    },
    submitMarking: function() {
      this.loadingMarking = true;
      this.$http
        .post(this.urlParticipacion, this.payload)
        .then(response => {
          this.$buefy.snackbar.open({
            message: "¡Participación guardada!",
            type: "is-success",
            actionText: "¡Genial!"
          });

          this.loadingMarking = false;
          this.response.ok = true;
          this.cancelarMarking();
        })
        .catch(error => {
          console.error(error.message);
          this.loadingMarking = false;
          this.response.ok = false;
          this.$buefy.snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        });
    },
    cancelarMarking: function(){
      this.showResults = false
      this.markingName = ''
      this.markingDNI = null
      this.showMarkingProcess = false
      this.results = []
    }
  },
  computed: {
    urlGet: function() {
      let query = [];
      if (this.numero) {
        query.push("matricula=" + this.numero);
      }
      // query.push("size=" + 7);
      return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    },
    payload: function() {
      return {
        matricula: this.markingDNI
      };
    }
  }
};
</script>

<style lang="scss" scoped>
.media-content {
  overflow: visible;
}

</style>


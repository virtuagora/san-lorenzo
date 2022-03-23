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
            <input
              ref="theInput"
              v-model="numero"
              v-on:keyup.enter="search()"
              class="input is-large"
              type="text"
              data-vv-name="numero"
              data-vv-as="'Numero de matrícula'"
              v-validate="'required|integer|min:6|max:10'"
              placeholder="Número de identificación"
            />
            <span class="help is-danger" v-show="errors.has('numero')">
              <i class="fas fa-times-circle fa-fw"></i>
              {{errors.first('numero')}}
            </span>
          </p>
          <p class="control">
            <a
              @click="search()"
              class="button is-primary is-large"
              :disabled="errors.count() > 0 || numero == ''"
            >
              <i class="fas fa-search"></i>
            </a>
          </p>
        </div>
      </div>
    </div>
    <hr />
    <div v-if="showResults">
      <div class="notification is-light has-text-centered" v-if="isLoading">
        <i class="fas fa-spinner fa-lg fa-pulse"></i>
        <br />Cargando, un momento por favor
      </div>
      <div v-else>
        <div class="notification is-warning" v-show="results.length > 1">
          <i class="fas fa-exclamation-triangle fa-fw"></i> Se han encontrado multiples resultados para el mismo DNI. ¡Cuidado! Reportelo de ser necesario
        </div>
        <div class="card" v-for="result in results" :key="result.dni">
          <div class="card-content">
            <figure class="is-pulled-left" style="margin-right: 10px; margin-bottom: 10px;">
              <avatar :username="result.data.name" :size="64" />
            </figure>
            <div class="columns">
              <div class="column">
                <div class="tag is-link" v-if="result.subject">
                  <i class="fas fa-user fa-fw"></i>&nbsp;¡Registrado en la plataforma!
                </div>
                <h1 class="title is-4 is-marginless">{{result.data.name}}</h1>
                <h1
                  class="subtitle is-6 is-marginless"
                >{{result.data.address}} - Año {{result.year}}</h1>
                <h1 class="subtitle is-6 is-marginless">
                  {{result.data.doc_type}} -
                  <b>{{result.dni}}</b>
                </h1>
                <h1
                  class="subtitle is-6 is-marginless"
                  v-if="result.table"
                >Mesa: {{result.table}} - Orden: {{result.orden}}</h1>
                <h1 class="subtitle is-6 is-marginless" v-else>(Agregado por sistema)</h1>
              </div>
              <div class="column">
                <div v-if="result.voted" class="notification is-success">
                  <i class="far fa-grin fa-lg fa-fw"></i> ¡Ha participado!
                </div>
                <div v-else>
                  <p>Aún no ha votado en el presupuesto</p>
                  <br />
                  <p v-if="votable">
                    <button class="button is-primary" @click="chooseVoteMethod(result)">
                      <i class="far fa-grin fa-fw fa-lg"></i>&nbsp;Elegir método de votación
                    </button>
                  </p>
                  <p v-else>
                    <button class="button is-primary is-outlined" disabled>
                      <i class="fas fa-exclamation-triangle fa-fw fa-lg"></i>&nbsp;Elegir método de votación
                    </button>
                    <span
                      class="help is-danger"
                    >El periodo de votación no está disponible o ha terminado</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="notification has-text-centered" v-if="results.length == 0">
          <i class="fas fa-question-circle fa-fw"></i>&nbsp;No se ha encontrado ninguna entrada en el padrón.
          <br />
          <br />
          <a @click="addCitizen()" class="button is-dark">
            <i class="fas fa-user-plus"></i>&nbsp;Agregar un nuevo registro
          </a>
        </div>
      </div>
    </div>
    <div v-if="showAddCitizen">
      <add-citizen
        :url="urlAddCitizen"
        :allow-restart="false"
        :allow-start-voting="true"
        @startVoting="startVoting"
        :url-participacion="urlParticipacion"
      ></add-citizen>
    </div>
  </section>
</template>

<script>
import Avatar from "../../utils/Avatar";
import AddCitizen from "./AddCitizen";

export default {
  props: ["url", "urlAddCitizen", "votable"],
  data() {
    return {
      type: "DNI",
      showResults: false,
      showAddCitizen: false,
      isLoading: false,
      numero: "",
      results: [],
      loadingMarking: false
    };
  },
  components: {
    Avatar,
    AddCitizen
  },
  methods: {
    search: function() {
      this.showAddCitizen = false;
      this.showResults = false;
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
    addCitizen: function() {
      (this.showResults = false), (this.showAddCitizen = true);
    },
    resetAll: function() {
      this.numero = null
      this.$refs.theInput.focus()
      this.showResults = false;
      this.results = [];
       this.$nextTick().then(() => {
        this.$validator.reset();
        this.errors.clear();
      });
    },
    startVoting: function(citizen){
      this.showAddCitizen = false
      this.$emit("chooseVoteMethod", citizen);
    },
    chooseVoteMethod: function(citizen) {
      this.$emit("chooseVoteMethod", citizen);
    }
  },
  computed: {
    urlGet: function() {
      let query = [];
      if (this.numero) {
        query.push("matricula=" + this.numero);
      }
      return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    }
  }
};
</script>

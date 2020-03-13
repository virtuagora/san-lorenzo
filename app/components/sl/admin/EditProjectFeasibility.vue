<template>
  <section>
      <h1 class="title is-3 is-marginless">
        <i class="fas fa-clipboard-check"></i>&nbsp;Factibilidad de la propuesta
      </h1>
      <p>Defina la factibilidad del proyecto. Si el proyecto no tiene una sentencia de su factibilidad, entonces marque como "Aún no evaluado"</p>
    <hr>
    <h1 class="title is-4">
      <i class="fas fa-caret-right"></i>&nbsp; Estado de la factibilidad
    </h1>
    <h1 class="subtitle is-6">
          <span class="has-text-link">* Requerido.</span> Una propuesta terminará siendo factible o no factible. Defina su estado en base a su sentencia.
      </h1>
    <b-field class="is-hidden-touch">
      <b-radio-button
        v-model="project.feasible"
        :native-value="null"
        size="is-medium"
        type="is-link"
        :disabled="!editable"
      >
        <i class="fas fa-clock"></i>&nbsp;Aún no evaluado
      </b-radio-button>
      <b-radio-button
        v-model="project.feasible"
        :native-value="true"
        size="is-medium"
        type="is-link"
        :disabled="!editable"
      >
        <i class="fas fa-clipboard-check"></i>&nbsp;Factible
      </b-radio-button>
      <b-radio-button
        v-model="project.feasible"
        :native-value="false"
        size="is-medium"
        type="is-link"
        :disabled="!editable"
      >
        <i class="fas fa-times"></i>&nbsp;No factible
      </b-radio-button>
    </b-field>
    <section class="is-hidden-desktop">
      <div class="field">
        <b-radio v-model="project.feasible" size="is-medium" :native-value="null" type="is-link" :disabled="!editable">
        <i class="fas fa-clock"></i>&nbsp;Aún no evaluado
        </b-radio>
        </div>
      <div class="field">
        <b-radio v-model="project.feasible" size="is-medium" :native-value="true" type="is-link" :disabled="!editable">
        <i class="fas fa-clipboard-check"></i>&nbsp;Factible
      </b-radio>
        </div>
      <div class="field">
        <b-radio v-model="project.feasible" size="is-medium" :native-value="false" type="is-link" :disabled="!editable">
        <i class="fas fa-times"></i>&nbsp;No factible
      </b-radio>
        </div>
    </section>
    <br>
    <div class="field" v-if="project.feasible === true || project.feasible === false">
      <h1 class="title is-4" :class="{'has-text-danger': errors.has('project.feasibility')}">
          <i class="fas fa-caret-right"></i>&nbsp; Sentencia de la factibilidad
      </h1>
      <h1 class="subtitle is-6">
          <span class="has-text-link">* Requerido.</span> Defina porque el proyecto es factible o no factible. Tenes un máximo de 1500 caracteres
      </h1>
      <div class="control">
        <b-input
          v-model="project.feasibility"
          data-vv-name="project.feasibility"
          data-vv-as="'Descripcion'"
          v-validate="'required|min:5|max:2000'"
          type="textarea"
          minlength="5"
          maxlength="2000"
          rows="3"
          placeholder="Requerido *"
          :readonly="!editable"
        ></b-input>
        <span v-show="errors.has('project.feasibility')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>
          &nbsp;{{errors.first('project.feasibility')}}
        </span>
      </div>
    </div>
    <div class="field" v-if="project.feasible === true">
      <div class="notification is-danger">
        <span class="is-800"><i class="fas fa-exclamation-triangle"></i>&nbsp;ES IMPORTANTE QUE ASIGNE EL CÓDIGO, ES CONDICION NECESARIA PARA LA PROGRAMACION DE LA VOTACIÓN</span>
      </div>
      <h1 class="title is-4">
          <i class="fas fa-caret-right"></i>&nbsp; Código del proyecto
      </h1>
      <h1 class="subtitle is-6">
          <span class="has-text-link">* Requerido.</span> ¡Genial! ¡El proyecto es factible! Ahora debe definir el codigo del proyecto.
      </h1>
         <b-message type="is-link">
           <p>Distrito: <b>{{existingProject.district.name}}</b> - Tipo: <b>{{capitalizeString(existingProject.type)}}</b></p>
         </b-message>
     <div class="columns">
       <div class="column">
         <div class="field">
           <h1 class="title is-4">
              <i class="fas fa-caret-right"></i>&nbsp; Defina el número del código
          </h1>
           <div class="control">
            <input
                  v-model="numberCode"
                  data-vv-name="numberCode"
                  data-vv-as="'Numero de código de proyecto'"
                  type="text"
                  v-validate="'required|min_value:1|numeric|max_value:500'"
                  class="input is-large"
                  placeholder="Requerido *"
                  :readonly="!editable"
                >
            <span v-show="errors.has('numberCode')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>
              &nbsp;{{errors.first('numberCode')}}
            </span>
          </div>
         </div>
       </div>
       <div class="column is-narrow">
         <div class="notification is-dark has-text-centered">
            <h1 class="subtitle is-5">Código</h1>
            <h1 class="title is-1">{{completeCode}}</h1>
         </div>
       </div>
     </div>
    </div>
    <button
      class="button is-primary is-large is-fullwidth"
      @click="submit"
      v-if="!response.replied && editable"
    >
      <i class="fas fa-save fa-fw"></i> Guardar
    </button>
    <div class="notification is-warning" v-if="!editable">
      <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp; <b>El proyecto ya no es editable!</b>
    </div>
    <div class="message is-success" v-show="response.replied && response.ok">
      <div class="message-body">
        <i class="fas fa-check fa-fw"></i>&nbsp;
        <b>¡Bien!</b> ¡El proyecto ha sido editado correctamente!
      </div>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["formUrl", "existingProject", "editable"],
  data() {
    return {
      isLoading: false,
      numberCode: null,
      project: {
        feasible: null,
        feasibility: null
      },
      response: {
        replied: false,
        ok: false
      }
    };
  },
  beforeMount: function() {
    this.project.feasible = this.existingProject.feasible;
    this.project.feasibility = this.existingProject.feasibility;
    if(this.existingProject.code){
      this.numberCode = this.existingProject.code.split('/')[1]
    }
  },
  methods: {
    submit: function() {
      this.isLoading = true;
      this.$validator.validate()
        .then(response => {
          if (response) {
            // Valid
            console.log("Sending form!");
            this.isLoading = true;
            this.$http
              .post(this.formUrl, this.payload)
              .then(response => {
                this.$snackbar.open({
                  message: "¡Factibilidad guardada!",
                  type: "is-success",
                  actionText: "¡Genial!"
                });
                this.isLoading = false;
                this.response.replied = true;
                this.response.ok = true;
              })
              .catch(error => {
                console.error(error);
                this.isLoading = false;
                this.$snackbar.open({
                  message: error.response.data.message || "Error inesperado",
                  type: "is-danger",
                  actionText: "Cerrar",
                  indefinite: true
                });
              });
          } else {
            // Not Valid
            this.isLoading = false;
            this.$snackbar.open({
              message: "Faltan datos o algunos son incorrectos. Verifíquelos.",
              type: "is-danger",
              actionText: "Cerrar",
              indefinite: true
            });
          }
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        });
    }
  },
  computed: {
    payload: function() {
      let payload = {
        feasibility: this.project.feasibility,
        feasible: this.project.feasible,
      };
      if(this.project.feasible === true){
        payload.code = this.completeCode;
      } else {
        payload.code = null
        if(this.project.feasible === null){
          payload.feasibility = null
        }
      }
      return payload;
    },
    completeCode: function(){
      return this.existingProject.district.name['0'] 
      + this.existingProject.type[0].toUpperCase()
      + '/'
      + (parseInt(this.numberCode) || '')
    }
  },
};
</script>

<style>
</style>

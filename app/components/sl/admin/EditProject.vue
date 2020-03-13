<template>
  <section>
    <div class="notification is-link">
      <h1 class="title is-3 is-marginless">
        <i class="fas fa-user"></i>&nbsp;Datos de la propuesta (Admin)
      </h1>
      <p>Estos campos Solo se encuentran disponible para la administración</p>
    </div>
    <div class="message is-link">
      <div class="message-body">
        <div class="field">
          <h1 class="title is-4" :class="{'has-text-danger': errors.has('project.notes')}">
            <i class="fas fa-caret-right"></i>&nbsp;Observaciones de la administracion
          </h1>
          <h1
            class="subtitle is-6"
          >Utilice este campo para guardar notas y observaciones para todos los administradores. Tiene un máximo de 1500 caracteres</h1>
          <div class="control">
            <b-input
              v-model="project.notes"
              data-vv-name="project.notes"
              data-vv-as="'Observaciones de la administracion'"
              v-validate="'min:5|max:1500'"
              type="textarea"
              minlength="10"
              maxlength="1500"
              rows="2"
              :readonly="responseNotes.ok"
              placeholder="Deje el campo vacio para borrar la observación"
            ></b-input>
            <span v-show="errors.has('project.notes')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>
              &nbsp;{{errors.first('project.notes')}}
            </span>
          </div>
        </div>
        <div class="field">
          <div class="buttons" v-if="!responseNotes.replied">
            <a @click="submitNotes" class="button is-link"><i class="fas fa-save"></i>&nbsp;Guardar</a>
          </div>
         <p class="has-text-link" v-show="responseNotes.replied && responseNotes.ok">
                <i class="fas fa-check"></i>&nbsp;Las observaciones han sido guardadas
              </p>
        </div>
      </div>
    </div>
    <br>
    <form-project-admin ref="FormProjectAdmin" :editing="true" :editable="editable" :budget="budget" :project.sync="project"></form-project-admin>
    <hr>
    <button
      class="button is-primary is-large is-fullwidth"
      @click="submit"
      v-if="!response.replied && editable"
    >
      <i class="fas fa-save"></i>&nbsp;Editar
    </button>
    <div class="notification is-warning" v-if="!editable">
      <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp; <b>¡El proyecto ya no es editable!</b>
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
import FormProjectAdmin from "../utils/FormProjectAdmin";
export default {
  props: ["formUrl", "notesUrl", "budget", "existingProject", "editable"],
  components: {
    FormProjectAdmin
  },
  data() {
    return {
      isLoading: false,
      project: {
        code: null,
        name: null,
        type: null,
        objective: null,
        description: null,
        benefited_population: null,
        community_contributions: null,
        budget: [],
        total_budget: null,
        author_names: null,
        author_surnames: null,
        author_phone: null,
        author_email: null,
        author_dni: null,
        organization_name: null,
        organization_legal_entity: null,
        organization_address: null,
        notes: null,
        district_id: null,
      },
      response: {
        replied: false,
        ok: false
      },
      responseNotes: {
        replied: false,
        ok: false
      }
    };
  },
  beforeMount: function() {
    this.project.code = this.existingProject.code;
    this.project.name = this.existingProject.name;
    this.project.type = this.existingProject.type;
    this.project.objective = this.existingProject.objective;
    this.project.description = this.existingProject.description;
    this.project.benefited_population = this.existingProject.benefited_population;
    this.project.community_contributions = this.existingProject.community_contributions;
    this.project.budget = this.existingProject.budget;
    this.project.author_names = this.existingProject.author_names;
    this.project.author_surnames = this.existingProject.author_surnames;
    this.project.author_phone = this.existingProject.author_phone;
    this.project.author_email = this.existingProject.author_email;
    this.project.author_dni = this.existingProject.author_dni;
    this.project.organization_name = this.existingProject.organization_name;
    this.project.organization_legal_entity = this.existingProject.organization_legal_entity;
    this.project.organization_address = this.existingProject.organization_address;
    this.project.notes = this.existingProject.notes;
    this.project.district_id = this.existingProject.district_id;
  },
  methods: {
    submitNotes: function(){
      this.$http
        .post(this.notesUrl, {
          notes: this.isOptional(this.project.notes)
        })
        .then(response => {
          this.$snackbar.open({
            message: "¡Observaciones guardadas!",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.isLoading = false;
          this.responseNotes.replied = true;
          this.responseNotes.ok = true;
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
    },
    submit: function() {
      this.isLoading = true;
      this.$refs.FormProjectAdmin.validateForm()
        .then(response => {
          if (response) {
            // Valid
            console.log("Sending form!");
            this.isLoading = true;
            this.$http
              .post(this.formUrl, this.payload)
              .then(response => {
                this.$snackbar.open({
                  message: "¡Proyecto guardado!",
                  type: "is-success",
                  actionText: "¡Genial!"
                });
                this.isLoading = false;
                this.response.replied = true;
                this.response.ok = true;
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
        name: this.project.name,
        type: this.project.type,
        district_id: this.project.district_id,
        objective: this.project.objective,
        description: this.project.description,
        benefited_population: this.project.benefited_population,
        community_contributions: this.project.community_contributions,
        budget: this.project.budget,
        author_names: this.project.author_names,
        author_surnames: this.project.author_surnames,
        author_phone: this.project.author_phone,
        author_email: this.project.author_email,
        author_dni: this.project.author_dni,
      };
      if (this.project.type == "institucional") {
        payload.organization_name = this.project.organization_name
        payload.organization_legal_entity = this.project.organization_legal_entity
        payload.organization_address = this.project.organization_address
      } else {
        payload.organization_name = null;
        payload.organization_legal_entity = null;
        payload.organization_address = null;
      }
      return payload;
    }
  }
};
</script>

<style>
</style>

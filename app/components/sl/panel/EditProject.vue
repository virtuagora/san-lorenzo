<template>
  <section>
    <form-project ref="formProject" :editing="true" :budget="budget" :project.sync="project" :editable="editable"></form-project>
    <hr>
    <button class="button is-primary is-large is-fullwidth" @click="submit" v-if="!response.replied && editable"><i class="fas fa-save fa-fw"></i> Editar</button>
    <div class="notification is-warning" v-if="!editable">
      <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp; <b>¡No se pueden editar mas los proyectos!</b>
    </div>
    <div class="message is-success" v-show="response.replied && response.ok">
      <div class="message-body">
        <i class="fas fa-check fa-fw"></i>&nbsp;<b>¡Bien!</b> ¡Tu propuesta ha sido editada correctamente!
      </div>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
import FormProject from "../utils/FormProjectUser";
export default {
  props: ["formUrl","budget", "existingProject", "editable"],
  components: {
    FormProject
  },
  data() {
    return {
      isLoading: false,
      project: {
        name: null,
        type: null,
        district_id: null,
        objective: null,
        description: null,
        benefited_population: null,
        community_contributions: null,
        budget: [],
        author_names: null,
        author_surnames: null,
        author_phone: null,
        author_email: null,
        author_dni: null,
        organization_name: null,
        organization_legal_entity: null,
        organization_address: null,
        organization_cuit: null,
        organization_nro_personeria: null
      },
      response: {
        replied: false,
        ok: false
      } 
    };
  },
  beforeMount: function(){
    this.project.name = this.existingProject.name
    this.project.type = this.existingProject.type
    this.project.district_id = this.existingProject.district_id
    this.project.objective = this.existingProject.objective
    this.project.description = this.existingProject.description
    this.project.benefited_population = this.existingProject.benefited_population
    this.project.community_contributions = this.existingProject.community_contributions
    this.project.budget = this.existingProject.budget
    this.project.author_names = this.existingProject.author_names
    this.project.author_surnames = this.existingProject.author_surnames
    this.project.author_phone = this.existingProject.author_phone
    this.project.author_email = this.existingProject.author_email
    this.project.author_dni = this.existingProject.author_dni
    this.project.organization_name = this.existingProject.organization_name
    this.project.organization_legal_entity = this.existingProject.organization_legal_entity
    this.project.organization_address = this.existingProject.organization_address
    this.project.organization_cuit = this.existingProject.organization_cuit
    this.project.organization_nro_personeria = this.existingProject.organization_nro_personeria
  },
  methods: {
    submit: function() {
      this.isLoading = true
      this.$refs.formProject
        .validateForm()
        .then(response => {
          if (response) {
            // Valid
            console.log("Sending form!");
            this.isLoading = true;
            this.$http
              .post(this.formUrl, this.payload)
              .then(response => {
                this.$snackbar.open({
                  message: "¡Propuesta guardada con éxito!",
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
        district_id: this.project.district_id
      };
      if (this.project.type == "institucional") {
        payload.organization_name = this.project.organization_name
        payload.organization_legal_entity = this.project.organization_legal_entity
        payload.organization_address = this.project.organization_address
        payload.organization_cuit = this.project.organization_cuit
        payload.organization_nro_personeria = this.project.organization_nro_personeria
      } else {
        payload.organization_name = null;
        payload.organization_legal_entity = null;
        payload.organization_address = null;
        payload.organization_cuit = null;
        payload.organization_nro_personeria = null;
      }
      return payload
    }
  }
};
</script>

<style>
</style>

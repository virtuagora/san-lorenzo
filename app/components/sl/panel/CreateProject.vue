<template>
  <section>
    <form-project ref="formProject" :project.sync="project" :budget="budget" :user="user" :citizen="citizen" :editable="editable"></form-project>
    <hr>
    <h1 class="title is-4 has-text-centered">
          ¿Todo listo?
      </h1>
      <h1 class="subtitle is-6 has-text-centered">
          Cuando hayas terminado, apretá en <b><i class="fas fa-save"></i> Guardar</b>.<br>¡Y no te preocupes! Vas a poder editarlo las veces que quieras, hasta que cierre la fecha para la subida de propuestas
      </h1>
    <button class="button is-primary is-large is-outlined is-fullwidth" @click="submit" v-if="!response.replied && editable"><i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
    <div class="notification is-warning" v-if="!editable">
      <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp; <b>¡No se pueden crear proyectos!</b>
    </div>
    <div class="message is-success" v-show="response.replied && response.ok">
      <div class="message-body">
        <i class="fas fa-check fa-fw"></i>&nbsp;<b>¡Genial!</b> ¡Tu propuesta ha sido guardada correctamente! ¡Felicidades y gracias por tu contribución!
      </div>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
import FormProject from "../utils/FormProjectUser";
export default {
  props: ["formUrl", "budget", "user", "citizen", "editable"],
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
  methods: {
    submit: function() {
      this.isLoading = true
      this.$refs.formProject
        .validateForm()
        .then(response => {
          if (response) {
            // Valid
            console.log("Sending form!");
            console.log(this.payload)
            this.isLoading = true;
            this.$http
              .post(this.formUrl, this.payload)
              .then(response => {
                this.$snackbar.open({
                  message: "¡Propuesta guardad con éxito!",
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
        payload.organization_cuit = this.project.organization_cuit
        payload.organization_nro_personeria = this.project.organization_nro_personeria
      } else {
        payload.organization_name = null;
        payload.organization_legal_entity = null;
        payload.organization_address = null;
        payload.organization_cuit = null;
        payload.organization_nro_personeria = null;
      }
      return payload;
    }
  }
};
</script>

<style>
</style>

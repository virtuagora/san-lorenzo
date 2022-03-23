<template>
  <section>
    <h1 class="title is-3"><i class="fas fa-list"></i>&nbsp;Asignar proyecto a un usuario</h1>
      <p>Puede asignarle el proyecto a un usuario que efectivamente sea ciudadano de San Lorenzo.</p>
      <br>

      <div class="box" v-if="projectCopy.author">
        <h1 class="title is-4 is-marginless">
          Usuario asignado al proyecto
        </h1>
        <h2 class="subtitle is-5 is-marginless">
          {{projectCopy.author.subject.display_name}}
        </h2>
        <div class="content">
          <p>Registrado el {{projectCopy.author.created_at}}</p>
        </div>
        <div class="buttons">
          <button v-if="projectCopy.author" @click="desvincularUsuario" class="button is-danger">
            <i class="fas fa-user-times"></i>&nbsp;Desvincular usuario</button>
        </div>
      </div>
      <div v-else>
        <div class="notification is-dark" v-if="isFetching">
          <i class="fas fa-spinner fa-spin"></i>&nbsp;Buscando ciudadano registrado con matrícula&nbsp;<b>{{projectCopy.author_dni}}</b>
        </div>
        <div class="notification is-dark" v-if="notFound">
          No se encuentra un usuario ciudadano registrado con matrícula&nbsp;<b>{{projectCopy.author_dni}}</b>
        </div>
        <div v-if="!projectCopy.author_dni" class="notification is-link">
          <i class="fas fa-times"></i>&nbsp;Debe completar el campo DNI en el formulario del proyecto para buscar un usuario
        </div>
        <div class="notification is-link" v-if="fetchedUser && foundUser">
          <i class="fas fa-check"></i>&nbsp;Se encontró un ciudadano registrado con matrícula&nbsp;<b>{{projectCopy.author_dni}}</b>
        </div>
        <table class="table is-fullwidth" v-if="fetchedUser && foundUser">
          <thead>
            <th>Apellidos, Nombres</th>
            <th>Apellidos, Nombres (Padron)</th>
            <th>Email</th>
            <th>DNI</th>
          </thead>
          <tbody>
            <tr>
              <td>
                <p>{{fetchedUser.user.surnames}}, {{fetchedUser.user.names}}</p>
                <p class="is-size-7"><i class="fas fa-check"></i>&nbsp; Es ciudadano empadronado</p>
              </td>
              <td>
                <p>{{fetchedUser.data.name}} ({{fetchedUser.year}})</p>
              </td>
              <td>{{fetchedUser.user.email}}</td>
              <td>{{fetchedUser.dni}}</td>
            </tr>
          </tbody>
        </table>
        <div class="buttons">
        <button v-if="fetchedUser && foundUser && !assigned" @click="vincular" class="button is-link">
          <i class="fas fa-user-edit"></i>&nbsp;Vincular usuario</button>
        </div>
      </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ['getUrl','submitUrl','project'],
  data(){
    return {
      fetchedUser: null,
      foundUser: false,
      isFetching: false,
      notFound: false,
      projectCopy: this.project,
      assigned: false,
      isLoading: false,
    }
  },
  created: function(){
    if(this.projectCopy.author_dni && !this.projectCopy.author){
    this.getUser(this.projectCopy.author_dni)
    }
  },
  methods:{
    getUser: function(dni){
      this.isFetching = true;
      this.$http
        .get(`${this.getUrl}?matricula=${dni}`)
        .then(({ data }) => {
          if(data){
            this.fetchedUser = data
          } else {
            this.notFound = true
          }
          this.foundUser = true
          this.isFetching = false
        })
        .catch(error => {
          this.notFound = true
          this.isFetching = false
          throw error
        });
    },
    vincular: function(){
      this.isLoading = true
      this.$http
        .post(this.submitUrl, {
          author_id: this.fetchedUser.user.id
        })
        .then(response => {
          this.assigned = true
          this.$buefy.snackbar.open({
            message: "Usuario vinculado al proyecto",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.isLoading = false
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$buefy.snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        });
    },
    desvincularUsuario(){
      this.isLoading = true
      this.$http
        .post(this.submitUrl, {
        })
        .then(response => {
          this.fetchedUser = null
          this.foundUser = false
          this.projectCopy.author = null
          this.getUser(this.projectCopy.author_dni)
          this.$buefy.snackbar.open({
            message: "Usuario desvinculado del proyecto",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.isLoading = false
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$buefy.snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        });
    }
  },
}
</script>

<style>

</style>

<template>
  <section>
    <b-autocomplete v-model="email" 
    :data="data" placeholder="Comience escribiendo el email" field="subject.display_name" 
    :loading="isFetching" @input="getAsyncData" @select="option => selected = option"
    size="is-medium">
      <template slot-scope="props">
        {{ props.option.subject.display_name }} - {{props.option.email}}
      </template>
      <template slot="empty">No se encontro un usuario con un email parecido a "{{email}}"</template>
    </b-autocomplete>
    <hr>
    <div class="notification has-text-centered" v-if="selected">
        <button class="delete" @click="selected = null"></button>
        <h2 class="title is-5">¿Desea agregar al usuario como administrador?</h2>
        <h2 class="subtitle is-6">{{ selected.subject.display_name }} - {{selected.email}}</h2>
        <button @click="submitAdmin" class="button is-primary is-fullwidth"><i class="fas fa-save fa-fw"></i>&nbsp;Agregar como administrador</button>
    </div>
    <div class="content">
      <table class="table">
        <thead>
          <tr>
            <th>Avatar</th>
            <th>Nombre y apellido</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
            <tr v-for="admin in currents" :key="admin.id">
              <td>
                <avatar :username="admin.subject.display_name" :size="30" :admin="true" />
              </td>
              <td>
                {{admin.subject.display_name}}</td>
              <td>{{admin.email}}</td>
            </tr>
            <tr v-if="currents.length == 0">
              <td colspan="3" class="has-text-centered">No hay administradores</td>
            </tr>
        </tbody>
      </table>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
import { debounce } from "lodash";
import Avatar from "../utils/Avatar";

export default {
  props: ["getCandidatesUrl", "runAdmin", "currents"],
  components: {
    Avatar
  },
  data: () => {
    return {
      admins: [],
      email: "",
      data: [],
      selected: null,
      isLoading: true,
      isFetching: false
    };
  },
  beforeMount: function() {
    // this.updateAdminList();
    this.isLoading = false;
  },
  methods: {
    submitAdmin: function() {
      this.isLoading = true;
      this.$http
        .post(this.runAdmin,{
            user_email: this.selected.email,
            role: 'admin'
        })
        .then(response => {
          this.$snackbar.open({
            message: "¡Nuevo administrador agregado!",
            type: "is-success",
            actionText: "OK"
          });
          window.location.reload();
        })
        .catch(error => {
          console.error(error.message);
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          this.isLoading = false;
        });
    },
    // updateAdminList: function(){
    //   this.isLoading = true;
    //   this.$http
    //     .get(this.urlGetAdmins)
    //     .then(response => {
    //       this.admins = response.data.data;
    //       this.isLoading = false;
    //     })
    //     .catch(error => {
    //       console.error(error.message);
    //       this.$snackbar.open({
    //         message: "Error inesperado",
    //         type: "is-danger",
    //         actionText: "Cerrar"
    //       });
    //       this.isLoading = false;
    //     });
    // },
    getAsyncData: debounce(function() {
      this.data = [];
      this.isFetching = true;
      this.$http
        .get(this.urlGetAdmins)
        .then(({ data }) => {
          data.forEach(item => this.data.push(item));
          this.isFetching = false;
        })
        .catch(error => {
          this.isFetching = false;
          throw error;
        });
    }, 500)
  },
  computed: {
    urlGetAdmins: function() {
      let query = [];
      query.push("email=" + this.email);
      return this.getCandidatesUrl.concat(
        query.length > 0 ? "?" : "",
        query.join("&")
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.table tr td {
  vertical-align: middle;
}
</style>


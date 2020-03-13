<template>
  <section>
    <div class="field has-addons">
      <div class="control">
    <a class="button is-static is-medium">
      <i class="fas fa-umbrella"></i>
    </a>
  </div>
      <div class="control is-expanded">
        <input type="text" v-model="message" class="input is-medium" data-vv-name="mensajeLluvia" data-vv-as="'Mensaje lluvia'" v-validate="'required'" placeholder="Ingrese un mensaje en caso de lluvia">
      </div>
    </div>
    <div class="is-clearfix">
      <button @click="submit()" class="button is-link is-pulled-right">
        <i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["value"],
  data() {
    return {
      isLoading: false,
      message: this.value
    };
  },
  methods: {
    submit: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$snackbar.open({
            message: "No puede dejar el mensaje en caso de lluvia vacio!",
            type: "is-danger",
            actionText: "Ok"
          });
        } else {
          this.isLoading = true;
          this.$http
            .post("/api/admin/option/rain", this.payload)
            .then(response => {
              window.location.href = "/logout";
            })
            .catch(x => {
              this.isLoading = false;
              this.$snackbar.open({
                message: "Error inesperado",
                type: "is-danger",
                actionText: "Cerrar"
              });
              return false;
            });
        }
      });
    }
  },
  computed: {
    payload: function() {
      return {
        value: this.message
      };
    }
  }
};
</script>

<style>
</style>

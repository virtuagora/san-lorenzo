<template>
  <section>
    <div class="buttons has-addons">
      <span class="button" @click="available = true" :class="{'is-primary': available}">Habilitado</span>
      <span class="button" @click="available = false" :class="{'is-primary': !available}">Deshabilitado</span>
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
      available: this.value
    };
  },
  methods: {
    submit: function() {
      this.isLoading = true;
      this.$http
        .post("/api/admin/option/proposals-available", this.payload)
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
  },
  computed: {
    payload: function() {
      return {
        value: this.available ? 1 : 0
      };
    }
  }
};
</script>

<style>
</style>

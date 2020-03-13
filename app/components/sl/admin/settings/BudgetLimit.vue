<template>
  <section>
    <div class="field has-addons">
      <div class="control">
    <a class="button is-static is-medium">
      <i class="fas fa-dollar-sign"></i>
    </a>
  </div>
      <div class="control">
        <input type="text" v-model="theBudget" class="input is-medium" data-vv-name="limitePresupuesto" data-vv-as="'Limite de Presupuesto'" v-validate="'required|integer|min_value:10'" placeholder="Ingrese un valor, sin decimales">
        <span class="help is-danger" v-show="errors.has('limitePresupuesto')">
          <i class="fas fa-times-circle fa-fw"></i> {{errors.first('limitePresupuesto')}}</span>
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
      theBudget: this.value
    };
  },
  methods: {
    submit: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$snackbar.open({
            message: "Limite de presupuesto invalido",
            type: "is-danger",
            actionText: "Ok"
          });
        } else {
          this.isLoading = true;
          this.$http
            .post("/api/admin/option/budget-limit", this.payload)
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
        value: this.theBudget
      };
    }
  }
};
</script>

<style>
</style>

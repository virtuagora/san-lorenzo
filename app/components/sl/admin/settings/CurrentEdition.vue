<template>
  <section>
    <div class="field has-addons">
      <div class="control">
    <a class="button is-static is-medium">
      <i class="fas fa-calendar-alt"></i>
    </a>
  </div>
      <div class="control">
        <input type="text" v-model="theYear" class="input is-medium" data-vv-name="edicionActual" data-vv-as="'Edición actual'" v-validate="'required|integer|min_value:2000|max_value:2050'" placeholder="Ingrese un año">
        <span class="help is-danger" v-show="errors.has('edicionActual')">
          <i class="fas fa-times-circle fa-fw"></i> {{errors.first('edicionActual')}}</span>
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
      theYear: this.value
    };
  },
  methods: {
    submit: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$buefy.snackbar.open({
            message: "Tiene que ser un año valido",
            type: "is-danger",
            actionText: "Ok"
          });
        } else {
          this.isLoading = true;
          this.$http
            .post("/api/admin/option/current-edition", this.payload)
            .then(response => {
              window.location.href = "/logout";
            })
            .catch(x => {
              this.isLoading = false;
              this.$buefy.snackbar.open({
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
        value: this.theYear
      };
    }
  }
};
</script>

<style>
</style>
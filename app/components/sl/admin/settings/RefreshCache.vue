<template>
  <section>
    <div class="field has-addons">
      <div class="control">
    <a class="button is-static is-medium">
      <i class="fas fa-calendar-alt"></i>
    </a>
  </div>
      <div class="control">
        <input type="text" v-model="theCache" class="input is-medium" data-vv-name="cachekey" data-vv-as="'Edición actual'" v-validate="'required|min:2|max:50'" placeholder="Ingrese alguna cadena de caracteres random">
        <span class="help is-danger" v-show="errors.has('cachekey')">
          <i class="fas fa-times-circle fa-fw"></i> {{errors.first('cachekey')}}</span>
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
      theCache: this.value
    };
  },
  methods: {
    submit: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$snackbar.open({
            message: "cachekey no cumple con los requisitos, (min 2 caracteres max 50)",
            type: "is-danger",
            actionText: "Ok"
          });
        } else {
          this.isLoading = true;
          this.$http
            .post("/api/admin/option/refresh-cache", this.payload)
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
        value: this.theCache
      };
    }
  }
};
</script>

<style>
</style>
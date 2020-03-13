<template>
  <section>
    <div v-if="response.ok === false">
      <div class="field">
        <label class="label">Contraseña actual *</label>
        <div class="control">
          <input
            type="password"
            v-model="current"
            name="current"
            v-validate="'required|min:6'"
            class="input"
            ref="current"
            style
            placeholder="Ingresá tu contraseña actual"
          >
          <span class="help is-danger" v-show="errors.has('current')">
            <i class="fas fa-times-circle fa-fw"></i> Error. La contraseña no puede ser vacia, (Mínimo 6 caracteres)
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Nueva contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="password"
            name="password"
            v-validate="'required|min:6'"
            class="input"
            ref="password"
            style
            placeholder="Ingresá tu nueva contraseña"
          >
          <span class="help is-danger" v-show="errors.has('password')">
            <i class="fas fa-times-circle fa-fw"></i> Error. La contraseña no puede ser vacia, (Mínimo 6 caracteres)
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Repetí tu nueva contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="repeat"
            name="repeat"
            v-validate="'required|confirmed:password'"
            class="input"
            style
            placeholder="Reescribi tu nueva contraseña"
          >
          <span class="help is-danger" v-show="errors.has('repeat')">
            <i class="fas fa-times-circle fa-fw"></i> Error. Las contraseñas no coinciden
          </span>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button
            @click="submitReset"
            class="button is-primary is-fullwidth"
            :class="{'is-loading': isLoading}"
          >Cambiar contraseña</button>
        </div>
      </div>
    </div>
    <div class="notification is-success" v-if="response.ok">
      <i class="fas fa-check fa-fw"></i> Datos enviados y guardados con éxito
    </div>
    <b-loading :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["url"],
  data() {
    return {
      password: null,
      repeat: null,
      isLoading: false,
      user: {},
      response: {
        ok: false,
        replied: false
      }
    };
  },
  methods: {
    submitReset: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error. Verifique los campos.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$http
            .post(this.url, {
              current: this.current,
              new: this.password,
              repeat: this.repeat
            })
            .then(response => {
              this.$snackbar.open({
                message: "Se cambio la contraseña correctamente",
                type: "is-success",
                actionText: "OK"
              });
              this.isLoading = false;
              this.response.replied = true;
              this.response.ok = true;
            })
            .catch(error => {
              console.error(error.message);
              this.isLoading = false;
              // this.response.replied = true;
              // this.response.ok = false;
              this.$snackbar.open({
                message: "Ocurrio un error: " + error.message,
                type: "is-danger",
                actionText: "Cerrar"
              });
              return false;
            });
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  },
  created: function() {
    this.user = this.$store.state.user;
  },
  computed: {
    payload: function() {
      return {
        email: this.email
      };
    }
  }
};
</script>


<style>
</style>

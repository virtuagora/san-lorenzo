<template>
  <div>
    <div v-if="!response.replied">
      <p>¡Aquí vas a poder reestablecer tu contraseña!</p>
      <p>Completá los campos para hacerlo</p>
      <br>
      <div class="field">
        <label class="label">Contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="password"
            name="password"
            ref="password"
            v-validate="'required|min:6'"
            class="input has-text-centered is-medium"
            style="padding-left:0"
            placeholder="Ingresá tu contraseña"
          >
          <span class="help is-danger" v-show="errors.has('password')">
            <i class="fas fa-times-circle fa-fw"></i> Error. La contraseña no puede ser vacia, (Mínimo 6 caracteres)
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Confirmar contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="repeat"
            name="repeat"
            v-validate="'required|confirmed:password'"
            class="input has-text-centered is-medium"
            style="padding-left:0"
            placeholder="Volvé a ingresar tu contraseña"
          >
          <span class="help is-danger" v-show="errors.has('repeat')">
            <i class="fas fa-times-circle fa-fw"></i> Error. Las contraseñas no coinciden
          </span>
        </div>
      </div>
      <br>
      <div class="field">
        <div class="control">
          <button
            @click="submitReset"
            class="button is-medium is-primary is-fullwidth"
            :class="{'is-loading': isLoading}"
          >
            <i class="fas fa-user-plus"></i>&nbsp;&nbsp;Reiniciar contraseña
          </button>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="notification is-success" v-if="response.replied && response.ok">
        <i class="fas fa-check fa-lg fa-fw"></i>
        ¡La contraseña ha sido reiniciada!<br> Continue iniciando sesión
      </div>
      <div class="buttons" v-if="response.replied && response.ok">
         <a :href="logInUrl" class="button is-primary is-medium is-fullwidth">
          <i class="fas fa-sign-in-alt fa-lg"></i>&nbsp;&nbsp;Iniciar sesión
        </a>
      </div>
      <div class="notification is-danger" v-if="response.replied && !response.ok">
        <i class="fas fa-times fa-lg fa-fw"></i>
        Error al cambiar la contraseña. Por favor vuelva a intentarlo. Si el problema persiste, contactesé enviando un mail a presupuestoparticipativo.sl@gmail.com o comunicandose por teléfono +54 9 3476 418-681
      </div>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </div>
</template>

<script>
export default {
  props: ["resetUrl", "logInUrl", "token", "user"],
  data() {
    return {
      password: null,
      repeat: null,
      isLoading: false,
      response: {
        replied: null,
        ok: null,
        message: null
      }
    };
  },
  methods: {
    submitReset: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$buefy.snackbar.open({
              message: "Error. Verifique los campos.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$http
            .post(this.resetUrl, {
              token: this.token,
              password: this.password,
              repeat: this.repeat
            })
            .then(response => {
              this.$buefy.snackbar.open({
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
              this.$buefy.snackbar.open({
                message: "Ocurrio un error: " + error.message,
                type: "is-danger",
                actionText: "Cerrar"
              });
              return false;
            });
        })
        .catch(error => {
          this.$buefy.snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  }
};
</script>

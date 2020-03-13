<template>
  <div>
    <div v-if="!response.replied">
      <p class="has-text-centered">
        Ingresa tu dirección de email, te enviaremos un correo con un link para que completes tu registro. Recuerde que <b>solo habitantes de San Lorenzo pueden registrarse</b>.
      </p>
      <br>
      <div class="field">
        <div class="control has-text-centered">
          <input type="email" name="email" v-model="email" v-validate="'required|email'" class="input is-medium has-text-centered"  data-vv-validate-on="change" placeholder="Ingresá tu email">
          <span class="help is-danger" v-show="errors.has('email')">
            <i class="fas fa-times-circle fa-fw"></i> Error. Debe ser un email bien formado</span>
        </div>
      </div>
      <br>
      <div class="field">
        <div class="control" style="display: inline-block;">
          <vue-recaptcha :sitekey="googleKey" @verify="onVerify" @expired="onExpired"></vue-recaptcha>
        </div>
      </div>
      <br>
      <div class="field">
        <div class="control">
          <button @click="submitSignUp" :disabled="!recaptcha || errors.has('email')" class="button is-large is-primary is-rounded is-fullwidth" :class="{'is-loading': isLoading}">
            <i class="fas fa-paper-plane"></i>&nbsp;&nbsp;Enviar email</button>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button @click="$emit('abort')" class="button is-white is-fullwidth">
            <i class="fas fa-undo"></i>&nbsp;&nbsp;Volver atrás
          </button>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="notification is-success" v-show="response.replied && response.ok">
        <i class="fas fa-check fa-lg fa-fw"></i>
        El email ha sido enviado a tu casilla de correo
        <b>{{email}}</b>. Revisá tu casilla y entrá al link que hemos enviado.
      </div>
      <div class="notification is-danger is-size-7" v-show="response.replied && !response.ok">
        <i class="fas fa-times fa-lg fa-fw"></i>
        Error enviando el email a "{{email}}". ¿Ya se encuentra registrado con este email? Verifique su conexión de internet e intente más tarde. Si el problema persiste, contactesé con la Unidad de Presupuesto Participativo (presupuestoparticipativo.sl@gmail.com)
      </div>
    </div>
    <b-loading :active.sync="isLoading"></b-loading>
  </div>
</template>

<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  props: ["signUpUrl", "googleKey"],
  components: {
    VueRecaptcha
  },
  data() {
    return {
      email: "",
      recaptcha: "",
      isLoading: false,
      response: {
        replied: null,
        ok: null,
        message: null
      }
    };
  },
  methods: {
    onVerify: function(response) {
      console.log("Verify: " + response);
      this.recaptcha = response;
    },
    onExpired: function() {
      console.log("Expired");
      this.recaptcha = null;
    },
    submitSignUp: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error. Verifique si el email está bien escrito.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$http
            .post(this.signUpUrl, {
              identifier: this.email,
              recaptcha: this.recaptcha
            })
            .then(response => {
              window.gtag('event', 'newPendingUser', {
                'event_category' : 'Sign Up',
                'event_label' : 'New user pending for valitation'
              });
              this.isLoading = false;
              this.response.replied = true;
              this.response.ok = true;
            })
            .catch(error => {
              window.gtag('event', 'newPendingUserFailed', {
                'event_category' : 'Sign Up',
                'event_label' : 'Failed to create a new user (Email already in use?)'
              });
              console.error(error.message);
              this.isLoading = false;
              this.response.replied = true;
              this.response.ok = false;
              this.$snackbar.open({
                message: "Error inesperado",
                type: "is-danger",
                actionText: "Cerrar"
              });
              return false;
            });
        })
        .catch(error => {
           window.gtag('event', 'newPendingUserError', {
                'event_category' : 'Sign Up',
                'event_label' : 'Unexpected error'
          });
          this.$snackbar.open({
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

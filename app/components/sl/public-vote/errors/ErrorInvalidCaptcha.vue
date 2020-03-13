<template>
  <section class>
    <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt />
    <div class="notification is-warning has-text-centered">
      <p>
        <i class="fas fa-exclamation-triangle fa-lg"></i>
      </p>
      <p>&nbsp;Error verificando el código "No soy un robot"</p>
      <p>Por favor, vuelva a hacer el desafio "No soy un robot" y vuelva a intentarlo</p>
    </div>
    <div class="field">
      <div class="control">
        <vue-recaptcha :sitekey="googleKey" @verify="onVerify" @expired="onExpired"></vue-recaptcha>
      </div>
    </div>
    <button @click="reSend" :disabled="!recaptcha" class="button is-primary is-medium is-rounded">
      <i class="fas fa-paper-plane fa-fw"></i>&nbsp;Volver a enviar
    </button>
  </section>
</template>


<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  props: ["googleKey"],
  components: {
    VueRecaptcha
  },
  data() {
    return {
      recaptcha: ""
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
    reSend: function() {
      this.$store.commit("bind", {
        recaptcha: this.recaptcha
      });
      this.$router.push({ name: "PublicSending" });
    }
  }
};
</script>

<style>
</style>

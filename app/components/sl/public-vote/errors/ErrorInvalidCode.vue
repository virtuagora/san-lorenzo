<template>
  <section class>
    <img src="/assets/img/pp-logo-horizontal.svg" class="image login-logo" alt />
    <div class="notification is-warning has-text-centered">
      <p>
        <i class="fas fa-exclamation-triangle fa-lg"></i>
      </p>
      <p>
        Hubo un problema con el código
        <b>
          <span class="has-text-black">{{publicVoteCode}}</span>
        </b> para votar
      </p>
    </div>
    <div class="content has-text-centered-desktop">
      <p class="is-size-7">El mismo puede ser causado por varias razones, entre algunas:</p>
      <ul>
        <li class="is-size-7">Se envió un código vacio</li>
        <li class="is-size-7">El codigo es invalido</li>
      </ul>
      <p>Le pedimos que vuelva a ingresar el código</p>
    </div>
    <div class="columns is-multiline">
      <div class="column is-12-tablet is-6-desktop">
        <h1 class="title is-4 has-text-centered-desktop">Ingrese un código</h1>
        <div class="field">
          <div class="control">
            <the-mask
              mask="KKKKKK"
              :tokens="hexTokens"
              v-model="inputCode"
              :masked="true"
              @keyup.native.enter="forward"
              class="input is-large code-input spaced-code"
              placeholder="******"
            />
          </div>
        </div>
      </div>
      <div class="column is-12-tablet is-6-desktop">
        <h1 class="title is-5 has-text-centered-desktop">Complete el desafio</h1>
        <div class="field">
          <div class="control">
            <vue-recaptcha
              :sitekey="googleKey"
              @verify="onVerify"
              @expired="onExpired"
            ></vue-recaptcha>
          </div>
        </div>
      </div>
    </div>
    <div class="buttons is-hidden-desktop">
      <button
        @click="reSend"
        :disabled="!recaptcha || inputCode == null || inputCode.length < 6"
        class="button is-primary is-medium is-rounded"
      >
        <i class="fas fa-paper-plane fa-fw"></i>&nbsp;Volver a enviar
      </button>
    </div>
    <div class="buttons is-hidden-touch is-centered">
      <button
        @click="reSend"
        :disabled="!recaptcha || inputCode == null || inputCode.length < 6"
        class="button is-primary is-medium is-rounded"
      >
        <i class="fas fa-paper-plane fa-fw"></i>&nbsp;Volver a enviar
      </button>
    </div>
    <p class="is-size-7 has-text-centered-desktop has-text-centered-desktop">
      Si el problema persiste o si queres reportar un problema, contactate con nosotros en nuestro
      <b>
        <a href="https://www.facebook.com/PresupuestoParticipativoSanLorenzo/">Facebook</a>
      </b> o enviando un email a
      <u>
        <a href="mailto:presupuestoparticipativo.sl@gmail.com">presupuestoparticipativo.sl@gmail.com</a>
      </u>
    </p>
  </section>
</template>


<script>
import VueRecaptcha from "vue-recaptcha";
import { TheMask } from "vue-the-mask";

export default {
  props: ["googleKey"],
  components: {
    VueRecaptcha,
    TheMask
  },
  data() {
    return {
      recaptcha: "",
      inputCode: null,
      hexTokens: {
        K: {
          pattern: /[0-9a-zA-Z]/,
          transform: v => v.toLocaleUpperCase()
        }
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
    reSend: function() {
      if (this.inputCode == null || this.inputCode.length < 6) return;
      if (!this.recaptcha.length) return;
      this.$store.commit("bind", {
        publicVoteCode: this.inputCode,
        recaptcha: this.recaptcha
      });
      this.$router.push({ name: "PublicSending" });
    }
  },
  computed: {
    publicVoteCode: function() {
      return this.$store.state.publicVoteCode;
    }
  }
};
</script>

<style scoped>
section {
  width: 100%;
}
.code-input {
  font-weight: 800;
  text-align: center;
  font-size: 3.2rem;
  /* width: 300px; */
  margin: 0 auto;
  padding: 10px;
  height: 5rem;
}
.code-logo {
  width: 160px;
  margin: 0 auto 15px;
}
</style>

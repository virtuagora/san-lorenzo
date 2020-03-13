<template>
  <section class="has-text-centered">
    <img src="/assets/img/pp-logo-horizontal.svg" class="image code-logo" alt="">
    <h1 class="title is-4">Ingrese el código</h1>
    <div class="field">
      <div class="control has-text-centered">
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
    <br />
    <button
      @click="forward"
      :disabled="inputCode == null || inputCode.length < 6"
      class="button is-primary is-rounded is-medium"
    >
      Comenzar&nbsp;
      <i class="fas fa-arrow-right fa-fw"></i>
    </button>
  </section>
</template>

<script>
import { TheMask } from "vue-the-mask";

export default {
  components: {
    TheMask
  },
  data() {
    return {
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
    forward: function() {
      if(this.inputCode == null || this.inputCode.length < 6) return
      this.$store.commit("bind", {
        publicVoteCode: this.inputCode
      });
      this.$router.push({ name: "PublicHello" });
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
  width: 300px;
  margin: 0 auto;
  padding: 10px;
  height: 5rem;
}
.code-logo{
  width: 160px;
  margin: 0 auto 15px;
}
</style>
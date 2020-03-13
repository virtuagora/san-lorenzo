<template>
    <section>
    <img src="/assets/img/pp-logo-horizontal.svg" class="image logo-small-pp-voting is-pulled-right" alt="">
    <h1 class="title is-3">4. Envio</h1>
    <h1 class="subtitle is-5">¿Listo para enviar?</h1>
    <article class="media">
      <div class="media-left">
        <h1 class="title is-2 is-marginless"><i class="fas fa-info-circle"></i></h1>
      </div>
      <div class="media-content">
        <div class="content">
    <p>A continuación vamos a enviar tu voto. Te recordamos lo siguiente</p>
          <ul>
            <li>Tu voto será almacenado de forma <u>ANÓNIMA</u>, quitándole cualquier dato que pueda vincularlo con vos.</li>
            <li>A fines estadísticos almacenamos en paralelo datos disociados, como género, año de nacimiento y fecha de votación.</li>
            <li>Recordá que una vez que enviás tu voto online no vas a poder votar de forma presencial, ya que se actualiza un padrón único digital.</li>
            <li>Por cualquier consulta que tengas, podés ponerte en contacto con el equipo de Presupuesto Participativo.</li>
          </ul>
        </div>
      </div>
    </article>
    <hr>
    <div class="">
      <h1 class="title has-text-primary is-4">¿Todo listo? ¡Haga clic para enviar su voto!</h1>
      <div class="field">
      <div class="control">
          <vue-recaptcha :sitekey="googleKey" @verify="onVerify" @expired="onExpired"></vue-recaptcha>
        </div>
      </div>
    <button @click="confirm" :disabled="!recaptcha" class="button is-primary is-medium is-rounded">
      <i class="fas fa-paper-plane fa-fw "></i>&nbsp;¡Enviar!
    </button>
    </div>
  </section>
</template>

<script>
import VueRecaptcha from "vue-recaptcha";
export default {
  props: ["googleKey"],
  components: {
    VueRecaptcha
  },
  data(){
    return {
      recaptcha: ""
    }
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
      confirm: function(){
        this.$store.commit("bind", {
          recaptcha: this.recaptcha
        });
        this.$router.push({ name: 'PublicSending'})
      }
  }
}
</script>


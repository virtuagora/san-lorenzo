<template>
<div>
  <div class="box">
    <div v-show="!sending && !sent">
      <p>So great to know you want to participate!</p>
      <p>First, we will ask your email address so we can send you and validate your account</p>
    </div>
    <div v-show="sending && !sent">
      <p class="has-text-primary">Sending the email to your inbox, hang in there!</p>
    </div>
    <div v-show="sent">
      <p class="has-text-success">Success!</p>
      <p>{{successMessage}}</p>
    </div>
      <div class="field" v-show="!sending && (!sent && !success)">
        <div class="control has-icons-left">
          <input type="text" name="email" v-model="email" class="input is-medium" placeholder="Your email address" :disabled="sending">
          <span class="icon is-left">
            <i class="far fa-envelope fa-lg"></i>
          </span>
        </div>
        <p class="help is-info is-hidden">This email is invalid</p>
      </div>
      <div class="field" v-show="!sent && !success">
        <div class="control">
          <button @click="signUp()" :class="{'button is-primary is-fullwidth': true, 'is-loading': sending }" :disabled="email == ''">Sign up</button>
        </div>
      </div>
  </div>
  <div class="control has-text-centered">
        <a href="/login" class="">
          <b>Already a user?</b><br>Log in now!</a>
        <br>
        <br>
        <a :href="homeUrl" class="button is-secondary ">Return home</a>
      </div>
</div>      
</template>

<script>
export default {
  name: "sign-up-component",
  props: ["message", "formUrl", "loginUrl", "homeUrl"],
  data() {
    return {
      errorMessage: null,
      successMessage: null,
      email: "",
      sending: false,
      sent: false,
      success: false
    };
  },
  methods: {
    signUp: function() {
      this.sending = true;
      this.$http
        .post(this.formUrl, {"email":this.email})
        .then(response => {
          console.log(response.data.message);
          this.successMessage = response.data.message
          this.success = true
          this.sending = false          
          this.sent = true
        })
        .catch(error => {
          console.log(error.data.message);
          this.errorMessage = error.data.message
          this.success= false
          this.sending = false          
          this.sent = true
        });
    }
  }
};
</script>

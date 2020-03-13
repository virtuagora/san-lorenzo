<template>
  <div id="login-component">
    <div class="box">
      <article v-show="errorMessage || successMessage" v-bind:class="{'message' : true, 'is-small' : true, 'is-success' : successMessage, 'is-danger' : errorMessage }">
        <div class="message-body">
          hello, {{message}}!
        </div>
      </article>
      <form :action="formUrl" method="POST">
        <div class="field">
          <div class="control has-icons-left">
            <input type="text" name="email" v-model="userEmail" class="input is-medium" placeholder="Your email address">
            <span class="icon is-left">
              <i class="far fa-envelope fa-lg"></i>
            </span>
          </div>
          <p class="help is-info is-hidden">This email is invalid</p>
        </div>
        <div class="field">
          <div class="control has-icons-left">
            <input type="password" name="password" v-model="userPassword" class="input is-medium" placeholder="Your password">
            <span class="icon is-left">
              <i class="fas fa-key fa-lg"></i>
            </span>
          </div>
          <p class="help is-danger is-hidden">This email is invalid</p>
        </div>
        <br>
        <div class="field">
          <div class="control has-text-centered">
            <button :class="{'button is-primary is-fullwidth': true, 'is-loading': sending }" :disabled="userEmail == '' && userPassword == ''">Log in</button>
          </div>
        </div>
        <div class="field">
          <div class="control has-text-centered">
            Are you new?
            <a :href="signUpUrl" class="has-text-primary">Sign up!</a>
          </div>
        </div>
      </form>
    </div>
      <div class="control is-clearfix has-text-centered">
        <a @click="" class="">
          <b>Forgot your password?</b><br>We can help you</a>
        <br>
        <br>
        <a :href="homeUrl" class="button is-secondary ">Return home</a>
      </div>
  </div>
</template>

<script>
export default {
  name: "login-component",
  props: ["message", "formUrl", "signUpUrl", "homeUrl"],
  data() {
    return {
      userEmail: "",
      userPassword: "",
      errorMessage: null,
      successMessage: null,
      sending: false
    };
  },
  methods: {
    submit: function(){
      console.log(':)')
    }
  }
};
</script>


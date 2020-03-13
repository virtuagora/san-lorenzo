<template>
    <div>
        <div class="box">
            <div v-show="!sending && !sent">
                <p>Welcome! Thanks for validating your email address</p>
                <p>To start, please complete the following information about you</p>
            </div>
            <article v-show="errorMessage || successMessage" class="message has-text-centered" :class="{'is-success' : successMessage, 'is-danger' : errorMessage }">
                <div class="message-body">
                    {{successMessage}}
                </div>
            </article>
            <div v-show="!sent">
                <div class="field">
                    <label class="label">Name and Surname</label>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input class="input is-medium" type="text" placeholder="My name" v-model.lazy="user.names">
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input is-medium" type="text" placeholder="My surname" v-model.lazy="user.surnames">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input is-medium" type="password" placeholder="My password" v-model="user.password">
                        <p class="help is-danger" v-show="!requiredPassword">You need to provide a password</p>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Write your password again</label>
                    <div class="control">
                        <input class="input is-medium" type="password" placeholder="Repeat password" v-model="confirmPassword">
                        <p class="help is-danger" v-show="!passwordMatch">Passwords do not match.</p>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <label class="label">Agreement</label>
                        <label class="checkbox">
                            <input type="checkbox" @click="agreement = !agreement" :checked="agreement"> I've read and I agree with the
                            <a href="#" class="has-text-link">terms and conditions</a> which includes the code of conduct. At the same time, I also agree with the
                            <a href="#" class="has-text-link">privacy policy</a> of this plataform.
                        </label>
                    </div>
                </div>
            </div>
            <div class="field" v-if="!sent">
                <br>
                <div class="control">
                    <button @click="completeRegistration()" :class="{'button is-primary is-fullwidth': true, 'is-loading': sending }" :disabled="!fieldsCompleted">Save and continue</button>
                </div>
            </div>
            <div class="buttons is-centered" v-else>
                <a :href="loginUrl" class="button is-primary">Log In</a>
                <a :href="homeUrl" class="button is-white">Return to Home</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "sign-up-component",
  props: ["message", "formUrl", "loginUrl", "homeUrl", "activationKey"],
  data() {
    return {
      errorMessage: null,
      successMessage: null,
      user: {
        names: "",
        surnames: "",
        password: "",
        activation_key: ""
      },
      confirmPassword: "",
      agreement: false,
      completedFields: false,
      sending: false,
      sent: false
    };
  },
  created: function() {
    this.user.activation_key = this.activationKey;
  },
  methods: {
    completeRegistration: function() {
      if (!this.checkCompleteFields()) {
        return false;
      }
      this.sending = true;
      this.$http
        .post(this.formUrl, this.user)
        .then(response => {
          console.log(response);
          this.successMessage = response.data.message;
          this.sending = false;
          this.sent = true;
        })
        .catch(error => {
          console.log(error);
          this.errorMessage = error.data.message;
          this.sending = false;
          this.sent = true;
        });
    },
    checkCompleteFields: function() {
      if (
        this.agreement &&
        this.user.names &&
        this.user.surnames &&
        this.user.password &&
        this.confirmPassword
      ) {
        return true;
      }
      return false;
    }
  },
  computed: {
    requiredPassword: function() {
      if (this.user.password != "") {
        return true;
      }
      return false;
    },
    passwordMatch: function() {
      if (this.user.password == this.confirmPassword) {
        return true;
      }
      return false;
    },
    fieldsCompleted: function() {
      if (
        this.agreement &&
        this.user.names &&
        this.user.surnames &&
        this.user.password &&
        this.confirmPassword
      ) {
        return true;
      }
      return false;
    }
  },
  watch: {}
};
</script>

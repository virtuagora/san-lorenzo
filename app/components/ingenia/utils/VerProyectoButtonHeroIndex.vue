<template>
  <div v-if="showButton" class="column is-narrow">
    <b-tooltip label="Ir a mi proyecto" type="is-dark" position="is-bottom">
      <a :href="'/proyecto/'+this.user.groups[0].project.id" class="">
        <span class="icon is-large has-text-white">
          <i class="fas fa-file-powerpoint fa-3x "></i>
        </span>&nbsp;&nbsp;
      </a>
    </b-tooltip>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: this.$store.state.user
    };
  },
  mounted: function() {
    this.user = this.$store.state.user;

    let intervalId = setInterval(
      function() {
        if (this.user == null) {
        console.log("Tick!");
          this.user = this.$store.state.user;
        } else {
        console.log("STOP!");          
          clearInterval( intervalId );
        }
      }.bind(this),
      2000
    );
  },
  computed: {
    showButton: function() {
      return (
        this.user !== null &&
        this.user.groups[0] !== undefined &&
        this.user.groups[0].project !== null
      );
    }
  }
};
</script>

<style>

</style>

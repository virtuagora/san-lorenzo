<template>
  <span v-if="isLoading">
    <i class="fas fa-cog fa-spin"></i>&nbsp;Cargando . . .
  </span>
  <span v-else>
    {{user.surnames}}, {{user.names}}
  </span>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      isLoading: false,
      user: null
    };
  },
  created: function() {
    this.isLoading = true;
    this.$http
      .get("/user/" + this.id)
      .then(response => {
        this.isLoading = false;
        this.user = response.data;
      })
      .catch(e => {
        console.error(e);
      });
  }
};
</script>

<style>

</style>

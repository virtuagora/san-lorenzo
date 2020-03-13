<template>
   <span v-if="isLoading">
    <i class="fas fa-cog fa-spin"></i>&nbsp;Cargando . . .
  </span>
  <span v-else>
    {{group.name}}
  </span>
</template>

<script>
export default {
  props: ['id'],
  data(){
    return {
      isLoading: false,
      group: {
        name: null
      }
    }
  },
  mounted: function(){
     this.isLoading = true;
    this.$http.get("/group/" + this.id).then(response => {
      this.isLoading = false;
      this.group = response.data;
    }).catch(e => {
      console.error(e)
    });
  }
}
</script>

<style>

</style>

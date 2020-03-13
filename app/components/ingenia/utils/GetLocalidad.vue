<template>
  <div v-if="isLoading">
    <i class="fas fa-cog fa-spin"></i>&nbsp;Cargando . . .
  </div>
  <div v-else>
    <p><i class="fas fa-map-marker "></i>&nbsp;{{locality.custom ? 'Otra (' + localityOther + ')' : locality.name}}, {{locality.department.name}}, Región {{locality.department.region.region}} ({{locality.department.region.name}})</p>
  </div>
</template>

<script>
export default {
  props: ["localityId", "localityOther"],
  data() {
    return {
      isLoading: true,
      locality: {}
    };
  },
  mounted: function() {
    this.isLoading = true;
    this.$http.get("/locality/" + this.localityId).then(response => {
      this.isLoading = false;
      this.locality = response.data;
    }).catch(e => {
      console.error(e)
    });
  }
};
</script>

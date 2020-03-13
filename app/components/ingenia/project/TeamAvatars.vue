<template>
  <div class="hero is-small is-light">
    <div class="hero-body">
      <div class="container has-text-centered">
        <div class="list-of-avatars">
          <figure v-show="members.length === 0" class="image is-64x64"></figure>
          <b-tooltip v-for="member in members" :key="member.id" :label="member.names + ' ' + member.surnames" type="is-dark" position="is-bottom">
            <Avatar :user="member" class="inline-image" size="64"></Avatar>&nbsp;&nbsp;
          </b-tooltip>
        </div>
        <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
      </div>
    </div>
  </div>
</template>

<script>
import Avatar from "../utils/Avatar";
export default {
  props: ["id", "getGroupMembers", "teamUrl"],
  components: {
    Avatar
  },
  data() {
    return {
      isLoading: true,
      members: []
    };
  },
  mounted: function() {
    Promise.all([
      this.$http.get(this.fetchTeamUrl),
      this.$http.get(this.fetchGroupMembers)
    ])
      .then(responses => {
        this.group = responses[0].data;
        this.members = responses[1].data;
        this.$store.commit("bind", { group: responses[0].data });
        this.$store.commit("bind", { members: responses[1].data });
        this.isLoading = false;
      })
      .catch(error => {
        console.error(error.message);
        this.isLoading = false;
        this.$snackbar.open({
          message:
            "Error al obtener los integrantes del equipo. Recargue la página",
          type: "is-danger",
          actionText: "Cerrar"
        });
      });
  },
  computed: {
    fetchGroupMembers: function() {
      return this.getGroupMembers.replace(":gro", this.id);
    },
    fetchTeamUrl: function() {
      return this.teamUrl.replace(":gro", this.id);
    }
  }
};
</script>

<style>

</style>

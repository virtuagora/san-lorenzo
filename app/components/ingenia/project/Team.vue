<template>
  <section class="section">
    <div class="container" v-if="!isLoading">
      <h1 class="subtitle is-4 has-text-centered">Te presentamos a...</h1>
      <h1 class="title is-2 has-text-centered">{{project.group.name}}</h1>
      <br>
      <div class="columns">
        <div class="column">
          <article class="media" v-for="member in members" :key="member.id">
            <Avatar :user="member" class="media-left" size="96"></Avatar>
            <div class="media-content" style="overflow: inherit">
              <h1 class="is-size-3 is-600">{{member.names}} {{member.surnames}}
                <b-tooltip label="Responsable" type="is-warning" position="is-bottom" v-if="member.pivot.relation === 'responsable'">
                <i class="fas fa-star has-text-warning"></i>
                </b-tooltip>
                <b-tooltip label="Co-responsable" type="is-info" position="is-bottom" v-if="member.pivot.relation === 'co-responsable'">
                <i class="fas fa-shield-alt has-text-info"></i>
                </b-tooltip>
              </h1>
              <p class="nl2br" v-if="member.bio">{{member.bio}}</p>
              <p v-else><i>- Sin información -</i></p>
            </div>
          </article>
        </div>
        <div class="column">
          <div v-if="group != null">
            <div class="content">
              <h5>
                <b>Acerca del equipo</b>
              </h5>
              <p>{{group.description}}</p>
              <h5>
                <b>Ubicación</b>
              </h5>
              <Localidad :locality-id="group.locality_id" :locality-other="group.locality_other"></Localidad>
              <br>
              <table class="table is-narrow is-bordered">
                <tbody>
                  <tr>
                    <th>Año de fundación</th>
                    <td>{{group.year}}</td>
                  </tr>
                  <tr>
                    <th>
                      Participaciones anteriores
                    </th>
                    <td>
                      {{group.previous_editions.length ? group.previous_editions.join(', ') : 'No registra'}}
                    </td>
                  </tr>
                  <tr v-if="group.email">
                    <th>Email de contacto</th>
                    <td>{{group.email}}</td>
                  </tr>
                  <tr v-if="group.telephone">
                    <th>Telefono de contacto</th>
                    <td>{{group.telephone}}</td>
                  </tr>
                  <tr>
                    <th>Página web</th>
                    <td>{{group.web ? group.web : 'No registra'}}</td>
                  </tr>
                  <tr>
                    <th>Facebook</th>
                    <td>{{group.facebook ? group.facebook : 'No registra'}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="group.parent_organization">
              <h1 class="subtitle is-5">El equipo es parte de una organización</h1>
              <h1 class="title is-3">{{group.parent_organization.name}}</h1>
              <div class="content">
                <h5>
                  <b>Tematicas que trabaja la organización</b>
                </h5>
                <p>{{arrayTopics.join(', ')}}</p>
                <h5>
                  <b>Ubicación de la organización</b>
                </h5>
                <Localidad :locality-id="group.parent_organization.locality_id" :locality-other="group.parent_organization.locality_other"></Localidad>
                <br>
                <table class="table is-narrow is-bordered">
                  <tbody>
                    <tr>
                      <th>Email de contacto</th>
                      <td>{{group.parent_organization.email ? group.parent_organization.email : 'No registra'}}</td>
                    </tr>
                    <tr>
                      <th>Telefono de contacto</th>
                      <td>{{group.parent_organization.telephone ? group.parent_organization.telephone : 'No registra'}}</td>
                    </tr>
                    <tr>
                      <th>Página web</th>
                      <td>{{group.parent_organization.web ? group.parent_organization.web : 'No registra'}}</td>
                    </tr>
                    <tr>
                      <th>Facebook</th>
                      <td>{{group.parent_organization.facebook ? group.parent_organization.facebook : 'No registra'}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="content" v-else>
              <h1 class="subtitle is-5">El equipo no es parte de una organización</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container" v-else>
      <div class="notification is-light">
        <br>
        <br>
        <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
        <br>
        <br>
      </div>
    </div>
  </section>
</template>

<script>
import Avatar from "../utils/Avatar";
import Localidad from "../utils/GetLocalidad";

export default {
  props: ["project"],
  components: {
    Avatar,
    Localidad
  },
  data() {
    return {
      isLoading: true,
      members: this.$store.state.members,
      group: this.$store.state.group
    };
  },
  mounted: function() {
    if (this.members.length === 0) {
      setTimeout(() => {
        this.members = this.$store.state.members;
        this.group = this.$store.state.group;
        this.isLoading = false;
      }, 2000);
    } else {
      this.isLoading = false;
    }
  },
  computed: {
     arrayTopics: function() {
      if (this.group.parent_organization) {
        let arr = this.group.parent_organization.topics.slice();
        if(this.group.parent_organization.topic_other){
          arr.push(this.group.parent_organization.topic_other)
        }
        return arr ;
      } else {
        return [];
      }
    }
  }
};
</script>

<style>

</style>

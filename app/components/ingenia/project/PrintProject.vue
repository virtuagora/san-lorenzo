<template>
  <section>
    <div class="box is-paddingless is-pulled-right" style="max-width:350px; margin:10px">
      <img :src="imageUrl" class="image" style="margin: 0 auto; border-radius:5px;" alt="">
    </div>
    <div class="tags">
      <span class="tag is-dark is-large">
        <i class="fa fa-hashtag fa-fw"></i>&nbsp;{{zeroPad(project.id,3)}}</span>
      <span class="tag is-light is-large">{{getCategory(project.category_id)}}</span>
    </div>
    <h1 class="title is-2">
      {{project.name}}
    </h1>
    <h1 class="subtitle is-3">
      {{project.group.name}}
    </h1>
    <div class="content is-clearfix">
      <h3>
        <b>Acerca del proyecto</b>
      </h3>
      <p class="nl2br">{{project.abstract}}</p>
      <h3>
        <b>Fundamentación</b>
      </h3>
      <p class="nl2br">{{project.foundation}}</p>
      <h3>
        <b>Trabajo previo</b>
      </h3>
      <p v-if="project.previous_work">{{project.previous_work}}</p>
      <p v-else>
        <i>No presenta trabajo previo</i>
      </p>
      <div class="columns">
        <div class="column">
          <h3>
            <b>Donde se implementará</b>
          </h3>
          <Localidad :locality-id="project.locality_id" :locality-other="project.locality_other"></Localidad>
        </div>
        <div class="column">
          <h3>
            <b>Barrios en que se implementará</b>
          </h3>
          <p>{{project.neighbourhoods.join(', ')}}</p>
        </div>
      </div>
    </div>
    <div class="content">
      <h3>
        <b>Objetivos</b>
      </h3>
      <table class="table is-narrow is-bordered">
        <tbody v-if="project.goals.length">
          <tr v-for="(objetivo, index) in project.goals" :key="index">
            <td>
              <i class="fas fa-flag-checkered fa-fw"></i> {{objetivo}}</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td class="has-text-centered" colspan="2">
              <i>No se han ingresado objetivos</i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="content">
      <h3>
        <b>Calendario de actividades</b>
      </h3>
      <table class="table is-narrow is-bordered">
        <thead>
          <tr>
            <th width="120px">Fecha</th>
            <th>Actividad</th>
          </tr>
        </thead>
        <tbody v-if="project.schedule.length">
          <tr v-for="(actividad, index) in project.schedule" :key="index">
            <td>
              <i class="far fa-calendar-check fa-fw"></i> {{actividad.date}}</td>
            <td>{{actividad.activity}}</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td class="has-text-centered" colspan="3">
              <i>No se han ingresado actividades</i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="content">
      <h3>
        <b>Presupuesto solicitado</b>
      </h3>
      <table class="table is-narrow is-bordered">
        <thead>
          <tr>
            <th width="120px">Rubro</th>
            <th>Descripción</th>
            <th width="120px" class="has-text-centered">Monto</th>
          </tr>
        </thead>
        <tbody v-if="project.budget.length">
          <tr v-for="(item, index) in project.budget" :key="index">
            <td>{{item.category}}</td>
            <td>{{item.description}}</td>
            <td class="has-text-centered">$ {{item.amount}}</td>
          </tr>
          <tr>
            <th colspan="2" class="has-text-right">Monto total:</th>
            <td class="has-text-centered">$ {{montoTotal}}</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td class="has-text-centered" colspan="4">
              <i>No se han ingresado items en el presupuesto</i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="project.organization" class="content">
      <div class="notification">
        <p>
          <i class="fas fa-info-circle fa-lg fa-fw"></i>&nbsp;El proyecto se trabajará en conjunto con una organización</p>
      </div>
      <h3 class="subtitle is-4">Organización</h3>
      <h3 class="title is-2 is-600">{{project.organization.name}}</h3>
      <h3>
        <b>Temáticas que trabaja la organización</b>
      </h3>
      <p>{{arrayTopics.join(', ')}}</p>
      <h3>
        <b>Ubicación de la organización</b>
      </h3>
      <Localidad :locality-id="project.organization.locality_id" :locality-other="project.organization.locality_other"></Localidad>
      <br>
      <table class="table is-narrow is-bordered">
        <tbody>
          <tr>
            <th>Email de contacto</th>
            <td>{{project.organization.email ? project.organization.email : 'No registra'}}</td>
          </tr>
          <tr>
            <th>Teléfono de contacto</th>
            <td>{{project.organization.telephone ? project.organization.telephone : 'No registra'}}</td>
          </tr>
          <tr>
            <th>Página web</th>
            <td>{{project.organization.web ? project.organization.web : 'No registra'}}</td>
          </tr>
          <tr>
            <th>Facebook</th>
            <td>{{project.organization.facebook ? project.organization.facebook : 'No registra'}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="content" v-else>
      <h4>
        <i>El proyecto no registra trabajo en conjunto con una organización</i>
      </h4>
    </div>
    <hr>
    <div class="content">
      <h3 class="subtitle is-4">Equipo</h3>
      <h3 class="title is-2 is-600">{{project.group.name}}</h3>
      <h3>
        <b>Acerca del equipo</b>
      </h3>
      <p class="nl2br">{{project.group.description}}</p>
      <h3>
        <b>Ubicación</b>
      </h3>
      <Localidad :locality-id="project.group.locality_id" :locality-other="project.group.locality_other"></Localidad>
      <br>
      <table class="table is-narrow is-bordered">
        <tbody>
          <tr>
            <th>Año de fundación</th>
            <td>{{project.group.year}}</td>
          </tr>
          <tr>
            <th>
              Participaciones anteriores
            </th>
            <td>
              {{project.group.previous_editions.length ? project.group.previous_editions.join(', ') : 'No registra'}}
            </td>
          </tr>
          <tr v-if="project.group.email">
            <th>Email de contacto</th>
            <td>{{project.group.email}}</td>
          </tr>
          <tr v-if="project.group.telephone">
            <th>Telefono de contacto</th>
            <td>{{project.group.telephone}}</td>
          </tr>
          <tr>
            <th>Página web</th>
            <td>{{project.group.web ? project.group.web : 'No registra'}}</td>
          </tr>
          <tr>
            <th>Facebook</th>
            <td>{{project.group.facebook ? project.group.facebook : 'No registra'}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="!isLoading">
      <p class="is-size-4">
        <b>Integrantes</b>
      </p>
      <br>  
      <article class="media" v-for="member in members" :key="member.id">
        <Avatar :user="member" class="media-left" size="64"></Avatar>
        <div class="media-content" style="overflow: inherit">
          <h1 class="is-size-5 is-600">{{member.subject.display_name}}
            <b-tooltip label="Responsable" type="is-warning" position="is-bottom" v-if="member.pivot.relation === 'responsable'">
              <i class="fas fa-star has-text-warning"></i>
            </b-tooltip>
            <b-tooltip label="Co-responsable" type="is-info" position="is-bottom" v-if="member.pivot.relation === 'co-responsable'">
              <i class="fas fa-shield-alt has-text-info"></i>
            </b-tooltip>
          </h1>
          <div class="content">
            <p v-if="member.subject.img_type === 0">
              <i class="fas fa-check has-text-success fa-fw fa-lg"></i>
              <i class="fas fa-envelope fa-fw fa-lg"></i>&nbsp;Verificado con correo electrónico:
              <b>{{member.email}}</b>
            </p>
            <p v-if="member.subject.img_type === 1">
              <i class="fas fa-check has-text-success fa-fw fa-lg"></i>
              <i class="fab fa-facebook has-text-link fa-fw fa-lg"></i>&nbsp;Verificado utilizando perfil de Facebook:
              <a :href="'https://facebook.com/' + member.subject.img_hash" target="_blank">{{member.subject.display_name}}</a>
            </p>
            <p class="nl2br" v-if="member.bio">{{member.bio}}</p>
            <p v-else>
              <i>- Sin información -</i>
            </p>
          </div>
        </div>
      </article>
      <br>
    </div>
    <div v-else>
      <div class="notification is-light">
        <br>
        <br>
        <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
        <br>
        <br>
      </div>
    </div>
      <div v-if="project.group.parent_organization" class="content">
        <div class="notification">
          <p>
            <i class="fas fa-info-circle fa-lg fa-fw"></i>&nbsp;El proyecto es parte de una organización</p>
        </div>
        <h3 class="subtitle is-5">Organización del equipo</h3>
        <h3 class="title is-3 is-600">{{project.group.parent_organization.name}}</h3>
        <h3>
          <b>Tematicas que trabaja la organización</b>
        </h3>
        <p>{{arrayTopicsOrg.join(', ')}}</p>
        <h3>
          <b>Ubicación de la organización</b>
        </h3>
        <Localidad :locality-id="project.group.parent_organization.locality_id" :locality-other="project.group.parent_organization.locality_other"></Localidad>
        <br>
        <table class="table is-narrow is-bordered">
          <tbody>
            <tr>
              <th>Email de contacto</th>
              <td>{{project.group.parent_organization.email ? project.group.parent_organization.email : 'No registra'}}</td>
            </tr>
            <tr>
              <th>Telefono de contacto</th>
              <td>{{project.group.parent_organization.telephone ? project.group.parent_organization.telephone : 'No registra'}}</td>
            </tr>
            <tr>
              <th>Página web</th>
              <td>{{project.group.parent_organization.web ? project.group.parent_organization.web : 'No registra'}}</td>
            </tr>
            <tr>
              <th>Facebook</th>
              <td>{{project.group.parent_organization.facebook ? project.group.parent_organization.facebook : 'No registra'}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="content" v-else>
        <h4>
          <i>El equipo no registra ser parte de una organización</i>
        </h4>
      </div>
  </section>
</template>

<script>
import Localidad from "../utils/GetLocalidad";
import Avatar from "../utils/Avatar";
export default {
  props: ["project", "getGroupMembers", "teamUrl"],
  components: {
    Localidad,
    Avatar
  },
  data() {
    return {
      isLoading: true,
      categorias: [],
      members: []
    };
  },
  created: function() {
    Promise.all([
      this.$http.get("/category"),
      this.$http.get(this.fetchGroupMembers)
    ])
      .then(responses => {
        this.categorias = responses[0].data;
        this.members = responses[1].data;
        this.isLoading = false;
      })
      .catch(error => {
        console.error(error.message);
        this.isLoading = false;
        this.$snackbar.open({
          message: "Error al obtener los integrantes del equipo.",
          type: "is-danger",
          actionText: "Cerrar"
        });
      });
  },
  mounted: function() {
    window.print();
  },
  methods: {
    zeroPad: function(num, places) {
      var zero = places - num.toString().length + 1;
      return Array(+(zero > 0 && zero)).join("0") + num;
    },
    getCategory(id) {
      let caty = this.categorias.find(x => {
        return x.id === id;
      });
      return caty ? caty.name : "";
    }
  },
  computed: {
    fetchGroupMembers: function() {
      return this.getGroupMembers.replace(":gro", this.project.group.id);
    },
    fetchTeamUrl: function() {
      return this.teamUrl.replace(":gro", this.project.group.id);
    },
    arrayTopicsOrg: function(gro) {
      if (this.project.group.parent_organization) {
        let arr = this.project.group.parent_organization.topics.slice();
        if (this.project.group.parent_organization.topic_other) {
          arr.push(this.project.group.parent_organization.topic_other);
        }
        return arr;
      } else {
        return [];
      }
    },
    montoTotal: function() {
      const reducer = (accumulator, item) =>
        accumulator + parseFloat(item.amount);
      return this.project.budget.reduce(reducer, 0);
    },
    arrayTopics: function() {
      if (this.project.organization) {
        let arr = this.project.organization.topics.slice();
        arr.push(this.project.organization.topic_other);
        return arr;
      } else {
        [""];
      }
    },
    imageUrl: function() {
      if (this.project.has_image) {
        return "/project/" + this.project.id + "/picture";
      }
      return "/assets/img/neuronas-ingenia-noimg.jpg";
    }
  }
};
</script>

<style>

</style>

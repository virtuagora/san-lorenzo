<template>
  <section>

    <section class="section">
      <div class="container">
        <div class="notification is-warning has-text-centered" v-if="selected">
          <h1 class="title is-1"><i class="fas fa-trophy"></i>&nbsp;¡Proyecto seleccionado!</h1>
        </div>
        <div class="columns">
          <div class="column is-8">
            <article class="message" v-if="isAdmin || isCoordinator">
              <div class="message-body">
                <a @click="showEditObservation = true" v-show="!showEditObservation" class="has-text-link is-pulled-right">
                  <i class="fas fa-edit fa-lg"></i>
                </a>
                <h3 class="is-size-5" :class="{'has-text-danger': errors.has('noteInput')}">
                  <b>Observaciones</b>
                </h3>
                  <div v-if="!showEditObservation">
                    <p class="nl2br" v-if="notesCopy">{{notesCopy}}</p>
                    <p class="nl2br" v-else>- Sin notas -</p>
                  </div>
                  <div class="field" v-else>
                    <div class="control ">
                      <b-input v-model="noteInput" size="is-small" data-vv-name="noteInput" data-vv-as="'Observaciones'" v-validate="'max:1000'" type="textarea" maxlength="1000" rows="2" placeholder="Observaciones">
                      </b-input>
                      <span v-show="errors.has('noteInput')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('noteInput')}}</span>
                    </div>
                <div class="buttons" v-if="sent == false">
                  <button @click="saveObs()" class="button is-link is-600">Guardar</button>
                  <button @click="deleteObs()" class="button">Borrar</button>
                </div>
                <div class="notification is-success" v-else>
                  <i class="fas fa-check fa-fw"></i>&nbsp;¡Observación guardada!
                </div>
                  </div>
              </div>
            </article>
            <h3 class="is-size-3">
              <b>Descripción del proyecto</b>
            </h3>
            <p class="nl2br">{{project.abstract}}</p>
            <br>
            <h3 class="is-size-3">
              <b>Fundamentación del proyecto</b>
            </h3>
            <p class="nl2br">{{project.foundation}}</p>
            <br>
            <div class="notification is-primary">
              <div class="columns">
                <div class="column is-hidden-mobile">
                  <h3 class="is-size-3">
                    <b>¡Compartilo!</b>
                  </h3>
                </div>
                <div class="column is-narrow has-text-centered-mobile">
                  <p class="">
                    <a href="javascript:shareOnFacebook()">
                      <i class="fab fa-facebook fa-3x fa-fw"></i>
                    </a>
                    <a :href="'https://twitter.com/intent/tweet?text=¡Este gran proyecto está participando de INGENIA y necesita tú apoyo! ¡Ingresá y bancalo con tú voto!&url=' + getLocation + '&hashtags=INGENIA,hayEquipo!'">
                      <i class="fab fa-twitter fa-3x fa-fw"></i>
                    </a>
                    <a :href="'whatsapp://send?text=¡Este gran proyecto está participando de INGENIA y necesita tú apoyo! ¡Ingresá y bancalo con tú voto! Visitalo entrando a ' + getLocation">
                      <i class="fab fa-whatsapp fa-3x fa-fw"></i>
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <h5 class="is-size-4">
                  <b>Donde se implementará</b>
                </h5>
                <Localidad :locality-id="project.locality_id" :locality-other="project.locality_other"></Localidad>
              </div>
              <div class="column">
                <h5 class="is-size-4">
                  <b>Barrios en que se implementara</b>
                </h5>
                <p>{{project.neighbourhoods.join(', ')}}</p>
              </div>
            </div>
            <h5 class="is-size-4">
              <b>Trabajo previo</b>
            </h5>
            <p v-if="project.previous_work">{{project.previous_work}}</p>
            <p v-else>
              <i>No presenta trabajo previo</i>
            </p>
          </div>
          <div class="column is-4 has-text-centered">
            <div class="box is-paddingless">
              <img :src="imageUrl" class="image" style="margin: 0 auto; border-radius:5px;" alt="">
            </div>
            <h5 class="is-size-4">
              <b>Categoría</b>
            </h5>
            <span class="tag is-primary is-large">
              {{project.category.name}}
            </span>
            <br>
            <br>
            <h5 class="is-size-4">
              <b>Presupuesto solicitado</b>
            </h5>
            <h5 class="is-size-2 has-text-info is-800">
              ${{montoTotal}}
            </h5>
            <br>
            <h5 class="is-size-4">
              <b>
                <i class="fas fa-flag-checkered fa-fw"></i>&nbsp;Declaran {{project.goals.length}} objetivo{{project.goals.length > 1 ?'s':null}}</b>
            </h5>
            <br>
            <h5 class="is-size-4">
              <b>
                <i class="far fa-calendar-check fa-fw"></i>&nbsp;Con {{project.schedule.length}} actividad{{project.schedule.length > 1 ?'es':null}}</b>
            </h5>
            <br>
            <router-link :to="{ name: 'projectImplementation'}" class="button is-black is-outlined is-medium is-fullwidth">Conocé la implementación</router-link>
            <br>
            <router-link :to="{ name: 'projectTeam'}" class="button is-black is-outlined is-medium is-fullwidth">Conocé al equipo</router-link>

          </div>
        </div>
      </div>
    </section>
    <div class="hero is-dark  has-image-background" v-if="(user !== null && user.groups[0] == undefined) || user === null " :style="imageUrlHeroInvite()">
      <!-- <img :src="'/project/'+project.id+'/picture'" class="image"  v-if="project.has_image"  style="width:200px; margin:0 auto;" alt=""> -->
      <div class="hero-body has-text-centered">
        <div class="columns">
          <div class="column is-6 is-offset-3" v-if="user !== null && user.groups[0] == undefined">
            <div class="has-text-centered">
              <h3 class="is-size-3 is-600">
                <span v-if="user !== null">¡{{user.names}}!</span> ¿Querés colaborar con el equipo?</h3>
              <p>¡Enviales una solicitud para ser parte!</p>
              <br>
              <div v-if="user !== null && user.pending_tasks.length > 0">
                <div class="notification is-warning">
                  <p>
                    <i class="fa fa-exclamation-circle fa-fw"></i>
                    <b>IMPORTANTE:</b> Antes debes completar todos tus datos personales para ser parte de un equipo INGENIA. Seguí los pasos indicados en tu panel de usuario.</p>
                  <br>
                  <a href="/panel" class="button is-dark is-outlined is-medium">Ir al panel</a>
                </div>
              </div>
              <button v-if="user !== null && user.pending_tasks.length == 0" @click="wannaColaborate = true" v-show="!wannaColaborate" class="button is-warning is-medium">¡Si! ¡Quiero colaborar!</button>
              <div v-if="wannaColaborate">
                <div class="box has-text-left" v-if="!response.ok">
                  <div class="field">
                    <label class="label">
                      <i class="fas fa-angle-double-right"></i> Escribí un mensaje al responsable del equipo</label>
                    <div class="control">
                      <b-input maxlength="200" v-model="message" type="textarea" rows="2"></b-input>
                    </div>
                  </div>
                  <div class="field">
                    <div class="control">
                      <button @click="submitInvitacion" class="button is-primary is-fullwidth" :class="{'is-loading': isLoading}">
                        <i class="fa fa-paper-plane fa-fw"></i>&nbsp;Enviar</button>
                    </div>
                  </div>
                </div>
                <div class="notification is-success" v-show="response.ok">
                  <i class="fas fa-check fa-fw"></i> ¡Tu solicitud ha sido enviada, gracias!
                </div>
              </div>
              <a v-if="user === null" href="/login" class="button is-warning is-outlined is-medium">
                <i class="fas fa-sign-in-alt fa-fw"></i>&nbsp;Inicia sesión para solicitar</a>
              <b-loading :active.sync="isLoading"></b-loading>
            </div>
          </div>
          <div class="column is-6 is-offset-3" v-if="user === null">
            <div class="has-text-centered">
              <h3 class="is-size-3 is-600">
                Entrá en Ingenia+Virtuágora para poder participar</h3>
              <p>Vas a poder bancar los proyectos y dejar comentarios, hasta podrias llegar a ser parte de un equipo!</p>
              <br>
              <a href="/panel" class="button is-warning is-medium">
                <i class="fas fa-sign-in-alt fa-fw"></i>&nbsp;Iniciar sesión</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Localidad from "../utils/GetLocalidad";
export default {
  props: [
    "project",
    "sendRequestJoin",
    "notes",
    "isAdmin",
    "isCoordinator",
    "idCoordinator",
    "putProjectNote",
    'selected'
  ],
  components: {
    Localidad
  },
  data() {
    return {
      user: {},
      notesCopy: this.notes,
      wannaColaborate: false,
      showEditObservation: false,
      message: "",
      isLoading: false,
      response: {
        ok: false
      },
      noteInput: "",
      sent: false,
    };
  },
  created: function() {
    this.user = this.$store.state.user;
  },
  methods: {
    isOptional: function(value) {
      if (value === null || value === "") {
        return null;
      }
      if (typeof value !== "undefined" && value.length == 0) {
        return [];
      } else return value;
    },
    submitInvitacion: function() {
      this.isLoading = true;
      this.$http
        .post(
          this.sendRequestJoin.replace(":gro", this.project.group.id),
          this.payload
        )
        .then(response => {
          this.$snackbar.open({
            message: "¡La solicitud ha sido enviada!",
            type: "is-success",
            actionText: "OK"
          });
          this.isLoading = false;
          this.response.ok = true;
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    },
    imageUrlHeroInvite: function() {
      if (this.project.has_image) {
        return (
          "background-image: url(/project/" +
          this.project.id +
          "/picture); background-position: center center; background-size: cover"
        );
      }
      return "";
    },
    deleteObs() {
      this.isLoading = true;
      this.$http
        .put(this.putProjectNote, {notes: null})
        .then(response => {
          this.isLoading = false;
          this.notesCopy = null
          this.showEditObservation = false
          this.$snackbar.open({
            message: "¡Observacion guardada!",
            type: "is-success",
            actionText: "OK"
          });
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    },
    saveObs() {
      this.isLoading = true;
      this.$http
        .put(this.putProjectNote, this.payload2)
        .then(response => {
          this.notesCopy = this.noteInput
          this.showEditObservation = false          
          this.$snackbar.open({
            message: "¡Observacion guardada!",
            type: "is-success",
            actionText: "OK"
          });
          this.isLoading = false;
        })
        .catch(error => {
          console.error(error.message);
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    },
  },
  computed: {
    payload: function() {
      return {
        comment: this.isOptional(this.message)
      };
    },
    payload2: function() {
      return {
        notes: this.isOptional(this.noteInput)
      };
    },
    montoTotal: function() {
      const reducer = (accumulator, item) =>
        accumulator + parseFloat(item.amount);
      return this.project.budget.reduce(reducer, 0);
    },
    imageUrl: function() {
      if (this.project.has_image) {
        return "/project/" + this.project.id + "/picture";
      }
      return "/assets/img/neuronas-ingenia-noimg.jpg";
    },
    getLocation: function() {
      return window.location.href;
    }
  }
};
</script>

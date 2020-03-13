<template>
  <div class="hero is-small is-dark">
    <div class="hero-body">
      <div class="container has-text-centered">
        <section v-if="idCoordin == null">
          <h3 class="is-size-4">
              <b>El proyecto no tiene asignado un coordinador</b>
            </h3>
            <a @click="assign()" v-if="isCoordinator" class="button is-info"><i class="fas fa-plus"></i>&nbsp;Asignarme como coordinador</a>
        </section>
        <section v-if="idCoordin != null && coordinator != null">
          <div class="columns">
            <div class="column" :class="{'is-8': review.grantedBudget == null, 'is-6': review.grantedBudget != null}">
              <article class="media">
            <Avatar :user="coordinator" class="media-left" size="96"></Avatar>
            <div class="media-content" style="overflow: inherit">
              <h1 class="is-size-5 is-300">Coordinador asignado</h1>
              <h1 class="is-size-3 is-600">{{coordinator.names}} {{coordinator.surnames}}
                <b-tooltip label="Coordinador" type="is-link" position="is-bottom">
                <i class="fas fa-bullhorn has-text-link"></i>
                </b-tooltip>
              </h1>
              <div class="buttons">
                <a @click="cardEvaluar()" class="button is-white is-outlined" v-if="(isCoordinator && user.id == idCoordin) || isAdmin">Cambiar evaluación</a>
                <a @click="deleteCoord()" class="button is-danger" v-if="isCoordinator && user.id == idCoordin">Dejar de coordinar</a>
              </div>
            </div>
          </article>
            </div>
            <div class="column" :class="{'is-4': review.grantedBudget == null, 'is-3': review.grantedBudget != null}">
              <h1 class="is-size-5 is-300">Evaluación</h1>
              <h1 class="is-size-1 is-600">{{review.quota ? review.quota : '-'}}</h1>
            </div>
            <div class="column is-3" v-if=" review.grantedBudget != null">
               <h1 class="is-size-5 is-300">Presupuesto</h1>
               <h1 class="is-size-1 is-600">$ {{review.grantedBudget}}</h1>
            </div>
          </div>
        </section>
        <b-loading :active.sync="isLoading"></b-loading>
      </div>
    </div>
  </div>
</template>

<script>
import ModalReview from '../utils/ModalReview'
import Avatar from '../utils/Avatar'
export default {
  props: ['id', 'isAdmin','isCoordinator','idCoordinator', 'grantedBudget','selected','quota','updateReview', 'assignCoordinator','deleteCoordinator'],
  components:{
    ModalReview,
    Avatar
  },
  data() {
    return {
      user: {},
      isLoading: false,
      idCoordin: this.idCoordinator,
      coordinator: null,
      review: {
        quota: this.quota,
        selected: this.selected,
        grantedBudget: this.grantedBudget
      }
    }
  },
  created: function() {
    this.user = this.$store.state.user;
  },
  mounted: function(){
    if(this.idCoordinator != null){
      this.isLoading = true
      this.$http.get('/user/' + this.idCoordinator)
      .then(response => {
        this.isLoading = false
        this.coordinator = response.data
      })
      .catch(error => {
        console.error(error.message);
        this.isLoading = false;
        this.$snackbar.open({
          message:
            "Error inesperado",
          type: "is-danger",
          actionText: "Cerrar"
        });
      });
    }
  },
  methods: {
    assign: function(){
      this.$http.post(this.assignCoordinator)
      .then(response => {
        this.coordinator = this.user
        this.idCoordin = this.user.id
      })
      .catch(error => {
        console.error(error.message);
        this.$snackbar.open({
          message:
            "Error inesperado",
          type: "is-danger",
          actionText: "Cerrar"
        });
      });
    },
    deleteCoord: function(){
      this.$http.delete(this.deleteCoordinator)
      .then(response => {
        location.reload()
      })
      .catch(error => {
        console.error(error.message);
        this.$snackbar.open({
          message:
            "Error inesperado",
          type: "is-danger",
          actionText: "Cerrar"
        });
      });
    },
    cardEvaluar: function() {
      this.$modal.open({
        parent: this,
        component: ModalReview,
        hasModalCard: true,
        props: { project: this.id, budget: this.review.grantedBudget, selected: this.review.selected, quota: this.review.quota, url: this.updateReview }
      });
    }
  }
}
</script>

<style>

</style>

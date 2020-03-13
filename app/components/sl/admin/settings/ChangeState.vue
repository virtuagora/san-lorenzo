<template>
  <section>
    <h1 class="title is-5 is-marginless">Pasos disponibles en la plataforma</h1>
    <nav class="breadcrumb has-arrow-separator">
      <ul>
        <li :class="{'is-active': current.key == estado.key}" v-for="estado in states" :key="estado.key"><a>{{estado.name}}</a></li>
      </ul>
    </nav>
    <div class="columns">
      <div class="column">
        <div class="notification">
        <p class="title is-5">Estado actual de la plataforma</p>
        <b-field>
        <input type="text" readonly class="input" :value="current.name">
        </b-field>
        </div>    
      </div>
      <div class="column is-narrow is-hidden-touch">
        <br>
        <i class="fas fa-arrow-right fa-lg fa-fw"></i>
      </div>
      <div class="column">
        <div class="notification">
        <p class="title is-5">Nuevo estado de la plataforma</p>
         <b-field>
            <b-select v-model="newState" placeholder="Seleccione el nuevo estado" expanded>
                <option
                    v-for="option in states"
                    :value="option.key"
                    :key="option.key"
                    :disabled="current.key == option.key">
                    {{ option.name }}
                </option>
            </b-select>
        </b-field>
        </div>
      </div>
    </div>
    <div class="is-clearfix">
      <button @click="submit()" class="button is-link is-pulled-right"><i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["currentState", "states"],
  data() {
    return {
      newState: null,
      isLoading: false,
    };
  },
  created: function() {

  },
  methods: {
    submit: function() {
      this.isLoading = true;
      this.$http
        .post("/api/admin/option/current-state", this.payload)
        .then(response => {
          window.location.href = "/logout";
        })
        .catch(x => {
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  },
  computed: {
    payload: function() {
      return {
        value: this.newState
      };
    },
    current: function(){
      return this.states.find( s => s.key === this.currentState)
    }
  }
};
</script>
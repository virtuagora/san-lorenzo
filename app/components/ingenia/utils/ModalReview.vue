<template>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Evaluar</p>
    </header>
    <section class="modal-card-body">
      <div class="notification">
        <i class="fas fa-exclamation-triangle"></i>&nbsp;Edite el campo que necesite, no es necesario que complete todos los campos.
      </div>
      <div v-if="!isLoading">
        <div class="field is-horizontal">
          <div class="field-label is-normal">
            <label class="label">Puntaje</label>
          </div>
          <div class="field-body">
            <div class="field">
              <div class="control">
                <vue-slider ref="slider" v-model="points"></vue-slider>
              </div>
            </div>
          </div>
        </div>
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">Monto otorgado</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <input class="input" v-model="theBudget" data-vv-name="theBudget" data-vv-as="'Monto'" v-validate="'numeric'" type="text" placeholder="Monto en AR$">
                  <span v-if="errors.has('theBudget')" class="help is-danger">
                    <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('theBudget')}}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">¿Seleccionado?</label>
            </div>
            <div class="field-body">
               <b-field>
        <b-radio-button v-model="isSelected" :native-value="true" type="is-primary">
          <span>
            <i class="fas fa-check"></i> Si</span>
        </b-radio-button>
        <b-radio-button v-model="isSelected " :native-value="false" type="is-primary">
          <span>
            <i class="fas fa-times"></i> No</span>
        </b-radio-button>
      </b-field>
            </div>
          </div>
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
    </section>
    <footer class="modal-card-foot">
      <button class="button is-link" type="button" @click="submit()">Guardar</button>
      <button class="button is-dark" type="button" @click="$parent.close()">Close</button>
    </footer>
  </div>
</template>

<script>
import vueSlider from 'vue-slider-component'

export default {
  props: ["project",'budget','selected','quota','url'],
  components: {
    vueSlider
  },
  data() {
    return {
      isLoading: false,
      theBudget: this.budget != null ? Number(this.budget) : null,
      isSelected: this.selected ? true : false,
      points: this.quota
    };
  },
  methods: {
    submit: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error en el formulario. Verifíquelo",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$http.post(this.url, this.payload)
          .then(response => {
            this.$parent.close()
            this.$snackbar.open({
              message: "Evaluación guardada",
              type: "is-success",
              actionText: "Ok!"
            });
            location.reload()
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message: "Error inesperado",
              type: "is-danger",
              actionText: "Cerrar"
            });
          });
        })
        .catch(error => {
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
    payload: function(){
      return {
        puntaje: this.isOptional(Number(this.points)),
        monto: this.isOptional(this.theBudget),
        seleccionado: this.isOptional(this.isSelected)
      }
    }
  }
};
</script>
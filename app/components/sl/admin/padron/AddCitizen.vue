<template>
  <section>
    <div class="notification is-light">
  <i class="fas fa-exclamation-triangle fa-fw"></i> No agregue al padron ciudadanos que no hayan sido verificados.
  </div>
    <div class="columns">
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.number')}">
            <i class="fas fa-angle-double-right"></i> Numero</label>
          <div class="control">
            <input v-model="citizen.number" data-vv-name="citizen.number" data-vv-as="'Código de indentificacion'" type="text" v-validate="'required|integer|min:2|max:11'" class="input is-large" placeholder="Requerido *">
            <span v-show="errors.has('citizen.number')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.number')}}</span>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.type')}">
            <i class="fas fa-angle-double-right"></i> Tipo *</label>
          <div class="control">
            <div class="select is-large is-fullwidth">
              <select data-vv-name="citizen.type" data-vv-as="'Tipo'" v-validate="'required'" v-model="citizen.type" placeholder="Seleccione el tipo de doc">
                <option :value="null" disabled>- Seleccione el tipo -</option>
                <option value="DNI">DNI</option>
                <option value="L">Libreta</option>
              </select>
            </div>
            <span v-show="errors.has('citizen.type')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.type')}}</span>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.year')}">
            <i class="fas fa-angle-double-right"></i> Año de nacimiento *</label>
          <div class="control">
            <input v-model="citizen.year" data-vv-name="citizen.year" data-vv-as="'Código de indentificacion'" type="text" v-validate="'required|integer|min:2|max:4|min_value:1900'" class="input is-large" placeholder="Requerido *">
            <span v-show="errors.has('citizen.year')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.year')}}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.names')}">
            <i class="fas fa-angle-double-right"></i> Nombre(s) *</label>
          <div class="control">
            <input v-model="citizen.names" data-vv-name="citizen.names" data-vv-as="'Nombre(s)'" type="text" v-validate="'required|min:2|max:100'" class="input is-large" placeholder="Requerido *">
            <span v-show="errors.has('citizen.names')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.names')}}</span>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.surnames')}">
            <i class="fas fa-angle-double-right"></i> Apellido(s) *</label>
          <div class="control">
            <input v-model="citizen.surnames" data-vv-name="citizen.surnames" data-vv-as="'Apellido(s)'" type="text" v-validate="'required|min:2|max:100'" class="input is-large" placeholder="Requerido *">
            <span v-show="errors.has('citizen.surnames')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.surnames')}}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="columns">
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.address')}">
            <i class="fas fa-angle-double-right"></i> Domicilio *</label>
          <div class="control">
            <input v-model="citizen.address" data-vv-name="citizen.address" data-vv-as="'Domicilio'" type="text" v-validate="'required|min:2|max:200'" class="input is-large" placeholder="Requerido *">
            <span v-show="errors.has('citizen.address')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.address')}}</span>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label is-size-4" :class="{'has-text-danger': errors.has('citizen.genre')}">
            <i class="fas fa-angle-double-right"></i> Genero *</label>
          <input type="hidden" v-model="citizen.genre" v-validate="'required'" data-vv-as="'Genero'" data-vv-name="citizen.genre">
          <b-field>
            <b-radio-button v-model="citizen.genre" v-validate="'required'" size="is-large" native-value="M">
              Masculino
            </b-radio-button>
            <b-radio-button v-model="citizen.genre" size="is-large" native-value="F">
              Femenino
            </b-radio-button>
          </b-field>
          <span v-show="errors.has('citizen.genre')" class="help is-danger">
            <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('citizen.genre')}}</span>
        </div>
      </div>
    </div>
    <div class="field" v-if="!response.ok">
      <div class="control">
        <button @click="submit()" class="button is-primary is-medium is-fullwidth">
          <i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
      </div>
    </div>
    <div v-else>
      <div class="notification is-success has-text-centered">
        <i class="fas fa-check fa-fw"></i>&nbsp;¡Se ha registrado con éxito en el padrón!
      </div>
      <div class="columns">
        <div class="column" v-if="allowRestart">
          <button @click="restart()" class="button is-primary is-outlined is-medium is-fullwidth">
            <i class="fas fa-undo fa-fw"></i>&nbsp;Reiniciar
          </button>
        </div>
        <div class="column" v-if="allowStartVoting">
        <button @click="startVoting" class="button is-link is-medium is-fullwidth">
          <i class="fas fa-arrow-right fa-fw fa-lg"></i>&nbsp;Comenzar proceso de votación
        </button>
        </div>
      </div>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["url", "allowRestart","allowStartVoting", "urlParticipacion"],
  data() {
    return {
      isLoading: false,
      response: {
        ok: false,
        replied: false,
        citizen: null
      },
      citizen: {
        surnames: "",
        names: "",
        year: "",
        number: "",
        type: "DNI",
        address: "",
        genre: "M"
      },
    };
  },
  methods: {
    submit: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          this.$buefy.snackbar.open({
            message: "Faltan datos o algunos son incorrectos. Verifíquelos.",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
          return;
        }
        this.isLoading = true;
        this.$http
          .post(this.url, this.payload)
          .then(response => {
            this.$buefy.snackbar.open({
              message: "Ciudadano guardado con éxito!",
              type: "is-success",
              actionText: "¡Genial!"
            });
            this.isLoading = false;
            this.response.ok = true;
            this.response.replied = true;
            this.response.citizen = response.data.citizen;
          })
          .catch(error => {
            console.error(error.message);
            this.isLoading = false;
            this.response.ok = false;
            this.response.replied = true;
            this.$buefy.snackbar.open({
              message: "Error inesperado: " + error.message,
              type: "is-danger",
              actionText: "Cerrar",
              indefinite: true
            });
          });
      });
    },
    restart: function() {
      this.citizen = {
        surnames: "",
        names: "",
        year: "",
        number: "",
        type: "DNI",
        address: "",
        genre: "M"
      };
      this.response = {
        ok: false,
        replied: false,
        citizen: null
      };
      this.$nextTick().then(() => {
        this.$validator.reset();
        this.errors.clear();
      });
    },
    startVoting: function(){
      this.$emit('startVoting',this.response.citizen)
      this.restart()
    }

  },
  computed: {
    payload: function() {
      return {
        number: this.citizen.number,
        year: parseInt(this.citizen.year),
        data: [
          this.citizen.surnames.toUpperCase().replace(",", "") +
            " " +
            this.citizen.names.toUpperCase().replace(",", ""),
          this.citizen.type.toUpperCase().replace(",", ""),
          this.citizen.address.toUpperCase().replace(",", "")
        ].join(", "),
        genre: this.citizen.genre
      };
    },
    payloadMarking: function() {
      return {
        matricula: this.response.citizen.dni
      };
    }
  }
};
</script>

<style>
</style>

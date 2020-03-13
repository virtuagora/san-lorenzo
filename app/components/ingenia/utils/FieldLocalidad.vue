<template>
  <div class="field">
        <label class="label" :class="{'has-text-danger': errors.has('regionSelected') || errors.has('departamentoSelected') || errors.has('localidadSelected') || errors.has('localidadCustom') }">
      Seleccione región, departamento, y la localidad</label>
    <b-field grouped>
      <b-field  expanded>
        <b-select v-model="regionSelected" data-vv-name="regionSelected" data-vv-as="'Región'" v-validate="'required'" placeholder="Región" :disabled="regiones.length == 0" :loading="regionLoading" expanded>
          <option v-for="region in regiones" :key="region.id" :value="region">{{region.name}}</option>
        </b-select>
      </b-field>
      <b-field  expanded>
        <b-select v-model="departamentoSelected" placeholder="Departamento" data-vv-name="departamentoSelected" data-vv-as="'Departamento'" v-validate="'required'" :disabled="departamentos.length == 0" :loading="departamentoLoading" expanded>
          <option v-for="departamento in departamentos" :key="departamento.id" :value="departamento">{{departamento.name}}</option>
        </b-select>
      </b-field>
      <b-field  expanded>
        <b-select v-model="localidadSelected" placeholder="Localidad" data-vv-name="localidadSelected" data-vv-as="'Localidad'" v-validate="'required'" :disabled="localidades.length == 0" :loading="localidadLoading" expanded>
          <option v-for="localidad in localidades" :key="localidad.id" :value="localidad">{{localidad.name}}</option>
        </b-select>
      </b-field>
    </b-field>
    <div class="field" v-if="localidadSelected != null && localidadSelected.custom">
      <div class="control">
        <label class="label">Otra localidad</label>
        <input type="text" class="input is-medium" :class="{'is-danger': errors.has('localidadCustom') }" v-model.lazy="localidadCustom" name="localidadCustom" v-validate="'required'" placeholder="Ingrese el nombre de la localidad...">
        <span class="help is-danger" v-show="errors.has('localidadCustom')">Requerido. Debe ingresar el nombre de la localidad</span>
      </div>
    </div>
    <span v-show="errors.has('regionSelected')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('regionSelected')}}</span>
    <span v-show="errors.has('departamentoSelected')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('departamentoSelected')}}</span>
    <span v-show="errors.has('localidadSelected')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('localidadSelected')}}</span>
  </div>
</template>

<script>
export default {
  data() {
    return {
      regionLoading: false,
      regionSelected: null,
      departamentoLoading: false,
      departamentoSelected: null,
      localidadLoading: false,
      localidadSelected: null,
      showCustom: false,
      localidadCustom: null,
      regiones: [],
      departamentos: [],
      localidades: []
    };
  },
  mounted: function() {
    this.regionLoading = true;
    this.$http
      .get("/region")
      .then(response => {
        this.regiones = response.data;
        this.regionLoading = false;
      })
      .catch(error => {
        console.error(error.message);
        this.$snackbar.open({
          message: "Error inesperado",
          type: "is-danger",
          actionText: "Cerrar"
        });
        this.regionLoading = false;
      });
  },
  methods: {
    validateForm: function() {
      let promise = new Promise((resolve, reject) => {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log("Localidad: Hay errores en los datos");
            return resolve(result);
          }
          console.log("Localidad: OK");
          return resolve(result);
        });
      });
      return promise;
    }
  },
  watch: {
    regionSelected: function(newVal, oldVal) {
      if (newVal != null) {
        this.departamentoSelected = null;
        this.departamentoLoading = true;
        this.$http
          .get("/region/" + newVal.id + "/department")
          .then(response => {
            this.departamentoLoading = false;
            this.departamentos = response.data;
            this.localidades = [];
            this.localidadSelected = null;
          })
          .catch(error => {
            console.error(error.message);
            this.departamentoLoading = false;
            this.localidades = [];
            this.localidadSelected = null;
            this.$snackbar.open({
              message: "Error inesperado",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          });
      }
    },
    departamentoSelected: function(newVal, oldVal) {
      if (newVal != null) {
        this.localidadSelected = null;
        this.localidadLoading = true;
        this.$http
          .get("/department/" + newVal.id + "/locality")
          .then(response => {
            this.localidades = response.data;
            this.localidadLoading = false;
          })
          .catch(error => {
            console.error(error.message);
            this.localidadLoading = false;
            this.$snackbar.open({
              message: "Error inesperado",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          });
      }
    },
    localidadSelected: function(newVal, oldVal) {
      if (newVal != null) {
        this.$emit("updateLocalidad", newVal.id);
        if (newVal.custom) {
          this.showCustom = true;
        } else {
          this.showCustom = false;
        }
      }
    },
    localidadCustom: function(newVal, oldVal) {
      if (newVal != null) {
        this.$emit("updateLocalidadCustom", newVal);
      }
    }
  }
};
</script>

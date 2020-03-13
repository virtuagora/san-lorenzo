<template>
  <div>

    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.name')}">
        <i class="fas fa-angle-double-right"></i> Nombre del proyecto *</label>
      <div class="control">
        <input v-model="project.name" data-vv-name="project.name" data-vv-as="'Nombre del proyecto'" type="text" v-validate="'required|min:10|max:250'" class="input is-large" placeholder="Requerido *">
        <span v-show="errors.has('project.name')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.name')}}</span>
      </div>
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.abstract')}">
        <i class="fas fa-angle-double-right"></i> Resumen del proyecto *</label>
      <p>Breve descripción de tu proyecto. Máximo 2000 caracteres</p>
      <br>
      <div class="control">
        <b-input v-model="project.abstract" data-vv-name="project.abstract" data-vv-as="'Resumen del proyecto'" v-validate="'required|min:10|max:2000'" type="textarea" minlength="10" maxlength="2000" rows="3" placeholder="Requerido *. Breve descripcion de tu proyecto">
        </b-input>
        <span v-show="errors.has('project.abstract')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.abstract')}}</span>
      </div>
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.foundation')}">
        <i class="fas fa-angle-double-right"></i> Fundamentación *</label>
      <p>¿Por qué vale la pena realizar el proyecto? Máximo 1500 caracteres</p>
      <br>
      <div class="control">
        <b-input v-model="project.foundation" data-vv-name="project.foundation" data-vv-as="'Fundamentación'" v-validate="'required|min:10|max:1500'" type="textarea" minlength="10" maxlength="1500" rows="3" placeholder="Requerido *">
        </b-input>
        <span v-show="errors.has('project.foundation')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.foundation')}}</span>
      </div>
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.category_id')}">
        <i class="fas fa-angle-double-right"></i>Seleccione la categoria del proyecto *</label>
      <p>Defina en que categoría se enmarca el proyecto</p>
      <br>
      <b-field>
        <div class="select is-large is-fullwidth" :class="{'is-loading': categoriasLoading}">
          <select data-vv-name="project.category_id" data-vv-as="'Categoría'" v-validate="'required'" v-model="project.category_id" placeholder="Seleccione la temática">
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">{{categoria.name}}</option>
          </select>
        </div>
      </b-field>
      <span v-show="errors.has('project.category_id')" class="help is-danger">
        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.category_id')}}</span>
      <div class="notification is-primary" v-show="project.category_id != null">
        <p v-for="categoria in categorias" :key="categoria.id" v-show="project.category_id == categoria.id">{{categoria.description}}</p>
      </div>
    </div>
    <br>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('enEjecucion')}">
        <i class="fas fa-angle-double-right"></i> ¿El proyecto ya esta en ejecución o en funcionamiento? *</label>
      <p>Si se trata de una iniciativa primigenia que aun no se ha desarrollado marcar
        <i class="fas fa-times"></i> NO</p>
      <br>
      <div class="control">
        <b-field>
          <b-radio-button data-vv-name="enEjecucion" data-vv-as="'En ejecución o en funcionamiento'" v-validate="'required'" v-model="enEjecucion" :native-value="true" type="is-primary" size="is-medium">
            <span>
              <i class="fas fa-check"></i> Si</span>
          </b-radio-button>
          <b-radio-button v-model="enEjecucion" :native-value="false" type="is-primary" size="is-medium">
            <span>
              <i class="fas fa-times"></i> No</span>
          </b-radio-button>
        </b-field>
        <span v-show="errors.has('enEjecucion')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('enEjecucion')}}</span>
      </div>
    </div>
    <br>
    <div class="field" v-if="enEjecucion">
      <label class="label is-size-5" :class="{'has-text-danger': errors.has('project.previous_work')}">
        <i class="fas fa-caret-right"></i> ¿De qué forma, cómo y dónde se está ejecutando?</label>
      <div class="control">
        <b-input type="textarea" v-model="project.previous_work" data-vv-name="project.previous_work" data-vv-as="'Descripcion de la ejecución'" v-validate="'required|min:10|max:1000'" minlength="10" maxlength="1000" rows="3" placeholder="Requerido *">
        </b-input>
        <span v-show="errors.has('project.previous_work')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.previous_work')}}</span>
      </div>
    </div>
    <label class="label is-size-4">
      <i class="fas fa-angle-double-right"></i> ¿Donde se implementa o implementará territorialmente el proyecto? *
    </label>
    <Localidad v-if="showLocalityField" ref="localidadForm" @updateLocalidad="updateLocalidad" @updateLocalidadCustom="updateLocalidadCustom"></Localidad>
    <div v-else>
      <button @click="cleanLocalidad" class="button is-light is-pulled-right">Cambiar ubicación</button>
      <show-localidad :locality-id="project.locality_id" :locality-other="project.locality_other"></show-localidad>
      <br>
      <br>
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.neighbourhoods')}">
        <i class="fas fa-angle-double-right"></i>¿En que barrio/s se llevará adelante? *</label>
      <p>Indiquen el/los barrios donde realizarán el proyecto. Separe con comas, o presione TAB</p>
      <br>
      <b-taginput v-model="project.neighbourhoods" data-vv-name="project.neighbourhoods" data-vv-as="'Barrios'" v-validate="'required'" size="is-medium" icon="map-marker" type="is-primary" placeholder="Nombre del barrio">
      </b-taginput>
      <span v-show="errors.has('project.neighbourhoods')" class="help is-danger">
        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.neighbourhoods')}}</span>
      <br>

    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.goals')}">
        <i class="fas fa-angle-double-right"></i> Objetivos *</label>
      <p>¿Qué se quiere lograr? ¡Anda agregando objetivos de a uno! Tratar de ser muy breves, concretos y precisos</p>
      <div class="control">
        <br>
        <div class="field is-grouped">
          <div class="control">
            <a @click.prevent class="button is-medium is-static">
              <i class="fas fa-flag-checkered"></i>
            </a>
          </div>
          <p class="control is-expanded">
            <b-input size="is-medium" v-model="inputObjetivos" name="inputObjetivos" maxlength="300" placeholder="Escriba el objetivo"></b-input>
          </p>
          <p class="control">
            <b-tooltip :label="disableAddObjetivo ? 'Falta información' : 'Agregar!'" :type="disableAddObjetivo ? 'is-danger' : 'is-primary'" position="is-bottom">
              <button @click="addObjetivo" class="button is-primary is-medium" :disabled="disableAddObjetivo">
                <i class="fas fa-plus"></i>
              </button>
            </b-tooltip>
          </p>
        </div>
        <input type="hidden" v-model="project.goals" data-vv-name="project.goals" data-vv-as="'Objetivos'" v-validate="'required'">
        <span v-show="errors.has('project.goals')" class="help is-danger">
          <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.goals')}}</span>
        <br>
        <div class="content">
          <table class="table is-narrow is-bordered">
            <thead>
              <tr>
                <th>Objetivos</th>
                <th width="50px" class="has-text-centered">
                  <i class="fas fa-times"></i>
                </th>
              </tr>
            </thead>
            <tbody v-if="project.goals.length">
              <tr v-for="(objetivo, index) in project.goals" :key="index">
                <td>
                  <i class="fas fa-flag-checkered fa-fw"></i> {{objetivo}}</td>
                <td class="has-text-centered">
                  <a @click="removeObjetivo(index)">
                    <i class="fas fa-times"></i>
                  </a>
                </td>
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
      </div>
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.schedule')}">
        <i class="fas fa-angle-double-right"></i> Calendario de Actividades *</label>
      <p>Coloque por cada actividad la fecha en que se realizará</p>
      <br>
      <div class="field is-grouped">
        <div class="control">
          <b-datepicker placeholder="Hace clic!" v-model="dateActividad" :mobile-native="false" size="is-medium" :date-formatter="(date) => date.toLocaleDateString('es-AR')" :min-date="new Date()" :max-date="new Date('04/30/2019')" icon="calendar-alt">
          </b-datepicker>
        </div>
        <p class="control is-expanded">
          <b-input size="is-medium" v-model="inputActividad" maxlength="300" placeholder="Escribí la actividad  "></b-input>
        </p>
        <p class="control">
          <b-tooltip :label="disableAddActividad ? 'Falta información' : 'Agregar!'" :type="disableAddActividad ? 'is-danger' : 'is-primary'" position="is-bottom">
            <button @click="addActividad" class="button is-primary is-medium" :disabled="disableAddActividad">
              <i class="far fa-calendar-plus"></i>
            </button>
          </b-tooltip>
        </p>
      </div>
      <input type="hidden" v-model="project.schedule" data-vv-name="project.schedule" data-vv-as="'Actividades'" v-validate="'required'">
      <span v-show="errors.has('project.schedule')" class="help is-danger">
        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.schedule')}}</span>
      <br>
      <div class="content">
        <table class="table is-narrow is-bordered">
          <thead>
            <tr>
              <th width="120px">Fecha</th>
              <th>Actividad</th>
              <th width="50px" class="has-text-centered">
                <i class="fas fa-times"></i>
              </th>
            </tr>
          </thead>
          <tbody v-if="project.schedule.length">
            <tr v-for="(actividad, index) in project.schedule" :key="index">
              <td>
                <i class="far fa-calendar-check fa-fw"></i> {{actividad.date}}</td>
              <td>{{actividad.activity}}</td>
              <td class="has-text-centered">
                <a @click="removeActividad(index)">
                  <i class="fas fa-times"></i>
                </a>
              </td>
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
    </div>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('project.budget')}">
        <i class="fas fa-angle-double-right"></i> Presupuesto *</label>
      <p>
        <span class="tag is-warning">Importante</span>&nbsp;&nbsp;Recuerda que el tope es de $22.000.-</p>
      <br>
      <div class="field is-grouped">
        <p class="control is-expanded">
          <b-input size="is-medium" v-model="inputItemRubro" maxlength="50" placeholder="Rubro Item"></b-input>
        </p>
        <p class="control is-expanded">
          <b-input size="is-medium" v-model="inputItemDescripcion" maxlength="300" placeholder="Descripción Item"></b-input>
        </p>
        <p class="control is-expanded">
          <input class="input is-medium" v-model.number="inputItemMonto" data-vv-name="inputItemMonto" data-vv-as="'Monto'" v-validate="'numeric'" type="text" placeholder="Monto en AR$">
          <span v-if="errors.has('inputItemMonto')" class="help is-danger">
            <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('inputItemMonto')}}</span>
          <span v-else class="help">Ingrese números sin decimal, puntos o comas</span>

        </p>
        <p class="control">
          <b-tooltip :label="disableAddItem ? 'Falta información' : 'Agregar!'" :type="disableAddItem ? 'is-danger' : 'is-primary'" position="is-bottom">
            <button @click="addItem" class="button is-primary is-medium" :disabled="disableAddItem">
              <i class="fas fa-plus"></i>
            </button>
          </b-tooltip>
        </p>
      </div>
      <input type="hidden" v-model="project.budget" data-vv-name="project.budget" data-vv-as="'Presupuesto'" v-validate="'required'">
      <span v-show="errors.has('project.budget')" class="help is-danger">
        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('project.budget')}}</span>
      <br>
      <div class="content">
        <table class="table is-narrow is-bordered">
          <thead>
            <tr>
              <th width="120px">Rubro</th>
              <th>Descripción</th>
              <th class="has-text-centered" width="70px">Monto</th>
              <th width="50px" class="has-text-centered">
                <i class="fas fa-times"></i>
              </th>
            </tr>
          </thead>
          <tbody v-if="project.budget.length">
            <tr v-for="(item, index) in project.budget" :key="index">
              <td>{{item.category}}</td>
              <td>{{item.description}}</td>
              <td class="has-text-centered">$ {{item.amount}}</td>
              <td class="has-text-centered">
                <a @click="removeItem(index)">
                  <i class="fas fa-times"></i>
                </a>
              </td>
            </tr>
            <tr>
              <th colspan="2" class="has-text-right">Monto total:</th>
              <td class="has-text-centered">$ {{montoTotal}}</td>
              <td></td>
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
      <label class="label is-size-4" v-if="project.budget.length">
        <i class="fas fa-angle-double-right"></i> Monto total solicitado: $ {{montoTotal}}</label>
    </div>
    <hr>
    <div class="field">
      <label class="label is-size-4" :class="{'has-text-danger': errors.has('conOrganizacion')}">
        <i class="fas fa-angle-double-right"></i> ¿Las actividades las realizarán en coordinación con otras organizaciones y/o instituciones? * </label>
      <p>Se refiere a organizaciones y/o instituciones
        <u>diferentes</u> a la que pertenece el equipo</p>
      <p>
        <b>IMPORTANTE:</b> Si el proyecto se realiza en coordinación con otra institución y/o organización, debera adjuntar la carta aval en el sistema en otro momento.</p>
      <br>
      <b-field>
        <b-radio-button v-model="conOrganizacion" data-vv-name="conOrganizacion" data-vv-as="'Trabajo en conjunto con Organización'" v-validate="'required'" :native-value="true" type="is-primary" size="is-medium">
          <span>
            <i class="fas fa-check"></i> Si</span>
        </b-radio-button>
        <b-radio-button v-model="conOrganizacion" :native-value="false" type="is-primary" size="is-medium">
          <span>
            <i class="fas fa-times"></i> No</span>
        </b-radio-button>
      </b-field>
      <span v-show="errors.has('conOrganizacion')" class="help is-danger">
        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('conOrganizacion')}}</span>
    </div>
    <br>
    <form-organizacion ref="formOrganizacion" v-if="conOrganizacion" :organization.sync="project.organization"></form-organizacion>
  </div>
</template>

<script>
import Localidad from "./FieldLocalidad";
import ShowLocalidad from "./GetLocalidad";

import FormOrganizacion from "./FormOrganizacion";
export default {
  props: ["project"],
  components: {
    Localidad,
    ShowLocalidad,
    FormOrganizacion
  },
  data() {
    return {
      isInitialized: false,
      showLocalityField: false,
      conOrganizacion: null,
      enEjecucion: null,
      radioEstado: null,
      inputObjetivos: null,
      inputActividad: null,
      inputItemRubro: null,
      inputItemDescripcion: null,
      inputItemMonto: null,
      dateActividad: null,
      categoriasLoading: false,
      categorias: []
    };
  },
  created: function() {
    if (this.project.organization) {
      this.conOrganizacion = true;
      this.isInitialized = true;
    } else if (this.project.organization === false) {
      this.conOrganizacion = false;
    }
    if (this.project.previous_work) {
      this.enEjecucion = true;
    } else if (this.project.previous_work === false) {
      this.enEjecucion = false;
    }
    this.showLocalityField = this.project.locality_id === null ? true : false;
  },
  mounted: function() {
    this.categoriasLoading = true;
    this.$http
      .get("/category")
      .then(response => {
        this.categorias = response.data;
        this.categoriasLoading = false;
      })
      .catch(error => {
        console.error(error.message);
        this.$snackbar.open({
          message:
            "Error de conexion con el servidor. No pudimos rescatar las categorias. Sin esto no se va a poder completar el formulario. Por favor, volvé a intentarlo más tarde o reintentá volviendo a cargar la pagina.",
          type: "is-danger",
          actionText: "Cerrar",
          indefinite: true
        });
        this.categoriasLoading = true;
      });
  },
  methods: {
    updateLocalidad: function(id) {
      this.project.locality_id = id;
    },
    updateLocalidadCustom: function(localidadCustom) {
      this.project.locality_other = localidadCustom;
    },
    addObjetivo: function() {
      if (!this.disableAddObjetivo) {
        this.project.goals.push(this.inputObjetivos);
        this.inputObjetivos = "";
      }
    },
    removeObjetivo: function(index) {
      this.project.goals.splice(index, 1);
    },
    addActividad: function() {
      if (!this.disableAddActividad) {
        this.project.schedule.push({
          date: this.dateActividad.toISOString().split("T")[0],
          activity: this.inputActividad
        });
        this.dateActividad = null;
        this.inputActividad = null;
      }
    },
    removeActividad: function(index) {
      this.project.schedule.splice(index, 1);
    },
    addItem: function() {
      this.$validator
        .validate("inputItemMonto", this.inputItemMonto)
        .then(result => {
          if (result) {
            if (!this.disableAddItem) {
              if (parseFloat(this.inputItemMonto) + this.montoTotal > 22000) {
                this.$snackbar.open(
                  "El item excede el total permitido ($22000)"
                );
                return;
              }
              this.project.budget.push({
                category: this.inputItemRubro,
                description: this.inputItemDescripcion,
                amount: this.inputItemMonto
              });
              this.inputItemRubro = null;
              this.inputItemDescripcion = null;
              this.inputItemMonto = null;
            }
          } else {
            this.$snackbar.open(
              "El monto debe ser un numero sin coma ni punto decimal"
            );
          }
        });
    },
    removeItem: function(index) {
      this.project.budget.splice(index, 1);
    },
    validateForm: function() {
      let promise = new Promise((resolve, reject) => {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log("Proyecto: Hay errores en los datos");
            return resolve(result);
          }
          console.log("Proyecto: OK");
          return resolve(result);
        });
      });
      return promise;
    },
    validateOrganizacion: function() {
      if (this.conOrganizacion) {
        return this.$refs.formOrganizacion.validateForm();
      } else {
        return true;
      }
    },
    validateLocalidadOrganizacion: function() {
      if (this.conOrganizacion) {
        return this.$refs.formOrganizacion.validateLocalidad();
      } else {
        return true;
      }
    },
    validateLocalidad: function() {
      return this.showLocalityField
        ? this.$refs.localidadForm.validateForm()
        : true;
    },
    cleanLocalidad: function() {
      this.project.locality_id = null;
      this.project.locality_other = null;
      this.showLocalityField = true;
    }
  },
  computed: {
    montoTotal: function() {
      const reducer = (accumulator, item) =>
        accumulator + parseFloat(item.amount);
      return this.project.budget.reduce(reducer, 0);
    },
    disableAddItem: function() {
      return (
        this.inputItemRubro == null ||
        this.inputItemRubro == "" ||
        this.inputItemDescripcion == null ||
        this.inputItemDescripcion == "" ||
        this.inputItemMonto == null ||
        this.inputItemMonto == ""
      );
    },
    disableAddActividad: function() {
      return (
        this.inputActividad == "" ||
        this.inputActividad == null ||
        this.dateActividad == null
      );
    },
    disableAddObjetivo: function() {
      return this.inputObjetivos == "" || this.inputObjetivos == null;
    }
  },
  watch: {
    enEjecucion: function(newVal, oldVal) {
      if (newVal === false) {
        this.project.previous_work = null;
      }
    },
    conOrganizacion: function(newVal, oldVal) {
      if (newVal) {
        if (this.isInitialized) {
          this.isInitialized = false;
        } else {
          this.project.organization = {
            name: null,
            topics: [],
            topic_other: null,
            locality_id: null,
            locality_other: null,
            web: null,
            facebook: null,
            telephone: null,
            email: null
          };
        }
      } else {
        this.project.organization = null;
      }
    }
  }
};
</script>

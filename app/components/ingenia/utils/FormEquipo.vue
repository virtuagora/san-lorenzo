<template>
    <section>
        <div class="field">
            <label class="label is-size-4" :class="{'has-text-danger': errors.has('team.name')}">
                <i class="fas fa-angle-double-right"></i> Nombre del equipo</label>
            <div class="control">
                <input v-model="team.name" data-vv-name="team.name" data-vv-as="'Nombre del equipo'" type="text" v-validate="'required|min:1|max:100'" class="input is-large" placeholder="(Requerido)">
                <span v-show="errors.has('team.name')" class="help is-danger">
                    <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('team.name')}}</span>
            </div>
        </div>
        <div class="field">
            <label class="label is-size-4" :class="{'has-text-danger': errors.has('team.description')}">
                <i class="fas fa-angle-double-right"></i> Acerca del equipo</label>
            <p>Breve descripción de tu equipo. Contanos acerca de a qué se dedican, como se formó, actividades mas recientes, etc. Máximo 1000 caracteres</p>
            <div class="control">
                <b-input v-model="team.description" data-vv-name="team.description" data-vv-as="'Descripción del equipo'" v-validate="'required|min:10|max:1000'" type="textarea" minlength="10" maxlength="1000" rows="3" placeholder="(Requerido). Breve descripcion de tu equipo">
                </b-input>
                <span v-show="errors.has('team.description')" class="help is-danger">
                    <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('team.description')}}</span>
            </div>
        </div>
        <label class="label is-size-4">
            <i class="fas fa-angle-double-right"></i> ¿De donde es el equipo? *
        </label>
        <Localidad v-if="showLocalityField" ref="localidadForm" @updateLocalidad="updateLocalidad" @updateLocalidadCustom="updateLocalidadCustom"></Localidad>
        <div v-else>
            <button @click="cleanLocalidad" class="button is-light is-pulled-right">Cambiar ubicación</button>
            <show-localidad :locality-id="team.locality_id" :locality-other="team.locality_other"></show-localidad>
            <br>
            <br>
        </div>
        <div class="field">
            <label class="label is-size-4" :class="{'has-text-danger': errors.has('team.year')}">
                <i class="fas fa-angle-double-right"></i> ¿En que año se conformó el equipo? *</label>
                <!-- <b-select size="is-medium" v-model="team.year" data-vv-name="team.year" data-vv-as="'Región/Nodo'" v-validate="'required'" placeholder="Elija el año" expanded>
                    <option v-for="number in listYearFoundation" :key="number" :value="number">{{number}}</option>
                </b-select> -->
                 <b-radio v-for="number in listYearFoundation" :key="number" v-model="team.year" data-vv-name="team.year" data-vv-as="'Año de conformación'"  v-validate="'required'" placeholder="Elija el año" expanded
                :native-value="number">
                Año {{number}}
            </b-radio>
        </div>
        <div class="field">
            <label class="label is-size-4">
                <i class="fas fa-angle-double-right"></i> (Opcional) Participación en años anteriores</label>
            <p>Si el equipo (o algun integrante) participó en ediciones anteriores de Ingenia, seleccione el o los años.</p>
            <br>
            <b-checkbox v-model="team.previous_editions" v-for="year in listPreviousEditions" :key="year" :native-value="year">
                Año {{year}}
            </b-checkbox>
        </div>
        <div class="field">
            <label class="label is-size-4">
                <i class="fas fa-angle-double-right"></i> Redes y contacto del equipo</label>
            <p>Escriba un email y un telefono de contacto. Estos dos
                <u>son requeridos</u>
            </p>
            <div class="field is-grouped">
                <div class="control">
                    <a @click.prevent class="button is-medium is-static">
                        <span class="icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </a>
                </div>
                <p class="control is-expanded">
                    <input v-model="team.email" data-vv-name="team.email" data-vv-as="'Email de contacto'" v-validate="'required|email'" class="input is-medium" type="team.email" placeholder="(Requerido) Email de contacto">
                    <span v-show="errors.has('team.email')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>{{errors.first('team.email')}}</span>
                </p>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <a @click.prevent class="button is-medium is-static">
                        <span class="icon">
                            <i class="fas fa-phone"></i>
                        </span>
                    </a>
                </div>
                <p class="control is-expanded">
                    <input v-model="team.telephone" data-vv-name="team.telephone" data-vv-as="'Teléfono de contacto'" v-validate="'required|max:20'" class="input is-medium" type="text" placeholder="(Requerido) Ej: 0342 - 4123456">
                    <span v-show="errors.has('team.telephone')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('team.telephone')}}</span>
                </p>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <a @click.prevent class="button is-medium is-static">
                        <span class="icon">
                            <i class="fas fa-globe"></i>
                        </span>
                    </a>
                </div>
                <p class="control is-expanded">
                    <input v-model="team.web" data-vv-name="team.web" data-vv-as="'Web del equipo'" v-validate="'url|max:100'" class="input is-medium" type="text" placeholder="(Opcional) URL página web (Ej: http://www.miequipo.org)">
                    <span v-show="errors.has('team.web')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('team.web')}}</span>
                </p>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <a @click.prevent class="button is-medium is-static">
                        <span class="icon">
                            <i class="fab fa-facebook"></i>
                        </span>
                    </a>
                </div>
                <p class="control is-expanded">
                    <input v-model="team.facebook" data-vv-name="team.facebook" data-vv-as="'Facebook del equipo'" v-validate="'max:100'"  class="input is-medium" type="text" placeholder="(Opcional) https://www.facebook.com/GabineteJoven">
                    <span v-show="errors.has('team.facebook')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('team.facebook')}}</span>
                </p>
            </div>
        </div>
        <div class="field">
            <label class="label is-size-4" :class="{'has-text-danger': errors.has('deUnaOrganizacion')}">
                <i class="fas fa-angle-double-right"></i> ¿El equipo pertenece a alguna organización o institución? * </label>
            <b-field>
                <b-radio-button v-model="deUnaOrganizacion" data-vv-name="deUnaOrganizacion" data-vv-as="'Organizacion a la que pertenece el equipo'" v-validate="'required'" :native-value="true" type="is-primary" size="is-medium">
                    <span>
                        <i class="fas fa-check"></i> Si</span>
                </b-radio-button>
                <b-radio-button v-model="deUnaOrganizacion" :native-value="false" type="is-primary" size="is-medium">
                    <span>
                        <i class="fas fa-times"></i> No</span>
                </b-radio-button>
            </b-field>
            <span v-show="errors.has('deUnaOrganizacion')" class="help is-danger">
                <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('deUnaOrganizacion')}}</span>
        </div>
        <form-organizacion ref="formOrganizacion" v-if="deUnaOrganizacion" :organization.sync="team.parent_organization"></form-organizacion>
    </section>
</template>

<script>
import Localidad from "./FieldLocalidad";
import ShowLocalidad from "./GetLocalidad";
import FormOrganizacion from "./FormOrganizacion";

export default {
  props: ["team"],
  components: {
    Localidad,
    FormOrganizacion,
    ShowLocalidad
  },
  data() {
    return {
      isInitialized: false,
      showLocalityField: false,
      deUnaOrganizacion: null,
      listPreviousEditions: [],
      filteredPreviousEditions: [],
      listYearFoundation: [2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990]
    };
  },
  created: function() {
    if (this.team.parent_organization) {
      this.deUnaOrganizacion = true;
      this.isInitialized = true;
    } else if(this.team.parent_organization === false){
      this.deUnaOrganizacion = false;
    }
    this.showLocalityField = this.team.locality_id === null ? true : false;
    for (let i = 2011; i <= 2017; i++) {
      this.listPreviousEditions.push(i);
    }
  },
  mounted: function() {
    // for (let i = 1990; i <= 2018; i++) {
    //   this.listYearFoundation.push(i);
    // }
    //   this.listYearFoundation.reverse()
  },
  methods: {
    updateLocalidad: function(id) {
      this.team.locality_id = id;
    },
    updateLocalidadCustom: function(localityName) {
      this.team.locality_other = localityName;
    },
    validateForm: function() {
      let promise = new Promise((resolve, reject) => {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log("Equipo: Hay errores en los datos");
            return resolve(result);
          }
          console.log("Equipo: OK");
          return resolve(result);
        });
      });
      return promise;
    },
    validateOrganizacion: function() {
      if (this.deUnaOrganizacion) {
        return this.$refs.formOrganizacion.validateForm();
      } else {
        return true;
      }
    },
    validateLocalidadOrganizacion: function() {
      if (this.deUnaOrganizacion) {
        return this.$refs.formOrganizacion.validateLocalidad();
      } else {
        return true;
      }
    },
    validateLocalidad: function() {
      return (this.showLocalityField ? this.$refs.localidadForm.validateForm() : true);
    },
    cleanLocalidad: function() {
      this.team.locality_id = null;
      this.team.locality_other = null;
      this.showLocalityField = true;
    }
  },
  watch: {
    deUnaOrganizacion: function(newVal, oldVal) {
      if (newVal) {
        if (this.isInitialized) {
          this.isInitialized = false;
        } else {
          this.team.parent_organization = {
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
        this.team.parent_organization = null;
      }
    }
  }
};
</script>

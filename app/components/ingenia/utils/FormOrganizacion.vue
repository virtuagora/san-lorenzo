<template>
  <div>
    <div>
      <div class="field">
        <label class="label is-size-4" :class="{'has-text-danger': errors.has('dummyOrganization.name')}">
          <i class="fas fa-angle-double-right"></i> Nombre de la organización *</label>
        <input type="text" v-model="dummyOrganization.name" data-vv-name="dummyOrganization.name" data-vv-as="'Nombre organización'" v-validate="'required|max:100'" class="input is-medium" placeholder="Requerido *">
        <span v-show="errors.has('dummyOrganization.name')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.name')}}</span>
      </div>
      <br>
      <div class="field">
        <label class="label is-size-4">
          <i class="fas fa-angle-double-right"></i> ¿Qué temas trabaja la organización?</label>
        <div class="columns">
          <div class="column">
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Arte y Cultura">Arte y Cultura</b-checkbox>
            </div>
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Comunicación">Comunicación</b-checkbox>
            </div>
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Deporte y recreación">Deporte y recreación</b-checkbox>
            </div>
          </div>
          <div class="column">
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Diversidad Sexual">Diversidad Sexual</b-checkbox>
            </div>
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Integración Social">Integración Social</b-checkbox>
            </div>
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Medio Ambiente">Medio Ambiente</b-checkbox>
            </div>

          </div>
          <div class="column">
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Salud y Discapacidad">Salud y Discapacidad</b-checkbox>
            </div>
            <div class="field">
              <b-checkbox v-model="dummyOrganization.topics" native-value="Ciudadanía y Participación">Ciudadanía y Participación</b-checkbox>
            </div>
            <div class="field">
              <input type="text" v-model="dummyOrganization.topic_other" data-vv-name="dummyOrganization.topic_other" data-vv-as="'Otra temática'" v-validate="'max:250'" class="input" placeholder="Otro tema">
              <span v-show="errors.has('dummyOrganization.topic_other')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.topic_other')}}</span>
            </div>
          </div>
        </div>
      </div>
      <label class="label is-size-4">
      <i class="fas fa-angle-double-right"></i> ¿De donde es la organización? *
    </label>
      <Localidad v-if="showLocalityField" ref="localidadOrganizationForm" @updateLocalidad="updateLocalidad" @updateLocalidadCustom="updateLocalidadCustom"></Localidad>
        <div v-else>
        <button @click="cleanLocalidad" class="button is-light is-pulled-right">Cambiar ubicación</button>
        <show-localidad :locality-id="dummyOrganization.locality_id" :locality-other="dummyOrganization.locality_other"></show-localidad>
        <br>
        <br>
        </div>
      <div class="field">
        <label class="label is-size-4">
          <i class="fas fa-angle-double-right"></i> Redes y contacto</label>
        <div class="field is-grouped">
          <div class="control">
            <a @click.prevent class="button is-medium is-static">
              <span class="icon">
                <i class="fas fa-envelope"></i>
              </span>
            </a>
          </div>
          <p class="control is-expanded">
            <input v-model="dummyOrganization.email" data-vv-name="dummyOrganization.email" data-vv-as="'Email de la organización'" v-validate="'email'" class="input is-medium" type="email" placeholder="(Opcional) Email de contacto">
            <span v-show="errors.has('dummyOrganization.email')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.email')}}</span>
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
            <input v-model="dummyOrganization.telephone" data-vv-name="dummyOrganization.telephone" data-vv-as="'Teléfono de contacto'" v-validate="'max:20'" class="input is-medium" type="text" placeholder="(Opcional) Ej: 0342 - 4123456">
<span v-show="errors.has('dummyOrganization.telephone')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.telephone')}}</span>
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
            <input v-model="dummyOrganization.web" data-vv-name="dummyOrganization.web" data-vv-as="'Web de la organización'" v-validate="'url|max:100'" class="input is-medium" type="text" placeholder="(Opcional) URL página web">
            <span v-show="errors.has('dummyOrganization.web')" class="help is-danger"><i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.web')}}</span>
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
            <input v-model="dummyOrganization.facebook" data-vv-name="dummyOrganization.facebook" data-vv-as="'Facebook de la organización'"  v-validate="'max:100'" class="input is-medium" type="text" placeholder="(Opcional) https://www.facebook.com/GabineteJoven">
            <span v-show="errors.has('dummyOrganization.facebook')" class="help is-danger">
                        <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('dummyOrganization.facebook')}}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Localidad from "./FieldLocalidad";
import ShowLocalidad from "./GetLocalidad";


export default {
  props: ["organization"],
  data() {
    return {
      dummyOrganization: this.organization,
      showLocalityField: false,      
    };
  },
  components: {
    Localidad,
    ShowLocalidad
  },
  created: function(){
      this.showLocalityField = this.dummyOrganization.locality_id === null ? true : false     
  },
  methods: {
    updateLocalidad: function(id) {
      this.dummyOrganization.locality_id = id;
    },
    updateLocalidadCustom: function(localityName) {
      this.dummyOrganization.locality_other = localityName;
    },
    validateForm: function() {
      let promise = new Promise((resolve, reject) => {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log("Organizacion: Hay errores en los datos");
            return resolve(result);
          }
          console.log("Organizacion: OK");
          return resolve(result);
        });
      });
      return promise;
    },
    validateLocalidad: function() {
      return (this.showLocalityField ? this.$refs.localidadOrganizationForm.validateForm() : true);
    },
    cleanLocalidad: function(){
      this.dummyOrganization.locality_id = null
      this.dummyOrganization.locality_other = null
      this.showLocalityField = true
    },
  }
};
</script>

<template>
  <section>
    <div class="card">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 2 - Pida el numero de celular
        </h1>
        <h1 class="subtitle is-6">Ingrese el numero de celular del ciudadano</h1>
        <div class="columns">
          <div class="column is-3">
            <label class="label">Código de area (sin 0)</label>
            <b-field>
              <div class="control">
                <span class="button is-medium is-static">0</span>
              </div>
              <div class="control is-expanded">
                <input
                  type="text"
                  class="input is-medium"
                  v-model="codigoArea"
                  data-vv-name="codigo-area"
                  data-vv-as="'Código de area'"
                  placeholder="Código de area"
                  v-validate="'required|integer|min:2'"
                  :disabled="showStepThree"
                />
              </div>
            </b-field>
            <p v-show="errors.has('codigo-area')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>
              {{errors.first('codigo-area')}}
            </p>
          </div>
          <div class="column is-6">
            <label class="label">Numero de telefono (sin el 15)</label>
            <b-field>
              <div class="control">
                <span class="button is-medium is-static">15</span>
              </div>
              <div class="control is-expanded">
                <input
                  type="text"
                  class="input is-medium"
                  v-model="numero"
                  data-vv-name="numero"
                  data-vv-as="'Numero de télefono'"
                  placeholder="Numero de teléfono"
                  v-validate="'required|integer|min:4'"
                  :disabled="showStepThree"
                />
              </div>
            </b-field>
            <p v-show="errors.has('numero')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>
              {{errors.first('numero')}}
            </p>
          </div>
        </div>
        <button
          @click="showStepThree = true"
          v-if="!showStepThree"
          class="button is-medium is-primary is-fullwidth is-outlined"
          :disabled="errors.has('numero') || errors.has('numero') || !this.codigoArea || !this.numero"
        >
          <i class="fas fa-arrow-right"></i>&nbsp;Siguiente paso
        </button>
      </div>
    </div>
    <div class="card" v-if="showStepThree">
      <div class="card-content">
        <div class="columns">
          <div class="column">
            <h1 class="title is-5 has-text-link">
              <i class="fas fa-arrow-right"></i>&nbsp; Paso 3 - Verificar el WhatsApp del ciudadano
            </h1>
            <h1 class="subtitle is-6">A continuación enviará un mensaje de prueba para verificar que es el ciudadano.</h1>
            <div class="content">
              <ol>
                <li>Cargue el lector QR del celular del equipo de presupuesto</li>
                <li>Haga un scaneo del codigo QR y vaya al link que le indica. <i>Se abrira WhatsApp Messenger</i></li>
                <li>Mande el mensaje</li>
                <li>Espere a que el ciudadano <b>le muestre que recibio el mensaje.</b></li>
              </ol>
            </div>
          </div>
          <div class="column is-narrow has-text-centered" v-if="!showStepFour">
            <qrcode :value="urlVerificarNumero" :options="{width: 300,margin:1}" class="myqr"></qrcode>
          </div>
        </div>
        <div class="columns" v-if="!showStepFour">
          <div class="column">
            <button @click="showStepThree = false" class="button is-medium is-primary is-fullwidth is-outlined">
              <i class="fas fa-reply"></i>&nbsp;Corregir numero
            </button>
          </div>
          <div class="column">
            <a
              @click="showStepFour = true"
              class="button is-medium is-success is-fullwidth"
            >
              <i class="fas fa-check"></i>&nbsp;Recibio el mensaje correctamente
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="card" v-if="showStepFour">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 4 - Confirmar participación en el padron
        </h1>
        <div class="content">
          <ol>
            <li>Confirme con el usuario quiere recibir el link para votar desde su celular. Puede volver a elegir otro metodo de votación.</li>
            <li>Una vez que confirme, marquelo en el padron.</li>
            <li>
              Al marcarlo en el padron,
              <b>se generará un codigo QR</b> para mandar por <i class="fab fa-whatsapp"></i>&nbsp;WhatsApp el link para entrar a votar.
            </li>
          </ol>
          <p class="has-text-danger">Recuerde, una vez generado el LINK, el ciudadano no podra volver a elegir otra forma de votación</p>
        </div>
        <div class="notification is-danger">
              <i class="fas fa-exclamation-triangle"></i>&nbsp;El link que se enviará por <i class="fab fa-whatsapp"></i> es de
              <b>UNICO USO E INTRANSFERIBLE</b>, luego de ser generado
              <b>NO PUEDE VOLVER A SER CONSULTADO</b>. Por lo tanto le recomendamos
              <b>NO CERRAR ESTA VENTANA</b> hasta terminar el proceso.
            </div>
        <div class="columns" v-if="!response">
          <div class="column">
            <button @click="resetAll" class="button is-medium is-primary is-fullwidth is-outlined">
              <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
            </button>
          </div>
          <div class="column">
            <a
              @click="submitCitizen"
              class="button is-medium is-link is-fullwidth"
              :class="{'is-loading': loadingMarking}"
              :disabled="loadingMarking"
            >
              <i class="fas fa-arrow-right"></i>&nbsp;Confirmar participación
            </a>
          </div>
        </div>
        <section v-if="response && responseOk">
          <div class="notification is-success has-text-centered" style="margin-top:10px;">
            <i class="fas fa-check fa-fw"></i>&nbsp;¡Participacion guardada con exito!
          </div>
        </section>
        <section v-if="response && responseFail">
          <div class="notification is-danger has-text-centered" style="margin-top:10px;">
            <p>
              <i class="fas fa-exclamation-triangle fa-fw"></i>&nbsp;Ocurrio un error al guardar la participación
            </p>
            <p>Por favor, vuelva a comenzar todo el proceso de nuevo</p>
            <br />
            <button @click="resetAll" class="button is-medium is-white is-outlined">
              <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
            </button>
          </div>
        </section>
      </div>
    </div>
    <div class="card" v-if="showStepFive">
      <div class="card-content">
        <div class="columns">
          <div class="column">
            <h1 class="title is-5 has-text-link">
              <i class="fas fa-arrow-right"></i>&nbsp; Paso 5 - Enviar link por <i class="fab fa-whatsapp"></i>&nbsp;WhatsApp
            </h1>
            <h1 class="subtitle is-6">Escanee el codigo QR para mandarle el link para votar</h1>
            <div class="content">
              <ol>
                <li>Cargue el lector QR del celular del equipo de presupuesto</li>
                <li>Haga un scaneo del codigo QR y vaya al link que le indica. <i>Se abrira WhatsApp Messenger</i></li>
                <li>Mande el mensaje, el mismo contiene el <b>link unico</b> para que el ciudadadano pueda votar.</li>
              </ol>
            </div>
          </div>
          <div class="column is-narrow">
            <qrcode :value="urlUrna" :options="{width: 250,margin:1}" class="myqr"></qrcode>
            <div class="notification is-black has-text-centered">
              <h1 class="subtitle is-6">Código del usuario</h1>
              <h1 class="title is-4 spaced-code">{{code}}</h1>
            </div>
          </div>
        </div>
        <button
          @click="showStepSix = true"
          v-if="!showStepSix"
          class="button is-medium is-primary is-fullwidth is-outlined"
        >
          <i class="fas fa-arrow-right"></i>&nbsp;Listo, mensaje enviado!
        </button>
      </div>
    </div>
    <div class="card" v-if="showStepSix">
      <div class="card-content">
        <h1 class="title is-5 has-text-link">
          <i class="fas fa-arrow-right"></i>&nbsp; Paso 6 - Exito!
        </h1>
        <h1 class="subtitle is-6">Listo! Puede continuar con el siguiente ciudadano.</h1>
        <button @click="resetAll" class="button is-medium is-primary is-fullwidth">
          <i class="fas fa-reply"></i>&nbsp;Volver a comenzar
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import VueQrcode from "@chenfengyuan/vue-qrcode";

export default {
  props: ["citizen", "urlParticipacion", "urlPublicVote"],
  data() {
    return {
      codigoArea: null,
      numero: null,
      loadingMarking: false,
      response: false,
      responseFail: false,
      responseOk: false,
      showStepThree: false,
      showStepFour: false,
      showStepFive: false,
      showStepSix: false,
      code: null
    };
  },
  components: {
    qrcode: VueQrcode
  },
  methods: {
    submitCitizen: function() {
      this.loadingMarking = true;
      this.$http
        .post(this.urlParticipacion, this.payload)
        .then(response => {
          this.$snackbar.open({
            message: "¡Participación guardada!",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.code = response.data.codigo;
          this.responseOk = true;
          this.showStepFive = true;
        })
        .catch(error => {
          console.error(error.message);
          this.responseFail = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar",
            indefinite: true
          });
        })
        .finally(() => {
          this.loadingMarking = false;
          this.response = true;
        });
    },
    resetAll: function() {
      window.scrollTo(0, 0);
      this.codigoArea = null,
      this.numero = null,
      this.loadingMarking = false,
      this.response = false,
      this.responseFail = false,
      this.responseOk = false,
      this.showStepThree = false,
      this.showStepFour = false,
      this.showStepFive = false,
      this.showStepSix = false,
      this.code = null
      this.$emit("resetAll");
    }
  },
  computed: {
    urlVerificarNumero: function() {
      return `https://wa.me/549${this.codigoArea}${this.numero}?text=${encodeURI('Hola, ¡Somos el equipo de PP de San Lorenzo!\nPara comenzar, *mostrá que te llego este mensaje* a los oficiales de la mesa de votación para obtener tu link.\n\n_(Desestime el mensaje si lo recibio por accidente)_')}`;
    },
    urlUrna: function() {
      return `https://wa.me/549${this.codigoArea}${this.numero}?text=${encodeURI('Este es tu link para votar en el presupuesto:\n'+this.urlPublicVote+'?code='+this.code+'\n\n_El link es único, e intrasferible._')}`;
    },
    payload: function() {
      return {
        matricula: this.citizen.dni,
        tipo: "link"
      };
    },
  }
};
</script>


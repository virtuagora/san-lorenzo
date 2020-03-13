<template>
  <section>
    <div class="message is-dark">
      <div class="message-body">
    <p class="is-size-7 has-text-centered">Nota: Los siguientes valores son estimativos. Ciertas cifras pueden llegar a no ser representativos en un 100% de la realidad</p>
        <br>
        <nav class="level">
          <div class="level-item">
            <h1 class="title is-3">Boletas</h1>
          </div>
          <div class="level-item has-text-centered">
            <div>
              <p class="heading">
                <i class="fas fa-envelope-open-text"></i>
                Registrados votos Offline</p>
              <p class="title">{{cantVotos}}</p>
            </div>
          </div>
          <div class="level-item has-text-centered">
            <div>
              <p class="heading">
                <i class="fas fa-inbox"></i>
                Boletas cargadas</p>
              <p class="title">{{boletas.length}}</p>
            </div>
          </div>
          <!-- <div class="level-item has-text-centered">
            <div>
              <p class="heading">
                <i class="fas fa-envelope-open-text"></i>
                Faltan</p>
              <p class="title">{{faltan}}</p>
            </div>
          </div> -->
          <div class="level-item has-text-centered">
            <div>
              <p class="heading">
                <i class="fas fa-envelope-open-text"></i>
                Porcentaje</p>
              <p class="title">{{parseFloat(faltanPorcentaje).toFixed(2)}}%</p>
            </div>
          </div>
          <div class="level-item">
            <h1 class="title is-1">
              <i :class="faltanEmoji"></i>
            </h1>
          </div>
        </nav>
      </div>
    </div>
    <div class="box">
      <div class="field is-horizontal">
        <div class="field-label is-medium">
          <label class="label">N° boleta</label>
        </div>
        <div class="field-body">
          <div class="field">
            <p class="control is-expanded has-icons-left">
              <input v-model="numberBoleta" class="input is-medium" :class="{'is-danger': errors.has('numero')}" type="text" placeholder="N° Boleta" data-vv-name="numero" v-validate="'required|numeric|min_value:1'">
              <span class="icon is-small is-left">
                <i class="fas fa-hashtag"></i>
              </span>
            </p>
            <span v-show="errors.has('numero')" class="help is-danger">
              <i class="fas fa-times-circle fa-fw"></i>&nbsp;{{errors.first('numero')}}</span>
          </div>
        </div>
      </div>
      <div class="field is-horizontal">
        <div class="field-label"></div>
        <div class="field-body">
          <p>Recuerde que una boleta puede tener votos en blanco. Ingresela de todas formas. Tambien puede anular el voto.</p>
        </div>
      </div>
      <div class="field is-horizontal">
        <div class="field-label is-medium">
          <label class="label">Comunitarios</label>
        </div>
        <div class="field-body">
          <div class="field">
            <div class="control" v-if="!isLoadingProjects">
              <div class="block">
                <b-checkbox v-model="checkComunitarios" size="is-medium" v-for="project in comunitarios" type="is-success" :key="project.id" :native-value="project.id">
                  <span class="is-mono" v-if="project.code.length < 5">{{project.code}}&nbsp;</span>
                  <span class="is-mono" v-else>{{project.code}}</span>
                </b-checkbox>
              </div>
            </div>
            <div class="notification" v-else>
              <i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;Cargando, un momento por favor
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="field is-horizontal">
        <div class="field-label is-medium">
          <label class="label">Institucionales</label>
        </div>
        <div class="field-body">
          <div class="field">
            <div class="control" v-if="!isLoadingProjects">
              <div class="block">
                <b-checkbox v-model="checkInstitucionales" size="is-medium" v-for="project in institucionales" type="is-link" :key="project.id" :native-value="project.id">
                  <span class="is-mono" v-if="project.code.length < 5">{{project.code}}&nbsp;</span>
                  <span class="is-mono" v-else>{{project.code}}</span>
                </b-checkbox>
              </div>
            </div>
            <div class="notification" v-else>
              <i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;Cargando, un momento por favor
            </div>
          </div>
        </div>
      </div>
      <div class="field">
        <div class="control is-clearfix">
          <button @click="submitInvalidBoleta()" class="button is-outlined is-dark is-pulled-left" :disabled="checkInstitucionales.length > 3 || checkComunitarios.length > 3 || errors.count() > 0">
            <i class="fas fa-backspace fa-fw fa-lg"></i>&nbsp;&nbsp;BOLETA NULA</button>
          <button @click="submitBoleta()" class="button is-link is-pulled-right" :disabled="checkInstitucionales.length > 3 || checkComunitarios.length > 3 || errors.count() > 0">
            <i class="fas fa-plus fa-fw"></i>&nbsp;Agregar boleta</button>
        </div>
      </div>
    </div>
    <button @click="reloadBoletas()" class="button is-dark is-outlined is-pulled-right">
      <i class="fas fa-sync-alt  fa-fw"></i>&nbsp;Actualizar tabla</button>
    <h1 class="title is-3">Tabla de boletas</h1>
    <p>Aquí podrá ver las boletas offline cargadas en el sistema. Puede borrar una boleta en caso de que sea necesario.</p>
    <table class="table is-fullwidth is-narrow">
      <thead>
        <tr>
          <th width="50">
            <i class="fas fa-hashtag"></i>
          </th>
          <th>Votos</th>
          <th width="200" class="has-text-centered">Fecha carga</th>
          <th width="30" class="has-text-centered">
            <i class="fas fa-times"></i>
          </th>
        </tr>
      </thead>
      <tbody v-if="isLoadingBoletas">
        <tr>
          <td colspan="4" class="has-text-centered">
            <i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;Cargando, un momento por favor
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-if="boletas.length == 0">
          <td colspan="4" class="has-text-centered">
            No hay boletas cargadas
          </td>
        </tr>
        <tr v-for="boleta in boletas" :key="boleta.id">
          <td>{{boleta.order}}</td>
          <td v-if="boleta.invalid">
            <div class="tags" v-if="boleta.votes.length > 0">
              <span class="tag" :class="{'is-success': vote.project.type == 'comunitario', 'is-link': vote.project.type != 'comunitario'}" v-for="vote in boleta.votes" :key="vote.id">{{vote.project.code}}</span>
            </div>
            <span class="tag is-light" v-else>
              Voto en blanco
            </span>
          </td>
          <td v-else>
            <span class="tag is-dark">Boleta NULA</span>
          </td>
          <td class="has-text-centered">{{ boleta.created_at }}</td>
          <td class="has-text-centered">
            <a @click="submitDeleteBoleta(boleta.id)">
              <i class="fas fa-times has-text-danger"></i>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- <b-loading :is-full-page="true" :active.sync="isLoadingProjects"></b-loading>¿ -->
  </section>
</template>

<script>
import { indexOf, without } from "lodash";

export default {
  props: [
    "retrieveProjects",
    "getBoletas",
    "postBoleta",
    "postBoletaNula",
    "deleteBoleta",
    "cantVotos"
  ],
  data() {
    return {
      isLoadingProjects: false,
      isLoadingBoletas: false,
      institucionales: [],
      comunitarios: [],
      checkInstitucionales: [],
      checkComunitarios: [],
      boletas: [],
      numberBoleta: ""
    };
  },
  mounted: function() {
    this.isLoadingProjects = true;
    this.isLoadingBoletas = true;
    Promise.all([
      this.$http.get("/api/proyectos?size=100&type=institucional&feasible=true"),
      this.$http.get("/api/proyectos?size=100&type=comunitario&feasible=true"),
      this.$http.get(this.getBoletas)
    ]).then(results => {
      this.institucionales = results[0].data.data;
      this.comunitarios = results[1].data.data;
      this.boletas = results[2].data;
      this.isLoadingProjects = false;
      this.isLoadingBoletas = false;
    });
  },
  methods: {
    getVotes: function(boleta) {
      return boleta.votes
        .map(vote => {
          return vote.project.code;
        })
        .join(", ");
    },
    submitBoleta: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          // Error validacion
          this.$snackbar.open({
            message: "No se puede enviar porque hay un error en algún dato",
            type: "is-danger",
            actionText: "Cerrar"
          });
        }
        this.$http
          .post(this.postBoleta, this.payload)
          .then(response => {
            this.$snackbar.open({
              message: "¡Boleta guardada!",
              type: "is-success",
              actionText: "¡Genial!"
            });
            this.checkComunitarios = [];
            this.checkInstitucionales = [];
            this.numberBoleta++;
            this.reloadBoletas();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message:
                "Error al guardar la boleta. Puede ser que el Nro ya haya sido cargado...",
              type: "is-danger",
              actionText: "Cerrar"
            });
          });
      });
    },
    submitInvalidBoleta: function() {
      this.$validator.validateAll().then(result => {
        if (!result) {
          // Error validacion
          this.$snackbar.open({
            message: "No se puede enviar porque hay un error en algún dato",
            type: "is-danger",
            actionText: "Cerrar"
          });
        }
        this.$http
          .post(this.postBoletaNula, this.payloadNulo)
          .then(response => {
            this.$snackbar.open({
              message: "¡Boleta guardada!",
              type: "is-success",
              actionText: "¡Genial!"
            });
            this.checkComunitarios = [];
            this.checkInstitucionales = [];
            this.numberBoleta++;
            this.reloadBoletas();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message:
                "Error al guardar la boleta. Puede ser que el Nro ya haya sido cargado...",
              type: "is-danger",
              actionText: "Cerrar"
            });
          });
      });
    },
    submitDeleteBoleta: function(id) {
      this.$http
        .delete(this.deleteBoleta.replace('##',id))
        .then(response => {
          this.$snackbar.open({
            message: "¡Boleta eliminada!",
            type: "is-success",
            actionText: "¡Genial!"
          });
          this.reloadBoletas();
        })
        .catch(error => {
          console.error(error.message);
          this.$snackbar.open({
            message:
              "Error al eliminar la boleta...",
            type: "is-danger",
            actionText: "Cerrar"
          });
        });
    },
    reloadBoletas: function() {
      this.isLoadingBoletas = true;
      this.$http
        .get(this.getBoletas)
        .then(response => {
          this.boletas = response.data;
          this.isLoadingBoletas = false;
        })
        .catch(error => {
          console.error(error.message);
          this.$snackbar.open({
            message: "Error cargando las boletas!",
            type: "is-danger",
            actionText: "Cerrar"
          });
        });
    }
    // toggleCheckboxComunitarios: function(proyecto){
    //     const index = indexOf(this.checkComunitarios, proyecto);
    //     if (index !== -1) {
    //       this.checkComunitarios = without(this.checkComunitarios, proyecto);
    //       return;
    //     }
    //     if(this.checkComunitarios.length === 3){
    //       this.$snackbar.open({
    //         message:
    //           "¡Recuerde! No puede votar mas de 3 proyectos",
    //         type: "is-warning",
    //         actionText: "OK",
    //       });
    //       return;
    //     }
    //     // this.checkComunitarios = [...this.checkComunitarios, proyecto];
    //     return;
    // },
    // toggleCheckboxInstitucionales: function(proyecto){
    //     const index = indexOf(this.checkComunitarios, proyecto);
    //     if (index !== -1) {
    //       this.checkInstitucionales = without(this.checkInstitucionales, proyecto);
    //       return;
    //     }
    //     if(this.checkInstitucionales.length === 3){
    //       this.$snackbar.open({
    //         message:
    //           "¡Recuerde! No puede votar mas de 3 proyectos",
    //         type: "is-warning",
    //         actionText: "OK",
    //       });
    //       return;
    //     }
    //     // this.checkInstitucionales = [...this.checkInstitucionales, proyecto];
    //     return;
    // },
  },
  computed: {
    payload: function() {
      return {
        order: this.numberBoleta,
        comunitarios: this.checkComunitarios.join("&&"),
        institucionales: this.checkInstitucionales.join("&&")
      };
    },
    payloadNulo: function() {
      return {
        order: this.numberBoleta
      };
    },
    faltan: function() {
      return this.cantVotos - this.boletas.length;
    },
    faltanPorcentaje: function() {
      return this.boletas.length / this.cantVotos * 100;
    },
    faltanEmoji: function() {
      if (this.faltanPorcentaje > 0 && this.faltanPorcentaje <= 10) {
        return "far fa-sad-cry";
      } else if (this.faltanPorcentaje > 10 && this.faltanPorcentaje <= 20) {
        return "far fa-frown";
      } else if (this.faltanPorcentaje > 20 && this.faltanPorcentaje <= 35) {
        return "far fa-frown-open";
      } else if (this.faltanPorcentaje > 35 && this.faltanPorcentaje <= 50) {
        return "far fa-meh-blank";
      } else if (this.faltanPorcentaje > 50 && this.faltanPorcentaje <= 65) {
        return "far fa-smile";
      } else if (this.faltanPorcentaje > 35 && this.faltanPorcentaje <= 75) {
        return "far fa-laugh-wink";
      } else if (this.faltanPorcentaje > 35 && this.faltanPorcentaje <= 85) {
        return "far fa-grin-tongue-squint";
      } else if (this.faltanPorcentaje > 35 && this.faltanPorcentaje <= 95) {
        return "far fa-grin-squint-tears";
      } else if (this.faltanPorcentaje > 95) {
        return "far fa-grin-stars";
      }
    }
  },
  watch: {
    checkComunitarios: function(newVal, oldVal) {
      if (newVal.length > 3) {
        this.$snackbar.open({
          message:
            "¡Recuerde! No puede asignar mas de 3 proyectos en comunitarios",
          type: "is-warning",
          actionText: "OK"
        });
      }
    },
    checkInstitucionales: function(newVal, oldVal) {
      if (newVal.length > 3) {
        this.$snackbar.open({
          message:
            "¡Recuerde! No puede asignar mas de 3 proyectos en institucionales",
          type: "is-warning",
          actionText: "OK"
        });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.b-checkbox.checkbox:first-child {
  margin-left: 0.5em;
}
.b-checkbox.checkbox {
  margin-right: 0.8em;
}
</style>

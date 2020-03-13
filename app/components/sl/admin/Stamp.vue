<template>
<section>

  <div class="card">
    <div class="card-content" >
      <button @click="submit" class="button is-primary" v-if="response == null && !isLoading && !responseError && !responseSuccess">
        <i class="fas fa-plus fa-fw"></i><i class="fas fa-stamp fa-fw"></i>&nbsp;&nbsp;Generar nuevo sello
      </button>
      <p v-if="response == null && isLoading && !responseError && !responseSuccess"><i class="fas fa-sync fa-spin fa-lg"></i>&nbsp;&nbsp;Cargando...</p>
      <p v-if="responseError" class="has-text-danger"><i class="fas fa-times fa-lg"></i>&nbsp;Error al generar el sellado del dataset</p>
      <p v-if="responseSuccess" class="has-text-success"><i class="fas fa-check fa-lg"></i>&nbsp;Exito! Se ha generado el nuevo sellado!</p>
      <p v-if="responseSuccess" class="is-size-7">{{response && response.trail.description}}</p>
    </div>
  </div>
  <br>
  <b-table :data="stamps">
    <template slot-scope="props">
      <b-table-column field="id" label="ID" width="40" numeric sortable>
        {{ props.row.id }}
      </b-table-column>
      <b-table-column field="state" label="Estado">
        <p class="is-size-7" style="word-break: break-all;">{{ capitalizeString(props.row.state) }}</p>
      </b-table-column>
      <b-table-column field="description" label="Descripcion">
        <p class="is-size-7" style="word-break: break-all;">{{ props.row.description }}</p>
      </b-table-column>
      <b-table-column label="Acciones" width="150" >
        <p class="is-size-7" v-if="props.row.state == 'created'"><a @click="checkStamp(props.row.id)" title="Comprobar" class="has-text-link"><i class="fa fa-file-import fa-lg"></i>&nbsp;Comprobar estado</a></p>
        <p class="is-size-7" v-if="props.row.state != 'created'"><a :href="makeUrl('receiptUrl',props.row.id)" title="Recibo" class="has-text-link"><i class="fa fa-receipt fa-lg"></i>&nbsp;Descargar recibo</a></p>
        <p class="is-size-7" v-if="props.row.state != 'created'"><a :href="makeUrl('datasetUrl',props.row.id)" class="has-text-link" title="Dataset"><i class="fa fa-database fa-lg"></i>&nbsp;Descargar dataset</a></p>
      </b-table-column>
    </template>
    <template slot="empty">
      <section class="section">
        <div class="content has-text-grey has-text-centered">
            <p><i class="far fa-sad-cry fa-2x"></i></p>
            <p>No se han creado sellos</p>
          </div>
      </section>
    </template>
  </b-table>
  <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
</section>
</template>

<script>
export default {
  props: [
    "getUrl",
    "stampUrl",
    "checkUrl",
    "receiptUrl",
    "datasetUrl",
  ],
  data(){
    return {
      isLoading: false,
      responseError: false,
      responseSuccess: false,
      response: null,
      stamps: []
    }
  },
  mounted: function(){
    this.fetchStamps()
  },
  methods: {
    fetchStamps: function(){
      this.isLoading = true
      this.$http.get(this.getUrl)
      .then( response => {
        this.stamps = response.data.trails
      })
      .catch( err => {
        this.responseError = true;
        this.$snackbar.open({
          message: "Error al obtener los sellos",
          type: "is-danger",
          actionText: "Cerrar",
          indefinite: true
        });
      })
      .finally( () => {
        this.isLoading = false
      })

    },
    checkStamp: function(idStamp){
      this.isLoading = true;
      this.$http.post(this.makeUrl('checkUrl', idStamp))
      .then(response => {
        this.fetchStamps()
      })
      .catch( err => {
        this.isLoading = false
        this.responseError = true;
        this.$snackbar.open({
          message: "Error al chequear el sello",
          type: "is-danger",
          actionText: "Cerrar",
          indefinite: true
        });
      })
    },
    submit: function(){
      this.isLoading = true;
      this.$http.post(this.stampUrl)
      .then(response => {
        this.responseSuccess = true
        this.response = response.data
        this.fetchStamps()
      })
      .catch( err => {
        this.responseError = true;
        this.isLoading = false
        this.$snackbar.open({
          message: "Error al crear el sello",
          type: "is-danger",
          actionText: "Cerrar",
          indefinite: true
        });
      })
    },
    makeUrl: function(theUrl, theParam){
      switch(theUrl){
        case 'checkUrl':
          return this.checkUrl.replace(':aud',theParam)
        case 'receiptUrl':
          return this.receiptUrl.replace(':aud',theParam)
        case 'datasetUrl':
          return this.datasetUrl.replace(':aud',theParam)
      }
    }
  },
};
</script>

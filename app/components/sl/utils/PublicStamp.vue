<template>
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
        <p class="is-size-7" v-if="props.row.state != 'created'"><a :href="makeUrl('receiptUrl',props.row.id)" title="Recibo" class="has-text-link"><i class="fa fa-receipt fa-lg"></i>&nbsp;Descargar recibo</a></p>
        <p class="is-size-7" v-if="props.row.state != 'created' && enableDownload"><a :href="makeUrl('datasetUrl',props.row.id)" class="has-text-link" title="Dataset"><i class="fa fa-database fa-lg"></i>&nbsp;Descargar dataset</a></p>
      </b-table-column>
    </template>
    <template slot="empty">
      <section class="section">
        <div class="content has-text-grey has-text-centered" v-if="!isLoading">
            <p><i class="far fa-sad-cry fa-2x"></i></p>
            <p>No se han creado sellos</p>
          </div>
          <div class="content has-text-grey has-text-centered" v-else>
              <p><i class="far fa-sync fa-spin fa-2x"></i></p>
            <p>Cargando</p>
          </div>
      </section>
    </template>
  </b-table>
</template>

<script>
export default {
  props: [
    "getUrl",
    "receiptUrl",
    "datasetUrl",
    "enableDownload"
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

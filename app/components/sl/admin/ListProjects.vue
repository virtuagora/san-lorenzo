<template>
<section>
  <div class="box">
    <div class="columns">
      <div class="column">
        <div class="field">
          <label for="" class="label">Filtrar por tipo</label>
          <div class="control">
            <div class="select is-fullwidth">
              <select v-model="selectedType">
                <option :value="null">- Todos los tipos - </option>
                <option v-for="(type) in availableTypes" :key="`filter-${type}`" :value="type"> {{capitalizeFirstLetter(type)}}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label for="" class="label">Filtrar por distrito</label>
          <div class="control">
            <div class="select is-fullwidth">
              <select v-model="selectedDistrict">
                <option :value="null">- Todos los distritos -</option>
                <option v-for="(district) in availableDistricts" :key="`filter-district-${district}`" :value="district"> {{district}}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <b-table :data="filteredProjects">
      <b-table-column v-slot="props" field="id" label="ID" width="40" numeric sortable>
      {{ props.row.id }}
      </b-table-column>
      <b-table-column v-slot="props" field="name" label="Propuesta" sortable>
        <p class="is-size-6 is-300"><b><a :href="`/proyectos/${props.row.id}`" class="has-text-link">{{props.row.name}}</a></b></p>
        <p class="is-size-7" v-if="props.row.type == 'comunitario'">Presentado por&nbsp;<b-tooltip position="is-left" multilined label="El responsable se encuentra registrado en la plataforma"  v-if="props.row.author_id"><i class="fas fa-user fa-fw has-text-link"></i></b-tooltip><b>{{props.row.author_names}} {{props.row.author_surnames}}</b></p>
        <div v-else>
        <p class="is-size-7">Institución: <b>{{props.row.organization_name}}</b></p>
        <p class="is-size-7">Presentado por&nbsp;<b-tooltip position="is-left" multilined label="El responsable se encuentra registrado en la plataforma"  v-if="props.row.author_id"><i class="fas fa-user fa-fw has-text-link"></i></b-tooltip><b>{{props.row.author_names}} {{props.row.author_surnames}}</b></p>
        </div>
       <span class="tag is-link" v-if="props.row.code">{{props.row.code}}</span>
       <span class="tag" :class="{'is-comunitario': props.row.type == 'comunitario', 'is-institucional': props.row.type == 'institucional'}">{{capitalizeFirstLetter(props.row.type)}}</span>
       <span class="tag is-light">{{props.row.district.name}}</span>
      </b-table-column>
      <!-- <b-table-column v-slot="props" field="type" label="Tipo" width="100" sortable centered>
      </b-table-column>
      <b-table-column v-slot="props" field="type" label="Distrito" width="100" sortable centered>
      </b-table-column> -->
      <b-table-column v-slot="props" label="Info" width="180">
        <p class="is-size-7">Presupuesto&nbsp;<i class="fas fa-dollar-sign"></i>&nbsp;<b>{{props.row.total_budget}}</b></p>       
        <p class="is-size-7" v-if="props.row.notes == null"><i class="fas fa-times fa-lg fa-fw"></i>&nbsp;Sin observaciones</p>       
        <p class="is-size-7" v-else><i class="far fa-sticky-note fa-lg fa-fw"></i>&nbsp;Hay observaciones hechas</p>       
        <p class="is-size-7" v-if="props.row.feasible === null"><i class="far fa-clock fa-lg fa-fw"></i>&nbsp;Aun sin evaluar</p>       
        <p class="is-size-7 has-text-danger" v-if="props.row.feasible === false"><i class="fas fa-times fa-lg fa-fw"></i>&nbsp;<b>No factible</b></p>       
        <p class="is-size-7 has-text-success" v-if="props.row.feasible === true"><i class="fas fa-clipboard-check fa-lg fa-fw"></i>&nbsp;<b>Factible</b></p>       
      </b-table-column>
      <b-table-column v-slot="props" label="Acciones">
        <div class="columns">
          <div class="column is-narrow">
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/editar`" class="has-text-link"><i class="fas fa-edit fa-lg fa-fw"></i>&nbsp;Editar propuesta</a></p>
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/usuario`" class="has-text-link"><i class="fas fa-user-edit fa-lg fa-fw"></i>&nbsp;Asignar usuario</a></p>
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/archivos`" class="has-text-link"><i class="fas fa-upload fa-lg fa-fw"></i>&nbsp;Subir archivos</a></p>
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/imagen`" class="has-text-link"><i class="fas fa-image fa-lg fa-fw"></i>&nbsp;Asignar una imagen</a></p>

          </div>
          <div class="column is-narrow">
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/factibilidad`" class="has-text-link"><i class="fas fa-clipboard-check fa-lg fa-fw"></i>&nbsp;Factibilidad</a></p>
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/bitacora`" class="has-text-link"><i class="fas fa-history fa-lg fa-fw"></i>&nbsp;Bicatora</a></p>
        <p class="is-size-7"><a :href="`/admin/proyectos/${props.row.id}/imprimir`" class="has-text-link"><i class="fas fa-print fa-lg fa-fw"></i>&nbsp;Imprimir</a></p>
          </div>
        </div>
      </b-table-column>
    <template #empty>
      <section class="section">
        <div class="content has-text-grey has-text-centered">
            <p><i class="far fa-sad-cry fa-2x"></i></p>
            <p>No se han cargado propuestas</p>
          </div>
      </section>
    </template>
  </b-table>
</section>
</template>

<script>
import { debounce } from "lodash";

export default {
  props: ["projects","url","editable",],
  data() {
    return {
        listing: this.projects,
        selectedType: null,
        availableTypes: [],
        selectedDistrict: null,
        availableDistricts: [],
        filteredProjects: []
        // search: ''
    };
  },
  mounted: function(){
    this.filteredProjects = this.projects;
    // get available types from projects
    this.availableTypes = [...new Set(this.projects.map(item => item.type))];
    // get available districts from projects
    this.availableDistricts = [...new Set(this.projects.map(item => item.district.name))];
  },
  methods: {
    // filterRows: debounce(function(){
    //   this.isLoading = true;
    //       this.$http
    //         .get(this.urlGet)
    //         .then(response => {
    //           this.showResults = true;
    //           this.isLoading = false;
    //           this.listing = response.data;
    //         })
    //         .catch(error => {
    //           this.isLoading = false;
    //           console.error(error);
    //           this.$buefy.snackbar.open({
    //             message: "Ocurrio un error inesperado. Recargue la página",
    //             type: "is-danger",
    //             actionText: "Ok"
    //           });
    //         });
    // }, 500),
    // // getSearchedRow: function() {
    //   return this.model.data.filter(row => {
    //     let value = row[this.query.search_column];
    //     for (var key in row) {
    //       if (String(row[key]).indexOf(this.query.search_input) !== -1) {
    //         // Return true required to populate table
    //         if (this.query.search_column.length < 1) {
    //           return true;
    //         }

    //         // when condition gets here, The table shows 0 records
    //         if (this.query.search_operator == "less_than") {
    //           return value < this.query.search_input;
    //         }
    //       }
    //     }
    //   });
    // },
    capitalizeFirstLetter: function(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    filterRows: function(){
      let resultList = this.projects;
      // first, filter by type if selectedType is not null
      if(this.selectedType !== null){
        resultList = resultList.filter(item => item.type === this.selectedType);
      }
      // second, filter by district if selectedDistrict is not null
      if(this.selectedDistrict !== null){
        resultList = resultList.filter(item => item.district.name === this.selectedDistrict);
      }
      this.filteredProjects = resultList;

    }
  },
  computed: {
    // urlGet: function() {
    //   let query = [];
    //   if (this.search) {
    //     query.push("s=" + this.search);
    //     query.push("size=100");
    //   }
    //   // query.push("size=" + 7);
    //   return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    // },
  },
  watch: {
    selectedType: function (newVal, oldVal) {
      this.filterRows();
    },
    selectedDistrict: function (newVal, oldVal) {
      this.filterRows();
    },
    // search: function(newVal, oldVal){
    //   if(this.search.length > 0){
    //     this.filterRows();
    //   } else {
    //     this.listing = this.projects
    //   }
    // }
  }
};

</script>

<style>
</style>

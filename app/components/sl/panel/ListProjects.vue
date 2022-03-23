<template>
<section>
  <b-table :data="projects">
      <b-table-column v-slot="props" field="name" label="Propuesta">
        <p class="is-size-6 is-300"><b><a :href="`/proyectos/${props.row.id}`" class="has-text-link">{{props.row.name}}</a></b></p>
        <p class="is-size-7" v-if="props.row.type == 'comunitario'">Presentado por&nbsp;<b>{{props.row.author_names}} {{props.row.author_surnames}}</b></p>
        <div v-else>
        <p class="is-size-7">Institución: <b>{{props.row.organization_name}}</b></p>
        <p class="is-size-7">Presentado por&nbsp;<b>{{props.row.author_names}} {{props.row.author_surnames}}</b></p>
        </div>
      </b-table-column>
      <b-table-column v-slot="props" field="type" label="Tipo" width="100" sortable centered>
       <span class="tag" :class="{'is-comunitario': props.row.type == 'comunitario', 'is-institucional': props.row.type == 'institucional'}">{{capitalizeFirstLetter(props.row.type)}}</span>
      </b-table-column>
      <b-table-column v-slot="props" field="type" label="Distrito" width="100" sortable centered>
       <span class="tag is-light">{{props.row.district.name}}</span>
      </b-table-column>
      <b-table-column v-slot="props" label="Info" width="170">
        <p class="is-size-7">Presupuesto&nbsp;<i class="fas fa-dollar-sign"></i>&nbsp;<b>{{props.row.total_budget}}</b></p>             
      </b-table-column>
      <b-table-column v-slot="props" label="Acciones" width="150">
        <p class="is-size-7" v-if="editAvailable"><a :href="`/panel/proyectos/${props.row.id}/editar`" class="has-text-link"><i class="fas fa-edit fa-lg fa-fw"></i>&nbsp;Editar propuesta</a></p>
        <p class="is-size-7" v-if="editAvailable"><a :href="`/panel/proyectos/${props.row.id}/archivos`" class="has-text-link"><i class="fas fa-upload fa-lg fa-fw"></i>&nbsp;Subir archivos</a></p>
        <p class="is-size-7" v-if="editAvailable"><a :href="`/panel/proyectos/${props.row.id}/imagen`" class="has-text-link"><i class="fas fa-image fa-lg fa-fw"></i>&nbsp;Asignar una imagen</a></p>
        <p class="is-size-7"><a :href="`/panel/proyectos/${props.row.id}/bitacora`" class="has-text-link"><i class="fas fa-history fa-lg fa-fw"></i>&nbsp;Bicatora</a></p>
      </b-table-column>
    <template #empty>
      <section class="section">
        <div class="content has-text-grey has-text-centered">
            <p><i class="far fa-sad-cry fa-2x"></i></p>
            <p>No cargaste ninguna propuesta</p>
          </div>
      </section>
    </template>
  </b-table>
</section>
</template>

<script>
import { debounce } from "lodash";

export default {
  props: ["projects","url","editAvailable"],
  data() {
    return {};
  },
  methods: {
    capitalizeFirstLetter: function(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }
  },
  computed: {
  },
  watch: {
  }
};

</script>

<style>
</style>

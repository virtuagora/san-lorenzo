<template>
  <section>
    <div class="card">
      <div class="card-content">

    <figure class="is-pulled-left" style="margin-right: 10px; margin-bottom: 10px;">
      <avatar :username="citizen.data.name" :size="40" />
    </figure>
    <div class="columns">
      <div class="column">
        <div class="tag is-link" v-if="citizen.subject">
          <i class="fas fa-user fa-fw"></i>&nbsp;¡Registrado en la plataforma!
        </div>
        <h1 class="title is-4 is-marginless">{{citizen.data.name}}</h1>
        <h1 class="subtitle is-6 is-marginless">{{citizen.data.address}} - Año {{citizen.year}}</h1>
      </div>
      <div class="column">
        <h1 class="subtitle is-6 is-marginless">
          {{citizen.data.doc_type}} -
          <b>{{citizen.dni}}</b>
        </h1>
         <h1
          class="subtitle is-6 is-marginless"
          v-if="citizen.table"
        >Mesa: {{citizen.table}} - Orden: {{citizen.orden}}</h1>
        <h1 class="subtitle is-6 has-text-link is-marginless" v-else>(Agregado por sistema)</h1>
      </div>
    </div>
      </div>
    </div>
  </section>
</template>

<script>
import Avatar from "../../utils/Avatar";
export default {
  props: ["citizen"],
  components: {
    Avatar
  },
};
</script>

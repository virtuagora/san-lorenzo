<template>
  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-6">
          <h1 class="title is-3"><i class="fas fa-flag-checkered fa-fw"></i>&nbsp;Los objetivos</h1>
          <br>
          <div class="content">
            <table class="table is-bordered">
              <tbody v-if="project.goals.length">
                <tr v-for="(objetivo, index) in project.goals" :key="index">
                  <td>
                    <i class="fas fa-flag-checkered fa-fw"></i> {{objetivo}}</td>
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
          <h1 class="title is-3"><i class="far fa-calendar-check fa-fw"></i>&nbsp;Calendario de actividades</h1>
          <br>
          <div class="content">
            <table class="table is-bordered">
              <thead>
                <tr>
                  <th width="120px">Fecha</th>
                  <th>Actividad</th>
                </tr>
              </thead>
              <tbody v-if="project.schedule.length">
                <tr v-for="(actividad, index) in project.schedule" :key="index">
                  <td>
                    <i class="far fa-calendar-check fa-fw"></i> {{actividad.date}}</td>
                  <td>{{actividad.activity}}</td>
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
        <div class="column">
        <h1 class="title is-3"><i class="fas fa-list-alt fa-fw"></i>&nbsp;Presupuesto</h1>
          <br>
          <div class="content">
            <h5>
              <b>Presupuesto solicitado</b>
            </h5>
            <table class="table is-bordered">
              <thead>
                <tr>
                  <th width="120px">Rubro</th>
                  <th>Descripción</th>
                  <th width="120px" class="has-text-centered">Monto</th>
                </tr>
              </thead>
              <tbody v-if="project.budget.length">
                <tr v-for="(item, index) in project.budget" :key="index">
                  <td>{{item.category}}</td>
                  <td>{{item.description}}</td>
                  <td class="has-text-centered">$ {{item.amount}}</td>
                </tr>
                <tr>
                  <th colspan="2" class="has-text-right">Monto total:</th>
                  <td class="has-text-centered">$ {{montoTotal}}</td>
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
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  props: ["project"],
  computed:{
    montoTotal: function() {
      const reducer = (accumulator, item) =>
        accumulator + parseFloat(item.amount);
      return this.project.budget.reduce(reducer, 0);
    },
  }
};
</script>

<style>

</style>

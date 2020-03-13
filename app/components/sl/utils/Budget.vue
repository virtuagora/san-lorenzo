<template>
  <b-table :data="project.budget" hoverable>
    <template slot-scope="props">
      <b-table-column label="Descripción">{{ props.row.description }}</b-table-column>
      <b-table-column label="Monto" width="100" centered>$ {{ props.row.amount }}</b-table-column>
    </template>
    <template slot="empty">
      <section class="section">
        <div class="content has-text-grey has-text-centered">
          <p>
            <i class="far fa-sad-cry fa-2x"></i>
          </p>
          <p>No se han ingresado items en el presupuesto</p>
        </div>
      </section>
    </template>
  </b-table>
</template>

<script>
export default {
  props: ["project"]
};
</script>

<style>
</style>

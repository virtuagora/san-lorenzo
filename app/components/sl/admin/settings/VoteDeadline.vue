<template>
  <section>
    <div class="columns">
      <div class="column">
        <div class="field">
          <label class="label is-size-5">
            <i class="fas fa-angle-double-right"></i>&nbsp;Día de cierre</label>
          <b-datepicker v-model="theDate" placeholder="Click para elegir..." icon="calendar" size="is-medium" :mobile-native="false" :date-formatter="(date) => date.toLocaleDateString('es-AR')">
          </b-datepicker>
        </div>
      </div>
      <div class="column">
        <div class="field">
          <label class="label is-size-5">
            <i class="fas fa-angle-double-right"></i>&nbsp;Hora de cierre</label>
          <b-timepicker v-model="theTime" size="is-medium" placeholder="Click para elegir..." icon="clock"></b-timepicker>
        </div>
      </div>
    </div>
    <div class="is-clearfix">
      <button @click="submit()" class="button is-link is-pulled-right"><i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["deadline"],
  data() {
    return {
      isLoading: false,
      theTime: null,
      theDate: null
    };
  },
  created: function() {
    (this.theTime = new Date(this.deadline.value.date)),
      (this.theDate = new Date(this.deadline.value.date));
  },
  methods: {
    submit: function() {
      this.isLoading = true;
      this.$http
        .post("/api/admin/option/vote-deadline", this.payload)
        .then(response => {
          window.location.href = "/logout";
        })
        .catch(x => {
          this.isLoading = false;
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  },
  computed: {
    payload: function() {
      return {
        value:
          this.theDate.toISOString().split("T")[0] +
          " " +
          this.theTime.toTimeString().split(" ")[0]
      };
    }
  }
};
</script>
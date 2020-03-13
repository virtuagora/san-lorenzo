<template>
  <section>
    <div class="box">
      <div class="columns">
        <div class="column">
          <div class="field">
            <label class="label is-size-5">
              <i class="fas fa-angle-double-right"></i>&nbsp;Día</label>
            <b-datepicker v-model="theDate" placeholder="Click para elegir..." icon="calendar" size="is-medium"  :mobile-native="false" :date-formatter="(date) => date.toLocaleDateString('es-AR')">
            </b-datepicker>
          </div>
        </div>
        <div class="column">
          <div class="field">
            <label class="label is-size-5">
              <i class="fas fa-angle-double-right"></i>&nbsp;Hora "desde"</label>
            <b-timepicker v-model="theTimeDesde" size="is-medium" placeholder="Click para elegir..." icon="clock"></b-timepicker>
          </div>
        </div>
        <div class="column">
          <div class="field">
            <label class="label is-size-5">
              <i class="fas fa-angle-double-right"></i>&nbsp;Hora "desde"</label>
            <b-timepicker v-model="theTimeHasta" size="is-medium" placeholder="Click para elegir..." icon="clock"></b-timepicker>
          </div>
        </div>
      </div>
      <div class="columns">
        <div class="column">
          <div class="field">
            <label class="label is-size-5">
              <i class="fas fa-angle-double-right"></i>&nbsp;Distrito</label>
            <div class="control">
              <div class="select is-medium is-fullwidth">
                <select name="district" v-model="district">
                  <option :value="null" disabled>Seleccione un distrito</option>
                  <option value="Norte">Norte</option>
                  <option value="Centro">Centro</option>
                  <option value="Sur">Sur</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="column">
          <label class="label is-size-5">
            <i class="fas fa-angle-double-right"></i>&nbsp;Dirección</label>
            <div class="control">
              <input type="text" v-model="address" placeholder="Ej: Av. Lorenzo 2827" class="input is-medium">
            </div>
        </div>
      </div>
      <div class="field is-clearfix">
         <button @click="add()" :disabled="district == null || address.length == 0 || theDate == null || theTimeDesde == null || theTimeHasta == null" class="button is-link is-outlined is-pulled-right">
            <i class="fas fa-plus"></i>&nbsp;Agregar al calendario
          </button>
      </div>
    </div>
    <table class="table is-fullwidth">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>
            <i class="fas fa-clock fa-fw"></i>&nbsp;Desde</th>
          <th>
            <i class="fas fa-clock fa-fw"></i>&nbsp;Hasta</th>
          <th>Distrito</th>
          <th>Dirección</th>
          <th>
            <i class="fas fa-times"></i>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(date, index) in dates" :key="index">
          <td>
            {{capitalizeString((new Date(date.from)).toLocaleDateString('es-AR',dateOptions))}}
          </td>
          <td>
            {{getTime(date.from)}}
          </td>
          <td>
            {{getTime(date.to)}}
          </td>
          <td>
            {{date.district}}
          </td>
          <td>
            {{date.address}}
          </td>
          <td>
            <a @click="remove(index)">
              <i class="fas fa-times"></i>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="is-clearfix">
      <button @click="submit()" class="button is-link is-pulled-right">
        <i class="fas fa-save fa-fw"></i>&nbsp;Guardar</button>
    </div>
    <b-loading :is-full-page="true" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["value"],
  data() {
    return {
      dates: [],
      isLoading: false,
      theTimeDesde: null,
      theTimeHasta: null,
      district: null,
      address: "",
      theDate: null
    };
  },
  created: function() {
    this.dates = this.sortDates(this.value);
  },
  methods: {
    getTime(time) {
      return time.match(/\d{1,2}:\d{2}/)[0];
    },
    add: function() {
      this.dates.push(this.createdDate);
      this.theDate = null;
      this.theTimeDesde = null;
      this.theTimeHasta = null;
      this.district = null;
      this.address = "";
      this.dates = this.sortDates(this.dates);
    },
    remove: function(index) {
      this.dates.splice(index, 1);
    },
    sortDates: function(arr) {
      return arr.sort(function(a, b) {
        a = new Date(a.from);
        b = new Date(b.from);
        return a < b ? -1 : a > b ? 1 : 0;
      });
    },
    submit: function() {
      this.isLoading = true;
      this.$http
        .post("/api/admin/option/calendar", this.payload)
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
    dateOptions: function() {
      return {
        weekday: "long",
        year: "numeric",
        month: "numeric",
        day: "numeric",
        time: "short"
      };
    },
    createdDate: function() {
      return {
        district: this.district,
        address: this.address,
        from:
          this.theDate.toISOString().split("T")[0] +
          " " +
          this.theTimeDesde.toTimeString().split(" ")[0],
        to:
          this.theDate.toISOString().split("T")[0] +
          " " +
          this.theTimeHasta.toTimeString().split(" ")[0]
      };
    },
    payload: function() {
      return {
        value: JSON.stringify(this.dates)
      }
    }
  }
};
</script>

<style>
</style>

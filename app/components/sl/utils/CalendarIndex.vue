<template>
  <section v-if="nextDates.length > 0">
    <div class="box">
    <article class="media" v-for="(date, index) in nextDates" :key="index">
      <figure class="media-left">
        <i class="fas fa-map-marked-alt fa-2x has-text-dark"></i>
      </figure>
      <div class="media-content" style="overflow: visible;">
        <span class="tag is-primary is-medium is-pulled-right"><i class="far fa-clock fa-fw"></i>&nbsp;{{getTime(date)}}</span>
        <h1 class="title is-4 is-marginless has-text-primary" :class="{'is-5': small}">Distrito {{date.district.toUpperCase()}}</h1>
        <h1 class="title is-5 has-text-dark" :class="{'is-6': small}">{{capitalizeString((new Date(date.from)).toLocaleDateString('es-AR',dateOptions))}}</h1>
        <h2 class="subtitle is-6 is-400 has-text-black" :class="{'is-size-7': small}">Dirección: {{date.address}}</h2>
      </div>
    </article>
    </div>
    <div class="buttons is-centered" v-if="showMore">
    <a href="/agenda" class="button is-primary is-rounded"><i class="fas fa-external-link-alt fa-fw"></i>&nbsp;Visitá la agenda completa</a>
    </div>
  </section>
  <b-message type="is-primary" v-else>
    <h1 class="title is-5 has-text-centered has-text-primary">No hay fechas próximas</h1>
  </b-message>
</template>

<script>
export default {
  props: ["calendar", "showMore", "small"],
  data(){
    return {
      today: new Date()
    }
  },
  methods:{
    getTime(date){
      let desde = date.from.match(/\d{1,2}:\d{2}/)
      let hasta = date.to.match(/\d{1,2}:\d{2}/)
      return desde + ' - ' + hasta
    }
  },
  computed: {
    dateOptions: function () {
      return {
       weekday: 'long', year: 'numeric', month: 'numeric', day: 'numeric'
      }
    },
    nextDates: function(){
      let theDates = this.calendar.filter((date, index) => {
          return ((new Date(date.to)) > this.today)
      })
      return theDates.filter((d,i) => {
        return i < 2
      })
    }
  }
};
</script>

<style>
</style>

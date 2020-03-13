<template>
  <section class="notification is-light is-clearfix" v-if="(new Date(date)) > (new Date())">
    <p class="is-size-4 is-500 is-pulled-left">Quedan
      <i class="fas fa-clock"></i> {{days}} días y {{hours}}:{{minutes}}:{{seconds}}</p>
    <p class="is-size-7 is-300 is-pulled-right has-text-right-desktop has-text-left-touch">Las inscripciones cierran <span class="is-hidden-touch"><br></span>{{Intl.DateTimeFormat('es-AR',options).format(dateDeadline)}}</p>
  </section>
  <section class="notification is-light is-clearfix" v-else>
     <p class="subtitle is-4">
      <i class="fas fa-info-circle"></i> ¡Convocatoria cerrada! ¡Gracias por participar!</p>
  </section>
</template>

<script>
export default {
  props: ['date'],
  data() {
    return {
      now: Math.trunc(new Date().getTime() / 1000),
      dateDeadline: new Date(this.date),
      deadline: Math.trunc((new Date(this.date)).getTime() / 1000),
      options: {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        hour12: false
      }
    };
  },
  mounted: function() {
    setInterval(() => {
      this.now = Math.trunc(new Date().getTime() / 1000);
    }, 1000);
  },
  methods: {
    twoDigits: function(value) {
      if (value.toString().length <= 1) {
        return "0" + value.toString();
      }
      return value.toString();
    }
  },
  computed: {
    seconds() {
      return this.twoDigits((this.deadline - this.now) % 60);
    },
    minutes() {
      return this.twoDigits(Math.trunc((this.deadline - this.now) / 60) % 60);
    },
    hours() {
      return this.twoDigits(
        Math.trunc((this.deadline - this.now) / 60 / 60) % 24
      );
    },
    days() {
      return this.twoDigits(
        Math.trunc((this.deadline - this.now) / 60 / 60 / 24)
      );
    }
  }
};
</script>
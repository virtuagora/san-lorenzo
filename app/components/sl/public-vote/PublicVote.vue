<template>
   <transition     name="custom-classes-transition"
        enter-active-class="animated fadeInRight"
        leave-active-class="animated fadeOutLeft"
        mode="out-in">
              <router-view :googleKey="googleKey" :urlVote="urlVote"></router-view>
              </transition>
</template>

<script>
export default {
  props: ['googleKey','urlVote'],
  beforeMount: function(){
    Promise.all([
    this.$http.get('/api/proyectos?size=100&type=institucional&feasible=true'),
    this.$http.get('/api/proyectos?size=100&type=comunitario&feasible=true')
    ])
    .then(results => {
      this.$store.commit('bind',{
        institucionales: this.shuffleArray(results[0].data.data)
      })
      this.$store.commit('bind',{
        comunitarios: this.shuffleArray(results[1].data.data)
      })
    })
    if(this.$route.query.code){
        this.$store.commit('bind',{
          publicVoteCode: this.$route.query.code
        })
        this.$store.commit('bind',{
          codeOnQuery: true
        })
        this.$router.push({name: 'PublicHello'})
      }
  },
  methods: {
     shuffleArray: function(arr) {
      return arr
        .map(a => [Math.random(), a])
        .sort((a, b) => a[0] - b[0])
        .map(a => a[1]);
    },
  }
}
</script>

<style>

</style>

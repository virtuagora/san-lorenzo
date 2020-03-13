<template>
  <article class="media">
    <figure class="media-left image is-128x128 is-rounded">

      <img src="https://pbs.twimg.com/profile_images/908495954524983297/nSinJTho_400x400.jpg" alt="">
    </figure>
    <div class="media-content" v-if="user != {}">
      <h1 class="title is-1">{{user.name}}</h1>
      <h1 class="subtitle is-4"><i class="em em-trophy"></i> 250 puntos</h1>
      <div class="field">
        <label class="label">Bio</label>
        <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ad ut optio incidunt accusamus vero earum nostrum exercitationem blanditiis dolor ea cumque, eveniet nisi suscipit alias cupiditate illo recusandae nulla hic.</p>
      </div>
      <div class="field">
        <label class="label">Occupation</label>
        <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ad ut optio incidunt accusamus vero earum nostrum exercitationem blanditiis dolor ea cumque, eveniet nisi suscipit alias cupiditate illo recusandae nulla hic.</p>
      </div>
      <div class="field">
        <label class="label">City</label>
        <p>{{user.address.city}}</p>
      </div>
      <div class="field">
        <label class="label">Social media</label>
        <a href="#" class="button is-light"><span class="icon"><i class="fab fa-facebook-f fa-lg"></i></span></a>
        <a href="#" class="button is-light"><span class="icon"><i class="fab fa-twitter fa-lg"></i></span></a>
        <a href="#" class="button is-light"><span class="icon"><i class="fas fa-globe fa-lg"></i></span></a>
      </div>
    </div>
    <div class="media-content" v-else>
      <span class="icon">
        <i class="fas fa-cog fa-spin"></i>
      </span> Loading . . .
    </div>
  </article>
</template>

<script>
export default {
  props: ['id'],
  data() {
    return {
      user: {}
    };
  },
  created: function(){
        this.user = this.$store.state.user

    // console.log(this.$store.state)
    // // console.log(this.$store.getters.get('user'))
    // this.user = (this.$store.getters.get).user;
    // console.log(this.$store.getters.get)
    // Object.assign(this.user,this.$store.getters.getKey('user'))
  },
  // computed:{
  //   user: function(){
  //    return this.$store.getters.get('user')    
  //   }
  // }
  // created: function() {
  //   this.user = this.$store.state.user
  // }
};
</script>

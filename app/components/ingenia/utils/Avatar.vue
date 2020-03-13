<template>
    <figure :class="'image is-rounded is-' + size + 'x' + size" >
  <a :href="toPanel === true ? '/panel' : userId ? '/usuario/' + userId : void(0)">
        <img :src="url" :alt="userName">
  </a>
    </figure>
</template>

<script>
export default {
  props: ["subject", "user", "size", "toPanel"],
  data(){
    return {
      userId: null,
      userName: null,
      img_type: null,
      img_hash: null
    }
  },
  created: function(){
    if(this.user !== undefined){
        this.userId = this.user.id
        this.userName = this.user.subject.display_name
        this.img_type = this.user.subject.img_type
        this.img_hash = this.user.subject.img_hash
      } else {
        this.userId = null
        this.userName = this.subject.display_name
        this.img_type = this.subject.img_type
        this.img_hash = this.subject.img_hash
      }
  },
  computed: {
    url: function() {
      switch (this.img_type) {
        case 0:
          return (
            "https://www.gravatar.com/avatar/" +
            this.img_hash +
            "?d=mm&s=" +
            this.size
          );
        case 1:
          return (
            "https://graph.facebook.com/" +
            this.img_hash +
            "/picture?width=" +
            this.size
          );
        default:
          return (
            "https://www.gravatar.com/avatar/" +
            this.img_hash +
            "?d=mm&s=" +
            this.size
          );
      } 
    },
  }
};
</script>

<style lang="scss" scoped>
.image img{
  height: 100%;
}
</style>

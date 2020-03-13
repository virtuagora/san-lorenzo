<template>
  <vue-avatar :username="username" :size="size" :customStyle="getStyle"></vue-avatar>
</template>

<script>
import VueAvatar from "vue-avatar";
export default {
  props: ["username", "size", "admin"],
  data: () => {
    return {
      fam: 'BlinkMacSystemFont, -apple-system, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif'
    }
  },
  components: {
    VueAvatar
  },
  computed: {
    getStyle: function() {
      let base = {
        borderRadius: 0,
        borderTopRightRadius: "10px",
        borderBottomLeftRadius: "10px",
        alignItems: "center",
        font: Math.floor(this.size / 3) + 'px/100px ' + this.fam
      };
      if (this.admin == true) {
        base.border = '1px'
        base.borderColor = 'black'
      }
      return base;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>


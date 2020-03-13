<template>
    <div>
        <h1 class="title is-4">Comments
            <span v-show="loaded">({{comments.length}})</span>
        </h1>
        <div v-if="loaded">
            <div v-for="(comment, index) in comments" :key="comment.id" class="media">
                <div class="media-left">
                    <figure class="image is-32x32 is-rounded">
                        <img alt="Placeholder image" src="assets/img/avatar.jpg" /></figure>
                </div>
                <div class="media-content">
                    <div class="content">
                        <b>
                            <a href="#">Guillermo Croppi</a>
                        </b>&nbsp;{{comment.body}}
                    </div>
                    <!-- <div class="media">
                        <div class="media-content">
                            <div class="content">
                                <img class="image is-16x16 is-inline is-rounded" src="assets/img/avatar.jpg" />
                                <b>
                                    <a href="#">Guillermo Croppi</a>
                                </b>&nbsp;{{comment.body}}
                            </div>
                        </div>
                    </div>
                    <div class="media">
                        <div class="media-content">
                            <div class="content">
                                <img class="image is-16x16 is-inline is-rounded" src="assets/img/avatar.jpg" />
                                <b>
                                    <a href="#">Guillermo Croppi</a>
                                </b>&nbsp;{{comment.body}}
                            </div>
                        </div>
                    </div>
                    -->
                </div>
            </div>
        </div>
        <div class="notification" v-else>
            Fetching comments...
        </div>
    </div>
</template>

<script>
export default {
  name: "node-comments",
  props: ["nodeId"],
  data() {
    return { comments: [], loaded: false };
  },
  beforeCreate() {
    this.$http
      .get(`http://jsonplaceholder.typicode.com/comments`)
      .then(response => {
        // JSON responses are automatically parsed.
        this.loaded = true;
        this.comments = response.data;
      })
      .catch(e => {
        this.errors.push(e);
      });
  },

  methods: {}
};
</script>

<style lang="scss" scoped>
.content {
  line-height: normal;
  font-size: 0.9rem;
  img{
      vertical-align: middle;
  }
}
</style>
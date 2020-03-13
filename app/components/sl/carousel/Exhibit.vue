<template>
  <div v-masonry transition-duration="1s" item-selector=".item-mosaic">
    <div v-masonry-tile class="item-mosaic" v-for="project in projects" :key="project.id">
        <div
          class="notification is-primary"
          :class="getColor(project.type, project.picture_name)"
          :style="getImageStyle(project.type, project.picture_name, project.id, project.selected)"
        >
          <img
            :src="'/assets/img/icons/medal-' + project.type + '.svg'"
            class="ribbon"
            alt
            v-if="project.selected && forceWinners"
          >
          <div class="bancar-count-container">
            <span class="is-size-7 shadow-text">{{capitalizeString(project.type)}}&nbsp;</span>
            <img
              :src="'/assets/img/icons/w-' + project.type + '.svg'"
              style="height:40px; vertical-align:top"
              alt
            >
          </div>
          <a :href="'/proyectos/'+project.id" target="_blank" style="text-decoration:none;">
            <div class="field is-grouped is-grouped-multiline">
              <div class="control">
                <div class="tags has-addons">
                  <span class="tag is-dark">
                    <i class="fas fa-map-marker-alt"></i>
                  </span>
                  <span class="tag is-white">{{getDistrict(project.district_id)}}</span>
                </div>
              </div>
              <div class="control" v-if="project.selected && forceWinners">
                <div class="tags has-addons">
                  <span class="tag is-dark">
                    <i class="fas fa-star has-text-warning"></i>
                  </span>
                  <span class="tag is-warning">¡Seleccionado!</span>
                </div>
              </div>
            </div>
            <h1 class="title is-4 is-500 is-marginless">{{project.name}}</h1>
            <p class="shadow-text some-other-effects">
              Por
              <span class="is-600">{{getWho(project)}}</span>
              - {{getShortDescription(project.objective,150)}}
            </p>
          </a>
        </div>
      </div>
    <div v-masonry-tile class="item-mosaic" v-if="showMore">
      <div class="notification is-primary and-more">
            <a href="/catalogo" style="text-decoration: none;">
              <img src="/assets/img/icons/w-agreement.svg" style="height:70px; vertical-align:top" alt="">
              <h1 class="title is-3">¡Conocelos a todos!</h1>
              <h1 class="subtitle is-5">¡Hay un monton de propuestas y proyectos para descubrir en el cátalogo! ¡Conocelos!</h1>
          </a>
      </div>
    </div>
    <!-- <div v-masonry-tile class="item-mosaic" v-if="!showMore">
      <div class="notification is-primary and-more">
            <a href="/catalogo" style="text-decoration: none;">
              <h1 class="title is-1 is-marginless">
                <i class="fas fa-book-reader"></i>
              </h1>
              <h1 class="title is-2">¡Y mucho más!</h1>
              <h1 class="subtitle is-5">¡Ingresá al catálogo para ver mas proyectos que participan!</h1>
          </a>
      </div>
    </div> -->
  </div>
</template>

<script>
export default {
  props: {
    url: {
      type: String,
      required: true
    },
    random: {
      type: Boolean
    },
    forceWinners: {
      type: Boolean
    },
    forceFeasible: {
      type: Boolean
    },
    forceUnfeasible: {
      type: Boolean
    },
    forceProposals: {
      type: Boolean
    },
    forceDistrict: {
      type: String,
      validator: function(value) {
        // The value must match one of these strings
        return ["comunitario", "institucional"].indexOf(value) !== -1;
      }
    },
    forceType: {
      type: Number
    },
    showAll: {
      type: Boolean
    },
    forceSize: {
      type: Number
    },
    showMore: {
      type: Boolean
    },
  },
  data() {
    return {
      projects: [],
      isLoading: true
    };
  },
  beforeMount: function() {
    this.isLoading = true;
    this.$http
      .get(this.urlGet)
      .then(response => {
        this.isLoading = false;
        this.projects = response.data.data;
        this.$redrawVueMasonry();
      })
      .catch(error => {
        this.isLoading = false;
        console.error(error);
      });
  },
  mounted: function() {},
  methods: {
    getWho(project){
      if(project.type == 'institucional'){
        return project.organization_name
      }
        return project.author_names + ' ' + project.author_surnames
    },
    getDistrict: function(district) {
      switch (district) {
        case 1:
          return "Norte";
        case 2:
          return "Centro";
        case 3:
          return "Sur";
        default:
          return "?";
      }
    },
    getColor: function(type, image) {
      if (!image) {
        return type == "comunitario"
          ? "comunitario-background"
          : "institucion-background";
      }
      return "has-image-background with-image " + type;
    },
    getImageStyle: function(type, image, slug) {
      if (!image) return "";
      else {
        if(type == 'comunitario') return 'background: linear-gradient(15deg, rgba(39, 150, 45, 0.9) 0%, rgba(151, 214, 74, 0.4) 39%, rgba(0, 98, 22, 0.2) 55%, rgba(0, 96, 25, 0) 75%), url("/proyectos/' + slug + '/imagen") center center / cover'
        return 'background: linear-gradient(15deg, rgba(28, 4, 83, 0.9) 0%, rgba(74, 156, 214, 0.4) 39%, rgba(49, 0, 98, 0.2) 55%, rgba(48, 0, 96, 0.0) 75%), url("/proyectos/' + slug + '/imagen") center center / cover'
      }
    },
    getShortDescription: function(text, limit) {
      if (text.length > limit) {
        for (let i = limit; i > 0; i--) {
          if (
            text.charAt(i) === " " &&
            (text.charAt(i - 1) != "," ||
              text.charAt(i - 1) != "." ||
              text.charAt(i - 1) != ";")
          ) {
            return text.substring(0, i) + "...";
          }
        }
      } else {
        return text;
      }
    },
  },
  computed: {
    urlGet: function() {
      let query = [];
      if (this.forceWinners == true) {
        query.push(`selected=1`);
      }
      if (this.forceFeasible == true) {
        query.push(`feasible=1`);
      }
      if (this.forceUnfeasible == true) {
        query.push(`feasible=0`);
      }
      if (this.forceProposals == true) {
        query.push(`proposals=1`);
      }
      if (this.forceDistrict) {
        query.push("district=" + this.forceDistrict);
      }
      if (this.forceType) {
        query.push("type=" + this.forceType);
      }
      if (this.showAll) {
        query.push("size=200");
      } else {
        if (this.forceSize) {
        query.push("size=" + this.forceSize);
        } else {
        query.push("size=11");
        }
      }
      if (this.random) {
        query.push("random=1");
      }
      return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    }
  }
};
</script>


<style lang="scss" scoped>
.some-other-effects{
  line-height: normal;
margin-top: 5px;
font-size: 0.85rem;
}
.ribbon{
  position: absolute;
  top: 10px;
  left: 10px;
  height: 70px;
}
</style>

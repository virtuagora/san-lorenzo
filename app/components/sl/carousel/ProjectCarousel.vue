<template>
  <div v-if="!isLoading" style="padding:10px 30px">
    <carousel class="project-carousel" v-if="projects.length > 0"
      :autoplay="autoplayEnabled"
      :loop="loopEnabled" 
      :autoplay-timeout="timeout" 
      :per-page-custom="responsiveLayout"
      :navigation-enabled="navigationEnabled" 
      navigationNextLabel="<i class='fas fa-angle-double-right fa-2x has-text-black'></i>"
      navigationPrevLabel="<i class='fas fa-angle-double-left fa-2x has-text-black'></i>">
      <slide class="item-carousel" v-for="project in projects" :key="project.id">
        <a :href="'/proyectos/' + project.id">
          <div class="box-project"
          :class="getColor(project.type, project.picture_name)"
          :style="getImageStyle(project.type, project.picture_name, project.id, project.selected)">
          <div class="bancar-count-container">
            <span class="is-size-7 shadow-text">{{capitalizeString(project.type)}}&nbsp;</span>
            <img
              :src="'/assets/img/icons/w-' + project.type + '.svg'"
              style="height:40px; vertical-align:top"
              alt
            >
          </div>
            <div class="add-bg">
              <div class="to-bottom">
                <div class="field is-grouped is-grouped-multiline">
                  <div class="control">
                    <div class="tags has-addons">
                    <span class="tag is-dark">
                      <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <span class="tag is-white">{{getDistrict(project.district_id)}}</span>
                  </div>
                  </div>
                </div>
                <h1 class="title is-4 is-500 is-marginless">{{project.name}}</h1>
            <p class="shadow-text some-other-effects">
              Por
              <span class="is-600">{{getWho(project)}}</span>
              - {{getShortDescription(project.objective,60)}}
            </p>
              </div>
            </div>
          </div>
        </a>
      </slide>
      <slide class="item-carousel" v-if="more">
        <a href="/catalogo">
        <div class="box-project and-more">
            <div>
              <div class="to-bottom">
                <h1 class="title is-1 is-marginless"><i class="fas fa-book-reader"></i></h1>
                <h1 class="title is-3">¡Y mucho más!</h1>
                <h1 class="subtitle is-5">¡Ingresá al catálogo para ver mas ideas que participan!</h1>
              </div>
            </div>
          </div>
        </a>
      </slide>
    </carousel>
    <div v-else class="notification">
      No se encontraron resultados
    </div>

  </div>
  <div class="notification" v-else>
    <br>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
    <br>
  </div>
</template>

<script>
import { Carousel, Slide } from "vue-carousel";


export default {
  props: ["url", "timeout", "district", "author", "selected", "random", "feasible", "size", "more"],
  components: {
    Carousel,
    Slide
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
      })
      .catch(error => {
        this.isLoading = false;
        console.error(error);
      });
  },
  methods: {
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
    getWho(project) {
      if (project.type == "institucion") {
        return project.organization_name;
      }
      return project.author_names + " " + project.author_surnames;
    },
    getColor: function(type, image) {
      if (!image) {
        return type == "comunitario"
          ? "comunitario-background"
          : "institucion-background";
      }
      return "has-image-background " + type;
    },
    getImageStyle: function(type, image, id, selected) {
      let selectedStyle = "";
      if (selected) {
        selectedStyle = "border: 7px solid #f8b64c;";
      }
      if (!image) return "";
      else {
        if (type == "comunitario")
          return (
            selectedStyle +
            'background: linear-gradient(15deg, rgba(39, 150, 45, 0.9) 0%, rgba(151, 214, 74, 0.4) 39%, rgba(0, 98, 22, 0.2) 55%, rgba(0, 96, 25, 0) 75%), url("/proyectos/' +
            id +
            '/imagen") center center / cover'
          );
        return (
          selectedStyle +
          'background: linear-gradient(15deg, rgba(28, 4, 83, 0.9) 0%, rgba(74, 156, 214, 0.4) 39%, rgba(49, 0, 98, 0.2) 55%, rgba(48, 0, 96, 0.0) 75%), url("/proyectos/' +
          id +
          '/imagen") center center / cover'
        );
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
    shuffleArray: function(arr) {
      return arr
        .map(a => [Math.random(), a])
        .sort((a, b) => a[0] - b[0])
        .map(a => a[1]);
    }
  },
  computed: {
    urlGet: function() {
      let query = [];
      if (this.district) {
        query.push("district=" + this.district);
      }
      if (this.author) {
        query.push("author=" + this.author);
      }
      if (this.random) {
        query.push("random=true");
      }
      if (this.selected) {
        query.push("selected=true");
      }
      if (this.feasible) {
        query.push("feasible=true");
      }
      return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    },
    navigationEnabled: function() {
      return this.projects.length - 1 ? true : false;
    },
    autoplayEnabled: function() {
      return this.projects.length - 1 ? true : false;
    },
    perPage: function() {
      if (this.projects.length > 2) {
        return 3;
      } else {
        return this.projects.length;
      }
    },
    loopEnabled: function() {
      return this.projects.length - 1 ? true : false;
    },
    responsiveLayout: function() {
      console.log(this.projects.length)
      if (this.projects.length > 2) {
        return [[0,1],[768, 2], [1024, 3]];
      } else if (this.projects.length == 2) {
        return [[0, 1], [768, 2]];
      } else if (this.projects.length == 1) {
        return null;
      }
    }
  }
};
</script>




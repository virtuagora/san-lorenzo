<template>
  <section>
    <div class="columns is-centered" v-if="showFilters">
      <div class="column is-6-desktop is-10-tablet is-12-mobile">
        <div class>
          <div class="columns is-centered">
            <div class="column delete-padding-touch">
              <div class="field has-addons">
                <p class="control is-expanded has-icons-left">
                  <input
                    v-model="nameToSearch"
                    class="input name-search"
                    type="text"
                    placeholder="Nombre del proyecto"
                  >
                  <span class="icon is-left">
                    <i class="fas fa-chevron-right"></i>
                  </span>
                </p>
              </div>
              <b-field expanded>
                <b-select
                  v-model="statusSelected"
                  placeholder="Estado del proyecto"
                  expanded
                  :disabled="forceWinners || forceFeasible || forceUnfeasible || forceProposals "
                >
                  <option :value="null">Cualquier estado</option>
                  <option value="selected" v-if="!hideSelectedFilter">🏅 Seleccionados</option>
                  <option value="feasible" v-if="!hideFeasibleFilter">✔️ Factibles</option>
                  <option value="unfeasible" v-if="!hideUnfeasibleFilter">❌ No factibles</option>
                  <option value="proposals" v-if="!hideProposalFilter">📝 Propuestas</option>
                </b-select>
              </b-field>
            </div>
            <div class="column">
              <b-field expanded>
                <b-select
                  v-model="typeSelected"
                  placeholder="Tipo de proyecto"
                  :disabled="forceType"
                  expanded
                >
                  <option :value="null">Todos los tipos</option>
                  <option value="comunitario">Comunitarios</option>
                  <option value="institucional">Institucionales</option>
                </b-select>
              </b-field>
              <b-field expanded>
                <b-select
                  v-model="districtSelected"
                  placeholder="Distrito"
                  :disabled="forceDistrict"
                  expanded
                >
                  <option :value="null">Todos los distritos</option>
                  <option :value="1">Norte</option>
                  <option :value="2">Centro</option>
                  <option :value="3">Sur</option>
                </b-select>
              </b-field>
            </div>
          </div>
          <div class="buttons is-centered">
            <a @click="search()" class="button is-white">
              <i class="fas fa-search fa-fw"></i>&nbsp;Buscar
            </a>
            <a @click="cleanFilters()" class="button is-white">
              <i class="fas fa-eraser fa-fw"></i>&nbsp;Borrar filtros
            </a>
          </div>
        </div>
      </div>
    </div>

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
              <div class="control" v-if="showFeasibility">
                <div class="tags has-addons">
                  <span class="tag is-dark" v-if="project.feasible == false">
                    <i class="fas fa-times has-text-danger"></i>
                  </span>
                  <span class="tag is-dark" v-else-if="project.selected == true && showWinners">
                    <i class="fas fa-star has-text-warning"></i>
                  </span>
                  <span class="tag is-dark" v-else-if="project.feasible == true">
                    <i class="fas fa-check has-text-success"></i>
                  </span>
                  <span class="tag is-dark" v-else>
                    <i class="fas fa-question-circle"></i>
                  </span>
                  <span class="tag is-danger" v-if="project.feasible == false">No factible</span>
                  <span class="tag is-warning" v-else-if="project.selected == true && showWinners">¡Seleccionado!</span>
                  <span class="tag is-success" v-else-if="project.feasible == true">Factible</span>
                  <span class="tag is-white" v-else>No evaluado</span>
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
    </div>
    <br>
    <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading">
      <span slot="no-results">
        <i class="fas fa-info-circle"></i> Fin de los resultados
      </span>
      <span slot="no-more">
        <i class="fas fa-info-circle"></i> ¡Fín de la lista!
      </span>
    </infinite-loading>
  </section>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";

export default {
  props: {
    url: {
      type: String,
      required: true
    },
    showFilters: {
      type: Boolean,
      default: true
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
    random: {
      type: Boolean,
      default: false
    },
    hideSelectedFilter: {
      type: Boolean,
      default: false
    },
    hideFeasibleFilter: {
      type: Boolean,
      default: false
    },
    hideUnfeasibleFilter: {
      type: Boolean,
      default: false
    },
    hideProposalFilter: {
      type: Boolean,
      default: false
    },
    showFeasibility: {
      type: Boolean,
      default: false
    },
    showWinners: {
      type: Boolean,
      default: false
    }
  },
  components: {
    InfiniteLoading
  },
  data() {
    return {
      isLoading: false,
      projects: [],
      paginator: {
        current_page: null,
        last_page: null,
        next_page_url: null,
        prev_page_url: null
      },
      nameToSearch: "",
      statusSelected: null,
      typeSelected: null,
      districtSelected: null
    };
  },
  created: function() {
    if (this.forceWinners) {
      this.statusSelected = "selected";
    }
    if (this.forceFeasible) {
      this.statusSelected = "feasible";
    }
    if (this.forceUnfeasible) {
      this.statusSelected = "unfeasible";
    }
    if (this.forceProposals) {
      this.statusSelected = "proposals";
    }
    if (this.forceType) {
      this.typeSelected = this.forceType;
    }
    if (this.forceDistrict) {
      this.districtSelected = this.forceDistrict;
    }
  },
  mounted: function() {},
  methods: {
    getWho(project) {
      if (project.type == "institucion") {
        return project.organization_name;
      }
      return project.author_names + " " + project.author_surnames;
    },
    cleanFilters: function() {
      this.statusSelected = null;
      this.districtSelected = null;
      this.typeSelected = null;
      this.nameToSearch = "";
      this.projects = [];
      this.paginator.current_page = null;
      this.paginator.last_page = null;
      this.paginator.next_page_url = null;
      this.paginator.prev_page_url = null;
      this.$nextTick(() => {
        this.$redrawVueMasonry();
        this.$refs.infiniteLoading.$emit("$InfiniteLoading:reset");
      });
    },
    resetEverything: function() {
      this.projects = [];
      this.paginator.current_page = null;
      this.paginator.last_page = null;
      this.paginator.next_page_url = null;
      this.paginator.prev_page_url = null;
      this.$nextTick(() => {
        this.$redrawVueMasonry();
        this.$refs.infiniteLoading.$emit("$InfiniteLoading:reset");
      });
    },
    search: function() {
      this.resetEverything();
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
    getImageStyle: function(type, image, id, selected) {
      let selectedStyle = "";
      if (selected && this.showWinners) {
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
    },
    fillPaginator: function(data) {
      this.paginator.current_page = data.current_page;
      this.paginator.last_page = data.last_page;
      this.paginator.next_page_url = data.next_page_url;
      this.paginator.prev_page_url = data.prev_page_url;
    },
    infiniteHandler: function($state) {
      if (this.paginator.current_page == null) {
        this.$http
          .get(this.urlGet)
          .then(response => {
            if (response.data.data === undefined)
              throw { message: "Error en query" };
            this.projects = this.projects.concat(
              this.shuffleArray(response.data.data)
            );
            this.$redrawVueMasonry();
            this.fillPaginator(response.data);
            $state.loaded();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message: "Error al obtener la lista de proyectos",
              type: "is-danger",
              actionText: "Cerrar"
            });
            $state.complete();
          });
      } else if (this.paginator.next_page_url) {
        this.$http
          .get(this.paginator.next_page_url)
          .then(response => {
            if (response.data.data === undefined)
              throw { message: "Error en query" };
            this.projects = this.projects.concat(
              this.shuffleArray(response.data.data)
            );
            this.$redrawVueMasonry();
            this.fillPaginator(response.data);
            $state.loaded();
          })
          .catch(error => {
            console.error(error.message);
            this.$snackbar.open({
              message: "Error al obtener la lista de proyectos",
              type: "is-danger",
              actionText: "Cerrar"
            });
            $state.complete();
          });
      } else {
        $state.complete();
      }
    }
  },
  watch: {
    selectedFilter: function(newVal, oldVal) {
      this.resetEverything();
    }
  },
  computed: {
    urlGet: function() {
      let query = [];
      if (this.random) {
        query.push("random=1");
      }
      if (this.nameToSearch !== "") {
        query.push("s=" + this.nameToSearch);
      }
      if (this.statusSelected == "selected" || this.forceWinners == true) {
        query.push(`selected=1`);
      }
      if (this.statusSelected == "feasible" || this.forceFeasible == true) {
        query.push(`feasible=1`);
      }
      if (this.statusSelected == "unfeasible" || this.forceUnfeasible == true) {
        query.push(`feasible=0`);
      }
      if (this.statusSelected == "proposals" || this.forceProposals == true) {
        query.push(`proposals=1`);
      }
      if (this.typeSelected !== null) {
        query.push("type=" + this.typeSelected);
      }
      if (this.districtSelected !== null) {
        query.push("district=" + this.districtSelected);
      }
      return this.url.concat(query.length > 0 ? "?" : "", query.join("&"));
    }
  }
};
</script>

<style lang="scss" scoped>
.name-search {
  background-color: transparent;
  border: 0;
  border-radius: 0;
  border-bottom: 1px solid #5a5a5a;
}

.some-other-effects {
  line-height: normal;
  margin-top: 5px;
}
.ribbon {
  position: absolute;
  top: 10px;
  left: 10px;
  height: 70px;
}
.delete-padding-touch {
  @media screen and (max-width: 769px) {
    padding-bottom: 0;
  }
}
</style>

<template>
  <section>
    <div>
      <b-message>
        Subí una imagen que tenga que ver con el proyecto para decorar la página del mismo.
        <br>Tamaño del archivo: 3MB. Se aceptan .JPG, .JPEG, PNG
        <br>
        <b>NOTA: Se recomiendan que las dimensiones sean al menos de un ancho de 1000px y alto de 500 px (De no se así se ajustaran las dimensionaes)</b>
      </b-message>
      <form :action="formUrl" ref="formProImg" method="post" enctype="multipart/form-data">
        <b-field class="file is-medium">
          <b-upload v-model="file" :required="true" name="archivo">
            <a class="button is-link is-medium">
              <i class="fas fa-upload"></i>&nbsp;
              <span>Click para cargar</span>
            </a>
          </b-upload>
          <span class="file-name" style="max-width: none;" v-if="file">{{ file[0].name }}</span>
        </b-field>
        <p
          v-show="!isFileOk && file !== null"
          class="has-text-danger"
        >Requerido. Debe ser un archivo .JPG, .JPEG, .PNG de hasta 3MB como máximo.</p>
        <div class="field">
          <div class="control is-clearfix">
            <a
              @click="submit"
              type="submit"
              class="button is-primary is-medium is-pulled-right"
              :class="{'is-loading': isLoading}"
            >
              <i class="fa fa-paper-plane fa-fw"></i> Enviar
            </a>
          </div>
        </div>
      </form>
      <hr>
      <img :src="`/proyectos/${project.id}/imagen`" alt>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["formUrl", "project"],
  data() {
    return {
      file: null,
      isLoading: false,
      mimes: ["image/jpeg", "image/pjpeg", "image/png"]
    };
  },
  methods: {
    submit: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error en el formulario. Verifíquelo",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$refs.formProImg.submit();
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    }
  },
  computed: {
    isFileOk: function() {
      if (this.file === null) return false;
      if (this.file[0] && this.file[0].size > 3145728) return false;
      if (this.file[0] && !this.mimes.includes(this.file[0].type)) return false;
      return true;
    }
  }
};
</script>

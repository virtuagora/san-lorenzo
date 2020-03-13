<template>
  <section>
    <div class="box">
      <b-message>
        Aquí podés subir archivos para ser presentados a la secretaria de Presupuesto Participativo de San Lorenzo.
        <br>Pueden ser imagenes, escaneos, archivos PDF o documentos de Microsoft Word
        <br>Maximo: 3MB. Se aceptan .JPG, .JPEG, .PDF, .DOC o .DOCX
      </b-message>

      <form :action="formUrl" ref="formProFile" method="post" enctype="multipart/form-data">
      <div class="field">
        <h1 class="title is-4" :class="{'has-text-danger': errors.has('name')}">
          <i class="fas fa-caret-right"></i>&nbsp;Nombre del archivo
        </h1>
        <h1 class="subtitle is-6">
          <span class="has-text-link">* Requerido.</span> Ejemplo: "Presupuesto Techo"
        </h1>
        <div class="control">
          <input
            v-model="name"
            name="name"
            data-vv-name="name"
            data-vv-as="'Nombre del archivo'"
            type="text"
            v-validate="'required|min:2|max:250'"
            class="input is-large"
            placeholder="Requerido *"
          >
          <span v-show="errors.has('name')" class="help is-danger">
            <i class="fas fa-times-circle fa-fw"></i>
            &nbsp;{{errors.first('name')}}
          </span>
        </div>
      </div>
      <div class="field">
        <h1 class="title is-4" :class="{'has-text-danger': errors.has('description')}">
          <i class="fas fa-caret-right"></i>&nbsp;Descripción del archivo
        </h1>
        <h1 class="subtitle is-6">
          <span class="has-text-link">* Opcional.</span> Utiliceló si desea dejar aclaraciones con respecto al archivo.
        </h1>
        <div class="control">
          <input
            v-model="description"
            data-vv-name="description"
            data-vv-as="'Descripción del archivo'"
            type="text"
            v-validate="'min:2|max:2000'"
            class="input is-large"
            placeholder="Opcional"
          >
          <span v-show="errors.has('description')" class="help is-danger">
            <i class="fas fa-times-circle fa-fw"></i>
            &nbsp;{{errors.first('description')}}
          </span>
          <input type="hidden" name="description" :value="descriptionFinal" v-if="descriptionFinal">
        </div>
      </div>
      <div class="field">
        <h1 class="title is-4" :class="{'has-text-danger': !isFileOk && file !== null }">
          <i class="fas fa-caret-right"></i>&nbsp;Archivo para subir
        </h1>
        <h1 class="subtitle is-6">
          <span class="has-text-link">* Requerido.</span>
        </h1>
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
        >Requerido. Debe ser un archivo .JPG, .JPEG, .PDF, .DOC o .DOCX de hasta 3MB como máximo.</p>
        </div>
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
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["formUrl", "project"],
  data() {
    return {
      name: null,
      description: null,
      file: null,
      isLoading: false,
      mimes: ['application/pdf','invalid/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/jpeg','image/pjpeg']
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
          this.$refs.formProFile.submit();
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
    },
    descriptionFinal: function(){
      return this.isOptional(this.description)
    }
  }
};
</script>
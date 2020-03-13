<template>
  <section>
    <div class="buttons">
      <button class="button is-primary is-fullwidth" @click="sendRandom" :class="{'is-loading': isSendingRandom}" :disabled="isSendingRandom">Enviar a 200 personas random</button>
    </div>
    <table class="table is-narrow is-fullwidth">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Ultimo Envio</th>
          <th>Padrón</th>
          <th class="has-text-centered">¿&nbsp;<i class="fas fa-paper-plane"></i>&nbsp;?</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in results" :key="user.id">
          <td>{{user.names}} {{user.surnames}}
            <br><span class="is-size-7">{{user.email}}</span>
            <br><span class="is-size-7"><b>Registro:</b> {{user.created_at}}</span>
            </td>
          <td>{{user.created_at == user.updated_at ? 'Nunca': user.updated_at}}</td>
          <td class="is-size-7">
            {{splitData(user.data)}}
            <br><b>Genero:</b> {{user.genre}}
            <br><b>Año:</b> {{user.year}}
            <br><b>Mesa:</b> {{user.table}} - <b>Orden:</b> {{user.orden}}
          </td>
          <td class="has-text-centered">
            <button  v-if="availableToShare(user.created_at,user.updated_at)" class="button is-small is-primary" @click="resend(user.id)" :class="{'button is-small is-primary': !isChecked(user.id), 'button is-small is-outlined': isChecked(user.id) }" :disabled="isChecked(user.id)">
              <i class="fas fa-paper-plane fa-fw"></i>
            </button>
            <button v-else class="button is-small is-dark is-outlined" disabled><i class="far fa-clock fa-fw"></i></button>
          </td>
        </tr>
        <tr v-if="isLoading">
          <td colspan="5" class="has-text-centered">
            <i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;Cargando, un momento por favor</td>
        </tr>
      </tbody>
    </table>
    <b-loading :is-full-page="true" :active.sync="isSending"></b-loading>
  </section>
</template>

<script>
export default {
  props: ["url", "post"],
  data() {
    return {
      isLoading: false,
      isSending: false,
      isSendingRandom: false,
      sended: [],
      results: []
    };
  },
  mounted: function() {
    this.get();
  },
  methods: {
    splitData: function(data){
      let arr = data.split(', ');
      return arr[0] + ' - ' + arr[1] + ' - ' + arr[2]
    },
    availableToShare: function(created, updated){
      let now = new Date();
      let dateCreated = new Date(created);
      let dateUpdated = updated ? new Date(updated) : null

      if(dateUpdated){
        let after = this.addMinutes(dateUpdated, 300)
        return now > after
      } else {
        let after = this.addMinutes(dateCreated, 300)
        return now > after
      }
    },
    addMinutes: function(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
},
    get: function() {
      this.isLoading = true;
      this.$http
        .get(this.url)
        .then(response => {
          this.isLoading = false;
          this.results = response.data;
        })
        .catch(error => {
          this.isLoading = false;
          console.error(error);
          this.$snackbar.open({
            message: "Ocurrio un error inesperado. Recargue la página",
            type: "is-danger",
            actionText: "Ok"
          });
        });
    },
    isChecked: function(id) {
      return this.sended.find(x => {
        return x == id;
      });
    },
    sendRandom: function(){
      this.isSendingRandom = true;
      let count = 0;
      let intervalId = setInterval(() => {
        count += 1
        let randomIndex = Math.floor(Math.random() * this.results.length)
        let user = this.results[randomIndex]
        console.log('Can send? (Counter: ' + count + ')')
        if(this.availableToShare(user.created_at,user.updated_at) && !this.isChecked(user.id)){
          console.log('Yes, sending...')
          this.resend(user.id)
        } else {
          console.log('No, waiting...')
        }
        if(count == 200){
          clearInterval(intervalId)
          this.isSendingRandom = false;
        }
      }, 4500)      
    },
    resend: function(id) {
      this.sended.push(id);
      this.isSending = true;
      this.$http
        .post(this.post, {id: id})
        .then(response => {
          this.isSending = false;
          this.$snackbar.open({
              message: "¡Email enviado! El servidor de correo contestó correctamente.",
              type: "is-success",
              actionText: "Genial"
            });
        })
        .catch(error => {
          var index = this.sended.indexOf(id);
          if (index > -1) {
            this.sended.splice(index, 1);
          }
          this.isSending = false;
          console.error(error);
          this.$snackbar.open({
            message: "Ocurrio un error inesperado.",
            type: "is-danger",
            actionText: "Ok"
          });
        });
    }
  }
};
</script>

<style>
</style>

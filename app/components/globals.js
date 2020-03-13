module.exports = {
  methods: {
    isOptional: function (value) {
      if (value === null || value === "") {
        return null;
      }
      if (typeof value !== "undefined" && value.length == 0) {
        return [];
      } else return value;
    },
    forceUpdateState: function (ref) {
      return new Promise((resolve, reject) => {
        http.get(window.getUserDataUrl())
          .then(response => {
            console.log('Updating user');
            store.commit('bind', { user: response.data })
            store.commit('bind', { expires: Date.now() + 5 * 60 * 1000 })
            if (ref != null || ref != undefined) {
              console.log('Updating ref: ' + ref);
              vm.$refs[ref].updateUserState()
            }
            resolve(response.data)
          })
          .catch(e => {
            console.error(e)
            vm.$snackbar.open({
              message: "Error al conectarse con el servidor. Por favor, recarge la página.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            reject(e)
          })
      })
    },
    capitalizeString: function(string){
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    updateState: function () {
      if (Date.now() > store.state.expires) {
        http.get(window.getUserDataUrl())
          .then(response => {
            console.log('Updating user');
            store.commit('bind', { user: response.data })
            store.commit('bind', { expires: Date.now() + 5 * 60 * 1000 })
          })
          .catch(e => {
            console.error(e)
            vm.$snackbar.open({
              message: "Error al conectarse con el servidor. Por favor, recarge la página.",
              type: "is-danger",
              actionText: "Cerrar"
            });
          })
      } else {
        console.log('State still fresh');
      }
    }
  }
}
import Vue from 'vue'
import Vuex from 'vuex'
import http from './http'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    user: null,
    expires: null,
    members: [],
    group: null,
    institucionales: [],
    comunitarios: [],
    voteInstitucionales: [],
    voteComunitarios: [],
    publicVoteCode: null,
    codeOnQuery: false,
    recaptcha: null
  },
  mutations: {
    removeComunitario: function(state, proyecto){
      state.voteComunitarios = state.voteComunitarios.filter( el => el.id !== proyecto.id );
    },
     addComunitario: function(state, proyecto){
      state.voteComunitarios.push(proyecto);
    },
    removeInstitucional: function(state, proyecto){
      state.voteInstitucionales = state.voteInstitucionales.filter( el => el.id !== proyecto.id );
    },
     addInstitucional: function(state, proyecto){
      state.voteInstitucionales.push(proyecto);
    },
    resetPublicVote: function(state){
      state.voteInstitucionales = [];
      state.voteComunitarios = [];
      state.publicVoteCode = null;
      state.codeOnQuery = false;
      state.recaptcha = null;
    },
    restartPublicVote: function(state){
      state.voteInstitucionales = [];
      state.voteComunitarios = [];
      state.recaptcha = null;
    },
    bind: function (state, element) {
      Object.assign(state, element);
    },
  },
  actions: {
    // prepareData: function ({commit}, session){
    //   return new Promise((resolve, reject) => {
    //       commit('checkUser', session)
    //       if(session.id != null){
    //         // There is a session, get user.
    //         resolve(true)
    //       } else {
    //         // NO session, NO user
    //         resolve(false)
    //       }
    //   })
    // }
  },
  getters: {
    get: (state) => {
      return key => {return state[key]}
      // return state
    },
    // getUserGroup: (state) => {
    //   if(state.user != null && state.user.groups.length > 0){
    //   return state.user.groups[0]
    //   }
    //   return null;
    // }
  },
  plugins: [createPersistedState({
    key: 'pp-sl',
    paths: ['user', 'expires']
  })]
})

export default store;
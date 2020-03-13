
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'
import http from './http'

import routePrivateVotePanel from './sl/private-vote/private-vote-routes'
import routePublicVotePanel from './sl/public-vote/public-vote-routes'

Vue.use(VueRouter)

let routes = [].concat(
  routePrivateVotePanel,
  routePublicVotePanel
)


const router = new VueRouter({
  mode: 'history',
  routes: routes,
  scrollBehavior(to, from, savedPosition) {
    return { x: 0, y: 0 }
  }
})

export default router;
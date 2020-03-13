import AppSettings from './AppSettings'
import Overview from './Overview'
import Moderation from './Moderation'
import Members from './Members'
import store from '../../store'
import http from '../../http'

const basePath = '/settings/'

const routes = [
  {
    path: basePath,
    component: AppSettings,
    name: 'settingsApp',
    props: true,
    children: [
      {
        path: basePath + 'overview',
        component: Overview,
        name: 'settingsOverview'
      },
      {
        path: basePath + 'moderation',
        component: Moderation,
        name: 'settingsModeration'
      },
      {
        path: basePath + 'members',
        component: Members,
        name: 'settingsMembers'
      }
    ],
    beforeEnter: (to, from, next) => {
      console.log('First time entering, getting user...')
      http.get('https://jsonplaceholder.typicode.com/users/1')
        .then(response => {
          // JSON responses are automatically parsed.
          store.commit('bind', { app: response.data })
          next()
        })
        .catch(e => {
          console.log(e)
          next()
        })
    }
  }
]

export default routes;
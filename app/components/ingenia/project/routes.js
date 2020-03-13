import store from '../../store'
import http from '../../http'


// Panel
import Project from './Project'
import Overview from './Overview'
import Budget from './Budget'
import Team from './Team'
import Implementation from './Implementation'


const basePath = '/proyecto/:id'

const routes = [
  {
    path: basePath,
    component: Project,
    props: true,
    children: [
      {
        path: '',
        component: Overview,
        name: 'projectOverview',
        props: true
      },
      {
        path: 'equipo',
        component: Team,
        name: 'projectTeam',
        props: true
      },
      {
        path: 'implementacion',
        component: Implementation,
        name: 'projectImplementation',
        props: true
      }
    ],
    // beforeEnter: (to, from, next) => {
    //   console.log('First time entering, getting user...')
    //   http.get(window.getUserDataUrl())
    //     .then(response => {
    //       // JSON responses are automatically parsed.
    //       store.commit('bind', { user: response.data })
    //       next()
    //     })
    //     .catch(e => {
    //       console.log(e)
    //       next()
    //     })un problema criti
    // }
  }
]

export default routes;
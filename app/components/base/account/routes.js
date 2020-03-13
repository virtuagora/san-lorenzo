import AccountSettings from './AccountSettings'
import Overview from './Overview'
import EditProfile from './EditProfile'
import Password from './Password'
import store from '../../store'
import http from '../../http'

const basePath = '/account/:id'

const routes = [
  {
    path: basePath,
    component: AccountSettings,
    name: 'accountSettings',
    props: true,
    children: [
      {
        path: '',
        component: Overview,
        name: 'accountProfile',
        props: true
      },
      {
        path: 'edit',
        component: EditProfile,
        name: 'accountEditProfile',
        props: true
      },
      {
        path: 'password',
        component: Password,
        name: 'accountPassword',
        props: true
      },
    ],
    beforeEnter: (to, from, next) => {
      console.log('First time entering, getting user...')
      http.get('https://jsonplaceholder.typicode.com/users/' + to.params.id)
        .then(response => {
          // JSON responses are automatically parsed.
          store.commit('bind', { user: response.data })
          next()
        })
        .catch(e => {
          console.log(e)
          next()
        })
    }
  }

  // {
  //   path: basePath,
  //   component: Overview,
  //   name: 'accountProfile',
  //   props: true
  // },
  // {
  //   path: basePath + 'edit',
  //   component: EditProfile,
  //   name: 'accountEditProfile',
  //   props: true
  // },
  // {
  //   path: basePath + 'password',
  //   component: Password,
  //   name: 'accountPassword',
  //   props: true
  // },

]

export default routes;
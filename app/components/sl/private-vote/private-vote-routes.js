import store from '../../store'
import http from '../../http'


// Panel
import PrivateVote from './PrivateVote'
import Hello from './Hello'
import Tutorial from './Tutorial'
import Step1 from './Step1'
import Step2 from './Step2'
import Step3 from './Step3'
import Confirm from './Confirm'
import Sending from './Sending'

const basePath = '/votar'

const routes = [
  {
        path: basePath + '',
        component: Hello,
        name: 'PrivateHello',
        props: true
      },
      {
        path: basePath + '/tutorial',
        component: Tutorial,
        name: 'PrivateTutorial',
        props: true
      },
      {
        path: basePath + '/paso-1',
        component: Step1,
        name: 'PrivateStep1',
        props: true
      },
      {
        path: basePath + '/paso-2',
        component: Step2,
        name: 'PrivateStep2',
        props: true
      },
      {
        path: basePath + '/paso-3',
        component: Step3,
        name: 'PrivateStep3',
        props: true
      },
      {
        path: basePath + '/confirmar',
        component: Confirm,
        name: 'PrivateConfirm',
        props: true
      },
      {
        path: basePath + '/sending',
        component: Sending,
        name: 'PrivateSending',
        props: true
      }
]

export default routes;
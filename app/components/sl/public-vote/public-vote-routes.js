import store from '../../store'
import http from '../../http'


// Panel
import PublicVote from './PublicVote'
import EnterCode from './EnterCode'
import Hello from './Hello'
import Tutorial from './Tutorial'
import Step1 from './Step1'
import Step2 from './Step2'
import Step3 from './Step3'
import Confirm from './Confirm'
import Sending from './Sending'
import Success from './Success'
import ErrorInvalid from './errors/ErrorInvalid'
import ErrorInvalidCaptcha from './errors/ErrorInvalidCaptcha'
import ErrorInvalidCode from './errors/ErrorInvalidCode'
import ErrorInvalidVote from './errors/ErrorInvalidVote'
import ErrorUsedCode from './errors/ErrorUsedCode'

const basePath = '/urna'

const routes = [
      {
        path: basePath + '',
        component: EnterCode,
        name: 'PublicEnterCode',
        props: true
      },
      {
        path: basePath + '/bienvenido',
        component: Hello,
        name: 'PublicHello',
        props: true
      },
      {
        path: basePath + '/tutorial',
        component: Tutorial,
        name: 'PublicTutorial',
        props: true
      },
      {
        path: basePath + '/paso-1',
        component: Step1,
        name: 'PublicStep1',
        props: true
      },
      {
        path: basePath + '/paso-2',
        component: Step2,
        name: 'PublicStep2',
        props: true
      },
      {
        path: basePath + '/paso-3',
        component: Step3,
        name: 'PublicStep3',
        props: true
      },
      {
        path: basePath + '/confirmar',
        component: Confirm,
        name: 'PublicConfirm',
        props: true
      },
      {
        path: basePath + '/sending',
        component: Sending,
        name: 'PublicSending',
        props: true
      },
      {
        path: basePath + '/success',
        component: Success,
        name: 'PublicSuccess',
        props: true
      },
      {
        path: basePath + '/error/invalid',
        component: ErrorInvalid,
        name: 'PublicErrorInvalid',
        props: true
      },
      {
        path: basePath + '/error/invalid-captcha',
        component: ErrorInvalidCaptcha,
        name: 'PublicErrorInvalidCaptcha',
        props: true
      },
      {
        path: basePath + '/error/invalid-code',
        component: ErrorInvalidCode,
        name: 'PublicErrorInvalidCode',
        props: true
      },
      {
        path: basePath + '/error/invalid-vote',
        component: ErrorInvalidVote,
        name: 'PublicErrorInvalidVote',
        props: true
      },
      {
        path: basePath + '/error/used-code',
        component: ErrorUsedCode,
        name: 'PublicErrorUsedCode',
        props: true
      },
  
]

export default routes;
// Base Components
import Vue from 'vue'
import Buefy from 'buefy'
import http from './http'
import router from './router'
import store from './store'
import es from 'vee-validate/dist/locale/es';
import VeeValidate, { Validator } from 'vee-validate';
import { VueMasonryPlugin } from 'vue-masonry';

Validator.localize('es',es);

Vue.use(VeeValidate,{
  locale: 'es'
});
Vue.use(VueMasonryPlugin);

// Ingenia 2018 Componentes
import VueCarousel from 'vue-carousel';
import Avatar from './sl/utils/Avatar';
import ProjectJournal from './sl/utils/ProjectJournal';
import Budget from './sl/utils/Budget';
import FormImage from './sl/utils/FormImage';
import FormFile from './sl/utils/FormFile';
import PublicStamp from './sl/utils/PublicStamp';
import Login from './sl/base/Login'
import CompletarRegistro from './sl/base/CompletarRegistro'
import CompletarResetPassword from './sl/base/CompletarResetPassword'
import ProjectCarousel from './sl/carousel/ProjectCarousel'
import Exhibit from './sl/carousel/Exhibit'
import Catalogo from './sl/carousel/Catalogo'
import AdminAssignAdmin from './sl/admin/AssignAdmin'
import AdminListProjects from './sl/admin/ListProjects'
import AdminCreateProject from './sl/admin/CreateProject'
import AdminEditProject from './sl/admin/EditProject'
import AdminEditProjectUser from './sl/admin/EditProjectUser'
import AdminEditProjectFeasibility from './sl/admin/EditProjectFeasibility'
import AdminStamp from './sl/admin/Stamp'
import Boletas from './sl/admin/Boletas'
import VotesChart from './sl/admin/stats/VotesChart'
import GenreDonut from './sl/admin/stats/GenreDonut'
import PendingSender from './sl/admin/PendingSender'
import PendingVoterSender from './sl/admin/PendingVoterSender'
// import SyncPadron from './sl/admin/SyncPadron'
import SyncPadron from './sl/admin/padron/Sync'
import AddCitizen from './sl/admin/padron/AddCitizen'
import Settings from './sl/admin/Settings'
import CountdownTo from './sl/utils/CountdownTo'
import CountdownClosing from './sl/utils/CountdownClosing'
import CalendarIndex from './sl/utils/CalendarIndex'
import PrivateVote from './sl/private-vote/PrivateVote'
import PublicVote from './sl/public-vote/PublicVote'
import StatLines from './sl/stats/StatLines'
import StatBars from './sl/stats/StatBars'
import StatStackedBars from './sl/stats/StatStackedBars'
import UserPanelListProjects from './sl/panel/ListProjects'
import UserPanelCreateProject from './sl/panel/CreateProject'
import UserPanelEditProject from './sl/panel/EditProject'
import UserPanelEditPassword from './sl/panel/EditPassword'

Vue.use(VueCarousel)

// We are going to use Buefy
Vue.use(Buefy, {
  defaultIconPack: 'fas',
  defaultDialogConfirmText: 'OK',
  defaultDialogCancelText: 'Cancelar',
  defaultDayNames: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
  defaultMonthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
})

// Axios configuration
// go to http.js to configure axios according to your needs
Vue.prototype.$http = http

Vue.mixin(require('./globals'))

window.vm = new Vue({ // eslint-disable-line no-new
  el: '#vue', // The id of the DOM element,
  http,
  router,
  store,
  components: {
    Avatar,
    Login,
    CompletarRegistro,
    CompletarResetPassword,
    ProjectCarousel,
    AdminAssignAdmin,
    AdminListProjects,
    AdminCreateProject,
    AdminEditProject,
    AdminEditProjectUser,
    AdminEditProjectFeasibility,
    AdminStamp,
    PublicStamp,
    ProjectJournal,
    Budget,
    FormImage,
    FormFile,
    UserPanelListProjects,
    UserPanelCreateProject,
    UserPanelEditProject,
    UserPanelEditPassword,
    SyncPadron,
    AddCitizen,
    CountdownTo,
    CountdownClosing,
    PublicVote,
    PrivateVote,
    Settings,
    Exhibit,
    CalendarIndex,
    Catalogo,
    PendingSender,
    PendingVoterSender,
    VotesChart,
    GenreDonut,
    Boletas,
    StatLines,
    StatBars,
    StatStackedBars
  },
  // beforeCreate: function () {
  //   store.dispatch('prepareData', window.getUserId()).then(response => {
  //     // response => true if the user state expired.
  //     if(response){
  //     this.updateState()
  //     }
  //   })
  // }
})
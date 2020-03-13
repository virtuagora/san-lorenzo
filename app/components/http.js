import axios from 'axios'

// Settings for axios 
  // axios.defaults.timeout = 5000;
axios.defaults.baseURL = '/';
axios.defaults.headers.common['Accept'] = 'application/json';
// axios.interceptors.request.use(
//     config => {
//         if (store.state.session) {
//             config.headers.Authorization = `Bearer ${store.state.session.token}`;
//         }
//         return config;
//     },
//     err => {
//         return Promise.reject(err);
//     });

// axios.interceptors.response.use(
//     response => {
//         return response;
//     },
//     error => {
//         if (error.response) {
//             switch (error.response.status) {
//                 case 401:
//                     store.commit('logout');
//                     router.replace({
//                         path: 'login',
//                         query: { redirect: router.currentRoute.fullPath }
//                     })
//             }
//         }
//         return Promise.reject(error.response.data)
//     });

export default axios;
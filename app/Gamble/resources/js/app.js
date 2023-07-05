import router from './router/index';

require('./bootstrap');
import Vue from 'vue';
import Mousetrap from 'mousetrap';
import Toast from 'cxlt-vue2-toastr';
import 'cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';

import ApplicationMenu from './components/application-menu';
import Multiselect from 'vue-multiselect';
import VueJSModal from 'vue-js-modal';

Vue.component('application-menu', ApplicationMenu);
Vue.component('multiselect', Multiselect);

Vue.use(Toast, {
    position: 'bottom right',
    showDuration: 2500,
});
Vue.use(VueJSModal);

Vue.prototype.$eventHub = new Vue();

new Vue({
    router: router,
    computed: {
        user() {
            return this.$refs.auth.user;
        },
    },
}).$mount('#app');

Mousetrap.bind('/', function (e) {
    const el = document.getElementById('search');
    if (el) {
        e.preventDefault();
        el.focus();
    }
});

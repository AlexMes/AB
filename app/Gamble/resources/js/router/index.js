import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from './Dashboard';
import ApplicationIndex from './applications/Index';
import ApplicationForm from './applications/Form';
import ApplicationShow from './applications/Show.vue';
import ApplicationLinks from './applications/Links';
import Accounts from './accounts/index.js';
import Campaigns from './campaigns/index.js';
import Insights from './insights/index.js';
import Offers from './offers/index.js';
import Groups from './groups/index.js';
import TechCosts from './tech-costs/index.js';
import Reports from './reports/index.js';

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/',
    hashbang: false,
    routes: [
        { path: '', redirect: 'dashboard' },
        { path: '/dashboard', component: Dashboard, name: 'dashboard' },

        { path: '/applications', component: ApplicationIndex, name: 'applications.index' },
        { path: '/applications/create', component: ApplicationForm, name: 'applications.create' },
        {
            path: '/applications/:id',
            component: ApplicationShow,
            /*name: 'applications.show',*/
            props: true,
            children: [
                {
                    path: '',
                    redirect: 'links',
                    props: true,
                },
                {
                    path: 'links',
                    name: 'applications.links',
                    component: ApplicationLinks,
                    props: true,
                },
            ],
        },
        { path: '/applications/:id/edit', component: ApplicationForm, name: 'applications.update', props: true },
        ...Accounts,
        ...Campaigns,
        ...Insights,
        ...Offers,
        ...Groups,
        ...TechCosts,
        ...Reports,
    ],
});

import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from './components/Dashboard.vue';
import List1 from './components/List1.vue';
import List2 from './components/List2.vue';
import Log from './components/Log.vue';
import Schedule from './components/Schedule.vue';

const routes = [
    // {
    //     path: '/',
    //     name: 'Dashboard',
    //     component: Dashboard
    // },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard
    },
    {
        path: '/list1',
        name: 'List1',
        component: List1,
    },
    {
        path: '/list2',
        name: 'List2',
        component: List2
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

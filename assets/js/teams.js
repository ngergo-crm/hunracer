import Vue from 'vue';
import teams from '@/pages/teams/teams';
import store from '@/store/usermanagerVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(teams),
}).$mount('.teams');

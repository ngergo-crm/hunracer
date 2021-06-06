import '../css/teams.scss';
import Vue from 'vue';
import teams from '@/pages/teams/teams';
import store from '@/store/teamsVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(teams),
}).$mount('.teams');

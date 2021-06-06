import Vue from 'vue';
import Vuex from 'vuex';
import teams from '@/store/modules/teams';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        teams,
    },
    strict: debug,
});

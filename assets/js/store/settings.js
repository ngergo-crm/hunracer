import Vue from 'vue';
import Vuex from 'vuex';
import settings from '@/store/modules/settings';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        settings,
    },
    strict: debug,
});

import Vue from 'vue';
import Vuex from 'vuex';
import usermanager from '@/store/modules/usermanager';
import settings from '@/store/modules/settings';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        usermanager,
        settings,
    },
    strict: debug,
});

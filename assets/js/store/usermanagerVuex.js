import Vue from 'vue';
import Vuex from 'vuex';
import usermanager from '@/store/modules/usermanager';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        usermanager,
    },
    strict: debug,
});

import Vue from 'vue';
import Vuex from 'vuex';
import sections from '@/store/modules/sections';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        sections,
    },
    strict: debug,
});

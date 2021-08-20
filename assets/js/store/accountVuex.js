import Vue from 'vue';
import Vuex from 'vuex';
import account from '@/store/modules/account';
import settings from '@/store/modules/settings';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        account,
        settings,
    },
    strict: debug,
});

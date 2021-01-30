import Vue from 'vue';
import Vuex from 'vuex';
import account from '@/store/modules/account';

Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        account,
    },
    strict: debug,
});

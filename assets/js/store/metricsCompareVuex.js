import Vue from 'vue';
import Vuex from 'vuex';
import metricsCompare from '@/store/modules/metricsCompare';
import adminHomepage from '@/store/modules/adminHomepage';
import VueGoogleCharts from 'vue-google-charts';

Vue.use(VueGoogleCharts);
Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        metricsCompare,
        adminHomepage,
    },
    strict: debug,
});

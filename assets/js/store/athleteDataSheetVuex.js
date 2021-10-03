import Vue from 'vue';
import Vuex from 'vuex';
import athleteDataSheet from '@/store/modules/athleteDataSheet';
import VueGoogleCharts from 'vue-google-charts';

Vue.use(VueGoogleCharts);
Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        athleteDataSheet,
    },
    strict: debug,
});

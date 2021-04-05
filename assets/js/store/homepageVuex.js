import Vue from 'vue';
import Vuex from 'vuex';
import trainingPeaksHandler from '@/store/modules/trainingPeaksHandler';
import VueGoogleCharts from 'vue-google-charts';

Vue.use(VueGoogleCharts);
Vue.use(Vuex);

const debug = process.env.APP_ENV !== 'prod';

export default new Vuex.Store({
    modules: {
        trainingPeaksHandler,
    },
    strict: debug,
});

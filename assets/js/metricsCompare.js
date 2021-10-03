import '../css/metricsCompare.scss';
import '../css/athleteDataSheet.scss';
import Vue from 'vue';
import metricsCompare from '@/pages/metricsCompare/metricsCompare';
import store from '@/store/metricsCompareVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(metricsCompare),
}).$mount('.metricsCompare');

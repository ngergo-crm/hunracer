import '../css/athleteDataSheet.scss';
import Vue from 'vue';
import athleteDataSheet from '@/pages/athleteDataSheet/athleteDataSheet';
import store from '@/store/athleteDataSheetVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(athleteDataSheet),
}).$mount('.athleteDataSheet');

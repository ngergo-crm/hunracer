import '../css/homepage.scss';
import Vue from 'vue';
import Homepage from '@/pages/homepage/homepage';
import store from '@/store/homepageVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(Homepage),
}).$mount('.homepage');

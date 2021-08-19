import '../css/app.scss';
import Vue from 'vue';
import Settings from '@/pages/settings/settings';
import store from '@/store/settings';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(Settings),
}).$mount('.settings');

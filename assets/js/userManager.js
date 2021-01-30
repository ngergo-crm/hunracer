import '../css/userManager.scss';
import Vue from 'vue';
import userManager from '@/pages/userManager/userManager';
import store from '@/store/usermanagerVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(userManager),
}).$mount('.userManager');

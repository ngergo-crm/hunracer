import '../css/account.scss';
import Vue from 'vue';
import Account from '@/pages/account/account';
import store from '@/store/accountVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(Account),
}).$mount('.account');

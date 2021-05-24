import Vue from 'vue';
import sections from '@/pages/sections/sections';
import store from '@/store/sectionsVuex';

export default {
    store,
};

new Vue({
    store,
    render: (h) => h(sections),
}).$mount('.sections');

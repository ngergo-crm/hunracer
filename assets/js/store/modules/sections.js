import Axios from 'axios';
import { findIndex } from '@/components/helper';

const state = () => ({
    sections: [],
});

const getters = {
    // getUser: (state) => (
    //     state.user
    // ),
};

const actions = {
    async initializeSections({ commit }) {
        await Axios.get('/api/sections').then((res) => {
            commit('setSections', res.data['hydra:member']);
        });
    },
    async rename({ commit, state }, { newName, section }) {
        const param = { description: newName };
        Axios.put(section['@id'], param).then(() => {
            commit('updateSection', { place: findIndex(state.sections, section), key: 'description', value: newName });
        });
    },
    async delete({ commit, state }, section) {
        await Axios.delete(section['@id']).then(() => {
            commit('deleteSection', findIndex(state.sections, section));
        });
    },
    async create({ commit, state }, newName) {
        Axios.post('/api/sections', { description: newName }).then((res) => {
            commit('addSection', res.data);
        });
    },
};

const mutations = {
    setSections(state, payload) {
        state.sections = payload;
    },
    updateSection(state, { place, key, value }) {
        state.sections[place][key] = value;
    },
    deleteSection(state, index) {
        state.sections.splice(index, 1);
    },
    addSection(state, section) {
        state.sections.push(section);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

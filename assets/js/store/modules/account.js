import Axios from 'axios';
import moment from 'moment/moment';

const state = () => ({
    user: {},
    trainerCode: '',
    sections: [],
    teams: [],
    genders: [],
});

const getters = {
    getUser: (state) => (
        state.user
    ),
    getSections: (state) => (
        state.sections
    ),
    getTeams: (state) => (
        state.teams
    ),
    getSectionIds: (state) => state.user.sections.map((section) => (
        section['@id']
    )),
};

const actions = {
    async initializeUser({ commit, state }) {
        Axios.get('/api/users/me').then((res) => {
            commit('setUser', res.data['hydra:member'][0]);
            commit('setTrainerCode', state.user.trainerCode);
        });
        Axios.get('/api/sections').then((res) => {
            commit('setSections', res.data['hydra:member']);
        });
        Axios.get('/api/teams').then((res) => {
            commit('setTeams', res.data['hydra:member']);
        });
        Axios.get('/api/genders').then((res) => {
            commit('setGenders', res.data['hydra:member']);
        });
    },
    modifyUser({ state, getters, commit }) {
        const param = {
            name: state.user.name,
            phone: state.user.phone,
            birthday: moment(state.user.birthday).format('YYYY-MM-DD') !== 'Invalid date' ? moment(state.user.birthday).format('YYYY-MM-DD') : null,
            trainerCode: state.trainerCode,
            sections: getters.getSectionIds,
            team: state.user.team ? state.user.team['@id'] : null,
            gender: state.user.gender ? state.user.gender['@id'] : null,
            photo: state.user.photo ? state.user.photo : null,
            photoSrc: state.user.photoSrc ? state.user.photoSrc : null,
        };
        Axios.put(state.user['@id'], param).then(() => {
            commit('updateUser', { key: 'photoSrc', value: null });
        });
    },
    deletePhoto({ commit }) {
        Axios.put('/removeAccountPhoto').then(() => {
            commit('updateUser', { key: 'photo', value: '' });
        });
    },
    changeUserPassword({ state }, { newPassword, currentPassword }) {
        const param = { password: newPassword, current_password: currentPassword };
        Axios.put(`${state.user['@id']}/changePassword`, param).then(() => {
            //todo validation
        });
    },
};

const mutations = {
    setUser(state, payload) {
        state.user = payload;
    },
    setTrainerCode(state, payload) {
        state.trainerCode = payload;
    },
    updateUser(state, { key, value }) {
        state.user[key] = value;
    },
    setSections(state, payload) {
        state.sections = payload;
    },
    setTeams(state, payload) {
        state.teams = payload;
    },
    setGenders(state, payload) {
        state.genders = payload;
    },
    setNewImages(state, { img, imgName }) {
        state.user.photo = imgName;
        state.user.photoSrc = img;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

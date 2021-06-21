import Axios from 'axios';
import moment from 'moment/moment';

const state = () => ({
    user: {},
    trainerCode: '',
    sections: [],
    teams: [],
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
            commit('setUser', res.data);
            commit('setTrainerCode', state.user.trainerCode);
        });
        Axios.get('/api/sections').then((res) => {
            commit('setSections', res.data['hydra:member']);
        });
        Axios.get('/api/teams').then((res) => {
            commit('setTeams', res.data['hydra:member']);
        });
    },
    modifyUser({ state, getters }) {
        //"0000-00-00T00:00:00+00:00"
        //console.log(moment('2010-06-11T00:00:00+00:00'));
        const param = {
            name: state.user.name,
            phone: state.user.phone,
            birthday: state.user.birthday ? state.user.birthday : '0000-00-00T00:00:00+00:00',
            trainerCode: state.trainerCode,
            sections: getters.getSectionIds,
            team: state.user.team ? state.user.team['@id'] : null,
        };
        Axios.put(state.user['@id'], param);
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
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

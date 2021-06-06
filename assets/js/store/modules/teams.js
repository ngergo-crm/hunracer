import Axios from 'axios';
import { findIndex } from '@/components/helper';

const state = () => ({
    teams: [],
});

const getters = {
    getTeams: (state) => state.teams.map((team) => ({
        '@id': team['@id'],
        '@type': team['@type'],
        fullname: team.fullname,
        shortname: team.shortname,
        contactphone: team.contactphone ? team.contactphone : '',
        contactmail: team.contactmail ? team.contactmail : '',
        contactname: team.contactname ? team.contactname : '',
        _showDetails: false,
        edit: {
            '@id': team['@id'],
            '@type': team['@type'],
            fullname: team.fullname,
            shortname: team.shortname,
            contactphone: team.contactphone ? team.contactphone : '',
            contactmail: team.contactmail ? team.contactmail : '',
            contactname: team.contactname ? team.contactname : '',
        },
    })),
};

const actions = {
    async initializeTeams({ commit }) {
        await Axios.get('/api/teams').then((res) => {
            commit('setTeams', res.data['hydra:member']);
        });
    },
    async edit({ commit, state }, team) {
        Axios.put(team['@id'], team).then(() => {
            commit('editTeam', { index: findIndex(state.teams, team), data: team });
        });
    },
    async delete({ commit, state }, team) {
        await Axios.delete(team['@id']).then(() => {
            commit('deleteTeam', findIndex(state.teams, team));
        });
    },
    async create({ commit, state }, newTeam) {
        await Axios.post('/api/teams', newTeam).then((res) => {
            commit('addTeam', res.data);
        });
    },
};

const mutations = {
    setTeams(state, payload) {
        state.teams = payload;
    },
    editTeam(state, { index, data }) {
        state.teams[index].fullname = data.fullname;
        state.teams[index].shortname = data.shortname;
        state.teams[index].contactphone = data.contactphone;
        state.teams[index].contactname = data.contactname;
        state.teams[index].contactmail = data.contactmail;
    },
    deleteTeam(state, index) {
        state.teams.splice(index, 1);
    },
    addTeam(state, section) {
        state.teams.unshift(section);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

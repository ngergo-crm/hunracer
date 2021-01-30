import axios from 'axios';
import Axios from 'axios';

const state = () => ({
    user: {},
});

const getters = {
    getUser: (state) => (
        state.user
    ),
};

const actions = {
    async initializeUser({ commit }) {
        await axios.get('/api/users/me').then((res) => {
            commit('setUser', res.data);
        });
    },
    modifyUser({ state }) {
        const param = { name: state.user.name, phone: state.user.phone };
        Axios.put(state.user['@id'], param);
    },
};

const mutations = {
    setUser(state, payload) {
        state.user = payload;
    },
    updateUser(state, { key, value }) {
        state.user[key] = value;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

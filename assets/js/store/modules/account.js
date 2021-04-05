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
        await Axios.get('/api/users/me').then((res) => {
            commit('setUser', res.data);
        });
    },
    modifyUser({ state }) {
        const param = { name: state.user.name, phone: state.user.phone };
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

import Axios from 'axios';

const state = () => ({
    user: {},
    trainerCode: '',
});

const getters = {
    getUser: (state) => (
        state.user
    ),
};

const actions = {
    async initializeUser({ commit, state }) {
        await Axios.get('/api/users/me').then((res) => {
            commit('setUser', res.data);
            commit('setTrainerCode', state.user.trainerCode);
        });
    },
    modifyUser({ state }) {
        const param = { name: state.user.name, phone: state.user.phone, trainerCode: state.trainerCode };
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
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

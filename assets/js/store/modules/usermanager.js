import Axios from 'axios';
import { uuid } from 'vue-uuid';

// const state = () => ({
//     users: {},
//     user: {
//         uuid: uuid.v1(),
//         name: 'ccc',
//         email: '',
//         phone: '',
//         roles: '',
//         password: uuid.v4(),
//     },
// });

const getDefaultState = () => ({
    users: {},
    user: {
        uuid: uuid.v1(),
        name: '',
        email: '',
        phone: '',
        roles: 'ROLE_USER',
        password: uuid.v4(),
    },
});

// initial state
const state = getDefaultState();

const getters = {
    getUser: (state) => (
        state.user
    ),
    //a roles itt tudom rendesen array-á alakítani, hogy ne omoljon össze a vs-option...
    getUserForRegistration: (state) => ({
        uuid: state.user.uuid,
        name: state.user.name,
        email: state.user.email,
        phone: state.user.phone,
        roles: [state.user.roles],
        password: state.user.password,
    }),
    getUsers: (state) => (
        state.users['hydra:member']
    ),
};

const actions = {
    async loadUsers({ commit }) {
        await Axios.get('/api/users').then((res) => {
            commit('setUsers', res.data);
        });
    },
    registerUser({ commit, state, getters }) {
        Axios.post('/api/users', getters.getUserForRegistration).then((res) => {
            commit('addUserToCollection', res.data);
            commit('resetUserForm');
        });
    },
    async deleteUser({ commit, state }, user) {
        await Axios.delete(user['@id']).then(() => {
            const cleanedUsers = state.users['hydra:member'].filter((item) => item['@id'] !== user['@id']);
            commit('setUserCollection', cleanedUsers);
        });
    },
    changeUserProperty({ commit, state }, data) {
        const param = { [data.prop]: data.val };
        Axios.put(data.user['@id'], param).then(() => {
            const index = state.users['hydra:member'].findIndex((element) => {
                if (element['@id'] === data.user['@id']) {
                    return true;
                }
            });
            commit('changeUserProp', { place: index, key: data.prop, value: data.val });
        });
    },
    changeUserPropertyWithoutDB({ commit, state }, data) {
        const index = state.users['hydra:member'].findIndex((element) => {
            if (element['@id'] === data.user['@id']) {
                return true;
            }
        });
        commit('changeUserProp', { place: index, key: data.prop, value: data.val });
    },
};

const mutations = {
    setUsers(state, payload) {
        state.users = payload;
    },
    setUserCollection(state, users) {
        state.users['hydra:member'] = users;
    },
    addUserToCollection(state, user) {
        state.users['hydra:member'].push(user);
    },
    updateUser(state, { key, value }) {
        state.user[key] = value;
    },
    resetUserForm(state) {
        Object.assign(state.user, getDefaultState().user);
    },
    changeUserProp(state, { place, key, value }) {
        state.users['hydra:member'][place][key] = value;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

import Axios from 'axios';
import { uuid } from 'vue-uuid';
import { calculateURating, findIndex } from '@/components/helper';

const getDefaultState = () => ({
    users: [],
    user: {
        uuid: uuid.v1(),
        name: '',
        email: '',
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
        roles: [state.user.roles],
        password: state.user.password,
    }),
    getUsers: (state) => state.users.map((user) => ({
        '@id': user['@id'],
        '@type': user['@type'],
        id: user.id, //todo??
        createdAt: user.createdAt,
        birthday: user.birthday,
        uRating: calculateURating(user.birthday, ''),
        name: user.name,
        email: user.email,
        phone: user.phone,
        isMe: user.isMe,
        isEnabled: user.isEnabled,
        roles: user.roles,
        roleDescription: user.roleDescription,
        sections: user.sections.length > 0 ? user.sections.map((section) => (section.description)) : '',
        team: user.team ? user.team.shortname : null,
        gender: user.gender ? user.gender.description : null,
        trainerCode: user.trainerCode,
        // contactname: team.contactname ? team.contactname : '',
        _showDetails: false,
        edit: {
            '@id': user['@id'],
            isEnabled: user.isEnabled,
            roles: user.roles,
            roleDescription: user.roleDescription,
        },
    })),
};

const actions = {
    async loadUsers({ commit }) {
        await Axios.get('/api/users').then((res) => {
            commit('setUsers', res.data['hydra:member']);
        });
    },
    post({ commit, getters }) {
        Axios.post('/api/users', getters.getUserForRegistration).then((res) => {
            commit('addUser', res.data);
        });
        commit('resetUserForm');
    },
    async delete({ commit, state }, user) {
        await Axios.delete(user['@id']).then(() => {
            const cleanedUsers = state.users.filter((item) => item['@id'] !== user['@id']);
            commit('setUserCollection', cleanedUsers);
        });
    },
    async put({ commit, state }, user) {
        Axios.put(user['@id'], user).then((editedUser) => {
            const newUser = editedUser.data;
            commit('editUser', { index: findIndex(state.users, user), data: newUser });
        });
    },
};

const mutations = {
    setUsers(state, payload) {
        state.users = payload;
    },
    setUserCollection(state, users) {
        state.users = users;
    },
    addUser(state, user) {
        state.users.unshift(user);
    },
    updateUser(state, { key, value }) {
        state.user[key] = value;
    },
    editUser(state, { index, data }) {
        //sajna az egész object-et nem lehet csak úgy kicserélni, mert nem megy át a commit rendesen
        //todo https://stackoverflow.com/questions/57169975/vuex-does-not-commit-changes-until-its-committed-manually-via-dev-tools
        state.users[index].isEnabled = data.isEnabled;
        state.users[index].roles = data.roles;
        state.users[index].roleDescription = data.roleDescription;
        state.users[index].trainerCode = data.trainerCode;
    },
    resetUserForm(state) {
        Object.assign(state.user, getDefaultState().user);
    },

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

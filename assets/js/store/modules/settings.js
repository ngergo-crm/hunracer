import Axios from 'axios';
import moment from 'moment/moment';
import { getSetting } from '@/components/helper';

const state = () => ({
    allSettings: [],
    workoutYearStart: '',
});

const getters = {
    getWorkoutYearStart: (state) => (
        state.workoutYearStart
    ),
};

const actions = {
    async initializeSettings({ commit, state }) {
        await Axios.get('/api/configs').then((res) => {
            commit('setAllSettings', res.data['hydra:member']);
            commit('setWorkoutYearStart', getSetting(res.data['hydra:member'], 'workoutYearStart'));
        });
    },
    saveSettings({ commit, state }) {
        const param = {
            settingValue: state.workoutYearStart.settingValue,
        };
        Axios.put(state.workoutYearStart['@id'], param);
    },
    getSetting(source, key) {
        return source.find((config) => config.settingKey === key);
    },
};

const mutations = {
    setAllSettings(state, payload) {
        state.allSettings = payload;
    },
    setWorkoutYearStart(state, payload) {
        state.workoutYearStart = payload;
    },
    updateSettingValue(state, { key, value }) {
        state[key].settingValue = value;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

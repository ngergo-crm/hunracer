import Axios from 'axios';
import moment from 'moment/moment';
import { fetchMetricsCompare } from '@/components/metricsCompare-service';

const state = () => ({
    user: {},
    selectedAthletes: [],
    athleteMetrics: [],
    metricTypes: [],
});

const getters = {
    // getTest: (state) => (),
};

const actions = {
    async initializeMetricCompare({ commit, state }) {
        const response = await fetchMetricsCompare();
        commit('setUser', response.data.user);
        await Axios.get('/api/metric_types').then((res) => {
            commit('setMetricTypes', res.data['hydra:member']);
        });
    },
    async getAthleteMetrics({ state, commit }, { athletes = null }) {
        if (athletes !== null) {
            commit('setSelectedAthletes', athletes);
        } else {
            athletes = state.selectedAthletes;
        }
        // const params = [];
        // athletes.forEach((athlete) => {
        //     const userId = `/api/users/${athlete.id}`;
        //     params.push(userId);
        // });
        //
        // await Axios.get('/api/metric_records', {
        //     params: {
        //         user: params,
        //     },
        // });
        // const athleteIds = [];
        // athletes.forEach((athlete) => {
        //     athleteIds.push(athlete.id);
        // });
        // const param = new URLSearchParams();
        // param.append('athleteIds', JSON.stringify(athleteIds));
        //
        // await Axios.get('/api/metric_records', {
        //     params: {
        //         // user.uuid: athleteIds
        //     },
        // }).then((res) => {
        //     commit('setAthleteMetrics', res.data['hydra:member']);
        // });
    },
};

const mutations = {
    setUser(state, payload) {
        state.user = payload;
    },
    setMetricTypes(state, payload) {
        state.metricTypes = payload;
    },
    setSelectedAthletes(state, payload) {
        state.selectedAthletes = payload;
    },
    setAthleteMetrics(state, payload) {
        state.athleteMetrics = payload;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

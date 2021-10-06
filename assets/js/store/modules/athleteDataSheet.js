import Axios from 'axios';
import moment from 'moment/moment';
import { fetchDataSheetData } from '@/components/datasheet-service';
import { containsObject, getMetricTypeDescription } from '@/components/helper';

const state = () => ({
    athlete: {},
    // trainerCode: '',
    metricTypes: [],
    selectedMetricTypes: {},
    team: {},
    metricRecords: [],
    datasheetDates: [],
    selectedDatasheet: [],
});

const getters = {
    getDatasheetOptions: (state) => state.datasheetDates.map((date) => ({
        value: date.date, //.format('YYYY-MM-DD'),
        text: moment(date.date).format('YYYY. MM. DD.'),
    })),
};

const actions = {
    async initializeDataSheet({ commit, state }) {
        const response = await fetchDataSheetData();
        commit('setAthlete', response.data.athlete);
        Axios.get(state.athlete.team).then((res) => {
            commit('setTeam', res.data);
        });
        await Axios.get('/api/metric_types').then((res) => {
            commit('setMetricTypes', res.data['hydra:member']);
        });
        const param = new URLSearchParams();
        param.append('athlete', state.athlete.id);
        await Axios.post('/getMetricDatasheetDates', param).then((res) => {
            commit('setDatasheetDates', res.data);
        });
        await Axios.get('/api/metric_records', {
            params: {
                user: `/api/users/${state.athlete.id}`,
            },
        }).then((res) => {
            commit('setMetricRecords', res.data['hydra:member']);
        });
    },
    async loadDatasheet({ commit, state }, { date }) {
        await Axios.get('/api/metric_records', {
            params: {
                user: `/api/users/${state.athlete.id}`,
                metricCreatedAt: date,
            },
        }).then((res) => {
            commit('setSelectedDatasheet', res.data['hydra:member']);
        });
    },
    async saveDatasheet({ commit, state }, { date, metrics }) {
        const param = new URLSearchParams();
        param.append('athlete', state.athlete.id);
        param.append('metrics', JSON.stringify(metrics));
        param.append('date', date);
        await Axios.post('/saveDatasheet', param).then(() => {
            if (!containsObject(state.datasheetDates, 'date', date)) {
                commit('addDatasheetDate', date);
            }
        });
    },
    async addMetricRecord({ commit, state }, { metric, date }) {
        let axisYType = null;
        if (state.selectedMetricTypes.Y !== null) {
            axisYType = state.selectedMetricTypes.Y['@id'];
        }
        const param = {
            user: `/api/users/${state.athlete.id}`,
            axisXType: state.selectedMetricTypes.X['@id'],
            axisYType,
            data: metric,
            metricCreatedAt: date,
        };
        Axios.post('/api/metric_records', param).then((res) => {
            commit('updateRecords', { payload: res.data, action: 'add' });
        });
    },
    async deleteMetrics({ commit, state }, { selectedData }) {
        const data = [];
        selectedData.forEach((record) => {
            data.push(record.id);
        });
        const params = new URLSearchParams();
        params.append('metricRecords', JSON.stringify(data));
        await Axios.post('/removeMetricRecords', params).then(() => {
            commit('updateRecords', { payload: selectedData, action: 'delete' });
        });
    },
    async selectMetricType({ commit, state }, { data }) {
        commit('setSelectedMetricTypes', data);
    },
};

const mutations = {
    setSelectedDatasheet(state, payload) {
        state.selectedDatasheet = payload;
    },
    setDatasheetDates(state, payload) {
        state.datasheetDates = payload;
    },
    addDatasheetDate(state, payload) {
        state.datasheetDates.push({ date: payload });
        state.datasheetDates.sort((a, b) => moment(b.date, 'YYYY-MM-DD').toDate() - moment(a.date, 'YYYY-MM-DD').toDate());
    },
    setAthlete(state, payload) {
        state.athlete = payload;
    },
    setTeam(state, payload) {
        state.team = payload;
    },
    setMetricTypes(state, payload) {
        state.metricTypes = payload;
    },
    setMetricRecords(state, payload) {
        const metricRecords = payload;
        let offsetName;
        state.metricTypes.forEach((type, key) => {
            if (!type.axis) {
                offsetName = type.description;
                state.metricRecords.push([{
                    [offsetName]: {
                        X: type,
                        Y: null,
                        records: metricRecords.filter((record) => record.axisXType['@id'] === type['@id']),
                    },
                }]);
            } else if (type.axis === 'X') {
                state.metricTypes.forEach((typeY) => {
                    if (typeY.axis === 'Y') {
                        offsetName = `${type.description}/ ${typeY.description}`;
                        state.metricRecords.push([{
                            [offsetName]: {
                                X: type,
                                Y: typeY,
                                records: metricRecords.filter((record) => record.axisXType['@id'] === type['@id'] && record.axisYType['@id'] === typeY['@id']),
                            },
                        }]);
                    }
                });
            }
        });
    },
    setSelectedMetricTypes(state, payload) {
        state.selectedMetricTypes = payload;
        // state.selectedMetricTypes.axisX = typeX;
        // state.selectedMetricTypes.axisY = typeY;
    },
    updateRecords(state, { payload, action }) {
        const description = getMetricTypeDescription(state.selectedMetricTypes);
        let index = 0;
        const N = state.metricRecords.length;
        while (index < N && !(Object.keys(state.metricRecords[index][0])[0] === description)) {
            index++;
        }
        if (action === 'add') {
            state.metricRecords[index][0][description].records.push(payload);
        } else if (action === 'delete') {
            payload.forEach((record) => {
                state.metricRecords[index][0][description].records.forEach((origRecord, key) => {
                    if (record.id === origRecord.id) {
                        state.metricRecords[index][0][description].records.splice(key, 1);
                    }
                });
            });
            state.selectedMetricTypes.records = state.metricRecords[index][0][description].records;
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

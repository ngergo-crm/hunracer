import Axios from 'axios';
import { fetchHomepageData } from '@/components/trainingPeaks-service';
import Moment from 'moment';
import { extendMoment } from 'moment-range';
import { getRange } from '@/components/helper';

const moment = extendMoment(Moment);

const DATE_FORMAT = 'Y-MM-DD';
const state = () => ({
    user: {},
    trainingPeaksLink: '',
    debug: process.env.APP_ENV !== 'prod',
    tokenAvailable: false,
    calendar: [],
    workoutWeek: [],
    showAllWorkouts: false,
    calendarFilter: 'tss',
    selectedTime: moment().format(DATE_FORMAT),
    workoutDays: [],
    autoRefresh: false,
    hasWorkouts: false,
    workoutWeekPeriod: [],
    workoutPeriodStart: '',
    workoutPeriodEnd: '',
    athletes: '',
    selectedAthlete: {},
});

const getters = {
    getAthleteSelection: (state) => state.athletes.map((athlete) => ({
        value: athlete.uuid,
        text: athlete.name,
    })),
};

const actions = {
    startImport() {
        Axios.post('/startImport');
    },
    async quickRefresh({ commit }, { autoRefresh = false }) {
        const params = new URLSearchParams();
        if (autoRefresh) {
            params.append('autoRefresh', 'true');
        }
        await Axios.post('/workout_refresh', params).then(() => {
            if (autoRefresh) {
                commit('setAutoRefresh', false);
            }
        });
    },
    async detailedRefresh({ commit, state }, { start, end }) {
        const params = new URLSearchParams();
        params.append('start', start);
        params.append('end', end);
        await Axios.post('/workout_refresh', params).then((res) => {
            if (!state.hasWorkouts && res.data.length > 0) {
                commit('setHasWorkouts', true);
            }
        });
    },
    async getTrainingPeaksLoginLink({ commit, state }) {
        const params = new URLSearchParams();
        params.append('test', state.debug);
        await Axios.post('/getTPLoginUrl', params).then((res) => {
            commit('setTrainingPeaksLink', res.data.tpLoginUrl);
        });
    },
    async checkAvailableToken({ state, commit }) {
        const response = await fetchHomepageData();
        commit('setTrainingPeaksTokenAvailable', response.data.tokenAvailable);
        commit('setUser', response.data.user);
        commit('setAutoRefresh', response.data.autoRefresh);
        commit('setHasWorkouts', response.data.hasWorkouts);
        commit('setAthletes', response.data.athletes);
        if (state.athletes.length > 0) {
            commit('setSelectedAthlete', response.data.athletes[0].uuid);
        }
    },
    async deauthorizeTpProfile({ commit }) {
        await Axios.post('/tpDeauthorize').then((res) => {
            commit('setTrainingPeaksTokenAvailable', res.data.tokenAvailable);
        });
    },
    async callTpApi({ commit }, { endpoint }) {
        const params = new URLSearchParams();
        params.append('endpoint', endpoint);
        const { data } = await Axios.post('/tpApiRequest', params);
        return data;
    },
    async getWorkoutWeek({ commit, state }, { time }) {
        let { id } = state.user;
        if (state.user.roleDescription === 'edző') {
            id = state.selectedAthlete.uuid;
        }
        const start = time.startOf('isoWeek').format(DATE_FORMAT);
        const end = time.endOf('isoWeek').format(DATE_FORMAT);
        await Axios.get('/api/workouts/', {
            params: {
                'workoutDay[after]': start,
                'workoutDay[before]': end,
                'user.uuid': id,
            },
        }).then((res) => {
            commit('setWorkoutWeek', res.data['hydra:member']);
        });
    },
    async getWorkoutPeriod({ commit, state }, { start, end }) {
        let { id } = state.user;
        if (state.user.roleDescription === 'edző') {
            id = state.selectedAthlete.uuid;
        }
        const endPeriod = end ? moment(end).format(DATE_FORMAT) : moment().format(DATE_FORMAT);
        const startPeriod = start ? moment(start).format(DATE_FORMAT) : moment(endPeriod).subtract(30, 'days').startOf('isoWeek').format(DATE_FORMAT);
        commit('setWorkoutPeriodStart', startPeriod);
        commit('setWorkoutPeriodEnd', endPeriod);
        await Axios.get('/api/workouts/', {
            params: {
                'workoutDay[after]': startPeriod,
                'workoutDay[before]': endPeriod,
                'user.uuid': id,
            },
        }).then((res) => {
            commit('setWorkoutWeekPeriod', res.data['hydra:member']);
        });
    },
    async getWorkoutDays({ commit, state }, { time }) {
        let { id } = state.user;
        if (state.user.roleDescription === 'edző') {
            id = state.selectedAthlete.uuid;
        }
        commit('setSelectedTime', time);
        const start = moment(time).startOf('month').format(DATE_FORMAT);
        const end = moment(time).endOf('month').format(DATE_FORMAT);
        await Axios.get('/api/workouts/workoutdays/', {
            params: {
                'workoutDay[after]': start,
                'workoutDay[before]': end,
                user: id,
            },
        }).then((res) => {
            commit('setWorkoutDays', res.data['hydra:member']);
        });
    },
    /** EZ AZ ADATOK IMPORTJA - ÁT KELL SZERVEZNI!!! */
    async loadCalendar({ getters, dispatch, commit }, { start, end, athleteId = null }) {
        let endpoint;
        if (!athleteId) {
            endpoint = `v1/workouts/${start}/${end}`;
        } else {
            endpoint = `v1/workouts/${athleteId}/${start}/${end}`;
        }
        const weeks = getRange(start, end, 'days', DATE_FORMAT);
        dispatch('callTpApi', { endpoint }).then((res) => {
            const workouts = res.response;
            if (workouts) {
                for (let i = 0; i < workouts.length; i++) {
                    const WorkoutDay = moment(workouts[i].WorkoutDay).format(DATE_FORMAT);
                    for (let j = 0; j < weeks.length; j++) {
                        for (let k = 0; k < weeks[j].days.length; k++) {
                            const DayOfWeek = moment(weeks[j].days[k].date).format(DATE_FORMAT);
                            if (WorkoutDay === DayOfWeek) {
                                weeks[j].days[k].workouts.push(workouts[i]);
                            }
                            //console.log(weeks[j].days[k].date);
                        }
                    }
                }
                commit('setCalendar', weeks);
            }
        });
    },

    async refreshHasWorkouts({ state, commit }) {
        await Axios.get('/api/workouts/', {
            params: {
                'user.uuid': state.selectedAthlete.uuid,
            },
        }).then((res) => {
            commit('setHasWorkouts', Boolean(res.data['hydra:member'].length));
        });
    },
};

const mutations = {
    setTrainingPeaksLink(state, payload) {
        state.trainingPeaksLink = payload;
    },
    setTrainingPeaksTokenAvailable(state, payload) {
        state.tokenAvailable = Boolean(payload);
    },
    setCalendar(state, payload) {
        state.calendar = payload;
    },
    setWorkoutWeek(state, payload) {
        state.workoutWeek = payload;
    },
    setShowAllWorkouts(state, payload) {
        state.showAllWorkouts = payload;
    },
    setCalendarFilter(state, payload) {
        state.calendarFilter = payload;
    },
    setUser(state, payload) {
        state.user = payload;
    },
    setSelectedTime(state, payload) {
        state.selectedTime = payload;
    },
    setWorkoutDays(state, payload) {
        const days = [];
        payload.forEach((entry) => {
            days.push(moment(entry.workoutDay).format(DATE_FORMAT));
        });
        state.workoutDays = days;
    },
    setAutoRefresh(state, payload) {
        state.autoRefresh = payload;
    },
    setHasWorkouts(state, payload) {
        state.hasWorkouts = payload;
    },
    setWorkoutWeekPeriod(state, payload) {
        state.workoutWeekPeriod = payload;
    },
    setWorkoutPeriodStart(state, payload) {
        state.workoutPeriodStart = payload;
    },
    setWorkoutPeriodEnd(state, payload) {
        state.workoutPeriodEnd = payload;
    },
    setAthletes(state, payload) {
        state.athletes = payload;
    },
    setSelectedAthlete(state, payload) {
        state.selectedAthlete = state.athletes.filter(({ uuid }) => uuid === payload)[0];
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

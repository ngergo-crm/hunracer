import Axios from 'axios';
import { calculateURating } from '@/components/helper';
import moment from 'moment';

const state = () => ({
    uRatingFilters: null,
    teamFilters: [],
    sectionFilters: [],
    genderFilters: null,
    uRatings: [],
    sections: [],
    teams: [],
    athletes: [],
    genders: [],
    filters: {
        uRating: [],
        team: [],
        section: [],
        gender: [],
    },
    trainers: [],
    trainerFilters: [],
    athletePerformance: [],
    selectedAthletes: [],
    performanceExpectations: [],
});

const getters = {
    getFilters: (state) => {
        const init = state.teamFilters.length > 0 || state.trainerFilters.length > 0 || state.sectionFilters.length > 0 || state.uRatingFilters || state.genderFilters ? {} : null;
        if (init) {
            if (state.genderFilters) {
                init.gender = state.genderFilters;
            }
            if (state.uRatingFilters) {
                init.uRating = state.uRatingFilters;
            }
            if (state.teamFilters.length > 0) {
                init.team = state.teamFilters;
            }
            if (state.trainerFilters.length > 0) {
                init.trainerCode = state.trainerFilters;
            }
            if (state.sectionFilters.length > 0) {
                init.sections = state.sectionFilters;
            }
        }
        return init;
    },
    getAthletes: (state) => state.athletes.map((user) => ({
        '@id': user['@id'],
        '@type': user['@type'],
        id: user.id,
        // createdAt: user.createdAt,
        birthday: user.birthday,
        uRating: calculateURating(user.birthday, ''),
        name: user.name,
        sections: user.sections.length > 0 ? user.sections.map((section) => (section.description)) : '',
        team: user.team ? user.team.shortname : null,
        gender: user.gender ? user.gender.description : null,
        trainerCode: user.trainerCode,
    })),
};

const actions = {
    async initializeFilters({ commit }) {
        Axios.get('/api/sections').then((res) => {
            commit('setSections', res.data['hydra:member']);
        });
        Axios.get('/api/teams').then((res) => {
            commit('setTeams', res.data['hydra:member']);
        });
        Axios.get('/api/genders').then((res) => {
            commit('setGenders', res.data['hydra:member']);
        });
        Axios.get('/api/u_ratings').then((res) => {
            commit('setUratings', res.data['hydra:member']);
        });
        Axios.get('/api/performances').then((res) => {
            commit('setPerformanceExpectations', res.data['hydra:member']);
        });
        Axios.get('/api/users', {
            params: {
                isEnabled: 1,
                roles: '["ROLE_TRAINER"]',
            },
        }).then((res) => {
            commit('setTrainers', res.data['hydra:member']);
        });
        Axios.get('/api/users', {
            params: {
                isEnabled: 1,
                roles: '["ROLE_USER"]',
            },
        }).then((res) => {
            commit('setAthletes', res.data['hydra:member']);
            // commit('setSelectedAthletes', res.data['hydra:member']);
        });
    },
    async getAthletePerformance({ state, commit }, { athletes = null, year = null }) {
        if (year === null) {
            year = moment().format('YYYY');
        }
        if (athletes !== null) {
            commit('setSelectedAthletes', athletes);
        } else {
            athletes = state.selectedAthletes;
        }
        const athleteIds = [];
        athletes.forEach((athlete) => {
            athleteIds.push(athlete.id);
        });
        const param = new URLSearchParams();
        param.append('year', year);
        param.append('athleteIds', JSON.stringify(athleteIds));
        await Axios.post('/getAthletePerformance', param).then((res) => {
            commit('setAthletePerformance', res.data);
        });
    },
    // manageSelectedAthletesArray({ commit, state }, athlete) {
    //     const found = state.selectedAthletes.filter((selected) => selected['@id'] === athlete['@id']);
    //     commit('manageSelectedAthletes', { athlete, action: !(found.length > 0) });
    // },
    // async edit({ commit, state }, team) {
    //     Axios.put(team['@id'], team).then(() => {
    //         commit('editTeam', { index: findIndex(state.teams, team), data: team });
    //     });
    // },
    // async delete({ commit, state }, team) {
    //     await Axios.delete(team['@id']).then(() => {
    //         commit('deleteTeam', findIndex(state.teams, team));
    //     });
    // },
    // async create({ commit, state }, newTeam) {
    //     await Axios.post('/api/teams', newTeam).then((res) => {
    //         commit('addTeam', res.data);
    //     });
    // },
};

const mutations = {
    setURatingFilters(state, payload) {
        state.uRatingFilters = payload;
    },
    setSectionFilters(state, payload) {
        state.sectionFilters = payload;
    },
    setGenderFilters(state, payload) {
        state.genderFilters = payload;
    },
    setTeamFilters(state, payload) {
        state.teamFilters = payload;
    },
    setSections(state, payload) {
        state.sections = payload;
    },
    setTeams(state, payload) {
        state.teams = payload;
    },
    setAthletes(state, payload) {
        state.athletes = payload;
    },
    setGenders(state, payload) {
        state.genders = payload;
    },
    setUratings(state, payload) {
        state.uRatings = payload;
    },
    setTrainers(state, payload) {
        state.trainers = payload;
    },
    setTrainerFilters(state, payload) {
        state.trainerFilters = payload;
    },
    setAthletePerformance(state, payload) {
        state.athletePerformance = payload;
    },
    setSelectedAthletes(state, payload) {
        state.selectedAthletes = payload;
    },
    setPerformanceExpectations(state, payload) {
        state.performanceExpectations = payload;
    },
    // setSelectedAthletes(state, payload) {
    //     if (payload === 'all') {
    //         state.selectedAthletes = state.athletes;
    //     } else {
    //         state.selectedAthletes = payload;
    //     }
    // },
    // manageSelectedAthletes(state, { athlete, action }) {
    //     const within = containsObject(state.selectedAthletes, '@id', athlete['@id']);
    //     //console.log('action;', action, 'withion;', within);
    //     if (action && !within) {
    //         //add and not in the array
    //         state.selectedAthletes.push(athlete);
    //     } else if (!action && within) {
    //         //delete object from array
    //         console.log('delete');
    //         const index = state.selectedAthletes.indexOf(state.selectedAthletes.filter((item) => item['@id'] === athlete['@id'])[0]);
    //         state.selectedAthletes.splice(index, 1);
    //     }
    // },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

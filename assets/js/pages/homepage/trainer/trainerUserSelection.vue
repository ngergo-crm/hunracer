<template>
    <div>
        <div
            v-if="athletes.length > 0"
            class="atheteSelect"
        >
            <div>
                <b-form-select
                    v-model="selectedAthlete"
                    :options="athletes"
                    @change="changeAthlete"
                />
            </div>
            <div style="align-self: center;">
                <b-link
                    target="_blank"
                    :href="'/adatlap/'+selectedAthlete"
                    style="margin-left: 2rem;"
                >
                    >>> Tovább az adatlaphoz
                </b-link>
            </div>
        </div>
        <no-athletes-component v-else />
        <div
            v-if="refreshmentAvailable && tokenAvailable"
            class="trainerRefreshment"
        >
            <trainer-refresh
                :has-workouts="refreshmentAvailable"
            />
        </div>
        <div
            v-if="athletes.length > 0 && !hasWorkouts"
            class="noWorkoutsForUser"
        >
            A kiválasztott sportolónak még nincsenek edzésadatai a Hunracer rendszerében.
        </div>
    </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex';
import moment from 'moment';
import NoAthletesComponent from '@/pages/homepage/trainer/noAthletes';
import TrainerRefresh from '@/pages/homepage/trainer/trainer-refresh';
import { containsObject } from '@/components/helper';

export default {
    name: 'TrainerUserSelection',
    components: { TrainerRefresh, NoAthletesComponent },
    data() {
        return {
            //selected: this.athletes[0]['value'],
        };
    },
    computed: {
        ...mapGetters({
            athletes: 'trainingPeaksHandler/getAthleteSelection',
        }),
        ...mapState({
            tokenAvailable: (state) => state.trainingPeaksHandler.tokenAvailable,
            athleteList: (state) => state.trainingPeaksHandler.athletes,
            selectedTime: (state) => state.trainingPeaksHandler.selectedTime,
            workoutPeriodStart: (state) => state.trainingPeaksHandler.workoutPeriodStart,
            workoutPeriodEnd: (state) => state.trainingPeaksHandler.workoutPeriodEnd,
            hasWorkouts: (state) => state.trainingPeaksHandler.hasWorkouts,
            coachAthletes: (state) => state.trainingPeaksHandler.coachAthletes,
        }),
        selectedAthlete: {
            get() {
                return this.$store.state.trainingPeaksHandler.selectedAthlete.uuid;
            },
            set(value) {
                this.$store.commit('trainingPeaksHandler/setSelectedAthlete', value);
            },
        },
        refreshmentAvailable() {
            let refreshmentAvailable = false;
            const athleteObject = this.athleteList.filter(({ uuid }) => uuid === this.selectedAthlete)[0];
            if (athleteObject.tpUserId) {
                refreshmentAvailable = containsObject(this.coachAthletes, 'Id', parseInt(athleteObject.tpUserId));
            }
            return refreshmentAvailable;
        },
    },
    methods: {
        changeAthlete() {
            if (this.hasWorkouts) {
                this.$root.$emit('resetChartData');
                this.$root.$emit('resetChartDataComprassion');
            }
            this.$store.dispatch('trainingPeaksHandler/refreshHasWorkouts').then(() => {
                this.$store.commit('trainingPeaksHandler/setCalendar', []);
                this.$store.commit('trainingPeaksHandler/setWorkoutDays', []);
                this.$store.commit('trainingPeaksHandler/setWorkoutWeek', []);
                this.$store.commit('trainingPeaksHandler/setWorkoutWeekPeriod', []);
                if (this.hasWorkouts) {
                    this.$root.$emit('getWorkoutWeek', moment(this.selectedTime));
                    this.$root.$emit('getWorkoutPeriod', { start: this.workoutPeriodStart, end: this.workoutPeriodEnd });
                    this.$root.$emit('changeWorkoutDays', moment(this.selectedTime));
                }
            });
        },
    },
};
</script>

<style scoped>
.trainerRefreshment{
  padding-top: 1.5rem;
}
.atheteSelect {
  max-width: 100%;
  display: flex;
}
.noWorkoutsForUser {
  padding-top: 2rem;
  text-align: center;
}
</style>

<template>
    <div>
        <div
            v-if="athletes.length > 0"
            class="atheteSelect"
        >
            <b-form-select
                v-model="selectedAthlete"
                :options="athletes"
                @change="changeAthlete"
            />
        </div>
        <no-athletes-component v-else />
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

export default {
    name: 'TrainerUserSelection',
    components: { NoAthletesComponent },
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
            athleteList: (state) => state.trainingPeaksHandler.athletes,
            selectedTime: (state) => state.trainingPeaksHandler.selectedTime,
            workoutPeriodStart: (state) => state.trainingPeaksHandler.workoutPeriodStart,
            workoutPeriodEnd: (state) => state.trainingPeaksHandler.workoutPeriodEnd,
            hasWorkouts: (state) => state.trainingPeaksHandler.hasWorkouts,
        }),
        selectedAthlete: {
            get() {
                return this.$store.state.trainingPeaksHandler.selectedAthlete.uuid;
            },
            set(value) {
                this.$store.commit('trainingPeaksHandler/setSelectedAthlete', value);
            },
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
.atheteSelect {
  max-width: 20%;
}
.noWorkoutsForUser {
  padding-top: 2rem;
  text-align: center;
}
</style>

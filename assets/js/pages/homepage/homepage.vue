<template>
    <div>
        <div class="contentpanel">
            <div class="contentcard">
                <!--                 v-if="user.roleDescription === 'sportoló'"-->
                <training-peaks-auth-component />
                <div
                    v-if="(user.roleDescription === 'edző' || user.roleDescription === 'admin' || user.roleDescription === 'szuperAdmin')"
                    class="trainerUserSelector"
                >
                    <div class="workoutweekBanner">
                        <h4>Edzéshetek elemzése</h4>
                    </div>
                    <trainer-user-selection-component />
                </div>
                <calendar-refresh-component v-if="tokenAvailable && user.roleDescription === 'sportoló'" />
                <calendar-component
                    v-show="hasWorkouts"
                />
                <admin-panel v-if="user.roleDescription === 'admin' || user.roleDescription === 'szuperAdmin'" />
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import CalendarComponent from '@/pages/homepage/calendar/calendar';
import moment from 'moment';
import CalendarRefreshComponent from '@/pages/homepage/calendar/calendar-refresh';
import TrainingPeaksAuthComponent from '@/pages/homepage/trainingPeaksAuth';
import TrainerUserSelectionComponent from '@/pages/homepage/trainer/trainerUserSelection';
import AdminPanel from '@/pages/homepage/admin/adminPanel';

export default {
    name: 'Homepage',
    components: {
        AdminPanel,
        TrainerUserSelectionComponent,
        TrainingPeaksAuthComponent,
        CalendarRefreshComponent,
        CalendarComponent,
    },
    computed: {
        ...mapState({
            tokenAvailable: (state) => state.trainingPeaksHandler.tokenAvailable,
            user: (state) => state.trainingPeaksHandler.user,
            autoRefresh: (state) => state.trainingPeaksHandler.autoRefresh,
            hasWorkouts: (state) => state.trainingPeaksHandler.hasWorkouts,
        }),
    },
    mounted() {
        this.$root.$on('getWorkoutWeek', (time) => {
            this.getWorkoutWeek(time);
        });
        this.$root.$on('getWorkoutPeriod', ({ start, end }) => {
            this.getWorkoutPeriod(start, end, true);
        });
        // this.$root.$on('getWorkoutPeriod2', ({ start, end }) => {
        //     console.log(start, end);
        //     this.getWorkoutPeriod2(start, end, true);
        // });
    },
    created() {
        this.$store.dispatch('trainingPeaksHandler/checkAvailableToken').then(() => {
            if (!this.tokenAvailable) {
                this.$store.dispatch('trainingPeaksHandler/getTrainingPeaksLoginLink');
            } else if (this.user.roleDescription === 'sportoló' && this.tokenAvailable && this.autoRefresh && this.hasWorkouts) {
                this.$store.dispatch('trainingPeaksHandler/quickRefresh', { autoRefresh: this.autoRefresh });
            } else if (this.user.roleDescription !== 'sportoló' && this.tokenAvailable) {
                this.$store.dispatch('trainingPeaksHandler/getCoachAthletes');
            }
            if (this.hasWorkouts) {
                this.getWorkoutWeek(moment());
                this.getWorkoutPeriod(null, null, true);
            }
        });
    },
    methods: {
        getCalendar() {
            this.$store.dispatch('trainingPeaksHandler/loadCalendar', { start: null, end: null });
        },
        /** time: moment() object */
        async getWorkoutWeek(time) {
            if (time === null) {
                time = moment();
            }
            await this.$store.dispatch('trainingPeaksHandler/getWorkoutWeek', { time }).then(() => {
                this.$root.$emit('reloadCalendar');
            });
        },
        async getWorkoutPeriod(start, end, init = false) {
            await this.$store.dispatch('trainingPeaksHandler/getWorkoutPeriod', { start, end }).then(() => {
                if (init) {
                    this.$root.$emit('reloadWorkoutComprassion');
                }
            });
        },
        async getWorkoutPeriod2(start, end, init = false) {
            await this.$store.dispatch('trainingPeaksHandler/getWorkoutPeriod', { start, end }).then(() => {
                if (init) {
                    console.log('meg lett hivva');
                    this.$root.$emit('reloadCalendar3');
                }
            });
        },
    },
};
</script>

<style scoped>
.trainerUserSelector{
  padding-top: 2rem;
}
.workoutweekBanner{
  padding-bottom: 1rem;
}
</style>

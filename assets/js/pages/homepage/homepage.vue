<template>
    <div>
        <div class="contentpanel">
            <div class="contentcard">
                <div
                    v-if="!tokenAvailable"
                    class="loginTP"
                >
                    <b-button

                        variant="primary"
                        @click="loginTP"
                    >
                        Training Peaks bejelentkezés
                    </b-button>
                </div>
                <div
                    v-else
                    class="tpauth"
                >
                    <b-button
                        @click="deauthorizeTpProfile"
                    >
                        Training Peaks kijelentkezés
                    </b-button>
                </div>

                <calendar-refresh-component v-if="tokenAvailable" />
                <calendar-component v-if="hasWorkouts" />
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import CalendarComponent from '@/pages/homepage/calendar/calendar';
import moment from 'moment';
import CalendarRefreshComponent from '@/pages/homepage/calendar/calendar-refresh';

export default {
    name: 'Homepage',
    components: { CalendarRefreshComponent, CalendarComponent },
    computed: {
        ...mapState({
            tokenAvailable: (state) => state.trainingPeaksHandler.tokenAvailable,
            trainingPeaksLink: (state) => state.trainingPeaksHandler.trainingPeaksLink,
            autoRefresh: (state) => state.trainingPeaksHandler.autoRefresh,
            hasWorkouts: (state) => state.trainingPeaksHandler.hasWorkouts,
        }),
    },
    mounted() {
        this.$root.$on('getWorkoutWeek', (time) => {
            this.getWorkoutWeek(time);
        });
    },
    created() {
        this.$store.dispatch('trainingPeaksHandler/checkAvailableToken').then(() => {
            if (!this.tokenAvailable) {
                this.$store.dispatch('trainingPeaksHandler/getTrainingPeaksLoginLink');
            } else if (this.tokenAvailable && this.autoRefresh && this.hasWorkouts) {
                this.$store.dispatch('trainingPeaksHandler/quickRefresh', { autoRefresh: this.autoRefresh });
            }
            if (this.hasWorkouts) {
                this.getWorkoutWeek(moment());
            }
        });
    },
    methods: {
        loginTP() {
            window.open(this.trainingPeaksLink, '_blank');
        },
        deauthorizeTpProfile() {
            this.$store.dispatch('trainingPeaksHandler/deauthorizeTpProfile').then(() => {
                this.$store.dispatch('trainingPeaksHandler/getTrainingPeaksLoginLink');
            });
        },
        getCalendar() {
            this.$store.dispatch('trainingPeaksHandler/loadCalendar', { start: null, end: null });
        },
        /** time: moment() object */
        async getWorkoutWeek(time) {
            await this.$store.dispatch('trainingPeaksHandler/getWorkoutWeek', { time }).then(() => {
                this.$root.$emit('reloadCalendar');
            });
        },
    },
};
</script>

<style scoped>
.tpauth {
  display: flex;
  justify-content: flex-start;
}
.loginTP {
  display: flex;
  justify-content: flex-start;
}
</style>

<template>
    <div class="refresh">
        <div class="detailedrefresh">
            <refresh-range-component />
        </div>
        <div
            v-if="hasWorkouts"
            class="quickrefresh"
        >
            <b-button
                pill
                variant="primary"
                style="width: 8rem;"
                @click="quickRefresh"
            >
                <span v-if="!quickRefreshWorking">Gyorsfrissítés</span>
                <b-spinner
                    v-else-if="quickRefreshWorking"
                    small
                />
            </b-button>
        </div>
    </div>
</template>

<script>
import RefreshRangeComponent from '@/pages/homepage/calendar/calendar-refresh/refresh-range';
import { mapState } from 'vuex';
import moment from 'moment';

export default {
    name: 'CalendarRefresh',
    components: { RefreshRangeComponent },
    data() {
        return {
            quickRefreshWorking: false,
        };
    },
    computed: {
        ...mapState({
            selectedTime: (state) => state.trainingPeaksHandler.selectedTime,
            hasWorkouts: (state) => state.trainingPeaksHandler.hasWorkouts,
        }),
    },
    methods: {
        quickRefresh() {
            this.quickRefreshWorking = true;
            this.$store.dispatch('trainingPeaksHandler/quickRefresh', { autoRefresh: false }).then(() => {
                this.$root.$emit('getWorkoutWeek', moment(this.selectedTime));
                this.quickRefreshWorking = false;
            });
        },
    },
};
</script>

<style scoped>
.refresh{
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
  margin-bottom: 1rem;
  align-items: first baseline;
}
</style>

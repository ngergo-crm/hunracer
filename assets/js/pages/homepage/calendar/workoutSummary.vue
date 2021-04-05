<template>
    <div>
        <h4>
            <b-badge variant="primary">
                {{ summary }} {{ measurement }}
            </b-badge>
        </h4>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    name: 'WorkoutSummary',
    computed: {
        ...mapState({
            workoutWeek: (state) => state.trainingPeaksHandler.workoutWeek,
            showAllWorkouts: (state) => state.trainingPeaksHandler.showAllWorkouts,
            calendarFilter: (state) => state.trainingPeaksHandler.calendarFilter,
        }),
        summary() {
            let count = 0;
            this.workoutWeek.forEach((workout) => {
                if (!this.showAllWorkouts) {
                    if ((workout.type === 'Bike' || workout.type === 'MTB')) {
                        count += workout[this.calendarFilter];
                    }
                } else {
                    count += workout[this.calendarFilter];
                }
            });
            if (this.calendarFilter === 'distance') {
                count /= 1000;
            }
            if (!Number.isInteger(count)) {
                count = count.toFixed(2);
            }
            return count;
        },
        measurement() {
            let measurement = '';
            if (this.calendarFilter === 'distance') {
                measurement = 'km';
            } else if (this.calendarFilter === 'elevation') {
                measurement = 'm';
            } else if (this.calendarFilter === 'totalTime') {
                measurement = 'h';
            } else if (this.calendarFilter === 'energy') {
                measurement = 'kJ';
            } else if (this.calendarFilter === 'tss') {
                measurement = 'tss';
            }
            return measurement;
        },
    },
};
</script>

<style scoped>

</style>

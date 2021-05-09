<template>
    <div>
        <h4>
            <b-badge variant="primary">
                {{ summary }}
            </b-badge>
        </h4>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import { getUnit } from '@/components/helper';

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
            count = `${count} ${getUnit(this.calendarFilter)}`;
            return count;
        },
    },
};
</script>

<style scoped>

</style>

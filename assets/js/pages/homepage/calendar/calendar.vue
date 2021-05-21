<template>
    <div>
        <workout-week-component />
        <workout-week-comprassion2 />
    </div>
</template>

<script>
import WorkoutWeekComponent from '@/pages/homepage/calendar/workoutWeek';
import WorkoutWeekComprassion2 from '@/pages/homepage/calendar/workoutWeekComprassion2';

export default {
    name: 'Calendar',
    components: { WorkoutWeekComprassion2, WorkoutWeekComponent },
};
</script>

<style scoped>

</style>

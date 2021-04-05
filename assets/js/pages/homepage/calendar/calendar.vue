<template>
    <!--        <button @click="startImport">-->
    <!--            Import data-->
    <!--        </button>-->
    <div>
        <div
            v-if="!(chartData.length > 0)"
            class="spinnerPlace"
        >
            <b-spinner
                class="calendarSpinner"
                label="Spinning"
                type="grow"
            />
        </div>
        <div
            v-else
            class="calendar-content"
        >
            <div class="calendar-filters">
                <div>
                    <b-input-group class="mb-3">
                        <b-input-group-append>
                            <b-form-datepicker
                                v-model="selectedDay"
                                label-help=""
                                label-prev-month="Előző hónap"
                                label-prev-year="Előző év"
                                label-next-month="Következő hónap"
                                label-next-year="Következő év"
                                label-current-month="Jelen hónap"
                                locale="hu"
                                :date-info-fn="dateClass"
                                :max="calendarMaxDate"
                                :initial-date="initialDate"
                                start-weekday="1"
                                :hide-header="true"
                                placeholder="Válassz edzéshetet"
                                button-only
                                aria-controls="example-input"
                                @context="onContext"
                                @input="changeWorkoutWeek"
                            />
                        </b-input-group-append>
                        <b-form-input
                            id="example-input"
                            v-model="selected"
                            type="text"
                            placeholder="Válassz edzéshetet"
                            autocomplete="off"
                            readonly
                        />
                    </b-input-group>
                </div>
                <div>
                    <b-form-radio-group
                        id="btn-radios-1"
                        v-model="selectedFilter"
                        :options="options"
                        name="radios-btn-default"
                        buttons
                        @change="changeFilter"
                    />
                </div>
                <div>
                    <b-form-checkbox
                        id="checkbox-1"
                        v-model="showAll"
                        name="checkbox-1"
                        @change="showAllWorkouts"
                    >
                        Minden edzést mutat
                    </b-form-checkbox>
                </div>
            </div>
            <div v-if="workoutWeek.length > 0 && calendarFilter">
                <workout-summary-component />
            </div>
            <div class="chart">
                <GChart
                    type="ColumnChart"
                    :data="chartData"
                    :options="chartOptions"
                />
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';
import WorkoutSummaryComponent from '@/pages/homepage/calendar/workoutSummary';

export default {
    name: 'Calendar',
    components: { WorkoutSummaryComponent },
    data() {
        return {
            // Array will be automatically processed with visualization.arrayToDataTable function
            selectedDay: moment().format('YYYY-MM-DD'),
            selected: '',
            showAll: false,
            selectedFilter: 'tss',
            options: [
                { text: 'TSS', value: 'tss' },
                { text: 'Táv', value: 'distance' },
                { text: 'Szintemelkedés', value: 'elevation' },
                { text: 'Idő', value: 'totalTime' },
                { text: 'Munka', value: 'energy' },
            ],
            chartData: [],
            chartOptions: {
                chart: {
                    title: 'Edzések',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                },
                isStacked: true,
            },
        };
    },
    computed: {
        ...mapState({
            workoutWeek: (state) => state.trainingPeaksHandler.workoutWeek,
            calendarFilter: (state) => state.trainingPeaksHandler.calendarFilter,
            workoutDays: (state) => state.trainingPeaksHandler.workoutDays,
        }),
        calendarMaxDate() {
            return moment().endOf('isoWeek').format('YYYY-MM-DD');
        },
        initialDate() {
            return moment().format('YYYY-MM-DD');
        },
    },
    mounted() {
        this.$root.$on('reloadCalendar', () => {
            this.reloadCalendar();
        });
    },
    created() {

    },
    methods: {
        startImport() {
            this.$store.dispatch('trainingPeaksHandler/startImport');
        },
        dateClass(ymd, date) {
            const day = moment(date).format('Y-MM-DD');
            return this.workoutDays.includes(day) ? 'table-info' : '';
        },
        onContext(ctx) {
            this.getWorkoutDays(ctx.activeYMD);
            const endofweek = moment(ctx.selectedYMD).endOf('isoWeek').format('YYYY.MM.DD');
            const startofweek = moment(ctx.selectedYMD).startOf('isoWeek').format('YYYY.MM.DD');
            this.selected = `${startofweek} - ${endofweek}`;
        },
        changeWorkoutWeek(time) {
            const timeObj = moment(time);
            this.$root.$emit('getWorkoutWeek', timeObj);
        },
        showAllWorkouts(result) {
            this.$store.commit('trainingPeaksHandler/setShowAllWorkouts', result);
            this.reloadCalendar();
        },
        changeFilter(filter) {
            this.$store.commit('trainingPeaksHandler/setCalendarFilter', filter);
            this.reloadCalendar();
        },
        reloadCalendar() {
            const { workoutWeek } = this;
            const chartData = [];
            const header = ['Napok'];
            //  if (this.showAll) {
            this.workoutWeek.forEach((workout) => {
                if (!this.showAll) {
                    if (!header.includes(workout.type) && (workout.type === 'Bike' || workout.type === 'MTB')) {
                        header.push(workout.type);
                    }
                } else if (!header.includes(workout.type)) {
                    header.push(workout.type);
                }
            });
            if (header.length === 1) {
                header.push('Bike');
            }
            chartData.push(header);
            for (let i = 1; i <= +7; i++) {
                const daycode = this.capitalizeFirstLetter(moment().day(i).locale('hu').format('dd'));
                const day = [daycode];
                for (let j = 1; j < header.length; j++) {
                    const workoutType = header[j];
                    let count = 0;
                    workoutWeek.forEach((workout) => {
                        const workoutDay = this.capitalizeFirstLetter(moment(workout.workoutDay).locale('hu').format('dd'));
                        if (workoutDay === daycode && workout.type === workoutType) {
                            count += workout[this.selectedFilter];
                        }
                    });
                    if (this.selectedFilter === 'distance') {
                        count /= 1000;
                    }
                    day.push(count);
                }
                chartData.push(day);
            }
            // console.log(chartData);
            // console.log(this.chartData);
            // console.log(this.workoutWeek);
            this.chartData = chartData;
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        getWorkoutDays(time) {
            this.$store.dispatch('trainingPeaksHandler/getWorkoutDays', { time });
        },
    },
};
</script>

<style scoped>
.calendar-filters{
  display: flex;
  justify-content: space-around;
  width: 68%;
}
.spinnerPlace {
  display: flex;
  justify-content: center;
}
.calendarSpinner {
  width: 3rem;
  height: 3rem;
}
.calendar-content {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-flow: column;
  padding-top: 2rem;
}
.chart {
  width: 70%;
}
.filterTwo{
  display: flex;
  justify-content: space-between;
}

</style>

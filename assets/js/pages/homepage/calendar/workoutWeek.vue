<template>
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
                    ref="workoutWeek"
                    type="ColumnChart"
                    :data="chartData"
                    :options="chartOptions"
                    :events="chartEvents"
                />
            </div>
        </div>
        <b-modal
            id="workoutDetailModal"
            :title="`Részletes adatok (${formattedWorkoutDay})`"
            scrollable
            size="md"
            hide-footer
        >
            <workout-details :workouts="selectedWorkouts" />
        </b-modal>
    </div>
</template>

<script>
import { calendarMaxDate, getUnit, initialDate } from '@/components/helper';
import moment from 'moment';
import { mapState } from 'vuex';
import WorkoutSummaryComponent from '@/pages/homepage/calendar/workoutSummary';
import WorkoutDetails from '@/pages/homepage/calendar/workoutDetails';

export default {
    name: 'WorkoutWeek',
    components: { WorkoutDetails, WorkoutSummaryComponent },
    data() {
        return {
            chartEvents: {
                select: () => {
                    this.showWorkoutDetails();
                },
            },
            selectedWorkouts: [],
            calendarMaxDate: calendarMaxDate(),
            initialDate: initialDate(),
            selectedDay: moment().format('YYYY-MM-DD'),
            selected: '',
            showAll: false,
            selectedFilter: 'tss',
            options: [
                { text: 'TSS', value: 'tss' },
                { text: 'Táv', value: 'distance' },
                { text: 'Szintemelkedés', value: 'elevation' },
                { text: 'Idő', value: 'totalTime' },
            ],
            chartData: [],
            chartOptions: {
                title: 'Az edzéshét összegzése',
                chart: {
                    title: 'Edzések',
                },
                isStacked: true,
                vAxes: {
                    0: { title: 'tss' },
                },
                tooltip: { isHtml: true },
            },

        };
    },
    computed: {
        ...mapState({
            workoutWeek: (state) => state.trainingPeaksHandler.workoutWeek,
            calendarFilter: (state) => state.trainingPeaksHandler.calendarFilter,
            workoutDays: (state) => state.trainingPeaksHandler.workoutDays,
            workoutWeekPeriod: (state) => state.trainingPeaksHandler.workoutWeekPeriod,
        }),
        formattedWorkoutDay() {
            return moment(this.selectedDay).locale('hu').format('YYYY. MM. DD., dddd');
        },
    },
    mounted() {
        this.$root.$on('reloadCalendar', () => {
            this.reloadCalendar();
        });
        this.$root.$on('changeWorkoutDays', (time) => {
            this.getWorkoutDays(time);
        });
        this.$root.$on('resetChartData', () => {
            this.chartData = [];
        });
    },
    methods: {
        showWorkoutDetails() {
            this.selectedWorkouts = [];
            const table = this.$refs.workoutWeek.chartObject;
            const selection = table.getSelection();
            const selectionIndex = selection.length !== 0 ? selection[0] : null;
            if (selectionIndex) {
                const selectedDay = this.chartData[selectionIndex.row + 1][0];
                this.workoutWeek.forEach((workout) => {
                    const workoutDay = this.capitalizeFirstLetter(moment(workout.workoutDay).locale('hu').format('dd'));
                    if (workoutDay === selectedDay) {
                        this.selectedWorkouts.push(workout);
                    }
                });
                this.$bvModal.show('workoutDetailModal');
                table.setSelection([]);
            }
            //alert(onSelectionMeaasge);
        },
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
            this.chartOptions.vAxes['0'].title = `${getUnit(filter, true)}`;
        },
        reloadCalendar() {
            const { workoutWeek } = this;
            const chartData = [];
            const header = ['Napok'];
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
.calendar-filters {
  display: flex;
  justify-content: space-around;
  width: 68%;
}
.chart {
  width: 70%;
}

</style>

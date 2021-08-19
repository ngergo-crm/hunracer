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
            class="calendar-period-content"
        >
            <div class="periodDateFilter">
                <b-form-group
                    label="Kezdődátum"
                    label-for="start-datepicker"
                >
                    <b-form-datepicker
                        id="start-datepicker"
                        v-model="start"
                        label-help=""
                        label-prev-month="Előző hónap"
                        label-prev-year="Előző év"
                        label-next-month="Következő hónap"
                        label-next-year="Következő év"
                        label-current-month="Jelen hónap"
                        locale="hu"
                        class="mb-2"
                        start-weekday="1"
                        :hide-header="true"
                        placeholder="Válassz edzéshetet"
                        :max="calendarStartMaxDate"
                        :state="formNotValid"
                        @input="validateDatepickers"
                    />
                </b-form-group>
                <b-form-group
                    label="Záródátum"
                    label-for="end-datepicker"
                >
                    <b-form-datepicker
                        id="end-datepicker"
                        v-model="end"
                        label-help=""
                        label-prev-month="Előző hónap"
                        label-prev-year="Előző év"
                        label-next-month="Következő hónap"
                        label-next-year="Következő év"
                        label-current-month="Jelen hónap"
                        locale="hu"
                        class="mb-2"
                        start-weekday="1"
                        :hide-header="true"
                        placeholder="Válassz edzéshetet"
                        :max="calendarEndMaxDate"
                        :state="formNotValid"
                        @input="validateDatepickers"
                    />
                </b-form-group>
                <div>
                    <b-button
                        variant="primary"
                        :disabled="!formNotValid"
                        @click="submitPeriodCalendar"
                    >
                        Újratölt
                    </b-button>
                </div>
            </div>
            <div class="calendar-period-type">
                <b-form-radio-group
                    id="btn-radios-1"
                    v-model="selectedFilter"
                    :options="options"
                    name="radios-btn-default"
                    buttons
                    @change="changeFilter"
                />
            </div>

            <div class="period-chart">
                <div id="periodChartPlace">
                    <GChart
                        type="ColumnChart"
                        :data="chartData"
                        :options="chartOptions"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import { getUnit, initialDate } from '@/components/helper';
import { mapState } from 'vuex';

export default {
    name: 'WorkoutWeekComprassion2',
    data() {
        return {
            calendarEndMaxDate: moment().format('YYYY-MM-DD'),
            start: moment(initialDate()).subtract(1, 'month').startOf('isoWeek').format('YYYY-MM-DD'),
            end: initialDate(),
            formNotValid: true,
            options: [
                { text: 'TSS', value: 'tss' },
                { text: 'Táv', value: 'distance' },
                { text: 'Szintemelkedés', value: 'elevation' },
                { text: 'Idő', value: 'totalTime' },
            ],
            selectedFilter: 'tss',
            chartData: [],
            loading: false,
            chartOptions: {
                legend: 'none',
                // height: 450,
                title: 'Edzéshetek összegzése',
                hAxis: {
                    format: 'y.MM.dd',
                    maxValue: new Date(this.workoutPeriodEnd),
                },
            },
        };
    },
    computed: {
        ...mapState({
            workoutWeekPeriod: (state) => state.trainingPeaksHandler.workoutWeekPeriod,
            workoutPeriodStart: (state) => state.trainingPeaksHandler.workoutPeriodStart,
            workoutPeriodEnd: (state) => state.trainingPeaksHandler.workoutPeriodEnd,
        }),
        calendarStartMaxDate() {
            return moment(this.end).subtract(14, 'days').format('YYYY-MM-DD');
        },
    },
    mounted() {
        // this.$root.$on('reloadCalendar3', () => {
        //     this.reloadPeriodCalendar();
        // });
        this.$root.$on('reloadWorkoutComprassion', () => {
            this.reloadPeriodCalendar();
        });
        this.$root.$on('resetChartDataComprassion', () => {
            this.chartData = [];
        });
    },
    methods: {
        validateDatepickers() {
            let isValid = true;
            if (moment(this.end).isBefore(moment(this.start).add(14, 'days'))) {
                isValid = false;
            }
            this.formNotValid = isValid;
        },
        async submitPeriodCalendar() {
            await this.$store.dispatch('trainingPeaksHandler/getWorkoutPeriod', { start: this.start, end: this.end }).then(() => {
                this.reloadPeriodCalendar();
            });
        },
        reloadPeriodCalendar() {
            const chartData = [];
            chartData.push([{ type: 'string', label: 'Időintervallum' }, { type: 'number', label: this.selectedFilter }, { type: 'string', role: 'tooltip' }]);
            let count;
            this.getPeriods().forEach((period) => {
                count = 0;
                this.workoutWeekPeriod.forEach((workout) => {
                    if (moment(workout.workoutDay).isSameOrAfter(period.start) && moment(workout.workoutDay).isSameOrBefore(period.end)) {
                        count += workout[this.selectedFilter];
                    }
                });
                const labelCountFormatted = count === Math.floor(count) ? count : count.toFixed(2);
                chartData.push([moment(period.start).format('MM.DD'), count, `${period.start.format('Y.MM.DD')} - ${period.end.format('Y.MM.DD')}: ${labelCountFormatted}`]);
            });
            this.chartData = chartData;
        },
        getPeriods() {
            const periods = [];
            let weekindex = 0;
            let periodStart;
            let periodEnd;
            let period;
            let weekIteration = moment(this.workoutPeriodStart);
            let loop = true;
            while (loop) {
                weekIteration = moment(weekIteration).add(weekindex, 'weeks');
                if (weekIteration.isSame(moment(this.workoutPeriodStart))) {
                    periodStart = weekIteration;
                    periodEnd = moment(weekIteration).endOf('isoWeek');
                    // } else if (weekIteration >= moment(this.end)) {
                } else if (weekIteration.diff(moment(this.workoutPeriodEnd), 'week') === 0) {
                    periodEnd = moment(this.workoutPeriodEnd);
                    periodStart = moment(weekIteration).startOf('isoWeek');
                    loop = false;
                } else {
                    periodStart = moment(weekIteration).startOf('isoWeek');
                    periodEnd = moment(weekIteration).endOf('isoWeek');
                }
                period = { start: periodStart, end: periodEnd };
                periods.push(period);
                weekindex = 1;
            }
            return periods;
        },
        changeFilter() {
            this.reloadPeriodCalendar();
        },
    },
};
</script>

<style scoped>
.calendar-period-content{
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  padding-top: 3rem;
}
.calendar-period-type {

}
.periodDateFilter{
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  width: 55%;
}
.period-chart {
  width: 80%;
}
</style>

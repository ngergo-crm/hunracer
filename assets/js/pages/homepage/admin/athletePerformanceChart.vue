<template>
    <div
        class="athletePerformancePanel"
    >
        <div class="performanceFilterSwitch">
            <b-form-radio-group
                id="btn-radios-1"
                v-model="selectedFilter"
                :options="options"
                name="radios-btn-default"
                buttons
                size="sm"
                @change="changeFilter"
            />
            <b-form-select
                v-model="selectedYear"
                style="width:7%; margin-left: 1rem;"
                :options="last5yearFilter"
                size="sm"
                @change="changeYear"
            />
            <b-button
                style="margin-left: 1rem;"
                size="sm"
                disabled
            >
                Mentés PDF-ként
            </b-button>
        </div>
        <div>
            <div
                v-for="(data, index) in dataGroup"
                :key="index"
            >
                <div style="margin-bottom: 1rem">
                    <b>{{ index }}</b>
                </div>
                <div>
                    <b-table
                        striped
                        hover
                        :items="data.table"
                        :fields="tableFields"
                        :tbody-tr-class="rowClass"
                    />
                </div>
                <div>
                    <GChart
                        type="BarChart"
                        :data="data.chart"
                        :options="chartOptions"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex';
import { getUnit } from '@/components/helper';
import moment from 'moment';

export default {
    name: 'AthletePerformanceChart',
    data() {
        return {
            tableFields: [
                {
                    key: 'name',
                    label: 'Név',
                },
                {
                    key: 'uRating',
                    label: 'Korcsoport',
                },
                {
                    key: 'expectation',
                    label: 'Elvárás',
                },
                {
                    key: 'performance',
                    label: 'Edzés mennyiség',
                },
                {
                    key: 'status',
                    label: 'Státusz',
                },
            ],
            selectedYear: moment().format('YYYY'),
            selectedFilter: 'distance',
            options: [
                { text: 'Táv', value: 'distance' },
                { text: 'Idő', value: 'totalTime' },
            ],
            chartOptions:
                {
                    //title: 'distance',
                    chart: {
                        title: 'Összesítés',
                    },
                    bars: 'horizontal',
                    hAxis: { format: 'decimal' },
                    height: 400,
                },
            chartsLib: null,
            dataGroup: {},
        };
    },
    computed: {
        ...mapState({
            athletePerformance: (state) => state.adminHomepage.athletePerformance,
            selectedAthletes: (state) => state.adminHomepage.selectedAthletes,
            performanceExpectations: (state) => state.adminHomepage.performanceExpectations,
            uRatings: (state) => state.adminHomepage.uRatings,
        }),
        last5yearFilter() {
            const years = [];
            const thisYear = moment().format('YYYY');
            years.push(thisYear);
            let year;
            for (let i = 1; i < 5; i++) {
                year = thisYear - i;
                years.push(year.toString());
            }
            return years;
        },
    },
    mounted() {
        this.$root.$on('reloadPerformanceChart', () => {
            this.resetFilters();
            this.reloadPerformanceChart();
            this.setTableData();
        });
    },
    methods: {
        setTableData() {
            const labelChange = this.selectedFilter === 'distance' ? '(km)' : '(h)';
            this.tableFields[2].label = 'Elvárás';
            this.tableFields[3].label = 'Edzés mennyiség';
            this.tableFields[2].label = `${this.tableFields[2].label} ${labelChange}`;
            this.tableFields[3].label = `${this.tableFields[3].label} ${labelChange}`;
            this.athletePerformance.forEach((athletePerformance) => {
                const athlete = this.selectedAthletes.filter((user) => athletePerformance.uuid === user.id)[0];
                const athleteExpectation = this.performanceExpectations.filter((expectation) => expectation.gender.description === athlete.gender && expectation.uRating.description === athlete.uRating)[0];
                const data = {};
                data.name = athlete.name;
                data.uRating = athlete.uRating;
                data.performance = parseFloat(athletePerformance[this.selectedFilter]).toFixed(2);
                let expectationValue;
                if (athleteExpectation) {
                    expectationValue = this.selectedFilter === 'distance' ? athleteExpectation.workoutDistancePerYear : athleteExpectation.workoutTimePerYear;
                } else {
                    expectationValue = '?';
                }
                data.expectation = expectationValue;
                let status;
                if (!isNaN(expectationValue)) {
                    status = data.performance >= expectationValue ? 'Megfelelt' : 'Nem felelt meg';
                } else {
                    status = '?';
                }
                data.status = status;
                this.dataGroup[data.uRating];
                if (!('table' in this.dataGroup[data.uRating])) {
                    this.dataGroup[data.uRating].table = [];
                    this.dataGroup[data.uRating].table.push(data);
                } else {
                    this.dataGroup[data.uRating].table.push(data);
                }
            });
        },
        rowClass(item, type) {
            if (!item || type !== 'row') return;
            let tableRowClass;
            if (item.status === 'Megfelelt') {
                tableRowClass = 'table-success';
            } else if (item.status === 'Nem felelt meg') {
                tableRowClass = 'table-danger';
            } else if (item.status === '?') {
                tableRowClass = 'table-warning';
            }
            return tableRowClass;
        },
        resetFilters() {
            this.selectedFilter = 'distance';
            this.selectedYear = moment().format('YYYY');
        },
        reloadPerformanceChart() {
            this.dataGroup = {};
            const data = [];
            let columnDescription;
            this.options.forEach((option) => {
                if (option.value === this.selectedFilter) {
                    columnDescription = option.text;
                }
            });
            const header = ['sportolók', columnDescription];
            data.push(header);
            let user;
            let performanceData;
            this.athletePerformance.forEach((performance) => {
                user = this.selectedAthletes.filter((athlete) => athlete.id === performance.uuid)[0];
                performanceData = [user.name, parseFloat(performance[this.selectedFilter])];
                data.push(performanceData);
                //todo new part - delete original chartData then
                if (!(user.uRating in this.dataGroup)) {
                    this.dataGroup[user.uRating] = { chart: [] };
                    this.dataGroup[user.uRating].chart.push(header);
                    this.dataGroup[user.uRating].chart.push(performanceData);
                } else {
                    this.dataGroup[user.uRating].chart.push(performanceData);
                }
                //todo new part
            });
        },
        onChartReady(chart, google) {
            this.chartsLib = google;
        },
        changeFilter(filter) {
            this.chartOptions.chart.title = `${getUnit(filter, true)}`;
            this.reloadPerformanceChart();
            this.setTableData();
        //this.$store.commit('trainingPeaksHandler/setCalendarFilter', filter);
        //this.reloadCalendar();
        //     this.chartOptions.vAxes['0'].title = `${getUnit(filter, true)}`;
        },
        changeYear() {
            this.$store.dispatch('adminHomepage/getAthletePerformance', { athletes: null, year: this.selectedYear }).then(() => {
                this.reloadPerformanceChart();
                this.setTableData();
            });
        },
    },
};
</script>

<style scoped>
.athletePerformancePanel {
  margin-top: 2rem;
}
.performanceFilterSwitch {
  padding-bottom: 2rem;
  display: flex;
}

</style>

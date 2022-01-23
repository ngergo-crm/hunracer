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
                @click="generateChartReport"
            >
                Mentés PDF-ként
            </b-button>
        </div>
        <!--        <div>-->
        <!--            <div-->
        <!--                v-for="(data, index) in dataGroup"-->
        <!--                :key="index"-->
        <!--            >-->
        <!--                <div style="margin-bottom: 1rem">-->
        <!--                    <b>{{ index }}</b>-->
        <!--                </div>-->
        <!--                <div>-->
        <!--                    <b-table-->
        <!--                        striped-->
        <!--                        hover-->
        <!--                        :items="data.table"-->
        <!--                        :fields="tableFields"-->
        <!--                        :tbody-tr-class="rowClass"-->
        <!--                    />-->
        <!--                </div>-->
        <!--                <div>-->
        <!--                    <GChart-->
        <!--                        type="BarChart"-->
        <!--                        :data="data.chart"-->
        <!--                        :options="chartOptions"-->
        <!--                    />-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <div>
            <div
                v-for="(genders, indexURating) in dataGroup2"
                :key="indexURating"
            >
                <div
                    v-for="(data2, indexGender) in genders"
                    :key="indexGender"
                >
                    <div style="margin-bottom: 1rem;">
                        <b>{{ indexURating }} ({{ indexGender }})</b>
                    </div>
                    <div>
                        <GChart
                            type="BarChart"
                            :data="data2.chart"
                            :options="dataGroup2[indexURating][indexGender].options"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- TODO -->
        <vue-html2pdf
            ref="html2Pdf_adminhome"
            :show-layout="false"
            :float-layout="true"
            :enable-download="false"
            :preview-modal="false"
            :paginate-elements-by-height="1400"
            filename="hee hee"
            :pdf-quality="1"
            :manual-pagination="false"
            pdf-format="a4"
            pdf-orientation="portrait"
            pdf-content-width="100%"
            @beforeDownload="beforeDownload($event)"
        >
            <section slot="pdf-content">
                <div>
                    <div class="pdf-header">
                        <h3>Sportolói teljesítményadatok</h3>
                    </div>
                    <div>
                        <span style="float: right">Letöltve: {{ getTime }}</span>
                    </div>
                    <div
                        v-for="(genders, indexURating) in dataGroup2"
                        :key="indexURating"
                    >
                        <div
                            v-for="(data2, indexGender) in genders"
                            :key="indexGender"
                        >
                            <div style="margin-bottom: 1rem; margin-top: 2rem;">
                                <b>{{ indexURating }} ({{ indexGender }})</b>
                            </div>
                            <div style="width: 50%">
                                <GChart
                                    type="BarChart"
                                    :data="data2.chart"
                                    :options="dataGroup2[indexURating][indexGender].options"
                                />
                                <div class="html2pdf__page-break" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </vue-html2pdf>
    </div>
</template>
<script>
import { mapState } from 'vuex';
import { getUnit } from '@/components/helper';
import moment from 'moment';
import VueHtml2pdf from 'vue-html2pdf';

export default {
    name: 'AthletePerformanceChart',
    components: {
        VueHtml2pdf,
    },
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
            htmlToPdfOptions: {
                margin: [15, 15, 15, 15],
                filename: 'test',
                jsPDF: {
                    format: 'a4',
                    orientation: 'portrait',
                },
            },
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
                    hAxis: {
                        // ticks: [0], // [1000, 8000],
                        format: 'decimal',
                        //gridlines: {
                        //     color: '#333',
                        // count: 5,
                        // },
                    },
                    height: 400,

                },
            chartsLib: null,
            dataGroup: {},
            dataGroup2: {},
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
        getTime() {
            return moment().format('YYYY. MM. DD. HH:mm');
        },
    },
    mounted() {
        this.$root.$on('reloadPerformanceChart', () => {
            this.resetFilters();
            this.reloadPerformanceChart();
            this.reloadPerformanceChart2();
            this.setTableData();
            this.setTableData2();
        });
    },
    methods: {
        setTableData2() {
            const labelChange = this.selectedFilter === 'distance' ? '(km)' : '(h)';
            this.tableFields[2].label = 'Elvárás';
            this.tableFields[3].label = 'Edzés mennyiség';
            this.tableFields[2].label = `${this.tableFields[2].label} ${labelChange}`;
            this.tableFields[3].label = `${this.tableFields[3].label} ${labelChange}`;
            this.athletePerformance.forEach((athletePerformance) => {
                const athlete = this.selectedAthletes.filter((user) => athletePerformance.uuid === user.id)[0];
                const athleteExpectation = this.performanceExpectations.filter((expectation) => expectation.gender.description === athlete.gender && expectation.uRating.description === athlete.uRating)[0];
                const data = {};
                data.gender = athlete.gender;
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

                const options = {
                    chart: {
                        title: 'Összesítés',
                    },
                    bars: 'horizontal',
                    hAxis: {
                        ticks: [{ v: expectationValue, f: `Elvárás: ${expectationValue} km` }],
                        format: 'decimal',
                        gridlines: {
                            color: '#333',
                            // count: 5,
                        },
                    },
                    height: 400,
                };

                if (!('table' in this.dataGroup2[data.uRating][data.gender])) {
                    this.dataGroup2[data.uRating][data.gender].options = options;
                    this.dataGroup2[data.uRating][data.gender].table = [];
                    this.dataGroup2[data.uRating][data.gender].table.push(data);
                } else {
                    this.dataGroup2[data.uRating][data.gender].options = options;
                    this.dataGroup2[data.uRating][data.gender].table.push(data);
                }
            });
        },
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
        reloadPerformanceChart2() {
            this.dataGroup2 = {};
            const data = [];
            let columnDescription;
            this.options.forEach((option) => {
                if (option.value === this.selectedFilter) {
                    columnDescription = option.text;
                }
            });
            const header = ['sportolók', columnDescription, { role: 'annotation' }];
            data.push(header);
            let user;
            let performanceData;
            this.athletePerformance.forEach((performance) => {
                user = this.selectedAthletes.filter((athlete) => athlete.id === performance.uuid)[0];
                performanceData = [user.name, parseFloat(performance[this.selectedFilter]), `${parseInt(performance[this.selectedFilter])} km`];
                data.push(performanceData);
                //todo new part - delete original chartData t
                //check if urating offset exists
                if (!(user.uRating in this.dataGroup2)) {
                    //init urating and gender first time
                    this.dataGroup2[user.uRating] = { [user.gender]: { chart: [] } };
                    this.dataGroup2[user.uRating][user.gender].chart.push(header);
                    this.dataGroup2[user.uRating][user.gender].chart.push(performanceData);
                } else {
                    //check if gender offset exists
                    if (!(user.gender in this.dataGroup2[user.uRating])) {
                        //adding new gender offset and insert
                        this.dataGroup2[user.uRating][user.gender] = { chart: [] };
                        this.dataGroup2[user.uRating][user.gender].chart.push(header);
                        this.dataGroup2[user.uRating][user.gender].chart.push(performanceData);
                    } else {
                        //all exist, just insert
                        this.dataGroup2[user.uRating][user.gender].chart.push(performanceData);
                    }
                }
                //todo new part
            });
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
        changeFilter(filter) {
            this.chartOptions.chart.title = `${getUnit(filter, true)}`;
            this.reloadPerformanceChart();
            this.reloadPerformanceChart2();
            this.setTableData();
            this.setTableData2();
        },
        changeYear() {
            this.$store.dispatch('adminHomepage/getAthletePerformance', { athletes: null, year: this.selectedYear }).then(() => {
                this.reloadPerformanceChart();
                this.reloadPerformanceChart2();
                this.setTableData();
                this.setTableData2();
            });
        },
        generateChartReport() {
            this.$refs.html2Pdf_adminhome.generatePdf();
        },
        async beforeDownload({ html2pdf, options, pdfContent }) {
            await html2pdf().set(this.htmlToPdfOptions).from(pdfContent).toPdf()
                .get('pdf')
                //.then((pdf) => {})
                .save();
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

<template>
    <div>
        <div class="contentpanel">
            <div class="contentcard">
                <div>
                    <b-button
                        style="float: right"
                        @click="generateReport"
                    >
                        Adatlap letöltése
                    </b-button>
                </div>
                <div class="usertablecontenthead">
                    <h2 class="cardtitle">
                        Adatlap
                    </h2>
                </div>
                <div>
                    <h4 class="headline">
                        Személyes adatok
                    </h4>
                    <div class="athleteDetails">
                        <div style="padding-left: 5rem;">
                            <p><b>Név:</b> {{ athlete.name }}</p>
                            <p><b>Születési idő:</b> {{ formatBirthday }}</p>
                            <p><b>UCI kód:</b> {{ athlete.uciNumber }}</p>
                            <p><b>Csapat:</b> {{ team.shortname }}</p>
                            <p><b>E-mail cím:</b> {{ athlete.email }}</p>
                            <p><b>Telefonszám:</b> {{ athlete.phone }}</p>
                        </div>
                        <div style="margin-left: 3rem">
                            <b-img
                                v-if="athlete.photo"
                                v-bind="mainProps"
                                rounded
                                :src="imgUrl"
                                alt="profilkép"
                            />
                            <i
                                v-else
                                class="fas fa-user-circle fa-10x"
                            />
                        </div>
                    </div>
                    <div class="athleteMetrics">
                        <div style="margin-right: 2.5rem; margin-left: 2.5rem">
                            <h4 class="headline">
                                Adatlapok
                            </h4>
                            <div>
                                <div>
                                    <p>Kiválasztott dátum</p>
                                    <b-form-datepicker
                                        v-model="selectedDatasheet"
                                        label-no-date-selected="Dátum választása"
                                    />
                                </div>
                                <div style="margin-top: 1rem">
                                    <b-button-group>
                                        <b-button

                                            :disabled="previousDatasheetEnabled"
                                            @click="!datasheetRequestSent? paginateDatasheet('previous') : null"
                                            v-html="'< Előző'"
                                        />
                                        <b-button
                                            :disabled="nextDatasheetEnabled"
                                            @click="!datasheetRequestSent? paginateDatasheet('next') : null"
                                            v-html="'Következő >'"
                                        />
                                    </b-button-group>
                                </div>
                                <div style="margin-top: 2rem">
                                    <label
                                        id="datasheets"
                                        for="datasheets"
                                    >
                                        Mentett adatlapok
                                    </label>
                                    <b-form-select
                                        id="datasheets"
                                        v-model="selectedDatasheet"
                                        :options="datasheetOtions"
                                    />
                                </div>
                                <div style="margin-top: 2rem">
                                    <b-button
                                        style="float: right"
                                        :disabled="datasheetSaveIsEnabled"
                                        @click="saveDatasheet"
                                    >
                                        Adatlap mentése
                                    </b-button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="headline">
                                Antropometria
                            </h4>
                            <div class="antropofields">
                                <div class="antropofield">
                                    <b-link
                                        :disabled="isModalLinkEnabled"
                                        @click="openMetricModal('height')"
                                    >
                                        Magasság
                                    </b-link>
                                    <div style="display: flex">
                                        <b-input
                                            v-model="metrics.height"
                                            class="antopoinput"
                                            type="number"
                                            :disabled="!this.selectedDatasheet"
                                        />
                                        <span>cm</span>
                                    </div>
                                </div>
                                <div class="antropofield">
                                    <b-link
                                        :disabled="isModalLinkEnabled"
                                        @click="openMetricModal('weight')"
                                    >
                                        Testsúly
                                    </b-link>
                                    <div style="display: flex">
                                        <b-input
                                            v-model="metrics.weight"
                                            class="antopoinput"
                                            type="number"
                                            :disabled="!this.selectedDatasheet"
                                        />
                                        <span>kg</span>
                                    </div>
                                </div>
                                <div class="antropofield">
                                    <b-link
                                        :disabled="isModalLinkEnabled"
                                        @click="openMetricModal('bodyfat')"
                                    >
                                        Testzsír
                                    </b-link>
                                    <div style="display: flex">
                                        <b-input
                                            v-model="metrics.bodyfat"
                                            class="antopoinput"
                                            type="number"
                                            :disabled="!this.selectedDatasheet"
                                        />
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-right: 2.5rem">
                            <h4 class="headline">
                                Teljesítményadatok
                            </h4>
                            <div>
                                <b-table
                                    hover
                                    :items="metrics.tableRecords"
                                    :fields="metricTableFields"
                                    v-bind="{selectedDatasheet}"
                                >
                                    <template #cell(description)="data">
                                        <b-link
                                            :disabled="isModalLinkEnabled"
                                            @click="openMetricModal(data.value)"
                                        >
                                            {{ data.value }}
                                        </b-link>
                                    </template>
                                    <template #cell(pulse)="data">
                                        <b-form-input
                                            v-model="data.value"
                                            type="number"
                                            :disabled="!selectedDatasheet"
                                            @input="setPerformanceValue(data)"
                                        />
                                    </template>
                                    <template
                                        #cell(wattkg)="data"
                                    >
                                        <b-form-input
                                            v-model="data.value"
                                            type="number"
                                            :disabled="!selectedDatasheet"
                                            @input="setPerformanceValue(data)"
                                        />
                                    </template>
                                    <template
                                        #cell(lactate)="data"
                                    >
                                        <b-form-input
                                            v-model="data.value"
                                            type="number"
                                            :disabled="!selectedDatasheet"
                                            @input="setPerformanceValue(data)"
                                        />
                                    </template>
                                </b-table>
                            </div>
                            <div style="margin-bottom: 2rem">
                                <div style="display: flex; width: 100%">
                                    <b-link
                                        :disabled="isModalLinkEnabled"
                                        @click="openMetricModal('vo2max')"
                                    >
                                        Vo2max
                                    </b-link>
                                    <div style="display: flex">
                                        <b-input
                                            v-model="metrics.vo2max"
                                            class="antopoinput"
                                            type="number"
                                            :disabled="!this.selectedDatasheet"
                                        />
                                        <span>ml/ttkg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--                    <div class="athleteMetrics">-->
                    <!--                        <div style="margin-right: 2.5rem">-->
                    <!--                            <h4 class="headline">-->
                    <!--                                Antropometria-->
                    <!--                            </h4>-->
                    <!--                            <div>-->
                    <!--                                <p-->
                    <!--                                    v-for="(type, key) in metricRecords"-->
                    <!--                                    v-if="type[0][Object.keys(type[0])[0]].X.metricGroup.description === 'Antropometria'"-->
                    <!--                                    :id="type.name"-->
                    <!--                                    :key="key"-->
                    <!--                                >-->
                    <!--                                    <b-link @click="openMetricModal2(type[0][Object.keys(type[0])[0]])">-->
                    <!--                                        <b>{{ Object.keys(type[0])[0] }}</b>-->
                    <!--                                    </b-link>-->
                    <!--                                    <GChart-->
                    <!--                                        type="LineChart"-->
                    <!--                                        :data="chartData[Object.keys(type[0])[0]]"-->
                    <!--                                        :options="chartOptions"-->
                    <!--                                    />-->
                    <!--                                </p>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                        <div style="margin-left: 2.5rem">-->
                    <!--                            <h4 class="headline">-->
                    <!--                                Teljesítményadatok-->
                    <!--                            </h4>-->
                    <!--                            <div>-->
                    <!--                                <p-->
                    <!--                                    v-for="(type, key) in metricRecords"-->
                    <!--                                    v-if="type[0][Object.keys(type[0])[0]].X.metricGroup.description === 'Teljesítményadatok'"-->
                    <!--                                    :id="type.name"-->
                    <!--                                    :key="key"-->
                    <!--                                >-->
                    <!--                                    <b-link @click="openMetricModal2(type[0][Object.keys(type[0])[0]])">-->
                    <!--                                        <b>{{ Object.keys(type[0])[0] }}</b>-->
                    <!--                                    </b-link>-->
                    <!--                                    <GChart-->
                    <!--                                        type="LineChart"-->
                    <!--                                        :data="chartData[Object.keys(type[0])[0]]"-->
                    <!--                                        :options="chartOptions"-->
                    <!--                                    />-->
                    <!--                                </p>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                </div>
            </div>
        </div>
        <!--      v-if="selectedMetricTypes"-->
        <metric-editor-modal
            ref="athleteMetricEdit"
            :chart-data="chartData"
        />
        <!--        <datasheet-report />-->
        <!--      @progress="onProgress($event)"-->
        <!--      @hasStartedGeneration="hasStartedGeneration()"-->
        <!--      @hasGenerated="hasGenerated($event)"-->
        <!--      https://www.npmjs.com/package/vue-html2pdf-->
        <!--      https://github.com/kempsteven/vue-html2pdf#getting-started-->

        <vue-html2pdf
            ref="html2Pdf"
            :show-layout="false"
            :float-layout="true"
            :enable-download="true"
            :preview-modal="false"
            :paginate-elements-by-height="1400"
            filename="hee hee"
            :pdf-quality="2"
            :manual-pagination="false"
            pdf-format="a4"
            pdf-orientation="landscape"
            pdf-content-width="800px"
        >
            <section slot="pdf-content">
                <!-- PDF Content Here -->
                <p>test content</p>
                <GChart
                    type="LineChart"
                    :data="chartDataTest"
                    :options="chartOptionsTest"
                />
                <datasheet-report />
                <b-table
                    hover
                    :items="metrics.tableRecords"
                    :fields="metricTableFields"
                    v-bind="{selectedDatasheet}"
                >
                    <template #cell(description)="data">
                        <b-link
                            :disabled="isModalLinkEnabled"
                            @click="openMetricModal(data.value)"
                        >
                            {{ data.value }}
                        </b-link>
                    </template>
                    <template #cell(pulse)="data">
                        <b-form-input
                            v-model="data.value"
                            type="number"
                            :disabled="!selectedDatasheet"
                            @input="setPerformanceValue(data)"
                        />
                    </template>
                    <template
                        #cell(wattkg)="data"
                    >
                        <b-form-input
                            v-model="data.value"
                            type="number"
                            :disabled="!selectedDatasheet"
                            @input="setPerformanceValue(data)"
                        />
                    </template>
                    <template
                        #cell(lactate)="data"
                    >
                        <b-form-input
                            v-model="data.value"
                            type="number"
                            :disabled="!selectedDatasheet"
                            @input="setPerformanceValue(data)"
                        />
                    </template>
                </b-table>
                <!--                </datasheet-report>-->
            </section>
        </vue-html2pdf>
    </div>
</template>

<script>
import VueHtml2pdf from 'vue-html2pdf';
import { mapState, mapGetters } from 'vuex';
import moment from 'moment';
import MetricEditorModal from '@/pages/athleteDataSheet/metricEditorModal';
import {
    containsDate, findDateIndex, findObjectIndex, getMetricTypeDescription,
} from '@/components/helper';
import DatasheetReport from '@/pages/athleteDataSheet/datasheetReport';

export default {
    name: 'AthleteDataSheet',
    components: {
        DatasheetReport,
        MetricEditorModal,
        VueHtml2pdf,
    },
    data() {
        return {
            chartDataTest: [
                ['Year', 'Sales', 'Expenses', 'Profit'],
                ['2014', 1000, 400, 200],
                ['2015', 1170, 460, 250],
                ['2016', 660, 1120, 300],
                ['2017', 1030, 540, 350],
            ],
            chartOptionsTest: {
                chart: {
                    title: 'Company Performance',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                },
            },
            datasheetRequestSent: false,
            metrics: {
                height: null,
                weight: null,
                bodyfat: null,
                vo2max: null,
                tableRecords: [
                    {
                        description: 'LT', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'IAS', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'ANS', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'VM', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'FTP', pulse: null, wattkg: null, lactate: null,
                    },
                ],
            },
            selectedDatasheet: null,
            // datasheetOtions: [
            //     { value: '2021-09-03', text: '2021. 09. 03' },
            //     { value: '2021-09-04', text: '2021. 09. 04' },
            //     { value: '2021-09-05', text: '2021. 09. 05' },
            // ],
            metricTableFields: [
                {
                    key: 'description',
                    label: '',
                },
                {
                    key: 'pulse',
                    label: 'Pulzus',
                },
                {
                    key: 'wattkg',
                    label: 'Watt/kg',
                },
                {
                    key: 'lactate',
                    label: 'Laktát',
                },
            ],
            mainProps: {
                width: 300, height: 'auto', class: 'accountImage',
            },
            chartData: {},
        };
    },
    computed: {
        ...mapGetters({
            datasheetOtions: 'athleteDataSheet/getDatasheetOptions',
        }),
        ...mapState({
            athlete: (state) => state.athleteDataSheet.athlete,
            team: (state) => state.athleteDataSheet.team,
            metricTypes: (state) => state.athleteDataSheet.metricTypes,
            metricRecords: (state) => state.athleteDataSheet.metricRecords,
            selectedMetricTypes: (state) => state.athleteDataSheet.selectedMetricTypes,
            selectedDatasheetRecords: (state) => state.athleteDataSheet.selectedDatasheet,
            //datasheetDates: (state) => state.athleteDataSheet.datasheetDates,
            // startWorkoutYear: (state) => state.settings.workoutYearStart.settingValue,
        }),
        nextDatasheetEnabled() {
            if (!this.datasheetRequestSent) {
                if (this.datasheetOtions.length > 0 && this.selectedDatasheet) {
                    const optionIndex = findObjectIndex(this.datasheetOtions, 'value', this.selectedDatasheet);
                    if ((optionIndex !== 0 && optionIndex !== -1) || (optionIndex === -1 && this.selectedDatasheet < this.datasheetOtions[0].value)) {
                        return false;
                    }
                }
            }
            return true;
        },
        previousDatasheetEnabled() {
            if (!this.datasheetRequestSent) {
                if (this.datasheetOtions.length > 0 && this.selectedDatasheet) {
                    const optionIndex = findObjectIndex(this.datasheetOtions, 'value', this.selectedDatasheet);
                    if ((optionIndex !== (this.datasheetOtions.length - 1) && optionIndex !== -1) || (optionIndex === -1 && this.selectedDatasheet > this.datasheetOtions[(this.datasheetOtions.length - 1)].value)) {
                        return false;
                    }
                }
            }
            return true;
        },
        formatBirthday() {
            return moment(this.athlete.birthday).format('YYYY. MM. DD.');
        },
        imgUrl() {
            return `/getSource/${this.athlete.photo}`;
        },
        datasheetSaveIsEnabled() {
            //todo if year and at least 1 value typed
            let conditionDate = false;
            let conditionValue = false;
            if (this.selectedDatasheet) {
                conditionDate = true;
            }
            if (this.metrics.vo2max || this.metrics.bodyfat || this.metrics.weight || this.metrics.height) {
                conditionValue = true;
            }
            if (!conditionValue) {
                this.metrics.tableRecords.forEach((record) => {
                    if (record.lactate || record.wattkg || record.pulse) {
                        conditionValue = true;
                    }
                });
            }
            return !(conditionDate && conditionValue);
        },
        isModalLinkEnabled() {
            return this.metricTypes.length === 0;
        },
    },
    watch: {
        selectedDatasheet(val) {
            this.loadDatasheet(val);
        },
    },
    async created() {
        await this.$store.dispatch('athleteDataSheet/initializeDataSheet').then(() => {
            this.initChartData();
        });
    },
    mounted() {
        this.$root.$on('insertIntoChartData', ({ metric, date }) => {
            this.insertIntoChartData(metric, date);
        });
        this.$root.$on('deleteFromChartData', ({ records }) => {
            this.deleteFromChartData(records);
        });
    },
    methods: {
        paginateDatasheet(direction) {
            const optionIndex = findObjectIndex(this.datasheetOtions, 'value', this.selectedDatasheet);
            if (direction === 'next') {
                let nextIndex;
                if (optionIndex !== -1) {
                    nextIndex = optionIndex - 1;
                } else {
                    nextIndex = this.datasheetOtions.length - 1;
                    while (this.selectedDatasheet > this.datasheetOtions[nextIndex].value) {
                        nextIndex--;
                    }
                }
                this.selectedDatasheet = this.datasheetOtions[nextIndex].value;
            } else if (direction === 'previous') {
                let previousIndex;
                if (optionIndex !== -1) {
                    previousIndex = optionIndex + 1;
                } else {
                    previousIndex = 0;
                    while (this.selectedDatasheet < this.datasheetOtions[previousIndex].value) {
                        previousIndex++;
                    }
                }
                this.selectedDatasheet = this.datasheetOtions[previousIndex].value;
            }
        },
        getEmptyDatasheet() {
            return {
                height: null,
                weight: null,
                bodyfat: null,
                vo2max: null,
                tableRecords: [
                    {
                        description: 'LT', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'IAS', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'ANS', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'VM', pulse: null, wattkg: null, lactate: null,
                    },
                    {
                        description: 'FTP', pulse: null, wattkg: null, lactate: null,
                    },
                ],
            };
        },
        loadDatasheet(date) {
            this.datasheetRequestSent = true;
            const existing = containsDate(this.datasheetOtions, 'value', date);
            if (existing) {
                const dateIndex = findDateIndex(this.datasheetOtions, 'value', date);
                this.selectedDatasheet = this.datasheetOtions[dateIndex].value;
                this.$store.dispatch('athleteDataSheet/loadDatasheet', { date }).then(() => {
                    const emptyDatasheet = this.getEmptyDatasheet();
                    this.selectedDatasheetRecords.forEach((record) => {
                        let value = parseFloat(record.data);
                        if (!record.axisYType) {
                            if (record.axisXType.name === 'height') {
                                emptyDatasheet.height = Number.isInteger(value) ? value : value.toFixed(1);
                            } else if (record.axisXType.name === 'weight') {
                                emptyDatasheet.weight = Number.isInteger(value) ? value : value.toFixed(1);
                            } else if (record.axisXType.name === 'bodyfat') {
                                emptyDatasheet.bodyfat = Number.isInteger(value) ? value : value.toFixed(1);
                            } else if (record.axisXType.name === 'vo2max') {
                                emptyDatasheet.vo2max = Number.isInteger(value) ? value : value.toFixed(1);
                            }
                        } else {
                            //table
                            const tableIndex = findObjectIndex(emptyDatasheet.tableRecords, 'description', record.axisXType.name);
                            if (record.axisYType.name === 'pulse') {
                                value = parseInt(value);
                            }
                            emptyDatasheet.tableRecords[tableIndex][record.axisYType.name] = value;
                        }
                    });
                    this.metrics = emptyDatasheet;
                    this.datasheetRequestSent = false;
                });
            } else {
                this.metrics = this.getEmptyDatasheet();
                this.$store.commit('athleteDataSheet/setSelectedDatasheet', []);
            }
        },
        setPerformanceValue(data) {
            const index = findObjectIndex(this.metrics.tableRecords, 'description', data.item.description);
            this.metrics.tableRecords[index][data.field.key] = parseFloat(data.value);
        },
        saveDatasheet() {
            this.$store.dispatch('athleteDataSheet/saveDatasheet', { date: this.selectedDatasheet, metrics: this.metrics }).then(() => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Adatlap elmentve!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        openMetricModal(metricType) {
            this.$store.dispatch('athleteDataSheet/selectMetricType', { data: this.metricTypes[findObjectIndex(this.metricTypes, 'name', metricType)] }).then(() => {
                this.$refs.athleteMetricEdit.show();
            });

            //this.$store.commit('athleteDataSheet/setSelectedMetricTypes', this.metricTypes[findObjectIndex(this.metricTypes, 'name', metricType)]);
        },
        initChartData() {
            const chartData = {};
            let header;
            let typeDescription;
            this.metricRecords.forEach((type, key) => {
                typeDescription = Object.keys(type[0])[0];
                header = ['Időpont', typeDescription];
                chartData[typeDescription] = [
                    header,
                ];
                type[0][typeDescription].records.forEach((record) => {
                    const data = [moment(record.metricCreatedAt).format('YYYY.MM.DD'), parseFloat(record.data)];
                    chartData[typeDescription].push(data);
                });
                if (chartData[typeDescription].length === 1) {
                    chartData[typeDescription].push(['-', 0]);
                } else {
                    //sort!
                    chartData[typeDescription].sort((a, b) => moment(a[0], 'YYYY-MM-DD').toDate() - moment(b[0], 'YYYY-MM-DD').toDate());
                }
            });
            this.chartData = chartData;
            // console.log(this.chartData);
            //todo order dates
        },
        insertIntoChartData(metric, date) {
            const updatedMetricType = getMetricTypeDescription(this.selectedMetricTypes);
            if (this.chartData[updatedMetricType][1][0] === '-') {
                this.chartData[updatedMetricType].splice(1, 1);
            }
            this.chartData[updatedMetricType].push([moment(date).format('YYYY.MM.DD'), parseFloat(metric)]);
            this.chartData[updatedMetricType].sort((a, b) => moment(a[0], 'YYYY-MM-DD').toDate() - moment(b[0], 'YYYY-MM-DD').toDate());
        },
        deleteFromChartData(records) {
            const selectedMetricType = getMetricTypeDescription(this.selectedMetricTypes);
            const data = [];
            data.push(['Időpont', selectedMetricType]);
            this.selectedMetricTypes.records.forEach((record) => {
                data.push([moment(record.metricCreatedAt).format('YYYY.MM.DD'), parseFloat(record.data)]);
            });
            if (data.length === 1) {
                data.push(['-', 0]);
            }
            this.chartData[selectedMetricType] = data;
            this.chartData[selectedMetricType].sort((a, b) => moment(a[0], 'YYYY-MM-DD').toDate() - moment(b[0], 'YYYY-MM-DD').toDate());
        },
        generateReport() {
            this.$refs.html2Pdf.generatePdf();
        },
    },
};
</script>

<style scoped>

</style>

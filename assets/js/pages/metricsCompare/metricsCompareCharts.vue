<template>
    <div>
        <div class="athleteMetrics">
            <div style="margin-right: 2.5rem">
                <h4 class="headline">
                    Antropometria
                </h4>
                <div>
                    <!--                    <p-->
                    <!--                        v-for="(type, key) in metricRecords"-->
                    <!--                        v-if="type[0][Object.keys(type[0])[0]].X.metricGroup.description === 'Antropometria'"-->
                    <!--                        :id="type.name"-->
                    <!--                        :key="key"-->
                    <!--                    >-->
                    <!--                        <b-link @click="openMetricModal2(type[0][Object.keys(type[0])[0]])">-->
                    <!--                            <b>{{ Object.keys(type[0])[0] }}</b>-->
                    <!--                        </b-link>-->
                    <!--                        <GChart-->
                    <!--                            type="LineChart"-->
                    <!--                            :data="chartData[Object.keys(type[0])[0]]"-->
                    <!--                            :options="chartOptions"-->
                    <!--                        />-->
                    <!--                    </p>-->
                </div>
            </div>
            <div style="margin-left: 2.5rem">
                <h4 class="headline">
                    Teljesítményadatok
                </h4>
                <div>
                    <!--                    <p-->
                    <!--                        v-for="(type, key) in metricRecords"-->
                    <!--                        v-if="type[0][Object.keys(type[0])[0]].X.metricGroup.description === 'Teljesítményadatok'"-->
                    <!--                        :id="type.name"-->
                    <!--                        :key="key"-->
                    <!--                    >-->
                    <!--                        <b-link @click="openMetricModal2(type[0][Object.keys(type[0])[0]])">-->
                    <!--                            <b>{{ Object.keys(type[0])[0] }}</b>-->
                    <!--                        </b-link>-->
                    <!--                        <GChart-->
                    <!--                            type="LineChart"-->
                    <!--                            :data="chartData[Object.keys(type[0])[0]]"-->
                    <!--                            :options="chartOptions"-->
                    <!--                        />-->
                    <!--                    </p>-->
                    <GChart
                        type="LineChart"
                        :data="chartData"
                        :options="chartOptions"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex';

export default {
    name: 'MetricsCompareCharts',
    data() {
        return {
            chartData: [
                ['idopont', 'user1', 'user2'],
                ['2021 09 01', 171, 175],
                ['2021 09 05', 171, 176],
                ['2021 09 09', 171, 176],
                ['2021 09 18', 171, 177],
                ['2021 09 22', 172, 177],
                ['2021 09 24', 173, 177],
                ['2021 09 29', 173, 178],

            ],
            chartOptions: {
                chart: {
                    title: 'Company Performance',
                    subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                },
            },
        };
    },
    computed: {
        ...mapState({
            selectedAthletes: (state) => state.metricsCompare.selectedAthletes,
            metricTypes: (state) => state.metricsCompare.metricTypes,
        }),
    },
    mounted() {
        this.$root.$on('reloadMetricsCharts', () => {
            this.reloadMetricsCharts();
        });
    },
    methods: {
        reloadMetricsCharts() {
            console.log('emit');
            //todo fill chartData
        },
    },
};
</script>

<style scoped>

</style>

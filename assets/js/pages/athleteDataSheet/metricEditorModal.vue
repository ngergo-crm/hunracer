<template>
    <div>
        <b-modal
            id="athleteMetricEdit"
            ref="athleteMetricEdit"
            hide-header
            hide-footer
        >
            <div
                v-if="selectedMetricTypes"
                class="metricModalHeader"
            >
                <h4 v-if="selectedMetricTypes.description">
                    <span>{{ selectedMetricTypes.description }}</span>
                    <span v-if="selectedMetricTypes.unit">({{ selectedMetricTypes.unit }})</span>
                </h4>
            </div>
            <div v-if="!selectedMetricTypes.axis">
                <spinner v-if="!(chartData[this.selectedMetricTypes.description])" />
                <GChart
                    v-else
                    type="LineChart"
                    :data="chartData[this.selectedMetricTypes.description]"
                    :options="chartOptions"
                />
            </div>
            <div
                v-else-if="selectedMetricTypes.axis === 'X'"
                style="height: 16rem; overflow: auto;"
            >
                <div
                    v-for="(type, key) in typeY"
                    :key="key"
                >
                    <p>{{ `${selectedMetricTypes.description}\/ ${type}` }}</p>
                    <spinner v-if="!(chartData[`${selectedMetricTypes.description}\/ ${type}`])" />
                    <GChart
                        v-else
                        type="LineChart"
                        :data="chartData[`${selectedMetricTypes.description}\/ ${type}`]"
                        :options="chartOptions"
                    />
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import moment from 'moment';
import multiselect from 'vue-multiselect';
import { getMetricTypeDescription } from '@/components/helper';
import Spinner from '@/pages/common/spinner';

export default {
    name: 'MetricEditorModal',
    components: {
        Spinner,
        multiselect,
    },
    props: {
        chartData: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            typeY: ['Laktát', 'watt/kg', 'Pulzus'],
            selectedData: [],
            chartOptions: {
                chart: {
                    title: 'zzzzzz',
                    subtitle: 'xxxx',
                },
            },
        };
    },
    computed: {
        ...mapState({
            selectedMetricTypes: (state) => state.athleteDataSheet.selectedMetricTypes,
            // selectedMetricTypes: (state) => state.athleteDataSheet.selectedMetricTypes,
            // startWorkoutYear: (state) => state.settings.workoutYearStart.settingValue,
        }),
    },
    methods: {
        show() {
            this.$refs.athleteMetricEdit.show();
        },
    },
};
</script>

<style scoped>
.metricModalHeader{

}

.modalMetricNewData{
  display: flex;
  justify-content: space-around;
  padding-top: 1rem;

}

.modalEdit{
  display: flex;
  width: 100%;
}

</style>

<template>
    <div>
        <div>
            <div
                v-if="paginationNeeded"
                class="workoutDetailPagination"
            >
                <div class="workoutDetailCount">
                    {{ workoutsCount }} edzés elérhető ezen a napon
                </div>

                <div class="workoutDetailButtons">
                    <b-button
                        style="margin-right: 1rem"
                        :disabled="selectedIndex <= 0"
                        @click="selectedIndex-=1"
                    >
                        Előző
                    </b-button>
                    <b-button
                        :disabled="selectedIndex >= (workoutsCount-1)"
                        @click="selectedIndex+=1"
                    >
                        Következő
                    </b-button>
                </div>
            </div>
            <div>
                Kezdés ideje: {{ workoutStartTime }}
            </div>
        </div>
        <div>
            <b-table
                :items="tableBasics"
                :fields="tableBasicsFields"
            />
        </div>
        <div>
            <b-table
                :items="tableMinAvgMax"
                :fields="tableMinAvgMaxFields"
            />
        </div>
        <div>
            <b-table
                :items="smallTable"
                :fields="smallTableFields"
            />
        </div>
    </div>
</template>

<script>

import moment from 'moment';
import { formatDecimalDuration } from '@/components/helper';

export default {
    name: 'WorkoutDetails',
    props: {
        workouts: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            selectedIndex: 0,
            tableBasicsFields: [
                {
                    key: 'time',
                    label: 'Idő',
                },
                {
                    key: 'distance',
                    label: 'Táv',
                },
                {
                    key: 'tss',
                    label: 'TSS',
                },
            ],
            tableMinAvgMaxFields: [
                {
                    key: 'method',
                    label: '',
                },
                {
                    key: 'min',
                    label: 'Min.',
                },
                {
                    key: 'avg',
                    label: 'Átlag',
                },
                {
                    key: 'max',
                    label: 'Max.',
                },
            ],
            smallTableFields: [
                {
                    key: 'method',
                    label: '',
                },
                {
                    key: 'val',
                    label: '',
                },
            ],
        };
    },
    computed: {
        workoutStartTime() {
            return this.workouts[this.selectedIndex].data.StartTime ? moment(this.workouts[this.selectedIndex].data.StartTime).format('HH:mm') : 'nincs adat';
        },
        paginationNeeded() {
            return this.workouts.length > 1;
        },
        workoutsCount() {
            return this.workouts.length;
        },
        tableBasics() {
            return [
                {
                    time: `${this.workouts[this.selectedIndex].totalTime === null ? 0 : formatDecimalDuration(this.workouts[this.selectedIndex].totalTime)}`,
                    distance: `${this.workouts[this.selectedIndex].distance === null ? 0 : (this.workouts[this.selectedIndex].distance / 1000).toFixed(2)} km`,
                    tss: this.workouts[this.selectedIndex].tss === null ? 0 : this.workouts[this.selectedIndex].tss,
                },
            ];
        },
        tableMinAvgMax() {
            return [
                {
                    method: 'Teljesítmény (w)',
                    min: 0,
                    avg: this.workouts[this.selectedIndex].data.PowerAverage === null ? 0 : this.workouts[this.selectedIndex].data.PowerAverage,
                    max: this.workouts[this.selectedIndex].data.PowerMaximum === null ? 0 : this.workouts[this.selectedIndex].data.PowerMaximum,
                },
                {
                    method: 'Pulzus (BPM)',
                    min: this.workouts[this.selectedIndex].data.HeartRateMinimum === null ? 0 : this.workouts[this.selectedIndex].data.HeartRateMinimum,
                    avg: this.workouts[this.selectedIndex].data.HeartRateAverage === null ? 0 : this.workouts[this.selectedIndex].data.HeartRateAverage,
                    max: this.workouts[this.selectedIndex].data.HeartRateMaximum === null ? 0 : this.workouts[this.selectedIndex].data.HeartRateMaximum,
                },
                {
                    method: 'Pedálfordulat (RPM)',
                    min: 0,
                    avg: this.workouts[this.selectedIndex].data.CadenceAverage === null ? 0 : this.workouts[this.selectedIndex].data.CadenceAverage,
                    max: this.workouts[this.selectedIndex].data.CadenceMaximum === null ? 0 : this.workouts[this.selectedIndex].data.CadenceMaximum,
                },
                {
                    method: 'Sebesség (km/h)',
                    min: 0,
                    avg: this.workouts[this.selectedIndex].data.VelocityAverage === null ? 0 : this.workouts[this.selectedIndex].data.VelocityAverage.toFixed(2),
                    max: this.workouts[this.selectedIndex].data.VelocityMaximum === null ? 0 : this.workouts[this.selectedIndex].data.VelocityMaximum.toFixed(2),
                },
            ];
        },
        smallTable() {
            return [
                {
                    method: 'Munka', val: `${this.workouts[this.selectedIndex].data.Energy === null ? 0 : this.workouts[this.selectedIndex].data.Energy.toFixed(2)} kJ`,
                },
                {
                    method: 'NP (w)', val: `${this.workouts[this.selectedIndex].data.NormalizedPower === null ? 0 : this.workouts[this.selectedIndex].data.NormalizedPower} W`,
                },
                {
                    method: 'Teljes szintemelkedés (m)', val: this.workouts[this.selectedIndex].data.ElevationGain === null ? 0 : this.workouts[this.selectedIndex].data.ElevationGain,
                },
                {
                    method: 'IF', val: this.workouts[this.selectedIndex].data.IF === null ? 0 : this.workouts[this.selectedIndex].data.IF.toFixed(2),
                },
            ];
        },
    },
};
</script>

<style scoped>
.workoutDetailPagination {
  display: flex;
}
.workoutDetailCount{
  width: 50%;
}
.workoutDetailButtons{
  width: 50%;
  display: flex;
  justify-content: flex-end;
}
</style>

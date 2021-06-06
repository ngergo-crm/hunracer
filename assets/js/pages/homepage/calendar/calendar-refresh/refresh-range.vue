<template>
    <div>
        <b-button
            v-b-popover.hover.right="'Add meg a kezdő- és záródátumot az Training Peaks-edzések szinkronizálásához. A kezdődátum nem lehet nagyobb a záródátumnál! A funkciót akkor is használhatod, ha a Training Peaks-en utólag frissítettél egy korábban létrehozott edzést, de a változás nem jelent meg a Hunracer-en.'"
            class="m-1"
            pill
            :class="visible ? null : 'collapsed'"
            :aria-expanded="visible ? 'true' : 'false'"
            aria-controls="refresh-collapse"
            @click="collapseVisible"
        >
            Adatok szinkronizálása
        </b-button>
        <b-collapse
            id="refresh-collapse"
            :visible="visible"
            style="width: 38rem;"
        >
            <b-card>
                <b-form
                    class="detailedRefreshForm"
                    @submit="workoutSync"
                >
                    <b-form-group
                        label="Válaszd ki a frissítés kezdődátumát:"
                    >
                        <b-form-datepicker
                            v-model="endDate"
                            label-help=""
                            label-prev-month="Előző hónap"
                            label-prev-year="Előző év"
                            label-next-month="Következő hónap"
                            label-next-year="Következő év"
                            label-current-month="Jelen hónap"
                            locale="hu"
                            start-weekday="1"
                            :hide-header="true"
                            placeholder="Nincs záródátum"
                            :max="calendarMaxDate"
                            required
                            :state="endDateState"
                            @context="endDateIsValid(endDate)"
                        />
                    </b-form-group>
                    <b-form-group
                        label="Válaszd ki a frissítés záródátumát:"
                    >
                        <b-form-datepicker
                            id="start-datepicker"
                            v-model="startDate"
                            label-help=""
                            label-prev-month="Előző hónap"
                            label-prev-year="Előző év"
                            label-next-month="Következő hónap"
                            label-next-year="Következő év"
                            label-current-month="Jelen hónap"
                            locale="hu"
                            start-weekday="1"
                            :hide-header="true"
                            placeholder="Nincs kezdődátum"
                            :max="calendarMaxDate"
                            required
                            :state="startDateState"
                            @context="startDateIsValid(startDate)"
                        />
                    </b-form-group>
                    <div class="refreshSubmit">
                        <b-button
                            variant="primary"
                            type="submit"
                            :disabled="formValid"
                            style="width: 9rem;"
                        >
                            <span v-if="!detailedRefreshWorking">Frissítés indítása</span>
                            <b-spinner
                                v-else-if="detailedRefreshWorking"
                                small
                            />
                        </b-button>
                    </div>
                </b-form>
            </b-card>
        </b-collapse>
    </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    name: 'RefreshRange',
    props: {
        hasWorkouts: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            startDate: moment().format('YYYY-MM-DD'),
            endDate: '',
            startDateState: true,
            endDateState: false,
            detailedRefreshWorking: false,
            visible: !this.hasWorkouts,
        };
    },
    computed: {

        ...mapState({
            selectedTime: (state) => state.trainingPeaksHandler.selectedTime,
        }),
        formValid() {
            return !(this.endDateState && this.startDateState);
        },
        initialDate() {
            return moment().format('YYYY-MM-DD');
        },
        calendarMaxDate() {
            return moment().format('YYYY-MM-DD');
        },
    },
    methods: {
        workoutSync(event) {
            event.preventDefault();
            this.detailedRefreshWorking = true;
            this.$store.dispatch('trainingPeaksHandler/detailedRefresh', { start: this.startDate, end: this.endDate }).then(() => {
                this.$root.$emit('getWorkoutWeek', moment(this.selectedTime));
                this.$root.$emit('getWorkoutPeriod', { start: null, end: null });
                this.detailedRefreshWorking = false;
                this.collapseVisible();
            });
        },
        startDateIsValid(date) {
            let state = !!date;
            if (state) {
                state = moment(date).format('Y-MM-DD') >= this.endDate;
            }
            this.startDateState = state;
            if (this.endDate) {
                this.endDateState = moment(date).format('Y-MM-DD') >= this.endDate;
            }
        },
        endDateIsValid(date) {
            let state = !!date;
            if (state) {
                state = moment(date).format('Y-MM-DD') <= this.startDate;
            }
            this.endDateState = state;
            if (this.startDate) {
                this.startDateState = moment(date).format('Y-MM-DD') <= this.startDate;
            }
        },
        collapseVisible() {
            this.visible = !this.visible;
        },
    },
};
</script>

<style scoped>
.detailedRefreshForm{
  max-width: 21rem;
}
.refreshSubmit {
  display: flex;
  justify-content: flex-end;
}
</style>

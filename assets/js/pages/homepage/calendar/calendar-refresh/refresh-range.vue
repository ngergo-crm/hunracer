<template>
    <div>
        <b-button
            v-b-toggle.collapse-3
            v-b-popover.hover.right="'Add meg a kezdő- és záródátumot az Training Peaks-edzések szinkronizálásához. A kezdődátum nem lehet nagyobb a záródátumnál! A funkciót akkor is használhatod, ha a Training Peaks-en utólag frissítettél egy korábban létrehozott edzést, de a változás nem jelent meg a Hunracer-en.'"
            class="m-1"
            pill
        >
            Adatok szinkronizálása
        </b-button>
        <b-collapse
            id="collapse-3"
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
                    <b-button
                        variant="primary"
                        type="submit"
                        :disabled="formValid"
                    >
                        Frissítés indítása
                    </b-button>
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
    data() {
        return {
            startDate: moment().format('YYYY-MM-DD'),
            endDate: '',
            startDateState: true,
            endDateState: false,
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
            this.$store.dispatch('trainingPeaksHandler/detailedRefresh', { start: this.startDate, end: this.endDate }).then(() => {
                this.$root.$emit('getWorkoutWeek', moment(this.selectedTime));
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
    },
};
</script>

<style scoped>
.detailedRefreshForm{
  max-width: 21rem;
}
</style>

<template>
    <div>
        <div class="contentcentral">
            <div
                class="contentcard"
            >
                <div class="cardtitle">
                    <h4>
                        Beállítások
                    </h4>
                </div>
                <spinner
                    v-if="settings.length === 0"
                    class="settingsBody"
                />
                <div
                    v-else
                    class="settingsBody"
                >
                    <div class="setting">
                        <div class="settingKey">
                            Edzésév kezdete
                        </div>
                        <div class="settingValue">
                            <b-input-group class="mb-3">
                                <b-form-input
                                    id="example-input"
                                    v-model="workoutYearStartFormatted"
                                    type="text"
                                    autocomplete="off"
                                    readonly
                                />
                                <b-input-group-append>
                                    <b-form-datepicker
                                        v-model="workoutYearStart"
                                        button-only
                                        right
                                        aria-controls="example-input"
                                        locale="hu"
                                        @context="onContext"
                                    />
                                </b-input-group-append>
                            </b-input-group>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; margin-top: 2rem">
                        <b-button
                            class="hunracerButton"
                            style="margin-bottom: 1rem;"
                            @click="saveSettings"
                        >
                            Mentés
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import { mapState } from 'vuex';
import spinner from '@/pages/common/spinner';

export default {
    name: 'Settings',
    components: {
        spinner,
    },
    data() {
        return {
            workoutYearStartFormatted: '',
        };
    },
    computed: {
        ...mapState({
            settings: (state) => state.settings.allSettings,
        }),
        workoutYearStart: {
            get() {
                return this.$store.state.settings.workoutYearStart.settingValue;
            },
            set(value) {
                this.$store.commit('settings/updateSettingValue', { key: 'workoutYearStart', value });
            },
        },
    },
    async created() {
        await this.$store.dispatch('settings/initializeSettings');
    },
    methods: {
        onContext(ctx) {
            this.workoutYearStartFormatted = moment(ctx.selectedYMD).lang('hu').format('MMMM D');
        },
        saveSettings() {
            this.$store.dispatch('settings/saveSettings').then(() => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Változások elmentve!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
    },
};
</script>

<style scoped>
.settingsBody{
  margin-top: 2rem;
}
.settingValue{

}
.settingKey{

}
.setting{
  display: flex;
  justify-content: space-between;
}

</style>

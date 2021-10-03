<template>
    <div>
        <div style="margin-bottom: 1rem;">
            <b>Szűrők:</b>
        </div>
        <div
            v-if="!(sections.length > 0 && teams.length > 0 && genders.length > 0)"
            class="spinnerPlace"
        >
            <b-spinner
                class="calendarSpinner"
                label="Spinning"
                type="grow"
            />
        </div>
        <div v-else>
            <div class="adminfilterpanel">
                <div class="smallfilter">
                    <div>U-besorolás:</div>
                    <multiselect
                        v-model="uRatingFilters"
                        :searchable="false"
                        label="description"
                        track-by="ageInterval"
                        :options="uRatings"
                        placeholder="Nincs kiválasztva..."
                        selected-label="kiválasztva"
                        deselect-label="eltávolítás"
                        select-label="kiválasztás"
                    />
                </div>
                <div class="smallfilter">
                    <div>Szakág:</div>
                    <multiselect
                        v-model="sectionFilters"
                        :options="sections"
                        :searchable="false"
                        track-by="description"
                        label="description"
                        :taggable="true"
                        :multiple="true"
                        placeholder="Szakág választása..."
                        selected-label="kiválasztva"
                        deselect-label="Szakág eltávolítása"
                        select-label="kiválasztás"
                        tag-placeholder=""
                    />
                </div>
                <div class="teamfilter">
                    <div>Csapat:</div>
                    <multiselect
                        v-model="teamFilters"
                        :options="teams"
                        :taggable="true"
                        :multiple="true"
                        track-by="shortname"
                        label="shortname"
                        placeholder="Csapat választása..."
                        selected-label="kiválasztva"
                        deselect-label="eltávolítás"
                        select-label="kiválasztás"
                        no-result="Nincs találat."
                        tag-placeholder=""
                    >
                        <template>
                            <span slot="noResult">Nincs találat.</span>
                        </template>
                    </multiselect>
                </div>
                <div
                    v-if="Object.keys(user).length === 0 || user.roleDescription === 'szuperAdmin' || user.roleDescription === 'admin'"
                    class="adminfilter"
                >
                    <div>Edző:</div>
                    <multiselect
                        v-model="trainerFilters"
                        :options="trainers"
                        :taggable="true"
                        :multiple="true"
                        track-by="trainerCode"
                        label="name"
                        placeholder="Edző választása..."
                        selected-label="kiválasztva"
                        deselect-label="eltávolítás"
                        select-label="kiválasztás"
                        no-result="Nincs találat."
                        tag-placeholder=""
                    >
                        <template>
                            <span slot="noResult">Nincs találat.</span>
                        </template>
                    </multiselect>
                </div>
                <div class="smallfilter">
                    <div>A sportolók neme:</div>
                    <multiselect
                        v-model="genderFilters"
                        :options="genders"
                        :searchable="false"
                        track-by="description"
                        label="description"
                        placeholder="Nincs kiválasztva..."
                        selected-label="kiválasztva"
                        deselect-label="Nem eltávolítása"
                        select-label="kiválasztás"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import multiselect from 'vue-multiselect';
import { mapState } from 'vuex';

export default {
    name: 'AdminFilters',
    components: {
        multiselect,
    },
    data() {
        return {
        };
    },
    computed: {
        ...mapState({
            uRatings: (state) => state.adminHomepage.uRatings,
            teams: (state) => state.adminHomepage.teams,
            sections: (state) => state.adminHomepage.sections,
            genders: (state) => state.adminHomepage.genders,
            athletes: (state) => state.adminHomepage.athletes,
            trainers: (state) => state.adminHomepage.trainers,
            user: (state) => state.metricsCompare.user,
        }),
        uRatingFilters: {
            get() {
                return this.$store.state.adminHomepage.uRatingFilters;
            },
            set(value) {
                this.$store.commit('adminHomepage/setURatingFilters', value);
            },
        },
        sectionFilters: {
            get() {
                return this.$store.state.adminHomepage.sectionFilters;
            },
            set(value) {
                this.$store.commit('adminHomepage/setSectionFilters', value);
            },
        },
        teamFilters: {
            get() {
                return this.$store.state.adminHomepage.teamFilters;
            },
            set(value) {
                this.$store.commit('adminHomepage/setTeamFilters', value);
            },
        },
        genderFilters: {
            get() {
                return this.$store.state.adminHomepage.genderFilters;
            },
            set(value) {
                this.$store.commit('adminHomepage/setGenderFilters', value);
            },
        },
        trainerFilters: {
            get() {
                return this.$store.state.adminHomepage.trainerFilters;
            },
            set(value) {
                this.$store.commit('adminHomepage/setTrainerFilters', value);
            },
        },

    },
    async created() {
        await this.$store.dispatch('adminHomepage/initializeFilters').then(() => {
            console.log(this.user);

            if (Object.keys(this.user) === 0 || this.user.roleDescription !== 'edző') {
                this.$store.dispatch('adminHomepage/initTrainers');
            }
            this.$store.dispatch('adminHomepage/initAthletes', { user: this.user });
        });
    },
    methods: {
        filterAthletes() {
            if (this.genderFilters || this.teamFilters.length > 0 || this.sectionFilters.length > 0 || this.teamFilters.length > 0 || this.uRatingFilters) {
                this.$store.dispatch('adminHomepage/filterAthletes');
            } else {
                //todo nincs filter - popup/üzenet
            }
        },

    },
};
</script>

<style scoped>
.smallfilter {
  width: 15%;
  /*display: inline-block;*/
}
.adminfilter {
  width: 20%;
  /*display: inline-block;*/
}
.teamfilter {
  width: 30%;
}
.adminfilterpanel {
  display: flex;
  justify-content: space-between;
}
</style>

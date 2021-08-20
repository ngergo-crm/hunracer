<template>
    <div>
        <admin-filters />
        <div
            v-if="athletes.length === 0"
            class="spinnerPlace"
        >
            <b-spinner
                class="calendarSpinner"
                label="Spinning"
                type="grow"
            />
        </div>
        <div v-else>
            <div class="filters">
                <div class="sorting">
                    <div class="sortbox">
                        <b-form-group
                            label="Mutat"
                            label-for="per-page-select"
                            label-cols-sm="6"
                            label-cols-md="4"
                            label-cols-lg="3"
                            label-align-sm="right"
                            label-size="sm"
                        >
                            <b-form-select
                                id="per-page-select"
                                v-model="perPage"
                                :options="pageOptions"
                                size="sm"
                            />
                        </b-form-group>
                    </div>
                </div>
            </div>
            <b-col
                v-if="athletes.length > 0"
                class="paginator"
            >
                <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    align="fill"
                    size="sm"
                    class="my-0"
                />
            </b-col>
            <b-table
                v-if="athletes.length > 0"
                ref="selectableTable"
                striped
                hover
                selectable
                select-mode="multi"
                :items="athletes"
                :per-page="perPage"
                :fields="fields"
                :filter="filter"
                :filter-ignored-fields="filterIgnoredFields"
                :current-page="currentPage"
                show-empty
                empty-filtered-html="Nincs találat..."
                empty-html="Nincsenek adatok..."
                :filter-function="filterAthletes"
                @filtered="onFiltered"
                @row-selected="onRowSelected"
            >
                <template #cell(sections)="row">
                    <b-badge
                        v-for="(section, key) in row.value"
                        :key="key"
                        class="sectionBadge"
                    >
                        {{ section }}
                    </b-badge>
                </template>
                <template #cell(actions)="{ rowSelected }">
                    <template v-if="rowSelected">
                        <span aria-hidden="true">&check;</span>
                        <span class="sr-only">Selected</span>
                    </template>
                    <template v-else>
                        <span aria-hidden="true">&nbsp;</span>
                        <span class="sr-only">Not selected</span>
                    </template>
                </template>
            </b-table>
            <div v-show="athletes.length > 0">
                <b-button
                    size="sm"
                    :disabled="!(selectedAthletes.length > 0)"
                    @click="showAthletePerformance"
                >
                    Kijelöltek mutatása
                </b-button>
                <b-button
                    size="sm"
                    :disabled="!(athletes.length > 0)"
                    @click="selectAllRows"
                >
                    Mindet kijelöl
                </b-button>
                <b-button
                    size="sm"
                    :disabled="!(athletes.length > 0)"
                    @click="clearSelected"
                >
                    Kijelölések törlése
                </b-button>
                <athlete-performance-chart-component v-if="performanceData.length > 0" />
            </div>
        </div>
    </div>
</template>

<script>
import AdminFilters from '@/pages/homepage/admin/adminFilters';
import { mapGetters, mapState } from 'vuex';
import { containsObject } from '@/components/helper';
import AthletePerformanceChartComponent from '@/pages/homepage/admin/athletePerformanceChart';

export default {
    name: 'AdminPanel',
    components: { AthletePerformanceChartComponent, AdminFilters },
    data() {
        return {
            filterIgnoredFields: ['@id', '@type'],
            fields:
              [
                  {
                      key: 'name',
                      label: 'Név',
                      sortable: true,
                  },
                  {
                      key: 'gender',
                      label: 'Nem',
                      sortable: true,
                  },
                  {
                      key: 'team',
                      label: 'Csapat',
                      sortable: true,
                  },
                  {
                      key: 'uRating',
                      label: 'Korcsoport',
                      sortable: true,
                  },
                  {
                      key: 'sections',
                      label: 'Szakág',
                      sortable: true,
                  },
                  {
                      key: 'actions',
                      label: 'Kiválasztva',
                      sortable: true,
                  },
              ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [10, 20, 50],
            athletesLocal: [],
            selectedAthletes: [],
        };
    },
    computed: {
        ...mapGetters({
            athletes: 'adminHomepage/getAthletes',
            filter: 'adminHomepage/getFilters',
        }),
        ...mapState({
            performanceData: (state) => state.adminHomepage.athletePerformance,
        }),
        maxPage() {
            return Math.ceil(this.totalRows / this.perPage);
        },
    },
    methods: {
        onRowSelected(items, selectMode = true) {
            if (selectMode) {
                this.selectedAthletes = items;
            }
        },
        resetSelectedAthletes() {
            this.athletesLocal.splice(0, this.athletesLocal.length);
        },
        async showAthletePerformance() {
            await this.$store.dispatch('adminHomepage/getAthletePerformance', { athletes: this.selectedAthletes }).then(() => {
                this.$root.$emit('reloadPerformanceChart');
            });
        },
        onFiltered(filteredItems) {
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        filterAthletes(data, filter) {
            //console.log(filter);
            const filtered = [];
            if (filter.gender) {
                filtered.push(data.gender === filter.gender.description);
            }
            if (filter.uRating) {
                filtered.push(data.uRating === filter.uRating.description);
            }
            if (filter.team && data.team) {
                let i = 0;
                const N = filter.team.length;
                while (i < N && !(filter.team[i].shortname === data.team)) {
                    i++;
                }
                filtered.push(i < N);
            } else if (filter.team && !data.team) {
                filtered.push(false);
            }
            if (filter.trainerCode && data.trainerCode) {
                let i = 0;
                const N = filter.trainerCode.length;
                while (i < N && !(filter.trainerCode[i].trainerCode === data.trainerCode)) {
                    i++;
                }
                filtered.push(i < N);
            } else if (filter.trainerCode && !data.trainerCode) {
                filtered.push(false);
            }
            if (filter.sections && data.sections.length > 0) {
                let i = 0;
                const N = filter.sections.length;
                while (i < N && !(data.sections.includes(filter.sections[i].description))) {
                    i++;
                }
                const test = i < N;
                //  console.log(test);
                filtered.push(test);
            } else if (filter.sections && data.sections.length === 0) {
                filtered.push(false);
            }
            const result = !filtered.includes(false);
            return result;
        },
        selectAllRows() {
            this.$refs.selectableTable.selectAllRows();
        },
        clearSelected() {
            this.$refs.selectableTable.clearSelected();
        },
    },
};
</script>

<style scoped>
.sectionBadge {
  padding: 5px;
  margin: 2px
}

.filters {
  display: flex;
  flex-direction: row;
}

.sorting {
  width: 100%;
}

.sortbox {
  width: 30%;
  float: right;
  padding-right: 1rem;
}

.paginator {
  max-width: 40%;
  float: right;
}
</style>

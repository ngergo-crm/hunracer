<template>
    <div class="contentpanel">
        <div class="contentcard">
            <div class="usertablecontenthead">
                <h3 class="cardtitle">
                    Csapatok kezelése
                </h3>
            </div>
            <spinner-component v-if="loading" />
            <div
                v-else
                class="teamboard"
            >
                <div class="newTeam">
                    <b-button
                        v-b-toggle.collapse-1
                        variant="primary"
                        aria-controls="newTeamForm"
                        :aria-expanded="newTeamFormVisible ? 'true' : 'false'"
                        @click="newTeamFormVisible = !newTeamFormVisible"
                    >
                        Csapat hozááadása
                    </b-button>
                    <b-collapse
                        id="newTeamForm"
                        v-model="newTeamFormVisible"
                        class="mt-2"
                        style=""
                    >
                        <b-card style="width: 50%;">
                            <b-form
                                class="newTeamForm"
                                @submit.stop.prevent="addTeam"
                            >
                                <b-form-input
                                    v-model="newTeam.fullname"
                                    placeholder="Bejegyzett név"
                                    type="text"
                                />
                                <b-form-input
                                    v-model="newTeam.shortname"
                                    placeholder="Rövid név"
                                    type="text"
                                />
                                <b-form-input
                                    v-model="newTeam.contactname"
                                    placeholder="Kapcsolattartó"
                                    type="text"
                                />
                                <b-form-input
                                    v-model="newTeam.contactphone"
                                    placeholder="Telefonszám"
                                    type="text"
                                />
                                <b-form-input
                                    v-model="newTeam.contactmail"
                                    placeholder="E-mail"
                                    type="email"
                                />
                                <b-button
                                    type="submit"
                                    class="newTeamSubmit"
                                    variant="primary"
                                >
                                    Mentés
                                </b-button>
                            </b-form>
                        </b-card>
                    </b-collapse>
                </div>
                <div class="filters">
                    <div class="searchbar">
                        <b-input-group size="sm">
                            <b-form-input
                                id="filter-input"
                                v-model="filter"
                                type="search"
                                placeholder="Keresés..."
                            />

                            <b-input-group-append>
                                <b-button
                                    :disabled="!filter"
                                    @click="filter = ''"
                                >
                                    Töröl
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </div>
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
                    striped
                    hover
                    :items="teams"
                    :per-page="perPage"
                    :fields="fields"
                    :filter="filter"
                    :filter-ignored-fields="filterIgnoredFields"
                    :current-page="currentPage"
                    show-empty
                    empty-filtered-html="Nincs találat..."
                    empty-html="Nincsenek adatok..."
                    @filtered="onFiltered"
                >
                    <template #cell(actions)="row">
                        <div style="display: inline-flex;">
                            <div style="padding-right: 5px">
                                <b-button
                                    size="sm"
                                    @click="row.toggleDetails"
                                >
                                    Módosít
                                </b-button>
                            </div>
                            <div>
                                <b-button
                                    variant="danger"
                                    size="sm"
                                    @click="deleteTeam(row.item)"
                                >
                                    Töröl
                                </b-button>
                            </div>
                        </div>
                    </template>
                    <template #row-details="row">
                        <b-card>
                            <b-form
                                class="editCard"
                                @submit.stop.prevent="editTeam(row.item.edit)"
                            >
                                <div class="editGroup">
                                    <b-form-input
                                        v-model="row.item.edit.fullname"
                                        placeholder="Bejegyzett név"
                                    />
                                    <b-form-input
                                        v-model="row.item.edit.shortname"
                                        placeholder="Rövid név"
                                    />
                                    <b-form-input
                                        v-model="row.item.edit.contactname"
                                        placeholder="Kapcsolattartó"
                                    />
                                </div>
                                <div class="editGroup">
                                    <b-form-input
                                        v-model="row.item.edit.contactphone"
                                        placeholder="Telefonszám"
                                    />
                                    <b-form-input
                                        v-model="row.item.edit.contactmail"
                                        placeholder="E-mail"
                                    />
                                    <b-button
                                        class="editSubmit"
                                        type="submit"
                                        style="width: 10%; float: right;"
                                        variant="primary"
                                    >
                                        Mentés
                                    </b-button>
                                </div>
                            </b-form>
                        </b-card>
                    </template>
                </b-table>
            </div>
        </div>
    </div>
</template>

<script>
import spinnerComponent from '@/pages/common/spinner';
import { mapGetters, mapState } from 'vuex';
import { findIndex } from '@/components/helper';

export default {
    name: 'Teams',
    components: {
        spinnerComponent,
    },
    data() {
        return {
            filterIgnoredFields: ['@id', '@type', 'edit'],
            fields:
          [
              {
                  key: 'fullname',
                  label: 'Bejegyzett név',
                  sortable: true,
              },
              {
                  key: 'shortname',
                  label: 'Rövid név',
                  sortable: true,
              },
              {
                  key: 'contactname',
                  label: 'Kapcsolattartó',
                  sortable: false,
              },
              {
                  key: 'contactphone',
                  label: 'Telefonszám',
                  sortable: false,
              },
              {
                  key: 'contactmail',
                  label: 'E-mail',
                  sortable: false,
              },
              {
                  key: 'actions',
                  label: 'Akciók',
              },
          ],
            newTeamFormVisible: false,
            loading: true,
            filter: null,
            filterOn: [],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [10, 20, 50],
            newTeam: {
                fullname: '',
                shortname: '',
                contactphone: '',
                contactmail: '',
                contactname: '',
            },
        };
    },
    computed: {
        ...mapGetters({
            teams: 'teams/getTeams',
        }),
        maxPage() {
            return Math.ceil(this.totalRows / this.perPage);
        },
    },
    created() {
        this.$store.dispatch('teams/initializeTeams').then(() => {
            this.loading = false;
            this.totalRows = this.teams.length;
        });
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        deleteTeam(team) {
            this.$swal.fire({
                text: 'Biztos törölni szeretné a kiválasztott csapatot?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF0000',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Mégsem',
            }).then((choice) => {
                if (choice.isConfirmed === true) {
                    this.$store.dispatch('teams/delete', team).then(() => {
                        if (this.totalRows > 0) {
                            this.totalRows -= 1;
                        }
                        if (this.currentPage > this.maxPage) {
                            this.currentPage = this.maxPage;
                        }
                        this.$swal.fire({
                            icon: 'success',
                            title: 'Klub sikeresen törölve!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    });
                }
            }).catch(() => {});
        },
        editTeam(team) {
            this.$store.dispatch('teams/edit', team).then(() => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'A csapat adatait megváltoztattuk!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        addTeam() {
            this.$store.dispatch('teams/create', this.newTeam).then(() => {
                this.totalRows += 1;
                this.currentPage = 1;
                this.newTeamFormVisible = false;
                this.newTeam = {};
                this.$swal.fire({
                    icon: 'success',
                    title: 'Csapat sikeresen hozzáadva!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
    },
};
</script>

<style scoped>
.teamboard {
  padding-top: 2rem;
}

.paginator {
  max-width: 40%;
  float: right;
}

.filters {
  display: flex;
  flex-direction: row;
}

.sorting {
  width: 50%;
}

.searchbar {
  width: 50%;
}

.sortbox {
  width: 30%;
  float: right;
  padding-right: 1rem;
}

.newTeam {
padding-bottom: 2rem;
}

.newTeamForm {
  display: flex;
  flex-direction: column;
}

.newTeamForm input {
 margin-bottom: 1rem;
}

.editCard {
  display: flex;
  flex-direction: column;

}
.editGroup {
  display: flex;
  flex-direction: row;
}

.editSubmit, .editGroup input {
  margin: 1rem;
  max-width: 30%;
}
.newTeamSubmit {
  width: 20%;
  align-self: end;
}

</style>

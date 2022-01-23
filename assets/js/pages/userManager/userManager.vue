<template>
    <div>
        <div class="contentpanel">
            <div class="contentcard">
                <div class="usertablecontenthead">
                    <h3 class="cardtitle">
                        Felhasználók kezelése
                    </h3>
                    <new-user-component class="newusercomponent" />
                </div>
                <spinner-component v-if="users.length === 0" />
                <div
                    v-else
                    class="usermanagertable"
                >
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
                        :items="users"
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
                        <template #cell(roleDescription)="row">
                            {{ formatUserRole(row.value) }}
                        </template>
                        <template #cell(sections)="row">
                            <b-badge
                                v-for="(section, key) in row.value"
                                :key="key"
                                class="sectionBadge"
                            >
                                {{ section }}
                            </b-badge>
                        </template>
                        <template #cell(actions)="row">
                            <div style="display: inline-flex;">
                                <div style="padding-right: 5px">
                                    <b-button
                                        size="sm"
                                        @click="row.toggleDetails"
                                    >
                                        Részletek
                                    </b-button>
                                </div>
                            </div>
                        </template>
                        <!--                        <template #cell(isEnabled)="row">-->

                        <!--                        </template>-->
                        <template #row-details="row">
                            <b-card>
                                <div class="userManagerCard">
                                    <div
                                        v-if="formatUserRole(row.item.roleDescription) !== 'Szuperadmin'"
                                        class="userManagerModify"
                                    >
                                        <b-form
                                            class="newTeamForm"
                                            @submit.stop.prevent="editUser(row.item.edit)"
                                        >
                                            <div
                                                style="display: flex; margin-bottom: 1rem; flex-direction: row;"
                                            >
                                                <span style="margin-right: 10px;">Felhasználó engedélyezve</span>
                                                <b-form-checkbox
                                                    v-model="row.item.edit.isEnabled"
                                                    name="check-button"
                                                    switch
                                                />
                                            </div>
                                            <b-form-select
                                                v-model="row.item.edit.roleDescription"
                                                :options="roleOptions"
                                                class="mb-3"
                                                style="width: 70%;"
                                            />
                                            <div>
                                                <b-button
                                                    style="width: 30%;float: right;"
                                                    variant="primary"
                                                    type="submit"
                                                >
                                                    Mentés
                                                </b-button>
                                            </div>
                                        </b-form>
                                    </div>
                                    <div
                                        v-if="formatUserRole(row.item.roleDescription) !== 'Szuperadmin'"
                                        class="userManagerLogs"
                                    >
                                        Legutolsó bejelentkezés: {{ row.item.logs.latestLogin === null? 'a felhasználó még nem jelentkezett be egyszer sem' : formatLatestLogin(row.item.logs.latestLogin.actionTime) }}
                                    </div>
                                    <div
                                        v-if="formatUserRole(row.item.roleDescription) !== 'Szuperadmin'"
                                        class="userManagerActions"
                                    >
                                        <div
                                            style="display: flex;flex-direction: column;"
                                        >
                                            <div>
                                                <b-button
                                                    variant="primary"
                                                    style="margin-bottom: 1rem;float: right;"
                                                    size="sm"
                                                    :href="`mailto:${row.item.email}`"
                                                >
                                                    E-mail küldése
                                                </b-button>
                                            </div>
                                            <div>
                                                <b-button
                                                    v-if="formatUserRole(row.item.roleDescription) !== 'Szuperadmin'"
                                                    variant="danger"
                                                    size="sm"
                                                    style="float: right;
"
                                                    @click="deleteUser(row.item, '')"
                                                >
                                                    Felhasználó törlése
                                                </b-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </b-card>
                        </template>
                    </b-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import newUserComponent from '@/pages/userManager/newUser';
import spinnerComponent from '@/pages/common/spinner';
import { formatRole, getRoleByRoleDescription } from '@/components/helper';
import moment from 'moment';

export default {
    name: 'UserManager',
    components: {
        newUserComponent,
        spinnerComponent,
    },
    data() {
        return {
            filterIgnoredFields: ['@id', '@type', 'id', 'isMe', 'roles', 'createdAt', 'isEnabled', 'birthday'],
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
                      key: 'roleDescription',
                      label: 'Rang',
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
                      label: 'Akciók',
                  },
              ],
            roleOptions: [
                // { value: 'Szuperadmin', text: 'Szuperadmin' },
                { value: 'admin', text: 'Admin' },
                { value: 'edző', text: 'Edző' },
                { value: 'sportoló', text: 'Sportoló' },
            ],
            loading: true,
            filter: null,
            filterOn: [],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [10, 20, 50],
            newUser: {
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
            users: 'usermanager/getUsers',
        }),
        maxPage() {
            return Math.ceil(this.totalRows / this.perPage);
        },
    },
    watch: {
        //after initialization of the users gets the right count for the paginator
        users() {
            this.totalRows = this.users.length;
        },
    },
    async created() {
        this.$store.dispatch('usermanager/loadUsers');
        this.loading = false;
        //this.totalRows = this.users.length;
    },
    mounted() {
        this.$root.$on('registerTableEffect', () => {
            this.newUserTableEffect();
        });
    },
    methods: {
        formatLatestLogin(date) {
            return moment(date).format('Y.MM.DD. HH:mm');
        },
        newUserTableEffect() {
            this.totalRows += 1;
            this.currentPage = 1;
        },
        editUser(userEdit) {
            userEdit.roles[0] = getRoleByRoleDescription(userEdit.roleDescription);
            this.$store.dispatch('usermanager/put', userEdit).then(() => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'A felhasználó adatait megváltoztattuk!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        formatUserRole(roleDescription) {
            return formatRole(roleDescription);
        },
        onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
            this.currentPage = 1;
        },
        changeUserRole(user, value) {
            const newRole = [];
            if (value === 'sportoló') {
                newRole.push('ROLE_USER');
            } else if (value === 'edző') {
                newRole.push('ROLE_TRAINER');
            } else if (value === 'admin') {
                newRole.push('ROLE_ADMIN');
            } else {
                newRole.push('ROLE_SUPER_ADMIN');
            }
            this.$store.dispatch('usermanager/changeUserPropertyWithoutDB', { user, prop: 'roleDescription', val: value });
            this.$store.dispatch('usermanager/changeUserProperty', { user, prop: 'roles', val: newRole });
        },
        userIsEnabled(user, event) {
            const val = event.target.checked;
            this.$store.dispatch('usermanager/changeUserProperty', { user, prop: 'isEnabled', val });
        },
        deleteUser(user) {
            this.$swal.fire({
                text: 'Biztos törölni szeretné a kiválasztott felhasználót?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF0000',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Mégsem',
            }).then((choice) => {
                if (choice.isConfirmed === true) {
                    this.$store.dispatch('usermanager/delete', user).then(() => {
                        //this.setCurrentPageWhenDelete();
                        if (this.totalRows > 0) {
                            this.totalRows -= 1;
                        }
                        if (this.currentPage > this.maxPage) {
                            this.currentPage = this.maxPage;
                        }
                        this.$swal.fire({
                            icon: 'success',
                            title: 'Felhasználó sikeresen törölve!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    });
                }
            }).catch(() => {});
        },
        // setCurrentPageWhenDelete() {
        //     if (this.currentPage > Math.ceil(this.users.length / this.perPage)) {
        //         this.currentPage = Math.ceil(this.users.length / this.perPage);
        //     }
        // },
    },

};
</script>

<style scoped>
.newusercomponent {
  margin-bottom: 1rem;
}
.usermanagertable {
  margin-top: 1.5rem;
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

.sectionBadge {
  padding: 5px;
  margin: 2px
}

.userManagerCard {
  display: flex;
  flex-direction: row;
}

.userManagerModify, .userManagerActions, .userManagerLogs {

  display: flex;
  flex-direction: column;
}

.userManagerModify {
  width: 30%;
}

.userManagerLogs {
  width: 50%;
}

.userManagerActions {
  width: 20%;
}

</style>

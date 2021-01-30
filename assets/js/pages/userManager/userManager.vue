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
                    <vs-table
                        v-model="selected"
                    >
                        <template #header>
                            <vs-input
                                v-model="search"
                                border
                                placeholder="Keresés"
                            />
                        </template>
                        <template #thead>
                            <vs-tr>
                                <vs-th
                                    sort
                                    @click="users = $vs.sortData($event ,users, 'name')"
                                >
                                    Név
                                </vs-th>
                                <vs-th
                                    sort
                                    @click="users = $vs.sortData($event ,users, 'email')"
                                >
                                    E-mail
                                </vs-th>
                                <vs-th
                                    sort
                                    @click="users = $vs.sortData($event ,users, 'roleDescription')"
                                >
                                    Rang
                                </vs-th>
                                <vs-th
                                    sort
                                    @click="users = $vs.sortData($event ,users, 'phone')"
                                >
                                    Telefonszám
                                </vs-th>
                                <vs-th
                                    sort
                                    @click="users = $vs.sortData($event ,users, 'isEnabled')"
                                >
                                    Aktivált
                                </vs-th>
                            </vs-tr>
                        </template>
                        <template #tbody>
                            <vs-tr
                                v-for="(tr, i) in $vs.getPage($vs.getSearch(users, search), page, max)"
                                :key="i"
                                :data="tr"
                                :is-selected="!!selected.includes(tr)"
                                not-click-selected
                                open-expand-only-td
                            >
                                <vs-td>
                                    {{ tr.name }}
                                </vs-td>
                                <vs-td>
                                    {{ tr.email }}
                                </vs-td>
                                <vs-td
                                    edit
                                    @click="edit = tr, editProp = 'roleDescription', editActive = true"
                                >
                                    <h6>
                                        <b-badge variant="primary">
                                            {{ tr.roleDescription.toLowerCase() }}
                                        </b-badge>
                                    </h6>
                                </vs-td>
                                <vs-td>
                                    {{ tr.phone }}
                                </vs-td>
                                <vs-td>
                                    <vs-switch
                                        :value="tr.isEnabled"
                                        style="width: 4rem;"
                                        @click="userIsEnabled(tr, $event)"
                                    />
                                </vs-td>

                                <template
                                    #expand
                                >
                                    <div
                                        :ref="tr.email"
                                        class="con-content"
                                    >
                                        <div class="usermanageraction">
                                            <vs-button
                                                border
                                                :href="'mailto:' + tr.email"
                                            >
                                                E-mail küldés
                                            </vs-button>
                                            <vs-button
                                                border
                                                danger
                                                @click="deleteUser(tr, $event)"
                                            >
                                                Törlés
                                            </vs-button>
                                        </div>
                                    </div>
                                </template>
                            </vs-tr>
                        </template>
                        <template #footer>
                            <vs-pagination
                                v-model="page"
                                :length="$vs.getLength($vs.getSearch(users, search), max)"
                                @input="closeCollapses"
                            />
                        </template>
                        <template #notFound>
                            <i>
                                Nincs találat...
                            </i>
                        </template>
                    </vs-table>

                    <vs-dialog v-model="editActive">
                        <template #header>
                            Rang megváltoztatása
                        </template>
                        <!--                            v-if="editProp == 'roles'"-->
                        <!--                            v-model="edit[editProp]"-->
                        <vs-select
                            v-if="editProp == 'roleDescription'"
                            :value="edit[editProp]"
                            block
                            placeholder="..."
                            @change="editActive = false"
                            @input="changeUserRole(edit, $event)"
                        >
                            <vs-option
                                label="Felhasználó"
                                value="felhasználó"
                            >
                                Felhasználó
                            </vs-option>
                            <vs-option
                                label="Admin"
                                value="admin"
                            >
                                Admin
                            </vs-option>
                            <vs-option
                                label="Szuperadmin"
                                value="szuperAdmin"
                            >
                                Szuperadmin
                            </vs-option>
                        </vs-select>
                    </vs-dialog>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import newUserComponent from '@/pages/userManager/newUser';
import spinnerComponent from '@/pages/common/spinner';

export default {
    name: 'UserManager',
    components: {
        newUserComponent,
        spinnerComponent,
    },
    data() {
        return {
            editActive: false,
            edit: null,
            editProp: {},
            search: '',
            allCheck: false,
            page: 1,
            max: 10,
            active: 0,
            selected: [],
            users: [],
        };
    },
    computed: {
        ...mapGetters({
            userstore: 'usermanager/getUsers',
        }),
    },
    async created() {
        await this.$store.dispatch('usermanager/loadUsers');
        //a SORT function csak úgy működik, ha a user = data
        this.users = this.userstore;
    },
    methods: {
        closeCollapses() {
            //ha ez nem fut le, akkor a kinyitott elemek megjelennek a következő oldalon...
            $('.vs-table__tr__expand').hide();
        },
        changeUserRole(user, value) {
            const newRole = [];
            if (value === 'felhasználó') {
                newRole.push('ROLE_USER');
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
        deleteUser(user, e) {
            let test = e.currentTarget;
            $('.vs-table__tr__expand');
            this.$swal.fire({
                text: 'Biztos törölni szeretné a kiválasztott felhasználót?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF0000',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Mégsem',
            }).then((choice) => {
                if (choice.isConfirmed === true) {
                    this.$store.dispatch('usermanager/deleteUser', user).then(() => {
                        this.setCurrentPageWhenDelete();
                        test = test.closest('.vs-table__tr__expand');
                        $(test).remove();
                        this.users = this.userstore;
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
        setCurrentPageWhenDelete() {
            if (this.page > Math.ceil(this.userstore.length / this.max)) {
                this.page = Math.ceil(this.userstore.length / this.max);
            }
        },
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
.usermanageraction {
  display: flex;
  float: inline-end;
  flex-direction: column;
}

</style>

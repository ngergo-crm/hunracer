<template>
    <div>
        <div class="contentcentral">
            <div class="contentcard">
                <div class="cardtitle">
                    <h4>
                        Felhasználói adatok
                    </h4>
                    <div
                        v-show="account.roles"
                        style="margin-left: 10px"
                    >
                        <b-badge
                            variant="warning"
                            style="float: right"
                        >
                            Admin
                        </b-badge>
                    </div>
                </div>
                <div class="isMeinfo">
                    <vs-input
                        v-model="account.email"
                        type="email"
                        class="isMeField"
                        label-placeholder="E-mail"
                        readonly
                        style="width: 100%"
                    />
                    <vs-input
                        v-model="name"
                        class="isMeField"
                        label-placeholder="Név*"
                    />
                    <vs-input
                        v-model="phone"
                        class="isMeField"
                        label-placeholder="Telefonszám"
                    />
                    <vs-input
                        v-if="account.roleDescription === 'edző'"
                        v-model="trainerCode"
                        class="isMeField"
                        label-placeholder="Edző-kód"
                        readonly
                    />
                    <vs-input
                        v-else-if="account.roleDescription === 'sportoló'"
                        v-model="trainerCode"
                        class="isMeField"
                        label-placeholder="Edző-kód"
                    />
                </div>
                <div style="display: flex; flex-direction: column;">
                    <b-button
                        class="hunracerButton"
                        style="margin-bottom: 1rem;"
                        @click="saveUserData"
                    >
                        Mentés
                    </b-button>
                    <b-button
                        v-b-modal.password-change
                        variant="secondary"
                    >
                        Jelszó megváltoztatása
                    </b-button>
                </div>
            </div>
        </div>
        <change-password-component />
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import changePasswordComponent from '@/pages/account/changePassword';

export default {
    name: 'Account',
    components: {
        changePasswordComponent,
    },
    computed: {
        ...mapGetters({
            account: 'account/getUser',
        }),
        name: {
            get() {
                return this.$store.state.account.user.name;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'name', value });
            },
        },
        phone: {
            get() {
                return this.$store.state.account.user.phone;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'phone', value });
            },
        },
        trainerCode: {
            get() {
                return this.$store.state.account.trainerCode;
            },
            set(value) {
                this.$store.commit('account/setTrainerCode', value);
            },
        },
    },
    async created() {
        await this.$store.dispatch('account/initializeUser');
    },
    methods: {
        saveUserData() {
            this.$store.dispatch('account/modifyUser').then(() => {
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

</style>

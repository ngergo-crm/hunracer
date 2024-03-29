<template>
    <div>
        <div class="contentcentral">
            <div
                class="contentcard"
            >
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
                    <multiselect
                        v-if="genders.length > 0 && account.roleDescription === 'sportoló'"
                        v-model="userGender"
                        style="margin-bottom: 1rem;"
                        :options="genders"
                        track-by="description"
                        label="description"
                        placeholder="Fiú/ lány..."
                        selected-label="kiválasztva"
                        deselect-label="Nem eltávolítása"
                        select-label="kiválasztás"
                    />
                    <vs-input
                        v-if="account.roleDescription === 'sportoló'"
                        v-model="birthday"
                        type="date"
                        class="isMeField"
                        label-placeholder="Születési idő"
                    />
                    <p v-if="account.roleDescription === 'sportoló'">
                        Besorolás: {{ uRating }}
                    </p>
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
                    <multiselect
                        v-if="sections.length > 0 && account.roleDescription === 'sportoló'"
                        v-model="userSections"
                        style="margin-bottom: 1rem;"
                        :options="sections"
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
                    <p
                        v-if="account.roleDescription === 'edző' && !account.team"
                        style="color: red"
                    >
                        Edzőként kötelező csapatot megadni!
                    </p>
                    <multiselect
                        v-if="teams.length > 0 && (account.roleDescription === 'sportoló' || account.roleDescription === 'edző')"
                        v-model="userTeam"
                        style="margin-bottom: 1rem;"
                        :options="teams"
                        track-by="shortname"
                        label="shortname"
                        placeholder="Csapat választása..."
                        selected-label="kiválasztva"
                        deselect-label="eltávolítás"
                        select-label="kiválasztás"
                        no-result="Nincs találat."
                    >
                        <template>
                            <span slot="noResult">Nincs találat.</span>
                        </template>
                    </multiselect>
                    <div v-if="account.roleDescription === 'sportoló'">
                        <label>Profilkép:</label>
                        <b-form-file
                            v-if="!photo"
                            v-model="img"
                            size="sm"
                            :placeholder="img? selectedImgName : 'Profilkép feltöltése...'"
                            browse-text="Kiválaszt"
                            accept="image/*"
                            @change="insertImg"
                        />
                        <div
                            v-else
                            style="display: flex; justify-content: space-between;"
                        >
                            <p>
                                Kép: {{ photo }}
                            </p>
                            <b-button
                                size="sm"
                                @click="deletePhoto"
                            >
                                Fájl törlése
                            </b-button>
                        </div>
                    </div>
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
import { mapGetters, mapState } from 'vuex';
import changePasswordComponent from '@/pages/account/changePassword';
import moment from 'moment';
import { calculateURating } from '@/components/helper';
import multiselect from 'vue-multiselect';

export default {
    name: 'Account',
    components: {
        changePasswordComponent,
        multiselect,
    },
    data() {
        return {
            img: null,
            selectedTeam: null,
            selectedImgName: null,
            selectedImgValue: null,
        };
    },
    computed: {
        ...mapGetters({
            // sections: 'account/getSections',
            account: 'account/getUser',
            // teams: 'account/getTeams',
        }),
        ...mapState({
            genders: (state) => state.account.genders,
            teams: (state) => state.account.teams,
            sections: (state) => state.account.sections,
            startWorkoutYear: (state) => state.settings.workoutYearStart.settingValue,
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
        birthday: {
            get() {
                let birtday = null;
                if (moment(this.$store.state.account.user.birthday).format('YYYY-MM-DD') !== 'Invalid date') {
                    birtday = moment(this.$store.state.account.user.birthday).format('YYYY-MM-DD');
                }
                return birtday;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'birthday', value });
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
        userSections: {
            get() {
                return this.$store.state.account.user.sections;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'sections', value });
            },
        },
        userTeam: {
            get() {
                return this.$store.state.account.user.team;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'team', value });
            },
        },
        userGender: {
            get() {
                return this.$store.state.account.user.gender;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'gender', value });
            },
        },
        photo: {
            get() {
                return this.$store.state.account.user.photo;
            },
            set(value) {
                this.$store.commit('account/updateUser', { key: 'photo', value });
            },
        },
        uRating() {
            return calculateURating(this.birthday, 'nincs');
        },
    },
    async created() {
        await this.$store.dispatch('account/initializeUser');
    },
    methods: {
        saveUserData() {
            this.$store.dispatch('account/modifyUser').then(() => {
                this.img = null;
                this.selectedImgName = null;
                this.selectedImgValue = null;
                this.$swal.fire({
                    icon: 'success',
                    title: 'Változások elmentve!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        deletePhoto() {
            this.$swal.fire({
                text: 'Biztosan ki szeretné a képet törölni?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF0000',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Mégsem',
            }).then((choice) => {
                if (choice.isConfirmed === true) {
                    this.$store.dispatch('account/deletePhoto').then(() => {
                        this.$swal.fire({
                            icon: 'success',
                            title: 'Fájl sikeresen eltávolítva!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    });
                }
            }).catch(() => {});
        },
        insertImg(img) {
            if (img.target.files[0]) {
                this.selectedImgName = img.target.files[0].name;
                const reader = new FileReader();
                reader.onload = (res) => {
                    this.selectedImgValue = res.target.result;
                    this.$store.commit('account/setNewImages', {
                        img: this.selectedImgValue,
                        imgName: this.selectedImgName,
                    });
                };
                reader.onerror = (err) => console.log(err);
                reader.readAsDataURL(img.target.files[0]);
            }
        },
    },
};
</script>

<style scoped>
/*https://vue-multiselect.js.org/#sub-getting-started*/
/deep/ .multiselect__tag {
  background-color: gray;
}
/deep/ .multiselect__tag-icon:hover {
  background-color: red;
}
/deep/ .multiselect__tag-icon {
  color: white;
}

/deep/ .multiselect__tag-icon::after {
  color: white;
}

</style>

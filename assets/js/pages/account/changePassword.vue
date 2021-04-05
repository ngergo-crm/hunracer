<template>
    <div>
        <b-modal
            id="password-change"
            ref="modal"
            title="Jelszó megváltoztatása"
        >
            <form
                ref="form"
            >
                <vs-input
                    v-model="currentPassword"
                    class="isMeField"
                    label-placeholder="Régi jelszó"
                    type="password"
                />
                <vs-input
                    v-model="newPassword"
                    class="isMeField"
                    label-placeholder="Új jelszó"
                    type="password"
                />
            </form>
            <template #modal-footer>
                <b-button
                    @click="closeModal"
                >
                    Mégsem
                </b-button>
                <b-button
                    class="hunracerButton"
                    @click="changePassword"
                >
                    Mentés
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: 'ChangePassword',
    data() {
        return {
            currentPassword: '',
            newPassword: '',
        };
    },
    methods: {
        closeModal() {
            this.$root.$emit('bv::hide::modal', 'password-change');
        },
        changePassword() {
            this.$store.dispatch('account/changeUserPassword', { newPassword: this.newPassword, currentPassword: this.currentPassword })
                .then(() => {
                    this.closeModal();
                    this.newPassword = '';
                    this.currentPassword = '';
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Jelszó sikeresen megváltoztatva!',
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

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
                    v-model="passwordOld"
                    class="isMeField"
                    label-placeholder="Régi jelszó"
                    type="password"
                />
                <vs-input
                    v-model="passwordNew"
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
                <b-button class="hunracerButton">
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
            passwordOld: '',
            passwordNew: '',
        };
    },
    methods: {
        closeModal() {
            this.$root.$emit('bv::hide::modal', 'password-change');
        },
    },
};
</script>

<style scoped>

</style>

<template>
    <div>
        <div class="newuserbtn">
            <b-button
                variant="primary"
                @click="collapsed"
            >
                Regisztráció
            </b-button>
            <i
                v-b-popover.hover.left="'Adja meg az új felhasználó adatait és kattintson a Létrehoz gombra. A megadott e-mail címre elküld a rendszer egy generált jelszót.'"
                class="far fa-question-circle"
                title="Regisztráció"
            />
        </div>
        <b-collapse
            :visible="registrationVisible"
            class="mt-2"
        >
            <b-card>
                <form>
                    <div class="newusercontent">
                        <div>
                            <vs-input
                                v-model="name"
                                class="isMeField"
                                label-placeholder="Név*"
                            />
                        </div>
                        <div>
                            <vs-input
                                v-model="email"
                                type="email"
                                class="isMeField"
                                label-placeholder="E-mail*"
                                style="width: 100%"
                            />
                        </div>
                        <div>
                            <vs-select
                                v-model="roles"
                                label-placeholder="Rang*"
                            >
                                <vs-option
                                    label="Sportoló"
                                    value="ROLE_USER"
                                >
                                    Sportoló
                                </vs-option>
                                <vs-option
                                    label="Edző"
                                    value="ROLE_TRAINER"
                                >
                                    Edző
                                </vs-option>
                                <vs-option
                                    label="Admin"
                                    value="ROLE_ADMIN"
                                >
                                    Admin
                                </vs-option>
                            </vs-select>
                        </div>
                        <div>
                            <b-button
                                variant="primary"
                                @click="createUser"
                            >
                                Létrehoz
                            </b-button>
                        </div>
                    </div>
                </form>
            </b-card>
        </b-collapse>
    </div>
</template>

<script>

export default {
    name: 'NewUser',
    data() {
        return {
            registrationVisible: false,
        };
    },
    computed: {
        name: {
            get() {
                return this.$store.state.usermanager.user.name;
            },
            set(value) {
                this.$store.commit('usermanager/updateUser', { key: 'name', value });
            },
        },
        email: {
            get() {
                return this.$store.state.usermanager.user.email;
            },
            set(value) {
                this.$store.commit('usermanager/updateUser', { key: 'email', value });
            },
        },
        roles: {
            get() {
                return this.$store.state.usermanager.user.roles;
            },
            set(value) {
                this.$store.commit('usermanager/updateUser', { key: 'roles', value });
            },
        },
    },
    methods: {
        collapsed() {
            this.registrationVisible = !this.registrationVisible;
        },
        createUser() {
            this.$store.dispatch('usermanager/post').then(() => {
                this.$root.$emit('registerTableEffect');
                this.collapsed();
                this.$swal.fire({
                    icon: 'success',
                    title: 'Sikeres regisztráció!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
    },
};
</script>

<style scoped>
.newuserbtn {
  display: flex;
  align-items: flex-start;
flex-direction: row-reverse;
}
.newuserbtn i {
  margin-right: 0.5rem;
}
.newusercontent {
 display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: center;
}
.newusercontent div{
  margin: 0.5rem;
}
</style>

<template>
    <div
        v-if="!tokenAvailable"
        class="loginTP"
    >
        <b-button

            variant="primary"
            @click="loginTP"
        >
            Training Peaks bejelentkezés
        </b-button>
    </div>
    <div
        v-else
        class="tpauth"
    >
        <b-button
            @click="deauthorizeTpProfile"
        >
            Training Peaks kijelentkezés
        </b-button>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    name: 'TrainingPeaksAuth',
    computed: {
        ...mapState({
            tokenAvailable: (state) => state.trainingPeaksHandler.tokenAvailable,
            trainingPeaksLink: (state) => state.trainingPeaksHandler.trainingPeaksLink,
        }),
    },
    methods: {
        loginTP() {
            window.open(this.trainingPeaksLink, '_blank');
        },
        deauthorizeTpProfile() {
            this.$store.dispatch('trainingPeaksHandler/deauthorizeTpProfile').then(() => {
                this.$store.dispatch('trainingPeaksHandler/getTrainingPeaksLoginLink');
            });
        },
    },
};
</script>

<style scoped>
.tpauth {
  display: flex;
  justify-content: flex-start;
}
.loginTP {
  display: flex;
  justify-content: flex-start;
}
</style>

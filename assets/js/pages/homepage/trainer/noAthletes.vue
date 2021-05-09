<template>
    <div>
        <p>Jelenleg még egy sportoló sem használja az edzői profilhoz tartozó edzőkódot: <strong>{{ user.trainerCode }}</strong></p>
        <i>A sportolók adatai azután jelennek meg az edzői felületen, miután beállították a kódot a profiljukban!</i>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    name: 'NoAthletes',
    computed: {
        ...mapState({
            user: (state) => state.trainingPeaksHandler.user,
        }),
    },
};
</script>

<style scoped>

</style>

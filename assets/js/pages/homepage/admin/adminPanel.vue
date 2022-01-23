<template>
    <div>
        <div class="performanceBanner">
            <h4>Teljesítményadatok elemzése</h4>
        </div>
        <admin-filters />
        <admin-user-selection :using-place="'home'" />
        <athlete-performance-chart-component v-if="performanceData.length > 0" />
    </div>
</template>

<script>
import AdminFilters from '@/pages/homepage/admin/adminFilters';
import { mapState } from 'vuex';
import AthletePerformanceChartComponent from '@/pages/homepage/admin/athletePerformanceChart';
import AdminUserSelection from '@/pages/homepage/admin/adminUserSelection';

export default {
    name: 'AdminPanel',
    components: { AdminUserSelection, AthletePerformanceChartComponent, AdminFilters },
    computed: {
        ...mapState({
            performanceData: (state) => state.adminHomepage.athletePerformance,
        }),
    },
};
</script>

<style scoped>
.performanceBanner{
  padding-top: 2rem;
  padding-bottom: 1rem;
}
</style>

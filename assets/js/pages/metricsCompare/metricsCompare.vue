<template>
    <div>
        <div class="contentpanel">
            <div class="contentcard">
                <admin-filters />
                <admin-user-selection :using-place="'metricsCompare'" />
                <metrics-compare-charts />
            </div>
        </div>
    </div>
</template>

<script>
import AdminFilters from '@/pages/homepage/admin/adminFilters';
import AdminUserSelection from '@/pages/homepage/admin/adminUserSelection';
import MetricsCompareCharts from '@/pages/metricsCompare/metricsCompareCharts';

export default {
    name: 'MetricsCompare',
    components: { MetricsCompareCharts, AdminUserSelection, AdminFilters },
    created() {
        this.$store.dispatch('metricsCompare/initializeMetricCompare');
    },
};
</script>

<style scoped>

</style>

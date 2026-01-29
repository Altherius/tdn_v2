import { useAppearance } from '@/composables/useAppearance';
import { computed } from 'vue';

export function usePieChartOptions() {
    const { resolvedAppearance } = useAppearance();

    const pieChartOptions = computed(() => {
        const isDark = resolvedAppearance.value === 'dark';
        const textColor = isDark ? '#A1A09A' : '#706f6c';

        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom' as const,
                    labels: {
                        color: textColor,
                        padding: 16,
                    },
                },
            },
        };
    });

    return { pieChartOptions };
}

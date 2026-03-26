import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

export function updateTheme(_value: Appearance) {
    // Dark mode is disabled globally; always render light mode.
    document.documentElement.classList.remove('dark');
}

const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

const handleSystemThemeChange = () => {
    updateTheme('light');
};

export function initializeTheme() {
    localStorage.setItem('appearance', 'light');
    updateTheme('light');

    // Listener kept for compatibility; it always re-applies light mode.
    mediaQuery.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance() {
    const appearance = ref<Appearance>('light');

    onMounted(() => {
        initializeTheme();
        appearance.value = 'light';
    });

    function updateAppearance(_value: Appearance) {
        appearance.value = 'light';
        localStorage.setItem('appearance', 'light');
        updateTheme('light');
    }

    return {
        appearance,
        updateAppearance,
    };
}

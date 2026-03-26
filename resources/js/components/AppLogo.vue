<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type SettingApps = {
    app_name: string;
    logo_url: string | null;
};

const page = usePage<{ appSetting: SettingApps }>();
const appSetting = computed(() => page.props.appSetting);
</script>

<template>
    <img v-if="appSetting.logo_url" :src="appSetting.logo_url" alt="App Logo" class="h-10 w-10 rounded-full object-cover" />
    <div v-else class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none uppercase">{{ appSetting.app_name }}</span>
    </div>
</template>

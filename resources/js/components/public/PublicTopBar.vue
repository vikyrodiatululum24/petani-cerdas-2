<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    HeartHandshake,
    Home,
    IdCard,
    LayoutDashboard,
    Leaf,
    LogIn,
    LogOut,
    MessageCircleMore,
    MessagesSquare,
    Newspaper,
    ShoppingBag,
    User,
    UserPlus,
    Video,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Button from '../ui/button/Button.vue';

type TopBarPageProps = {
    auth?: {
        user?: {
            name?: string;
            email?: string;
        } | null;
        roles?: string[];
    };
    chatQuota?: {
        max: number;
        used: number;
        remaining: number;
    } | null;
    appSetting?: {
        app_name: string;
        brand_name: string;
        app_description: string;
        logo_url: string | null;
    };
};

const page = usePage<TopBarPageProps>();
const authUser = computed(() => page.props.auth?.user ?? null);
const userRoles = computed(() => page.props.auth?.roles ?? []);
const isPetani = computed(() => userRoles.value.includes('petani'));
const appSetting = computed(() => page.props.appSetting ?? null);

const navLinks = [
    { label: 'Beranda', href: '/', icon: Home },
    { label: 'Artikel', href: '/articles', icon: Newspaper },
    { label: 'Produk', href: '/products', icon: ShoppingBag },
    { label: 'Layanan', href: '/layanan', icon: HeartHandshake },
];

const dropdownMenu = [
    { label: 'Chat', href: '/chat', icon: MessagesSquare },
    { label: 'Live Session', href: '/live-sessions', icon: Video },
];

const isActive = (href: string) => {
    const url = page.url;
    if (href === '/') return url === '/';
    return url.startsWith(href);
};

const handleLogout = () => {
    router.flushAll();
};

const initials = computed(() => {
    if (!authUser.value?.name) return '';
    return authUser.value.name
        .split(' ')
        .slice(0, 2)
        .map((w: string) => w[0])
        .join('')
        .toUpperCase();
});
</script>

<template>
    <header class="fixed left-0 right-0 top-0 z-50 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex shrink-0 items-center gap-2">
                    <img v-if="appSetting?.logo_url" :src="appSetting.logo_url" alt="Logo" class="h-10 w-10 object-contain" />
                    <div v-else class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2E7D32]">
                        <Leaf class="h-4 w-4 text-white" />
                    </div>
                    <h1 class="text-lg font-bold text-[#1b1b18]">Petani<span class="text-[#4CAF50]">Cerdas</span></h1>
                    <!-- <span class="text-base font-bold leading-tight text-[#1b1b18]">{{ appSetting?.brand_name || 'PetaniCerdas' }}</span> -->
                </Link>

                <!-- Desktop navigation links (hidden on mobile) -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="isActive(link.href) ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <component :is="link.icon" class="h-4 w-4" />
                        {{ link.label }}
                    </Link>
                    <!-- Dropdown menu items for authenticated users -->
                    <DropdownMenu v-if="authUser">
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900"
                            >
                                <MessageCircleMore class="h-4 w-4" />
                                Komunikasi
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="start" :side-offset="8" class="w-48 rounded-xl shadow-lg">
                            <DropdownMenuItem as-child v-for="item in dropdownMenu" :key="item.href">
                                <Link :href="item.href" class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm">
                                    <component :is="item.icon" class="h-4 w-4" />
                                    {{ item.label }}
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </nav>

                <!-- Right side: desktop auth buttons + profile icon (all sizes) -->
                <div class="flex items-center gap-2">
                    <!-- Desktop auth buttons: only shown on md+ when guest -->
                    <template v-if="!authUser">
                        <Link
                            href="/login"
                            class="hidden items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 md:inline-flex"
                        >
                            <LogIn class="h-4 w-4" />
                            Masuk
                        </Link>
                        <Link
                            href="/pendaftaran"
                            class="hidden items-center gap-1.5 rounded-lg bg-[#2E7D32] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#256427] md:inline-flex"
                        >
                            <UserPlus class="h-4 w-4" />
                            Daftar Gratis
                        </Link>
                    </template>

                    <!-- Profile icon dropdown (always visible) -->
                    <div class="flex items-center gap-0.5">
                        <div class="md:hidden flex items-center gap-0.5">
                            <Link
                                v-for="link in dropdownMenu"
                                :key="link.href"
                                :href="link.href"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                :class="isActive(link.href) ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                            >
                                <component :is="link.icon" class="h-4 w-4" />
                            </Link>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <button class="flex items-center gap-2" :class="!authUser ? 'md:hidden' : ''"

                                    aria-label="Menu profil"
                                >
                                    <!-- Initials if logged in, icon if guest -->
                                     <div class="flex h-9 w-9 items-center justify-center rounded-full transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2E7D32] focus-visible:ring-offset-2"
                                    :class="
                                        authUser
                                            ? 'bg-[#2E7D32] text-white shadow-sm hover:bg-[#256427]'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    ">
                                         <span v-if="authUser && initials" class="text-xs font-bold">{{ initials }}</span>
                                         <User v-else class="h-4 w-4" />
                                     </div>

                                    <p class="hidden lg:flex truncate text-sm font-semibold text-gray-900">
                                        {{ authUser?.name || 'Akun' }}
                                    </p>
                                </button>
                            </DropdownMenuTrigger>

                            <DropdownMenuContent align="end" :side-offset="8" class="w-56 rounded-xl shadow-lg">
                                <!-- Logged in state -->
                                <template v-if="authUser">
                                    <DropdownMenuLabel class="p-0 font-normal">
                                        <div class="flex items-center gap-3 px-3 py-2.5">
                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#2E7D32] text-xs font-bold text-white"
                                            >
                                                {{ initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-gray-900">
                                                    {{ authUser.name }}
                                                </p>
                                                <p class="truncate text-xs text-gray-500">
                                                    {{ authUser.email }}
                                                </p>
                                            </div>
                                        </div>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuGroup>
                                        <DropdownMenuItem v-if="!isPetani" as-child>
                                            <Link href="/dashboard" class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm">
                                                <LayoutDashboard class="h-4 w-4 text-[#2E7D32]" />
                                                Dashboard
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-if="isPetani" as-child>
                                            <Link href="/petani/profile" class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm">
                                                <IdCard class="h-4 w-4 text-gray-500" />
                                                Profil Petani
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuGroup>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem as-child>
                                        <Link
                                            href="/logout"
                                            as="button"
                                            method="post"
                                            class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm text-red-600"
                                            @click="handleLogout"
                                        >
                                            <LogOut class="h-4 w-4" />
                                            Keluar
                                        </Link>
                                    </DropdownMenuItem>
                                </template>

                                <!-- Guest state -->
                                <template v-else>
                                    <DropdownMenuLabel class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Akun
                                    </DropdownMenuLabel>
                                    <DropdownMenuGroup>
                                        <DropdownMenuItem as-child>
                                            <Link href="/login" class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm">
                                                <LogIn class="h-4 w-4 text-[#2E7D32]" />
                                                Masuk
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem as-child>
                                            <Link
                                                href="/register"
                                                class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm font-medium text-[#2E7D32]"
                                            >
                                                <UserPlus class="h-4 w-4" />
                                                Daftar Gratis
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuGroup>
                                </template>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

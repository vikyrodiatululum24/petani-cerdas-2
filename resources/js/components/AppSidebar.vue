<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BadgeDollarSign, BookOpen, Building2, Folder, LayoutGrid, MessagesSquare, Newspaper, Store, Users, CheckSquare, WalletCards, Video, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const adminMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Articles',
        href: '/admin/articles',
        icon: Newspaper,
    },
    {
        title: 'Products',
        href: '/admin/products',
        icon: Store,
    },
    {
        title: 'Sponsors',
        href: '/sponsors',
        icon: Building2,
    },
    {
        title: 'Subscription Plans',
        href: '/subscription-plans',
        icon: BadgeDollarSign,
    },
    {
        title: 'Subscriptions',
        href: '/subscriptions',
        icon: WalletCards,
    },
    {
        title: 'Review Subscription',
        href: '/admin/subscriptions/payments',
        icon: CheckSquare,
    },
    {
        title: 'Users',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Chat Inbox',
        href: '/admin/chat',
        icon: MessagesSquare,
    },
    {
        title: 'Live Sessions',
        href: '/admin/live-sessions',
        icon: Video,
    },
    {
        title: 'Setting App',
        href: '/setting-app',
        icon: Settings,
    },
];

const mentorMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Articles',
        href: '/admin/articles',
        icon: Newspaper,
    },
    {
        title: 'Chat Inbox',
        href: '/admin/chat',
        icon: MessagesSquare,
    },
    {
        title: 'Live Sessions',
        href: '/admin/live-sessions',
        icon: Video,
    },
];

const page = usePage<SharedData & Record<string, unknown>>();

const userRoles = computed(() => page.props.auth.roles ?? []);

const mainNavItems = computed<NavItem[]>(() => {
    if (userRoles.value.includes('admin')) {
        return adminMainNavItems;
    }

    if (userRoles.value.includes('mentor')) {
        return mentorMainNavItems;
    }

    return [];
});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

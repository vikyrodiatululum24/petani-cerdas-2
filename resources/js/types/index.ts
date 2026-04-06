import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    roles?: string[];
    subscriptions?: {
        active: boolean;
        plan_name: string | null;
    } | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    appSetting?: {
        app_name: string;
        brand_name: string;
        app_description: string;
        logo_url: string | null;
        contact_email: string | null;
        contact_phone: string | null;
        contact_address: string | null;
        instagram_url: string | null;
        youtube_url: string | null;
        facebook_url: string | null;
        tiktok_url: string | null;
    };
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

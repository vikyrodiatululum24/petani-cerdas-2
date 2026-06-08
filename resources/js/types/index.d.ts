declare module 'aos' {
    type AosEasing = 'linear' | 'ease' | 'ease-in' | 'ease-out' | 'ease-in-out' | 'ease-in-back' | 'ease-out-back' | 'ease-in-out-back' | string;

    interface AosOptions {
        offset?: number;
        delay?: number;
        duration?: number;
        easing?: AosEasing;
        once?: boolean;
        mirror?: boolean;
        anchorPlacement?: string;
        disable?: boolean | 'phone' | 'tablet' | 'mobile' | ((...args: unknown[]) => boolean);
    }

    interface AosInstance {
        init(options?: AosOptions): void;
        refresh(): void;
        refreshHard(): void;
    }

    const AOS: AosInstance;
    export default AOS;
}

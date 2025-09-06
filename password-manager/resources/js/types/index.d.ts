import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    preferences?: {
        sidebar_collapsed?: boolean;
        sidebar_docked?: boolean;
        dark_mode?: boolean;
        [key: string]: any;
    };
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};

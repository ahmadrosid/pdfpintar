export interface Document {
    path: string;
    title: string;
    id: number;
    status?: string;
    created_at: string;
}

export interface Chat {
    id: string;
    title: string;
    user: User;
    document?: Document;
    created_at: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
};

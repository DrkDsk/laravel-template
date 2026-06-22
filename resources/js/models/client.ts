export interface Client {
    id: number;
    name: string;
    last_name: string | null;
    phone: string | null;
    email: string | null;
    rfc: string | null;
    address: string | null;
    city: string | null;
    postal_code: string | null;
    notes: string | null;
}

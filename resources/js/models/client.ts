export interface Client {
    id: number;
    name: string;
    last_name: string | null;
    phone: string | null;
    email: string | null;
    curp: string;
    notes: string | null;
}

// Standard Laravel Resource Response
export interface ApiResponse<T> {
    data: T;
    message?: string;
    meta?: any; // For pagination
    links?: any;
}

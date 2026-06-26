export type Amenity = {
    id: number;
    name: string;
    icon?: string | null;
};

export type RoomType = {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    base_capacity: number;
    max_guests: number;
    base_rate: string | number;
    max_extra_mattresses: number;
    extra_guest_rate: string | number;
    extra_mattress_rate: string | number;
    allows_extra_guests: boolean;
    amenities?: Amenity[];
    rooms?: Room[];
    rooms_count?: number;
};

export type Room = {
    id: number;
    number: string;
    floor?: string | null;
    status: string;
    room_type?: RoomType;
    roomType?: RoomType;
};

export type Quote = {
    subtotal: number;
    discount_total: number;
    tax_total: number;
    grand_total: number;
    nights: number;
    nightly_rate: number;
    room_total: number;
    extra_guest_total: number;
    extra_mattress_total: number;
};

export type AvailableRoom = Room & {
    room_type: RoomType;
    quote: Quote;
};

export type Guest = {
    id: number;
    first_name: string;
    last_name: string;
    full_name?: string;
    email?: string | null;
    phone?: string | null;
};

export type ReservationRoom = {
    id: number;
    nightly_rate: string | number;
    line_total: string | number;
    room: Room;
    room_type?: RoomType;
};

export type Reservation = {
    id: number;
    code: string;
    check_in: string;
    check_out: string;
    adults: number;
    children: number;
    extra_guests: number;
    extra_mattresses: number;
    subtotal: string | number;
    discount_total: string | number;
    grand_total: string | number;
    status: string;
    special_requests?: string | null;
    guest: Guest;
    reservation_rooms?: ReservationRoom[];
    reservationRooms?: ReservationRoom[];
};

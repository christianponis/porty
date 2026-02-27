/* ─── Paginated Response ─────────────────────────────────────── */
export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

/* ─── User ───────────────────────────────────────────────────── */
export interface User {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string | null;
  role: "admin" | "owner" | "guest";
  avatar: string | null;
  nodi_balance: number;
  is_active: boolean;
  created_at: string;
}

/* ─── Port ───────────────────────────────────────────────────── */
export interface Port {
  id: number;
  name: string;
  city: string;
  province: string;
  region: string;
  country: string;
  latitude: number;
  longitude: number;
  address: string | null;
  description: string | null;
  amenities: string[];
  image_url: string | null;
  is_active: boolean;
  total_berths?: number;
  conventions_count?: number;
  created_at?: string;
}

/* ─── Berth ──────────────────────────────────────────────────── */
export interface Berth {
  id: number;
  code: string;
  title: string;
  description: string | null;
  port: Port;
  owner: { id: number; name: string; email: string };
  length_m: number;
  width_m: number;
  max_draft_m: number;
  price_per_day: number;
  price_per_week: number | null;
  price_per_month: number | null;
  status: string;
  is_active: boolean;
  rating_level: "grey" | "blue" | "gold" | null;
  grey_anchor_count: number;
  blue_anchor_count: number;
  gold_anchor_count: number;
  review_count: number;
  review_average: number | null;
  sharing_enabled: boolean;
  nodi_value_per_day: number | null;
  images: string[];
  created_at: string;
}

/* ─── Booking ────────────────────────────────────────────────── */
export interface Booking {
  id: number;
  berth: { id: number; title: string; code: string; port?: Port };
  guest: { id: number; name: string; email: string };
  start_date: string;
  end_date: string;
  total_days: number;
  total_price: number;
  status: "pending" | "confirmed" | "cancelled" | "completed";
  booking_mode: "rental" | "sharing";
  nodi_amount: number;
  guest_notes: string | null;
  created_at: string;
}

/* ─── Review ─────────────────────────────────────────────────── */
export interface Review {
  id: number;
  berth_id: number;
  berth?: { id: number; title: string; port?: Port };
  guest: { id: number; name: string };
  rating_ormeggio: number;
  rating_servizi: number;
  rating_posizione: number;
  rating_qualita_prezzo: number;
  rating_accoglienza: number;
  average_rating: number;
  comment: string;
  is_verified: boolean;
  created_at: string;
}

/* ─── Transaction ────────────────────────────────────────────── */
export interface Transaction {
  id: number;
  booking_id: number | null;
  type: string;
  amount: number;
  currency: string;
  status: string;
  commission_rate: number | null;
  commission_amount: number | null;
  owner_amount: number | null;
  created_at: string;
}

/* ─── Convention ─────────────────────────────────────────────── */
export type ConventionCategory =
  | "commercial"
  | "technical"
  | "tourism"
  | "health"
  | "transport"
  | "other";

export type DiscountType = "percentage" | "fixed" | "free";

export interface Convention {
  id: number;
  port_id: number;
  port?: Port;
  name: string;
  description: string | null;
  category: ConventionCategory;
  category_label: string;
  address: string | null;
  phone: string | null;
  email: string | null;
  website: string | null;
  discount_type: DiscountType;
  discount_value: number | null;
  discount_description: string | null;
  logo: string | null;
  image: string | null;
  latitude: number | null;
  longitude: number | null;
  is_active: boolean;
  valid_from: string | null;
  valid_until: string | null;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

/* ─── Dashboard Stats ────────────────────────────────────────── */
export interface DashboardStats {
  total_users: number;
  total_ports: number;
  total_berths: number;
  active_berths: number;
  total_bookings: number;
  pending_bookings: number;
  total_revenue: number;
  total_nodi: number;
  recent_users: User[];
  recent_bookings: Booking[];
}

/* ─── Auth ────────────────────────────────────────────────────── */
export interface LoginResponse {
  access: string;
  user: User;
}

/* ─── Category Option ────────────────────────────────────────── */
export interface CategoryOption {
  value: string;
  label: string;
}

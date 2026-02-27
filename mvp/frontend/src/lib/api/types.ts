// ─── API Types ───────────────────────────────────────────────────────────────

export class ApiError extends Error {
  status: number;
  data: Record<string, unknown>;

  constructor(status: number, data: Record<string, unknown>) {
    super(data?.detail as string || data?.message as string || 'Errore API');
    this.status = status;
    this.data = data;
    this.name = 'ApiError';
  }
}

// ─── Auth ────────────────────────────────────────────────────────────────────

export interface User {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  name: string;
  role: 'guest' | 'owner' | 'admin';
  avatar?: string;
  phone?: string;
  nodi_balance: number;
  is_active: boolean;
  created_at: string;
}

export interface RegisterData {
  email: string;
  password: string;
  first_name: string;
  last_name: string;
  role: 'guest' | 'owner';
}

export interface LoginResponse {
  access: string;
  refresh?: string;
  user: User;
}

export interface RefreshResponse {
  access: string;
}

// ─── Ports & Berths ──────────────────────────────────────────────────────────

export interface Port {
  id: number;
  name: string;
  city: string;
  province: string;
  region: string;
  country: string;
  latitude: string;
  longitude: string;
  is_active: boolean;
  image_url: string | null;
  total_berths?: number;
}

export interface Berth {
  id: number;
  name: string;
  port: Port;
  description: string;
  length: number;
  width: number;
  max_draft: number;
  price_per_day: number;
  price_per_month: number | null;
  is_active: boolean;
  status: string;
  rating_level: 'grey' | 'blue' | 'gold' | null;
  grey_anchor_count: number | null;
  blue_anchor_count: number | null;
  gold_anchor_count: number | null;
  review_count: number;
  review_average: number | null;
  sharing_enabled: boolean;
  nodi_value_per_day: number | null;
  images: string[];
  created_at: string;
}

export interface BerthOwner {
  id: number;
  name: string;
}

export interface BerthAvailability {
  id: number;
  start_date: string;
  end_date: string;
  is_available: boolean;
  note: string | null;
}

export interface BerthDetail extends Berth {
  reviews: Review[];
  availability: BerthAvailability[];
  owner: BerthOwner;
}

// ─── Bookings ────────────────────────────────────────────────────────────────

export interface Booking {
  id: number;
  berth: Berth;
  guest: User;
  check_in: string;
  check_out: string;
  total_price: number;
  nodi_earned: number;
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
  sharing: boolean;
  boat_length: number;
  boat_name: string;
  notes?: string;
  created_at: string;
}

export interface CreateBookingData {
  berth_id: number;
  check_in: string;
  check_out: string;
  boat_length: number;
  boat_name: string;
  sharing: boolean;
  notes?: string;
}

// ─── Reviews ─────────────────────────────────────────────────────────────────

export interface Review {
  id: number;
  berth_id: number;
  guest: BerthOwner;
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

export interface CreateReviewData {
  rating_ormeggio: number;
  rating_servizi: number;
  rating_posizione: number;
  rating_qualita_prezzo: number;
  rating_accoglienza: number;
  comment: string;
}

// ─── Wallet & Transactions ───────────────────────────────────────────────────

export interface Wallet {
  id: number;
  user_id: number;
  nodi_balance: number;
  total_earned: number;
  total_spent: number;
}

export interface Transaction {
  id: number;
  wallet_id: number;
  amount: number;
  type: 'earn' | 'spend' | 'refund';
  description: string;
  booking_id?: number;
  created_at: string;
}

// ─── Assessment ──────────────────────────────────────────────────────────────

export interface Assessment {
  id: number;
  berth_id: number;
  status: 'pending' | 'in_progress' | 'completed';
  anchor_rating: number;
  anchor_level: 'grey' | 'blue' | 'gold';
  criteria: AssessmentCriterion[];
  submitted_at?: string;
  completed_at?: string;
}

export interface AssessmentCriterion {
  key: string;
  label: string;
  score: number;
  max_score: number;
}

export interface SubmitAssessmentData {
  criteria: { key: string; score: number }[];
}

// ─── Stats ───────────────────────────────────────────────────────────────────

export interface Stats {
  total_ports: number;
  total_berths: number;
  total_bookings: number;
  total_users: number;
}

export interface GuestDashboard {
  upcoming_bookings: Booking[];
  past_bookings: Booking[];
  nodi_balance: number;
  total_bookings: number;
}

export interface AdminDashboard {
  stats: Stats;
  recent_bookings: Booking[];
  recent_users: User[];
}

// ─── Pagination (Laravel format) ─────────────────────────────────────────────

export interface LaravelPagination<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
    path: string;
    links: { url: string | null; label: string; active: boolean }[];
  };
}

// ─── Search Params ───────────────────────────────────────────────────────────

export interface SearchParams {
  q?: string;
  port_id?: number;
  country?: string;
  region?: string;
  min_price?: number;
  max_price?: number;
  min_length?: number;
  max_length?: number;
  min_rating?: number;
  sharing_enabled?: boolean;
  date_from?: string;
  date_to?: string;
  sort_by?: string;
  page?: number;
}

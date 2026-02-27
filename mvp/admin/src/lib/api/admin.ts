import { get, post, put, del } from "./client";
import type {
  PaginatedResponse,
  User,
  Port,
  Berth,
  Booking,
  Review,
  Transaction,
  Convention,
  DashboardStats,
  CategoryOption,
} from "./types";

/* ─── Dashboard ──────────────────────────────────────────────── */
export function getDashboard() {
  return get<DashboardStats>("/admin/dashboard");
}

/* ─── Users ──────────────────────────────────────────────────── */
export function getUsers(page = 1, search = "", role = "") {
  const params = new URLSearchParams({ page: String(page) });
  if (search) params.set("search", search);
  if (role) params.set("role", role);
  return get<PaginatedResponse<User>>(`/admin/users?${params}`);
}

export function updateUserRole(userId: number, role: string) {
  return put<User>(`/admin/users/${userId}/role`, { role });
}

/* ─── Ports ──────────────────────────────────────────────────── */
export function getPorts(page = 1, search = "") {
  const params = new URLSearchParams({ page: String(page) });
  if (search) params.set("search", search);
  return get<PaginatedResponse<Port>>(`/admin/ports?${params}`);
}

export function getPort(id: number) {
  return get<Port>(`/admin/ports/${id}`);
}

export function createPort(data: Partial<Port>) {
  return post<Port>("/admin/ports", data);
}

export function updatePort(id: number, data: Partial<Port>) {
  return put<Port>(`/admin/ports/${id}`, data);
}

/* ─── Berths ─────────────────────────────────────────────────── */
export function getBerths(
  page = 1,
  filters: { search?: string; port_id?: number; status?: string; rating_level?: string } = {}
) {
  const params = new URLSearchParams({ page: String(page) });
  if (filters.search) params.set("search", filters.search);
  if (filters.port_id) params.set("port_id", String(filters.port_id));
  if (filters.status) params.set("status", filters.status);
  if (filters.rating_level) params.set("rating_level", filters.rating_level);
  return get<PaginatedResponse<Berth>>(`/admin/berths?${params}`);
}

export function getBerth(id: number) {
  return get<Berth>(`/admin/berths/${id}`);
}

export function toggleBerthActive(id: number) {
  return put<Berth>(`/admin/berths/${id}/toggle-active`);
}

/* ─── Bookings ───────────────────────────────────────────────── */
export function getBookings(
  page = 1,
  filters: { status?: string; search?: string } = {}
) {
  const params = new URLSearchParams({ page: String(page) });
  if (filters.status) params.set("status", filters.status);
  if (filters.search) params.set("search", filters.search);
  return get<PaginatedResponse<Booking>>(`/admin/bookings?${params}`);
}

/* ─── Ratings & Reviews ──────────────────────────────────────── */
export function getRatings(page = 1) {
  return get<PaginatedResponse<Berth>>(`/admin/ratings?page=${page}`);
}

export function getReviews(page = 1, search = "") {
  const params = new URLSearchParams({ page: String(page) });
  if (search) params.set("search", search);
  return get<PaginatedResponse<Review>>(`/admin/reviews?${params}`);
}

export function moderateReview(id: number, action: string) {
  return put(`/admin/reviews/${id}/moderate`, { action });
}

/* ─── Transactions / Financial ───────────────────────────────── */
export function getFinancialOverview() {
  return get<{
    total_revenue: number;
    total_commissions: number;
    total_nodi_issued: number;
    total_nodi_spent: number;
  }>("/admin/financial/overview");
}

export function getTransactions(page = 1, filters: { type?: string; status?: string } = {}) {
  const params = new URLSearchParams({ page: String(page) });
  if (filters.type) params.set("type", filters.type);
  if (filters.status) params.set("status", filters.status);
  return get<PaginatedResponse<Transaction>>(`/admin/financial/transactions?${params}`);
}

export function getRevenueByPort() {
  return get<{ port: string; revenue: number }[]>("/admin/financial/revenue-by-port");
}

export function getRevenueByPeriod(period: "daily" | "weekly" | "monthly" = "monthly") {
  return get<{ date: string; revenue: number }[]>(
    `/admin/financial/revenue-by-period?period=${period}`
  );
}

/* ─── Conventions ────────────────────────────────────────────── */
export function getConventions(
  page = 1,
  filters: { port_id?: number; category?: string; is_active?: boolean; search?: string } = {}
) {
  const params = new URLSearchParams({ page: String(page) });
  if (filters.search) params.set("search", filters.search);
  if (filters.port_id) params.set("port_id", String(filters.port_id));
  if (filters.category) params.set("category", filters.category);
  if (filters.is_active !== undefined) params.set("is_active", String(filters.is_active));
  return get<PaginatedResponse<Convention>>(`/admin/conventions?${params}`);
}

export function getConvention(id: number) {
  return get<Convention>(`/admin/conventions/${id}`);
}

export function getConventionCategories() {
  return get<CategoryOption[]>("/admin/conventions/categories");
}

export function createConvention(data: Partial<Convention>) {
  return post<Convention>("/admin/conventions", data);
}

export function updateConvention(id: number, data: Partial<Convention>) {
  return put<Convention>(`/admin/conventions/${id}`, data);
}

export function deleteConvention(id: number) {
  return del(`/admin/conventions/${id}`);
}

export function getPortConventions(portId: number) {
  return get<Convention[]>(`/admin/ports/${portId}/conventions`);
}

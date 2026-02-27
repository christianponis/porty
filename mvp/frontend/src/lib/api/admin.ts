import { get, post, put } from './client';
import {
  AdminDashboard,
  User,
  Port,
  Berth,
  Booking,
  Transaction,
  PaginatedResponse,
} from './types';

export function getDashboard() {
  return get<AdminDashboard>('/admin/dashboard/');
}

export function getUsers(page = 1) {
  return get<PaginatedResponse<User>>(`/admin/users/?page=${page}`);
}

export function updateUserRole(userId: number, role: User['role']) {
  return put<User>(`/admin/users/${userId}/role/`, { role });
}

export function getPorts(page = 1) {
  return get<PaginatedResponse<Port>>(`/admin/ports/?page=${page}`);
}

export function createPort(data: Partial<Port>) {
  return post<Port>('/admin/ports/', data);
}

export function updatePort(id: number, data: Partial<Port>) {
  return put<Port>(`/admin/ports/${id}/`, data);
}

export function getBookings(page = 1) {
  return get<PaginatedResponse<Booking>>(`/admin/bookings/?page=${page}`);
}

export function getRatings(page = 1) {
  return get<PaginatedResponse<Berth>>(`/admin/ratings/?page=${page}`);
}

export function getTransactions(page = 1) {
  return get<PaginatedResponse<Transaction>>(`/admin/transactions/?page=${page}`);
}

import { get, post } from './client';
import {
  Booking,
  CreateBookingData,
  CreateReviewData,
  Review,
  GuestDashboard,
  PaginatedResponse,
} from './types';

export function getMyBookings(page = 1) {
  return get<PaginatedResponse<Booking>>(`/guest/bookings/?page=${page}`);
}

export function createBooking(data: CreateBookingData) {
  return post<Booking>('/guest/bookings/', data);
}

export function getBooking(id: number) {
  return get<Booking>(`/guest/bookings/${id}/`);
}

export function cancelBooking(id: number) {
  return post<Booking>(`/guest/bookings/${id}/cancel/`);
}

export function submitReview(bookingId: number, data: CreateReviewData) {
  return post<Review>(`/guest/bookings/${bookingId}/review/`, data);
}

export function getDashboard() {
  return get<GuestDashboard>('/guest/dashboard/');
}

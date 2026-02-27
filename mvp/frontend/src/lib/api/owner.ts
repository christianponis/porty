import { get, post, put, del, upload } from './client';
import {
  Berth,
  BerthDetail,
  Booking,
  PaginatedResponse,
  AvailabilitySlot,
  Assessment,
  SubmitAssessmentData,
} from './types';

export function getMyBerths() {
  return get<Berth[]>('/owner/berths/');
}

export function createBerth(data: Partial<BerthDetail>) {
  return post<BerthDetail>('/owner/berths/', data);
}

export function getBerth(id: number) {
  return get<BerthDetail>(`/owner/berths/${id}/`);
}

export function updateBerth(id: number, data: Partial<BerthDetail>) {
  return put<BerthDetail>(`/owner/berths/${id}/`, data);
}

export function deleteBerth(id: number) {
  return del(`/owner/berths/${id}/`);
}

export function uploadImages(berthId: number, files: File[]) {
  const formData = new FormData();
  files.forEach((file) => formData.append('images', file));
  return upload<{ images: string[] }>(`/owner/berths/${berthId}/images/`, formData);
}

export function getAvailability(berthId: number, month?: string) {
  const query = month ? `?month=${month}` : '';
  return get<AvailabilitySlot[]>(`/owner/berths/${berthId}/availability/${query}`);
}

export function setAvailability(
  berthId: number,
  slots: { date: string; is_available: boolean; price_override?: number }[]
) {
  return post<AvailabilitySlot[]>(`/owner/berths/${berthId}/availability/`, { slots });
}

export function getBerthBookings(berthId: number, page = 1) {
  return get<PaginatedResponse<Booking>>(`/owner/berths/${berthId}/bookings/?page=${page}`);
}

export function confirmBooking(bookingId: number) {
  return post<Booking>(`/owner/bookings/${bookingId}/confirm/`);
}

export function rejectBooking(bookingId: number, reason?: string) {
  return post<Booking>(`/owner/bookings/${bookingId}/reject/`, { reason });
}

export function getAssessment(berthId: number) {
  return get<Assessment>(`/owner/berths/${berthId}/assessment/`);
}

export function submitAssessment(berthId: number, data: SubmitAssessmentData) {
  return post<Assessment>(`/owner/berths/${berthId}/assessment/`, data);
}

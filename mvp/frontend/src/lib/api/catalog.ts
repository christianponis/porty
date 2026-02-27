import { get } from './client';
import {
  Port,
  Berth,
  BerthDetail,
  PaginatedResponse,
  SearchParams,
  Stats,
} from './types';

export function getPorts() {
  return get<Port[]>('/catalog/ports/');
}

export function getPort(id: number) {
  return get<Port>(`/catalog/ports/${id}/`);
}

export function searchBerths(params: SearchParams) {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      query.append(key, String(value));
    }
  });
  return get<PaginatedResponse<Berth>>(`/catalog/berths/?${query.toString()}`);
}

export function getBerth(id: number) {
  return get<BerthDetail>(`/catalog/berths/${id}/`);
}

export function getTopBerths(limit = 6) {
  return get<Berth[]>(`/catalog/berths/top/?limit=${limit}`);
}

export function getLatestBerths(limit = 6) {
  return get<Berth[]>(`/catalog/berths/latest/?limit=${limit}`);
}

export function getStats() {
  return get<Stats>('/catalog/stats/');
}

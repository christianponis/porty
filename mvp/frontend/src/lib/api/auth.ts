import { get, post, put, upload } from './client';
import { LoginResponse, RegisterData, User } from './types';

type ApiEnvelope<T> = { data: T };

function unwrap<T>(payload: T | ApiEnvelope<T>): T {
  if (payload && typeof payload === 'object' && 'data' in payload) {
    return (payload as ApiEnvelope<T>).data;
  }
  return payload as T;
}

export function login(email: string, password: string) {
  return post<LoginResponse>('/auth/login', { email, password });
}

export function register(data: RegisterData) {
  return post<LoginResponse>('/auth/register', data);
}

export function logout(refresh: string) {
  return post<void>('/auth/logout', { refresh });
}

export function refreshToken(refresh: string) {
  return post<{ access: string }>('/auth/refresh', { refresh });
}

export function getProfile() {
  return get<User | ApiEnvelope<User>>('/auth/profile').then(unwrap);
}

export function updateProfile(data: Partial<User>) {
  return put<User | ApiEnvelope<User>>('/auth/profile', data).then(unwrap);
}

export function updatePassword(data: { old_password: string; new_password: string }) {
  return post<void>('/auth/password', data);
}

export function uploadAvatar(file: File) {
  const formData = new FormData();
  formData.append('avatar', file);
  return upload<User | ApiEnvelope<User>>('/auth/avatar', formData).then(unwrap);
}

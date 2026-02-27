import { create } from 'zustand';
import { User, RegisterData } from '@/lib/api/types';
import * as authApi from '@/lib/api/auth';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => void;
  fetchProfile: () => Promise<void>;
  init: () => void;
  setUser: (user: User) => void;
}

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: true,

  init: () => {
    if (typeof window === 'undefined') return;
    const token = localStorage.getItem('token');
    if (token) {
      set({ token, isAuthenticated: true });
      get().fetchProfile();
    } else {
      set({ isLoading: false });
    }
  },

  login: async (email, password) => {
    const data = await authApi.login(email, password);
    localStorage.setItem('token', data.access);
    if (data.refresh) {
      localStorage.setItem('refresh_token', data.refresh);
    } else {
      localStorage.removeItem('refresh_token');
    }
    set({
      user: data.user,
      token: data.access,
      isAuthenticated: true,
      isLoading: false,
    });
  },

  register: async (data) => {
    const res = await authApi.register(data);
    localStorage.setItem('token', res.access);
    if (res.refresh) {
      localStorage.setItem('refresh_token', res.refresh);
    } else {
      localStorage.removeItem('refresh_token');
    }
    set({
      user: res.user,
      token: res.access,
      isAuthenticated: true,
      isLoading: false,
    });
  },

  logout: () => {
    const refresh = localStorage.getItem('refresh_token');
    if (refresh) {
      authApi.logout(refresh).catch(() => {});
    }
    localStorage.removeItem('token');
    localStorage.removeItem('refresh_token');
    set({
      user: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,
    });
  },

  fetchProfile: async () => {
    try {
      const user = await authApi.getProfile();
      set({ user, isLoading: false });
    } catch {
      set({ user: null, isAuthenticated: false, token: null, isLoading: false });
      localStorage.removeItem('token');
      localStorage.removeItem('refresh_token');
    }
  },

  setUser: (user) => set({ user }),
}));

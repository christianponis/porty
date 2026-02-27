"use client";

import { create } from "zustand";
import { post, get } from "@/lib/api/client";
import type { User, LoginResponse } from "@/lib/api/types";

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  fetchProfile: () => Promise<void>;
  init: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  isAuthenticated: false,
  isLoading: true,

  login: async (email, password) => {
    const data = await post<LoginResponse>("/auth/login", { email, password });

    if (data.user.role !== "admin") {
      throw new Error("Accesso riservato agli amministratori.");
    }

    localStorage.setItem("token", data.access);
    localStorage.setItem("user", JSON.stringify(data.user));
    set({ user: data.user, isAuthenticated: true });
  },

  logout: () => {
    try {
      post("/auth/logout");
    } catch {
      // ignore
    }
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    set({ user: null, isAuthenticated: false });
  },

  fetchProfile: async () => {
    try {
      const user = await get<User>("/auth/profile");
      if (user.role !== "admin") {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        set({ user: null, isAuthenticated: false });
        return;
      }
      localStorage.setItem("user", JSON.stringify(user));
      set({ user, isAuthenticated: true });
    } catch {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      set({ user: null, isAuthenticated: false });
    }
  },

  init: () => {
    const token = localStorage.getItem("token");
    const userStr = localStorage.getItem("user");

    if (token && userStr) {
      try {
        const user = JSON.parse(userStr) as User;
        if (user.role === "admin") {
          set({ user, isAuthenticated: true, isLoading: false });
          return;
        }
      } catch {
        // corrupt data
      }
    }

    localStorage.removeItem("token");
    localStorage.removeItem("user");
    set({ user: null, isAuthenticated: false, isLoading: false });
  },
}));

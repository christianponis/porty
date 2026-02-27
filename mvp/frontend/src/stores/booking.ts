import { create } from 'zustand';
import { Berth } from '@/lib/api/types';

interface BookingState {
  selectedBerth: Berth | null;
  checkIn: string;
  checkOut: string;
  boatLength: number;
  boatName: string;
  sharing: boolean;
  notes: string;
  step: number;
  setSelectedBerth: (berth: Berth | null) => void;
  setDates: (checkIn: string, checkOut: string) => void;
  setBoatInfo: (boatName: string, boatLength: number) => void;
  setSharing: (sharing: boolean) => void;
  setNotes: (notes: string) => void;
  setStep: (step: number) => void;
  nextStep: () => void;
  prevStep: () => void;
  reset: () => void;
}

const initialState = {
  selectedBerth: null,
  checkIn: '',
  checkOut: '',
  boatLength: 0,
  boatName: '',
  sharing: false,
  notes: '',
  step: 1,
};

export const useBookingStore = create<BookingState>((set) => ({
  ...initialState,

  setSelectedBerth: (berth) => set({ selectedBerth: berth }),

  setDates: (checkIn, checkOut) => set({ checkIn, checkOut }),

  setBoatInfo: (boatName, boatLength) => set({ boatName, boatLength }),

  setSharing: (sharing) => set({ sharing }),

  setNotes: (notes) => set({ notes }),

  setStep: (step) => set({ step }),

  nextStep: () => set((state) => ({ step: state.step + 1 })),

  prevStep: () => set((state) => ({ step: Math.max(1, state.step - 1) })),

  reset: () => set(initialState),
}));

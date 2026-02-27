import { get } from './client';
import { Wallet, Transaction, PaginatedResponse } from './types';

export function getWallet() {
  return get<Wallet>('/wallet/');
}

export function getTransactions(page = 1) {
  return get<PaginatedResponse<Transaction>>(`/wallet/transactions/?page=${page}`);
}

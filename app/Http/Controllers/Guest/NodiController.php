<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\NodiService;
use Illuminate\Support\Facades\Auth;

class NodiController extends Controller
{
    public function __construct(
        private NodiService $nodiService,
    ) {}

    public function index()
    {
        $wallet = $this->nodiService->getOrCreateWallet(Auth::user());
        $transactions = $wallet->transactions()->latest()->paginate(20);

        return view('guest.nodi.index', compact('wallet', 'transactions'));
    }
}

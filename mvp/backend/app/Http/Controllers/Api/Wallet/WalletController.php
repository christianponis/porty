<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get the authenticated user's wallet.
     */
    public function index(): JsonResponse
    {
        $user = auth('api')->user();
        $wallet = $user->getOrCreateWallet();

        return response()->json([
            'data' => new WalletResource($wallet),
        ]);
    }

    /**
     * Get paginated transactions for the user's wallet.
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $wallet = $user->getOrCreateWallet();

        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json(TransactionResource::collection($transactions)->response()->getData(true));
    }
}

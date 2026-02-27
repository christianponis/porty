<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\NodiTransaction;
use App\Models\NodiWallet;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    /**
     * Financial overview stats.
     */
    public function overview(): JsonResponse
    {
        $totalRevenue = (float) Booking::where('status', 'completed')->sum('total_price');
        $totalCommissions = (float) Transaction::where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');
        $totalNodiIssued = (float) NodiTransaction::where('type', 'earned')->sum('amount')
            + (float) NodiTransaction::where('type', 'bonus')->sum('amount');
        $totalNodiSpent = (float) NodiTransaction::where('type', 'spent')->sum('amount');

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_commissions' => $totalCommissions,
            'total_nodi_issued' => $totalNodiIssued,
            'total_nodi_spent' => abs($totalNodiSpent),
        ]);
    }

    /**
     * Paginated transactions.
     */
    public function transactions(Request $request): JsonResponse
    {
        $query = Transaction::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        $data = $transactions->through(fn ($t) => [
            'id' => $t->id,
            'booking_id' => $t->booking_id,
            'type' => $t->type?->value ?? $t->type,
            'amount' => (float) $t->amount,
            'currency' => $t->currency ?? 'EUR',
            'status' => $t->status?->value ?? $t->status,
            'commission_rate' => $t->commission_rate ? (float) $t->commission_rate : null,
            'commission_amount' => $t->commission_amount ? (float) $t->commission_amount : null,
            'owner_amount' => $t->owner_amount ? (float) $t->owner_amount : null,
            'created_at' => $t->created_at?->toISOString(),
        ]);

        return response()->json($data);
    }

    /**
     * Revenue grouped by port.
     */
    public function revenueByPort(): JsonResponse
    {
        $data = Booking::select('berths.port_id', DB::raw('SUM(bookings.total_price) as revenue'))
            ->join('berths', 'bookings.berth_id', '=', 'berths.id')
            ->where('bookings.status', 'completed')
            ->groupBy('berths.port_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $portIds = $data->pluck('port_id')->filter();
        $ports = DB::table('ports')->whereIn('id', $portIds)->pluck('name', 'id');

        $result = $data->map(fn ($row) => [
            'port' => $ports->get($row->port_id) ?? "Porto #{$row->port_id}",
            'revenue' => (float) $row->revenue,
        ]);

        return response()->json($result);
    }

    /**
     * Revenue time series.
     */
    public function revenueByPeriod(Request $request): JsonResponse
    {
        $period = $request->get('period', 'monthly');

        $format = match ($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m',
        };

        $data = Booking::select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as date_key"),
                DB::raw('SUM(total_price) as revenue')
            )
            ->where('status', 'completed')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->limit(24)
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date_key,
                'revenue' => (float) $row->revenue,
            ]);

        return response()->json($data);
    }
}

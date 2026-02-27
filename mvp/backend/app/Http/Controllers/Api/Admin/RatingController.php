<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BerthResource;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\Port;
use App\Models\Review;
use App\Models\User;
use App\Models\NodiWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Admin dashboard stats.
     */
    public function dashboard(): JsonResponse
    {
        $totalUsers = User::count();
        $totalPorts = Port::count();
        $totalBerths = Berth::count();
        $activeBerths = Berth::where('is_active', true)->count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalRevenue = (float) Booking::where('status', 'completed')->sum('total_price');
        $totalNodi = (float) NodiWallet::sum('balance');

        $recentUsers = User::orderByDesc('created_at')->take(5)->get()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'first_name' => explode(' ', $u->name ?? '', 2)[0] ?? '',
            'last_name' => explode(' ', $u->name ?? '', 2)[1] ?? '',
            'email' => $u->email,
            'role' => $u->role->value,
            'is_active' => $u->is_active,
            'created_at' => $u->created_at?->toISOString(),
        ]);

        $recentBookings = Booking::with(['berth.port'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $guestIds = $recentBookings->pluck('guest_id')->unique()->filter();
        $guests = User::whereIn('id', $guestIds)->get()->keyBy('id');

        $recentBookingsData = $recentBookings->map(function ($b) use ($guests) {
            $guest = $guests->get($b->guest_id);
            return [
                'id' => $b->id,
                'berth' => $b->berth ? [
                    'id' => $b->berth->id,
                    'title' => $b->berth->title,
                    'code' => $b->berth->code,
                    'port' => $b->berth->port ? ['id' => $b->berth->port->id, 'name' => $b->berth->port->name] : null,
                ] : null,
                'guest' => $guest ? ['id' => $guest->id, 'name' => $guest->name, 'email' => $guest->email] : null,
                'start_date' => $b->start_date,
                'end_date' => $b->end_date,
                'total_price' => (float) $b->total_price,
                'status' => $b->status->value ?? $b->status,
                'booking_mode' => $b->booking_mode,
                'created_at' => $b->created_at?->toISOString(),
            ];
        });

        return response()->json([
            'total_users' => $totalUsers,
            'total_ports' => $totalPorts,
            'total_berths' => $totalBerths,
            'active_berths' => $activeBerths,
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'total_revenue' => $totalRevenue,
            'total_nodi' => $totalNodi,
            'recent_users' => $recentUsers,
            'recent_bookings' => $recentBookingsData,
        ]);
    }

    /**
     * Overview of berths with rating data (admin).
     */
    public function index(): JsonResponse
    {
        $berths = Berth::with(['port', 'selfAssessment', 'latestCertification'])
            ->whereNotNull('rating_level')
            ->orWhere('review_count', '>', 0)
            ->orWhereNotNull('grey_anchor_count')
            ->orWhereNotNull('blue_anchor_count')
            ->orWhereNotNull('gold_anchor_count')
            ->orderByDesc('review_average')
            ->paginate(15);

        $data = $berths->through(function ($berth) {
            return [
                'id' => $berth->id,
                'title' => $berth->title,
                'port' => $berth->port ? [
                    'id' => $berth->port->id,
                    'name' => $berth->port->name,
                ] : null,
                'rating_level' => $berth->rating_level?->value,
                'grey_anchor_count' => $berth->grey_anchor_count,
                'blue_anchor_count' => $berth->blue_anchor_count,
                'gold_anchor_count' => $berth->gold_anchor_count,
                'review_count' => $berth->review_count ?? 0,
                'review_average' => $berth->review_average ? (float) $berth->review_average : null,
                'has_self_assessment' => $berth->selfAssessment !== null,
                'self_assessment_status' => $berth->selfAssessment?->status?->value,
                'has_certification' => $berth->latestCertification !== null,
                'certification_valid' => $berth->latestCertification?->isValid() ?? false,
                'certification_valid_until' => $berth->latestCertification?->valid_until?->toDateString(),
            ];
        });

        return response()->json($data);
    }
}

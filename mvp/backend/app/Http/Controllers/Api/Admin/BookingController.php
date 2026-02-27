<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Paginate all bookings with filters (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['berth.port']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Search by guest
        if ($request->filled('search')) {
            $search = $request->search;
            $guestIds = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->pluck('id');
            $query->whereIn('guest_id', $guestIds);
        }

        $bookings = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        // Attach guest info manually (cross-database)
        $guestIds = $bookings->pluck('guest_id')->unique()->filter();
        $guests = User::whereIn('id', $guestIds)->get()->keyBy('id');

        $bookings->through(function ($booking) use ($guests) {
            $booking->guest_info = $guests->get($booking->guest_id)
                ? [
                    'id' => $guests->get($booking->guest_id)->id,
                    'name' => $guests->get($booking->guest_id)->name,
                    'email' => $guests->get($booking->guest_id)->email,
                ]
                : null;

            return $booking;
        });

        $data = BookingResource::collection($bookings)->response()->getData(true);

        // Merge guest_info into the response data
        foreach ($data['data'] as $index => &$item) {
            $item['guest'] = $bookings->values()->get($index)?->guest_info;
        }

        return response()->json($data);
    }
}

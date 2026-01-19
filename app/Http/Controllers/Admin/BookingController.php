<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Date;
use App\Models\TimeSlot;
use App\Services\TableAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private TableAssignmentService $tableAssignmentService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with(['customer', 'date', 'timeSlot']);

        // Search filter (applies to customer name/email)
        if ($search = $request->input('search')) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sorting
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'total', 'status'];

        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $bookings = $query->paginate(10)->withQueryString();

        $totalBookings = Booking::count();

        $confirmedBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->count();

        $pendingBookings = Booking::query()
            ->whereIn('status', [Booking::STATUS_INITIATED, Booking::STATUS_PENDING_PAYMENT])
            ->count();

        $cancelledBookings = Booking::query()
            ->whereIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_PAYMENT_FAILED])
            ->count();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'totalBookings' => $totalBookings,
            'confirmedBookings' => $confirmedBookings,
            'pendingBookings' => $pendingBookings,
            'cancelledBookings' => $cancelledBookings,
            'currentSort' => $sortColumn,
            'currentDirection' => $sortDirection,
        ]);
    }

    public function byDate(Date $date): View
    {
        $timeSlots = TimeSlot::all();

        $bookings = Booking::with(['customer', 'timeSlot', 'details.price', 'tableBookings.table'])
            ->where('date_id', $date->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderBy('time_slot_id')
            ->get();

        $availabilitySummary = [];
        foreach ($timeSlots as $timeSlot) {
            $availabilitySummary[$timeSlot->id] = $this->tableAssignmentService->getAvailabilitySummary(
                $date->id,
                $timeSlot->id
            );
        }

        $totalRevenue = $bookings->sum('total');

        return view('admin.bookings.by-date', compact(
            'date',
            'timeSlots',
            'bookings',
            'availabilitySummary',
            'totalRevenue'
        ));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function availability(Request $request): JsonResponse
    {
        $dateId = $request->input('date_id');
        $timeSlotId = $request->input('time_slot_id');

        if (!$dateId || !$timeSlotId) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $summary = $this->tableAssignmentService->getAvailabilitySummary($dateId, $timeSlotId);

        return response()->json($summary);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AmendBookingRequest;
use App\Mail\BookingInvoice;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Services\BookingAmendmentService;
use App\Services\TableAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private TableAssignmentService $tableAssignmentService,
        private BookingAmendmentService $bookingAmendmentService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with(['customer', 'date', 'timeSlot']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $query->where('date_id', $request->input('date'));
        }

        if ($request->filled('time_slot')) {
            $query->where('time_slot_id', $request->input('time_slot'));
        }

        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'total', 'status'];

        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $bookings = $query->paginate(15)->withQueryString();

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

        $dateFilterOptions = Date::orderBy('date_value')->get()->pluck('formatted_date', 'id')->toArray();
        $timeSlotFilterOptions = TimeSlot::all()->pluck('formatted_time', 'id')->toArray();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'totalBookings' => $totalBookings,
            'confirmedBookings' => $confirmedBookings,
            'pendingBookings' => $pendingBookings,
            'cancelledBookings' => $cancelledBookings,
            'dateFilterOptions' => $dateFilterOptions,
            'timeSlotFilterOptions' => $timeSlotFilterOptions,
            'currentSort' => $sortColumn,
            'currentDirection' => $sortDirection,
        ]);
    }

    public function byDate(Date $date, TimeSlot $timeSlot): View
    {
        $bookings = Booking::with(['customer', 'timeSlot', 'details.price', 'tableBookings.table'])
            ->where('date_id', $date->id)
            ->where('time_slot_id', $timeSlot->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->get();

        $availabilitySummary = $this->tableAssignmentService->getAvailabilitySummary(
            $date->id,
            $timeSlot->id
        );

        $totalRevenue = $bookings->sum('total');

        return view('admin.bookings.by-date', compact(
            'date',
            'timeSlot',
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

    public function edit(Booking $booking): View|RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('error', 'Only confirmed bookings can be amended.');
        }

        $booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);

        $dates = Date::orderBy('date_value')->get();
        $timeSlots = TimeSlot::all();
        $allTables = Table::orderBy('capacity', 'desc')->orderBy('table_number')->get();

        $availableTables = $this->bookingAmendmentService->getAvailableTablesForAmendment(
            $booking,
            $booking->date_id,
            $booking->time_slot_id
        );

        $currentTableIds = $booking->tableBookings->pluck('table_id')->toArray();

        return view('admin.bookings.edit', compact(
            'booking',
            'dates',
            'timeSlots',
            'allTables',
            'availableTables',
            'currentTableIds'
        ));
    }

    public function update(AmendBookingRequest $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_CONFIRMED) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('error', 'Only confirmed bookings can be amended.');
        }

        $booking->load('details', 'tableBookings');

        $dateId = (int) $request->validated('date_id');
        $timeSlotId = (int) $request->validated('time_slot_id');
        $tableIds = $request->validated('table_ids');

        $availability = $this->bookingAmendmentService->checkAmendmentAvailability(
            $booking,
            $dateId,
            $timeSlotId,
            $tableIds
        );

        if (!$availability['available']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $availability['message']);
        }

        $success = $this->bookingAmendmentService->amendBooking($booking, $dateId, $timeSlotId, $tableIds);

        if (!$success) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to amend booking. Please try again.');
        }

        $from = $request->input('from');

        return redirect()
            ->route('admin.bookings.show', array_filter([$booking->getRouteKey(), 'from' => $from]))
            ->with('success', 'Booking successfully amended.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status'                  => 'required|integer|in:0,1,2,3,4',
            'transaction_reference_no' => 'required|string|max:255',
        ]);

        $result = $this->bookingAmendmentService->updateBookingStatus(
            $booking,
            (int) $validated['status'],
            $validated['transaction_reference_no']
        );

        $from = $request->input('from');
        $redirectParams = $from ? [$booking, 'from' => $from] : $booking;

        $message = 'Booking status updated successfully.';
        if ($result['reassigned']) {
            $message .= ' Some tables were automatically reassigned due to conflicts.';
        }

        return redirect()
            ->route('admin.bookings.show', $redirectParams)
            ->with('success', $message);
    }

    public function resendEmail(Request $request, Booking $booking): RedirectResponse
    {
        $booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);

        Mail::to($booking->customer->email)->send(new BookingInvoice($booking));

        $from = $request->input('from');
        $redirectParams = $from ? [$booking, 'from' => $from] : $booking;

        return redirect()
            ->route('admin.bookings.show', $redirectParams)
            ->with('success', 'Invoice email resent to ' . $booking->customer->email);
    }

    public function checkAmendmentAvailability(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'date_id' => 'required|exists:dates,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'table_ids' => 'nullable|array',
            'table_ids.*' => 'exists:tables,id',
        ]);

        $booking->load('details', 'tableBookings');

        $tableIds = $request->input('table_ids');

        $result = $this->bookingAmendmentService->checkAmendmentAvailability(
            $booking,
            (int) $request->input('date_id'),
            (int) $request->input('time_slot_id'),
            $tableIds ? array_map('intval', $tableIds) : null
        );

        $result['current_table_ids'] = $booking->tableBookings->pluck('table_id')->toArray();

        return response()->json($result);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Flight;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->latest()->get(); // Tampilkan booking user yang sedang login
        return view('bookings.index', compact('bookings'));
    }

    // Metode untuk menampilkan form pemesanan
    public function create(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');
        $item = null;

        switch ($type) {
            case 'hotel':
                $item = Hotel::find($id);
                break;
            case 'flight':
                $item = Flight::find($id);
                break;
            case 'train':
                $item = Train::find($id);
                break;
        }

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found for booking.');
        }

        return view('bookings.create', compact('item', 'type'));
    }

    // Metode untuk menyimpan pemesanan
    public function store(Request $request)
    {
        $request->validate([
            'bookable_type' => 'required|in:hotel,flight,train',
            'bookable_id' => 'required|integer',
            'booking_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
        ]);

        $modelClass = 'App\\Models\\' . ucfirst($request->bookable_type);
        $bookableItem = $modelClass::find($request->bookable_id);

        if (!$bookableItem) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        $totalPrice = $bookableItem->price * $request->quantity; // Asumsi harga per unit

        // Untuk Hotel, periksa ketersediaan kamar
        if ($request->bookable_type === 'hotel') {
            // Ini memerlukan logika yang lebih kompleks jika ada tipe kamar,
            // untuk saat ini kita asumsikan price_per_night adalah harga untuk satu "unit" booking hotel
            $totalPrice = $bookableItem->price_per_night * $request->quantity;
        } else if ($request->bookable_type === 'flight' || $request->bookable_type === 'train') {
            if ($bookableItem->available_seats < $request->quantity) {
                return redirect()->back()->with('error', 'Not enough seats available.');
            }
            $bookableItem->decrement('available_seats', $request->quantity); // Kurangi kursi yang tersedia
        }


        Booking::create([
            'user_id' => Auth::id(),
            'bookable_type' => $modelClass,
            'bookable_id' => $request->bookable_id,
            'booking_date' => $request->booking_date,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'confirmed', // Langsung confirmed untuk simulasi
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        // Pastikan user hanya bisa melihat bookingnya sendiri
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Jika itu pemesanan pesawat/kereta, kembalikan kursi yang tersedia
        if ($booking->bookable_type === Flight::class || $booking->bookable_type === Train::class) {
            $bookableItem = $booking->bookable;
            if ($bookableItem) {
                $bookableItem->increment('available_seats', $booking->quantity);
            }
        }

        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /**
     * Menampilkan daftar shipment sesuai hak akses pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Mengambil data shipment beserta relasi yang dibutuhkan.
        $shipments = Shipment::with([
            'customer',
            'carrier',
            'originLocation',
            'destinationLocation',
        ]);

        // Customer hanya dapat melihat shipment miliknya sendiri.
        if ($user->role === 'customer') {
            $shipments->where('customer_id', $user->id);
        }

        // Fitur pencarian nomor shipment atau jenis cargo.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $shipments->where(function ($query) use ($search) {
                $query->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status shipment.
        if ($request->filled('status')) {
            $shipments->where('current_status', $request->input('status'));
        }

        // Menampilkan data terbaru terlebih dahulu.
        $shipments = $shipments
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('shipments.index', [
            'shipments' => $shipments,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ]);
    }
}

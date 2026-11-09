<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    /**
     * Menampilkan daftar shipment sesuai hak akses pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

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

        // Pencarian nomor shipment atau jenis cargo.
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

    /**
     * Menampilkan formulir tambah shipment.
     */
    public function create(): View
    {
        $customers = User::where('role', 'customer')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $carriers = Carrier::where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('shipments.create', [
            'customers' => $customers,
            'carriers' => $carriers,
            'locations' => $locations,
        ]);
    }

    /**
     * Memvalidasi dan menyimpan shipment baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('role', 'customer')
                    ->where('is_active', true),
            ],

            'carrier_id' => [
                'required',
                Rule::exists('carriers', 'id')
                    ->where('is_active', true),
            ],

            'origin_location_id' => [
                'required',
                Rule::exists('locations', 'id')
                    ->where('is_active', true),
            ],

            'destination_location_id' => [
                'required',
                'different:origin_location_id',
                Rule::exists('locations', 'id')
                    ->where('is_active', true),
            ],

            'cargo_type' => ['required', 'string', 'max:100'],

            'total_weight_kg' => [
                'nullable',
                'numeric',
                'min:0.001',
            ],

            'total_volume_m3' => [
                'nullable',
                'numeric',
                'min:0.001',
            ],

            'expected_departure_at' => [
                'required',
                'date',
            ],

            'expected_arrival_at' => [
                'required',
                'date',
                'after:expected_departure_at',
            ],
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak valid atau tidak aktif.',
            'carrier_id.required' => 'Carrier wajib dipilih.',
            'carrier_id.exists' => 'Carrier tidak valid atau tidak aktif.',
            'origin_location_id.required' => 'Lokasi asal wajib dipilih.',
            'origin_location_id.exists' => 'Lokasi asal tidak valid atau tidak aktif.',
            'destination_location_id.required' => 'Lokasi tujuan wajib dipilih.',
            'destination_location_id.exists' => 'Lokasi tujuan tidak valid atau tidak aktif.',
            'destination_location_id.different' => 'Lokasi tujuan harus berbeda dari lokasi asal.',
            'cargo_type.required' => 'Jenis cargo wajib diisi.',
            'cargo_type.max' => 'Jenis cargo maksimal 100 karakter.',
            'total_weight_kg.min' => 'Berat cargo harus lebih dari 0.',
            'total_volume_m3.min' => 'Volume cargo harus lebih dari 0.',
            'expected_departure_at.required' => 'Jadwal keberangkatan wajib diisi.',
            'expected_arrival_at.required' => 'Jadwal kedatangan wajib diisi.',
            'expected_arrival_at.after' => 'Jadwal kedatangan harus setelah jadwal keberangkatan.',
        ]);

        // Membuat nomor shipment otomatis dan memastikan nomor unik.
        do {
            $shipmentNumber = 'CT-' . now()->format('Ymd') . '-'
                . strtoupper(bin2hex(random_bytes(3)));
        } while (Shipment::where('shipment_number', $shipmentNumber)->exists());

        // Menyimpan shipment dalam transaksi database.
        DB::transaction(function () use ($validated, $request, $shipmentNumber) {
            Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $validated['customer_id'],
                'carrier_id' => $validated['carrier_id'],
                'origin_location_id' => $validated['origin_location_id'],
                'destination_location_id' => $validated['destination_location_id'],
                'cargo_type' => $validated['cargo_type'],
                'total_weight_kg' => $validated['total_weight_kg'] ?? null,
                'total_volume_m3' => $validated['total_volume_m3'] ?? null,
                'expected_departure_at' => $validated['expected_departure_at'],
                'expected_arrival_at' => $validated['expected_arrival_at'],
                'current_status' => 'CREATED',
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Shipment berhasil ditambahkan dengan nomor ' . $shipmentNumber . '.');
    }
}

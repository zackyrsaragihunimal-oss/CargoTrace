<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $view = match ($user->role) {
            'admin' => 'dashboards.admin',
            'operator' => 'dashboards.operator',
            'customer' => 'dashboards.customer',
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };

        return view($view, [
            'user' => $user,
        ]);
    }
}

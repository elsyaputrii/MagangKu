<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use App\Services\Profil\ProfilService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly ProfilService $profil
    ) {
    }

    public function index(Request $request)
    {
        return view('dashboard.index', [
            'ringkasan' => $this->dashboard->summary(
                $request->query('bulan'),
                $request->integer('minggu') ?: null
            ),
            'profil'    => $this->profil->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use App\Services\Profil\ProfilService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly ProfilService $profil
    ) {
    }

    public function index()
    {
        return view('dashboard.index', [
            'ringkasan' => $this->dashboard->summary(),
            'profil'    => $this->profil->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Profil;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profil\ProfilRequest;
use App\Services\Profil\ProfilService;

class ProfilController extends Controller
{
    public function __construct(private readonly ProfilService $service)
    {
    }

    public function edit()
    {
        return view('profil.edit', ['profil' => $this->service->get()]);
    }

    public function update(ProfilRequest $request)
    {
        $this->service->update($request->validated());

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil disimpan.');
    }
}

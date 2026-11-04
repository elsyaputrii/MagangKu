<?php

namespace App\Services\Profil;

use App\Models\Profile;

class ProfilService
{
    public function get(): Profile
    {
        return Profile::current();
    }

    public function update(array $data): Profile
    {
        $profil = Profile::current();
        $profil->update($data);

        return $profil->refresh();
    }
}

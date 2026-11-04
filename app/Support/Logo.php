<?php

namespace App\Support;

class Logo
{
    /** Logo sebagai data-URI agar tampil di PDF tanpa akses jaringan/path. */
    public static function dataUri(): string
    {
        $path = config('magang.logo_path');
        if (! is_file($path)) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $fillable = ['tanggal', 'uraian'];

    protected $casts = [
        'tanggal' => 'date',
    ];
}

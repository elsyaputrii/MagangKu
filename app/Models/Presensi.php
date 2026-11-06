<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensis';

    protected $fillable = ['tanggal', 'status', 'jam_datang', 'jam_pulang', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function scopeBulan(Builder $query, int $tahun, int $bulan): Builder
    {
        return $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
    }

    /** True bila hadir (bukan izin/sakit). */
    public function getHadirAttribute(): bool
    {
        return ($this->status ?? 'hadir') === 'hadir';
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'hadir');
    }

    /** Jam datang format "08.00" (gaya template). */
    public function getJamDatangTemplateAttribute(): string
    {
        return $this->formatJam($this->jam_datang);
    }

    public function getJamPulangTemplateAttribute(): string
    {
        return $this->formatJam($this->jam_pulang);
    }

    /** Jam format "08:00" untuk <input type="time">. */
    public function getJamDatangInputAttribute(): string
    {
        return substr((string) $this->jam_datang, 0, 5);
    }

    public function getJamPulangInputAttribute(): string
    {
        return $this->jam_pulang ? substr((string) $this->jam_pulang, 0, 5) : '';
    }

    /** Durasi kerja dalam menit; null jika belum pulang. */
    public function getDurasiMenitAttribute(): ?int
    {
        if (! $this->jam_pulang || ! $this->jam_datang) {
            return null;
        }

        $datang = Carbon::createFromTimeString($this->jam_datang);
        $pulang = Carbon::createFromTimeString($this->jam_pulang);

        return max(0, (int) round(($pulang->timestamp - $datang->timestamp) / 60));
    }

    public function getDurasiTeksAttribute(): string
    {
        $menit = $this->durasi_menit;
        if ($menit === null) {
            return '-';
        }

        return intdiv($menit, 60) . ' j ' . str_pad((string) ($menit % 60), 2, '0', STR_PAD_LEFT) . ' m';
    }

    /** True bila sudah pulang dan durasi kurang dari batas minimum jam kerja. */
    public function getKurangJamAttribute(): bool
    {
        $menit = $this->durasi_menit;

        return $menit !== null && $menit < config('magang.min_jam_kerja') * 60;
    }

    private function formatJam(?string $jam): string
    {
        return $jam ? str_replace(':', '.', substr($jam, 0, 5)) : '';
    }
}

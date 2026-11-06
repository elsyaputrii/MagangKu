@include('partials.status-style')
@php
    $isEdit = isset($presensi);
    $tanggal    = old('tanggal', $isEdit ? $presensi->tanggal->toDateString() : ($tanggalDefault ?? ''));
    $status     = old('status', $isEdit ? ($presensi->status ?? 'hadir') : 'hadir');
    $datang     = old('jam_datang', $isEdit ? $presensi->jam_datang_input : '08:00');
    $pulang     = old('jam_pulang', $isEdit ? $presensi->jam_pulang_input : '');
    $keterangan = old('keterangan', $isEdit ? $presensi->keterangan : '');
@endphp

<form method="POST" action="{{ $isEdit ? route('presensi.update', $presensi) : route('presensi.store') }}" class="form" data-status-form novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="fields">
        <div class="field @error('tanggal') has-error @enderror">
            <label for="tanggal">Tanggal <span class="req">*</span></label>
            <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" required data-day-hint="hint-tanggal">
            <div id="hint-tanggal" class="hint"></div>
            @error('tanggal')<div class="err">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="field @error('status') has-error @enderror">
        <label>Status kehadiran <span class="req">*</span></label>
        <div class="seg">
            <label class="hadir"><input type="radio" name="status" value="hadir" @checked($status === 'hadir')> Hadir</label>
            <label class="izin"><input type="radio" name="status" value="izin" @checked($status === 'izin')> Izin</label>
            <label class="sakit"><input type="radio" name="status" value="sakit" @checked($status === 'sakit')> Sakit</label>
        </div>
        @error('status')<div class="err">{{ $message }}</div>@enderror
    </div>

    <div class="fields" data-when-hadir>
        <div class="field @error('jam_datang') has-error @enderror">
            <label for="jam_datang">Jam datang (WIB) <span class="req">*</span></label>
            <input type="time" id="jam_datang" name="jam_datang" value="{{ $datang }}" step="60">
            @error('jam_datang')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field @error('jam_pulang') has-error @enderror">
            <label for="jam_pulang">Jam pulang (WIB)</label>
            <input type="time" id="jam_pulang" name="jam_pulang" value="{{ $pulang }}" step="60">
            <div class="hint">Boleh dikosongkan dulu, lalu diisi saat pulang.</div>
            @error('jam_pulang')<div class="err">{{ $message }}</div>@enderror
        </div>
    </div>

    <div data-when-absen style="display:none">
        <div class="field @error('keterangan') has-error @enderror" style="max-width:560px">
            <label for="keterangan">Keterangan <span class="muted">(opsional)</span></label>
            <input type="text" id="keterangan" name="keterangan" value="{{ $keterangan }}" maxlength="150" placeholder="">
            <div class="hint">Boleh dikosongkan. Kalau diisi, tulisan ini ikut masuk ke Presensi dan Logbook tanggal tersebut.</div>
            @error('keterangan')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="alert alert-info" style="margin:12px 0 0;max-width:560px">
            Yang akan tertulis di Logbook:<br>
            <b id="preview-absen"></b>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check']) Simpan</button>
        <a href="{{ route('presensi.index', $isEdit ? ['bulan' => $presensi->tanggal->format('Y-m')] : []) }}" class="btn">Batal</a>
    </div>
</form>

<script>
(function () {
    var form = document.querySelector('form[data-status-form]');
    if (!form) return;
    var hadirBox = form.querySelector('[data-when-hadir]');
    var absenBox = form.querySelector('[data-when-absen]');
    var ket = form.querySelector('#keterangan');
    var preview = form.querySelector('#preview-absen');

    function teks(status, alasan) {
        alasan = (alasan || '').trim();
        if (alasan) {
            alasan = alasan.charAt(0).toUpperCase() + alasan.slice(1);
            return /[.!?]$/.test(alasan) ? alasan : alasan + '.';
        }
        return status === 'sakit' ? 'Tidak masuk magang karena sakit.' : 'Izin tidak masuk magang.';
    }
    function update() {
        var checked = form.querySelector('input[name="status"]:checked');
        var status = checked ? checked.value : 'hadir';
        var hadir = status === 'hadir';
        hadirBox.style.display = hadir ? '' : 'none';
        absenBox.style.display = hadir ? 'none' : '';
        hadirBox.querySelectorAll('input').forEach(function (i) { i.disabled = !hadir; });
        absenBox.querySelectorAll('input').forEach(function (i) { i.disabled = hadir; });
        if (!hadir) {
            ket.placeholder = status === 'sakit' ? 'Contoh: Sakit demam, istirahat di rumah' : 'Contoh: Izin mengurus keperluan keluarga';
            preview.textContent = teks(status, ket.value);
        }
    }
    form.querySelectorAll('input[name="status"]').forEach(function (r) { r.addEventListener('change', update); });
    ket.addEventListener('input', update);
    update();
})();
</script>

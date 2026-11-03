@php
    $isEdit = isset($presensi);
    $tanggal = old('tanggal', $isEdit ? $presensi->tanggal->toDateString() : ($tanggalDefault ?? ''));
    $datang  = old('jam_datang', $isEdit ? $presensi->jam_datang_input : '08:00');
    $pulang  = old('jam_pulang', $isEdit ? $presensi->jam_pulang_input : '');
@endphp

<form method="POST" action="{{ $isEdit ? route('presensi.update', $presensi) : route('presensi.store') }}" class="form" novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="fields">
        <div class="field @error('tanggal') has-error @enderror">
            <label for="tanggal">Tanggal <span class="req">*</span></label>
            <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" required data-day-hint="hint-tanggal">
            <div id="hint-tanggal" class="hint"></div>
            @error('tanggal')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field @error('jam_datang') has-error @enderror">
            <label for="jam_datang">Jam datang (WIB) <span class="req">*</span></label>
            <input type="time" id="jam_datang" name="jam_datang" value="{{ $datang }}" step="60" required>
            @error('jam_datang')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field @error('jam_pulang') has-error @enderror">
            <label for="jam_pulang">Jam pulang (WIB)</label>
            <input type="time" id="jam_pulang" name="jam_pulang" value="{{ $pulang }}" step="60">
            <div class="hint">Boleh dikosongkan dulu, lalu diisi saat pulang.</div>
            @error('jam_pulang')<div class="err">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check']) Simpan</button>
        <a href="{{ route('presensi.index', $isEdit ? ['bulan' => $presensi->tanggal->format('Y-m')] : []) }}" class="btn">Batal</a>
    </div>
</form>

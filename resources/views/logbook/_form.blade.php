@php
    $isEdit = isset($logbook);
    $tanggal = old('tanggal', $isEdit ? $logbook->tanggal->toDateString() : ($tanggalDefault ?? ''));
    $uraian  = old('uraian', $isEdit ? $logbook->uraian : '');
@endphp

<form method="POST" action="{{ $isEdit ? route('logbook.update', $logbook) : route('logbook.store') }}" class="form" novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="field @error('tanggal') has-error @enderror" style="max-width:320px">
        <label for="tanggal">Tanggal <span class="req">*</span></label>
        <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" required data-day-hint="hint-tanggal">
        <div id="hint-tanggal" class="hint"></div>
        @error('tanggal')<div class="err">{{ $message }}</div>@enderror
    </div>

    <div class="field @error('uraian') has-error @enderror">
        <label for="uraian">Uraian kegiatan magang <span class="req">*</span></label>
        <textarea id="uraian" name="uraian" rows="8" maxlength="5000" required placeholder="Tuliskan kegiatan yang dikerjakan pada hari ini…">{{ $uraian }}</textarea>
        @error('uraian')<div class="err">{{ $message }}</div>@enderror
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-primary">@include('partials.icon', ['name' => 'check']) Simpan</button>
        <a href="{{ route('logbook.index') }}" class="btn">Batal</a>
    </div>
</form>

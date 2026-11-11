{{--
    Month picker (pilih bulan & tahun). Pemakaian:
    @include('partials.month-picker', ['name' => 'bulan', 'value' => '2026-11'])
    Memilih bulan -> kalender menutup & kolom menampilkan "November 2026". Nilai dikirim sebagai YYYY-MM.
--}}
@php
    $mpNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $mpValue = preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', (string) ($value ?? ''), $mpM) ? $value : now()->format('Y-m');
    [$mpY, $mpMo] = array_map('intval', explode('-', $mpValue));
    $mpLabel = $mpNames[$mpMo - 1] . ' ' . $mpY;
@endphp

@once
<style>
    .mp{position:relative;min-width:220px}
    .mp-btn{display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;border:1px solid #cdd5e4;border-radius:10px;background:#fff;color:var(--ink);font:inherit;cursor:pointer;text-align:left}
    .mp-btn:focus-visible,.mp-btn[aria-expanded="true"]{outline:2px solid #9ec5f2;border-color:var(--brand)}
    .mp-btn svg{width:18px;height:18px;flex:none;color:var(--muted)}
    .mp-btn .mp-label{flex:1;font-weight:600}
    .mp-btn .mp-chev{transition:transform .15s}
    .mp-btn[aria-expanded="true"] .mp-chev{transform:rotate(180deg)}
    .mp-pop{position:absolute;z-index:40;top:calc(100% + 6px);left:0;width:290px;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 10px 30px rgba(16,24,40,.18);padding:12px}
    .mp-pop[hidden]{display:none}
    .mp-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
    .mp-head b{font-size:1rem}
    .mp-nav{width:32px;height:32px;border:1px solid var(--line);border-radius:8px;background:#fff;cursor:pointer;display:grid;place-items:center;color:var(--ink)}
    .mp-nav:hover{background:#f1f4fa}
    .mp-nav svg{width:16px;height:16px}
    .mp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
    .mp-grid button{padding:10px 0;border:1px solid transparent;border-radius:10px;background:#f6f8fc;font:inherit;font-weight:600;color:var(--ink);cursor:pointer}
    .mp-grid button:hover{background:var(--brand-soft)}
    .mp-grid button.now{border-color:#9ec5f2}
    .mp-grid button.on{background:var(--brand);color:#fff}
    @media (max-width:520px){.mp{width:100%}.mp-pop{width:100%}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    document.querySelectorAll('[data-mp]').forEach(function (root) {
        var hidden = root.querySelector('input[type="hidden"]');
        var btn    = root.querySelector('[data-mp-btn]');
        var label  = root.querySelector('[data-mp-label]');
        var pop    = root.querySelector('[data-mp-pop]');
        var yearEl = root.querySelector('[data-mp-year]');
        var grid   = root.querySelector('[data-mp-grid]');
        var now    = new Date();
        var p      = (hidden.value || '').split('-').map(Number);
        var sel    = { y: p[0] || now.getFullYear(), m: p[1] || now.getMonth() + 1 };
        var viewY  = sel.y;

        function render() {
            yearEl.textContent = viewY;
            grid.innerHTML = '';
            SHORT.forEach(function (nama, i) {
                var b = document.createElement('button');
                b.type = 'button';
                b.textContent = nama;
                var cls = [];
                if (sel.y === viewY && sel.m === i + 1) cls.push('on');
                if (now.getFullYear() === viewY && now.getMonth() === i) cls.push('now');
                b.className = cls.join(' ');
                b.addEventListener('click', function () {
                    sel = { y: viewY, m: i + 1 };
                    hidden.value = viewY + '-' + ('0' + (i + 1)).slice(-2);
                    label.textContent = BULAN[i] + ' ' + viewY;
                    close();
                });
                grid.appendChild(b);
            });
        }
        function open()  { viewY = sel.y; render(); pop.hidden = false; btn.setAttribute('aria-expanded', 'true'); }
        function close() { pop.hidden = true; btn.setAttribute('aria-expanded', 'false'); }

        btn.addEventListener('click', function () { pop.hidden ? open() : close(); });
        root.querySelector('[data-mp-prev]').addEventListener('click', function () { viewY--; render(); });
        root.querySelector('[data-mp-next]').addEventListener('click', function () { viewY++; render(); });
        document.addEventListener('click', function (e) { if (!root.contains(e.target)) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    });
});
</script>
@endonce

<div class="mp" data-mp>
    <input type="hidden" name="{{ $name ?? 'bulan' }}" value="{{ $mpValue }}">
    <button type="button" class="mp-btn" data-mp-btn aria-haspopup="true" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
        <span class="mp-label" data-mp-label>{{ $mpLabel }}</span>
        <svg class="mp-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div class="mp-pop" data-mp-pop hidden>
        <div class="mp-head">
            <button type="button" class="mp-nav" data-mp-prev aria-label="Tahun sebelumnya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg></button>
            <b data-mp-year>{{ $mpY }}</b>
            <button type="button" class="mp-nav" data-mp-next aria-label="Tahun berikutnya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button>
        </div>
        <div class="mp-grid" data-mp-grid></div>
    </div>
</div>
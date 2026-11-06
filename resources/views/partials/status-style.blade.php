<style>
    .seg{display:flex;flex-wrap:wrap;gap:8px}
    .field .seg label{display:inline-flex;align-items:center;gap:6px;margin:0;padding:9px 18px;border:1px solid #cdd5e4;border-radius:999px;cursor:pointer;font-weight:600;background:#fff}
    .seg input{position:absolute;opacity:0;pointer-events:none}
    .field .seg label:has(input:checked){background:var(--brand-soft);border-color:var(--brand);color:var(--brand)}
    .field .seg label.izin:has(input:checked){background:#fff3dc;border-color:#c77700;color:#8a5200}
    .field .seg label.sakit:has(input:checked){background:var(--bad-soft);border-color:var(--bad);color:var(--bad)}
    .badge.izin{background:#fff3dc;color:#8a5200}
    .badge.sakit{background:var(--bad-soft);color:var(--bad)}
    table.rekap td.st-izin{background:#fff3dc;font-weight:700;color:#8a5200}
    table.rekap td.st-sakit{background:var(--bad-soft);font-weight:700;color:var(--bad)}
</style>

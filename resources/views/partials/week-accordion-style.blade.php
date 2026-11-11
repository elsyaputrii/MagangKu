@once
<style>
    .acc{border:1px solid var(--line);border-radius:var(--radius);background:#fff;margin-top:12px}
    .acc > summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px}
    .acc > summary::-webkit-details-marker{display:none}
    .acc > summary:hover{background:#fafcff;border-radius:var(--radius)}
    .acc-title{font-weight:700;font-size:1.02rem}
    .acc-sub{color:var(--muted);font-size:.85rem;margin-top:3px}
    .acc-hint{color:var(--muted);font-size:.8rem;margin-top:2px}
    .acc[open] .acc-hint{display:none}
    .acc .chev{width:20px;height:20px;flex:none;color:var(--muted);transition:transform .2s}
    .acc[open] .chev{transform:rotate(180deg)}
    .acc-body{border-top:1px solid var(--line);margin:0 18px;padding:12px 0 6px}
    .filters{align-items:flex-end}
</style>
@endonce
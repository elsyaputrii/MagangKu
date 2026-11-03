@php
    $paths = [
        'home'     => '<path d="M3 11l9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'book'     => '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M8 7h7M8 11h7"/>',
        'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'plus'     => '<path d="M12 5v14M5 12h14"/>',
        'sheet'    => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 9h16M10 3v18"/>',
        'file'     => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
        'check'    => '<path d="M5 12l5 5L20 7"/>',
        'alert'    => '<path d="M12 3l10 18H2z"/><path d="M12 10v5M12 18v.01"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'edit'     => '<path d="M4 20h4L19 9l-4-4L4 16z"/>',
        'trash'    => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/>',
        'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
    ];
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>

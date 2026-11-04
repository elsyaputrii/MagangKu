<?php

return [
    // Jam kerja minimum per hari (jam). Di bawah ini ditandai "tidak dihitung".
    'min_jam_kerja' => 9,

    // Kota default untuk blok tanda tangan.
    'kota_default' => 'Batam',

    // File template acuan format export.
    'template_presensi_xlsx' => resource_path('templates/Form_Presensi_Magang.xlsx'),
    'template_logbook_docx'  => resource_path('templates/Borang_Logbook_Magang.docx'),
    'logo_path'              => public_path('images/logo-polibatam.png'),
];

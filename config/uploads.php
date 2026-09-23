<?php

return [
    // Laravel menghitung batas rule "max" untuk file dalam KiB.
    'max_file_size_kb' => 1024 * 1024,

    // File template pada menu Keterangan Kelengkapan Administrasi.
    'template_max_file_size_kb' => 800 * 1024,

    'template_extensions' => ['pdf', 'doc', 'docx'],
    'template_mime_types' => [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ],
];

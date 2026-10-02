<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Resmi Usaha Kopi Hiku Himu
    |--------------------------------------------------------------------------
    |
    | Satu sumber kebenaran untuk identitas usaha yang digunakan di seluruh
    | keluaran dokumen penjualan: Faktur Penjualan (A4), Struk Thermal,
    | dan Pesan Nota WhatsApp.
    |
    */

    'name' => env('BUSINESS_NAME', 'Kopi Hiku Himu'),
    'tagline' => env('BUSINESS_TAGLINE', 'Roastery & Distribusi Kopi'),
    'address' => env('BUSINESS_ADDRESS', 'Jl. Letkol Subadri, Ngangkrik, Triharjo, Sleman, D.I. Yogyakarta'),
    'phone' => env('BUSINESS_PHONE', '0812-1287-8844'),
    'email' => env('BUSINESS_EMAIL', 'tokokopihikuhimu@gmail.com'),
    'website' => env('BUSINESS_WEBSITE', 'kopihikuhimu.id'),
    'public_url' => env('BUSINESS_PUBLIC_URL', 'https://kopihikuhimu.id'),

    /*
    |--------------------------------------------------------------------------
    | Rekening Bank Pembayaran
    |--------------------------------------------------------------------------
    |
    | Rekening resmi BRI atas nama AGUNG WAHYU WID...
    |
    */
    'bank' => [
        'name' => env('BUSINESS_BANK_NAME', 'BRI'),
        'account_number' => env('BUSINESS_BANK_ACCOUNT', '306101061146539'),
        'account_name' => env('BUSINESS_BANK_HOLDER', 'AGUNG WAHYU WID...'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Penanda Tangan Dokumen Resmi
    |--------------------------------------------------------------------------
    */
    'signer' => [
        'name' => env('BUSINESS_SIGNER_NAME', 'Agung Wahyu W.'),
        'title' => env('BUSINESS_SIGNER_TITLE', 'Pengelola Kopi Hiku Himu'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Syarat & Ketentuan Baku (Maksimal 3 Butir)
    |--------------------------------------------------------------------------
    */
    'terms' => [
        'Komplain kualitas barang dilayani maksimal 1x24 jam sejak barang diterima.',
        'Simpan produk kopi di tempat yang sejuk, kering, dan tertutup rapat.',
        'Pemeriksaan jumlah fisik wajib dilakukan saat serah terima barang.',
    ],
];

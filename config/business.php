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
    'phone' => env('BUSINESS_PHONE', '0889-5744-289'),
    'email' => env('BUSINESS_EMAIL', 'halo@kopihikuhimu.id'),
    'website' => env('BUSINESS_WEBSITE', 'kopihikuhimu.id'),

    /*
    |--------------------------------------------------------------------------
    | Rekening Bank Pembayaran
    |--------------------------------------------------------------------------
    |
    | Nilai contoh seperti "BCA 123-456-7890" ditandai belum diisi dan
    | tidak boleh dicetak seolah-olah data asli. Isi via .env jika sudah ada.
    |
    */
    'bank' => [
        'name' => env('BUSINESS_BANK_NAME', null),
        'account_number' => env('BUSINESS_BANK_ACCOUNT', null),
        'account_name' => env('BUSINESS_BANK_HOLDER', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Penanda Tangan Dokumen Resmi
    |--------------------------------------------------------------------------
    */
    'signer' => [
        'name' => env('BUSINESS_SIGNER_NAME', 'Pengelola Roastery'),
        'title' => env('BUSINESS_SIGNER_TITLE', 'Kopi Hiku Himu'),
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

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas situs
    |--------------------------------------------------------------------------
    |
    | Dipakai komponen <x-seo> untuk menyusun title, meta description,
    | Open Graph, Twitter Card, dan structured data JSON-LD. Semua nilai
    | bisa ditimpa lewat .env agar tidak perlu menyentuh kode saat dipasang
    | di server toko yang berbeda.
    |
    */

    'site_name'   => env('SEO_SITE_NAME', 'Phone Repair'),

    'title'       => env('SEO_TITLE', 'Sistem Pengelolaan Layanan Servis Smartphone'),

    'description' => env('SEO_DESCRIPTION', 'Lacak status servis smartphone Anda secara online. Phone Repair mencatat setiap unit yang masuk, memantau progres perbaikan, dan menerbitkan invoice servis secara digital.'),

    'keywords'    => env('SEO_KEYWORDS', 'servis hp, servis smartphone, cek status servis, ganti lcd, service handphone, phone repair'),

    'locale'      => env('SEO_LOCALE', 'id_ID'),

    'author'      => env('SEO_AUTHOR', 'Phone Repair'),

    /*
    |--------------------------------------------------------------------------
    | Gambar pratinjau saat tautan dibagikan
    |--------------------------------------------------------------------------
    */

    'image'       => env('SEO_IMAGE', 'images/logo-phone-repair.png'),

    /*
    |--------------------------------------------------------------------------
    | Data usaha untuk structured data LocalBusiness
    |--------------------------------------------------------------------------
    |
    | Kolom yang dibiarkan kosong otomatis tidak ikut ditulis ke JSON-LD,
    | sehingga tidak ada data karangan yang dikirim ke mesin pencari.
    |
    */

    'business' => [
        'telephone' => env('SEO_TELEPHONE'),
        'street'    => env('SEO_STREET'),
        'city'      => env('SEO_CITY'),
        'region'    => env('SEO_REGION'),
        'postal'    => env('SEO_POSTAL'),
        'country'   => env('SEO_COUNTRY', 'ID'),
        'hours'     => env('SEO_OPENING_HOURS'),
    ],

];

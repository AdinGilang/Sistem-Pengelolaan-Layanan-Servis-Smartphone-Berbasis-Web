@props([
    'title'       => null,
    'description' => null,
    'keywords'    => null,
    'image'       => null,
    'type'        => 'website',
    'noindex'     => false,
    'schema'      => null,
])

@php
    $siteName    = config('seo.site_name');
    $pageTitle   = $title ? $title . ' — ' . $siteName : $siteName . ' — ' . config('seo.title');
    $description = $description ?: config('seo.description');
    $keywords    = $keywords ?: config('seo.keywords');
    $canonical   = url()->current();
    $imageUrl    = $image ? asset($image) : asset(config('seo.image'));
    $business    = array_filter(config('seo.business', []));
@endphp

<title>{{ $pageTitle }}</title>

<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 155) }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ config('seo.author') }}">

{{-- Halaman yang berisi data pelanggan tidak boleh masuk indeks mesin pencari. --}}
@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
@endif

{{-- Open Graph: tampilan pratinjau saat tautan dibagikan di WhatsApp/Facebook --}}
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:locale" content="{{ config('seo.locale') }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 155) }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $imageUrl }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 155) }}">
<meta name="twitter:image" content="{{ $imageUrl }}">

<meta name="theme-color" content="#1a2035">

@unless ($noindex)
    {{-- Structured data. Kolom alamat dan telepon hanya ikut kalau memang
         diisi di konfigurasi, supaya tidak ada data karangan yang dikirim
         ke mesin pencari. --}}
    @php
        $jsonLd = $schema ?: [
            '@context'    => 'https://schema.org',
            '@type'       => 'LocalBusiness',
            'name'        => $siteName,
            'description' => strip_tags($description),
            'url'         => url('/'),
            'image'       => $imageUrl,
        ];

        if (! $schema) {
            if (isset($business['telephone'])) {
                $jsonLd['telephone'] = $business['telephone'];
            }

            if (isset($business['hours'])) {
                $jsonLd['openingHours'] = $business['hours'];
            }

            $alamat = array_filter([
                'streetAddress'   => $business['street'] ?? null,
                'addressLocality' => $business['city'] ?? null,
                'addressRegion'   => $business['region'] ?? null,
                'postalCode'      => $business['postal'] ?? null,
                'addressCountry'  => $business['country'] ?? null,
            ]);

            if (count($alamat) > 1) {
                $jsonLd['address'] = ['@type' => 'PostalAddress'] + $alamat;
            }
        }
    @endphp

    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endunless

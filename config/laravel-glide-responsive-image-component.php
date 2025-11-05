<?php
// config for Johncarter/LaravelGlideResponsiveImageComponent
return [
    'breakpoints' => [
        'xs' => 480,
        'sm' => 640,
        'md' => 768,
        'lg' => 1024,
        'xl' => 1280,
        '2xl' => 1536,
    ],

    'conversion_directory' => public_path('assets/conversions'),
    'conversion_url' => url('assets/conversions'),

    // S3 Support
    'use_s3' => env('GLIDE_USE_S3', false),
    's3_disk' => env('GLIDE_S3_DISK', 's3_assets'),
    's3_download_on_demand' => env('GLIDE_S3_DOWNLOAD_ON_DEMAND', true),
    's3_assets_prefix' => env('GLIDE_S3_ASSETS_PREFIX', 'assets'),
];

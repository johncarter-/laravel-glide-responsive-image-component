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
];

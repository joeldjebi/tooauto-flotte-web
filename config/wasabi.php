<?php

return [
    'access_key' => env('WAS_ACCESS_KEY', env('WASABI_ACCESS_KEY_ID')),
    'secret_key' => env('WAS_SECRET_KEY', env('WASABI_SECRET_ACCESS_KEY')),
    'region' => env('WASABI_DEFAULT_REGION', env('WASABI_REGION', env('AWS_DEFAULT_REGION', 'eu-west-1'))),
    'bucket' => env('WASABI_BUCKET', env('AWS_BUCKET')),
    'endpoint' => env('WASABI_ENDPOINT', env('AWS_ENDPOINT')),
    'url' => env('WASABI_URL', env('AWS_URL')),

    'avatar_directory' => env('WASABI_AVATAR_DIRECTORY', 'images/avatar'),
    'chauffeur_image_directory' => env('WASABI_CHAUFFEUR_IMAGE_DIRECTORY', 'images/chauffeur'),
    'autodoc_directory' => env('WASABI_AUTODOC_DIRECTORY', 'images/autodoc'),
    'piece_image_directory' => env('WASABI_PIECE_IMAGE_DIRECTORY', 'images/annonce'),
    'vehicule_image_directory' => env('WASABI_VEHICULE_IMAGE_DIRECTORY', 'images/vehicules'),
];

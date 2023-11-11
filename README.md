# bluesky-api-php

## Installation

`composer require mov/php-bluesky:dev-main`

## Basic Usage

```php
// Simple text post
$blueskyClient = new BlueskyClient();
$blueskyClient->authenticate('<YOUR BLUESKY HANDLE>', '<YOUR APP PASSWORD>');
$blueskyClient->post('Post something.');

// Upload image and post it
$blueskyClient = new BlueskyClient();
$blueskyClient->authenticate('<YOUR BLUESKY HANDLE>', '<YOUR APP PASSWORD>');
$image = file_get_contents('<PATH/TO/IMAGE>');
$contentType = mime_content_type('<PATH/TO/IMAGE>');
$responseJson = $blueskyClient->uploadBlob($image, $contentType);
$response = json_decode($responseJson, true);
$embed = [
    '$type' => 'app.bsky.embed.images',
    'images' => [
        [
            'alt' => 'A test image',
            'image' => $response['blob'],
        ],
    ]
];
$blueskyClient->post('Additional text', $embed);
```
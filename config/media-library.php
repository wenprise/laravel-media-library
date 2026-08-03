<?php

use Wenprise\MediaLibrary\Enums\MediaKind;
use Wenprise\MediaLibrary\Http\Controllers\MediaAssetController;
use Wenprise\MediaLibrary\Models\MediaAsset;
use Wenprise\MediaLibrary\Models\MediaAttachment;

return [
    'enums' => [
        'kind' => MediaKind::class,
    ],
    'models' => [
        'media' => MediaAsset::class,
        'attachment' => MediaAttachment::class,
        'user' => config('auth.providers.users.model', 'App\\Models\\User'),
    ],
    'tables' => [
        'media' => 'media_assets',
        'attachments' => 'media_attachments',
    ],
    'upload' => [
        'disk' => 'public',
        'directory' => 'media',
        'max_kilobytes' => 20480,
        'mimes' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'pdf', 'doc', 'docx'],
    ],
    'routes' => [
        'enabled' => true,
        'prefix' => 'api/media',
        'name_prefix' => 'media.',
        'middleware' => ['web', 'auth'],
        'controller' => MediaAssetController::class,
    ],
    'usage_inspectors' => [],
];

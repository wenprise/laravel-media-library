# Laravel Media Library

A reusable Laravel media library for uploads, metadata, downloads, polymorphic attachments, and protected deletion.

## Requirements

- PHP 8.3 or newer
- Laravel 12 or 13

## Installation

```bash
composer require wenprise/laravel-media-library
php artisan vendor:publish --tag=media-library-config
php artisan vendor:publish --tag=media-library-migrations
php artisan migrate
php artisan storage:link
```

The package auto-discovers `Wenprise\MediaLibrary\MediaLibraryServiceProvider` and registers the configured media routes.

## Configuration

Configure the host models, tables, upload disk, route middleware, and business-specific usage inspectors in `config/media-library.php`.

```php
return [
    'models' => [
        'media' => App\Models\MediaAsset::class,
        'attachment' => App\Models\MediaAttachment::class,
        'user' => App\Models\User::class,
    ],
    'routes' => [
        'enabled' => true,
        'prefix' => 'api/admin/media',
        'name_prefix' => 'admin.media.',
        'middleware' => ['web', 'auth', 'permission:media.manage'],
        'controller' => App\Http\Controllers\Admin\MediaAssetController::class,
    ],
    'usage_inspectors' => [App\Media\ProjectMediaUsageInspector::class],
];
```

Existing applications can subclass the supplied models and controller to retain their namespaces without copying package logic.

## Attachments

Add `Wenprise\MediaLibrary\Concerns\HasMediaAttachments` to a business model to expose sorted polymorphic media relationships.

## Protected deletion

The package blocks deletion when the generic attachment table references an asset. Implement `Wenprise\MediaLibrary\Contracts\MediaUsageInspector` for direct business foreign keys such as Hero, cover, QR-code, or settings media IDs.

## API

The default controller provides list, upload, statistics, detail, metadata update, download, single delete, and atomic batch delete endpoints.

## License

MIT

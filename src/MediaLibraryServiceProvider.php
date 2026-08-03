<?php

namespace Wenprise\MediaLibrary;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * 注册媒体库配置、路由和可发布迁移。
 */
class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * 合并媒体库默认配置。
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/media-library.php', 'media-library');
    }

    /**
     * 注册可配置路由并发布配置和迁移。
     */
    public function boot(): void
    {
        if (config('media-library.routes.enabled', true)) {
            Route::prefix(config('media-library.routes.prefix'))
                ->name(config('media-library.routes.name_prefix'))
                ->middleware(config('media-library.routes.middleware', []))
                ->group(__DIR__.'/../routes/media.php');
        }

        $this->publishes([
            __DIR__.'/../config/media-library.php' => config_path('media-library.php'),
        ], 'media-library-config');
        $this->publishes([
            __DIR__.'/../database/migrations/create_media_library_tables.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_media_library_tables.php'),
        ], 'media-library-migrations');
    }
}

<?php

namespace Wenprise\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wenprise\MediaLibrary\Enums\MediaCollection;

/**
 * 表示业务模型关联的媒体集合、封面标记和顺序。
 */
#[Fillable(['media_asset_id', 'collection', 'is_primary', 'sort_order'])]
class MediaAttachment extends Model
{
    /**
     * 返回配置的媒体附件表名。
     */
    public function getTable(): string
    {
        return config('media-library.tables.attachments', parent::getTable());
    }

    /**
     * 获取媒体所属的业务模型。
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 获取附件引用的媒体文件。
     */
    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(config('media-library.models.media'), 'media_asset_id');
    }

    /**
     * 返回附件字段的类型转换配置。
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'collection' => MediaCollection::class,
            'is_primary' => 'boolean',
        ];
    }
}

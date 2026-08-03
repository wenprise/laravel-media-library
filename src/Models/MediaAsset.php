<?php

namespace Wenprise\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wenprise\MediaLibrary\Enums\MediaKind;

/**
 * 表示可上传、复用和软删除的媒体文件。
 */
#[Fillable(['disk', 'path', 'original_name', 'title', 'mime_type', 'kind', 'size', 'width', 'height', 'alt_text', 'description', 'uploaded_by'])]
class MediaAsset extends Model
{
    use SoftDeletes;

    /**
     * 返回配置的媒体表名。
     */
    public function getTable(): string
    {
        return config('media-library.tables.media', parent::getTable());
    }

    /**
     * 获取上传该媒体的用户。
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(config('media-library.models.user'), 'uploaded_by');
    }

    /**
     * 获取引用该媒体的通用附件记录。
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(config('media-library.models.attachment'), 'media_asset_id');
    }

    /**
     * 返回媒体字段的类型转换配置。
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => config('media-library.enums.kind', MediaKind::class),
            'alt_text' => 'array',
        ];
    }
}

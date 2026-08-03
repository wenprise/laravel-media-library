<?php

namespace Wenprise\MediaLibrary\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * 为业务模型提供排序后的媒体附件关系。
 */
trait HasMediaAttachments
{
    /**
     * 获取模型的媒体附件记录。
     */
    public function mediaAttachments(): MorphMany
    {
        return $this->morphMany(config('media-library.models.attachment'), 'attachable')
            ->orderBy('sort_order');
    }

    /**
     * 获取模型关联的媒体资源。
     */
    public function mediaAssets(): MorphToMany
    {
        return $this->morphToMany(
            config('media-library.models.media'),
            'attachable',
            config('media-library.tables.attachments'),
        )->withPivot(['collection', 'is_primary', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}

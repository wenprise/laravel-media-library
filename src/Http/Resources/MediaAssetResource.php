<?php

namespace Wenprise\MediaLibrary\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * 将媒体模型转换为稳定的前端数据结构。
 */
class MediaAssetResource extends JsonResource
{
    /**
     * 返回媒体选择器和管理页需要的字段。
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'original_name' => $this->original_name,
            'title' => $this->title,
            'mime_type' => $this->mime_type,
            'kind' => $this->kind instanceof \BackedEnum ? $this->kind->value : $this->kind,
            'path' => $this->path,
            'url' => Storage::disk($this->disk)->url($this->path),
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'alt_text' => $this->alt_text,
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace Wenprise\MediaLibrary\Enums;

/**
 * 定义媒体库支持的通用文件类型。
 */
enum MediaKind: string
{
    case Image = 'image';
    case Video = 'video';
    case Document = 'document';

    /**
     * 根据 MIME 类型返回对应媒体类型。
     */
    public static function fromMimeType(string $mime_type): self
    {
        return match (true) {
            str_starts_with($mime_type, 'image/') => self::Image,
            str_starts_with($mime_type, 'video/') => self::Video,
            default => self::Document,
        };
    }
}

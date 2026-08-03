<?php

namespace Wenprise\MediaLibrary\Enums;

/**
 * 定义媒体附件的常用业务集合。
 */
enum MediaCollection: string
{
    case Logo = 'logo';
    case Cover = 'cover';
    case Gallery = 'gallery';
    case Detail = 'detail';
    case Preview = 'preview';
    case QrCode = 'qr_code';

    /**
     * 返回集合的用户可读中文标签。
     */
    public function label(): string
    {
        return match ($this) {
            self::Logo => 'Logo',
            self::Cover => '封面图',
            self::Gallery => '相册',
            self::Detail => '详情图',
            self::Preview => '预览图',
            self::QrCode => '二维码',
        };
    }
}

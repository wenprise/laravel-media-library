<?php

namespace Wenprise\MediaLibrary\Contracts;

use Wenprise\MediaLibrary\Models\MediaAsset;

/**
 * 检查媒体是否仍被具体项目的业务数据引用。
 */
interface MediaUsageInspector
{
    /**
     * 返回阻止删除的用户可读原因，未使用时返回 null。
     */
    public function usageMessage(MediaAsset $media): ?string;
}

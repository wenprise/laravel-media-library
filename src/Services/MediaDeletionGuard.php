<?php

namespace Wenprise\MediaLibrary\Services;

use Illuminate\Validation\ValidationException;
use Wenprise\MediaLibrary\Contracts\MediaUsageInspector;
use Wenprise\MediaLibrary\Models\MediaAsset;

/**
 * 统一执行通用附件和项目自定义引用保护。
 */
class MediaDeletionGuard
{
    /**
     * 确保媒体没有任何通用附件或项目业务引用。
     */
    public function ensureUnused(MediaAsset $media): void
    {
        if ($media->attachments()->exists()) {
            $this->fail('该媒体仍被业务附件使用，不能删除。');
        }

        foreach (config('media-library.usage_inspectors', []) as $inspector_class) {
            $inspector = app($inspector_class);
            if (! $inspector instanceof MediaUsageInspector) {
                throw new \LogicException("{$inspector_class} 必须实现 MediaUsageInspector。");
            }

            $message = $inspector->usageMessage($media);
            if ($message !== null) {
                $this->fail($message);
            }
        }
    }

    /**
     * 抛出媒体字段使用的标准验证错误。
     */
    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['media' => $message]);
    }
}

<?php

namespace Wenprise\MediaLibrary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 校验媒体上传文件和可编辑元数据。
 */
class MediaUploadRequest extends FormRequest
{
    /**
     * 将授权交由路由中间件处理。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 返回媒体上传字段规则。
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $max_kilobytes = (int) config('media-library.upload.max_kilobytes', 20480);
        $mimes = implode(',', config('media-library.upload.mimes', []));

        return [
            'file' => ['required', 'file', "max:{$max_kilobytes}", "mimes:{$mimes}"],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'array'],
            'alt_text.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

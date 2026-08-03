<?php

namespace Wenprise\MediaLibrary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 校验媒体元数据编辑请求。
 */
class MediaUpdateRequest extends FormRequest
{
    /**
     * 将授权交由路由中间件处理。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 返回媒体元数据编辑规则。
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'array'],
            'alt_text.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

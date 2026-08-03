<?php

namespace Wenprise\MediaLibrary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 校验媒体库批量删除编号。
 */
class MediaBatchDeleteRequest extends FormRequest
{
    /**
     * 将授权交由路由中间件处理。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 返回批量删除字段规则。
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists(config('media-library.tables.media'), 'id'),
            ],
        ];
    }
}

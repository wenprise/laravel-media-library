<?php

namespace Wenprise\MediaLibrary\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Wenprise\MediaLibrary\Enums\MediaKind;
use Wenprise\MediaLibrary\Http\Requests\MediaBatchDeleteRequest;
use Wenprise\MediaLibrary\Http\Requests\MediaUpdateRequest;
use Wenprise\MediaLibrary\Http\Requests\MediaUploadRequest;
use Wenprise\MediaLibrary\Http\Resources\MediaAssetResource;
use Wenprise\MediaLibrary\Models\MediaAsset;
use Wenprise\MediaLibrary\Services\MediaDeletionGuard;

/**
 * 提供可配置的媒体查询、上传、编辑、下载和安全删除接口。
 */
class MediaAssetController extends Controller
{
    /**
     * 注入媒体删除保护服务。
     */
    public function __construct(private readonly MediaDeletionGuard $deletion_guard) {}

    /**
     * 返回支持类型和关键词筛选的媒体分页列表。
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->mediaQuery()->latest();
        $query->when($request->string('kind')->isNotEmpty(), fn (Builder $builder) => $builder->where('kind', $request->string('kind')->toString()));
        $query->when($request->string('keyword')->isNotEmpty(), function (Builder $builder) use ($request): void {
            $keyword = $request->string('keyword')->toString();
            $builder->where(function (Builder $keyword_query) use ($keyword): void {
                $keyword_query->where('original_name', 'like', '%'.$keyword.'%')
                    ->orWhere('title', 'like', '%'.$keyword.'%');
            });
        });

        return MediaAssetResource::collection($query->paginate($request->integer('page_size', 24))->withQueryString());
    }

    /**
     * 保存上传文件并登记媒体记录。
     */
    public function store(MediaUploadRequest $request): MediaAssetResource
    {
        $file = $request->file('file');
        $disk = config('media-library.upload.disk', 'public');
        $directory = trim(config('media-library.upload.directory', 'media'), '/').'/'.now()->format('Y/m');
        $path = $file->store($directory, $disk);
        $mime_type = $file->getMimeType() ?: 'application/octet-stream';
        $dimensions = str_starts_with($mime_type, 'image/') ? @getimagesize($file->getRealPath()) : false;
        $media = $this->mediaQuery()->create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'title' => $request->validated('title'),
            'mime_type' => $mime_type,
            'kind' => MediaKind::fromMimeType($mime_type)->value,
            'size' => $file->getSize(),
            'width' => $dimensions ? $dimensions[0] : null,
            'height' => $dimensions ? $dimensions[1] : null,
            'alt_text' => $request->validated('alt_text'),
            'description' => $request->validated('description'),
            'uploaded_by' => $request->user()?->getAuthIdentifier(),
        ]);

        return new MediaAssetResource($media);
    }

    /**
     * 返回指定媒体详情。
     */
    public function show(string $medium): MediaAssetResource
    {
        return new MediaAssetResource($this->findMedia($medium));
    }

    /**
     * 更新媒体标题、替代文本和描述。
     */
    public function update(MediaUpdateRequest $request, string $medium): MediaAssetResource
    {
        $media = $this->findMedia($medium);
        $media->update($request->validated());

        return new MediaAssetResource($media->refresh());
    }

    /**
     * 返回媒体数量、总容量和类型统计。
     */
    public function stats(): JsonResponse
    {
        $media = $this->mediaQuery()->get(['kind', 'size']);
        $type_counts = $media->countBy(fn (MediaAsset $asset): string => $asset->kind instanceof \BackedEnum ? $asset->kind->value : (string) $asset->kind)
            ->map(fn (int $count): int => $count)
            ->all();

        return response()->json(['data' => [
            'total_files' => $media->count(),
            'total_size' => (int) $media->sum('size'),
            'type_counts' => $type_counts,
        ]]);
    }

    /**
     * 在所有媒体通过引用检查后原子批量软删除。
     */
    public function batchDestroy(MediaBatchDeleteRequest $request): Response
    {
        $media = $this->mediaQuery()->whereKey($request->validated('ids'))->get();
        DB::transaction(function () use ($media): void {
            $media->each(fn (MediaAsset $asset) => $this->deletion_guard->ensureUnused($asset));
            $this->mediaQuery()->whereKey($media->modelKeys())->delete();
        });

        return response()->noContent();
    }

    /**
     * 使用原始文件名下载媒体文件。
     */
    public function download(string $medium): StreamedResponse
    {
        $media = $this->findMedia($medium);
        abort_unless(Storage::disk($media->disk)->exists($media->path), 404, '媒体文件不存在。');

        return Storage::disk($media->disk)->download($media->path, $media->original_name);
    }

    /**
     * 软删除未被引用的媒体记录。
     */
    public function destroy(string $medium): Response
    {
        $media = $this->findMedia($medium);
        $this->deletion_guard->ensureUnused($media);
        $media->delete();

        return response()->noContent();
    }

    /**
     * 返回配置媒体模型的查询构造器。
     */
    private function mediaQuery(): Builder
    {
        $model_class = config('media-library.models.media', MediaAsset::class);

        return $model_class::query();
    }

    /**
     * 查找指定媒体或返回 404。
     */
    private function findMedia(string $id): MediaAsset
    {
        return $this->mediaQuery()->findOrFail($id);
    }
}

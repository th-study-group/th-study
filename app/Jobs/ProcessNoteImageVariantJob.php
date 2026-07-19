<?php

namespace App\Jobs;

use App\Models\Note;
use App\Services\ImageProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

/**
 * Undocumented class
 */
class ProcessNoteImageProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 최대 실행 횟수
     *
     * 최초 실행을 포함하여 최대 3번 실행한다.
     */
    public int $tries = 3;

    /**
     * Job 최대 실행 시간
     *
     * 60초가 지나도 끝나지 않으면 실패 처리한다.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $noteIdx,
        public string $sourceThumbnailPath,
        public string $imageType,
        public ?string $previousImagePath = null,
    ) {
        $this->onQueue('media');
    }

    /**
     * 큐 작업 처리
     */
    public function handle(ImageProcessingService $imageProcessingService): void 
    {
        $settings = config("image_processing.types.{$this->imageType}");

        if (! is_array($settings)) {
            throw new InvalidArgumentException("정의되지 않은 이미지 유형입니다: {$this->imageType}");
        }

        $modelColumn = (string) ($settings['model_column'] ?? '');

        if ($modelColumn === '') {
            throw new InvalidArgumentException("이미지 경로 저장 컬럼이 설정되지 않았습니다: {$this->imageType}");
        }

        $note = Note::query()->find($this->noteIdx);

        // Queue가 실행되기 전에 노트가 삭제된 경우
        if (!$note) {
            Log::warning('Note image variant skipped', [
                'action' => 'note_not_found',
                'model' => 'Note',
                'note_idx' => $this->noteIdx,
                'image_type' => $this->imageType,
            ]);

            return;
        }

        /*
         * 사용자가 연속으로 썸네일을 변경한 경우
         * 오래된 Job이 최신 이미지를 덮어쓰지 않도록 검사한다.
         */
        if ((string) $note->thumbnail_path !== $this->sourceThumbnailPath) {
            Log::info('Note image variant skipped', [
                'action' => 'stale_job',
                'model' => 'Note',
                'note_idx' => $this->noteIdx,
                'image_type' => $this->imageType,
                'job_thumbnail_path' => $this->sourceThumbnailPath,
                'current_thumbnail_path' => $note->thumbnail_path,
            ]);

            return;
        }

        Log::info('Note image variant process', [
            'action' => 'process',
            'model' => 'Note',
            'note_idx' => $this->noteIdx,
            'image_type' => $this->imageType,
            'source_thumbnail_path' => $this->sourceThumbnailPath,
        ]);

        /*
         * OG 이미지 등을 실제로 가공하여 파일로 저장한다.
         */
        $newImagePath = $imageProcessingService->process(
            sourcePath: $this->sourceThumbnailPath,
            imageType: $this->imageType
        );

        /*
         * 가공 중 썸네일이 다시 변경되었을 가능성이 있으므로
         * 현재 썸네일 경로가 Job의 원본 경로와 같은 경우에만 갱신한다.
         */
        $updatedCount = Note::query()
            ->where('idx', $this->noteIdx)
            ->where('thumbnail_path', $this->sourceThumbnailPath)
            ->update([$modelColumn => $newImagePath]);

        /*
         * 썸네일이 중간에 변경되어 DB 갱신에 실패한 경우
         * 방금 생성한 사용되지 않는 이미지를 삭제한다.
         */
        if ($updatedCount === 0) {
            Storage::disk('public')->delete($newImagePath);

            Log::info('Note image variant discarded', [
                'action' => 'discard',
                'model' => 'Note',
                'note_idx' => $this->noteIdx,
                'image_type' => $this->imageType,
                'generated_path' => $newImagePath,
            ]);

            return;
        }

        /*
         * DB가 새 이미지 경로로 정상 변경된 뒤에만
         * 이전 이미지를 삭제한다.
         */
        $this->deletePreviousImage(
            $newImagePath
        );

        Log::info('Note image variant completed', [
            'action' => 'completed',
            'model' => 'Note',
            'note_idx' => $this->noteIdx,
            'image_type' => $this->imageType,
            'model_column' => $modelColumn,
            'generated_path' => $newImagePath,
        ]);
    }

    /**
     * 모든 재시도가 실패했을 때 실행된다.
     */
    public function failed(Throwable $exception): void 
    {
        Log::error('Note image variant failed', [
            'action' => 'failed',
            'model' => 'Note',
            'note_idx' => $this->noteIdx,
            'image_type' => $this->imageType,
            'source_thumbnail_path' => $this->sourceThumbnailPath,
            'previous_image_path' => $this->previousImagePath,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * 새로운 이미지 생성이 성공한 뒤 이전 이미지를 삭제한다.
     */
    private function deletePreviousImage(string $newImagePath): void {
        $previousImagePath = $this->normalizePublicPath((string) $this->previousImagePath);

        if ($previousImagePath === '') {
            return;
        }

        if ($previousImagePath === $newImagePath) {
            return;
        }

        Storage::disk('public')->delete($previousImagePath);
    }

    /**
     * /storage/가 포함된 경로를 public 디스크 상대 경로로 변경한다.
     */
    private function normalizePublicPath(string $path): string {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        $normalizedPath = preg_replace(
            '#^/?storage/#',
            '',
            $path
        ) ?? $path;

        return ltrim(
            $normalizedPath,
            '/'
        );
    }
}

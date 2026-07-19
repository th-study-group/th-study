<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use RuntimeException;

class ImageProcessingService
{
    /**
     * 저장된 원본 이미지를 설정된 이미지 유형에 맞게 가공한다.
     *
     * 반환 예:
     * og_image/202607/20260719213015_a2b4c6d8.jpg
     */
    public function process(string $sourcePath, string $imageType): string 
    {
        $settings = config("image_processing.types.{$imageType}");

        if (! is_array($settings)) {
            throw new InvalidArgumentException("정의되지 않은 이미지 유형입니다: {$imageType}");
        }

        $sourcePath = $this->normalizePublicPath($sourcePath);

        if (!Storage::disk('public')->exists($sourcePath)) {
            throw new RuntimeException("원본 이미지 파일이 존재하지 않습니다: {$sourcePath}");
        }

        $sourceAbsolutePath = Storage::disk('public')->path($sourcePath);

        $width = (int) ($settings['width'] ?? 1200);
        $height = (int) ($settings['height'] ?? 630);

        $backgroundBlur = (int) ($settings['background_blur'] ?? 0);
        $backgroundColor = (string) ($settings['background_color'] ?? 'ffffff');

        $format = strtolower((string) ($settings['format'] ?? 'jpg'));

        $quality = (int) ($settings['quality'] ?? 85);

        $manager = ImageManager::gd();

        /*
         * 배경 이미지
         *
         * 1200 × 630 전체 영역을 채우고 흐림 효과를 적용한다.
         */
        $background = $manager
            ->read($sourceAbsolutePath)
            ->orient()
            ->cover($width, $height);

        if ($backgroundBlur > 0) {
            $background->blur($backgroundBlur);
        }

        /*
         * 전경 이미지
         *
         * 원본 비율을 유지하면서 지정된 영역 안에 맞춘다.
         */
        $foreground = $manager
            ->read($sourceAbsolutePath)
            ->orient()
            ->contain($width, $height, $backgroundColor);

        /*
         * 흐린 배경 위 중앙에 원본 비율 이미지를 배치한다.
         */
        $background->place($foreground, 'center');

        $directory = $this->makeMonthlyDirectory((string) $settings['directory']);

        $filename = $this->makeFilename($format);

        $savePath = "{$directory}/{$filename}";

        $encodedImage = match ($format) {
            'png' => $background->encode(new PngEncoder()),
            'jpg', 'jpeg' => $background->encode( new JpegEncoder(quality: $quality)),
            default => throw new InvalidArgumentException("지원하지 않는 이미지 형식입니다: {$format}"),
        };

        Storage::disk('public')->put($savePath, (string) $encodedImage);

        return $savePath;
    }

    /**
     * 이미지 종류별 년월 폴더를 만든다.
     *
     * og_image를 전달하면:
     * og_image/202607
     *
     * youtube_image를 전달하면:
     * youtube_image/202607
     */
    private function makeMonthlyDirectory(string $baseDirectory): string 
    {
        $baseDirectory = trim($baseDirectory, '/');

        if ($baseDirectory === '') {
            throw new InvalidArgumentException('이미지 저장 폴더가 설정되지 않았습니다.');
        }

        return sprintf('%s/%s', $baseDirectory, now()->format('Ym'));
    }

    /**
     * 중복을 방지하는 이미지 파일명을 만든다.
     */
    private function makeFilename(string $extension): string {
        $extension = $extension === 'jpeg'
            ? 'jpg'
            : $extension;

        return sprintf(
            '%s_%s.%s',
            now()->format('YmdHis'),
            Str::lower(Str::random(12)),
            $extension
        );
    }

    /**
     * /storage/가 붙은 경로를 public 디스크 상대 경로로 변환한다.
     */
    private function normalizePublicPath(string $path): string {
        $path = trim($path);

        $normalizedPath = preg_replace('#^/?storage/#',  '', $path) ?? $path;

        return ltrim($normalizedPath, '/');
    }
}
<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * 캐시 생성 서비스
 */
class ContentCacheService
{
    /**
     * 캐시 조회 / 생성
     *
     * @param string $resource
     * @param string $context
     * @param string $scope
     * @param int $limit
     * @param int $ttlMinutes
     * @param Closure $callback
     * @param int|null $userIdx
     * @return mixed
     */
    public function remember(
        string $resource,
        string $context,
        string $scope,
        int $limit,
        int $ttlMinutes,
        Closure $callback,
        ?int $userIdx = null
    ) {
        $this->validate(
            $scope,
            $limit,
            $ttlMinutes,
            $userIdx
        );

        $cacheKey = $this->makeCacheKey(
            $resource,
            $context,
            $scope,
            $limit,
            $userIdx
        );

        $result = Cache::remember(
            $cacheKey,
            now()->addMinutes($ttlMinutes),
            function () use ($callback, $limit) {
                return $callback($limit);
            }
        );

        return $result;
    }

    /**
     * 해당 리소스의 캐시 전체 삭제
     *
     * 예:
     * blog public 캐시 전체 삭제
     * inquiry 특정 사용자 캐시 전체 삭제
     *
     * @param string $resource
     * @param string $scope
     * @param int|null $userIdx
     * @return void
     */
    public function forgetResource(
        string $resource,
        string $scope = 'public',
        ?int $userIdx = null
    ): void {
        $this->validateScope($scope, $userIdx);

        $versionKey = $this->makeVersionKey(
            $resource,
            $scope,
            $userIdx
        );

        if (! Cache::has($versionKey)) {
            Cache::forever($versionKey, 2);

            return;
        }

        Cache::increment($versionKey);
    }

    /**
     * 캐시 키 생성
     */
    private function makeCacheKey(
        string $resource,
        string $context,
        string $scope,
        int $limit,
        ?int $userIdx
    ): string {
        $version = $this->getVersion(
            $resource,
            $scope,
            $userIdx
        );

        $key = sprintf(
            'content:%s:%s:%s:v%d:limit:%d',
            $resource,
            $context,
            $scope,
            $version,
            $limit
        );

        if ($scope === 'user') {
            $key .= ':user:' . $userIdx;
        }

        return $key;
    }

    /**
     * 리소스의 현재 캐시 버전 조회
     *
     * 캐시 버전이 존재하지 않으면 최초 버전인 1을 반환한다.
     * 글 등록/수정/삭제로 버전이 증가하면 새로운 캐시 키가 생성된다.
     *
     * @param string $resource
     * @param string $scope
     * @param int|null $userIdx
     * @return int
     */
    private function getVersion(
        string $resource,
        string $scope,
        ?int $userIdx
    ): int {
        $versionKey = $this->makeVersionKey(
            $resource,
            $scope,
            $userIdx
        );

        return (int) Cache::get($versionKey, 1);
    }

    private function makeVersionKey(
        string $resource,
        string $scope,
        ?int $userIdx
    ): string {
        $key = sprintf(
            'content-cache:version:%s:%s',
            $scope,
            $resource
        );

        if ($scope === 'user') {
            $key .= ':user:' . $userIdx;
        }

        return $key;
    }

    /**
     * 기본값 검증
     */
    private function validate(
        string $scope,
        int $limit,
        int $ttlMinutes,
        ?int $userIdx
    ): void {
        $this->validateScope($scope, $userIdx);

        if ($limit < 1) {
            throw new InvalidArgumentException(
                '캐시 조회 개수는 1 이상이어야 합니다.'
            );
        }

        if ($ttlMinutes < 1) {
            throw new InvalidArgumentException(
                '캐시 유지 시간은 1분 이상이어야 합니다.'
            );
        }
    }

    /**
     * public / user 범위 검증
     */
    private function validateScope(
        string $scope,
        ?int $userIdx
    ): void {
        if (! in_array($scope, ['public', 'user'], true)) {
            throw new InvalidArgumentException(
                '캐시 scope는 public 또는 user만 사용할 수 있습니다.'
            );
        }

        if ($scope === 'user' && empty($userIdx)) {
            throw new InvalidArgumentException(
                '사용자별 캐시는 userIdx가 필요합니다.'
            );
        }
    }
}
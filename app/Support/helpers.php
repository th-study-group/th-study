<?php

use Jenssegers\Agent\Agent;

if (! function_exists('env_default')) {
    /**
     * env 디폴트값 부여
     *
     * @param [type] $key
     * @param [type] $default
     * @return void
     */
    function env_default($key, $default = null)
    {
        $value = env($key);

        if ($value === null) {
            return $default;
        }

        if (is_string($value) && trim($value) === '') {
            return $default;
        }

        return $value;
    }
}

if (! function_exists('detectDeviceType')) {
    /**
     * 접속 디바이스 타입 식별
     */
    function detectDeviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'tablet';
        }

        if ($agent->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    }
}

if (! function_exists('detectDeviceInfo')) {
    /**
     * User-Agent 기반 디바이스 제조사/모델 식별
     *
     * @return array{device_brand: ?string, device_model: ?string}
     */
    function detectDeviceInfo(?string $userAgent): array
    {
        $ua = $userAgent ?? '';

        $deviceBrand = null;
        $deviceModel = null;

        if (stripos($ua, 'iPhone') !== false) {
            $deviceBrand = 'Apple';
            $deviceModel = 'iPhone';
        } elseif (stripos($ua, 'iPad') !== false) {
            $deviceBrand = 'Apple';
            $deviceModel = 'iPad';
        } elseif (preg_match('/SM-[A-Z0-9]+/i', $ua, $matches)) {
            $deviceBrand = 'Samsung';
            $deviceModel = strtoupper($matches[0]);
        } elseif (stripos($ua, 'Galaxy') !== false) {
            $deviceBrand = 'Samsung';
            $deviceModel = 'Galaxy';
        } elseif (stripos($ua, 'Pixel') !== false) {
            $deviceBrand = 'Google';
            $deviceModel = 'Pixel';
        } elseif (stripos($ua, 'Macintosh') !== false) {
            $deviceBrand = 'Apple';
            $deviceModel = 'Mac';
        } elseif (stripos($ua, 'Windows') !== false) {
            $deviceBrand = 'Microsoft';
            $deviceModel = 'Windows PC';
        }

        return [
            'device_brand' => $deviceBrand,
            'device_model' => $deviceModel,
        ];
    }
}

if (! function_exists('detectBrowserName')) {
    /**
     * User-Agent 기반 브라우저명 식별
     */
    function detectBrowserName(?string $userAgent, Agent $agent): ?string
    {
        $ua = $userAgent ?? '';

        if (stripos($ua, 'Whale') !== false) {
            return 'Whale';
        }

        if (stripos($ua, 'SamsungBrowser') !== false) {
            return 'Samsung Internet';
        }

        if (stripos($ua, 'Edg/') !== false || stripos($ua, 'Edge/') !== false) {
            return 'Edge';
        }

        if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Chromium') === false) {
            return 'Chrome';
        }

        if (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) {
            return 'Safari';
        }

        return $agent->browser();
    }
}

if (! function_exists('sanitize_redirect_target')) {
    /**
     * 경로 설정
     *
     * @param [type] $url
     * @return string
     */
    function sanitize_redirect_target($url): string
    {
        if (!is_string($url)) {
            return '/';
        }

        $target = trim($url);
        if ($target === '') {
            return '/';
        }

        if (str_starts_with($target, '/')) {
            if (str_starts_with($target, '//')) {
                return '/';
            }

            return $target;
        }

        if (preg_match('/^https?:\\/\\//i', $target) === 1) {
            return $target;
        }

        return '/';
    }
}

if (! function_exists('normalize_target_user_ids')) {
    /**
     * 단건/다건 대상 사용자 idx를 정규화
     *
     * @param array $data
     * @return int[]
     */
    function normalize_target_user_ids(array $data): array
    {
        $userIds = [];

        if (isset($data['user_id']) && is_numeric($data['user_id'])) {
            $userIds[] = (int) $data['user_id'];
        }

        if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            foreach ($data['user_ids'] as $userId) {
                if (is_numeric($userId)) {
                    $userIds[] = (int) $userId;
                }
            }
        }

        return array_values(array_unique(array_filter($userIds, function ($id) {
            return $id > 0;
        })));
    }
}

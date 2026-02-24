<?php

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

if (! function_exists('extract_first_image')) {
    /**
     * HTML 내용에서 첫 번째 <img> src 추출
     * - 이미지 있으면: uploads/posts/... 형태로 리턴
     * - 없으면: null
     */
    function extract_first_image(?string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if ($src) {
                // URL → path
                $path = parse_url($src, PHP_URL_PATH);

                // /storage/ → uploads/... 으로 변환
                if ($path && str_starts_with($path, '/storage/')) {
                    return ltrim(substr($path, strlen('/storage/')), '/');
                }

                // 외부 이미지라면 그대로 리턴(필요하면 차단 가능)
                return $src;
            }
        }

        return null;
    }
}
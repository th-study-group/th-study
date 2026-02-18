<?php

if (! function_exists('env_default')) {
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

<?php

namespace Database\Seeders;

use App\Models\NoteCategory;
use App\Models\NoteGroup;
use App\Models\NoteTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NoteMasterSeeder extends Seeder
{
    public function run(): void
    {
        $seed = config('seeders.note', []);
        $defaultCreateUserIdx = (int) ($seed['default_create_user_idx'] ?? 1);

        DB::transaction(function () use ($seed, $defaultCreateUserIdx) {
            $this->seedGroups($seed['groups'] ?? [], $defaultCreateUserIdx);
            $this->seedCategories($seed['categories'] ?? [], $defaultCreateUserIdx);
            $this->seedTopics($seed['topics'] ?? [], $defaultCreateUserIdx);
        });
    }

    private function seedGroups(array $groups, int $defaultCreateUserIdx): void
    {
        foreach ($groups as $row) {
            NoteGroup::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'create_user_idx' => $row['create_user_idx'] ?? $defaultCreateUserIdx,
                    'update_user_idx' => $row['update_user_idx'] ?? null,
                    'delete_user_idx' => null,
                    'delete_datetime' => null,
                ]
            );
        }
    }

    private function seedCategories(array $categories, int $defaultCreateUserIdx): void
    {
        foreach ($categories as $row) {
            $group = NoteGroup::query()->where('code', $row['group_code'])->first();

            if (!$group) {
                throw new RuntimeException("note seed error: group_code not found [{$row['group_code']}]");
            }

            NoteCategory::query()->updateOrCreate(
                [
                    'group_idx' => $group->idx,
                    'code' => $row['code'],
                ],
                [
                    'name' => $row['name'],
                    'memo' => $row['memo'] ?? '',
                    'create_user_idx' => $row['create_user_idx'] ?? $defaultCreateUserIdx,
                    'update_user_idx' => $row['update_user_idx'] ?? null,
                    'delete_user_idx' => null,
                    'delete_datetime' => null,
                ]
            );
        }
    }

    private function seedTopics(array $topics, int $defaultCreateUserIdx): void
    {
        foreach ($topics as $row) {
            $group = NoteGroup::query()->where('code', $row['group_code'])->first();

            if (! $group) {
                throw new RuntimeException("note seed error: group_code not found [{$row['group_code']}]");
            }

            $category = NoteCategory::query()
                ->where('group_idx', $group->idx)
                ->where('code', $row['category_code'])
                ->first();

            if (! $category) {
                throw new RuntimeException("note seed error: category not found [{$row['group_code']}/{$row['category_code']}]");
            }

            NoteTopic::query()->updateOrCreate(
                [
                    'categories_idx' => $category->idx,
                    'name' => $row['name'],
                ],
                [
                    'memo' => $row['memo'] ?? '',
                    'create_user_idx' => $row['create_user_idx'] ?? $defaultCreateUserIdx,
                    'update_user_idx' => $row['update_user_idx'] ?? null,
                    'delete_user_idx' => null,
                    'delete_datetime' => null,
                ]
            );
        }
    }
}

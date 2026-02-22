<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('group_idx')->comment('FK : th_note_groups.idx');
            $table->unsignedBigInteger('categories_idx')->comment('FK : th_note_categories.idx');
            $table->string('group_code', 20)->comment('그룹 코드');
            $table->string('categories_code', 20)->comment('카테고리 코드');
            $table->string('subject', 20)->comment('제목');
            $table->longText('content')->comment('내용');
            $table->string('thumbnail_path', 500)->nullable()->comment('썸네일 경로');
            $table->tinyInteger('use_flag')->default(0)->comment('공개여부');
            $table->unsignedBigInteger('create_user_idx')->comment('등록자');
            $table->unsignedBigInteger('update_user_idx')->nullable()->comment('수정자');
            $table->unsignedBigInteger('delete_user_idx')->nullable()->comment('삭제자');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            $table->dateTime('delete_datetime')->nullable()->comment('삭제시각');

            $table->index('group_idx');
            $table->index('categories_idx');
            $table->index('group_code');
            $table->index('categories_code');
            $table->index('subject');
            $table->index('create_user_idx');
            $table->index('create_datetime');

            $table->comment('노트 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};

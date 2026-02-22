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
        Schema::create('note_categories', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('group_idx')->comment('FK : th_note_groups.idx');
            $table->string('code', 20)->comment('카테고리 코드');
            $table->string('name', 30)->comment('카테고리명');
            $table->string('memo', 255)->comment('비고');
            $table->unsignedBigInteger('create_user_idx')->comment('등록자');
            $table->unsignedBigInteger('update_user_idx')->nullable()->comment('수정자');
            $table->unsignedBigInteger('delete_user_idx')->nullable()->comment('삭제자');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            $table->dateTime('delete_datetime')->nullable()->comment('삭제시각');

            $table->index('group_idx');
            $table->index('code');
            $table->index('name');
            $table->index('create_user_idx');
            $table->index('create_datetime');

            $table->comment('노트 카테고리 테이블');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_categories');
    }
};

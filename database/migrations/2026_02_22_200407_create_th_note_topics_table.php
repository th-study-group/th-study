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
        Schema::create('note_topics', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('categories_idx')->comment('FK : th_note_categories.idx');
            $table->string('name', 30)->comment('주제명');
            $table->string('memo', 255)->comment('비고');
            $table->tinyInteger('use_flag')->default(0)->comment('1 : 사용  0 : 미사용');
            $table->unsignedBigInteger('create_user_idx')->comment('등록자');
            $table->unsignedBigInteger('update_user_idx')->nullable()->comment('수정자');
            $table->unsignedBigInteger('delete_user_idx')->nullable()->comment('삭제자');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            $table->dateTime('delete_datetime')->nullable()->comment('삭제시각');

            $table->index('categories_idx');
            $table->index('name');
            $table->index('create_user_idx');
            $table->index('create_datetime');

            $table->comment('노트 주제 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_topics');
    }
};

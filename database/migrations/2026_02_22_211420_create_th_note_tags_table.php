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
        Schema::create('note_tags', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->string('name', 20)->comment('해시태그명');
            $table->unsignedBigInteger('create_user_idx')->comment('등록자');
            $table->unsignedBigInteger('update_user_idx')->nullable()->comment('수정자');
            $table->unsignedBigInteger('delete_user_idx')->nullable()->comment('삭제자');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            $table->dateTime('delete_datetime')->nullable()->comment('삭제시각');

            $table->index('name');
            $table->index('create_user_idx');
            $table->index('create_datetime');

            $table->comment('노트 해시태그 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_tags');
    }
};

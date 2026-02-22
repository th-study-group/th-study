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
        Schema::create('note_histories', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('note_idx')->comment('FK : th_notes.idx');
            $table->string('job_type', 20)->comment('작업구분 (등록, 수정, 삭제, 조회)');
            $table->ipAddress('ip')->comment('아이피');
            $table->string('user_agent', 512)->comment('User Agent');
            $table->string('referer_url', 2048)->nullable()->comment('접속 referer 원문 URL');
            $table->unsignedBigInteger('create_user_idx')->nullable()->comment('등록자');
            $table->dateTime('create_datetime')->comment('등록시각');

            $table->index('note_idx');
            $table->index('job_type');
            $table->index('ip');
            $table->index('create_user_idx');
            $table->index('create_datetime');

            $table->comment('노트 이력 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_histories');
    }
};

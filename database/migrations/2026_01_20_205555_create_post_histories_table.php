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
        Schema::create('post_histories', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('post_idx')->comment('게시판 FK');
            $table->ipAddress('ip')->comment('아이피');
            $table->string('user_agent', 512)->comment('User Agent');
            $table->string('job_type', 20)->comment('작업구분 (등록, 수정, 삭제, 조회)');
            $table->string('table_name', 64)->nullable()->comment('테이블 유형 (테이블명 그대로)');
            $table->string('status', 15)->nullable()->comment('현재 진행상태');
            $table->string('post_type', 20)->nullable()->comment('게시판 유형');
            $table->dateTime('view_datetime')->comment('조회시각');
            $table->unsignedBigInteger('view_user_idx')->comment('조회자');

            $table->index('post_idx');
            $table->index('job_type');
            $table->index('table_name');

            $table->comment('게시글 이력관리 테이블 post_history (로그인정보 기반)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_histories');
    }
};

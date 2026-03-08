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
        Schema::create('bot_access_logs', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->date('access_date')->comment('접속일');
            $table->dateTime('access_datetime')->comment('접속시각');
            $table->string('access_page', 255)->comment('접속페이지');
            $table->string('referer_host', 255)->comment('접속 도메인');
            $table->string('bot_name', 100)->nullable()->comment('봇 명칭');
            $table->string('referer_url', 2048)->nullable()->comment('접속 referer 원문 URL');
            $table->string('user_agent', 512)->comment('User Agent');

            $table->index(['access_date', 'access_page', 'bot_name']);
            $table->index('referer_host');
 
            $table->comment('봇 사이트 접근 이력 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_access_logs');
    }
};

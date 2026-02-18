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
        Schema::create('web_push_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('user_idx')->comment('th_users FK');
            $table->string('endpoint', 500)->unique()->comment('디바이스 정보');
            $table->string('p256dh', 100)->comment('공개키');
            $table->string('auth', 100)->comment('인증키');
            $table->string('user_agent', 512)->comment('접속 클라이언트의 User-Agent 정보(브라우저/OS 등)');
            $table->dateTime('last_seen_datetime')->nullable()->comment('최근접속시간');
            $table->dateTime('create_datetime')->comment('등록시각');

            $table->index('user_idx');
            $table->index('last_seen_datetime');
            $table->comment('웹 푸시 토큰 구독 정보');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_push_subscriptions');
    }
};

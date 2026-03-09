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
        Schema::create('conversion_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('conversion_date')->comment('전환일자');
            $table->dateTime('conversion_datetime')->comment('전환시각');
            $table->string('access_page', 255)->comment('전환이 발생한 페이지');
            $table->string('conversion_type', 50)->comment('전환유형 - page_view, outbound, login 등');
            $table->string('target_page', 1000)->nullable()->comment('전환 버튼 클릭 후 이동 페이지'); # 클릭한 URL
            $table->string('referer_host', 255)->comment('접속 도메인');
            $table->string('device_type', 20)->comment('접속 디바이스 정보(pc, mobile, tablet');
            $table->string('device_brand', 50)->nullable()->comment('접속 디바이스 기기 제조사 - Apple, sumsung 등');
            $table->string('device_model', 100)->nullable()->comment('접속 기기 모델명');
            $table->string('os', 50)->nullable()->comment('OS 정보 - ios, android, windows, macosx 등');
            $table->string('browser', 50)->nullable()->comment('접속 브라우저 정보 - Chrome Safari Whale Edge 등');
            $table->ipAddress('ip')->comment('아이피');
            $table->string('referer_url', 2048)->nullable()->comment('접속 referer 원문 URL');
            $table->string('user_agent', 512)->comment('User Agent');
            $table->string('session_id', 120)->nullable()->comment('세션 ID');
            $table->unsignedBigInteger('user_idx')->nullable()->comment('FK : th_users.idx');

            $table->index(['conversion_date', 'access_page', 'device_type']);
            $table->index('conversion_type');
            $table->index('referer_host');
            $table->index('ip');
            $table->index('session_id');
            $table->index('user_idx');

            $table->comment('사이트 전환 이력 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_logs');
    }
};

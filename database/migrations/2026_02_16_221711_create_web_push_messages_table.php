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
        Schema::create('web_push_messages', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('user_idx')->comment('사용자 idx (th_users FK)');
            $table->string('endpoint', 500)->comment('푸시 디바이스 정보');
            $table->string('title', 200)->comment('푸시 제목');
            $table->text('body')->comment('푸시 내용');
            $table->string('click_token', 128)->comment('푸시 클릭 시 이력 남기기 위한 정보');
            $table->string('target_url', 1024)->comment('푸시 클릭 시 이동할 내부 경로');
            $table->string('user_agent', 512)->comment('접속 클라이언트의 User-Agent 정보(브라우저/OS 등)');
            $table->string('table_name', 64)->comment('테이블 유형 (테이블명 그대로)');
            $table->dateTime('send_datetime')->comment('보낸 시각');
            $table->dateTime('click_datetime')->nullable()->comment('푸시를 받아서 실제 클릭하여 조회한 시각');

            $table->index('user_idx');
            $table->index('title');
            $table->index('send_datetime');
            
            $table->comment('푸시 전송 내역');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_push_messages');
    }
};

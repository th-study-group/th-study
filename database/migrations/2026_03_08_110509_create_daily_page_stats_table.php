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
        Schema::create('daily_page_stats', function (Blueprint $table) {
            $table->date('stat_date')->comment('집계일자');
            $table->string('access_page', 255)->comment('접속페이지');
            $table->string('device_type', 20)->comment('접속 디바이스 정보(pc, mobile, tablet');
            $table->unsignedInteger('total_access_count')->default(0)->comment('총 접속자 수');
            $table->unsignedInteger('real_access_count')->default(0)->comment('중복 제외한 총 접속자 수');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            
            $table->primary(['stat_date', 'access_page', 'device_type']);
            $table->index('access_page');

            $table->comment('일 페이지별 통계'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_page_stats');
    }
};

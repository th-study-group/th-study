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
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_access_status', 20)
                ->default('pending')
                ->comment('API 접근상태 - 대기 : pending, 승인 : approved, 거절 : rejected, 일시정지 : suspended')
                ->after('ip');

            $table->dateTime('api_access_approved_datetime')
                ->nullable()
                ->comment('API 접근 승인일시')
                ->after('api_access_status');
            
            $table->index('api_access_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['api_access_status']);

            $table->dropColumn([
            'api_access_status',
            'api_access_approved_datetime',
        ]);
        });
    }
};

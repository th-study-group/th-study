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
        Schema::table('web_push_messages', function (Blueprint $table) {
            $table->unsignedTinyInteger('success_flag')
                ->nullable()
                ->after('target_url')
                ->comment('푸시 성공여부 - 성공 1 실패 0');

            $table->json('send_error_message')
                ->nullable()
                ->after('success_flag')
                ->comment('푸시 실패 상세정보(JSON: response_code, response_msg)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_push_messages', function (Blueprint $table) {
            $table->dropColumn('send_error_message');
            $table->dropColumn('success_flag');
        });
    }
};

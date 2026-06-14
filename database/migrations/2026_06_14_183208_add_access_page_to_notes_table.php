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
        Schema::table('notes', function (Blueprint $table) {
            Schema::table('notes', function (Blueprint $table) {
                $table->string('access_page', 255)
                    ->nullable()
                    ->default(null)
                    ->after('use_flag')
                    ->comment('유입통계 매칭용 접근 경로');

                $table->index('access_page');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropIndex(['access_page']);
                $table->dropColumn('access_page');
            });
        });
    }
};

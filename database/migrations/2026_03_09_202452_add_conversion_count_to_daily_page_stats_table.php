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
        Schema::table('daily_page_stats', function (Blueprint $table) {
            $table->unsignedInteger('conversion_count')
                ->default(0)
                ->after('real_access_count')
                ->comment('전환 수');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_page_stats', function (Blueprint $table) {
            $table->dropColumn('conversion_count');
        });
    }
};

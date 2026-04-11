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
        Schema::table('note_categories', function (Blueprint $table) {
            Schema::table('note_categories', function (Blueprint $table) {
                $table->tinyInteger('use_flag')
                    ->default(0)
                    ->comment('1 : 사용  0 : 미사용')
                    ->after('memo');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('note_categories', function (Blueprint $table) {
            Schema::table('note_categories', function (Blueprint $table) {
                $table->dropColumn('use_flag');
            });
        });
    }
};

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
        Schema::table('post_histories', function (Blueprint $table) {
            $table->string('referer_url', 2048)
                ->nullable()
                ->after('user_agent')
                ->comment('접속 referer 원문 URL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_histories', function (Blueprint $table) {
            $table->dropColumn('referer_url');
        });
    }
};

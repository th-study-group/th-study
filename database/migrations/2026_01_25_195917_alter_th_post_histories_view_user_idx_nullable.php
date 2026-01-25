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
            $table->unsignedBigInteger('view_user_idx')
                ->nullable()
                ->comment('조회자 - 미로그인의 경우 null로 표시')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('view_user_idx')
                ->nullable(false)
                ->comment('조회자 - 미로그인의 경우 null로 표시')
                ->change();
        });
    }
};

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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('status', 15)
                ->default('wait')
                ->comment('현재 진행상태')
                ->after('content');

            $table->string('post_type', 20)
                ->nullable()
                ->comment('게시판 유형')
                ->after('status');
                
            $table->index('user_idx');
            $table->index('title');
            $table->index('status');
            $table->index('post_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['user_idx']);
            $table->dropIndex(['title']);
            $table->dropIndex(['status']);
            $table->dropIndex(['post_type']);

            $table->dropColumn('status');
            $table->dropColumn('post_type');
        });
    }
};

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
            $table->renameColumn('view_datetime', 'create_datetime');
            $table->renameColumn('view_user_idx', 'create_user_idx');
        });

        Schema::table('post_histories', function (Blueprint $table) {
            $table->dateTime('create_datetime')
                ->comment('작성시각')
                ->change();
            $table->unsignedBigInteger('create_user_idx')
                ->nullable()
                ->comment('작성자')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_histories', function (Blueprint $table) {
            $table->renameColumn('create_datetime', 'view_datetime');
            $table->renameColumn('create_user_idx', 'view_user_idx');
        });

        Schema::table('post_histories', function (Blueprint $table) {
            $table->dateTime('view_datetime')
                ->comment('조회시각')
                ->change();
            $table->unsignedBigInteger('view_user_idx')
                ->nullable()
                ->comment('조회자 - 미로그인의 경우 null로 표시')
                ->change();
        });
    }
};

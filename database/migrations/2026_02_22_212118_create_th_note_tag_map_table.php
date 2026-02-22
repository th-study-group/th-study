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
        Schema::create('note_tag_map', function (Blueprint $table) {
            $table->unsignedBigInteger('note_idx')->comment('PK/FK : th_notes.idx');
            $table->unsignedBigInteger('tag_idx')->comment('PK/FK : th_note_tags.idx');

            $table->primary(['note_idx', 'tag_idx']);
            $table->index('tag_idx');

            $table->comment('노트-해시태그 매핑 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_tag_map');
    }
};

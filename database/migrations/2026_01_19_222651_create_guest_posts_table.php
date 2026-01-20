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
        Schema::create('guest_posts', function (Blueprint $table) {
            $table->bigIncrements('idx')->comment('PK');
            $table->unsignedBigInteger('user_idx')->comment('th_users FK');
            $table->string('title', 255)->comment('글 제목');
            $table->longText('content')->comment('글 내용');
            $table->string('status', 15)->default('wait')->comment('현재 진행상태');
            $table->string('post_type', 20)->nullable()->comment('게시판 유형');
            $table->unsignedTinyInteger('personal_info_agree')->default(1)->comment('개인정보동의: Y, 미동의 N');
            $table->unsignedTinyInteger('marketing_info_agree')->default(0)->comment('마케팅동의: Y, 미동의 N');
            $table->string('contact_method', 20)->nullable()->comment('연락수단 (email, phone)');
            $table->string('contact_value', 50)->nullable()->comment('연락수단 값');
            $table->text('memo')->nullable()->comment('미인증 고객 처리 결과');
            $table->string('writer', 30)->comment('등록자 : 로그인정보가 없어서 텍스트로 남김');
            $table->unsignedBigInteger('update_user_idx')->nullable()->comment('수정자');
            $table->unsignedBigInteger('delete_user_idx')->nullable()->comment('삭제자');
            $table->dateTime('create_datetime')->comment('등록시각');
            $table->dateTime('update_datetime')->nullable()->comment('수정시각');
            $table->dateTime('delete_datetime')->nullable()->comment('삭제시각');

            $table->index('user_idx');
            $table->index('title');
            $table->index('status');
            $table->index('post_type');
            $table->index('contact_method');
            $table->index('contact_value');
            $table->index('writer');

            $table->comment('미인증 유저 문의 테이블');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_posts');
    }
};

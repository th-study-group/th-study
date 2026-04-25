<?php

return [
    'default_create_user_idx' => 1,

    'groups' => [
        ['code' => 'blog', 'name' => '블로그'],
        ['code' => 'photo', 'name' => '사진'],
        ['code' => 'map', 'name' => '지도'],
        ['code' => 'document', 'name' => '문서'],
        ['code' => 'video', 'name' => '영상'],
    ],

    'categories' => [
        ['group_code' => 'blog', 'code' => 'develop', 'name' => '개발', 'memo' => '개발과 관련된 내용을 정리해요'],
        ['group_code' => 'blog', 'code' => 'tour', 'name' => '여행', 'memo' => '여행 정보를 정리해요'],
        ['group_code' => 'blog', 'code' => 'food', 'name' => '맛집', 'memo' => '맛집 정보를 정리해요'],
        ['group_code' => 'blog', 'code' => 'cafe', 'name' => '카페', 'memo' => '카페 정보를 정리해요'],
        ['group_code' => 'blog', 'code' => 'economy', 'name' => '경제', 'memo' => '경제 정보를 정리해요'],
        ['group_code' => 'blog', 'code' => 'shopping', 'name' => '쇼핑', 'memo' => '쇼핑 관련 후기 정리해요.'],
        ['group_code' => 'blog', 'code' => 'shopping', 'name' => '쇼핑', 'memo' => '쇼핑 관련 후기 정리해요.'],
        ['group_code' => 'blog', 'code' => 'real-estate', 'name' => '부동산', 'memo' => '부동산 관련 정보 정리해요.'],
        ['group_code' => 'blog', 'code' => 'life', 'name' => '일상', 'memo' => '여행, 맛집, 카페, 쇼핑 등 일상 정리해요.'],
    ],

    'topics' => [
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '라라벨 기초', 'memo' => '라라벨 기초 정보 정리해요'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '라라벨 개발환경 구성', 'memo' => '실전에 필요한 라라벨 웹 환경 설치방법 정리해요'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '라라벨 데이터베이스', 'memo' => '실전에 필요한 라벨 ORM, 쿼리빌더, 디비조작 방법 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'AI MCP', 'memo' => 'AI MCP 개발'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '바이브코딩', 'memo' => 'AI 바이브코딩 정보 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'PHP', 'memo' => 'PHP 개발'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'PHP', 'memo' => 'SEO 개발'],
        ['group_code' => 'blog', 'category_code' => 'tour', 'name' => '국내여행', 'memo' => '국내 여행 후기 정리'],
        ['group_code' => 'blog', 'category_code' => 'food', 'name' => '국내맛집', 'memo' => '국내 맛집 후기 정리'],
        ['group_code' => 'blog', 'category_code' => 'cafe', 'name' => '국내카페', 'memo' => '국내 카페 후기 정리'],
        ['group_code' => 'blog', 'category_code' => 'economy', 'name' => '경제상식', 'memo' => '초딩도 쉽게 이해하는 경제상식'],
        ['group_code' => 'blog', 'category_code' => 'shopping', 'name' => 'IT/가전', 'memo' => 'IT 및 가전제품 구매 후기'],
        ['group_code' => 'blog', 'category_code' => 'shopping', 'name' => '일상', 'memo' => '일상속 쇼핑 구매 후기'],
        ['group_code' => 'blog', 'category_code' => 'real-estate', 'name' => '국내부동산', 'memo' => '국내 부동산 정보 정리'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '리액트', 'memo' => '프론트엔드 리액트에 대해 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'Vue', 'memo' => '프론트엔드 Vue 대해 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'AI', 'memo' => 'AI정보'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '파이썬', 'memo' => '파이썬에 대해 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => 'FastAPI', 'memo' => '파이썬 프레임워크 FastAPI에 대해서 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '플러터', 'memo' => '플러터 앱 프레임워크에 대해서 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '장인정신', 'memo' => '개발자 장인정신에 대해 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '커리어', 'memo' => '개발자 커리어 정리해요.'],
        ['group_code' => 'blog', 'category_code' => 'develop', 'name' => '스프리부트', 'memo' => '자바 스프링부트 대해서 정리해요.'],
    ],
];

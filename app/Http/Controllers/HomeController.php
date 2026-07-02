<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService
    ) {}

    public function index(): View
    {
        $projects = [
            [
                'title' => '재고관리 PDA',
                'icon' => 'bi-box-seam',
                'summary' => '발주입고, 보관입고, 출고, 사용, 재고이동, 재고실사를 지원하는 PDA 스타일 재고관리 서비스입니다.',
                'description' => '재고관리 PDA는 복잡한 ERP나 MES 화면을 그대로 따라 하기보다, 실제 사용자가 모바일이나 PDA 화면에서 빠르게 물품을 처리할 수 있도록 단순화한 재고관리 서비스입니다. 발주로 들어온 물품을 입고 처리하고, 별도 보관 물품을 등록하며, 필요한 물품을 출고하거나 사용 처리할 수 있습니다. 또한 보관 위치가 바뀌는 경우 재고이동으로 기록하고, 실제 보유 수량과 시스템 수량이 맞는지 재고실사를 통해 확인할 수 있도록 구성했습니다. 가정용 생활용품, 사무실 비품, 공구, 전자기기, 소모품처럼 작지만 자주 잊어버리는 물품을 관리하는 데 사용할 수 있고, 소규모 사업장이나 개인 창고 관리에도 응용할 수 있는 구조입니다.',
                'tags' => ['발주입고', '보관입고', '출고', '사용', '재고이동', '재고실사'],
                'buttonText' => '서비스 체험하기',
                'href' => 'https://pda.th-study.com',
            ],
            [
                'title' => '티에이치스터디 MCP',
                'icon' => 'bi-robot',
                'summary' => 'ChatGPT가 티에이치스터디의 블로그, 카테고리, 주제, 노트 데이터를 조회할 수 있도록 연결하는 MCP 서버입니다.',
                'description' => '티에이치스터디 MCP는 ChatGPT와 티에이치스터디의 콘텐츠 데이터를 연결하기 위해 만든 실험용 서버입니다. 티에이치스터디는 블로그 글, 개발 노트, 카테고리, 주제, 태그 같은 콘텐츠 데이터를 관리하는 사이트입니다. MCP 서버는 이 데이터를 외부 AI 도구가 정해진 API 규칙에 따라 조회할 수 있도록 연결하는 역할을 합니다. 예를 들어 ChatGPT가 티에이치스터디에 저장된 블로그 그룹, 카테고리, 주제, 노트, 해시태그 정보를 도구 호출 방식으로 조회할 수 있게 만드는 구조입니다. 단순히 웹 페이지를 보여주는 블로그에서 끝나는 것이 아니라, 사이트 내부 콘텐츠를 AI가 검색하고 활용할 수 있는 데이터 기반 블로그로 확장하는 실험입니다. 현재 MCP 도구는 노트 그룹 조회, 노트 카테고리 조회, 노트 주제 조회, 노트 목록 조회, 노트 해시태그 조회 같은 기능을 중심으로 구성되어 있습니다. 이를 통해 티에이치스터디의 콘텐츠를 사람이 보는 화면뿐 아니라 AI가 이해하고 검색할 수 있는 형태로 확장하는 것을 목표로 합니다.',
                'tags' => ['ChatGPT 연동', 'MCP', '블로그 데이터', '노트 검색', '카테고리 조회', '태그 조회', 'API'],
                'buttonText' => 'ChatGPT 열기',
                'href' => 'https://chatgpt.com/apps/app/asdk_app_6a1c1dd908a4819184b43b373c0ccbb8',
            ],
        ];

        return view('home', [
            'postType' => 'inquiries',
            'projects' => $projects,
            'latestBlogs' => $this->homeService->getLatestBlogs(),
        ]);
    }
}

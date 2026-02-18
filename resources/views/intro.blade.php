@extends('layouts.app')

@section('title', '소개')

@section('style')
@endsection

@push('styles')
    <link href="{{ asset('css/intro/intro.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="col-12 p-0 intro-page">
  <div class="wrap" id="wrap">
    <nav class="dotnav" id="dotnav" aria-label="section navigation"></nav>

    <!-- SVG 아이콘(내장) -->
    <template id="icon-ai">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M7 11a5 5 0 0 1 10 0v2a4 4 0 0 1-4 4h-2l-3 2v-2a4 4 0 0 1-1-2.5V11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M9.2 12.6h.01M12 12.6h.01M14.8 12.6h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
      </svg>
    </template>
    <template id="icon-globe">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="1.8"/>
        <path d="M2 12h20" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 2c3 3 4.5 7 4.5 10s-1.5 7-4.5 10c-3-3-4.5-7-4.5-10S9 5 12 2Z" stroke="currentColor" stroke-width="1.8"/>
      </svg>
    </template>
    <template id="icon-mail">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M4 7.5h16v9H4v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="m4.6 8.2 7.4 5.6 7.4-5.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>

    <!-- 0: HERO -->
    <section class="section hero active" data-label="메인">
      <div class="bg" aria-hidden="true" id="heroBg"></div>

      <div class="inner">
        <div class="kicker reveal delay-1">{{ config('app.name')}} · 함께 성장하는 기록과 실전의 공간</div>

        <h1 class="title reveal delay-2">
          <span id="typing"></span><span class="typing-caret" aria-hidden="true"></span>
        </h1>

        <p class="subtitle reveal delay-3">
          성장하는 개발자에서 장인정신을 지닌 리더로 나아가고자 합니다.
          {{ config('app.name') }}는 나만의 블로그 서비스를 기반으로 지식을 정리하고 공유하며,
          콘텐츠와 광고를 통해 오래 가는 수익 구조를 조금씩 만들어가는 공간입니다.
          누군가의 시행착오를 줄여주는 기록을 남기는 과정 자체가, 자연스럽게 사회에 보탬이 된다고 믿습니다.
        </p>

        <div class="cta reveal delay-3">
          <a class="btn primary" href="#" data-goto="1">사이트 소개</a>
          <a class="btn" href="#" data-goto="2">약력과 경험</a>
          <a class="btn" href="#" data-goto="7">문의하기</a>
        </div>

        <div class="note reveal delay-3">
          스크롤 또는 우측 점 메뉴로 섹션을 이동할 수 있습니다.
        </div>
      </div>
    </section>

    <!-- 1: 사이트 소개 -->
    <section class="section" data-label="소개">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.18" alt="intro image"
               src="{{ asset('images/intro/001.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">사이트 소개</h2>

          <p class="p reveal delay-2">
            블로그에 쌓아온 기록(여행, 맛집, 카페, 개발, 대중교통, 신기술, 미래, 경제)을
            나만의 블로그 서비스로 옮겨 더 잘 정리하고, 더 잘 검색되게 만들고자 합니다.
            흩어진 정보가 한 곳에 모이면, 기록은 자산이 되고 경험은 노하우가 됩니다.
          </p>

          <p class="p reveal delay-3">
            {{ config('app.name') }}는 혼자만의 기록으로 끝나지 않도록,
            처음 보는 사람도 따라올 수 있는 설명과 실전 기준을 함께 남기려 합니다.
            쉽게 설명된 기록이 하나씩 늘어나는 과정이 곧 사회에 도움이 된다고 생각합니다.
          </p>

          <div class="tagrow reveal delay-3">
            <span class="tag">티스토리</span>
            <span class="tag">네이버블로그</span>
            <span class="tag">나만의 블로그</span>
            <span class="tag">함께 성장</span>
            <span class="tag">콘텐츠 기반 수익화</span>
            <span class="tag">검색 가능한 기록</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 2: 약력 + 경험 -->
    <section class="section" data-label="약력">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.20" alt="career image"
               src="{{ asset('images/intro/002.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">약력</h2>

          <p class="p reveal delay-2">
            10년 경력의 웹 개발자로서 MES·ERP, 에너지 플랫폼(BEMS/FEMS/REMS/HEMS),
            마케팅 웹서비스와 랜딩페이지, 그리고 SaaS 기획과 운영까지 폭넓게 경험해왔습니다.
            서비스가 운영 가능한 형태가 되도록 구조화하고 문서화하는 과정에 강점이 있습니다.
          </p>

          <p class="p reveal delay-3">
            특히 PHP는 빠르고 경제적인 개발이 가능하지만 국내에서는 자료와 교육이 부족한 편입니다.
            {{ config('app.name') }}에서는 PHP 장인 개발자의 관점으로 구버전과 레거시, 프레임워크 운영 노하우를 정리해 공유하려 합니다.
            또한 AI를 활용해 학습과 정리를 더 쉽게 만들고, 개발자 양성에 도움이 되는 기준을 만들어가겠습니다.
          </p>

          <div class="tagrow reveal delay-3">
            <span class="tag"><i class="devicon-php-plain"></i>PHP</span>
            <span class="tag"><i class="devicon-laravel-original"></i>Laravel</span>
            <span class="tag"><i class="devicon-codeigniter-plain"></i>CodeIgniter</span>
            <span class="tag"><i class="devicon-spring-plain"></i>Spring Boot</span>
            <span class="tag"><i class="devicon-python-plain"></i>Python</span>
            <span class="tag"><i class="devicon-nodejs-plain"></i>Node.js</span>
            <span class="tag"><i class="devicon-amazonwebservices-plain-wordmark"></i>AWS</span>
            <span class="tag" id="aiTag"></span>
          </div>
        </div>
      </div>
    </section>

    <!-- 3: 사회공헌 -->
    <section class="section" data-label="사회공헌">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.20" alt="share image"
               src="{{ asset('images/intro/003.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">함께 쌓아가는 경험, 작은 사회공헌</h2>

          <p class="p reveal delay-2">
            거창한 말보다, 실무에서 진짜 도움이 되는 내용을 차근차근 쌓아가고자 합니다.
            {{ config('app.name') }}는 “처음 보는 사람도 따라할 수 있는 글”을 기준으로,
            막히는 지점을 줄이고 시행착오를 덜어주는 노하우를 만들고자 합니다.
          </p>

          <ul class="list reveal delay-3">
            <li><strong>초보 친화 문서</strong>를 꾸준히 공개합니다. 용어부터 흐름까지 쉽게 풀어서 정리합니다.</li>
            <li><strong>레거시 개선 노하우</strong>를 공유합니다. PHP 구버전/현업 코드 정리 방식까지 담아냅니다.</li>
            <li><strong>실전 체크리스트</strong>를 제공합니다. 운영 관점에서 안전하게 개발하는 기준을 함께 정리합니다.</li>
          </ul>

          <div class="tagrow reveal delay-3">
            <span class="tag">지식 나눔</span>
            <span class="tag">개발자 양성</span>
            <span class="tag">실전 문서</span>
            <span class="tag">함께 성장</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 4: PHP -->
    <section class="section" data-label="PHP">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.22" alt="php image"
               src="{{ asset('images/intro/004.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">PHP 장인의 정리 노하우를 공유합니다</h2>

          <p class="p reveal delay-2">
            레거시는 버리는 것이 아니라 다루는 방법을 배우는 것이라고 생각합니다.
            구버전 PHP와 오래된 코드에서도 문제를 찾고, 개선하고, 운영 가능한 형태로 만드는 과정은
            실무에서 자주 만나게 되는 현실입니다.
          </p>

          <p class="p reveal delay-3">
            {{ config('app.name') }}에서는 PHP 레거시, Laravel, CodeIgniter를 중심으로
            왜 이렇게 구성하는지까지 담아 차근차근 정리해 나가겠습니다.
          </p>

          <div class="tagrow reveal delay-3">
            <span class="tag">구버전 유지보수</span>
            <span class="tag">레거시 개선</span>
            <span class="tag">실전 코드 정리</span>
            <span class="tag">운영 기준</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 5: AI -->
    <section class="section" data-label="AI">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.24" alt="ai image"
               src="{{ asset('images/intro/005.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">기술이 많아질수록, 정리 방식이 더 중요해집니다</h2>

          <p class="p reveal delay-2">
            Spring Boot, Node.js, Python처럼 배울 것이 많아지는 시대에는
            단순한 기능 나열보다 어떻게 접근하는지가 더 큰 차이를 만듭니다.
            {{ config('app.name') }}는 AI를 활용하되, 사람의 감각이 담긴 정리 방식으로 함께 성장할 수 있도록 돕고자 합니다.
          </p>

          <p class="p reveal delay-3">
            빠르게 만들고 끝내는 것이 아니라, 오래 운영할 수 있는 기준을 세우고
            누구나 따라올 수 있는 설명과 레시피로 정리해 나가겠습니다.
          </p>

          <div class="tagrow reveal delay-3">
            <span class="tag">AI 활용</span>
            <span class="tag">장인의 레시피</span>
            <span class="tag">함께 성장</span>
            <span class="tag">개발자 양성</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 6: 수익 -->
    <section class="section" data-label="수익">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.18" alt="monetize image"
               src="{{ asset('images/intro/006.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">기록을 모으면, 수익의 기반이 만들어집니다</h2>

          <p class="p reveal delay-2">
            블로그를 운영하며 느낀 한계는 플랫폼 제약이었습니다.
            나만의 서비스로 통합하면 기록을 더 자유롭게 관리할 수 있고,
            검색 품질과 콘텐츠 구조를 개선하면서 광고와 콘텐츠 기반 수익 구조도 더 안정적으로 설계할 수 있습니다.
          </p>

          <p class="p reveal delay-3">
            {{ config('app.name') }}는 개발뿐 아니라 여행, 맛집, 카페, 대중교통, 신기술, 미래, 경제 같은
            일상의 기록까지 함께 모아 검색 가능한 데이터로 정리해 나가는 방향을 지향합니다.
          </p>

          <div class="tagrow reveal delay-3">
            <span class="tag">콘텐츠 구조화</span>
            <span class="tag">검색 최적화</span>
            <span class="tag">광고 수익화</span>
            <span class="tag">장기 운영</span>
            <span class="tag">마케팅서비스</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 7: 문의하기 -->
    <section class="section" data-label="문의">
      <div class="split">
        <div class="imgbox reveal delay-1">
          <img class="parallax" data-speed="0.18" alt="contact image"
               src="{{ asset('images/intro/007.avif') }}">
        </div>

        <div class="content">
          <h2 class="h reveal delay-2">문의하기</h2>

          <p class="p reveal delay-2">
            아래 링크에서 글과 코드를 확인하실 수 있습니다.
            궁금한 점이나 제안이 있으시면 편하게 연락 주셔도 좋습니다.
          </p>

          <div class="actionrow reveal delay-3">
            <a class="actionbtn" href="https://github.com/th-study-group/th-study" target="_blank" rel="noopener">
              <i class="devicon-github-original"></i> GitHub
            </a>

            <a class="actionbtn" href="https://th-study.tistory.com" target="_blank" rel="noopener" id="tistoryBtn">
              <span class="iconSlot"></span> 티스토리
            </a>

            <a class="actionbtn" href="mailto:admin@th-study.com" id="mailBtn">
              <span class="iconSlot"></span> Contact
            </a>

            <a class="actionbtn primary" href="{{ route('login') }}">
              <i class="devicon-laravel-original"></i> 로그인
            </a>
          </div>

          <p class="note reveal delay-3">
            이미지 출처는 Unsplash 무료 라이선스를 사용했습니다. 아이콘은 Devicon(MIT)과 내장 SVG 아이콘을 사용했습니다.
          </p>
        </div>
      </div>
    </section>
  </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/intro/intro.js') }}" defer></script> 
@endpush

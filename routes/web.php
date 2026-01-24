<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GuestPostController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // 라라벨 인덱스
    return view('home', [
        'postType' => 'inquiry'
    ]);
})->name('home')->defaults('hideSide', true);

// sidebar 없는 정적 페이지들
Route::group([], function() {
    Route::view('/privacy-policy', 'privacy')
        ->name('privacy')
        ->defaults('hideSide', true);
    Route::view('/terms-of-service', 'terms')
        ->name('terms')
        ->defaults('hideSide', true);
});

// 회원가입 로그인 
Route::get("/register", [RegisterController::class, 'create'])->name('register.form'); // 회원가입 폼
Route::post("/register", [RegisterController::class, 'register'])->name('register.submit'); // 회원가입 처리
Route::get("/login", [LoginController::class, 'login'])->name('login'); // 로그인 폼
Route::post("/login", [LoginController::class, 'authenticate'])->name('authenticate'); // 로그인 처리 
Route::get("/logout", [LoginController::class, 'logout'])->name('logout'); // 로그아웃 처리

// 회원 가입 인증 링크
Route::get('/email/verify', [
    EmailVerifyController::class,'verify'
    ])->name('email.verify');

// 회원가입 인증 메일 재발송 (로그인상태)
Route::post('/email/resend', [EmailVerifyController::class,'resend'])
    ->middleware('auth')
    ->name('email.resend');

// 관리자 or 시더 생성 로그인 계정에 대한 비밀번호 재설정 
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordChangeController::class,'index'])
        ->name('password.change.form');
        
    Route::post('/password/change', [PasswordChangeController::class,'requirePasswordReset'])
        ->name('password.change.update');
});

// 계정찾기 화면
Route::get('/find-account', [ForgotPasswordController::class,'index'])
    ->name('password.find.account');

// 비밀번호 초기화 메일 발송 (쓰로틀: 1분 5회)
Route::post('/find-password', [ForgotPasswordController::class,'requestAccountEmail'])
    ->middleware('throttle:5,1')
    ->name('password.request.account_email');

// 비밀번호 재설정 메일 링크 클릭
Route::get('/reset-password/{token}', [ResetPasswordController::class,'index'])
   ->name('password.reset.mail');

// 비밀번호 재설정처리
Route::post('/reset-password', [ResetPasswordController::class,'store'])
    ->name('password.reset.mail_update');

// 로그인 후 보여야 하는 메뉴
Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard'); // 마이페이지 (로그인 후 진입페이지)   
       
    Route::prefix("mypage")->name("mypage.")->group(function() {
        Route::view('/inquiries', 'inquiries.index'); // 문의내역 (글쓰기, 상세, 목록 나올 경우 컨트롤러 생성)
    });
});

// 회원 라우팅
// 관리자, 사용자가 모두 접속해야 해서 컨트롤러에서 관리자, 로그인 여부 체크 
// 관리는 별도 라우팅 이용
Route::middleware(['auth', 'email.verified'])->prefix('users')->name('users.')->group(function () {
    Route::get("/", [UserController::class, 'index'])->name('index'); // 회원목록
    Route::get("account", [UserController::class, 'show'])->name('account.show');
    Route::get("/edit", [UserController::class, 'edit'])->name("account.edit"); 
    Route::put("/update", [UserController::class, 'update'])->name('account.update');
    Route::get("/withdrawal", [UserController::class, 'withdrawal'])->name('account.withdrawal');
    Route::patch("/destroy", [UserController::class, 'destroy'])->name('account.destroy');
});

// 게시글 라우팅
Route::prefix("posts/{post_type}")
    ->name("posts.")
    ->whereIn('post_type', array_keys(config('board.post_type')))
    ->group(function() {
        Route::get("/", [PostController::class, 'index'])->name('index');
        Route::get("/create", [PostController::class, 'create'])->name('create');
        Route::get("/{idx}", [PostController::class, 'show'])->whereNumber('idx')->name('show');
        Route::post("/", [PostController::class, 'store'])->name('store');
        Route::get("/{idx}/edit", [PostController::class, 'edit'])->whereNumber('idx')->name("edit");
        Route::put("/{idx}", [PostController::class, 'update'])->whereNumber('idx')->name('update');
        Route::patch("/{idx}/soft-delete", [PostController::class, 'destroy'])->whereNumber('idx')->name('destroy');
    });

// 댓글 라우팅
Route::prefix("comments")->name("comments.")->group(function() {
    Route::get("/", [CommentController::class, 'index'])->name('index');
    Route::get("/create", [CommentController::class, 'create'])->name('create');
    Route::get("/{idx}", [CommentController::class, 'show'])->whereNumber('idx')->name('show');
    Route::post("/", [CommentController::class, 'store'])->name('store');
    Route::get("/{idx}/edit", [CommentController::class, 'edit'])->whereNumber('idx')->name("edit");
    Route::put("/{idx}", [CommentController::class, 'update'])->whereNumber('idx')->name('update');
    Route::patch("/{idx}/soft-delete", [CommentController::class, 'destroy'])->whereNumber('idx')->name('destroy');
});

// 미인증 게시글 라우팅
Route::prefix("guest-posts/{post_type}")
    ->name("guest_posts.")
    ->whereIn('post_type', array_keys(config('board.post_type')))
    ->group(function() {
        Route::post("/", [GuestPostController::class, 'store'])->name('store');
    });

// 사진 라우팅
Route::prefix('photo')->name('photo.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('photo.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 영상 라우팅
Route::prefix('video')->name('video.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('video.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 정보 라우팅
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('blog.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 장소 라우팅
Route::prefix('map')->name('map.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('map.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 문서 라우팅
Route::prefix('document')->name('document.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('document.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

Route::post('/send', [ChatController::class, 'send']);
Route::view('/chat', 'chat');

// TestController
// 라라벨 테스트 라우팅
Route::prefix("tests")->name("test.")->group(function() {
    Route::get("mail_queue", [TestController::class, 'mailQueue'])->name('mail_queue');
});

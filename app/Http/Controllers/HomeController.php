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
        return view('home', [
            'postType' => 'inquiries',
            'latestBlogs' => $this->homeService->getLatestBlogs(),
        ]);
    }
}

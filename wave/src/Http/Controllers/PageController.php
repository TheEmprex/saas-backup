<?php

declare(strict_types=1);

namespace Wave\Http\Controllers;

use App\Http\Controllers\Controller;
use Wave\Page;

class PageController extends Controller
{
    public function page($slug)
    {
        $page = Page::query()->where('slug', '=', $slug)->firstOrFail();

        $seo = [
            'seo_title' => $page->title,
            'seo_description' => $page->meta_description,
        ];

        return view('theme::page', ['page' => $page, 'seo' => $seo]);
    }
}

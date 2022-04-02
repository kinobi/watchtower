<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $urls = Url::with(['annotation', 'shortUrl'])->shared()->get();

        return Inertia::render('Home', compact('urls'));
    }
}

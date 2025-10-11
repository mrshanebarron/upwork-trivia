<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Inertia\Inertia;

class PageController extends Controller
{
    public function about()
    {
        return Inertia::render('About', [
            'content' => Setting::getValue('about_content', '<h2>Welcome to Poop Bag Trivia!</h2><p>Edit this content from the admin panel.</p>'),
        ]);
    }

    public function terms()
    {
        return Inertia::render('Legal/Terms', [
            'termlyCode' => Setting::getValue('termly_terms_code'),
        ]);
    }

    public function privacy()
    {
        return Inertia::render('Legal/Privacy', [
            'termlyCode' => Setting::getValue('termly_privacy_code'),
        ]);
    }
}

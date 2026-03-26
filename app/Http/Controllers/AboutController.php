<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Inertia\Inertia;

class AboutController extends Controller
{

    public function index()
    {
        return Inertia::render('public/about/Index');
    }
}

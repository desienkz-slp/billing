<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OltController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Maintenance/Index', [
            'module' => 'OLT'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcsController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Maintenance/Index', [
            'module' => 'ACS System'
        ]);
    }
}

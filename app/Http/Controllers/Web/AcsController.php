<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcsController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard')->with('info', 'Modul ACS sedang dalam tahap pengembangan.');
    }
}

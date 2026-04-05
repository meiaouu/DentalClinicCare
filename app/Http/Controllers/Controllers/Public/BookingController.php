<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('public.booking');
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $clinic = ClinicSetting::first();

        $services = [
            [
                'name' => 'Dental Cleaning',
                'description' => 'Keep your teeth and gums healthy with regular cleaning.',
            ],
            [
                'name' => 'Tooth Extraction',
                'description' => 'Safe and careful tooth removal when needed.',
            ],
            [
                'name' => 'Braces',
                'description' => 'Orthodontic solutions for alignment and smile improvement.',
            ],
            [
                'name' => 'Tooth Filling',
                'description' => 'Restore damaged teeth and prevent further decay.',
            ],
            [
                'name' => 'Teeth Whitening',
                'description' => 'Brighten your smile with whitening treatment.',
            ],
            [
                'name' => 'Consultation',
                'description' => 'Professional dental checkup and treatment advice.',
            ],
        ];

        return view('public.home', compact('clinic', 'services'));
    }
}

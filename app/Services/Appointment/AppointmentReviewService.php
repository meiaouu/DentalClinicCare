<?php

namespace App\Services\Appointment;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AppointmentReviewService
{
    public function store(array $validated): string
    {
        $token = Str::uuid()->toString();

        Session::put("booking_review.{$token}", $validated);

        return $token;
    }

    public function get(string $token): ?array
    {
        return Session::get("booking_review.{$token}");
    }

    public function forget(string $token): void
    {
        Session::forget("booking_review.{$token}");
    }
}

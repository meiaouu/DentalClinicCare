<?php

namespace App\Services\Appointment;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class AppointmentReviewService
{
    protected string $sessionKey = 'booking_review';

    public function store(array $validated): string
    {
        $token = Str::uuid()->toString();

        Session::put($this->buildTokenKey($token), [
            'data' => $validated,
            'created_at' => now()->toDateTimeString(),
            'expires_at' => now()->addMinutes(30)->toDateTimeString(),
        ]);

        Session::put($this->latestTokenKey(), $token);

        return $token;
    }

    public function update(string $token, array $validated): void
    {
        if (!$this->exists($token)) {
            throw new RuntimeException('Booking review session not found.');
        }

        Session::put($this->buildTokenKey($token), [
            'data' => $validated,
            'created_at' => now()->toDateTimeString(),
            'expires_at' => now()->addMinutes(30)->toDateTimeString(),
        ]);

        Session::put($this->latestTokenKey(), $token);
    }

    public function get(string $token): ?array
    {
        $payload = Session::get($this->buildTokenKey($token));

        if (!$payload || !is_array($payload)) {
            return null;
        }

        if ($this->isExpiredPayload($payload)) {
            $this->forget($token);
            return null;
        }

        return $payload['data'] ?? null;
    }

    public function getOrFail(string $token): array
    {
        $data = $this->get($token);

        if (!$data) {
            throw new RuntimeException('Booking review session expired or not found.');
        }

        return $data;
    }

    public function exists(string $token): bool
    {
        return $this->get($token) !== null;
    }

    public function forget(string $token): void
    {
        Session::forget($this->buildTokenKey($token));

        if (Session::get($this->latestTokenKey()) === $token) {
            Session::forget($this->latestTokenKey());
        }
    }

    public function forgetLatest(): void
    {
        $token = $this->latestToken();

        if ($token) {
            $this->forget($token);
        }
    }

    public function latestToken(): ?string
    {
        return Session::get($this->latestTokenKey());
    }

    public function latestData(): ?array
    {
        $token = $this->latestToken();

        if (!$token) {
            return null;
        }

        return $this->get($token);
    }

    protected function buildTokenKey(string $token): string
    {
        return "{$this->sessionKey}.{$token}";
    }

    protected function latestTokenKey(): string
    {
        return "{$this->sessionKey}_latest_token";
    }

    protected function isExpiredPayload(array $payload): bool
    {
        if (empty($payload['expires_at'])) {
            return false;
        }

        return Carbon::parse($payload['expires_at'])->isPast();
    }
}

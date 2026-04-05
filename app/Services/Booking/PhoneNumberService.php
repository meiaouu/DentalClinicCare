<?php

namespace App\Services\Booking;

class PhoneNumberService
{
    public function normalizePhilippineMobile(string $number): string
    {
        $clean = preg_replace('/\s+|-+/', '', trim($number));

        if ($clean === null) {
            throw new \InvalidArgumentException('Invalid contact number.');
        }

        if (str_starts_with($clean, '+')) {
            $clean = substr($clean, 1);
        }

        if (!preg_match('/^\d+$/', $clean)) {
            throw new \InvalidArgumentException(
                'Contact number must contain digits only. Accepted formats: 09XXXXXXXXX, 639XXXXXXXXX, +639XXXXXXXXX.'
            );
        }

        if (str_starts_with($clean, '09')) {
            if (strlen($clean) !== 11) {
                throw new \InvalidArgumentException('Philippine mobile number using 09 format must be exactly 11 digits.');
            }

            return '639' . substr($clean, 2);
        }

        if (str_starts_with($clean, '639')) {
            if (strlen($clean) !== 12) {
                throw new \InvalidArgumentException('Philippine mobile number using 639 format must be exactly 12 digits.');
            }

            return $clean;
        }

        throw new \InvalidArgumentException(
            'Invalid Philippine mobile number format. Accepted formats: 09XXXXXXXXX, 639XXXXXXXXX, +639XXXXXXXXX.'
        );
    }
}

<?php

namespace App\Services\Ai;

class ClinicChatbotService
{
    public function generateReplyPayload(string $message, bool $isGuest = false): array
    {
        $normalized = $this->normalize($message);
        $intent = $this->detectIntent($normalized);

        $reply = match ($intent) {
            'greeting' => $this->replyGreeting(),
            'hours' => $this->replyClinicHours(),
            'location_contact' => $this->replyLocationContact(),
            'booking' => $this->replyBooking(),
            'reschedule' => $this->replyReschedule(),
            'cancel' => $this->replyCancel(),
            'follow_up' => $this->replyFollowUp(),
            'services' => $this->replyServices(),
            'price' => $this->replyPrice(),
            'human' => $this->replyHumanHandoff(),
            'urgent' => $this->replyUrgentConcern(),
            default => $this->replyDefault($isGuest),
        };

        $needsHandoff = in_array($intent, [
            'urgent',
            'human',
            'unknown',
            'reschedule',
            'cancel',
            'price',
        ], true);

        return [
            'reply' => $reply,
            'intent' => $intent,
            'needs_handoff' => $needsHandoff,
        ];
    }

    public function generateReply(string $message, bool $isGuest = false): string
    {
        return $this->generateReplyPayload($message, $isGuest)['reply'];
    }

    protected function detectIntent(string $message): string
    {
        if ($message === '') {
            return 'unknown';
        }

        if ($this->containsAny($message, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'])) {
            return 'greeting';
        }

        if ($this->containsAny($message, ['hour', 'hours', 'open', 'close', 'oras'])) {
            return 'hours';
        }

        if ($this->containsAny($message, ['location', 'address', 'contact', 'phone', 'email', 'where'])) {
            return 'location_contact';
        }

        if ($this->containsAny($message, ['book', 'booking', 'appointment', 'schedule'])) {
            return 'booking';
        }

        if ($this->containsAny($message, ['reschedule', 'move appointment', 'change time', 'change date'])) {
            return 'reschedule';
        }

        if ($this->containsAny($message, ['cancel', 'remove appointment'])) {
            return 'cancel';
        }

        if ($this->containsAny($message, ['follow up', 'follow-up', 'next visit'])) {
            return 'follow_up';
        }

        if ($this->containsAny($message, ['service', 'cleaning', 'brace', 'braces', 'extract', 'extraction', 'filling', 'checkup', 'consultation'])) {
            return 'services';
        }

        if ($this->containsAny($message, ['price', 'cost', 'fee', 'payment', 'how much', 'bayad'])) {
            return 'price';
        }

        if ($this->containsAny($message, ['staff', 'human', 'real person', 'agent', 'assistant'])) {
            return 'human';
        }

        if ($this->containsAny($message, ['pain', 'swelling', 'bleeding', 'infection', 'emergency', 'urgent', 'sobrang sakit'])) {
            return 'urgent';
        }

        return 'unknown';
    }

    protected function replyGreeting(): string
    {
        return 'Hello. Welcome to our clinic chat support. How can we help you today?';
    }

    protected function replyClinicHours(): string
    {
        return 'Clinic hours depend on the current clinic schedule. You may check the booking calendar, and clinic staff can confirm the exact available time for you.';
    }

    protected function replyLocationContact(): string
    {
        return 'You can find the clinic address and contact details on the website contact section. If needed, clinic staff can also confirm them for you.';
    }

    protected function replyBooking(): string
    {
        return 'To book an appointment, please select a service, preferred date, and time, then submit your request. Clinic staff will review and confirm your schedule.';
    }

    protected function replyReschedule(): string
    {
        return 'Please send your preferred new date and time. Clinic staff will review availability and assist you with rescheduling.';
    }

    protected function replyCancel(): string
    {
        return 'Please send your cancellation concern and clinic staff will review it. Confirmed appointments may require staff approval before changes are applied.';
    }

    protected function replyFollowUp(): string
    {
        return 'Follow-up visits are usually based on the dentist’s recommendation after treatment. Clinic staff can help confirm the proper follow-up schedule.';
    }

    protected function replyServices(): string
    {
        return 'We can help with inquiries about cleaning, braces, extraction, fillings, and checkups. Please tell us which service you are asking about.';
    }

    protected function replyPrice(): string
    {
        return 'Service fees can vary depending on the actual treatment and dentist evaluation. Clinic staff can provide proper fee guidance for your concern.';
    }

    protected function replyHumanHandoff(): string
    {
        return 'Thank you for your message. A clinic staff member will assist you as soon as possible.';
    }

    protected function replyUrgentConcern(): string
    {
        return 'For pain, swelling, bleeding, infection, or urgent dental concerns, please contact the clinic directly or visit the clinic so staff or the dentist can properly assist you.';
    }

    protected function replyDefault(bool $isGuest): string
    {
        return $isGuest
            ? 'Thank you for your message. I can help with booking steps, clinic information, and follow-up guidance. If your concern needs staff review, a clinic staff member will assist you.'
            : 'Thank you for your message. I can help with clinic information, booking guidance, and follow-up questions. If needed, clinic staff will assist you.';
    }

    protected function normalize(string $message): string
    {
        $message = mb_strtolower(trim($message));

        return preg_replace('/\s+/', ' ', $message) ?: $message;
    }

    protected function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

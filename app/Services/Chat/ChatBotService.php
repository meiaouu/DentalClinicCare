<?php

namespace App\Services\Chat;

use App\Models\ClinicSetting;
use App\Models\Conversation;

class ChatbotService
{
    public function shouldBotReply(Conversation $conversation): bool
    {
        if ($conversation->is_guest) {
            return true;
        }

        if ($conversation->handled_by) {
            return false;
        }

        // Simple capstone-friendly fallback:
        // if no assigned staff yet, bot may answer first.
        return true;
    }

    public function replyToConversation(Conversation $conversation, string $message): ?string
    {
        $text = mb_strtolower(trim($message));
        $clinic = ClinicSetting::query()->first();

        if ($text === '') {
            return null;
        }

        if ($this->containsAny($text, ['hello', 'hi', 'good morning', 'good afternoon'])) {
            return "Hello. I am the clinic support bot. I can help with clinic hours, booking guidance, follow-up reminders, and general service inquiries. For medical concerns, please wait for clinic staff.";
        }

        if ($this->containsAny($text, ['hours', 'open', 'close', 'clinic hours', 'what time'])) {
            $open = $clinic?->open_time ?? '08:00';
            $close = $clinic?->close_time ?? '17:00';

            return "Our clinic hours are {$open} to {$close}. If you want to request an appointment, please use the booking form on the website.";
        }

        if ($this->containsAny($text, ['book', 'booking', 'appointment', 'schedule'])) {
            return "To request an appointment, go to the booking page, choose your service, select your preferred date and time, and submit your request. Clinic staff will review and confirm it.";
        }

        if ($this->containsAny($text, ['follow up', 'follow-up', 'next visit', 'return visit'])) {
            return "For follow-up visits, please wait for the dentist or clinic staff recommendation. If you already have a follow-up instruction, clinic staff can help schedule it.";
        }

        if ($this->containsAny($text, ['service', 'services', 'cleaning', 'tooth extraction', 'braces'])) {
            return "You can view the clinic services on the services section of the website. For procedure-specific preparation or pricing confirmation, please wait for clinic staff.";
        }

        if ($this->containsAny($text, ['contact', 'staff', 'call', 'phone', 'email'])) {
            return "You may contact the clinic through the contact details shown on the website. If clinic staff are available, they will continue this conversation.";
        }

        if ($this->containsAny($text, ['pain', 'swelling', 'infection', 'diagnosis', 'medicine', 'prescription'])) {
            return "I cannot give medical diagnosis or treatment advice. For pain, swelling, infection, or prescription concerns, please contact clinic staff directly or visit the clinic as soon as possible.";
        }

        return "I am the clinic support bot. I can help with clinic hours, booking guidance, follow-up scheduling guidance, and general clinic information. For medical concerns or personalized advice, please wait for clinic staff.";
    }

    protected function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

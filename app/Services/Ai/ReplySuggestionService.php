<?php

namespace App\Services\Ai;

use Throwable;

class ReplySuggestionService
{
    public function __construct(
        protected OpenAiService $openAiService
    ) {
    }

    public function suggest(string $conversationText): string
    {
        $instructions = <<<PROMPT
You are assisting dental clinic staff.

Write a short, professional, patient-friendly reply.

Rules:
- Do not diagnose.
- Do not prescribe medicine.
- Do not guarantee outcomes.
- If medical judgment is needed, recommend clinic consultation or staff follow-up.
- Keep the tone clear, polite, and practical.
- Output only the reply text.
PROMPT;

        try {
            return $this->openAiService->generateText($instructions, trim($conversationText));
        } catch (Throwable $e) {
            return $this->buildFallbackReply($conversationText, false);
        }
    }

    public function suggestFromConversation(
        string $latestMessage,
        array $conversationMessages,
        bool $isGuest = false
    ): string {
        $latestMessage = trim($latestMessage);

        if ($latestMessage === '') {
            return '';
        }

        $conversationText = collect($conversationMessages)
            ->map(function (array $message) {
                $senderType = strtoupper((string) ($message['sender_type'] ?? 'UNKNOWN'));
                $messageText = trim((string) ($message['message_text'] ?? ''));

                return $senderType . ': ' . $messageText;
            })
            ->filter()
            ->implode("\n");

        $userType = $isGuest ? 'guest' : 'patient';

        $instructions = <<<PROMPT
You are assisting dental clinic staff.

Your task is to draft a short and professional reply for a {$userType} in a dental clinic chat.

Rules:
- Base the reply mainly on the latest sender message.
- Use recent conversation context only when helpful.
- Be short, polite, clear, and practical.
- Sound like clinic staff, not like an AI assistant.
- Do not diagnose.
- Do not prescribe medicine.
- Do not recommend treatment plans as final decisions.
- Do not promise outcomes.
- If the message needs clinical judgment, tell the user that clinic staff or the dentist will assist further, or recommend consultation.
- If the concern is only about hours, booking, schedule, services, or follow-up process, answer simply and clearly.
- Output only the reply text.
PROMPT;

        $input = <<<TEXT
Latest sender message:
{$latestMessage}

Recent conversation:
{$conversationText}
TEXT;

        try {
            return trim($this->openAiService->generateText($instructions, $input));
        } catch (Throwable $e) {
            return $this->buildFallbackReply($latestMessage, $isGuest);
        }
    }

    protected function buildFallbackReply(string $message, bool $isGuest = false): string
    {
        $text = mb_strtolower(trim($message));

        if ($text === '') {
            return 'Thank you for your message. Our clinic staff will assist you shortly.';
        }

        if (str_contains($text, 'hour') || str_contains($text, 'open') || str_contains($text, 'close')) {
            return 'Thank you for your message. Our clinic staff will confirm the clinic hours with you shortly.';
        }

        if (
            str_contains($text, 'book') ||
            str_contains($text, 'appointment') ||
            str_contains($text, 'schedule') ||
            str_contains($text, 'reschedule')
        ) {
            return 'Thank you for your message. We can help you with your appointment schedule. Please wait for clinic staff to confirm the available date and time.';
        }

        if (
            str_contains($text, 'price') ||
            str_contains($text, 'cost') ||
            str_contains($text, 'fee') ||
            str_contains($text, 'payment')
        ) {
            return 'Thank you for your message. The clinic staff will help you regarding fees and payment details shortly.';
        }

        if (
            str_contains($text, 'pain') ||
            str_contains($text, 'swelling') ||
            str_contains($text, 'bleeding') ||
            str_contains($text, 'infection')
        ) {
            return 'Thank you for your message. For symptoms or urgent concerns, please contact the clinic directly or visit the clinic so the staff or dentist can properly assist you.';
        }

        if ($isGuest) {
            return 'Thank you for your message. Our clinic staff will review your concern and assist you shortly.';
        }

        return 'Thank you for your message. We have received your concern and clinic staff will assist you shortly.';
    }
}

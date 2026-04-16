<?php

namespace App\Services\Ai;

class ClinicChatbotService
{
    public function __construct(
        protected OpenAiService $openAiService
    ) {
    }

    public function reply(string $userMessage, array $clinicContext = []): string
    {
        $hours = $clinicContext['hours'] ?? '8:00 AM to 5:00 PM';
        $location = $clinicContext['location'] ?? 'Clinic location not set';
        $contact = $clinicContext['contact'] ?? 'Clinic contact not set';

        $instructions = <<<PROMPT
You are a dental clinic support assistant.

Rules:
- Only answer clinic support questions.
- Allowed topics: clinic hours, location, contact details, booking guidance, follow-up scheduling guidance, service overview, staff handoff.
- Do not diagnose.
- Do not prescribe medicine.
- Do not recommend treatment plans.
- If the user asks about pain, swelling, infection, urgent symptoms, or treatment decisions, tell them to contact clinic staff or visit the clinic.
- Keep replies short, polite, and practical.
- If helpful, use this clinic context:
  Hours: {$hours}
  Location: {$location}
  Contact: {$contact}
PROMPT;

        return $this->openAiService->generateText($instructions, $userMessage);
    }



    
}

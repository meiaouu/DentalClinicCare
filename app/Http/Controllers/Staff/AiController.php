<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\Ai\ClinicChatbotService;
use App\Services\Ai\ReplySuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiController extends Controller
{
    public function suggestReply(
        Request $request,
        Conversation $conversation,
        ReplySuggestionService $service,
        ClinicChatbotService $chatbotService
    ): JsonResponse {
        try {
            if (($conversation->conversation_status ?? null) === 'closed') {
                return response()->json([
                    'suggested_reply' => '',
                    'message' => 'Conversation is closed.',
                ]);
            }

            $latestSenderMessage = $conversation->messages()
                ->whereIn('sender_type', ['patient', 'guest'])
                ->latest('message_id')
                ->first();

            if (!$latestSenderMessage) {
                return response()->json([
                    'suggested_reply' => '',
                    'message' => 'No patient or guest message found.',
                ]);
            }

            $latestMessageText = trim((string) ($latestSenderMessage->message_text ?? ''));

            if ($latestMessageText === '') {
                return response()->json([
                    'suggested_reply' => '',
                    'message' => 'Latest sender message is empty.',
                ]);
            }

            $recentMessages = $conversation->messages()
                ->orderByDesc('message_id')
                ->limit(8)
                ->get()
                ->reverse()
                ->map(function ($message) {
                    return [
                        'sender_type' => $message->sender_type,
                        'message_text' => (string) ($message->message_text ?? ''),
                    ];
                })
                ->values()
                ->all();

            try {
                $suggestedReply = $service->suggestFromConversation(
                    latestMessage: $latestMessageText,
                    conversationMessages: $recentMessages,
                    isGuest: (bool) ($conversation->is_guest ?? false)
                );
            } catch (Throwable $inner) {
                Log::warning('AI suggest reply fallback used', [
                    'conversation_id' => $conversation->conversation_id,
                    'error' => $inner->getMessage(),
                ]);

                $payload = $chatbotService->generateReplyPayload(
                    $latestMessageText,
                    (bool) ($conversation->is_guest ?? false)
                );

                $suggestedReply = (string) ($payload['reply'] ?? 'Thank you for your message. Our clinic staff will assist you shortly.');
            }

            return response()->json([
                'suggested_reply' => (string) $suggestedReply,
            ]);
        } catch (Throwable $e) {
            Log::error('AI suggest reply failed', [
                'conversation_id' => $conversation->conversation_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'suggested_reply' => 'Thank you for your message. Our clinic staff will assist you shortly.',
            ]);
        }
    }
}

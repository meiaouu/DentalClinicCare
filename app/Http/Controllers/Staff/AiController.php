<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
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
        ReplySuggestionService $service
    ): JsonResponse {
        try {
            if ($conversation->conversation_status === 'closed') {
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

            $latestMessageText = trim(
                (string) ($latestSenderMessage->message_text ?: $latestSenderMessage->message_body)
            );

            if ($latestMessageText === '') {
                return response()->json([
                    'suggested_reply' => '',
                    'message' => 'Latest sender message is empty.',
                ]);
            }

            $recentMessages = $conversation->messages()
                ->orderByDesc('message_id')
                ->limit(6)
                ->get()
                ->reverse()
                ->map(function ($message) {
                    return [
                        'sender_type' => $message->sender_type,
                        'message_text' => (string) ($message->message_text ?: $message->message_body ?: ''),
                    ];
                })
                ->values()
                ->all();

            $suggestedReply = $service->suggestFromConversation(
                latestMessage: $latestMessageText,
                conversationMessages: $recentMessages,
                isGuest: (bool) $conversation->is_guest
            );

            return response()->json([
                'suggested_reply' => (string) $suggestedReply,
            ]);
        } catch (Throwable $e) {
            Log::error('AI suggest reply failed', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'suggested_reply' => '',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0,
            ], 401);
        }

        return response()->json([
            'notifications' => $user->notifications()->latest()->limit(10)->get(),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function open(string $id): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $notification = $user->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        if (!empty($data['redirect_url'])) {
            return redirect()->to($data['redirect_url']);
        }

        if (($data['type'] ?? null) === 'appointment_request' && !empty($data['request_id'])) {
            return redirect()->route('staff.appointment-requests.show', $data['request_id']);
        }

        return redirect()->route('staff.dashboard');
    }
}

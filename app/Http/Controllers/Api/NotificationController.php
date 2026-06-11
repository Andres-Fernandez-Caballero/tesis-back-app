<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterNotificationTokenRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function registerToken(RegisterNotificationTokenRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $this->notificationService->registerToken($user, $data);

        return response()
            ->json(['message' => 'Token registered successfully'])
            ->status(201);
    }

    public function sendNotification(Request $request)
    {
        $user = User::find($request->input('user_id'));
        $title = $request->input('title', 'Default Title');
        $body = $request->input('body', 'Default Body');
        $url = $request->input('url', null);

        Log::info(
            'notification_user',
            [
                'user' => $user->email,
                'url' => $url,
            ]
        );
        $user->notify(new \App\Notifications\UserNotification($title, $body, $url));

        return response()
            ->json(['message' => 'Notification sent successfully']);
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->orderByRaw('read_at IS NOT NULL') // Los que son NULL (0) van antes que los NOT NULL (1)
            ->orderBy('created_at', 'DESC')    // Orden secundario para las más recientes
            ->limit(10)
            ->get();

        return NotificationResource::collection($notifications)
            ->additional([
                'meta' => [
                    'unreadCount' => $user->unreadNotifications()->count(),
                ]
            ])
            ->response();
    }

    public function getUnreadNotifications(Request $request)
    {
        $user = $request->user();
        $unreadNotifications = $user->unreadNotifications()->latest()->limit(5)->get();
        return NotificationResource::collection($unreadNotifications)
            ->additional([
                'meta' => [
                    'unread_count' => $user->unreadNotifications()->count(),
                ]
            ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($id);

        if (! $notification) {
            return response()->json(['message' => 'Notificación no encontrada.'], 404);
        }

        if ($notification->read_at !== null) {
            return response()->json(['message' => 'La notificación ya estaba marcada como leída.']);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notificación marcada como leída.']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $notifications = $user->unreadNotifications()->get();
        $notifications->markAsRead();

        return response(
            [
                "message" => "All Notifications was marked"
            ],
        );
    }

    public function countNotRead(Request $request)
    {
        $countNotRead = $request->user()->unreadNotifications()->count();

        return response([
            "message" => "ok",
            "count" => $countNotRead,
        ]);
    }
}

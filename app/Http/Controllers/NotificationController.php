<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * NotificationController
 *
 * Backs the notification bell icon in the top nav (see
 * resources/views/layouts/topnav.blade.php for the polling JS). The
 * frontend polls poll() every 30s rather than using websockets — simple
 * and good enough at this app's scale. read()/readAll() just flip the
 * read_at timestamp; see Notification::markAsRead().
 */
class NotificationController extends Controller
{
    /**
     * GET /notifications/poll
     * Returns unread count + recent notifications as JSON (called every 30s)
     */
    public function poll()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->recent()
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'icon'       => $n->icon,
                'color'      => $n->color,
                'url'        => $n->url,
                'unread'     => $n->isUnread(),
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'unread_count'  => $notifications->where('unread', true)->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * PATCH /notifications/{id}/read
     * Mark a single notification as read and redirect to its URL
     */
    public function read(Notification $notification)
    {
        // Security — only own notifications
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $url = $notification->url;
        $notification->markAsRead();

        // Delete it since "once read, gone"
        $notification->delete();

        return redirect($url ?: '/');
    }

    /**
     * POST /notifications/read-all
     * Mark all as read and delete them
     */
    public function readAll()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->delete();

        return response()->json(['success' => true]);
    }
}
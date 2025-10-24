<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();

            return back()->with('success', 'Notifikasi berhasil dihapus');
        }

        return back()->with('error', 'Notifikasi tidak ditemukan');
    }
}

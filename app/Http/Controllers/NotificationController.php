<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai terbaca.');
    }

    public function markAsRead($id)
    {
        if (auth()->check()) {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        if (auth()->check()) {
            auth()->user()->notifications()->findOrFail($id)->delete();
        }
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        if (auth()->check() && $request->has('notification_ids')) {
            auth()->user()->notifications()->whereIn('id', $request->notification_ids)->delete();
            return redirect()->back()->with('success', 'Notifikasi terpilih berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Tidak ada notifikasi yang terpilih.');
    }
}

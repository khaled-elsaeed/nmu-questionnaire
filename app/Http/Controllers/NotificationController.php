<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Simulate sending a notification to the currently authenticated user
        $user = auth()->user(); // Make sure the user is authenticated
        $title = "New Notification!";
        $message = "You have received a new message.";

        // Send the notification
        $user->notify(new UserNotification($title, $message));

        return redirect()->back()->with('success', 'Notification sent successfully.');
    }

    public function showNotifications()
    {
        // Fetch notifications for the authenticated user
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }
}

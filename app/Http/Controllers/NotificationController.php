<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationPreference;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();

        // Get all notifications
        $notifications = $user->notifications()->paginate(20);

        // Get unread notifications count
        $unreadCount = $user->unreadNotifications()->count();

        // Get notification preferences
        $preferences = $user->notificationPreferences;

        // Group notifications by date
        $groupedNotifications = $notifications->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        });

        return view('notifications.index', compact(
            'notifications',
            'unreadCount',
            'preferences',
            'groupedNotifications'
        ));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        foreach ($request->preferences as $type => $categories) {
            foreach ($categories as $category => $enabled) {
                NotificationPreference::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'type' => $type,
                        'category' => $category
                    ],
                    ['enabled' => $enabled == '1' || $enabled === true]
                );
            }
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Get unread notifications count (for AJAX)
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications (for AJAX)
     */
    public function getLatest()
{
    $notifications = Auth::user()
        ->notifications()
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'read_at' => $notification->read_at?->toISOString(),
                'created_at' => $notification->created_at->toISOString(),
                'icon' => $this->getNotificationIcon($notification->type),
                'color' => $this->getNotificationColor($notification->type)
            ];
        });

    return response()->json(['notifications' => $notifications]);
}

    /**
     * Helper: Get notification icon by type
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'App\Notifications\StudentRegistered' => 'fas fa-user-plus',
            'App\Notifications\TeacherAssigned' => 'fas fa-chalkboard-teacher',
            'App\Notifications\MarkEntered' => 'fas fa-edit',
            'App\Notifications\ReportGenerated' => 'fas fa-chart-bar',
            'App\Notifications\SystemAlert' => 'fas fa-exclamation-triangle',
            'App\Notifications\PasswordChanged' => 'fas fa-key',
            'App\Notifications\UserCreated' => 'fas fa-user-plus',
            'App\Notifications\PaymentReceived' => 'fas fa-money-check-alt',
            'App\Notifications\Announcement' => 'fas fa-bullhorn',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Helper: Get notification color by type
     */
    private function getNotificationColor($type)
    {
        $colors = [
            'App\Notifications\StudentRegistered' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/30',
            'App\Notifications\TeacherAssigned' => 'text-green-600 bg-green-50 dark:bg-green-900/30',
            'App\Notifications\MarkEntered' => 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30',
            'App\Notifications\ReportGenerated' => 'text-purple-600 bg-purple-50 dark:bg-purple-900/30',
            'App\Notifications\SystemAlert' => 'text-red-600 bg-red-50 dark:bg-red-900/30',
            'App\Notifications\PasswordChanged' => 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30',
            'App\Notifications\UserCreated' => 'text-pink-600 bg-pink-50 dark:bg-pink-900/30',
            'App\Notifications\PaymentReceived' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30',
            'App\Notifications\Announcement' => 'text-orange-600 bg-orange-50 dark:bg-orange-900/30',
        ];

        return $colors[$type] ?? 'text-gray-600 bg-gray-50 dark:bg-gray-900/30';
    }
}

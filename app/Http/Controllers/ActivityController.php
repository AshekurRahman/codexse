<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\UserSession;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Show activity log and sessions.
     */
    public function index()
    {
        $user = Auth::user();

        // Get recent activity logs
        $activities = ActivityLog::forUser($user->id)
            ->latest()
            ->take(50)
            ->get();

        // Get active sessions
        $sessions = UserSession::forUser($user->id)
            ->active()
            ->orderBy('last_active_at', 'desc')
            ->get();

        // Get security alerts
        $alerts = ActivityLogService::getSecurityAlerts($user);

        // Get activity summary
        $summary = ActivityLogService::getUserActivitySummary($user, 30);

        return view('pages.activity.index', compact(
            'activities',
            'sessions',
            'alerts',
            'summary'
        ));
    }

    /**
     * Revoke a session.
     */
    public function revokeSession(Request $request, UserSession $session)
    {
        $user = Auth::user();

        // Ensure the session belongs to the user
        if ($session->user_id !== $user->id) {
            abort(403);
        }

        // Cannot revoke current session
        if ($session->session_id === session()->getId()) {
            return back()->with('error', 'You cannot revoke your current session.');
        }

        // Log the revocation
        ActivityLogService::logSessionRevoked($user, $session);

        // Revoke the session
        $session->revoke();

        return back()->with('success', 'Session revoked successfully.');
    }

    /**
     * Revoke all other sessions.
     */
    public function revokeAllSessions()
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        // Get all other active sessions
        $sessions = UserSession::forUser($user->id)
            ->active()
            ->where('session_id', '!=', $currentSessionId)
            ->get();

        foreach ($sessions as $session) {
            ActivityLogService::logSessionRevoked($user, $session);
            $session->revoke();
        }

        return back()->with('success', 'All other sessions have been revoked.');
    }

    /**
     * Filter activities by category.
     */
    public function filter(Request $request)
    {
        $user = Auth::user();
        $category = $request->get('category');
        $days = $request->get('days', 30);

        $query = ActivityLog::forUser($user->id)->recent($days);

        if ($category && $category !== 'all') {
            $query->forCategory($category);
        }

        $activities = $query->latest()->take(100)->get();

        return response()->json([
            'activities' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action' => $activity->action_name,
                    'action_color' => $activity->action_color,
                    'action_icon' => $activity->action_icon,
                    'category' => $activity->category_name,
                    'description' => $activity->description,
                    'ip_address' => $activity->ip_address,
                    'device_info' => $activity->device_info,
                    'location' => $activity->location,
                    'created_at' => $activity->created_at->format('M j, Y g:i A'),
                    'is_suspicious' => $activity->is_suspicious,
                ];
            }),
        ]);
    }
}

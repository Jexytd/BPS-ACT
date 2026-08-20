<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use App\Models\Activity;
use App\Models\User;
use App\Models\Division;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    protected function getUsersCollection()
    {
        try {
            $dbUsers = User::all();
            if ($dbUsers->isNotEmpty()) {
                return $dbUsers->keyBy('id')->toArray();
            }
        } catch (\Throwable $e) {}
        return $this->firestore->getCollection('users');
    }

    protected function getDivisionsCollection()
    {
        try {
            $dbDivisions = Division::all();
            if ($dbDivisions->isNotEmpty()) {
                return $dbDivisions->keyBy('id')->toArray();
            }
        } catch (\Throwable $e) {}
        return $this->firestore->getCollection('divisions');
    }

    protected function getActivitiesCollection()
    {
        try {
            $dbActivities = Activity::all();
            if ($dbActivities->isNotEmpty()) {
                return $dbActivities->keyBy('id')->toArray();
            }
        } catch (\Throwable $e) {}
        return $this->firestore->getCollection('activities');
    }

    public function index()
    {
        $user = session('user');
        $users = collect($this->getUsersCollection())->keyBy('id');
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        $activities = collect($this->getActivitiesCollection());

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        // 1. Activities scheduled this week
        $activitiesThisWeek = $activities->filter(function ($act) use ($startOfWeek, $endOfWeek) {
            $startVal = $act['start'] ?? ($act['start_date'] ?? null);
            if (!$startVal) return false;
            
            $start = Carbon::parse($startVal);
            return $start->between($startOfWeek, $endOfWeek);
        });

        // 2. Completion rate
        $totalActivities = $activities->count();
        $completedActivities = $activities->filter(function ($act) {
            return in_array($act['status'] ?? '', ['Confirmed', 'done', 'Closed']);
        })->count();
        $completionRate = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0;

        // 3. Per-person load
        $userWorkload = [];
        foreach ($users as $u) {
            $count = $activities->filter(function ($act) use ($u) {
                return in_array($u['id'], $act['assignees'] ?? [$act['assignee_id'] ?? null]);
            })->count();

            $userWorkload[] = [
                'user' => $u,
                'division' => $divisions->get($u['division_id'] ?? '') ?? ['name' => 'Umum'],
                'count' => $count,
            ];
        }

        // 4. Overdue and Unassigned List
        $overdueList = $activities->filter(function ($act) use ($now) {
            $endVal = $act['end'] ?? ($act['due_date'] ?? null);
            if (!$endVal) return false;

            $end = Carbon::parse($endVal);
            $isNotDone = !in_array($act['status'] ?? '', ['Confirmed', 'done', 'Closed', 'cancelled']);
            $isUnassigned = empty($act['assignees']) && empty($act['assignee_id']);
            return ($end->lt($now) && $isNotDone) || $isUnassigned;
        });

        // 5. Team Specific Data
        $myDivisionId = $user['division_id'] ?? null;
        $myTeamActivities = collect();
        $recentTeamFeed = collect();

        if ($myDivisionId) {
            $myTeamActivities = $activities->filter(function ($act) use ($myDivisionId) {
                return ($act['division_id'] ?? null) === $myDivisionId;
            });

            $recentTeamFeed = $myTeamActivities->filter(function ($act) use ($user) {
                // Exclude if user has deleted this notification
                $deletedBy = $act['deleted_notification_by'] ?? ($act['deletedNotificationBy'] ?? []);
                return !in_array($user['id'], $deletedBy);
            })->sortByDesc(function ($act) {
                return Carbon::parse($act['created_at'] ?? '1970-01-01')->timestamp;
            });
            
            $unreadCount = $recentTeamFeed->filter(function($act) use ($user) {
                 $readBy = $act['read_by'] ?? ($act['readBy'] ?? []);
                 return !in_array($user['id'], $readBy);
            })->count();
            
            $recentTeamFeed = $recentTeamFeed->take(3); // only top 3 for dropdown
        } else {
            $unreadCount = 0;
        }

        return view('dashboard', compact(
            'user',
            'users',
            'divisions',
            'activities',
            'activitiesThisWeek',
            'completionRate',
            'userWorkload',
            'overdueList',
            'totalActivities',
            'completedActivities',
            'myTeamActivities',
            'recentTeamFeed',
            'unreadCount'
        ));
    }
}

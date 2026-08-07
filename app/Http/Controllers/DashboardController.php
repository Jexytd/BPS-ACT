<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function index()
    {
        $user = session('user');
        $users = collect($this->firestore->getCollection('users'))->keyBy('id');
        $divisions = collect($this->firestore->getCollection('divisions'))->keyBy('id');
        $activities = collect($this->firestore->getCollection('activities'));

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        // 1. Activities scheduled this week
        $activitiesThisWeek = $activities->filter(function ($act) use ($startOfWeek, $endOfWeek) {
            $start = Carbon::parse($act['start'] ?? $act['start_date']);
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
            $end = Carbon::parse($act['end'] ?? $act['due_date']);
            $isNotDone = !in_array($act['status'] ?? '', ['Confirmed', 'done', 'Closed', 'cancelled']);
            $isUnassigned = empty($act['assignees']) && empty($act['assignee_id']);
            return ($end->lt($now) && $isNotDone) || $isUnassigned;
        });

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
            'completedActivities'
        ));
    }
}

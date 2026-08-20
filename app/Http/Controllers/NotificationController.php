<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function index()
    {
        $user = session('user');

        try {
            $dbUsers = User::all();
            if ($dbUsers->isNotEmpty()) {
                $users = $dbUsers->keyBy('id')->toArray();
            } else {
                $users = collect($this->firestore->getCollection('users'))->keyBy('id');
            }
        } catch (\Throwable $e) {
            $users = collect($this->firestore->getCollection('users'))->keyBy('id');
        }
        
        $myDivisionId = $user['division_id'] ?? null;
        
        try {
            $dbActivities = Activity::all();
            if ($dbActivities->isNotEmpty()) {
                $activities = collect($dbActivities->keyBy('id')->toArray());
            } else {
                $activities = collect($this->firestore->getCollection('activities'));
            }
        } catch (\Throwable $e) {
            $activities = collect($this->firestore->getCollection('activities'));
        }
        
        $notifications = collect();

        if ($myDivisionId) {
            // Get all activities from this user's division
            $notifications = $activities->filter(function ($act) use ($myDivisionId, $user) {
                // Must be in the same division
                if (($act['division_id'] ?? null) !== $myDivisionId) {
                    return false;
                }
                
                // Exclude if user has deleted this notification
                $deletedBy = $act['deleted_notification_by'] ?? ($act['deletedNotificationBy'] ?? []);
                if (is_array($deletedBy) && in_array($user['id'] ?? '', $deletedBy)) {
                    return false;
                }
                
                return true;
            })->sortByDesc(function ($act) {
                return Carbon::parse($act['created_at'] ?? '1970-01-01')->timestamp;
            });
        }

        return view('notifications.index', compact('user', 'users', 'notifications'));
    }
}

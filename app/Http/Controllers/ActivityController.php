<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use App\Http\Requests\ActivityStoreRequest;
use App\Http\Requests\ActivityUpdateRequest;
use App\Models\Activity;
use App\Models\User;
use App\Models\Division;
use Carbon\Carbon;

class ActivityController extends Controller
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

    public function create()
    {
        $user = session('user');
        $users = $this->getUsersCollection();
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        
        return view('activities.create', compact('user', 'users', 'divisions'));
    }

    public function edit(string $id)
    {
        $user = session('user');
        
        $activity = null;
        try {
            $model = Activity::find($id);
            if ($model) {
                $activity = $model->toArray();
            }
        } catch (\Throwable $e) {}

        if (!$activity) {
            $activity = $this->firestore->getDocument('activities', $id);
        }

        if (!$activity) {
            return redirect()->route('activities.index')->with('error', 'Kegiatan tidak ditemukan');
        }

        $users = $this->getUsersCollection();
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        
        return view('activities.edit', compact('user', 'activity', 'users', 'divisions'));
    }

    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function list()
    {
        $user = session('user');
        $users = collect($this->getUsersCollection())->keyBy('id');
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        
        $activities = collect($this->getActivitiesCollection())->map(function($act) use ($users, $divisions) {
            $creator = $users->get($act['created_by'] ?? ($act['createdBy'] ?? null));
            $act['creator_name'] = $creator ? $creator['name'] : 'Sistem';
            $act['creator_team'] = $creator && isset($creator['division_id']) ? ($divisions->get($creator['division_id'])['name'] ?? 'Umum') : ($divisions->get($act['division_id'] ?? '')['name'] ?? 'Umum');
            
            $actAssignees = $act['assignees'] ?? [$act['assignee_id'] ?? null];
            $act['assignees_rich'] = collect($actAssignees)->filter()->map(function($id) use ($users, $divisions) {
                $u = $users->get($id);
                if (!$u) return ['name' => $id, 'team' => 'Unknown', 'avatar' => null, 'initials' => 'U'];
                $div = $divisions->get($u['division_id'] ?? '')['name'] ?? 'Umum';
                return [
                    'name' => $u['name'],
                    'team' => $div,
                    'avatar' => $u['photo'] ?? null,
                    'initials' => strtoupper(substr($u['name'], 0, 2))
                ];
            })->values()->toArray();
            return $act;
        })->sortByDesc('start_date')->values();

        return view('activities.list', compact('user', 'activities', 'divisions'));
    }

    public function calendarTest()
    {
        $user = session('user');
        $users = collect($this->getUsersCollection())->keyBy('id');
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');

        return view('activities.calendar_test', compact('user', 'users', 'divisions'));
    }

    public function getEvents(Request $request)
    {
        $activities = $this->getActivitiesCollection();
        $users = collect($this->getUsersCollection())->keyBy('id');
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        $teamId = $request->query('team_id');
        $events = [];

        foreach ($activities as $act) {
            if ($teamId && ($act['division_id'] ?? null) !== $teamId) {
                continue;
            }

            $statusColor = match ($act['status'] ?? 'planned') {
                'Confirmed', 'done' => '#6DBE45',
                'In progress', 'ongoing' => '#ED8B00',
                'In specification', 'planned' => '#005AA9',
                'New' => '#00A6B4',
                'cancelled', 'Closed' => '#6B7280',
                default => '#005AA9'
            };

            $creator = $users->get($act['created_by'] ?? ($act['createdBy'] ?? null));
            $creatorName = $creator ? $creator['name'] : 'Sistem';
            $creatorTeam = $creator && isset($creator['division_id']) ? ($divisions->get($creator['division_id'])['name'] ?? 'Umum') : 'Umum';

            $actAssignees = $act['assignees'] ?? [$act['assignee_id'] ?? null];
            $assigneesNames = [];
            $assigneesRich = [];
            foreach (collect($actAssignees)->filter() as $id) {
                $u = $users->get($id);
                if (!$u) {
                    $assigneesNames[] = $id;
                    $assigneesRich[] = ['name' => $id, 'team' => 'Unknown', 'avatar' => null, 'initials' => 'U'];
                    continue;
                }
                $div = $divisions->get($u['division_id'] ?? '')['name'] ?? 'Umum';
                $assigneesNames[] = $u['name'];
                $assigneesRich[] = [
                    'name' => $u['name'],
                    'team' => $div,
                    'avatar' => $u['photo'] ?? null,
                    'initials' => strtoupper(substr($u['name'], 0, 2))
                ];
            }

            $startRaw = $act['start'] ?? ($act['start_date'] ?? null);
            $endRaw = $act['end'] ?? ($act['due_date'] ?? null);

            $start = $startRaw;
            if ($startRaw && (strlen($startRaw) === 10 || str_ends_with($startRaw, '00:00:00') || str_ends_with($startRaw, '00:00:00Z'))) {
                $start = substr($startRaw, 0, 10) . 'T07:30:00';
            }

            $end = $endRaw;
            if ($endRaw && (strlen($endRaw) === 10 || str_ends_with($endRaw, '00:00:00') || str_ends_with($endRaw, '00:00:00Z'))) {
                $end = substr($endRaw, 0, 10) . 'T16:30:00';
            }

            $events[] = [
                'id' => (string) $act['id'],
                'title' => $act['title'] ?? $act['subject'] ?? 'Kegiatan BPS',
                'start' => $start,
                'end' => $end,
                'allDay' => false,
                'resourceId' => $act['assignees'][0] ?? ($act['assignee_id'] ?? null),
                'resourceIds' => $act['assignees'] ?? [$act['assignee_id'] ?? null],
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'extendedProps' => [
                    'description' => $act['description'] ?? '',
                    'status' => $act['status'] ?? 'planned',
                    'category' => $act['category'] ?? 'Survei',
                    'location' => $act['location'] ?? 'BPS HQ',
                    'createdBy' => $act['created_by'] ?? ($act['createdBy'] ?? 'system'),
                    'creator_name' => $creatorName,
                    'creator_team' => $creatorTeam,
                    'project_name' => $act['project_name'] ?? 'Kegiatan Tim',
                    'assignees' => $act['assignees'] ?? [$act['assignee_id'] ?? null],
                    'assignees_names' => $assigneesNames,
                    'assignees_rich' => $assigneesRich,
                    'result' => $act['result'] ?? '',
                    'documents' => $act['documents'] ?? null,
                    'documents_links' => $act['documents_links'] ?? null,
                ]
            ];
        }

        return response()->json($events);
    }

    public function getResources(Request $request)
    {
        $users = $this->getUsersCollection();
        $divisions = collect($this->getDivisionsCollection())->keyBy('id');
        $teamId = $request->query('team_id');
        $resources = [];

        foreach ($users as $u) {
            if ($teamId && ($u['division_id'] ?? null) !== $teamId) {
                continue;
            }
            $div = $divisions->get($u['division_id'] ?? '') ?? ['name' => 'Umum'];
            $resources[] = [
                'id' => (string) $u['id'],
                'title' => $u['name'],
                'group' => $div['name'],
                'role' => strtoupper($u['role'] ?? 'staff'),
                'avatar' => $u['photo'] ?? null,
                'initials' => strtoupper(substr($u['name'], 0, 2)),
            ];
        }

        return response()->json($resources);
    }

    public function checkConflicts(Request $request)
    {
        $assignees = $request->input('assignees', []);
        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));
        $excludeId = $request->input('exclude_id');

        $activities = $this->getActivitiesCollection();
        $conflicts = [];

        foreach ($activities as $act) {
            if ($excludeId && (string) $act['id'] === (string) $excludeId) {
                continue;
            }

            $actStart = Carbon::parse($act['start'] ?? $act['start_date']);
            $actEnd = Carbon::parse($act['end'] ?? $act['due_date']);
            $actAssignees = $act['assignees'] ?? [$act['assignee_id'] ?? null];

            // Overlap check
            if ($start->lt($actEnd) && $end->gt($actStart)) {
                $intersect = array_intersect($assignees, $actAssignees);
                if (!empty($intersect)) {
                    $conflicts[] = [
                        'activity_id' => $act['id'],
                        'activity_title' => $act['title'] ?? $act['subject'] ?? 'Kegiatan BPS',
                        'conflicting_users' => array_values($intersect),
                        'start' => $actStart->toIso8601String(),
                        'end' => $actEnd->toIso8601String(),
                    ];
                }
            }
        }

        return response()->json([
            'has_conflict' => !empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    public function store(ActivityStoreRequest $request)
    {
        $user = session('user');
        $validated = $request->validated();

        $id = (string) \Illuminate\Support\Str::uuid();
        $payload = array_merge($validated, [
            'id' => $id,
            'subject' => $validated['title'],
            'start_date' => Carbon::parse($validated['start'])->toDateString(),
            'due_date' => Carbon::parse($validated['end'])->toDateString(),
            'all_day' => $validated['allDay'] ?? false,
            'assignee_id' => $validated['assignees'][0] ?? null,
            'created_by' => $user['id'] ?? null,
            'createdBy' => $user['id'] ?? null,
            'division_id' => $user['division_id'] ?? null,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ]);

        try {
            Activity::updateOrCreate(['id' => $id], $payload);
        } catch (\Throwable $e) {}

        $saved = $this->firestore->setDocument('activities', $id, $payload);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $payload]);
        }

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(ActivityUpdateRequest $request, string $id)
    {
        $user = session('user');
        
        $existing = null;
        try {
            $model = Activity::find($id);
            if ($model) {
                $existing = $model->toArray();
            }
        } catch (\Throwable $e) {}

        if (!$existing) {
            $existing = $this->firestore->getDocument('activities', $id);
        }

        if (!$existing) {
            return response()->json(['message' => 'Kegiatan tidak ditemukan'], 404);
        }

        $creatorId = $existing['created_by'] ?? ($existing['createdBy'] ?? null);
        if ($user['role'] !== 'admin' && $creatorId !== $user['id'] && !in_array($user['id'], $existing['assignees'] ?? [])) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk mengedit kegiatan ini.'], 403);
        }

        $validated = $request->validated();
        if (isset($validated['title'])) {
            $validated['subject'] = $validated['title'];
        }
        if (isset($validated['start'])) {
            $validated['start_date'] = Carbon::parse($validated['start'])->toDateString();
        }
        if (isset($validated['end'])) {
            $validated['due_date'] = Carbon::parse($validated['end'])->toDateString();
        }
        if (isset($validated['allDay'])) {
            $validated['all_day'] = $validated['allDay'];
        }
        if (isset($validated['assignees'])) {
            $validated['assignee_id'] = $validated['assignees'][0] ?? null;
        }

        try {
            Activity::where('id', $id)->update($validated);
        } catch (\Throwable $e) {}

        $updated = $this->firestore->setDocument('activities', $id, $validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $updated]);
        }

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = session('user');
        $existing = null;

        try {
            $model = Activity::find($id);
            if ($model) {
                $existing = $model->toArray();
            }
        } catch (\Throwable $e) {}

        if (!$existing) {
            $existing = $this->firestore->getDocument('activities', $id);
        }

        if (!$existing) {
            return response()->json(['message' => 'Kegiatan tidak ditemukan'], 404);
        }

        $creatorId = $existing['created_by'] ?? ($existing['createdBy'] ?? null);
        if ($user['role'] !== 'admin' && $creatorId !== $user['id']) {
            return response()->json(['message' => 'Hanya admin atau pembuat kegiatan yang dapat menghapus.'], 403);
        }

        try {
            Activity::where('id', $id)->delete();
        } catch (\Throwable $e) {}

        $this->firestore->deleteDocument('activities', $id);

        return response()->json(['status' => 'success', 'message' => 'Kegiatan berhasil dihapus.']);
    }

    public function markNotificationRead(string $id)
    {
        $user = session('user');
        $existing = null;

        try {
            $model = Activity::find($id);
            if ($model) {
                $existing = $model->toArray();
            }
        } catch (\Throwable $e) {}

        if (!$existing) {
            $existing = $this->firestore->getDocument('activities', $id);
        }

        if (!$existing) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $readBy = $existing['read_by'] ?? ($existing['readBy'] ?? []);
        if (!in_array($user['id'], $readBy)) {
            $readBy[] = $user['id'];
            try {
                Activity::where('id', $id)->update(['read_by' => $readBy]);
            } catch (\Throwable $e) {}
            $this->firestore->setDocument('activities', $id, ['readBy' => $readBy]);
        }

        return response()->json(['status' => 'success']);
    }

    public function deleteNotification(string $id)
    {
        $user = session('user');
        $existing = null;

        try {
            $model = Activity::find($id);
            if ($model) {
                $existing = $model->toArray();
            }
        } catch (\Throwable $e) {}

        if (!$existing) {
            $existing = $this->firestore->getDocument('activities', $id);
        }

        if (!$existing) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $deletedBy = $existing['deleted_notification_by'] ?? ($existing['deletedNotificationBy'] ?? []);
        if (!in_array($user['id'], $deletedBy)) {
            $deletedBy[] = $user['id'];
            try {
                Activity::where('id', $id)->update(['deleted_notification_by' => $deletedBy]);
            } catch (\Throwable $e) {}
            $this->firestore->setDocument('activities', $id, ['deletedNotificationBy' => $deletedBy]);
        }

        return response()->json(['status' => 'success']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use App\Http\Requests\ActivityStoreRequest;
use App\Http\Requests\ActivityUpdateRequest;
use Carbon\Carbon;

class ActivityController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function index()
    {
        $user = session('user');
        $users = $this->firestore->getCollection('users');
        $divisions = $this->firestore->getCollection('divisions');
        $activities = $this->firestore->getCollection('activities');

        return view('activities.index', compact('user', 'users', 'divisions', 'activities'));
    }

    public function getEvents(Request $request)
    {
        $activities = $this->firestore->getCollection('activities');
        $users = collect($this->firestore->getCollection('users'))->keyBy('id');
        $divisions = collect($this->firestore->getCollection('divisions'))->keyBy('id');
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

            $creator = $users->get($act['createdBy'] ?? null);
            $creatorName = $creator ? $creator['name'] : 'Sistem';
            $creatorTeam = $creator && isset($creator['division_id']) ? ($divisions->get($creator['division_id'])['name'] ?? 'Umum') : 'Umum';

            $actAssignees = $act['assignees'] ?? [$act['assignee_id'] ?? null];
            $assigneesNames = collect($actAssignees)
                ->filter()
                ->map(fn($id) => $users->get($id)['name'] ?? $id)
                ->values()
                ->toArray();

            $events[] = [
                'id' => (string) $act['id'],
                'title' => $act['title'] ?? $act['subject'] ?? 'Kegiatan BPS',
                'start' => $act['start'] ?? ($act['start_date'] ?? null),
                'end' => $act['end'] ?? ($act['due_date'] ?? null),
                'allDay' => $act['allDay'] ?? true,
                'resourceId' => $act['assignees'][0] ?? ($act['assignee_id'] ?? null),
                'resourceIds' => $act['assignees'] ?? [$act['assignee_id'] ?? null],
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'extendedProps' => [
                    'description' => $act['description'] ?? '',
                    'status' => $act['status'] ?? 'planned',
                    'category' => $act['category'] ?? 'Survei',
                    'location' => $act['location'] ?? 'BPS HQ',
                    'createdBy' => $act['createdBy'] ?? 'system',
                    'creator_name' => $creatorName,
                    'creator_team' => $creatorTeam,
                    'project_name' => $act['project_name'] ?? 'Kegiatan Tim',
                    'assignees' => $act['assignees'] ?? [$act['assignee_id'] ?? null],
                    'assignees_names' => $assigneesNames,
                ]
            ];
        }

        return response()->json($events);
    }

    public function getResources()
    {
        $users = $this->firestore->getCollection('users');
        $divisions = collect($this->firestore->getCollection('divisions'))->keyBy('id');
        $resources = [];

        foreach ($users as $u) {
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

        $activities = $this->firestore->getCollection('activities');
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

        $id = (string) rand(100, 9999);
        $payload = array_merge($validated, [
            'id' => $id,
            'subject' => $validated['title'],
            'start_date' => Carbon::parse($validated['start'])->toDateString(),
            'due_date' => Carbon::parse($validated['end'])->toDateString(),
            'assignee_id' => $validated['assignees'][0] ?? null,
            'createdBy' => $user['id'],
            'division_id' => $user['division_id'] ?? null,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ]);

        $saved = $this->firestore->setDocument('activities', $id, $payload);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $saved]);
        }

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(ActivityUpdateRequest $request, string $id)
    {
        $user = session('user');
        $existing = $this->firestore->getDocument('activities', $id);

        if (!$existing) {
            return response()->json(['message' => 'Kegiatan tidak ditemukan'], 404);
        }

        // Staff can only update activities they created or are assigned to
        if ($user['role'] !== 'admin' && $existing['createdBy'] !== $user['id'] && !in_array($user['id'], $existing['assignees'] ?? [])) {
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
        if (isset($validated['assignees'])) {
            $validated['assignee_id'] = $validated['assignees'][0] ?? null;
        }

        $updated = $this->firestore->setDocument('activities', $id, $validated);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $updated]);
        }

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = session('user');
        $existing = $this->firestore->getDocument('activities', $id);

        if (!$existing) {
            return response()->json(['message' => 'Kegiatan tidak ditemukan'], 404);
        }

        if ($user['role'] !== 'admin' && $existing['createdBy'] !== $user['id']) {
            return response()->json(['message' => 'Hanya admin atau pembuat kegiatan yang dapat menghapus.'], 403);
        }

        $this->firestore->deleteDocument('activities', $id);

        return response()->json(['status' => 'success', 'message' => 'Kegiatan berhasil dihapus.']);
    }

    public function markNotificationRead(string $id)
    {
        $user = session('user');
        $existing = $this->firestore->getDocument('activities', $id);

        if (!$existing) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $readBy = $existing['readBy'] ?? [];
        if (!in_array($user['id'], $readBy)) {
            $readBy[] = $user['id'];
            $this->firestore->setDocument('activities', $id, ['readBy' => $readBy]);
        }

        return response()->json(['status' => 'success']);
    }

    public function deleteNotification(string $id)
    {
        $user = session('user');
        $existing = $this->firestore->getDocument('activities', $id);

        if (!$existing) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $deletedBy = $existing['deletedNotificationBy'] ?? [];
        if (!in_array($user['id'], $deletedBy)) {
            $deletedBy[] = $user['id'];
            $this->firestore->setDocument('activities', $id, ['deletedNotificationBy' => $deletedBy]);
        }

        return response()->json(['status' => 'success']);
    }
}

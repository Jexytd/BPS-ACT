<?php

namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FirestoreService
{
    protected $database;
    protected bool $isLocalFallback = false;
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/firestore_local.json');
        
        try {
            if (config('firebase.projects.app.credentials') && file_exists(config('firebase.projects.app.credentials'))) {
                $this->database = Firebase::project('app')->firestore()->database();
            } else {
                $this->isLocalFallback = true;
                $this->ensureLocalStoreExists();
            }
        } catch (\Throwable $e) {
            Log::warning('Firestore connection fallback to local storage: ' . $e->getMessage());
            $this->isLocalFallback = true;
            $this->ensureLocalStoreExists();
        }
    }

    protected function ensureLocalStoreExists(): void
    {
        if (!File::exists($this->storagePath)) {
            $initialData = [
                'users' => [
                    'usr_catherine' => [
                        'id' => 'usr_catherine',
                        'name' => 'Catherine Johnson',
                        'email' => 'catherine@bps.go.id',
                        'role' => 'staff',
                        'division_id' => 'div_soc',
                        'photo' => 'https://ui-avatars.com/api/?name=Catherine+Johnson&background=005AA9&color=fff'
                    ],
                    'usr_daphne' => [
                        'id' => 'usr_daphne',
                        'name' => 'Daphne Turner',
                        'email' => 'daphne@bps.go.id',
                        'role' => 'staff',
                        'division_id' => 'div_ipd',
                        'photo' => 'https://ui-avatars.com/api/?name=Daphne+Turner&background=00A6B4&color=fff'
                    ],
                    'usr_leonard' => [
                        'id' => 'usr_leonard',
                        'name' => 'Leonard Douglas',
                        'email' => 'leonard@bps.go.id',
                        'role' => 'admin',
                        'division_id' => 'div_ipd',
                        'photo' => 'https://ui-avatars.com/api/?name=Leonard+Douglas&background=6DBE45&color=fff'
                    ],
                    'usr_maya' => [
                        'id' => 'usr_maya',
                        'name' => 'Maya Berdygylyjova',
                        'email' => 'maya@bps.go.id',
                        'role' => 'staff',
                        'division_id' => 'div_prod',
                        'photo' => 'https://ui-avatars.com/api/?name=Maya+Berdygylyjova&background=ED8B00&color=fff'
                    ]
                ],
                'divisions' => [
                    'div_soc' => [
                        'id' => 'div_soc',
                        'name' => 'Statistik Sosial',
                        'code' => 'SOC',
                        'color' => '#005AA9'
                    ],
                    'div_ipd' => [
                        'id' => 'div_ipd',
                        'name' => 'Integrasi Pengolahan Data',
                        'code' => 'IPD',
                        'color' => '#00A6B4'
                    ],
                    'div_prod' => [
                        'id' => 'div_prod',
                        'name' => 'Statistik Produksi',
                        'code' => 'PROD',
                        'color' => '#6DBE45'
                    ]
                ],
                'activities' => [
                    '742' => [
                        'id' => '742',
                        'title' => 'Launch new website',
                        'subject' => 'Launch new website',
                        'description' => 'Final review and launch of updated BPS public portal.',
                        'start' => '2026-06-08T09:00:00Z',
                        'end' => '2026-06-09T17:00:00Z',
                        'start_date' => '2026-06-08',
                        'due_date' => '2026-06-09',
                        'allDay' => true,
                        'location' => 'BPS HQ Room 301',
                        'status' => 'Confirmed',
                        'category' => 'Pengolahan',
                        'assignees' => ['usr_catherine'],
                        'assignee_id' => 'usr_catherine',
                        'project_id' => 'proj_web',
                        'project_name' => 'Website Development',
                        'createdBy' => 'usr_leonard',
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ],
                    '744' => [
                        'id' => '744',
                        'title' => 'Press release',
                        'subject' => 'Press release',
                        'description' => 'Preparation of economic growth statistics press release.',
                        'start' => '2026-06-11T08:00:00Z',
                        'end' => '2026-06-12T16:00:00Z',
                        'start_date' => '2026-06-11',
                        'due_date' => '2026-06-12',
                        'allDay' => true,
                        'location' => 'Press Room',
                        'status' => 'In specification',
                        'category' => 'Survei',
                        'assignees' => ['usr_daphne'],
                        'assignee_id' => 'usr_daphne',
                        'project_id' => 'proj_pub',
                        'project_name' => 'Public Relations',
                        'createdBy' => 'usr_leonard',
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ],
                    '56' => [
                        'id' => '56',
                        'title' => 'Design and Prototyping',
                        'subject' => 'Design and Prototyping',
                        'description' => 'UI design and workflow prototype for Census activity tracking.',
                        'start' => '2026-03-03T08:00:00Z',
                        'end' => '2026-07-31T17:00:00Z',
                        'start_date' => '2026-03-03',
                        'due_date' => '2026-07-31',
                        'allDay' => true,
                        'location' => 'Design Studio',
                        'status' => 'In progress',
                        'category' => 'Sensus',
                        'assignees' => ['usr_leonard'],
                        'assignee_id' => 'usr_leonard',
                        'project_id' => 'proj_web',
                        'project_name' => 'Website Development',
                        'createdBy' => 'usr_leonard',
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ],
                    '743' => [
                        'id' => '743',
                        'title' => 'Translate website into German',
                        'subject' => 'Translate website into German',
                        'description' => 'Translation of key statistical indicators into German.',
                        'start' => '2026-06-09T09:00:00Z',
                        'end' => '2026-06-12T17:00:00Z',
                        'start_date' => '2026-06-09',
                        'due_date' => '2026-06-12',
                        'allDay' => true,
                        'location' => 'Online',
                        'status' => 'New',
                        'category' => 'Pengolahan',
                        'assignees' => ['usr_maya'],
                        'assignee_id' => 'usr_maya',
                        'project_id' => 'proj_web',
                        'project_name' => 'Website Development',
                        'createdBy' => 'usr_leonard',
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ]
                ]
            ];
            File::ensureDirectoryExists(storage_path('app'));
            File::put($this->storagePath, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }

    protected function getLocalData(): array
    {
        $this->ensureLocalStoreExists();
        return json_decode(File::get($this->storagePath), true) ?? [];
    }

    protected function saveLocalData(array $data): void
    {
        File::put($this->storagePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getCollection(string $collection): array
    {
        if ($this->isLocalFallback) {
            $data = $this->getLocalData();
            return array_values($data[$collection] ?? []);
        }

        try {
            $snapshot = $this->database->collection($collection)->documents();
            $results = [];
            foreach ($snapshot as $document) {
                if ($document->exists()) {
                    $results[] = array_merge(['id' => (string) $document->id()], $document->data());
                }
            }
            return $results;
        } catch (\Throwable $e) {
            Log::error("Firestore getCollection error for {$collection}: " . $e->getMessage());
            $data = $this->getLocalData();
            return array_values($data[$collection] ?? []);
        }
    }

    public function getDocument(string $collection, string $id): ?array
    {
        if ($this->isLocalFallback) {
            $data = $this->getLocalData();
            return $data[$collection][$id] ?? null;
        }

        try {
            $doc = $this->database->collection($collection)->document($id)->snapshot();
            return $doc->exists() ? array_merge(['id' => (string) $doc->id()], $doc->data()) : null;
        } catch (\Throwable $e) {
            $data = $this->getLocalData();
            return $data[$collection][$id] ?? null;
        }
    }

    public function setDocument(string $collection, string $id, array $payload): array
    {
        $payload['updated_at'] = now()->toIso8601String();
        
        if ($this->isLocalFallback) {
            $data = $this->getLocalData();
            $data[$collection][$id] = array_merge(['id' => (string) $id], $payload);
            $this->saveLocalData($data);
            return $data[$collection][$id];
        }

        try {
            $this->database->collection($collection)->document($id)->set($payload, ['merge' => true]);
            return array_merge(['id' => (string) $id], $payload);
        } catch (\Throwable $e) {
            Log::error("Firestore setDocument error for {$collection}/{$id}: " . $e->getMessage());
            $data = $this->getLocalData();
            $data[$collection][$id] = array_merge(['id' => (string) $id], $payload);
            $this->saveLocalData($data);
            return $data[$collection][$id];
        }
    }

    public function deleteDocument(string $collection, string $id): bool
    {
        if ($this->isLocalFallback) {
            $data = $this->getLocalData();
            unset($data[$collection][$id]);
            $this->saveLocalData($data);
            return true;
        }

        try {
            $this->database->collection($collection)->document($id)->delete();
            return true;
        } catch (\Throwable $e) {
            $data = $this->getLocalData();
            unset($data[$collection][$id]);
            $this->saveLocalData($data);
            return true;
        }
    }

    public function seedInitialData(): bool
    {
        $this->ensureLocalStoreExists();
        return true;
    }
}

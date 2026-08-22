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
        $this->storagePath = storage_path('app/firestore_local_store.json');

        try {
            if (class_exists(\Google\Cloud\Firestore\FirestoreClient::class)) {
                $this->database = Firebase::project('app')
                    ->firestore()
                    ->database();

                Log::info('Firestore connection initialized successfully.', [
                    'project_id' => config('firebase.projects.app.project_id', 'bpsact-e8fc3'),
                ]);
            } else {
                $this->isLocalFallback = true;
                $this->ensureLocalStoreExists();
                Log::info('Firestore client class not found. Using local JSON store fallback.');
            }
        } catch (\Throwable $e) {
            $this->isLocalFallback = true;
            $this->ensureLocalStoreExists();
            Log::warning('Firestore initialization failed. Falling back to local store.', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
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
                    'div_ipds' => [
                        'id' => 'div_ipds',
                        'name' => 'Tim Kerja Pengolahan dan Teknologi Informasi',
                        'code' => 'IPDS',
                        'color' => '#00A6B4'
                    ],
                    'div_nerwilis' => [
                        'id' => 'div_nerwilis',
                        'name' => 'Tim Kerja Nerwilis dan Tim Kerja UKP',
                        'code' => 'NERWILIS',
                        'color' => '#005AA9'
                    ],
                    'div_pss' => [
                        'id' => 'div_pss',
                        'name' => 'Tim Pembinaan Statistik Sektoral',
                        'code' => 'PSS',
                        'color' => '#7C3AED'
                    ],
                    'div_prod' => [
                        'id' => 'div_prod',
                        'name' => 'Tim Kerja Statistik Produksi',
                        'code' => 'PROD',
                        'color' => '#16A34A'
                    ],
                    'div_mitra' => [
                        'id' => 'div_mitra',
                        'name' => 'Tim Kerja Manajemen Lapangan dan Mitra',
                        'code' => 'MITRA',
                        'color' => '#EA580C'
                    ],
                    'div_dist' => [
                        'id' => 'div_dist',
                        'name' => 'Tim Kerja Statistik Distribusi',
                        'code' => 'DIST',
                        'color' => '#0284C7'
                    ],
                    'div_sensus' => [
                        'id' => 'div_sensus',
                        'name' => 'Tim Kerja Sensus dan Pengembangan Survei',
                        'code' => 'SENSUS',
                        'color' => '#D97706'
                    ],
                    'div_diseminasi' => [
                        'id' => 'div_diseminasi',
                        'name' => 'Tim Kerja Diseminasi Statistik dan Hubungan Masyarakat',
                        'code' => 'DISEMINASI',
                        'color' => '#DB2777'
                    ],
                    'div_kualitas' => [
                        'id' => 'div_kualitas',
                        'name' => 'Tim Kerja Penjaminan Kualitas dan Manajemen Resiko',
                        'code' => 'PKMR',
                        'color' => '#4B5563'
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
                        'division_id' => 'div_soc',
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
                        'division_id' => 'div_ipd',
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
                        'division_id' => 'div_ipd',
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
                        'division_id' => 'div_prod',
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

    /**
     * Get all documents from a Firestore collection.
     */
    public function getCollection(string $collection): array
    {
        if ($this->isLocalFallback || !$this->database) {
            $data = $this->getLocalData();
            return array_values($data[$collection] ?? []);
        }

        try {
            $snapshot = $this->database
                ->collection($collection)
                ->documents();

            $results = [];

            foreach ($snapshot as $document) {
                if ($document->exists()) {
                    $results[] = array_merge(
                        ['id' => (string) $document->id()],
                        $document->data()
                    );
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::error("Firestore getCollection error for {$collection}", [
                'message' => $e->getMessage(),
            ]);

            $data = $this->getLocalData();
            return array_values($data[$collection] ?? []);
        }
    }

    /**
     * Get a single Firestore document.
     */
    public function getDocument(string $collection, string $id): ?array
    {
        if ($this->isLocalFallback || !$this->database) {
            $data = $this->getLocalData();
            return $data[$collection][$id] ?? null;
        }

        try {
            $document = $this->database
                ->collection($collection)
                ->document($id)
                ->snapshot();

            if (!$document->exists()) {
                return null;
            }

            return array_merge(
                ['id' => (string) $document->id()],
                $document->data()
            );
        } catch (\Throwable $e) {
            Log::error("Firestore getDocument error for {$collection}/{$id}", [
                'message' => $e->getMessage(),
            ]);

            $data = $this->getLocalData();
            return $data[$collection][$id] ?? null;
        }
    }

    /**
     * Create or update a Firestore document.
     */
    public function setDocument(
        string $collection,
        string $id,
        array $payload
    ): array {
        $now = now()->toIso8601String();
        $payload['updated_at'] = $now;

        if ($this->isLocalFallback || !$this->database) {
            $data = $this->getLocalData();
            $existing = $data[$collection][$id] ?? [];
            $merged = array_merge($existing, ['id' => (string) $id], $payload);
            $data[$collection][$id] = $merged;
            $this->saveLocalData($data);
            return $merged;
        }

        try {
            $this->database
                ->collection($collection)
                ->document($id)
                ->set($payload, [
                    'merge' => true,
                ]);

            return array_merge(
                ['id' => (string) $id],
                $payload
            );
        } catch (\Throwable $e) {
            Log::error("Firestore setDocument error for {$collection}/{$id}", [
                'message' => $e->getMessage(),
            ]);

            $data = $this->getLocalData();
            $existing = $data[$collection][$id] ?? [];
            $merged = array_merge($existing, ['id' => (string) $id], $payload);
            $data[$collection][$id] = $merged;
            $this->saveLocalData($data);
            return $merged;
        }
    }


    /**
     * Delete a Firestore document.
     */
    public function deleteDocument(string $collection, string $id): bool
    {
        if ($this->isLocalFallback || !$this->database) {
            $data = $this->getLocalData();
            if (isset($data[$collection][$id])) {
                unset($data[$collection][$id]);
                $this->saveLocalData($data);
            }
            return true;
        }

        try {
            $this->database
                ->collection($collection)
                ->document($id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error("Firestore deleteDocument error for {$collection}/{$id}", [
                'message' => $e->getMessage(),
            ]);

            $data = $this->getLocalData();
            if (isset($data[$collection][$id])) {
                unset($data[$collection][$id]);
                $this->saveLocalData($data);
            }
            return true;
        }
    }

    public function seedInitialData(): bool
    {
        $this->ensureLocalStoreExists();
        return true;
    }

    /**
     * Test Firestore connection.
     */
    public function testConnection(): bool
    {
        if ($this->isLocalFallback || !$this->database) {
            return false;
        }

        try {
            $this->database
                ->collection('__system')
                ->document('__connection_test')
                ->snapshot();

            return true;
        } catch (\Throwable $e) {
            Log::error('Firestore connection test failed.', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

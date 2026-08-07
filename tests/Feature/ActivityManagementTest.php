<?php

namespace Tests\Feature;

use Tests\TestCase;

class ActivityManagementTest extends TestCase
{
    protected function getTestSessionUser(): array
    {
        return [
            'id' => 'usr_leonard',
            'name' => 'Leonard Douglas',
            'email' => 'leonard@bps.go.id',
            'role' => 'admin',
            'division_id' => 'div_ipd'
        ];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->withSession(['user' => $this->getTestSessionUser()])
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');
    }

    public function test_can_fetch_events_feed(): void
    {
        $response = $this->withSession(['user' => $this->getTestSessionUser()])
            ->getJson('/api/events');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'start', 'extendedProps' => ['status', 'description', 'category']]
        ]);
    }

    public function test_can_fetch_resources_feed(): void
    {
        $response = $this->withSession(['user' => $this->getTestSessionUser()])
            ->getJson('/api/resources');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'group', 'role']
        ]);
    }

    public function test_conflict_detection_logic(): void
    {
        $response = $this->withSession(['user' => $this->getTestSessionUser()])
            ->postJson('/api/check-conflicts', [
                'start' => '2026-06-08T10:00:00Z',
                'end' => '2026-06-08T15:00:00Z',
                'assignees' => ['usr_catherine']
            ]);

        $response->assertStatus(200);
        $response->assertJson(['has_conflict' => true]);
    }

    public function test_can_store_new_activity(): void
    {
        $response = $this->withSession(['user' => $this->getTestSessionUser()])
            ->postJson('/api/activities', [
                'title' => 'Rapat Koordinasi Sensus 2026',
                'description' => 'Pembahasan kuesioner dan alur lapangan',
                'start' => '2026-08-10T09:00:00Z',
                'end' => '2026-08-10T12:00:00Z',
                'allDay' => false,
                'status' => 'planned',
                'category' => 'Rapat',
                'assignees' => ['usr_leonard'],
                'location' => 'Ruang Rapat Utama BPS'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}

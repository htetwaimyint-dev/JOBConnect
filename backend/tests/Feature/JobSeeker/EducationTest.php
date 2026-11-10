<?php

namespace Tests\Feature\JobSeeker;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated users cannot access education API.
     */
    public function test_unauthenticated_user_cannot_access_education(): void
    {
        $response = $this->getJson('/api/v1/job-seeker/educations');

        $response->assertUnauthorized();
    }

    /**
     * Job seeker can create an education record.
     */
    public function test_job_seeker_can_create_education(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/job-seeker/educations', [
                'institution' => 'University of Computer Studies',
                'degree' => 'Bachelor',
                'field_of_study' => 'Computer Science',
                'start_date' => '2022-12-01',
                'end_date' => '2026-12-01',
                'description' => 'Studied Computer Science.',
            ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Education record created successfully.',
                'data' => [
                    'institution' => 'University of Computer Studies',
                    'degree' => 'Bachelor',
                    'field_of_study' => 'Computer Science',
                ],
            ]);

        $this->assertDatabaseHas('educations', [
            'job_seeker_id' => $user->id,
            'institution' => 'University of Computer Studies',
            'degree' => 'Bachelor',
        ]);
    }

    /**
     * Job seeker can view their education records.
     */
    public function test_job_seeker_can_view_education_records(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        $user->educations()->create([
            'institution' => 'University of Computer Studies',
            'degree' => 'Bachelor',
            'field_of_study' => 'Computer Science',
            'start_date' => '2022-12-01',
            'end_date' => '2026-12-01',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-seeker/educations');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'institution' => 'University of Computer Studies',
                        'degree' => 'Bachelor',
                    ],
                ],
            ]);
    }

    /**
     * Job seeker can view a single education record.
     */
    public function test_job_seeker_can_view_single_education_record(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        /** @var \App\Models\Education $education */
        $education = $user->educations()->create([
            'institution' => 'University of Computer Studies',
            'degree' => 'Bachelor',
            'field_of_study' => 'Computer Science',
            'start_date' => '2022-12-01',
            'end_date' => '2026-12-01',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/job-seeker/educations/{$education->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $education->id,
                    'institution' => 'University of Computer Studies',
                    'degree' => 'Bachelor',
                ],
            ]);
    }
}

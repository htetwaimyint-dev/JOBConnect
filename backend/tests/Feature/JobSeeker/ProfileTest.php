<?php

namespace Tests\Feature\JobSeeker;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated users cannot access the profile API.
     */
    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/v1/job-seeker/profile');

        $response->assertUnauthorized();
    }

    /**
     * Job seeker can see 404 when profile does not exist.
     */
    public function test_job_seeker_can_get_404_when_profile_does_not_exist(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-seeker/profile');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Profile not found.',
            ]);
    }

    /**
     * Job seeker can create a profile.
     */
    public function test_job_seeker_can_create_profile(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/job-seeker/profile', [
                'phone' => '09123456789',
                'city' => 'Yangon',
                'professional_title' => 'Junior Laravel Developer',
                'bio' => 'I am a Laravel developer.',
                'experience_years' => 1.5,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => [
                    'phone' => '09123456789',
                    'city' => 'Yangon',
                    'professional_title' => 'Junior Laravel Developer',
                ],
            ]);

        $this->assertDatabaseHas('job_seeker_profiles', [
            'job_seeker_id' => $user->id,
            'phone' => '09123456789',
            'city' => 'Yangon',
        ]);
    }

    /**
     * Job seeker can update an existing profile.
     */
    public function test_job_seeker_can_update_existing_profile(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'job_seeker',
        ]);

        $user->jobSeekerProfile()->create([
            'city' => 'Yangon',
            'professional_title' => 'Junior Developer',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/job-seeker/profile', [
                'city' => 'Mandalay',
                'professional_title' => 'Backend Developer',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'city' => 'Mandalay',
                    'professional_title' => 'Backend Developer',
                ],
            ]);

        $this->assertDatabaseHas('job_seeker_profiles', [
            'job_seeker_id' => $user->id,
            'city' => 'Mandalay',
            'professional_title' => 'Backend Developer',
        ]);

        $this->assertDatabaseCount('job_seeker_profiles', 1);
    }

    /**
     * Employers cannot access job seeker profile API.
     */
    public function test_employer_cannot_access_job_seeker_profile(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'employer',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/job-seeker/profile');

        $response->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Access denied. Job Seeker account required.',
            ]);
    }
}

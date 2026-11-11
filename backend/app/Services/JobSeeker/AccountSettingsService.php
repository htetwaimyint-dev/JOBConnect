<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeekerProfile;
use App\Models\User;
use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountSettingsService
{
    public function __construct(
        private JobSeekerProfileRepositoryInterface $profileRepository
    ) {}

    /**
     * Update account information.
     */
    public function updateAccountInformation(
        User $user,
        array $data,
        ?UploadedFile $profilePhoto = null,
        bool $removeProfilePhoto = false
    ): JobSeekerProfile {

        $profile = $this->profileRepository->findByUser($user);

        $oldPhotoPath = $profile?->profile_photo;
        $newPhotoPath = null;

        // Store new profile photo
        if ($profilePhoto) {
            $newPhotoPath = $profilePhoto->store(
                'profile-photos',
                'public'
            );
        }

        try {
            $profile = DB::transaction(function () use (
                $user,
                $data,
                $newPhotoPath,
                $removeProfilePhoto
            ) {
                // Update users table
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);

                // Update job_seeker_profiles table
                $profileData = [];

                if ($newPhotoPath) {
                    $profileData['profile_photo'] = $newPhotoPath;
                } elseif ($removeProfilePhoto) {
                    $profileData['profile_photo'] = null;
                }

                return $this->profileRepository->createOrUpdate(
                    $user,
                    $profileData
                );
            });
        } catch (Throwable $exception) {
            // Delete newly uploaded photo if update fails
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $exception;
        }

        // Delete old photo after successful update
        if (
            $oldPhotoPath &&
            ($newPhotoPath || $removeProfilePhoto)
        ) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return $profile;
    }

    /**
     * Change account password.
     */
    public function changePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {

        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'The current password is incorrect.',
                ],
            ]);
        }

        // User model's hashed cast handles password hashing.
        $user->update([
            'password' => $newPassword,
        ]);
    }
}

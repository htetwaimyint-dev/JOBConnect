<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeekerProfile;
use App\Models\User;
use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileService
{
    public function __construct(
        private JobSeekerProfileRepositoryInterface $profileRepository
    ) {}

    public function getProfile(User $user): ?JobSeekerProfile
    {
        return $this->profileRepository->findByUser($user);
    }

    public function updateProfile(
        User $user,
        array $data,
        ?UploadedFile $profilePhoto = null
    ): JobSeekerProfile {

        $profile = $this->profileRepository->findByUser($user);

        // Keep the old photo path.
        $oldPhotoPath = $profile?->profile_photo;

        // Store the new photo first.
        $newPhotoPath = null;

        if ($profilePhoto) {
            $newPhotoPath = $profilePhoto->store(
                'profile-photos',
                'public'
            );
        }

        try {
            // Update database inside a transaction.
            $profile = DB::transaction(function () use (
                $user,
                $data,
                $newPhotoPath
            ) {
                if ($newPhotoPath) {
                    $data['profile_photo'] = $newPhotoPath;
                }

                return $this->profileRepository->createOrUpdate(
                    $user,
                    $data
                );
            });
        } catch (Throwable $exception) {

            // Remove the new photo if database update fails.
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $exception;
        }

        // Delete old photo only after successful database update.
        if ($newPhotoPath && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return $profile;
    }
}

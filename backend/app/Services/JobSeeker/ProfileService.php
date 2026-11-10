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
        ?UploadedFile $profilePhoto = null,
        bool $removeProfilePhoto = false
    ): JobSeekerProfile {

        $profile = $this->profileRepository->findByUser($user);

        $oldPhotoPath = $profile?->profile_photo;
        $newPhotoPath = null;

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
                $userData = [];

                if (array_key_exists('name', $data)) {
                    $userData['name'] = $data['name'];
                }

                if (array_key_exists('email', $data)) {
                    $userData['email'] = $data['email'];
                }

                if (!empty($userData)) {
                    $user->update($userData);
                }

                // Remove user information from profile data
                unset(
                    $data['name'],
                    $data['email'],
                    $data['profile_photo'],
                    $data['remove_profile_photo']
                );

                // New photo takes priority over remove request
                if ($newPhotoPath) {
                    $data['profile_photo'] = $newPhotoPath;
                } elseif ($removeProfilePhoto) {
                    $data['profile_photo'] = null;
                }

                return $this->profileRepository->createOrUpdate(
                    $user,
                    $data
                );
            });
        } catch (Throwable $exception) {

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
}

<?php

namespace App\Services\Employer;

use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CompanyService
{
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    public function getCompanyByEmployer(User $user): ?Company
    {
        return $this->companyRepository->findByEmployer($user);
    }

    public function createOrUpdateCompany(
        User $user,
        array $data,
        array $files = []
    ): Company {
        $company = $this->companyRepository->findByEmployer($user);

        $oldLogo = $company?->logo;
        $oldCover = $company?->cover_image;
        $oldGallery = $company?->gallery ?? [];

        // Capture existing gallery BEFORE removing it from $data.
        $requestedGallery = $data['existing_gallery'] ?? [];

        // Normalize gallery input.
        $requestedGallery = array_filter(
            $requestedGallery,
            fn($url) => is_string($url) && trim($url) !== ''
        );

        unset(
            $data['employer_id'],
            $data['verification_status'],
            $data['verified_at'],
            $data['existing_gallery'],
            $data['logo'],
            $data['cover_image'],
            $data['gallery']
        );

        $newFiles = [];
        $filesToDelete = [];

        try {
            /*
             * Handle company logo.
             */
            if (
                isset($files['logo']) &&
                $files['logo'] instanceof UploadedFile
            ) {
                $newLogo = $this->storeFile(
                    $files['logo'],
                    $user,
                    'logo'
                );

                $data['logo'] = $newLogo;
                $newFiles[] = $newLogo;

                if ($oldLogo) {
                    $filesToDelete[] = $oldLogo;
                }
            }

            /*
             * Handle company cover image.
             */
            if (
                isset($files['cover_image']) &&
                $files['cover_image'] instanceof UploadedFile
            ) {
                $newCover = $this->storeFile(
                    $files['cover_image'],
                    $user,
                    'cover'
                );

                $data['cover_image'] = $newCover;
                $newFiles[] = $newCover;

                if ($oldCover) {
                    $filesToDelete[] = $oldCover;
                }
            }

            /*
             * Handle existing gallery images.
             */
            $retainedGallery = [];

            foreach ($oldGallery as $path) {

                $url = Storage::disk('public')->url($path);

                if (in_array($url, $requestedGallery, true)) {
                    // Keep the existing image.
                    $retainedGallery[] = $path;
                } else {
                    // Remove images not selected by the employer.
                    $filesToDelete[] = $path;
                }
            }

            /*
             * Handle newly uploaded gallery images.
             */
            $uploadedGallery = $files['gallery'] ?? [];

            foreach ($uploadedGallery as $image) {
                if (!$image instanceof UploadedFile) {
                    continue;
                }

                $path = $this->storeFile(
                    $image,
                    $user,
                    'gallery'
                );

                $retainedGallery[] = $path;
                $newFiles[] = $path;
            }

            // Save the final gallery paths.
            $data['gallery'] = array_values($retainedGallery);

            /*
             * Create or update company.
             */
            $savedCompany = $this->companyRepository->createOrUpdate(
                $user,
                $data
            );

            /*
             * Delete old files only after database save succeeds.
             */
            foreach (array_unique($filesToDelete) as $path) {
                Storage::disk('public')->delete($path);
            }

            return $savedCompany;
        } catch (Throwable $exception) {
            /*
             * Clean up newly uploaded files if saving fails.
             */
            foreach ($newFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }
    }

    private function storeFile(
        UploadedFile $file,
        User $user,
        string $directory
    ): string {
        return $file->store(
            "companies/{$user->id}/{$directory}",
            'public'
        );
    }
}

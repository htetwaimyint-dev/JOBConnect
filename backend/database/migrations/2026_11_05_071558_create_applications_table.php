
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('jobs');

            $table->foreignId('job_seeker_id')
                ->constrained('users');

            $table->foreignId('resume_id')
                ->constrained('resumes');

            $table->text('cover_letter')->nullable();

            $table->enum('status', [
                'pending',
                'reviewed',
                'shortlisted',
                'interview',
                'accepted',
                'rejected',
                'withdrawn',
            ])->default('pending');

            $table->timestamp('applied_at');

            $table->timestamps();

            $table->unique(
                ['job_id', 'job_seeker_id'],
                'applications_job_seeker_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

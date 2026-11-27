
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_evaluation_batches', function (Blueprint $table) {
            $table->id();

            // Job being evaluated
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnDelete();

            // Employer who started the evaluation
            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Total applicants included in this batch
            $table->unsignedInteger('total_applicants')->default(0);

            // Batch status
            $table->enum('status', [
                'queued',
                'processing',
                'completed',
                'completed_with_errors',
                'failed',
            ])->default('queued');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['job_id', 'status']);
            $table->index(['requested_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_evaluation_batches');
    }
};

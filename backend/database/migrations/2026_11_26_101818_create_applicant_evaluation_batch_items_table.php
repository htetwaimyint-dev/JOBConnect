
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_evaluation_batch_items', function (Blueprint $table) {
            $table->id();

            // Parent batch
            $table->foreignId('batch_id')
                ->constrained('applicant_evaluation_batches')
                ->cascadeOnDelete();

            // Applicant application
            $table->foreignId('application_id')
                ->constrained()
                ->restrictOnDelete();

            // Corresponding AI analysis result
            $table->foreignId('analysis_id')
                ->nullable()
                ->constrained('ai_analyses')
                ->nullOnDelete();

            // Individual applicant evaluation status
            $table->enum('status', [
                'queued',
                'processing',
                'completed',
                'failed',
            ])->default('queued');

            // Store error information if evaluation fails
            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Prevent the same applicant from appearing twice in one batch
            $table->unique(['batch_id', 'application_id']);

            $table->index(['batch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_evaluation_batch_items');
    }
};

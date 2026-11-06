
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');

            $table->foreignId('job_id')
                ->nullable()
                ->constrained('jobs');

            $table->enum('analysis_type', [
                'job_matching',
                'cover_letter',
                'job_description',
            ]);

            $table->json('input_data')->nullable();
            $table->json('result')->nullable();

            $table->string('provider', 100)->default('Groq');
            $table->string('model')->nullable();

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
